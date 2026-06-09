<!DOCTYPE html>
<html>

<head>
    <title>Student Dashboard - MTU Academic System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f5f0e8;
            margin: 0;
        }

        h1 {
            color: #800000;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-logout {
            background: #800000;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-logout:hover {
            background: #600000;
        }

        .btn-back {
            background: #666;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .info-card {
            background: #f8f9fa;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: left;
        }

        .health-score {
            font-size: 48px;
            font-weight: bold;
            color: #800000;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Student Dashboard</h1>
        <p>Welcome, {{ Auth::user()->name }}!</p>
        <p>Email: {{ Auth::user()->email }}</p>
        <p>Role: Student</p>
        <hr>

        <div class="info-card">
            <h3>🎓 Academic Health Score</h3>
            <div class="health-score">86%</div>
            <p>Your academic health is Stable</p>
        </div>

        <div class="info-card">
            <h3>📚 My Enrolled Courses</h3>
            <p>Course list will appear here</p>
        </div>

        <div class="info-card">
            <h3>📊 Attendance Summary</h3>
            <p>Attendance: 82%</p>
            <p>Roll Call Mark: 7.5/10</p>
            <p>Eligibility Status: Eligible</p>
        </div>

        <div class="info-card">
            <h3>📅 Today's Timetable</h3>
            <p>Schedule will appear here</p>
        </div>

        <a href="/dashboard" class="btn-back">Back to Dashboard</a>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</body>

</html>
