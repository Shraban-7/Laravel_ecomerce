<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
    <p>Hello , {{ $formData['user']->name }}</p>
    <h3>You have request to change password:</h3>

    <p>Plese, click the link given below for resetting password</p>
    <a href="{{ route('user.reset_password',$formData['token']) }}">Click here</a>

</body>
</html>
