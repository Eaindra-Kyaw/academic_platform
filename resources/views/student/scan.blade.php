@extends('layouts.app')

@section('title', 'QR Attendance Scanner')
@section('role', 'Student')
@section('page-title', 'QR Attendance Scanner')
@section('welcome-text', 'Position the QR code within the frame')

@section('sidebar')
    <div class="nav-label">Navigation</div>
    <a href="{{ route('student.dashboard') }}" class="nav-item">
        <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
    </a>
    <a href="{{ route('student.courses.available') }}" class="nav-item">
        <i class="bi bi-book"></i><span>Available Courses</span>
    </a>
    <a href="{{ route('student.my.enrollments') }}" class="nav-item">
        <i class="bi bi-list-check"></i><span>My Enrollments</span>
    </a>
    <a href="{{ route('student.scan') }}" class="nav-item active">
        <i class="bi bi-qr-code-scan"></i><span>QR Attendance</span>
    </a>
    <a href="{{ route('student.timetable') }}" class="nav-item">
        <i class="bi bi-calendar"></i><span>Timetable</span>
    </a>
    <a href="{{ route('student.progress') }}" class="nav-item">
        <i class="bi bi-graph-up"></i><span>My Progress</span>
    </a>
@endsection

@section('content')
    <style>
        .scanner-container {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            margin-bottom: 1.5rem;
        }

        .scanner-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1rem;
            text-align: center;
            color: white;
        }

        .video-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            background: #000;
            min-height: 300px;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        #video {
            width: 100%;
            height: auto;
            display: block;
            background: #000;
        }

        .scan-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            border: 2px solid #00ff00;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
            pointer-events: none;
        }

        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 50px;
            margin: 15px 0;
            flex-wrap: wrap;
        }

        .session-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f2f4;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .manual-code-display {
            font-size: 1.8rem;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 0.3rem;
            text-align: center;
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-top: 10px;
        }

        .btn-manual {
            background: transparent;
            border: 2px solid #800000;
            padding: 0.75rem;
            border-radius: 50px;
            color: #800000;
            font-weight: 600;
            width: 100%;
            margin-top: 0.5rem;
            cursor: pointer;
        }

        .btn-back {
            background: #800000;
            border: none;
            padding: 0.75rem;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
        }

        .result-alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
        }

        .result-success {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #10b981;
        }

        .result-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .camera-controls {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 0.5rem;
        }

        .camera-btn {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 0.3rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.7rem;
            cursor: pointer;
        }

        .tab-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tab-btn {
            flex: 1;
            padding: 0.5rem;
            background: #f3f4f6;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        .tab-btn.active {
            background: #800000;
            color: white;
        }

        #statusMsg {
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-error {
            background: #f8d7da;
            color: #721c24;
        }

        .status-info {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>

    <div>
        <div class="tab-buttons">
            <button class="tab-btn active" id="cameraTabBtn">📷 Scan QR Code</button>
            <button class="tab-btn" id="manualTabBtn">⌨️ Enter Manual Code</button>
        </div>

        <!-- Camera Scanner Section -->
        <div id="cameraSection" class="scanner-container">
            <div class="scanner-header">
                <i class="bi bi-camera-fill"></i>
                <h3>ZXing QR Code Scanner</h3>
                <p style="font-size: 0.75rem; opacity: 0.9;">Powered by Google's ZXing library</p>
            </div>
            <div style="padding: 1rem;">
                <div class="video-container">
                    <video id="video" playsinline autoplay></video>
                    <div id="scanOverlay" class="scan-overlay"></div>
                </div>
                <div class="camera-controls">
                    <button class="camera-btn" id="switchCameraBtn"><i class="bi bi-arrow-repeat"></i> Switch
                        Camera</button>
                    <button class="camera-btn" id="restartScannerBtn"><i class="bi bi-play-fill"></i> Restart
                        Scanner</button>
                </div>
                <div id="statusMsg"></div>
            </div>
        </div>

        <!-- Manual Entry Section -->
        <div id="manualSection" style="display: none;">
            <div class="session-card">
                <h3 style="color:#800000;">📝 Manual Code Entry</h3>
                <p>Enter the 6-digit manual code provided by your lecturer:</p>
                <input type="text" id="manualCodeInput" placeholder="Enter 6-digit code" maxlength="6"
                    style="width:100%; padding:0.8rem; border:2px solid #ddd; border-radius:0.5rem; text-align:center; font-size:1.2rem; text-transform:uppercase;">
                <button class="btn-manual" id="manualSubmitBtn" style="margin-top:1rem;">✓ Submit Attendance</button>
            </div>
        </div>

        <div id="resultArea" style="display: none;"></div>

        <div class="security-badge">
            <span><i class="bi bi-shield-lock-fill" style="color: #800000;"></i> Secure QR</span>
            <span><i class="bi bi-check-circle-fill" style="color: #10b981;"></i> One scan per session</span>
            <span><i class="bi bi-person-check-fill" style="color: #3b82f6;"></i> Enrollment verified</span>
        </div>

        <div id="sessionCard" class="session-card">
            <div class="session-header"
                style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                <i class="bi bi-clock-history"></i>
                <h4 style="display: inline; margin-left: 0.5rem;">Checking for active sessions...</h4>
            </div>
            <div class="info-row"><span>Status</span><span>Loading...</span></div>
            <div class="info-row"><span>Note</span><span>Please wait</span></div>
        </div>

        <button class="btn-back" onclick="window.location.href='{{ route('student.dashboard') }}'"><i
                class="bi bi-arrow-left"></i> Back to Dashboard</button>
    </div>

    <!-- ZXing Library -->
    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.21.3/umd/index.min.js"></script>

    <script>
        let reader = null;
        let isScanning = false;
        let videoElement = null;
        let currentFacingMode = 'environment';

        function showStatus(message, type) {
            const statusDiv = document.getElementById('statusMsg');
            statusDiv.innerHTML = message;
            statusDiv.className = `status-${type}`;
            setTimeout(() => {
                if (statusDiv.innerHTML === message) {
                    statusDiv.innerHTML = '';
                    statusDiv.className = '';
                }
            }, 3000);
        }

        async function initZXingScanner() {
            showStatus('Initializing camera...', 'info');

            try {
                reader = new ZXing.BrowserMultiFormatReader();
                videoElement = document.getElementById('video');

                if (!videoElement) {
                    showStatus('Video element not found!', 'error');
                    return;
                }

                await startZXingScanner();
                showStatus('Camera ready! Point at QR code.', 'success');
            } catch (error) {
                console.error('Scanner init error:', error);
                showStatus('Unable to initialize camera: ' + error.message, 'error');
            }
        }

        async function startZXingScanner() {
            if (isScanning) return;

            try {
                const devices = await reader.listVideoInputDevices();
                console.log('Available cameras:', devices);

                if (devices.length === 0) {
                    showStatus('No camera detected on this device!', 'error');
                    return;
                }

                // Find appropriate camera based on facing mode
                let selectedDevice = null;

                if (currentFacingMode === 'environment') {
                    // Find back camera
                    selectedDevice = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('environment') ||
                        device.label.toLowerCase().includes('rear')
                    );
                } else {
                    // Find front camera
                    selectedDevice = devices.find(device =>
                        device.label.toLowerCase().includes('front') ||
                        device.label.toLowerCase().includes('user')
                    );
                }

                // Fallback to first camera
                if (!selectedDevice && devices.length > 0) {
                    selectedDevice = devices[0];
                }

                if (!selectedDevice) {
                    showStatus('No suitable camera found!', 'error');
                    return;
                }

                showStatus('Starting camera: ' + selectedDevice.label, 'info');

                await reader.decodeFromVideoElement(
                    videoElement,
                    selectedDevice.deviceId,
                    (result, error) => {
                        if (result) {
                            console.log('QR Code detected:', result.getText());
                            processZXingQR(result.getText());
                        }
                    }
                );

                isScanning = true;
                console.log('Scanner started successfully');
            } catch (error) {
                console.error('Start scanner error:', error);
                showStatus('Camera error: ' + error.message, 'error');

                // Try to request camera permission explicitly
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: true
                    });
                    stream.getTracks().forEach(track => track.stop());
                    showStatus('Camera permission granted. Please refresh and try again.', 'info');
                } catch (permError) {
                    showStatus('Camera permission denied. Please allow camera access.', 'error');
                }
            }
        }

        async function switchZXingCamera() {
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            showStatus('Switching camera...', 'info');

            if (reader && isScanning) {
                await reader.reset();
                isScanning = false;
                await startZXingScanner();
            }
        }

        function stopZXingScanner() {
            if (reader && isScanning) {
                reader.reset();
                isScanning = false;
            }
        }

        function processZXingQR(qrData) {
            stopZXingScanner();

            try {
                const url = new URL(qrData);
                const token = url.searchParams.get('token');
                const sessionId = url.searchParams.get('session');

                if (!token || !sessionId) {
                    showResult('Invalid QR code format', 'error');
                    setTimeout(() => startZXingScanner(), 2000);
                    return;
                }

                showResult('Processing attendance...', 'info');

                fetch(`/student/scan/process?token=${encodeURIComponent(token)}&session=${encodeURIComponent(sessionId)}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showResult(data.message, 'success');
                            setTimeout(() => {
                                window.location.href = '/student/dashboard';
                            }, 2000);
                        } else {
                            showResult(data.message, 'error');
                            setTimeout(() => startZXingScanner(), 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showResult('Network error', 'error');
                        setTimeout(() => startZXingScanner(), 2000);
                    });
            } catch (error) {
                console.error('QR parse error:', error);
                showResult('Invalid QR code', 'error');
                setTimeout(() => startZXingScanner(), 2000);
            }
        }

        function showResult(message, type) {
            const area = document.getElementById('resultArea');
            area.style.display = 'block';
            area.innerHTML =
                `<div class="result-alert result-${type}"><i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i> ${message}</div>`;
            setTimeout(() => {
                area.style.display = 'none';
                area.innerHTML = '';
            }, 4000);
        }

        function checkActiveSession() {
            fetch('{{ route('student.scan.check-session') }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.hasSession && data.session) {
                        document.getElementById('sessionCard').innerHTML = `
                        <div class="session-header" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="bi bi-check-circle-fill" style="color:#10b981;"></i>
                            <h4 style="display: inline; margin-left: 0.5rem;">Active Session Found!</h4>
                        </div>
                        <div class="info-row"><span>📚 Course</span><span><strong>${data.session.course_name}</strong></span></div>
                        <div class="info-row"><span>👨‍🏫 Lecturer</span><span>${data.session.lecturer_name}</span></div>
                        <div class="info-row"><span>📍 Room</span><span>${data.session.room || 'Not specified'}</span></div>
                        <div class="info-row"><span>⏰ Expires in</span><span style="color:#dc2626;">${Math.floor(data.session.expires_in / 60)}m ${data.session.expires_in % 60}s</span></div>
                        <div class="info-row"><span>🔑 Manual Code</span></div>
                        <div class="manual-code-display">${data.session.session_code}</div>
                    `;
                    } else {
                        document.getElementById('sessionCard').innerHTML = `
                        <div class="session-header" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                            <i class="bi bi-clock-history"></i>
                            <h4 style="display: inline; margin-left: 0.5rem;">No Active Session</h4>
                        </div>
                        <div class="info-row"><span>Status</span><span>Waiting for QR scan</span></div>
                        <div class="info-row"><span>Note</span><span>Ask your lecturer to generate a QR code</span></div>
                    `;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Manual code submission
        function submitManualCode() {
            let code = document.getElementById('manualCodeInput').value.toUpperCase();
            if (code.length !== 6) {
                alert('Please enter a valid 6-digit code');
                return;
            }

            let btn = document.getElementById('manualSubmitBtn');
            btn.innerText = 'Processing...';
            btn.disabled = true;

            fetch('{{ route('student.scan.manual') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        manual_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '/student/dashboard';
                        }, 2000);
                    } else {
                        showResult(data.message, 'error');
                        btn.innerText = '✓ Submit Attendance';
                        btn.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResult('Network error', 'error');
                    btn.innerText = '✓ Submit Attendance';
                    btn.disabled = false;
                });
        }

        // Tab switching
        document.getElementById('cameraTabBtn').addEventListener('click', function() {
            document.getElementById('cameraSection').style.display = 'block';
            document.getElementById('manualSection').style.display = 'none';
            this.classList.add('active');
            document.getElementById('manualTabBtn').classList.remove('active');
            initZXingScanner();
        });

        document.getElementById('manualTabBtn').addEventListener('click', function() {
            document.getElementById('cameraSection').style.display = 'none';
            document.getElementById('manualSection').style.display = 'block';
            this.classList.add('active');
            document.getElementById('cameraTabBtn').classList.remove('active');
            stopZXingScanner();
        });

        // Event listeners
        document.getElementById('switchCameraBtn').addEventListener('click', switchZXingCamera);
        document.getElementById('restartScannerBtn').addEventListener('click', () => {
            stopZXingScanner();
            initZXingScanner();
        });

        document.getElementById('manualCodeInput').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().slice(0, 6);
        });
        document.getElementById('manualCodeInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') submitManualCode();
        });
        document.getElementById('manualSubmitBtn').addEventListener('click', submitManualCode);

        // Initialize
        checkActiveSession();
        setInterval(checkActiveSession, 30000);

        // Initialize scanner when page loads
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initZXingScanner, 500);
        });

        // Stop scanner on page unload
        window.addEventListener('beforeunload', () => {
            stopZXingScanner();
        });
    </script>
@endsection
