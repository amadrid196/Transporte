<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
<p>

    <i><b>Hello {{$driver->name}}</b></i><br><br>
    <i><b>Please see attached your payroll, if any questions or issues please let me know at your earliest convenience. Thanks</b></i><br><br>
    @if($message2)
        <br>
        {{$message2}}
        <br>
    @endif
    <br>
    <i><b>ACCOUNTING</b></i><br>

    <i><b>MILAM TRANSPORT LLC</b></i><br>

    <i><b>888-433-0331</b></i>
</p>
</body>
</html>