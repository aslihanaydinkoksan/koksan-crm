<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KÖKSAN CRM - Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .login-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 2rem 1rem;
            text-align: center;
        }

        .login-header h3 {
            margin: 0;
            font-weight: bold;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

    <div class="card login-card">
        <div class="login-header">
            <h3>KÖKSAN CRM</h3>
            <small>Kurumsal Yönetim Sistemi</small>
        </div>
        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li><small>{{ $error }}</small></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-muted fw-bold">E-Posta Adresi</label>
                    <input type="email" name="email" class="form-control" placeholder="adiniz@koksan.com"
                        value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted fw-bold">Şifre</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Sisteme Giriş Yap</button>
            </form>
        </div>
    </div>

</body>

</html>
