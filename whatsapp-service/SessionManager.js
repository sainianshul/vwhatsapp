const { Client, LocalAuth } = require('whatsapp-web.js');
const fs = require('fs');
const path = require('path');

/**
 * SessionManager — Manages WhatsApp Web sessions with:
 *   - webVersionCache for stable initialization
 *   - Auto-reconnection with exponential backoff
 *   - Heartbeat monitoring for zombie session detection
 *   - Puppeteer crash recovery
 */
class SessionManager {
    constructor() {
        this.sessions = new Map();       // sessionId -> Client
        this.status = new Map();          // sessionId -> status string
        this.qrCodes = new Map();         // sessionId -> QR text
        this.userInfo = new Map();        // sessionId -> { phone, name, profilePic }
        this.timeouts = new Map();        // sessionId -> QR timeout timer
        this.bootTimeouts = new Map();    // sessionId -> boot deadline timer
        this.reconnectAttempts = new Map(); // sessionId -> current attempt count
        this.reconnectTimers = new Map();  // sessionId -> reconnect delay timer
        this.heartbeatInterval = null;

        // ─── Configuration ───
        this.MAX_RECONNECT_ATTEMPTS = 5;
        this.RECONNECT_BASE_DELAY_MS = 10000;     // 10 seconds
        this.RECONNECT_MAX_DELAY_MS = 120000;      // 2 minutes max
        this.BOOT_TIMEOUT_MS = 5 * 60 * 1000;     // 5 minutes (up from 3)
        this.QR_TIMEOUT_MS = 180000;               // 3 minutes for QR scan
        this.PUPPETEER_TIMEOUT_MS = 180000;        // 3 minutes (up from 2)
        this.HEARTBEAT_INTERVAL_MS = 60000;        // Check every 60 seconds

        // Start heartbeat monitoring
        this._startHeartbeat();
    }

    // ═══════════════════════════════════════════════════════════════════
    //  PUPPETEER / CLIENT CONFIG
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Build the Client configuration object.
     * webVersionCache pins to a known-working WhatsApp Web version.
     */
    _buildClientConfig(sessionId) {
        const config = {
            authStrategy: new LocalAuth({
                clientId: sessionId,
                dataPath: './.wwebjs_auth'
            }),
            puppeteer: {
                executablePath: process.env.PUPPETEER_EXECUTABLE_PATH || '/usr/bin/chromium',
                args: [
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-accelerated-2d-canvas',
                    '--no-first-run',
                    '--no-zygote',
                    '--disable-gpu',
                    '--disable-extensions',
                    '--mute-audio',
                    '--disable-software-rasterizer',
                    '--disable-background-timer-throttling',
                    '--disable-backgrounding-occluded-windows',
                    '--disable-renderer-backgrounding',
                    '--single-process'
                ],
                timeout: this.PUPPETEER_TIMEOUT_MS,
                headless: true
            },
            // FIX: Use 'none' cache type to completely bypass the LocalWebCache.persist() crash
            // The production error was:
            //   TypeError: Cannot read properties of null (reading '1')
            //   at LocalWebCache.persist (/app/node_modules/whatsapp-web.js/src/webCache/LocalWebCache.js:34:69)
            // This happens because WA changed their HTML structure and the library's regex fails.
            // type:'none' skips all local caching and lets the library fetch WA Web fresh each time.
            webVersionCache: {
                type: 'none'
            }
        };

        return config;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  SESSION LIFECYCLE
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Start a new WhatsApp session or retrieve an existing one.
     * @param {string} sessionId
     * @param {boolean} isAutoBoot - true when called from AutoBoot (skip QR timeout)
     */
    startSession(sessionId, isAutoBoot = false) {
        if (this.sessions.has(sessionId)) {
            const currentStatus = this.status.get(sessionId);
            console.log(`[SessionManager] Session already exists: ${sessionId} (status: ${currentStatus}), skipping.`);
            return this.sessions.get(sessionId);
        }

        console.log(`[SessionManager] Initializing session: ${sessionId} (AutoBoot: ${isAutoBoot})`);
        this.status.set(sessionId, 'initializing');

        // Clear any pending reconnect for this session
        this._clearReconnectTimer(sessionId);

        const client = new Client(this._buildClientConfig(sessionId));

        // ─── QR Code received ───
        client.on('qr', (qr) => {
            console.log(`[SessionManager] QR Code generated for session: ${sessionId}`);
            this.status.set(sessionId, 'qr_ready');
            this.qrCodes.set(sessionId, qr);

            // Only set QR timeout for NEW sessions (not AutoBoot reconnections)
            if (!isAutoBoot && !this.timeouts.has(sessionId)) {
                const timeoutId = setTimeout(() => {
                    console.log(`[SessionManager] QR Timeout (180s). Cleaning up session: ${sessionId}`);
                    this._cleanupSession(sessionId, false); // Don't trigger reconnect for QR timeout
                }, this.QR_TIMEOUT_MS);
                this.timeouts.set(sessionId, timeoutId);
            }
        });

        // ─── Authenticated (QR scanned, now syncing) ───
        client.on('authenticated', () => {
            console.log(`[SessionManager] Authenticated for session: ${sessionId}`);
            this.status.set(sessionId, 'authenticating');
            this.qrCodes.delete(sessionId);
            this._clearSessionTimeout(sessionId);
            // Reset reconnect counter on successful auth
            this.reconnectAttempts.delete(sessionId);
        });

        // ─── Client is fully ready ───
        client.on('ready', () => {
            console.log(`[SessionManager] Client is ready for session: ${sessionId}`);
            this.status.set(sessionId, 'syncing_data');
            this.qrCodes.delete(sessionId);
            this._clearSessionTimeout(sessionId);
            this._clearBootTimeout(sessionId);
            // Reset reconnect counter — we're healthy
            this.reconnectAttempts.delete(sessionId);

            // Extract user info, then set status to 'connected'
            this._extractUserInfo(client, sessionId);
        });

        // ─── Authentication failure ───
        client.on('auth_failure', (msg) => {
            console.error(`[SessionManager] Auth failure for session: ${sessionId}`, msg);
            // Auth failure means saved credentials are bad — clear them and stop
            this._cleanupSession(sessionId, false);
            this.status.set(sessionId, 'auth_failed');
        });

        // ─── Client disconnected ───
        client.on('disconnected', (reason) => {
            console.log(`[SessionManager] Client disconnected for session: ${sessionId}, reason: ${reason}`);

            // Clean up the current client instance
            this.sessions.delete(sessionId);
            this.qrCodes.delete(sessionId);
            this.userInfo.delete(sessionId);
            this._clearSessionTimeout(sessionId);
            this._clearBootTimeout(sessionId);

            // Try to destroy the browser
            try { client.destroy(); } catch (e) {}

            // Determine if we should auto-reconnect
            const isUserLogout = (reason === 'LOGOUT' || reason === 'CONFLICT');
            if (isUserLogout) {
                console.log(`[SessionManager] User-initiated disconnect (${reason}), not reconnecting: ${sessionId}`);
                this.status.set(sessionId, 'disconnected');
                this.reconnectAttempts.delete(sessionId);
            } else {
                // Unexpected disconnect — schedule auto-reconnect if auth data exists
                console.log(`[SessionManager] Unexpected disconnect, will attempt reconnect: ${sessionId}`);
                this.status.set(sessionId, 'reconnecting');
                this._scheduleReconnect(sessionId);
            }
        });

        // ─── WhatsApp connection state changes (CONFLICT, UNLAUNCHED, etc.) ───
        client.on('change_state', (state) => {
            console.log(`[SessionManager] Connection state changed for ${sessionId}: ${state}`);
        });

        // Store the session BEFORE initializing
        this.sessions.set(sessionId, client);

        // ─── Boot Timeout ───
        // If session doesn't reach 'connected' within the timeout, it's stuck.
        const bootTimer = setTimeout(() => {
            const currentStatus = this.status.get(sessionId);
            if (currentStatus && currentStatus !== 'connected') {
                console.error(`[SessionManager] Boot Timeout (${this.BOOT_TIMEOUT_MS / 1000}s): Session ${sessionId} stuck at '${currentStatus}'. Cleaning up.`);
                this._cleanupSession(sessionId, true); // Allow reconnect
                this.status.set(sessionId, 'error');
            }
        }, this.BOOT_TIMEOUT_MS);
        this.bootTimeouts.set(sessionId, bootTimer);

        // Initialize — wrapped in proper error handling
        client.initialize().catch(err => {
            console.error(`[SessionManager] Failed to initialize session ${sessionId}:`, err.message);
            this.status.set(sessionId, 'error');
            this.sessions.delete(sessionId);
            this._clearSessionTimeout(sessionId);
            this._clearBootTimeout(sessionId);

            // Try to kill the browser if it was partially started
            try { client.destroy(); } catch (e) {}

            // Schedule reconnect if we have saved auth
            if (this._hasAuthData(sessionId)) {
                this._scheduleReconnect(sessionId);
            }
        });

        return client;
    }

    // ═══════════════════════════════════════════════════════════════════
    //  AUTO-RECONNECTION
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Schedule a reconnection attempt with exponential backoff.
     */
    _scheduleReconnect(sessionId) {
        const attempts = this.reconnectAttempts.get(sessionId) || 0;

        if (attempts >= this.MAX_RECONNECT_ATTEMPTS) {
            console.error(`[SessionManager] Max reconnect attempts (${this.MAX_RECONNECT_ATTEMPTS}) reached for ${sessionId}. Giving up.`);
            this.status.set(sessionId, 'error');
            this.reconnectAttempts.delete(sessionId);
            return;
        }

        // Exponential backoff: 10s, 20s, 40s, 80s, 120s (capped)
        const delay = Math.min(
            this.RECONNECT_BASE_DELAY_MS * Math.pow(2, attempts),
            this.RECONNECT_MAX_DELAY_MS
        );

        console.log(`[SessionManager] Scheduling reconnect for ${sessionId} in ${delay / 1000}s (attempt ${attempts + 1}/${this.MAX_RECONNECT_ATTEMPTS})`);
        this.status.set(sessionId, 'reconnecting');
        this.reconnectAttempts.set(sessionId, attempts + 1);

        const timer = setTimeout(() => {
            this.reconnectTimers.delete(sessionId);

            // Don't reconnect if session was manually started or logged out in the meantime
            if (this.sessions.has(sessionId)) {
                console.log(`[SessionManager] Session ${sessionId} already active, skipping reconnect.`);
                return;
            }

            // Check if auth data still exists on disk
            if (!this._hasAuthData(sessionId)) {
                console.log(`[SessionManager] No auth data for ${sessionId}, cannot reconnect without QR.`);
                this.status.set(sessionId, 'disconnected');
                this.reconnectAttempts.delete(sessionId);
                return;
            }

            console.log(`[SessionManager] Attempting reconnect for ${sessionId}...`);
            this.startSession(sessionId, true); // isAutoBoot = true (skip QR timeout)
        }, delay);

        this.reconnectTimers.set(sessionId, timer);
    }

    /**
     * Manually trigger a reconnection (called from API endpoint).
     */
    reconnectSession(sessionId) {
        // Clear any existing state
        this._clearReconnectTimer(sessionId);
        this.reconnectAttempts.delete(sessionId);

        // If session is already running, destroy it first
        if (this.sessions.has(sessionId)) {
            const client = this.sessions.get(sessionId);
            this.sessions.delete(sessionId);
            try { client.destroy(); } catch (e) {}
        }

        // Check if auth data exists
        if (!this._hasAuthData(sessionId)) {
            throw new Error('No saved session data. Please scan QR code again.');
        }

        console.log(`[SessionManager] Manual reconnect triggered for ${sessionId}`);
        this.startSession(sessionId, true);
    }

    /**
     * Check if saved auth data exists on disk for a session.
     */
    _hasAuthData(sessionId) {
        const authPath = path.join(__dirname, '.wwebjs_auth', `session-${sessionId}`);
        return fs.existsSync(authPath);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  HEARTBEAT MONITORING
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Periodic check to detect zombie sessions.
     * A zombie session is one where the Client object exists but
     * the Puppeteer browser has crashed silently.
     */
    _startHeartbeat() {
        this.heartbeatInterval = setInterval(() => {
            for (const [sessionId, client] of this.sessions.entries()) {
                const status = this.status.get(sessionId);

                // Only check connected sessions
                if (status !== 'connected') continue;

                // Try to ping the browser — if it throws, the session is dead
                this._pingSession(sessionId, client);
            }
        }, this.HEARTBEAT_INTERVAL_MS);

        console.log(`[SessionManager] Heartbeat started (interval: ${this.HEARTBEAT_INTERVAL_MS / 1000}s)`);
    }

    /**
     * Ping a session to verify the browser is still alive.
     */
    async _pingSession(sessionId, client) {
        try {
            // getState() requires a live Puppeteer page — if browser crashed, this throws
            const state = await Promise.race([
                client.getState(),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Ping timeout')), 10000))
            ]);

            if (!state) {
                console.warn(`[SessionManager] Heartbeat: No state for ${sessionId}, session may be unhealthy`);
            }
        } catch (err) {
            console.error(`[SessionManager] Heartbeat: Session ${sessionId} is dead (${err.message}). Triggering reconnect.`);
            this._cleanupSession(sessionId, true);
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  USER INFO EXTRACTION
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Safely extract user info after the client is ready.
     */
    _extractUserInfo(client, sessionId) {
        const tryExtract = async (attempt) => {
            try {
                if (!client.info) {
                    if (attempt < 5) {
                        console.log(`[SessionManager] client.info not ready for ${sessionId}, retry ${attempt + 1}/5`);
                        setTimeout(() => tryExtract(attempt + 1), 2000);
                        return;
                    }
                    console.log(`[SessionManager] client.info still undefined after 5 retries for ${sessionId}, marking connected anyway`);
                    this.status.set(sessionId, 'connected');
                    this._clearBootTimeout(sessionId);
                    return;
                }

                console.log(`[SessionManager] Raw client.info:`, JSON.stringify(client.info));
                const wid = client.info.wid || client.info.me;
                const phone = (wid && typeof wid === 'object' && wid.user)
                    ? wid.user
                    : (typeof wid === 'string' ? wid.split('@')[0] : null);
                const name = client.info.pushname || 'WhatsApp User';

                let profilePic = null;
                try {
                    const serializedWid = (wid && typeof wid === 'object' && wid._serialized)
                        ? wid._serialized
                        : `${phone}@c.us`;
                    profilePic = await client.getProfilePicUrl(serializedWid);
                } catch (e) {
                    // Profile pic fetch can fail — not critical
                }

                this.userInfo.set(sessionId, { phone, name, profilePic });
                console.log(`[SessionManager] User Info: Name=${name}, Phone=${phone}, DP=${profilePic ? 'Yes' : 'No'}`);

                // NOW mark as connected — user info is guaranteed to be available
                this.status.set(sessionId, 'connected');
                this._clearBootTimeout(sessionId);
                console.log(`[SessionManager] Session ${sessionId} is now fully connected with user info.`);
            } catch (err) {
                console.error(`[SessionManager] Error extracting user info:`, err.message);
                // Still mark as connected so user isn't stuck forever
                this.status.set(sessionId, 'connected');
                this._clearBootTimeout(sessionId);
            }
        };

        // Start first attempt after 1 second
        setTimeout(() => tryExtract(0), 1000);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  CLEANUP & TIMER MANAGEMENT
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Internal cleanup — stops browser, removes from maps.
     * Does NOT delete auth folder (so reconnect/AutoBoot can work).
     * @param {boolean} allowReconnect — if true, schedule auto-reconnect
     */
    async _cleanupSession(sessionId, allowReconnect = false) {
        const client = this.sessions.get(sessionId);
        this.sessions.delete(sessionId);
        this.status.set(sessionId, 'disconnected');
        this.qrCodes.delete(sessionId);
        this.userInfo.delete(sessionId);
        this._clearSessionTimeout(sessionId);
        this._clearBootTimeout(sessionId);

        if (client) {
            try { await client.destroy(); } catch (e) {}
            console.log(`[SessionManager] Browser destroyed for session: ${sessionId}`);
        }

        // Schedule auto-reconnect if allowed and auth data exists
        if (allowReconnect && this._hasAuthData(sessionId)) {
            this._scheduleReconnect(sessionId);
        }
    }

    /**
     * Clear the QR timeout for a session
     */
    _clearSessionTimeout(sessionId) {
        if (this.timeouts.has(sessionId)) {
            clearTimeout(this.timeouts.get(sessionId));
            this.timeouts.delete(sessionId);
        }
    }

    /**
     * Clear the boot timeout for a session
     */
    _clearBootTimeout(sessionId) {
        if (this.bootTimeouts.has(sessionId)) {
            clearTimeout(this.bootTimeouts.get(sessionId));
            this.bootTimeouts.delete(sessionId);
        }
    }

    /**
     * Clear any pending reconnection timer
     */
    _clearReconnectTimer(sessionId) {
        if (this.reconnectTimers.has(sessionId)) {
            clearTimeout(this.reconnectTimers.get(sessionId));
            this.reconnectTimers.delete(sessionId);
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  GETTERS
    // ═══════════════════════════════════════════════════════════════════

    getUserInfo(sessionId) {
        return this.userInfo.get(sessionId) || null;
    }

    getQrCode(sessionId) {
        return this.qrCodes.get(sessionId) || null;
    }

    getStatus(sessionId) {
        return this.status.get(sessionId) || 'not_found';
    }

    /**
     * Get reconnection info for a session.
     */
    getReconnectInfo(sessionId) {
        return {
            attempts: this.reconnectAttempts.get(sessionId) || 0,
            maxAttempts: this.MAX_RECONNECT_ATTEMPTS,
            hasPendingReconnect: this.reconnectTimers.has(sessionId),
            hasAuthData: this._hasAuthData(sessionId)
        };
    }

    // ═══════════════════════════════════════════════════════════════════
    //  MESSAGING
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Send a message through a connected session
     */
    async sendMessage(sessionId, to, message) {
        const client = this.sessions.get(sessionId);
        const currentStatus = this.status.get(sessionId);

        if (currentStatus === 'error' || currentStatus === 'auth_failed') {
            throw new Error('Session failed to connect. Please reconnect the account from the dashboard.');
        }
        if (currentStatus === 'reconnecting') {
            throw new Error('Session is reconnecting. Please try again in a few seconds.');
        }
        if (currentStatus === 'initializing' || currentStatus === 'authenticating') {
            throw new Error('Session is currently booting up. Please try again in 5-10 seconds.');
        }
        if (!client || (currentStatus !== 'connected' && currentStatus !== 'syncing_data')) {
            throw new Error('Session is not connected');
        }

        let cleanTo = to.replace(/[^0-9]/g, '');
        const formattedTo = `${cleanTo}@c.us`;

        try {
            const numberDetails = await client.getNumberId(formattedTo);
            if (!numberDetails) {
                throw new Error(`The number ${cleanTo} is not registered on WhatsApp.`);
            }

            return await client.sendMessage(numberDetails._serialized, message);
        } catch (err) {
            if (this._isPuppeteerCrash(err)) {
                console.error(`[SessionManager] Puppeteer crash in sendMessage for ${sessionId}: ${err.message}`);
                this._cleanupSession(sessionId, true); // Allow reconnect
                throw new Error('Session crashed. Auto-reconnecting... Please retry in 30 seconds.');
            }
            throw err;
        }
    }

    /**
     * Send a media message through a connected session.
     */
    async sendMediaMessage(sessionId, to, mediaPath, caption, filename) {
        const client = this.sessions.get(sessionId);
        const currentStatus = this.status.get(sessionId);

        if (currentStatus === 'error' || currentStatus === 'auth_failed') {
            throw new Error('Session failed to connect. Please reconnect the account from the dashboard.');
        }
        if (currentStatus === 'reconnecting') {
            throw new Error('Session is reconnecting. Please try again in a few seconds.');
        }
        if (currentStatus === 'initializing' || currentStatus === 'authenticating') {
            throw new Error('Session is currently booting up. Please try again in 5-10 seconds.');
        }
        if (!client || (currentStatus !== 'connected' && currentStatus !== 'syncing_data')) {
            throw new Error('Session is not connected');
        }

        // Verify file exists before attempting to send
        if (!fs.existsSync(mediaPath)) {
            throw new Error(`Media file not found at path: ${mediaPath}`);
        }

        // Verify file size (WhatsApp limit is 16MB for most media types)
        const MAX_FILE_SIZE = 16 * 1024 * 1024; // 16MB
        const fileStats = fs.statSync(mediaPath);
        if (fileStats.size > MAX_FILE_SIZE) {
            throw new Error(`Media file is too large (${(fileStats.size / 1024 / 1024).toFixed(1)}MB). Maximum allowed is 16MB.`);
        }
        if (fileStats.size === 0) {
            throw new Error(`Media file is empty (0 bytes): ${mediaPath}`);
        }

        let cleanTo = to.replace(/[^0-9]/g, '');
        const formattedTo = `${cleanTo}@c.us`;

        try {
            const numberDetails = await client.getNumberId(formattedTo);
            if (!numberDetails) {
                throw new Error(`The number ${cleanTo} is not registered on WhatsApp.`);
            }

            const { MessageMedia } = require('whatsapp-web.js');
            const media = MessageMedia.fromFilePath(mediaPath);

            if (filename) {
                media.filename = filename;
            }

            const options = {};
            if (caption) {
                options.caption = caption;
            }

            return await client.sendMessage(numberDetails._serialized, media, options);
        } catch (err) {
            if (this._isPuppeteerCrash(err)) {
                console.error(`[SessionManager] Puppeteer crash in sendMediaMessage for ${sessionId}: ${err.message}`);
                this._cleanupSession(sessionId, true); // Allow reconnect
                throw new Error('Session crashed. Auto-reconnecting... Please retry in 30 seconds.');
            }
            throw err;
        }
    }

    // ═══════════════════════════════════════════════════════════════════
    //  ERROR DETECTION
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Check if an error is a Puppeteer/browser-level crash.
     */
    _isPuppeteerCrash(err) {
        const msg = (err.message || '').toLowerCase();
        return msg.includes('protocol error') ||
               msg.includes('target closed') ||
               msg.includes('execution context was destroyed') ||
               msg.includes('session closed') ||
               msg.includes('browser has disconnected') ||
               msg.includes('page crashed') ||
               msg.includes('navigation failed') ||
               msg.includes('frame was detached') ||
               msg.includes('cannot find context') ||
               msg.includes('most likely the page has been closed');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  LOGOUT (USER-INITIATED)
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Logout and fully destroy a session (called when user deletes an account).
     * Also removes auth folder from disk.
     */
    async logoutSession(sessionId) {
        // Cancel any pending reconnections
        this._clearReconnectTimer(sessionId);
        this.reconnectAttempts.delete(sessionId);

        const client = this.sessions.get(sessionId);
        if (client) {
            console.log(`[SessionManager] Logging out session: ${sessionId}`);
            try { await client.logout(); } catch (err) {
                console.error(`[SessionManager] Logout error (non-fatal):`, err.message);
            }
            try { await client.destroy(); } catch (err) {
                console.error(`[SessionManager] Destroy error (non-fatal):`, err.message);
            }
            console.log(`[SessionManager] Browser destroyed for session: ${sessionId}`);
        }

        this.sessions.delete(sessionId);
        this.status.set(sessionId, 'disconnected');
        this.qrCodes.delete(sessionId);
        this.userInfo.delete(sessionId);
        this._clearSessionTimeout(sessionId);
        this._clearBootTimeout(sessionId);

        // Delete the auth folder from disk
        try {
            const authPath = path.join(__dirname, '.wwebjs_auth', `session-${sessionId}`);
            if (fs.existsSync(authPath)) {
                fs.rmSync(authPath, { recursive: true, force: true });
                console.log(`[SessionManager] Deleted auth folder for session: ${sessionId}`);
            }
        } catch (err) {
            console.error(`[SessionManager] Failed to delete auth folder:`, err.message);
        }
    }
}

module.exports = new SessionManager();
