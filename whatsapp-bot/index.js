const { Client, LocalAuth } = require('whatsapp-web.js');
const qrcode = require('qrcode-terminal');
const express = require('express');

const app = express();
app.use(express.json());

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: { 
        executablePath: '/usr/bin/chromium',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage', '--disable-gpu'] 
    }
});

let isReady = false;

client.on('qr', (qr) => {
    console.log("=========================================");
    console.log("   ESCANEA ESTE CODIGO QR CON WHATSAPP   ");
    console.log("=========================================");
    qrcode.generate(qr, {small: true});
});

client.on('ready', () => {
    console.log("=========================================");
    console.log("   WHATSAPP CONECTADO CORRECTAMENTE!     ");
    console.log("=========================================");
    isReady = true;
});

client.on('auth_failure', msg => {
    console.error('Fallo en la autenticación', msg);
});

client.initialize();

app.get('/status', (req, res) => {
    res.json({ ready: isReady });
});

app.post('/send', async (req, res) => {
    if (!isReady) return res.status(503).json({error: "WhatsApp no está listo todavía."});
    const { number, message } = req.body;
    try {
        await client.sendMessage(number + "@c.us", message);
        res.json({success: true});
    } catch (err) {
        res.status(500).json({error: err.toString()});
    }
});

app.listen(3000, () => {
    console.log("Microservicio WhatsApp escuchando en puerto 3000");
});
