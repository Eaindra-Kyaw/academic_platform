const https = require('https');
const http = require('http');
const fs = require('fs');

const TARGET_PORT = 8000;
const HTTPS_PORT = 8443;

if (!fs.existsSync('server.key') || !fs.existsSync('server.crt')) {
    console.log('📜 Generating self-signed certificate...');
    const { execSync } = require('child_process');
    try {
        execSync(`openssl req -x509 -newkey rsa:2048 -nodes -keyout server.key -out server.crt -days 365 -subj "/CN=localhost" 2>/dev/null`, { stdio: 'ignore' });
        console.log('✅ Certificate generated!');
    } catch (e) {
        console.error('❌ OpenSSL not found. Please install: brew install openssl');
        process.exit(1);
    }
}

const options = {
    key: fs.readFileSync('server.key'),
    cert: fs.readFileSync('server.crt')
};

const server = https.createServer(options, (req, res) => {
    console.log(`🔀 ${req.method} ${req.url}`);

    const headers = {
        ...req.headers,
        host: `127.0.0.1:${TARGET_PORT}`,
        'x-forwarded-proto': 'https',
        'x-forwarded-for': req.socket.remoteAddress || req.connection.remoteAddress
    };

    delete headers['accept-encoding'];

    const proxyReq = http.request({
        host: '127.0.0.1',
        port: TARGET_PORT,
        path: req.url,
        method: req.method,
        headers: headers
    }, (proxyRes) => {
        res.writeHead(proxyRes.statusCode, proxyRes.headers);
        proxyRes.pipe(res);
    });

    req.pipe(proxyReq);

    proxyReq.on('error', (err) => {
        console.error('❌ Proxy error:', err.message);
        if (!res.headersSent) {
            res.writeHead(502);
            res.end('Bad Gateway');
        }
    });
});

server.listen(HTTPS_PORT, '0.0.0.0', () => {
    console.log('');
    console.log('✅ HTTPS PROXY RUNNING');
    console.log(`📱 iPhone URL: https://${getLocalIP()}:${HTTPS_PORT}`);
    console.log('');
});

function getLocalIP() {
    const { execSync } = require('child_process');
    try {
        const ip = execSync('ipconfig getifaddr en0').toString().trim();
        return ip || '127.0.0.1';
    } catch (e) {
        return '127.0.0.1';
    }
}
