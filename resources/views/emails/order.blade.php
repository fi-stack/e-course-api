<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>User</div>
    <div>Nama: {{ $data['user']->name }}</div>
    <div>Email: {{ $data['user']->email }}</div>
    <hr />
    <div>Service</div>
    <div>{{ $data['service']->name }}</div>
    <div>{{ $data['service']->total }}</div>
    <div>{{ $data['service']->discount }}</div>
    <div>{{ $data['service']->price }}</div>
    <hr />
    <div>Payment</div>
    <div>{{ $data['payment_type'] }}</div>
    <div>silahkan lakukan pembayaran dan unggah bukti pembayaran dengan klik <a href="http://localhost:3002/transactions">disini</a></div>
</body>

</html>