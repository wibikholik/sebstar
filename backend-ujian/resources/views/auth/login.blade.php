<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SEBSTAR</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Background dengan Gradasi Merah-Putih Tegas + Efek Polkadot Grid Modern */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f5f9;
            background-image: 
                radial-gradient(rgba(230, 57, 70, 0.15) 1.5px, transparent 1.5px), 
                linear-gradient(135deg, #fceade 0%, #f4f5f9 50%, #ffffff 100%);
            background-size: 24px 24px, 100% 100%;
            background-attachment: fixed;
        }

        /* Container Card Login Glassmorphism Nyata */
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 24px;
            border: 1px solid rgba(230, 57, 70, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
            text-align: center;
        }

        /* Bagian Logo dan Header */
        .login-header img {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            object-fit: cover;
            box-shadow: 0 6px 15px rgba(230, 57, 70, 0.25);
            margin-bottom: 15px;
        }

        .login-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #1e1e2f;
            letter-spacing: 1px;
        }

        .login-header p {
            margin: 5px 0 30px 0;
            font-size: 13px;
            color: #cd0000;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Styling Input Group Premium */
        .input-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a0b0;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            font-size: 14px;
            color: #1e1e2f;
            font-weight: 600;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            outline: none;
            background: #ffffff;
            border-color: #cd0000;
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.1);
        }

        /* Saat input difokuskan, icon ikut menyala merah */
        .input-group input:focus + i {
            color: #cd0000;
        }

        /* Notifikasi Error */
        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 12px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid rgba(231, 76, 60, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }

        /* Tombol Login Sign-In Premium */
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #cd0000 0%, #950000 100%);
            color: #ffffff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(205, 0, 0, 0.25);
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(205, 0, 0, 0.35);
            filter: brightness(1.1);
        }

        .btn-login:active {
            transform: translateY(0);
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-header">
            <img src="{{ asset('images/LOGO.png') }}" alt="Logo SEBSTAR">
            <h2>SEBSTAR</h2>
            <p>Sistem Ujian Digital</p>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Email atau password salah!</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <input type="email" name="email" placeholder="Masukkan Email" value="{{ old('email') }}" required autofocus>
                <i class="fas fa-envelope"></i>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Masukkan Password" required>
                <i class="fas fa-lock"></i>
            </div>

            <button type="submit" class="btn-login">
                Masuk Sistem <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>
    </div>

</body>
</html>