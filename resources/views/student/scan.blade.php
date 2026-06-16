@extends('layouts.app')

@section('title', 'QR Attendance Scanner')
@section('role', 'Student')
@section('page-title', 'QR Attendance Scanner')
@section('welcome-text', 'Point camera at QR code')

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

        .result-alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1rem;
            text-align: center;
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
                <p>Enter the 6-digit code from your lecturer:</p>
                <input type="text" id="manualCode" placeholder="Enter code" maxlength="6"
                    style="width:100%; padding:0.8rem; text-align:center; font-size:1.2rem; text-transform:uppercase;">
                <button class="btn-manual" id="submitManualBtn">Submit Attendance</button>
            </div>
        </div>

        <div id="sessionCard" class="session-card">
            <div class="info-row"><span>Status</span><span id="sessionStatus">Loading...</span></div>
            <div class="info-row"><span>Course</span><span id="sessionCourse">-</span></div>
            <div class="info-row"><span>Manual Code</span><span id="sessionManualCode">-</span></div>
        </div>

        <button class="btn-back" onclick="window.location.href='/student/dashboard'">← Back to Dashboard</button>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        let html5QrCode = null;
        let isScanning = false;

        function showResult(message, type) {
            const area = document.getElementById('resultArea');
            area.style.display = 'block';
            area.innerHTML = `<div class="result-alert result-${type}">${message}</div>`;
            setTimeout(() => {
                area.style.display = 'none';
                area.innerHTML = '';
            }, 4000);
        }

        function checkActiveSession() {
            fetch('/student/scan/check-session')
                .then(response => response.json())
                .then(data => {
                    if (data.hasSession && data.session) {
                        document.getElementById('sessionStatus').innerHTML = '✅ Active';
                        document.getElementById('sessionCourse').innerHTML = data.session.course_name;
                        document.getElementById('sessionManualCode').innerHTML = data.session.session_code;
                    } else {
                        document.getElementById('sessionStatus').innerHTML = '❌ No Active Session';
                        document.getElementById('sessionCourse').innerHTML = '-';
                        document.getElementById('sessionManualCode').innerHTML = '-';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('sessionStatus').innerHTML = '⚠️ Error';
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

            console.log('Starting scanner...');

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

                    console.log('Available Cameras:', devices);

                    if (!devices || devices.length === 0) {

                        showResult(
                            'No camera found on this device',
                            'error'
                        );

                        return;
                    }

                    let cameraId = devices[0].id;

                    const backCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back')
                    );

                    if (backCamera) {
                        cameraId = backCamera.id;
                    }

                    return html5QrCode.start(
                        cameraId,
                        config,

                        (decodedText) => {

                            console.log(
                                'QR Scanned:',
                                decodedText
                            );

                            html5QrCode.stop()
                                .then(() => processQR(decodedText))
                                .catch(() => processQR(decodedText));
                        },

                        (errorMessage) => {
                            console.log(
                                'Scanner Frame:',
                                errorMessage
                            );
                        }
                    );
                })
                .catch(error => {

                    console.error(
                        'Camera startup failed:',
                        error
                    );

                    showResult(
                        'Camera Error: ' + error,
                        'error'
                    );
                });
        }

        function processQR(qrData) {
            console.log('Processing QR:', qrData);

            let token = null;
            let sessionId = null;
            let courseId = null;

            const tokenMatch = qrData.match(/token=([^&]+)/);
            const sessionMatch = qrData.match(/session=([^&]+)/);
            const courseMatch = qrData.match(/course=([^&]+)/);

            if (tokenMatch) token = tokenMatch[1];
            if (sessionMatch) sessionId = sessionMatch[1];
            if (courseMatch) courseId = courseMatch[1];

            // Semester QR (has course parameter)
            if (courseId && !sessionId) {
                showResult('Processing...', 'info');
                window.location.href =
                    `/student/scan/semester?token=${encodeURIComponent(token)}&course=${encodeURIComponent(courseId)}`;
                return;
            }

            // Dynamic QR (has session parameter)
            if (!token || !sessionId) {
                showResult('Invalid QR code format', 'error');
                startScanner();
                return;
            }

            showResult('Processing attendance...', 'info');

            fetch(`/student/scan/process?token=${encodeURIComponent(token)}&session=${encodeURIComponent(sessionId)}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Response:', data);
                    if (data.success) {
                        showResult('✅ ' + data.message, 'success');
                        checkActiveSession();
                    } else {
                        showResult('❌ ' + data.message, 'error');
                    }
                    startScanner();
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showResult('Attendance Recorded', 'error');
                    startScanner();
                });
        }

        function submitManualCode() {
            let code = document.getElementById('manualCode').value.toUpperCase();
            if (code.length !== 6) {
                alert('Enter 6-digit code');
                return;
            }

            fetch('/student/scan/manual', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        manual_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult('✅ ' + data.message, 'success');
                        checkActiveSession();
                    } else {
                        showResult('❌ ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showResult('Attendance Recorded ' + error.message, 'error');
                });
        }

        document.getElementById('cameraTabBtn').addEventListener('click', () => {
            document.getElementById('cameraSection').style.display = 'block';
            document.getElementById('manualSection').style.display = 'none';
            document.getElementById('cameraTabBtn').classList.add('active');
            document.getElementById('manualTabBtn').classList.remove('active');
            startScanner();
        });

        document.getElementById('manualTabBtn').addEventListener('click', () => {
            document.getElementById('cameraSection').style.display = 'none';
            document.getElementById('manualSection').style.display = 'block';
            document.getElementById('manualTabBtn').classList.add('active');
            document.getElementById('cameraTabBtn').classList.remove('active');
            if (html5QrCode) html5QrCode.stop();
        });

        document.getElementById('submitManualBtn').addEventListener('click', submitManualCode);
        document.getElementById('manualCode').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') submitManualCode();
        });
        document.getElementById('manualCode').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().slice(0, 6);
        });

        checkActiveSession();
        setInterval(checkActiveSession, 30000);
        startScanner();
    </script>
@endsection
