@extends('layouts.app')

@section('title', 'QR Attendance Scanner')
@section('role', 'Student')
@section('page-title', 'QR Attendance Scanner')
@section('welcome-text', 'Point camera at QR code')

@section('sidebar')
    @include('layouts.partials.student-sidebar')
@endsection

@section('content')
    <style>
        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
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

        .btn-manual {
            background: #800000;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
        }

        .btn-manual:hover {
            background: #6b0000;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
        }

        .btn-back:hover {
            background: #5a6268;
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
            font-weight: 600;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: #800000;
            color: white;
        }

        .tab-btn:hover:not(.active) {
            background: #e5e7eb;
        }

        .result-alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
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

        .result-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #2563eb;
        }

        #manualCode {
            width: 100%;
            padding: 0.8rem;
            text-align: center;
            font-size: 1.2rem;
            text-transform: uppercase;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            letter-spacing: 4px;
            font-weight: 700;
        }

        #manualCode:focus {
            border-color: #800000;
            outline: none;
        }

        .status-error {
            color: #ef4444;
            font-weight: 600;
        }
    </style>

    <div>
        <div class="tab-buttons">
            <button class="tab-btn active" id="cameraTabBtn">📷 Scan QR Code</button>
            <button class="tab-btn" id="manualTabBtn">⌨️ Manual Code</button>
        </div>

        <div id="cameraSection">
            <div id="qr-reader"></div>
            <div id="resultArea" style="display: none;"></div>
        </div>

        <div id="manualSection" style="display: none;">
            <div class="session-card">
                <h3 style="color:#800000;">Manual Code Entry</h3>
                <p style="color:#6b7280; font-size:0.9rem;">Enter the 6-digit code from your lecturer:</p>
                <input type="text" id="manualCode" placeholder="e.g. A1B2C3" maxlength="6">
                <button class="btn-manual" id="submitManualBtn">✅ Submit Attendance</button>
            </div>
        </div>

        <div id="sessionCard" class="session-card">
            <h5 style="color:#800000; margin-bottom:0.5rem;">📋 Active Session Status</h5>
            <div class="info-row">
                <span>Status</span>
                <span id="sessionStatus">Loading...</span>
            </div>
            <div class="info-row">
                <span>Course</span>
                <span id="sessionCourse">-</span>
            </div>
            <div class="info-row">
                <span>Manual Code</span>
                <span id="sessionManualCode">-</span>
            </div>
            <div class="info-row">
                <span>Room</span>
                <span id="sessionRoom">-</span>
            </div>
        </div>

        <button class="btn-back" onclick="window.location.href='/student/dashboard'">← Back to Dashboard</button>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        let html5QrCode = null;
        let isScanning = false;

        // Get the base URL dynamically from the current page
        const baseUrl = window.location.origin;
        console.log('🔍 Base URL:', baseUrl);

        function showResult(message, type) {
            const area = document.getElementById('resultArea');
            area.style.display = 'block';
            area.innerHTML = `<div class="result-alert result-${type}">${message}</div>`;
            setTimeout(() => {
                area.style.display = 'none';
                area.innerHTML = '';
            }, 5000);
        }

        function checkActiveSession() {
            const statusEl = document.getElementById('sessionStatus');
            statusEl.innerHTML = '⏳ Checking...';
            statusEl.style.color = '#6b7280';

            fetch(baseUrl + '/student/scan/check-session', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('📡 Session check response status:', response.status);
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📡 Session check data:', data);
                    if (data.hasSession && data.session) {
                        document.getElementById('sessionStatus').innerHTML = '✅ Active';
                        document.getElementById('sessionStatus').style.color = '#10b981';
                        document.getElementById('sessionCourse').innerHTML = data.session.course_name || '-';
                        document.getElementById('sessionManualCode').innerHTML = data.session.session_code || '-';
                        document.getElementById('sessionRoom').innerHTML = data.session.room || 'Not specified';
                    } else {
                        document.getElementById('sessionStatus').innerHTML = '❌ No Active Session';
                        document.getElementById('sessionStatus').style.color = '#6b7280';
                        document.getElementById('sessionCourse').innerHTML = '-';
                        document.getElementById('sessionManualCode').innerHTML = '-';
                        document.getElementById('sessionRoom').innerHTML = '-';
                    }
                })
                .catch(error => {
                    console.error('❌ Session check error:', error);
                    document.getElementById('sessionStatus').innerHTML = '⚠️ Connection Error';
                    document.getElementById('sessionStatus').style.color = '#ef4444';
                });
        }

        function startScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => startScannerInternal()).catch(() => startScannerInternal());
            } else {
                startScannerInternal();
            }
        }

        function startScannerInternal() {
            console.log('📷 Starting scanner...');

            const readerElement = document.getElementById('qr-reader');
            if (!readerElement) {
                console.error('❌ QR reader element not found');
                return;
            }

            html5QrCode = new Html5Qrcode("qr-reader");

            const config = {
                fps: 10,
                qrbox: {
                    width: 250,
                    height: 250
                }
            };

            Html5Qrcode.getCameras()
                .then(devices => {
                    console.log('📷 Available cameras:', devices);

                    if (!devices || devices.length === 0) {
                        showResult('No camera found. Please use manual code entry.', 'error');
                        return;
                    }

                    let cameraId = devices[0].id;

                    // Prefer back camera
                    const backCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('rear')
                    );

                    if (backCamera) {
                        cameraId = backCamera.id;
                        console.log('📷 Using back camera');
                    } else {
                        console.log('📷 Using front camera');
                    }

                    return html5QrCode.start(
                        cameraId,
                        config,
                        (decodedText) => {
                            console.log('📷 QR Scanned:', decodedText);
                            html5QrCode.stop()
                                .then(() => processQR(decodedText))
                                .catch(() => processQR(decodedText));
                        },
                        (errorMessage) => {
                            // Silently ignore frame errors
                        }
                    );
                })
                .catch(error => {
                    console.error('❌ Camera startup failed:', error);
                    showResult('Camera Error: ' + error.message + '. Please use manual code entry.', 'error');
                });
        }

        function processQR(qrData) {
            console.log('🔍 Processing QR:', qrData);

            let token = null;
            let sessionId = null;
            let courseId = null;

            // Parse the QR data
            try {
                const url = new URL(qrData);
                token = url.searchParams.get('token');
                sessionId = url.searchParams.get('session');
                courseId = url.searchParams.get('course');
            } catch (e) {
                // If URL parsing fails, try regex
                const tokenMatch = qrData.match(/token=([^&]+)/);
                const sessionMatch = qrData.match(/session=([^&]+)/);
                const courseMatch = qrData.match(/course=([^&]+)/);

                if (tokenMatch) token = tokenMatch[1];
                if (sessionMatch) sessionId = sessionMatch[1];
                if (courseMatch) courseId = courseMatch[1];
            }

            console.log('📝 Parsed - Token:', token, 'Session:', sessionId, 'Course:', courseId);

            // Semester QR
            if (courseId && !sessionId) {
                showResult('Processing semester QR...', 'info');
                window.location.href = baseUrl +
                    `/student/scan/semester?token=${encodeURIComponent(token)}&course=${encodeURIComponent(courseId)}`;
                return;
            }

            // Dynamic QR
            if (!token || !sessionId) {
                showResult('Invalid QR code format. Please try again.', 'error');
                setTimeout(startScanner, 2000);
                return;
            }

            showResult('Processing attendance...', 'info');

            // Use the full URL from the QR code or build it
            const scanUrl = baseUrl +
                `/student/scan/process?token=${encodeURIComponent(token)}&session=${encodeURIComponent(sessionId)}`;
            console.log('📡 Sending request to:', scanUrl);

            fetch(scanUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('📡 Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Server returned ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📡 Response data:', data);
                    if (data.success) {
                        showResult('✅ ' + data.message, 'success');
                        checkActiveSession();
                    } else {
                        showResult('❌ ' + (data.message || 'Unknown error'), 'error');
                    }
                    setTimeout(startScanner, 3000);
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    showResult(
                        '⚠️ Cannot connect to server. Please try again.<br>Make sure you are on the same network.',
                        'error');
                    setTimeout(startScanner, 5000);
                });
        }

        function submitManualCode() {
            let code = document.getElementById('manualCode').value.toUpperCase().trim();
            if (code.length !== 6) {
                alert('Please enter a 6-digit code');
                return;
            }

            const btn = document.getElementById('submitManualBtn');
            btn.disabled = true;
            btn.textContent = '⏳ Submitting...';

            fetch(baseUrl + '/student/scan/manual', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        manual_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.textContent = '✅ Submit Attendance';
                    if (data.success) {
                        showResult('✅ ' + data.message, 'success');
                        checkActiveSession();
                        document.getElementById('manualCode').value = '';
                    } else {
                        showResult('❌ ' + (data.message || 'Unknown error'), 'error');
                    }
                })
                .catch(error => {
                    console.error('❌ Fetch error:', error);
                    btn.disabled = false;
                    btn.textContent = '✅ Submit Attendance';
                    showResult('⚠️ Cannot connect to server. Please try again.', 'error');
                });
        }

        // Tab switching
        document.getElementById('cameraTabBtn').addEventListener('click', function() {
            document.getElementById('cameraSection').style.display = 'block';
            document.getElementById('manualSection').style.display = 'none';
            this.classList.add('active');
            document.getElementById('manualTabBtn').classList.remove('active');
            startScanner();
        });

        document.getElementById('manualTabBtn').addEventListener('click', function() {
            document.getElementById('cameraSection').style.display = 'none';
            document.getElementById('manualSection').style.display = 'block';
            this.classList.add('active');
            document.getElementById('cameraTabBtn').classList.remove('active');
            if (html5QrCode) {
                html5QrCode.stop().catch(() => {});
            }
        });

        document.getElementById('submitManualBtn').addEventListener('click', submitManualCode);
        document.getElementById('manualCode').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') submitManualCode();
        });
        document.getElementById('manualCode').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Page loaded, initializing scanner...');
            console.log('📍 Base URL:', baseUrl);

            // Check session status
            checkActiveSession();

            // Check every 15 seconds
            setInterval(checkActiveSession, 15000);

            // Start scanner after a short delay
            setTimeout(startScanner, 800);
        });
    </script>
@endsection
