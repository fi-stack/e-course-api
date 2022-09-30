<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>Verifikasi Akun</div>
    <div>Nama: {{ $data['name'] }}</div>
    <div>Email: {{ $data['email'] }}</div>
    <div>Password: {{ $data['password'] }}</div>
    <div>Silahkan lakukan aktivasi akun dengan klik <a href="http://localhost:3002/account-activation?token={{ $data['token'] }}">disini</a></div>
</body>

</html>