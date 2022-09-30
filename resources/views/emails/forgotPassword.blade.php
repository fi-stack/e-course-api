<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>Lupa Password</div>
    <div>Nama: {{ $data['name'] }}</div>
    <div>Email: {{ $data['email'] }}</div>
    <div>ubah password dengan klik <a href="http://localhost:3002/change-password?token={{ $data['token'] }}">disini</a></div>
</body>

</html>