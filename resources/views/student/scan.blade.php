@extends('layouts.app')

@section('title', 'QR Attendance Scanner')
@section('role', 'Student')
@section('page-title', 'Scan QR Attendance')
@section('welcome-text', 'Position the QR code within the frame')

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('student.dashboard') }}" class="nav-item"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <a href="{{ route('student.scan') }}" class="nav-item active"><i class="bi bi-qr-code-scan"></i><span>Scan
            Attendance</span></a>
    <a href="#" class="nav-item"><i class="bi bi-calendar-week"></i><span>Timetable</span></a>
    <a href="#" class="nav-item"><i class="bi bi-graph-up-arrow"></i><span>Progress</span></a>
@endsection

@section('content')
    <style>
        .scanner-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .scanner-header {
            background: #800000;
            padding: 15px;
            text-align: center;
            color: white;
        }

        #qr-reader {
            padding: 20px;
            background: #f8f9fa;
        }

        #qr-reader__dashboard_section_csr {
            display: none;
        }

        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 50px;
            font-size: 12px;
            margin: 15px 0;
        }

        .session-info {
            background: white;
            border-radius: 20px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .btn-manual {
            background: transparent;
            border: 1px solid #800000;
            padding: 10px;
            border-radius: 50px;
            color: #800000;
            width: 100%;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn-back {
            background: #800000;
            border: none;
            padding: 12px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            width: 100%;
            margin-top: 15px;
            cursor: pointer;
        }
    </style>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div class="scanner-card">
        <div class="scanner-header">
            <i class="bi bi-camera"></i>
            <h3 style="margin: 5px 0 0;">Camera Scanner</h3>
        </div>
        <div id="qr-reader"></div>
        <div id="result-area" style="padding: 20px; display: none;"></div>
    </div>

    <div class="security-badge">
        <i class="bi bi-shield-check"></i> Secure 60-second QR | One scan per session | Enrollment verified
    </div>

    <div class="session-info">
        <h4 style="color: #800000;"><i class="bi bi-clock-history"></i> Active Session Detected</h4>
        <div class="info-row"><span>Course</span><span><strong>Database Systems (CS301)</strong></span></div>
        <div class="info-row"><span>Lecturer</span><span>Dr. Aye Min Thu</span></div>
        <div class="info-row"><span>Room</span><span>A-203</span></div>
        <div class="info-row"><span>QR Expires in</span><span id="qrExpiryTimer" style="color:#dc2626; font-weight:bold;">60
                seconds</span></div>
    </div>

    <button class="btn-manual" onclick="showManualEntry()"><i class="bi bi-pencil-square"></i> Can't scan? Enter Manual
        Code</button>
    <button class="btn-back" onclick="window.location.href='{{ route('student.dashboard') }}'"><i
            class="bi bi-arrow-left"></i> Back to Dashboard</button>

    <div id="manualModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:white; border-radius:24px; padding:25px; max-width:350px; width:90%; margin:auto;">
            <h3 style="color:#800000;"><i class="bi bi-keyboard"></i> Manual Entry</h3>
            <p style="font-size:13px; margin:10px 0;">Enter 6-digit code from lecturer</p>
            <input type="text" id="manualCode" placeholder="000000" maxlength="6"
                style="width:100%; padding:12px; border:1px solid #ddd; border-radius:12px; text-align:center; letter-spacing:3px;">
            <button onclick="verifyManualCode()"
                style="background:#800000; color:white; border:none; padding:12px; border-radius:12px; width:100%; margin-top:10px;">Verify</button>
            <button onclick="closeManualModal()"
                style="background:transparent; border:1px solid #ddd; padding:12px; border-radius:12px; width:100%; margin-top:8px;">Cancel</button>
        </div>
    </div>

    <script>
        let html5QrCode = null;
        let isScanning = false;
        let expirySeconds = 60;

        setInterval(() => {
            if (expirySeconds > 0) {
                expirySeconds--;
                document.getElementById('qrExpiryTimer').innerText = expirySeconds + ' seconds';
            }
        }, 1000);

        function showResult(msg, type) {
            const area = document.getElementById('result-area');
            area.style.display = 'block';
            area.innerHTML =
                `<div style="padding:15px; border-radius:16px; background:${type === 'success' ? '#dcfce7' : '#fee2e2'}; color:${type === 'success' ? '#166534' : '#991b1b'};">${msg}</div>`;
        }

        function resetScanner() {
            document.getElementById('result-area').style.display = 'none';
            document.getElementById('result-area').innerHTML = '';
            expirySeconds = 60;
            startScanner();
        }

        function startScanner() {
            if (html5QrCode && isScanning) return;
            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: {
                    width: 280,
                    height: 280
                }
            }, (text) => {
                if (isScanning) {
                    html5QrCode.stop();
                    isScanning = false;
                    if (expirySeconds <= 0) {
                        showResult('QR Code Expired! Please request a new QR from your lecturer.', 'danger');
                        setTimeout(resetScanner, 2000);
                        return;
                    }
                    showResult('Attendance Recorded Successfully!', 'success');
                    setTimeout(() => window.location.href = '{{ route('student.dashboard') }}', 2000);
                }
            }).catch(() => showResult('Camera access failed. Please grant permission.', 'danger'));
            isScanning = true;
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
                alert('Enter valid 6-digit code');
                return;
            }
            showResult('Manual attendance recorded successfully!', 'success');
            closeManualModal();
            setTimeout(() => window.location.href = '{{ route('student.dashboard') }}', 2000);
        }

        window.onload = startScanner;
    </script>
@endsection
