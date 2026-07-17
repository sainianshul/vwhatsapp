const { Client, LocalAuth } = require('whatsapp-web.js');

class SessionManager {
    constructor() {
        this.sessions = new Map();
        this.status = new Map();
        this.qrCodes = new Map();
        this.userInfo = new Map();
        this.timeouts = new Map();
        this.bootTimeouts = new Map(); // Tracks boot deadlines per session
    }

    /**
     * Start a new WhatsApp session or retrieve an existing one.
     * @param {string} sessionId
     * @param {boolean} isAutoBoot - true when called from AutoBoot (skip QR timeout)
     */
    startSession(sessionId, isAutoBoot = false) {
        if (this.sessions.has(sessionId)) {
            console.log(`[SessionManager] Session already exists: ${sessionId}, skipping.`);
            return this.sessions.get(sessionId);
        }

        console.log(`[SessionManager] Initializing session: ${sessionId} (AutoBoot: ${isAutoBoot})`);
        this.status.set(sessionId, 'initializing');

        const client = new Client({
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
                    '--disable-software-rasterizer'
                ],
                timeout: 120000  // 2 minutes timeout for slow servers
            }
        });

        // ─── QR Code received ───
        client.on('qr', (qr) => {
            console.log(`[SessionManager] QR Code generated for session: ${sessionId}`);
            this.status.set(sessionId, 'qr_ready');
            this.qrCodes.set(sessionId, qr);

            // Only set QR timeout for NEW sessions (not AutoBoot reconnections)
            if (!isAutoBoot && !this.timeouts.has(sessionId)) {
                const timeoutId = setTimeout(() => {
                    console.log(`[SessionManager] QR Timeout (180s). Cleaning up session: ${sessionId}`);
                    this._cleanupSession(sessionId);
                }, 180000);
                this.timeouts.set(sessionId, timeoutId);
            }
        });

        // ─── Authenticated (QR scanned, now syncing) ───
        client.on('authenticated', () => {
            console.log(`[SessionManager] Authenticated for session: ${sessionId}`);
            this.status.set(sessionId, 'authenticating');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);
        });

        // ─── Client is fully ready ───
        client.on('ready', () => {
            console.log(`[SessionManager] Client is ready for session: ${sessionId}`);
            // Don't set 'connected' yet — wait until user info is extracted
            this.status.set(sessionId, 'syncing_data');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);

            // Extract user info, then set status to 'connected'
            this._extractUserInfo(client, sessionId);
        });

        // ─── Authentication failure ───
        client.on('auth_failure', (msg) => {
            console.error(`[SessionManager] Auth failure for session: ${sessionId}`, msg);
            this._cleanupSession(sessionId);
        });

        // ─── Client disconnected ───
        client.on('disconnected', (reason) => {
            console.log(`[SessionManager] Client disconnected for session: ${sessionId}`, reason);
            this.status.set(sessionId, 'disconnected');
            this.sessions.delete(sessionId);
            this.qrCodes.delete(sessionId);
            this.userInfo.delete(sessionId);
            this.clearSessionTimeout(sessionId);
            // Don't call destroy here — whatsapp-web.js already cleaned up
        });

        // Store the session BEFORE initializing
        this.sessions.set(sessionId, client);

        // ─── Boot Timeout (3 minutes) ───
        // If session doesn't reach 'connected' within 3 minutes, it's dead/stuck.
        // Clean it up so it doesn't show 'booting up' forever.
        const BOOT_TIMEOUT_MS = 3 * 60 * 1000; // 3 minutes
        const bootTimer = setTimeout(() => {
            const currentStatus = this.status.get(sessionId);
            if (currentStatus && currentStatus !== 'connected') {
                console.error(`[SessionManager] Boot Timeout (3 min): Session ${sessionId} stuck at '${currentStatus}'. Cleaning up.`);
                this._cleanupSession(sessionId);
                this.status.set(sessionId, 'error'); // Override disconnected with error
            }
        }, BOOT_TIMEOUT_MS);
        this.bootTimeouts.set(sessionId, bootTimer);

        // Initialize — wrapped in proper error handling
        client.initialize().catch(err => {
            console.error(`[SessionManager] Failed to initialize session ${sessionId}:`, err.message);
            this.status.set(sessionId, 'error');
            this.sessions.delete(sessionId);
            this.clearSessionTimeout(sessionId);
            this.clearBootTimeout(sessionId);
            // Try to kill the browser if it was partially started
            try { client.destroy(); } catch (e) {}
        });

        return client;
    }

    /**
     * Safely extract user info after the client is ready.
     */
    _extractUserInfo(client, sessionId) {
        // Try immediately, then retry after delays if client.info isn't ready yet
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
                this.clearBootTimeout(sessionId); // Session is healthy, cancel boot timeout
                console.log(`[SessionManager] Session ${sessionId} is now fully connected with user info.`);
            } catch (err) {
                console.error(`[SessionManager] Error extracting user info:`, err.message);
                // Still mark as connected so user isn't stuck forever
                this.status.set(sessionId, 'connected');
            }
        };

        // Start first attempt after 1 second
        setTimeout(() => tryExtract(0), 1000);
    }

    /**
     * Internal cleanup — stops browser, removes from maps.
     * Does NOT delete auth folder (so AutoBoot can reconnect).
     */
    async _cleanupSession(sessionId) {
        const client = this.sessions.get(sessionId);
        this.sessions.delete(sessionId);
        this.status.set(sessionId, 'disconnected');
        this.qrCodes.delete(sessionId);
        this.userInfo.delete(sessionId);
        this.clearSessionTimeout(sessionId);
        this.clearBootTimeout(sessionId);

        if (client) {
            try { await client.destroy(); } catch (e) {}
            console.log(`[SessionManager] Browser destroyed for session: ${sessionId}`);
        }
    }

    /**
     * Clear the QR timeout for a session
     */
    clearSessionTimeout(sessionId) {
        if (this.timeouts.has(sessionId)) {
            clearTimeout(this.timeouts.get(sessionId));
            this.timeouts.delete(sessionId);
        }
    }

    /**
     * Clear the boot timeout for a session (called when session becomes connected)
     */
    clearBootTimeout(sessionId) {
        if (this.bootTimeouts.has(sessionId)) {
            clearTimeout(this.bootTimeouts.get(sessionId));
            this.bootTimeouts.delete(sessionId);
        }
    }

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
     * Send a message through a connected session
     */
    async sendMessage(sessionId, to, message) {
        const client = this.sessions.get(sessionId);
        const currentStatus = this.status.get(sessionId);

        if (currentStatus === 'error') {
            throw new Error('Session failed to connect. Please reconnect the account from the dashboard.');
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
            // Detect Puppeteer-level crashes (session is dead, not a user error)
            if (this._isPuppeteerCrash(err)) {
                console.error(`[SessionManager] Puppeteer crash in sendMessage for ${sessionId}: ${err.message}`);
                this._cleanupSession(sessionId);
                throw new Error('Session crashed. Please reconnect the account from the dashboard.');
            }
            throw err; // Re-throw user-level errors (invalid number, etc.)
        }
    }

    /**
     * Send a media message through a connected session.
     * Uses MessageMedia.fromFilePath() to read the file directly from disk.
     *
     * @param {string} sessionId
     * @param {string} to - Phone number
     * @param {string} mediaPath - Absolute path to the media file on disk
     * @param {string} caption - Optional text caption
     * @param {string} filename - Optional filename override (useful for documents)
     */
    async sendMediaMessage(sessionId, to, mediaPath, caption, filename) {
        const client = this.sessions.get(sessionId);
        const currentStatus = this.status.get(sessionId);

        if (currentStatus === 'error') {
            throw new Error('Session failed to connect. Please reconnect the account from the dashboard.');
        }
        if (currentStatus === 'initializing' || currentStatus === 'authenticating') {
            throw new Error('Session is currently booting up. Please try again in 5-10 seconds.');
        }
        if (!client || (currentStatus !== 'connected' && currentStatus !== 'syncing_data')) {
            throw new Error('Session is not connected');
        }

        // Verify file exists before attempting to send
        const fs = require('fs');
        if (!fs.existsSync(mediaPath)) {
            throw new Error(`Media file not found at path: ${mediaPath}`);
        }

        // Verify file size (WhatsApp limit is 16MB for most media types)
        const MAX_FILE_SIZE = 16 * 1024 * 1024; // 16MB in bytes
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

            // Override filename if provided (useful for documents)
            if (filename) {
                media.filename = filename;
            }

            const options = {};
            if (caption) {
                options.caption = caption;
            }

            return await client.sendMessage(numberDetails._serialized, media, options);
        } catch (err) {
            // Detect Puppeteer-level crashes (session is dead, not a user error)
            if (this._isPuppeteerCrash(err)) {
                console.error(`[SessionManager] Puppeteer crash in sendMediaMessage for ${sessionId}: ${err.message}`);
                this._cleanupSession(sessionId);
                throw new Error('Session crashed. Please reconnect the account from the dashboard.');
            }
            throw err; // Re-throw user-level errors
        }
    }

    /**
     * Check if an error is a Puppeteer/browser-level crash (not a user error).
     */
    _isPuppeteerCrash(err) {
        const msg = (err.message || '').toLowerCase();
        return msg.includes('protocol error') ||
               msg.includes('target closed') ||
               msg.includes('execution context was destroyed') ||
               msg.includes('session closed') ||
               msg.includes('browser has disconnected') ||
               msg.includes('page crashed') ||
               msg.includes('navigation failed');
    }

    /**
     * Logout and fully destroy a session (called when user deletes an account).
     * Also removes auth folder from disk.
     */
    async logoutSession(sessionId) {
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
        this.clearSessionTimeout(sessionId);

        // Delete the auth folder from disk
        try {
            const fs = require('fs');
            const path = require('path');
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
