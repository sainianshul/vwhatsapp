const { Client, LocalAuth } = require('whatsapp-web.js');

class SessionManager {
    constructor() {
        this.sessions = new Map();
        this.status = new Map();
        this.qrCodes = new Map();
        this.userInfo = new Map();
        this.timeouts = new Map();
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
            this.status.set(sessionId, 'connected');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);

            // Extract user info safely
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

        // Initialize — wrapped in proper error handling
        client.initialize().catch(err => {
            console.error(`[SessionManager] Failed to initialize session ${sessionId}:`, err.message);
            this.status.set(sessionId, 'error');
            this.sessions.delete(sessionId);
            this.clearSessionTimeout(sessionId);
            // Try to kill the browser if it was partially started
            try { client.destroy(); } catch (e) {}
        });

        return client;
    }

    /**
     * Safely extract user info after the client is ready.
     */
    _extractUserInfo(client, sessionId) {
        setTimeout(async () => {
            try {
                if (!client.info) {
                    console.log(`[SessionManager] client.info is undefined for ${sessionId}`);
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
            } catch (err) {
                console.error(`[SessionManager] Error extracting user info:`, err.message);
            }
        }, 3000); // Wait 3 seconds for client.info to populate
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

        if (currentStatus === 'initializing') {
            throw new Error('Session is currently booting up. Please try again in 5-10 seconds.');
        }
        if (!client || currentStatus !== 'connected') {
            throw new Error('Session is not connected');
        }

        let cleanTo = to.replace(/[^0-9]/g, '');
        const formattedTo = `${cleanTo}@c.us`;

        const numberDetails = await client.getNumberId(formattedTo);
        if (!numberDetails) {
            throw new Error(`The number ${cleanTo} is not registered on WhatsApp.`);
        }

        return await client.sendMessage(numberDetails._serialized, message);
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
