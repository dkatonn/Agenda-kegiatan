<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:linear-gradient(135deg,#0b3d75,#123e68 42%,#f0c615 180%);font-family:"Poppins",sans-serif}
        .box{width:min(460px,92vw);background:rgba(255,255,255,.96);border-radius:24px;padding:28px;box-shadow:0 24px 60px rgba(0,0,0,.22)}
        h1{margin:0 0 8px;color:#0c4482;font-size:34px}p{margin:0 0 20px;color:#567090}label{display:grid;gap:8px;margin-bottom:14px;color:#45607f;font-weight:600;font-size:14px}input{width:100%;padding:13px 14px;border:1px solid #d4deeb;border-radius:14px;font:inherit}button{width:100%;padding:14px;border:0;border-radius:14px;background:#0c4482;color:#fff;font:inherit;font-weight:700;cursor:pointer}.error{margin:0 0 14px;padding:12px 14px;border-radius:14px;background:rgba(199,58,69,.12);color:#b12f39;font-weight:600}.hint{margin-top:18px;padding:14px;border-radius:14px;background:#eff5fb;color:#37577c;font-size:14px;line-height:1.6}.hint strong{display:block;color:#0c4482}
    </style>
</head>
<body>
    <form class="box" method="POST" action="{{ route('login.store') }}">
        @csrf
        <h1>Masuk Admin</h1>
        <p>Silakan masuk dulu sebelum mengakses halaman CRUD.</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <label>NIP
            <input type="text" name="nip" inputmode="numeric" pattern="[0-9]{18}" maxlength="18" value="{{ old('nip') }}" placeholder="18 digit angka" required>
        </label>

        <label>Password
            <input type="password" name="password" minlength="8" placeholder="Minimal 8 karakter" required>
        </label>

        <button type="submit">Masuk</button>

        <div class="hint">
            <strong>Kredensial seed sementara</strong>
            Superadmin: <code>100000000000000001</code> / <code>password123</code><br>
            Admin TU: <code>100000000000000002</code> / <code>password123</code><br>
            Admin Data: <code>100000000000000003</code> / <code>password123</code>
        </div>
    </form>
</body>
</html>
