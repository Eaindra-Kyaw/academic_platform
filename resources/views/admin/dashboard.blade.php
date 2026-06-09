<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard - MTU Academic System</title>
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
    </style>
</head>

<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, {{ Auth::user()->name }}!</p>
        <p>Email: {{ Auth::user()->email }}</p>
        <p>Role: Administrator</p>
        <hr>
        <p>Full implementation coming in Week 5.</p>

        <a href="/dashboard" class="btn-back">Back to Dashboard</a>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</body>

</html>
