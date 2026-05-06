<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Sebstar</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/login.css'])

    
</head>

<body>

<div class="login-container">

    <!-- 🔥 LOGO CENTER -->
    <div class="logo-box">
        <img src="{{ asset('images/LOGO.png') }}" alt="Logo">
    </div>

    <div class="login-header">
        <h2>Login</h2>
        <p>Selamat datang di SEBSTAR</p>
    </div>

    @if(session('error'))
        <div class="error">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <div class="form-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" class="login-btn">
            Masuk
        </button>
    </form>

</div>

</body>
</html>