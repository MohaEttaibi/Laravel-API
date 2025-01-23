<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
    <h3>Reset Your Password</h3>
    <a href="{{ route('admin.reset.password', ['id' => $id]) }}">Click To Reset</a>
</body>
</html>