<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>Telah Menyelesaikan Kursus {{ $certificate->course->name }}</div>
    <div>{{ $certificate->user->name }}</div>
    <div>{{ $certificate->user->email }}</div>
</body>

</html>