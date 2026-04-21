<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Ujian SMK</title>
</head>
<body>
    <div style="margin: 100px auto; width: 300px; text-align: center;">
        <h2>Login Sistem Ujian</h2>
        
        @if(session('error'))
            <p style="color: red;">{{ session('error') }}</p>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>