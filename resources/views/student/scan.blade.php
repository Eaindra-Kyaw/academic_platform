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
    <div class="nav-label">Support</div>
    <a href="#" class="nav-item" onclick="openUniBot()">
        <i class="bi bi-robot"></i><span>Uni Bot</span>
    </a>
    <a href="#" class="nav-item">
        <i class="bi bi-bell"></i><span>Notifications</span>
    </a>
@endsection

@section('content')
    <style>
        .scanner-container {
            background: white;
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .scanner-header {
            background: linear-gradient(135deg, #800000 0%, #6b0000 100%);
            padding: 1.25rem;
            text-align: center;
            color: white;
        }

        .video-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            background: #000;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        video {
            width: 100%;
            height: auto;
            display: block;
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

        @media (max-width: 768px) {
            .security-badge {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <script type="importmap">
        {
            "imports": {
                "@zxing/library": "https://unpkg.com/@zxing/library@0.21.3/umd/index.js"
            }
        }
    </script>

    <div>
        <div class="scanner-container">
            <div class="scanner-header">
                <i class="bi bi-camera-fill"></i>
                <h3>QR Code Scanner</h3>
                <p style="font-size: 0.75rem; opacity: 0.9; margin-top: 0.25rem;">Point camera at the QR code</p>
            </div>
            <div style="padding: 1rem;">
                <div class="video-container">
                    <video id="video" playsinline></video>
                </div>
                <div class="camera-controls">
                    <button class="camera-btn" onclick="switchCamera()"><i class="bi bi-arrow-repeat"></i> Switch
                        Camera</button>
                    <button class="camera-btn" onclick="startScanner()"><i class="bi bi-play-fill"></i> Restart</button>
                </div>
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
                <h4 style="display: inline; margin-left: 0.5rem;">No Active Session</h4>
            </div>
            <div class="info-row"><span>Status</span><span>Waiting for QR scan</span></div>
            <div class="info-row"><span>Note</span><span>Ask your lecturer to generate a QR code</span></div>
        </div>

        <button class="btn-manual" onclick="showManualEntry()"><i class="bi bi-pencil-square"></i> Can't scan? Enter Manual
            Code</button>
        <button class="btn-back" onclick="window.location.href='{{ route('student.dashboard') }}'"><i
                class="bi bi-arrow-left"></i> Back to Dashboard</button>
    </div>

    <!-- Manual Entry Modal -->
    <div id="manualModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:white; border-radius:1.5rem; padding:1.5rem; max-width:350px; width:90%;">
            <div style="text-align:center; margin-bottom:1rem;">
                <i class="bi bi-keyboard" style="font-size:2rem; color:#800000;"></i>
                <h3 style="margin-top:0.5rem;">Manual Entry</h3>
                <p style="font-size:0.8rem; color:#6b7280;">Enter the 6-digit code from your lecturer</p>
            </div>
            <input type="text" id="manualCode" placeholder="000000" maxlength="6"
                style="width:100%; padding:0.8rem; border:2px solid #e5e7eb; border-radius:0.75rem; text-align:center; letter-spacing:4px; font-size:1rem;">
            <div style="display:flex; gap:0.75rem; margin-top:1rem;">
                <button onclick="verifyManualCode()"
                    style="flex:1; background:#800000; color:white; border:none; padding:0.6rem; border-radius:0.75rem; font-weight:600; cursor:pointer;">Verify</button>
                <button onclick="closeManualModal()"
                    style="flex:1; background:#f3f4f6; color:#374151; border:none; padding:0.6rem; border-radius:0.75rem; cursor:pointer;">Cancel</button>
            </div>
        </div>
    </div>

    <script type="module">
        import {
            BrowserMultiFormatReader
        } from '@zxing/library';

        let reader = null;
        let currentCameraId = 'environment';
        let isScanning = false;

        async function initScanner() {
            reader = new BrowserMultiFormatReader();
            await reader.listVideoInputDevices();
            startScanner();
        }

        async function startScanner() {
            if (isScanning) return;
            try {
                const devices = await reader.listVideoInputDevices();
                const backCamera = devices.find(device =>
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('environment')
                );
                const cameraId = currentCameraId === 'environment' && backCamera ? backCamera.deviceId : devices[0]
                    ?.deviceId;
                if (!cameraId) {
                    showResult('No camera found', 'error');
                    return;
                }
                const videoElement = document.getElementById('video');
                await reader.decodeFromVideoElement(videoElement, cameraId, (result, error) => {
                    if (result) {
                        processQR(result.getText());
                    }
                });
                isScanning = true;
            } catch (error) {
                showResult('Unable to access camera', 'error');
            }
        }

        async function switchCamera() {
            currentCameraId = currentCameraId === 'environment' ? 'user' : 'environment';
            if (reader && isScanning) {
                await reader.reset();
                isScanning = false;
                startScanner();
            }
        }

        function stopScanner() {
            if (reader && isScanning) {
                reader.reset();
                isScanning = false;
            }
        }

        function processQR(qrData) {
            stopScanner();
            const url = new URL(qrData);
            const token = url.searchParams.get('token');
            const sessionId = url.searchParams.get('session');

            fetch('{{ route('student.scan.process') }}?token=' + token + '&session=' + sessionId, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = '{{ route('student.dashboard') }}';
                        }, 2000);
                    } else {
                        showResult(data.message, 'error');
                        setTimeout(() => {
                            startScanner();
                        }, 2000);
                    }
                })
                .catch(error => {
                    showResult('Network error', 'error');
                    setTimeout(() => {
                        startScanner();
                    }, 2000);
                });
        }

        function showResult(message, type) {
            const area = document.getElementById('resultArea');
            area.style.display = 'block';
            area.innerHTML =
                `<div class="result-alert result-${type}"><i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i> ${message}</div>`;
            setTimeout(() => {
                area.style.display = 'none';
                area.innerHTML = '';
            }, 3000);
        }

        function checkActiveSession() {
            fetch('{{ route('student.scan.check-session') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.hasSession) {
                        document.getElementById('sessionCard').innerHTML =
                            `
                            <div class="session-header" style="border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem; margin-bottom: 0.5rem;">
                                <i class="bi bi-clock-history"></i>
                                <h4 style="display: inline; margin-left: 0.5rem;">Active Session</h4>
                            </div>
                            <div class="info-row"><span>Course</span><span><strong>${data.session.course_name}</strong></span></div>
                            <div class="info-row"><span>Lecturer</span><span>${data.session.lecturer_name}</span></div>
                            <div class="info-row"><span>Room</span><span>${data.session.room || 'Not specified'}</span></div>
                            <div class="info-row"><span>Expires in</span><span style="color:#dc2626; font-weight:bold;">${data.session.expires_in} seconds</span></div>`;
                    }
                }).catch(() => {});
        }

        function showManualEntry() {
            document.getElementById('manualModal').style.display = 'flex';
        }

        function closeManualModal() {
            document.getElementById('manualModal').style.display = 'none';
            document.getElementById('manualCode').value = '';
        }

        function verifyManualCode() {
            const code = document.getElementById('manualCode').value;
            if (code.length !== 6) {
                alert('Please enter a valid 6-digit code');
                return;
            }
            fetch('{{ route('student.scan.manual') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        manual_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showResult(data.message, 'success');
                        closeManualModal();
                        setTimeout(() => {
                            window.location.href = '{{ route('student.dashboard') }}';
                        }, 2000);
                    } else {
                        showResult(data.message, 'error');
                    }
                }).catch(error => {
                    showResult('Network error', 'error');
                });
        }

        window.onload = () => {
            initScanner();
            checkActiveSession();
            setInterval(checkActiveSession, 10000);
        };
        window.switchCamera = switchCamera;
        window.startScanner = startScanner;
        window.showManualEntry = showManualEntry;
        window.closeManualModal = closeManualModal;
        window.verifyManualCode = verifyManualCode;
    </script>
@endsection
