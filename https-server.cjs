const https = require('https');
const fs = require('fs');
const http = require('http');

const YOUR_IP = '172.20.10.3';

const options = {
  key: fs.readFileSync(`${YOUR_IP}-key.pem`),
  cert: fs.readFileSync(`${YOUR_IP}.pem`)
};

https.createServer(options, (req, res) => {
  console.log('📱 Request:', req.method, req.url);

  // Fix host header
  if (req.headers.host) {
    req.headers.host = req.headers.host.replace(':8443', '');
  }

  const proxyReq = http.request({
    host: '127.0.0.1',
    port: 8000,
    path: req.url,
    method: req.method,
    headers: req.headers
  }, (proxyRes) => {
    console.log('✅ Response:', proxyRes.statusCode);

    // Fix redirect URLs
    if (proxyRes.headers.location) {
      proxyRes.headers.location = proxyRes.headers.location
        .replace('http://127.0.0.1:8000', `https://${YOUR_IP}:8443`)
        .replace('http://localhost:8000', `https://${YOUR_IP}:8443`);
    }

    res.writeHead(proxyRes.statusCode, proxyRes.headers);
    proxyRes.pipe(res);
  });

  proxyReq.on('error', (err) => {
    console.error('❌ Error:', err.message);
    res.writeHead(500);
    res.end(`<h1>Proxy Error</h1><p>${err.message}</p><p>Make sure Laravel is running on port 8000</p>`);
  });

  req.pipe(proxyReq);
}).listen(8443, '0.0.0.0', () => {
  console.log('');
  console.log('═══════════════════════════════════════════════════');
  console.log('✅ HTTPS PROXY SERVER RUNNING');
  console.log(`📱 iPhone URL: https://${YOUR_IP}:8443/student/scan`);
  console.log('⚠️  Tap "Show Details" → "Visit Website"');
  console.log('═══════════════════════════════════════════════════');
  console.log('');
});
