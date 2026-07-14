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
        :root {
            --primary: #0A2463;
            --primary-dark: #061840;
            --primary-light: #1E3A8A;
            --secondary: #C5A020;
            --accent: #D4A017;
            --bg-main: #EEF2F7;
            --white: #FFFFFF;
            --text-gray: #64748b;
            --text-dark: #1e293b;
            --shadow: 0 4px 20px rgba(10, 36, 99, 0.08);
            --shadow-hover: 0 8px 30px rgba(10, 36, 99, 0.15);
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #qr-reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .session-card {
            background: var(--white);
            border-radius: 1rem;
            border: 1px solid rgba(10, 36, 99, 0.06);
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f2f4;
            font-size: 0.85rem;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .label {
            color: var(--text-gray);
        }

        .info-row .value {
            font-weight: 600;
            color: var(--text-dark);
        }

        .btn-manual {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-manual:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-back {
            background: var(--text-gray);
            color: var(--white);
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .tab-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tab-btn {
            flex: 1;
            padding: 0.5rem;
            background: var(--bg-main);
            border: 1px solid rgba(10, 36, 99, 0.1);
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            color: var(--text-dark);
        }

        .tab-btn.active {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
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
            background: #d1fae5;
            color: #166534;
            border-left: 4px solid var(--success);
        }

        .result-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid var(--danger);
        }

        .result-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid var(--info);
        }

        #manualCode {
            width: 100%;
            padding: 0.8rem;
            text-align: center;
            font-size: 1.2rem;
            text-transform: uppercase;
            border: 2px solid rgba(10, 36, 99, 0.12);
            border-radius: 0.5rem;
            letter-spacing: 4px;
            font-weight: 700;
            transition: var(--transition);
        }

        #manualCode:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(10, 36, 99, 0.08);
        }

        .qr-mode-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .qr-mode-badge.semester {
            background: #dbeafe;
            color: #1e40af;
        }

        .qr-mode-badge.dynamic {
            background: #fef3c7;
            color: #92400e;
        }

        @media (max-width: 480px) {
            .tab-btn {
                font-size: 0.75rem;
                padding: 0.4rem;
            }

            .info-row {
                font-size: 0.75rem;
                flex-direction: column;
                gap: 0.2rem;
                align-items: flex-start;
            }
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
                <h3 style="color: var(--primary);">Manual Code Entry</h3>
                <p style="color: var(--text-gray); font-size:0.9rem;">Enter the 6-digit code from your lecturer:</p>
                <input type="text" id="manualCode" placeholder="e.g. A1B2C3" maxlength="6">
                <button class="btn-manual" id="submitManualBtn">✅ Submit Attendance</button>
            </div>
        </div>

        <div class="session-card">
            <h5 style="color: var(--primary); margin-bottom:0.5rem;">📋 Active Session Status</h5>
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value" id="sessionStatus">Loading...</span>
            </div>
            <div class="info-row">
                <span class="label">Course</span>
                <span class="value" id="sessionCourse">-</span>
            </div>
            <div class="info-row">
                <span class="label">Manual Code</span>
                <span class="value" id="sessionManualCode">-</span>
            </div>
            <div class="info-row">
                <span class="label">Room</span>
                <span class="value" id="sessionRoom">-</span>
            </div>
            <div class="info-row">
                <span class="label">QR Mode</span>
                <span class="value" id="sessionQrMode">-</span>
            </div>
        </div>

        <button class="btn-back" onclick="window.location.href='/student/dashboard'">← Back to Dashboard</button>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        const csrfToken = '{{ csrf_token() }}';
        let html5QrCode = null;
        let isScanning = false;
        const baseUrl = window.location.origin;

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
            statusEl.style.color = 'var(--text-gray)';

            fetch(baseUrl + '/student/scan/check-session', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.hasSession && data.session) {
                        document.getElementById('sessionStatus').innerHTML = '✅ Active';
                        document.getElementById('sessionStatus').style.color = 'var(--success)';
                        document.getElementById('sessionCourse').innerHTML = data.session.course_name || '-';
                        document.getElementById('sessionManualCode').innerHTML = data.session.session_code || data
                            .session
                            .manual_code || '-';
                        document.getElementById('sessionRoom').innerHTML = data.session.room || 'Not specified';

                        const qrModeEl = document.getElementById('sessionQrMode');
                        if (data.session.qr_mode === 'semester') {
                            qrModeEl.innerHTML = '<span class="qr-mode-badge semester">📚 Semester QR (Static)</span>';
                        } else {
                            qrModeEl.innerHTML = '<span class="qr-mode-badge dynamic">📱 Dynamic QR</span>';
                        }
                    } else {
                        document.getElementById('sessionStatus').innerHTML = '❌ No Active Session';
                        document.getElementById('sessionStatus').style.color = 'var(--danger)';
                        document.getElementById('sessionCourse').innerHTML = '-';
                        document.getElementById('sessionManualCode').innerHTML = '-';
                        document.getElementById('sessionRoom').innerHTML = '-';
                        document.getElementById('sessionQrMode').innerHTML = '-';
                    }
                })
                .catch(error => {
                    console.error('Session check error:', error);
                    document.getElementById('sessionStatus').innerHTML = '⚠️ Connection Error';
                    document.getElementById('sessionStatus').style.color = 'var(--danger)';
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
            const readerElement = document.getElementById('qr-reader');
            if (!readerElement) return;

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
                    if (!devices || devices.length === 0) {
                        showResult('No camera found. Please use manual code entry.', 'error');
                        return;
                    }

                    let cameraId = devices[0].id;
                    const backCamera = devices.find(device =>
                        device.label.toLowerCase().includes('back') ||
                        device.label.toLowerCase().includes('rear')
                    );

                    if (backCamera) {
                        cameraId = backCamera.id;
                    }

                    return html5QrCode.start(
                        cameraId,
                        config,
                        (decodedText) => {
                            html5QrCode.stop()
                                .then(() => processQR(decodedText))
                                .catch(() => processQR(decodedText));
                        },
                        (errorMessage) => {}
                    );
                })
                .catch(error => {
                    showResult('Camera Error: ' + error.message + '. Please use manual code entry.', 'error');
                });
        }

        function processQR(qrData) {
            let token = null;
            let sessionId = null;
            let courseId = null;

            try {
                const url = new URL(qrData);
                token = url.searchParams.get('token');
                sessionId = url.searchParams.get('session');
                courseId = url.searchParams.get('course');
            } catch (e) {
                const tokenMatch = qrData.match(/token=([^&]+)/);
                const sessionMatch = qrData.match(/session=([^&]+)/);
                const courseMatch = qrData.match(/course=([^&]+)/);

                if (tokenMatch) token = tokenMatch[1];
                if (sessionMatch) sessionId = sessionMatch[1];
                if (courseMatch) courseId = courseMatch[1];
            }

            if (courseId && !sessionId) {
                showResult('Processing semester QR...', 'info');
                window.location.href = baseUrl +
                    `/student/scan/semester?token=${encodeURIComponent(token)}&course=${encodeURIComponent(courseId)}`;
                return;
            }

            if (!token || !sessionId) {
                showResult('Invalid QR code format. Please try again.', 'error');
                setTimeout(startScanner, 2000);
                return;
            }

            showResult('Processing attendance...', 'info');

            const scanUrl = baseUrl +
                `/student/scan/process?token=${encodeURIComponent(token)}&session=${encodeURIComponent(sessionId)}`;

            fetch(scanUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Server returned ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showResult('✅ ' + data.message, 'success');
                        checkActiveSession();
                    } else {
                        showResult('❌ ' + (data.message || 'Unknown error'), 'error');
                    }
                    setTimeout(startScanner, 3000);
                })
                .catch(error => {
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
            checkActiveSession();
            setInterval(checkActiveSession, 15000);
            setTimeout(startScanner, 800);
        });
    </script>
@endsection
