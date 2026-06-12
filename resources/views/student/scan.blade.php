@extends('layouts.app')

@section('title', 'QR Attendance Scanner')
@section('role', 'Student')
@section('page-title', 'QR Attendance Scanner')
@section('welcome-text', 'Position the QR code within the frame to mark attendance')

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
        /* Scanner Container */
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

        .scanner-header h3 {
            margin: 0;
            font-weight: 700;
        }

        .scanner-header p {
            margin: 0.25rem 0 0;
            font-size: 0.8rem;
            opacity: 0.9;
        }

        #qr-reader {
            padding: 1rem;
            background: #000;
            min-height: 400px;
            position: relative;
        }

        #qr-reader video {
            border-radius: 1rem;
        }

        /* Scanner Controls */
        .scanner-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-top: 1px solid #e5e7eb;
        }

        .control-btn {
            background: white;
            border: 1px solid #e5e7eb;
            padding: 0.6rem 1.2rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .control-btn:hover {
            background: #800000;
            color: white;
            border-color: #800000;
        }

        /* Session Card */
        .session-card {
            background: white;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .session-header {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            color: #800000;
        }

        .session-body {
            padding: 1rem;
        }

        .session-info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f2f4;
        }

        .session-info-row:last-child {
            border-bottom: none;
        }

        .timer-ring {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: conic-gradient(#10b981 0deg 0deg, #e5e7eb 0deg 360deg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .timer-inner {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.2rem;
            color: #800000;
        }

        /* Attendance History */
        .history-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-course {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .history-date {
            font-size: 0.7rem;
            color: #6b7280;
        }

        .history-time {
            font-size: 0.7rem;
            color: #10b981;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
        }

        .no-session {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
        }

        /* Success Animation */
        .success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .success-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .success-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            text-align: center;
            max-width: 300px;
            animation: bounceIn 0.5s ease;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-card i {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 1rem;
        }

        /* Manual Entry Modal */
        .manual-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1500;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .manual-modal.show {
            display: flex;
        }

        .manual-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.5rem;
            max-width: 350px;
            width: 90%;
        }

        .code-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 1rem;
            text-align: center;
            letter-spacing: 4px;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .code-input:focus {
            border-color: #800000;
            outline: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .two-col {
                grid-template-columns: 1fr;
            }

            .scanner-controls {
                flex-wrap: wrap;
            }
        }
    </style>

    <!-- Libraries -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1"></script>

    <div>
        <!-- Scanner Card -->
        <div class="scanner-container">
            <div class="scanner-header">
                <h3><i class="bi bi-camera-fill"></i> Live QR Scanner</h3>
                <p>Point camera at the QR code displayed by your lecturer</p>
            </div>
            <div id="qr-reader"></div>
            <div class="scanner-controls">
                <button class="control-btn" id="switchCameraBtn">
                    <i class="bi bi-arrow-repeat"></i> Switch Camera
                </button>
                <button class="control-btn" id="toggleTorchBtn">
                    <i class="bi bi-lightbulb"></i> Flashlight
                </button>
                <button class="control-btn" id="zoomInBtn">
                    <i class="bi bi-zoom-in"></i> Zoom In
                </button>
                <button class="control-btn" id="zoomOutBtn">
                    <i class="bi bi-zoom-out"></i> Zoom Out
                </button>
            </div>
        </div>

        <!-- Two Column Layout for Session & History -->
        <div class="two-col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <!-- Active Session Card -->
            <div class="session-card" id="sessionCard">
                <div class="session-header">
                    <i class="bi bi-clock-history"></i>
                    <span>Active Session</span>
                </div>
                <div class="session-body" id="sessionContent">
                    <div class="no-session">
                        <i class="bi bi-qr-code" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">No active session detected</p>
                        <p style="font-size: 0.7rem;">Ask your lecturer to generate a QR code</p>
                    </div>
                </div>
            </div>

            <!-- Attendance History Card -->
            <div class="session-card">
                <div class="session-header">
                    <i class="bi bi-calendar-check"></i>
                    <span>Recent Attendance</span>
                </div>
                <div class="history-list" id="attendanceHistory">
                    <div class="history-item">
                        <div>
                            <div class="history-course">Database Systems (CS301)</div>
                            <div class="history-date">Monday, June 12, 2026</div>
                        </div>
                        <div class="history-time">9:15 AM</div>
                        <div><span class="badge-success">Present</span></div>
                    </div>
                    <div class="history-item">
                        <div>
                            <div class="history-course">Networking (CS302)</div>
                            <div class="history-date">Monday, June 12, 2026</div>
                        </div>
                        <div class="history-time">11:00 AM</div>
                        <div><span class="badge-success">Present</span></div>
                    </div>
                    <div class="history-item">
                        <div>
                            <div class="history-course">Operating Systems (CS303)</div>
                            <div class="history-date">Friday, June 9, 2026</div>
                        </div>
                        <div class="history-time">10:30 AM</div>
                        <div><span class="badge-success">Present</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Entry Button -->
        <button class="control-btn" onclick="showManualModal()"
            style="width: 100%; margin-top: 1rem; justify-content: center;">
            <i class="bi bi-pencil-square"></i> Can't scan? Enter Manual Code
        </button>
    </div>

    <!-- Success Overlay -->
    <div id="successOverlay" class="success-overlay">
        <div class="success-card">
            <i class="bi bi-check-circle-fill"></i>
            <h3 style="color: #800000;">Attendance Recorded!</h3>
            <p id="successCourseName">Database Systems</p>
            <p style="font-size: 0.8rem; color: #6b7280;">✓ QR verified | ✓ Enrollment confirmed</p>
            <button onclick="closeSuccessOverlay()" class="control-btn"
                style="margin-top: 1rem; background: #800000; color: white; border: none;">Continue</button>
        </div>
    </div>

    <!-- Manual Entry Modal -->
    <div id="manualModal" class="manual-modal">
        <div class="manual-card">
            <div style="text-align: center; margin-bottom: 1rem;">
                <i class="bi bi-keyboard" style="font-size: 2rem; color: #800000;"></i>
                <h3 style="margin-top: 0.5rem;">Manual Entry</h3>
                <p style="font-size: 0.8rem; color: #6b7280;">Enter the 6-digit code from your lecturer</p>
            </div>
            <input type="text" id="manualCode" class="code-input" placeholder="000000" maxlength="6"
                pattern="[0-9]{6}">
            <div style="display: flex; gap: 0.75rem; margin-top: 1rem;">
                <button onclick="verifyManualCode()" class="control-btn"
                    style="background: #800000; color: white; border: none; flex: 1; justify-content: center;">Verify</button>
                <button onclick="closeManualModal()" class="control-btn"
                    style="flex: 1; justify-content: center;">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let html5QrCode = null;
        let isScanning = false;
        let currentCameraId = 'environment';
        let currentZoom = 1;
        let torchEnabled = false;
        let videoElement = null;

        // Simulated active session (will come from backend)
        let activeSession = {
            hasSession: true,
            courseName: 'Database Systems (CS301)',
            courseCode: 'CS301',
            lecturerName: 'Dr. Aye Min Thu',
            room: 'A-203',
            expirySeconds: 45,
            sessionId: 'sess_12345'
        };

        // Start scanner
        function startScanner(cameraId = 'environment') {
            if (html5QrCode && isScanning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    initScanner(cameraId);
                }).catch(() => initScanner(cameraId));
            } else {
                initScanner(cameraId);
            }
        }

        function initScanner(cameraId) {
            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start({
                facingMode: cameraId === 'environment' ? 'environment' : 'user'
            }, {
                fps: 15,
                qrbox: {
                    width: 280,
                    height: 280
                },
                aspectRatio: 1.0,
                showTorchButton: false
            }, (decodedText) => {
                if (isScanning) {
                    processQRCode(decodedText);
                }
            }).then(() => {
                isScanning = true;
                setupVideoControls();
            }).catch((err) => {
                console.error('Scanner error:', err);
                showToast('Camera access failed. Please grant permission.', 'error');
            });
        }

        function setupVideoControls() {
            // Get video element for torch control
            setTimeout(() => {
                const video = document.querySelector('#qr-reader video');
                if (video && video.srcObject) {
                    videoElement = video;
                }
            }, 1000);
        }

        function toggleTorch() {
            if (videoElement && videoElement.srcObject) {
                const track = videoElement.srcObject.getVideoTracks()[0];
                if (track) {
                    torchEnabled = !torchEnabled;
                    track.applyConstraints({
                        advanced: [{
                            torch: torchEnabled
                        }]
                    }).catch(() => {
                        showToast('Flashlight not available on this device', 'error');
                        torchEnabled = false;
                    });
                }
            }
        }

        function zoomCamera(delta) {
            if (videoElement && videoElement.srcObject) {
                const track = videoElement.srcObject.getVideoTracks()[0];
                if (track) {
                    const capabilities = track.getCapabilities();
                    if (capabilities.zoom) {
                        currentZoom = Math.min(capabilities.zoom.max, Math.max(capabilities.zoom.min, currentZoom + delta));
                        track.applyConstraints({
                            advanced: [{
                                zoom: currentZoom
                            }]
                        });
                    }
                }
            }
        }

        function switchCamera() {
            currentCameraId = currentCameraId === 'environment' ? 'user' : 'environment';
            startScanner(currentCameraId);
        }

        function processQRCode(qrData) {
            if (isScanning) {
                html5QrCode.stop();
                isScanning = false;

                // Simulate API call
                setTimeout(() => {
                    showSuccessAnimation(activeSession.courseName);
                    updateSessionAfterScan();
                    addToHistory(activeSession.courseName);
                    playBeep();
                    vibrate();

                    setTimeout(() => {
                        resetScanner();
                    }, 3000);
                }, 500);
            }
        }

        function showSuccessAnimation(courseName) {
            const overlay = document.getElementById('successOverlay');
            const courseElem = document.getElementById('successCourseName');
            courseElem.textContent = courseName;
            overlay.classList.add('show');
            canvasConfetti({
                particleCount: 150,
                spread: 70,
                origin: {
                    y: 0.6
                },
                colors: ['#800000', '#FFD700', '#10b981']
            });
        }

        function closeSuccessOverlay() {
            document.getElementById('successOverlay').classList.remove('show');
        }

        function playBeep() {
            try {
                const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                oscillator.frequency.value = 880;
                gainNode.gain.value = 0.3;
                oscillator.start();
                gainNode.gain.exponentialRampToValueAtTime(0.00001, audioContext.currentTime + 0.5);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (e) {
                console.log('Audio not supported');
            }
        }

        function vibrate() {
            if (navigator.vibrate) {
                navigator.vibrate(200);
            }
        }

        function showToast(message, type) {
            // Simple toast implementation
            const toast = document.createElement('div');
            toast.style.cssText =
                `position:fixed; bottom:100px; left:50%; transform:translateX(-50%); background:${type === 'error' ? '#ef4444' : '#10b981'}; color:white; padding:12px 24px; border-radius:50px; z-index:2000; font-size:14px; box-shadow:0 4px 12px rgba(0,0,0,0.2);`;
            toast.innerHTML =
                `<i class="bi bi-${type === 'error' ? 'exclamation-triangle' : 'check-circle'}"></i> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function updateSessionAfterScan() {
            const sessionContent = document.getElementById('sessionContent');
            if (activeSession.hasSession) {
                activeSession.expirySeconds = Math.max(0, activeSession.expirySeconds - 1);
                sessionContent.innerHTML = `
                    <div class="session-info-row">
                        <span><i class="bi bi-book"></i> Course</span>
                        <span><strong>${activeSession.courseName}</strong></span>
                    </div>
                    <div class="session-info-row">
                        <span><i class="bi bi-person-badge"></i> Lecturer</span>
                        <span>${activeSession.lecturerName}</span>
                    </div>
                    <div class="session-info-row">
                        <span><i class="bi bi-door-open"></i> Room</span>
                        <span>${activeSession.room}</span>
                    </div>
                    <div style="text-align: center; margin-top: 1rem;">
                        <div class="timer-ring" id="timerRing">
                            <div class="timer-inner" id="timerSeconds">${activeSession.expirySeconds}</div>
                        </div>
                        <p style="font-size: 0.7rem; margin-top: 0.5rem;">QR expires in</p>
                    </div>
                `;
                startTimer();
            }
        }

        function startTimer() {
            let remaining = activeSession.expirySeconds;
            const timerSeconds = document.getElementById('timerSeconds');
            const timerRing = document.getElementById('timerRing');
            const interval = setInterval(() => {
                remaining--;
                if (timerSeconds) timerSeconds.innerText = remaining;
                if (timerRing) {
                    const percent = (remaining / activeSession.expirySeconds) * 360;
                    timerRing.style.background =
                        `conic-gradient(#10b981 0deg ${percent}deg, #e5e7eb ${percent}deg 360deg)`;
                }
                if (remaining <= 0) {
                    clearInterval(interval);
                    if (timerRing) timerRing.style.background =
                        'conic-gradient(#ef4444 0deg 360deg, #ef4444 360deg 360deg)';
                }
            }, 1000);
        }

        function resetScanner() {
            document.getElementById('result-area').style.display = 'none';
            if (activeSession.hasSession && activeSession.expirySeconds > 0) {
                startScanner(currentCameraId);
            }
        }

        function updateSessionDisplay() {
            const sessionContent = document.getElementById('sessionContent');
            if (activeSession.hasSession) {
                sessionContent.innerHTML = `
                    <div class="session-info-row">
                        <span><i class="bi bi-book"></i> Course</span>
                        <span><strong>${activeSession.courseName}</strong></span>
                    </div>
                    <div class="session-info-row">
                        <span><i class="bi bi-person-badge"></i> Lecturer</span>
                        <span>${activeSession.lecturerName}</span>
                    </div>
                    <div class="session-info-row">
                        <span><i class="bi bi-door-open"></i> Room</span>
                        <span>${activeSession.room}</span>
                    </div>
                    <div style="text-align: center; margin-top: 1rem;">
                        <div class="timer-ring" id="timerRing">
                            <div class="timer-inner" id="timerSeconds">${activeSession.expirySeconds}</div>
                        </div>
                        <p style="font-size: 0.7rem; margin-top: 0.5rem;">QR expires in</p>
                    </div>
                `;
                startTimer();
            } else {
                sessionContent.innerHTML = `
                    <div class="no-session">
                        <i class="bi bi-qr-code" style="font-size: 2rem;"></i>
                        <p style="margin-top: 0.5rem;">No active session detected</p>
                        <p style="font-size: 0.7rem;">Ask your lecturer to generate a QR code</p>
                    </div>
                `;
            }
        }

        function addToHistory(courseName) {
            const historyDiv = document.getElementById('attendanceHistory');
            const newItem = document.createElement('div');
            newItem.className = 'history-item';
            const now = new Date();
            newItem.innerHTML = `
                <div>
                    <div class="history-course">${courseName}</div>
                    <div class="history-date">${now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })}</div>
                </div>
                <div class="history-time">${now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                <div><span class="badge-success">Present</span></div>
            `;
            historyDiv.insertBefore(newItem, historyDiv.firstChild);
        }

        function showManualModal() {
            document.getElementById('manualModal').classList.add('show');
        }

        function closeManualModal() {
            document.getElementById('manualModal').classList.remove('show');
            document.getElementById('manualCode').value = '';
        }

        function verifyManualCode() {
            const code = document.getElementById('manualCode').value;
            if (code.length !== 6) {
                showToast('Please enter a valid 6-digit code', 'error');
                return;
            }
            showSuccessAnimation(activeSession.courseName);
            addToHistory(activeSession.courseName);
            playBeep();
            vibrate();
            closeManualModal();
            setTimeout(() => closeSuccessOverlay(), 2000);
        }

        // Event Listeners
        document.getElementById('switchCameraBtn')?.addEventListener('click', switchCamera);
        document.getElementById('toggleTorchBtn')?.addEventListener('click', toggleTorch);
        document.getElementById('zoomInBtn')?.addEventListener('click', () => zoomCamera(0.5));
        document.getElementById('zoomOutBtn')?.addEventListener('click', () => zoomCamera(-0.5));

        // Initialize
        window.onload = () => {
            updateSessionDisplay();
            startScanner();
        };
    </script>
@endsection
