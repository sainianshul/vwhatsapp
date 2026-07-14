const { Client, LocalAuth } = require('whatsapp-web.js');

class SessionManager {
    constructor() {
        this.sessions = new Map();
        this.status = new Map(); // Store statuses: initializing, qr_ready, connected, disconnected
        this.qrCodes = new Map();
        this.userInfo = new Map(); // Store phone number, pushname, profilePic
        this.timeouts = new Map(); // Store timeout IDs for cleanup
    }

    /**
     * Start a new WhatsApp session or retrieve an existing one
     */
    startSession(sessionId) {
        if (this.sessions.has(sessionId)) {
            return this.sessions.get(sessionId);
        }

        console.log(`[SessionManager] Initializing session: ${sessionId}`);
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
                timeout: 60000
            }
        });

        // Event: QR Code received
        client.on('qr', (qr) => {
            console.log(`[SessionManager] QR Code generated for session: ${sessionId}`);
            this.status.set(sessionId, 'qr_ready');
            this.qrCodes.set(sessionId, qr);

            // Set 180 seconds timeout to auto-destroy if not scanned
            if (!this.timeouts.has(sessionId)) {
                const timeoutId = setTimeout(() => {
                    console.log(`[SessionManager] Timeout reached (180s). Destroying session: ${sessionId}`);
                    this.logoutSession(sessionId);
                }, 180000); // 180 seconds
                this.timeouts.set(sessionId, timeoutId);
            }
        });

        // Event: Authenticated successfully
        client.on('authenticated', () => {
            console.log(`[SessionManager] Authenticated for session: ${sessionId}`);
            this.status.set(sessionId, 'authenticating');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);
        });

        // Event: Client is ready
        client.on('ready', () => {
            console.log(`[SessionManager] Client is ready for session: ${sessionId}`);
            this.status.set(sessionId, 'connected');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);

            // Extract User Info
            try {
                setTimeout(async () => {
                    if (client.info) {
                        console.log(`[SessionManager] Raw client.info:`, JSON.stringify(client.info));
                        const wid = client.info.wid || client.info.me;
                        // wid could be an object { user: '...' } or string
                        const phone = (wid && typeof wid === 'object' && wid.user) ? wid.user : (typeof wid === 'string' ? wid.split('@')[0] : null);
                        const name = client.info.pushname || 'WhatsApp User';
                        let profilePic = null;
                        try {
                            const serializedWid = (wid && typeof wid === 'object' && wid._serialized) ? wid._serialized : `${phone}@c.us`;
                            profilePic = await client.getProfilePicUrl(serializedWid);
                        } catch (e) {
                            console.error(`[SessionManager] Could not fetch profile pic:`, e.message);
                        }
                        this.userInfo.set(sessionId, { phone, name, profilePic });
                        console.log(`[SessionManager] User Info extracted: Name=${name}, Phone=${phone}, DP=${profilePic ? 'Yes' : 'No'}`);
                    } else {
                        console.log(`[SessionManager] client.info is undefined even after 2 seconds`);
                    }
                }, 2000); // Wait 2 seconds for client.info to populate
            } catch (err) {
                console.error(`[SessionManager] Error extracting user info:`, err);
            }
        });

        // Event: Authentication failure
        client.on('auth_failure', (msg) => {
            console.error(`[SessionManager] Auth failure for session: ${sessionId}`, msg);
            this.status.set(sessionId, 'disconnected');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);
        });

        // Event: Client disconnected
        client.on('disconnected', (reason) => {
            console.log(`[SessionManager] Client disconnected for session: ${sessionId}`, reason);
            this.status.set(sessionId, 'disconnected');
            this.sessions.delete(sessionId);
            this.qrCodes.delete(sessionId);
            this.userInfo.delete(sessionId);
            this.clearSessionTimeout(sessionId);
        });

        this.sessions.set(sessionId, client);
        
        // Initialize client
        client.initialize().catch(err => {
            console.error(`[SessionManager] Failed to initialize session ${sessionId}`, err);
            this.status.set(sessionId, 'error');
            this.sessions.delete(sessionId);
            this.clearSessionTimeout(sessionId);
        });

        return client;
    }

    /**
     * Clear the timeout for a session
     */
    clearSessionTimeout(sessionId) {
        if (this.timeouts.has(sessionId)) {
            clearTimeout(this.timeouts.get(sessionId));
            this.timeouts.delete(sessionId);
        }
    }

    /**
     * Get the user info (phone, name) for a connected session
     */
    getUserInfo(sessionId) {
        return this.userInfo.get(sessionId) || null;
    }

    /**
     * Get the latest QR code for a session
     */
    getQrCode(sessionId) {
        return this.qrCodes.get(sessionId) || null;
    }

    /**
     * Get the status of a session
     */
    getStatus(sessionId) {
        return this.status.get(sessionId) || 'not_found';
    }

    /**
     * Send a message through a session
     */
    async sendMessage(sessionId, to, message) {
        const client = this.sessions.get(sessionId);
        const currentStatus = this.status.get(sessionId);

        if (currentStatus === 'initializing') {
            throw new Error('Session is currently booting up (initializing). Please try again in 5-10 seconds.');
        }

        if (!client || currentStatus !== 'connected') {
            throw new Error('Session is not connected');
        }

        // Strip any '+' or spaces just in case
        let cleanTo = to.replace(/[^0-9]/g, '');
        const formattedTo = `${cleanTo}@c.us`;

        // Validate if the number actually exists on WhatsApp
        const numberDetails = await client.getNumberId(formattedTo);
        if (!numberDetails) {
            throw new Error(`The number ${cleanTo} is not registered on WhatsApp or is invalid.`);
        }
        
        return await client.sendMessage(numberDetails._serialized, message);
    }

    /**
     * Logout and destroy a session
     */
    async logoutSession(sessionId) {
        const client = this.sessions.get(sessionId);
        if (client) {
            console.log(`[SessionManager] Logging out session: ${sessionId}`);
            try {
                await client.logout();
            } catch (err) {
                console.error(`[SessionManager] Error during logout (might not be logged in):`, err.message);
            }
            
            // Explicitly destroy the client to kill the Chrome process
            try {
                await client.destroy();
                console.log(`[SessionManager] Browser destroyed for session: ${sessionId}`);
            } catch (err) {
                console.error(`[SessionManager] Error destroying browser:`, err.message);
            }

            this.sessions.delete(sessionId);
            this.status.set(sessionId, 'disconnected');
            this.qrCodes.delete(sessionId);
            this.clearSessionTimeout(sessionId);
        }
    }
}

module.exports = new SessionManager();
