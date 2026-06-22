const https = require('https');
const http = require('http');
const fs = require('fs');
const url = require('url');
const os = require('os');

// Get local IP
function getLocalIP() {
    const interfaces = os.networkInterfaces();
    for (const name of Object.keys(interfaces)) {
        for (const iface of interfaces[name]) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }
    return '192.168.1.12';
}

const LOCAL_IP = getLocalIP();
const TARGET_PORT = 8000;

// SSL certificate options
let sslOptions;
try {
    sslOptions = {
        key: fs.readFileSync('./server.key'),
        cert: fs.readFileSync('./server.cert'),
    };
    console.log('✅ SSL certificates loaded successfully');
} catch (err) {
    console.error('❌ SSL certificates not found!');
    console.error('Run: openssl req -x509 -newkey rsa:2048 -nodes -keyout server.key -out server.cert -days 365 -subj "/CN=localhost"');
    process.exit(1);
}

// Create HTTPS server
const server = https.createServer(sslOptions, (req, res) => {
    // Parse the request URL
    const parsedUrl = url.parse(req.url);
    const path = parsedUrl.pathname;
    const query = parsedUrl.query;

    // Log the request
    console.log(`📱 ${req.method} ${req.url}`);

    // Build target URL (forward to Laravel on port 8000)
    const targetUrl = `http://${LOCAL_IP}:${TARGET_PORT}${path}${query ? '?' + query : ''}`;

    // Only log non-static requests to reduce noise
    if (!path.includes('.css') && !path.includes('.js') && !path.includes('.png') && !path.includes('.jpg') && !path.includes('.ico')) {
        console.log(`➡️ Forwarding to: ${targetUrl}`);
    }

    // Set CORS headers
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');

    // Handle preflight
    if (req.method === 'OPTIONS') {
        res.writeHead(200);
        res.end();
        return;
    }

    // Forward the request to Laravel
    const proxyReq = http.request(targetUrl, {
        method: req.method,
        headers: {
            ...req.headers,
            'Host': `${LOCAL_IP}:${TARGET_PORT}`,
            'X-Forwarded-For': req.socket.remoteAddress,
            'X-Forwarded-Proto': 'https',
            'X-Forwarded-Host': `${LOCAL_IP}:8443`
        }
    }, (proxyRes) => {
        // Forward response headers
        res.writeHead(proxyRes.statusCode, proxyRes.headers);

        // Pipe the response body
        proxyRes.pipe(res);

        if (!path.includes('.css') && !path.includes('.js') && !path.includes('.png') && !path.includes('.jpg') && !path.includes('.ico')) {
            console.log(`✅ Response: ${proxyRes.statusCode}`);
        }
    });

    // Handle errors
    proxyReq.on('error', (err) => {
        console.error('❌ Proxy Error:', err.message);
        res.writeHead(500, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({
            success: false,
            message: 'Server error: ' + err.message
        }));
    });

    // Pipe the request body
    req.pipe(proxyReq);
});

// Start the server
const PORT = 8443;
server.listen(PORT, '0.0.0.0', () => {
    console.log('\n═══════════════════════════════════════════════════');
    console.log('✅ HTTPS PROXY RUNNING');
    console.log(`📱 iPhone URL: https://${LOCAL_IP}:${PORT}/student/scan`);
    console.log(`🔗 Local URL: https://localhost:${PORT}/student/scan`);
    console.log('⚠️  Tap "Show Details" → "Visit Website"');
    console.log('═══════════════════════════════════════════════════\n');
    console.log(`➡️ Proxying to: http://${LOCAL_IP}:${TARGET_PORT}`);
    console.log('Press Ctrl+C to stop\n');
});
