@extends('layouts.app')

@section('title', 'Lecturer Dashboard | MTU Academic Intelligence')
@section('role', 'Lecturer')
@section('page-title', 'Lecturer Intelligence Dashboard')
@section('welcome-text', 'Welcome back, ' . Auth::user()->name)

@section('sidebar')
    <div class="nav-label">Main</div>
    <a href="{{ route('lecturer.dashboard') }}" class="nav-item active"><i
            class="bi bi-speedometer2"></i><span>Dashboard</span></a>
    <a href="#" class="nav-item"><i class="bi bi-qr-code-scan"></i><span>Take Attendance</span></a>
    <a href="#" class="nav-item"><i class="bi bi-clock-history"></i><span>Session History</span></a>
    <a href="#" class="nav-item"><i class="bi bi-people"></i><span>All Students</span></a>
    <a href="#" class="nav-item"><i class="bi bi-calendar3"></i><span>Schedule</span></a>
    <div class="nav-label">Reports</div>
    <a href="#" class="nav-item"><i class="bi bi-download"></i><span>Export Reports</span></a>
    <a href="#" class="nav-item"><i class="bi bi-megaphone"></i><span>Announcements</span></a>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto;">

        <!-- Row 1: Stats Cards (7 cards) -->
        <div style="display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap;">
            <div
                style="background: white; padding: 20px; border-radius: 12px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: 800; color: #800000;">156</div>
                <div style="color: #666;">Total Students</div>
                <small>Across 4 courses</small>
            </div>
            <div
                style="background: white; padding: 20px; border-radius: 12px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: 800; color: #dc2626;">23</div>
                <div style="color: #666;">At Risk Students</div>
                <small>Need intervention</small>
            </div>
            <div
                style="background: white; padding: 20px; border-radius: 12px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: 800; color: #800000;">78%</div>
                <div style="color: #666;">Avg Attendance</div>
                <small style="color: #dc2626;">↓ 3% from last month</small>
            </div>
            <div
                style="background: white; padding: 20px; border-radius: 12px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: 800; color: #800000;">84</div>
                <div style="color: #666;">Course Engagement Score</div>
                <small style="color: #10b981;">↑ +5 this month</small>
            </div>
            <div
                style="background: white; padding: 20px; border-radius: 12px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: 800; color: #f59e0b;">7</div>
                <div style="color: #666;">Low Attendance Alerts</div>
                <small style="color: #dc2626;">Requires Action</small>
            </div>
            <div
                style="background: white; padding: 20px; border-radius: 12px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size: 32px; font-weight: 800; color: #800000;">2</div>
                <div style="color: #666;">Active Sessions</div>
                <button onclick="alert('New Session')"
                    style="margin-top: 8px; background: #800000; color: white; border: none; padding: 5px 12px; border-radius: 6px; cursor: pointer;">+
                    New Session</button>
            </div>
        </div>

        <!-- Row 2: QR + Manual Attendance -->
        <div style="display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap;">
            <div
                style="flex: 1; background: linear-gradient(135deg, #800000, #5f0000); color: white; padding: 20px; border-radius: 12px; text-align: center;">
                <h4><i class="bi bi-qr-code"></i> Active QR Session</h4>
                <div
                    style="background: white; width: 100px; height: 100px; margin: 15px auto; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-qr-code-scan" style="font-size: 50px; color: #800000;"></i>
                </div>
                <p>Database Systems (CS301) - Room A-203</p>
                <p>QR expires: <span id="timer" style="font-weight: bold;">45</span> seconds</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button onclick="alert('Session ended')"
                        style="background: #dc2626; color: white; border: none; padding: 6px 15px; border-radius: 6px; cursor: pointer;">End
                        Session</button>
                    <button onclick="alert('QR Refreshed')"
                        style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 6px 15px; border-radius: 6px; cursor: pointer;">Refresh
                        QR</button>
                </div>
            </div>
            <div
                style="flex: 1; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h4><i class="bi bi-pencil-square"></i> Manual Attendance Fallback</h4>
                <select style="width: 100%; padding: 8px; margin: 10px 0; border-radius: 6px; border: 1px solid #ddd;">
                    <option>Database Systems (CS301) - Today 9:00 AM</option>
                    <option>Networking (CS302) - Today 11:00 AM</option>
                </select>
                <select style="width: 100%; padding: 8px; margin: 10px 0; border-radius: 6px; border: 1px solid #ddd;">
                    <option>Select Student</option>
                    <option>Eaindra Kyaw</option>
                    <option>Su Mon Kyaw</option>
                    <option>Phone Myint</option>
                </select>
                <select style="width: 100%; padding: 8px; margin: 10px 0; border-radius: 6px; border: 1px solid #ddd;">
                    <option>Present</option>
                    <option>Absent</option>
                    <option>Late</option>
                </select>
                <button onclick="alert('Manual attendance recorded')"
                    style="width: 100%; background: #800000; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer;">Save
                    Attendance</button>
                <small style="color: #999;">Use when students cannot scan QR code</small>
            </div>
        </div>

        <!-- Row 3: Live Attendance -->
        <div style="background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div
                style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h4><i class="bi bi-clock-history"></i> Live Attendance - Database Systems</h4>
                <span
                    style="background: #10b981; color: white; padding: 3px 10px; border-radius: 20px; font-size: 12px;">LIVE
                    NOW</span>
            </div>
            <div style="padding: 20px;">
                <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; text-align: center; background: #f9fafb; padding: 15px; border-radius: 10px;">
                        <div style="font-size: 28px; font-weight: 800; color: #800000;">28</div>
                        <div>Present</div>
                        <small>71%</small>
                    </div>
                    <div style="flex: 1; text-align: center; background: #f9fafb; padding: 15px; border-radius: 10px;">
                        <div style="font-size: 28px; font-weight: 800; color: #dc2626;">8</div>
                        <div>Absent</div>
                        <small>21%</small>
                    </div>
                    <div style="flex: 1; text-align: center; background: #f9fafb; padding: 15px; border-radius: 10px;">
                        <div style="font-size: 28px; font-weight: 800; color: #f59e0b;">3</div>
                        <div>Late</div>
                        <small>8%</small>
                    </div>
                    <div style="flex: 1; text-align: center; background: #f9fafb; padding: 15px; border-radius: 10px;">
                        <div style="font-size: 28px; font-weight: 800; color: #800000;">39</div>
                        <div>Total</div>
                    </div>
                </div>
                <div style="height: 8px; background: #e5e7eb; border-radius: 10px; overflow: hidden;">
                    <div style="width: 72%; height: 100%; background: #10b981; border-radius: 10px;"></div>
                </div>
                <div style="margin-top: 15px; padding: 10px; background: #fef9c3; border-radius: 8px;">
                    <strong><i class="bi bi-clock"></i> Late Arrivals (3):</strong>
                    <span
                        style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; margin-left: 5px;">Eaindra
                        Kyaw (+8 min)</span>
                    <span
                        style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; margin-left: 5px;">Su
                        Mon Kyaw (+12 min)</span>
                    <span
                        style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px; margin-left: 5px;">Phone
                        Myint (+5 min)</span>
                </div>
            </div>
        </div>

        <!-- Row 4: Roll Call Calculation -->
        <div style="background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="padding: 15px 20px; border-bottom: 1px solid #eee;">
                <h4><i class="bi bi-star-fill"></i> Roll Call Mark Calculation Reference</h4>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; text-align: center;">
                    <div><strong>95-100%</strong><br><span
                            style="background: #10b981; color: white; padding: 2px 8px; border-radius: 20px;">10</span>
                    </div>
                    <div><strong>90-94%</strong><br><span
                            style="background: #10b981; color: white; padding: 2px 8px; border-radius: 20px;">9</span></div>
                    <div><strong>85-89%</strong><br><span
                            style="background: #10b981; color: white; padding: 2px 8px; border-radius: 20px;">8</span></div>
                    <div><strong>80-84%</strong><br><span
                            style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 20px;">7</span></div>
                    <div><strong>75-79%</strong><br><span
                            style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 20px;">6</span></div>
                    <div><strong>70-74%</strong><br><span
                            style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px;">5</span>
                    </div>
                    <div><strong>65-69%</strong><br><span
                            style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px;">4</span>
                    </div>
                    <div><strong>60-64%</strong><br><span
                            style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px;">3</span>
                    </div>
                    <div><strong>55-59%</strong><br><span
                            style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px;">2</span>
                    </div>
                    <div><strong>Below 55%</strong><br><span
                            style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px;">0-1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 5: Chart + Insights -->
        <div style="display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap;">
            <div
                style="flex: 1; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h4><i class="bi bi-graph-up"></i> Course Engagement Trend (6 Weeks)</h4>
                <canvas id="engagementChart" height="200"></canvas>
            </div>
            <div
                style="flex: 1; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h4><i class="bi bi-lightbulb"></i> Smart Insights + Prediction</h4>
                <div style="margin-top: 15px;">
                    <div
                        style="padding: 10px; background: #fffbeb; border-left: 3px solid #f59e0b; margin-bottom: 10px; border-radius: 6px;">
                        <strong>📊 Low Attendance Pattern</strong><br>Tuesday 8 AM: 62% | Thursday: 81%
                    </div>
                    <div
                        style="padding: 10px; background: #fef2f2; border-left: 3px solid #dc2626; margin-bottom: 10px; border-radius: 6px;">
                        <strong>📉 Engagement Drop</strong><br>Networking dropped 18% this month
                    </div>
                    <div
                        style="padding: 10px; background: #eff6ff; border-left: 3px solid #3b82f6; margin-bottom: 10px; border-radius: 6px;">
                        <strong>🔮 Attendance Prediction</strong><br>8 students likely at-risk next week
                    </div>
                    <div style="padding: 10px; background: #ecfdf5; border-left: 3px solid #10b981; border-radius: 6px;">
                        <strong>📈 Recovery Tracking</strong><br>5 students improved by 10% this month
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 6: Session History -->
        <div style="background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="padding: 15px 20px; border-bottom: 1px solid #eee;">
                <h4><i class="bi bi-clock-history"></i> Session History</h4>
            </div>
            <div style="overflow-x: auto; padding: 0 20px 20px 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Date</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Course</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Present</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Absent</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Late</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Attendance %</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">2024-06-10</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Database Systems</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">28</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">8</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">3</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">72%</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><span
                                    style="background: #10b981; color: white; padding: 2px 8px; border-radius: 20px;">Completed</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">2024-06-07</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Networking</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">25</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">12</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">2</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">64%</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><span
                                    style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px;">Low</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">2024-06-05</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Operating Systems</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">35</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">3</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">1</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">90%</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><span
                                    style="background: #10b981; color: white; padding: 2px 8px; border-radius: 20px;">Excellent</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Row 7: At-Risk Students -->
        <div style="background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div
                style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h4><i class="bi bi-exclamation-triangle"></i> At-Risk Students</h4>
                <span style="background: #dc2626; color: white; padding: 3px 10px; border-radius: 20px;">23 Students</span>
            </div>
            <div style="overflow-x: auto; padding: 0 20px 20px 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Student</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Course</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Attendance</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Risk</th>
                            <th style="text-align: left; padding: 12px; background: #f9fafb;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><strong>Eaindra Kyaw</strong></td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Networking</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">58%<div
                                    style="height: 4px; background: #e5e7eb; margin-top: 5px;">
                                    <div style="width: 58%; height: 100%; background: #dc2626;"></div>
                                </div>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><span
                                    style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px;">High</span>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><button
                                    onclick="alert('Alert sent')"
                                    style="background: none; border: 1px solid #ddd; padding: 4px 10px; border-radius: 6px; cursor: pointer;">Notify</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><strong>Su Mon Kyaw</strong></td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Database</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">62%<div
                                    style="height: 4px; background: #e5e7eb; margin-top: 5px;">
                                    <div style="width: 62%; height: 100%; background: #f59e0b;"></div>
                                </div>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><span
                                    style="background: #f59e0b; color: white; padding: 2px 8px; border-radius: 20px;">Medium</span>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><button
                                    onclick="alert('Alert sent')"
                                    style="background: none; border: 1px solid #ddd; padding: 4px 10px; border-radius: 6px; cursor: pointer;">Notify</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><strong>Phone Myint</strong></td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">Web Dev</td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;">55%<div
                                    style="height: 4px; background: #e5e7eb; margin-top: 5px;">
                                    <div style="width: 55%; height: 100%; background: #dc2626;"></div>
                                </div>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><span
                                    style="background: #dc2626; color: white; padding: 2px 8px; border-radius: 20px;">High</span>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #eee;"><button
                                    onclick="alert('Alert sent')"
                                    style="background: none; border: 1px solid #ddd; padding: 4px 10px; border-radius: 6px; cursor: pointer;">Notify</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="padding: 15px 20px; border-top: 1px solid #eee;">
                <button onclick="alert('Announcement sent')"
                    style="width: 100%; background: #800000; color: white; border: none; padding: 10px; border-radius: 8px; cursor: pointer;">Send
                    Announcement to All Students</button>
            </div>
        </div>

        <!-- Row 8: Export Reports -->
        <div style="background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="padding: 15px 20px; border-bottom: 1px solid #eee;">
                <h4><i class="bi bi-download"></i> Export Reports</h4>
            </div>
            <div style="padding: 20px;">
                <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                    <select style="padding: 8px; border-radius: 6px; border: 1px solid #ddd;">
                        <option>All Courses</option>
                        <option>Database Systems</option>
                        <option>Networking</option>
                    </select>
                    <button onclick="alert('PDF Export')"
                        style="background: none; border: 1px solid #ddd; padding: 8px 16px; border-radius: 6px; cursor: pointer;"><i
                            class="bi bi-file-pdf"></i> PDF</button>
                    <button onclick="alert('Excel Export')"
                        style="background: none; border: 1px solid #ddd; padding: 8px 16px; border-radius: 6px; cursor: pointer;"><i
                            class="bi bi-file-excel"></i> Excel</button>
                    <button onclick="alert('CSV Export')"
                        style="background: none; border: 1px solid #ddd; padding: 8px 16px; border-radius: 6px; cursor: pointer;"><i
                            class="bi bi-filetype-csv"></i> CSV</button>
                </div>
            </div>
        </div>

    </div>

    <!-- Floating Uni Bot -->
    <div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
        <button onclick="openUniBot()"
            style="background: #800000; color: white; border: none; padding: 12px 20px; border-radius: 50px; font-weight: 600; cursor: pointer; box-shadow: 0 5px 15px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-robot" style="font-size: 1.2rem;"></i> Uni Bot
        </button>
    </div>

    <script>
        function openUniBot() {
            alert(
                '🤖 Uni Bot: Lecturer Help\n\nYou can ask me:\n- Show at-risk students\n- Attendance summary for Networking\n- Which course has lowest attendance?\n- Export attendance report');
        }
        let timer = 45;
        setInterval(() => {
            if (timer > 0) {
                timer--;
                document.getElementById('timer').innerText = timer;
            }
        }, 1000);
        new Chart(document.getElementById('engagementChart'), {
            type: 'line',
            data: {
                labels: ['W1', 'W2', 'W3', 'W4', 'W5', 'W6'],
                datasets: [{
                        label: 'Database Systems',
                        data: [85, 83, 80, 78, 76, 74],
                        borderColor: '#800000',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Networking',
                        data: [82, 78, 74, 70, 68, 62],
                        borderColor: '#dc2626',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Web Development',
                        data: [88, 87, 86, 85, 84, 83],
                        borderColor: '#10b981',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    </script>
@endsection
