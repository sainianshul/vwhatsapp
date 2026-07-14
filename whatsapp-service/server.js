const express = require('express');
const cors = require('cors');
const qrcode = require('qrcode');
const SessionManager = require('./SessionManager');
const fs = require('fs');
const path = require('path');
require('dotenv').config();

// Prevent Node.js from crashing due to unhandled puppeteer errors (like context destroyed)
process.on('unhandledRejection', (reason, promise) => {
    console.error('[CRITICAL] Unhandled Rejection at:', promise, 'reason:', reason);
});
process.on('uncaughtException', (err) => {
    console.error('[CRITICAL] Uncaught Exception:', err);
});

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ limit: '50mb', extended: true }));

// Basic health check
app.get('/', (req, res) => {
    res.json({ status: 'success', message: 'WhatsApp Microservice is running' });
});

/**
 * Start a new WhatsApp session (Triggers QR generation if not logged in)
 */
app.post('/api/sessions/start', (req, res) => {
    const { sessionId } = req.body;
    
    if (!sessionId) {
        return res.status(400).json({ status: 'error', message: 'sessionId is required' });
    }

    try {
        SessionManager.startSession(sessionId);
        const status = SessionManager.getStatus(sessionId);
        res.json({ status: 'success', data: { sessionId, state: status } });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

/**
 * Get the status of a session
 */
app.get('/api/sessions/:id/status', (req, res) => {
    const sessionId = req.params.id;
    const status = SessionManager.getStatus(sessionId);
    
    res.json({ status: 'success', data: { sessionId, state: status } });
});

/**
 * Get the QR code for a session
 */
app.get('/api/sessions/:id/qr', async (req, res) => {
    const sessionId = req.params.id;
    const status = SessionManager.getStatus(sessionId);
    const qrText = SessionManager.getQrCode(sessionId);
    const userInfo = SessionManager.getUserInfo(sessionId);

    if (status === 'connected') {
        return res.json({ 
            status: 'success', 
            data: { 
                state: 'connected', 
                message: 'Already connected',
                phone: userInfo ? userInfo.phone : null,
                name: userInfo ? userInfo.name : null,
                profile_pic_url: userInfo ? userInfo.profilePic : null
            } 
        });
    }

    if (!qrText) {
        if (status === 'initializing' || status === 'authenticating') {
            return res.json({ status: 'syncing', data: { state: status } });
        }
        return res.status(404).json({ status: 'error', message: 'QR Code not available yet or session is disconnected' });
    }

    try {
        // Return as base64 image
        const qrImage = await qrcode.toDataURL(qrText);
        res.json({ status: 'success', data: { state: status, qr: qrImage, raw: qrText } });
    } catch (error) {
        res.status(500).json({ status: 'error', message: 'Failed to generate QR code image' });
    }
});

/**
 * Send a text message
 */
app.post('/api/messages/send', async (req, res) => {
    const { session_id, receiver, text } = req.body;

    if (!session_id || !receiver) {
        return res.status(400).json({ status: 'error', message: 'session_id and receiver are required' });
    }

    if (!text) {
        return res.status(400).json({ status: 'error', message: 'Text message is required' });
    }

    try {
        const response = await SessionManager.sendMessage(session_id, receiver, text);
        res.json({ status: 'success', data: { messageId: response.id.id } });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

/**
 * Logout and remove a session
 */
app.post('/api/sessions/:id/logout', async (req, res) => {
    const sessionId = req.params.id;
    
    try {
        await SessionManager.logoutSession(sessionId);
        res.json({ status: 'success', message: 'Session logged out and removed' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
});

app.listen(PORT, () => {
    console.log(`WhatsApp Microservice running on port ${PORT}`);
    
    // Auto-boot saved sessions
    const authDir = path.join(__dirname, '.wwebjs_auth');
    if (fs.existsSync(authDir)) {
        const folders = fs.readdirSync(authDir);
        for (const folder of folders) {
            if (folder.startsWith('session-')) {
                const sessionId = folder.replace('session-', '');
                
                // Remove Chromium locks to prevent "profile in use" errors after crash/restart
                const sessionPath = path.join(authDir, folder);
                const lockFiles = ['SingletonLock', 'SingletonCookie'];
                lockFiles.forEach(file => {
                    const filePath = path.join(sessionPath, file);
                    try {
                        // Use lstatSync to detect broken symlinks, existsSync fails for broken symlinks
                        const stat = fs.lstatSync(filePath);
                        if (stat) {
                            fs.unlinkSync(filePath);
                            console.log(`[AutoBoot] Removed stale lock ${file} for session ${sessionId}`);
                        }
                    } catch (err) {
                        // File doesn't exist, ignore
                    }
                });

                console.log(`[AutoBoot] Found saved session: ${sessionId}, starting it up...`);
                SessionManager.startSession(sessionId, true); // isAutoBoot = true
            }
        }
    }
});
