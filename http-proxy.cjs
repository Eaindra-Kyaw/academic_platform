const http = require('http');

const httpProxy = http.createServer((req, res) => {
  console.log('📱 Request:', req.method, req.url);

  const proxyReq = http.request({
    host: '127.0.0.1',
    port: 8000,
    path: req.url,
    method: req.method,
    headers: req.headers
  }, (proxyRes) => {
    res.writeHead(proxyRes.statusCode, proxyRes.headers);
    proxyRes.pipe(res);
  });

  proxyReq.on('error', (err) => {
    console.error('❌ Error:', err.message);
    res.writeHead(500);
    res.end('Proxy error: ' + err.message);
  });

  req.pipe(proxyReq);
});

httpProxy.listen(8080, '0.0.0.0', () => {
  console.log('');
  console.log('✅ HTTP PROXY RUNNING on port 8080');
  console.log('📱 iPhone URL: http://YOUR_IP:8080/student/scan');
  console.log('');
});

