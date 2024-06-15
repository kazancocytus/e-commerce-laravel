<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size:16px">

    <p>Hello, {{ $formData['user']->name }}</p>

    <h1>You have requested to change password</h1>

    <p>Please click the link below to reset password</p>

    <a href="{{ route('reset-password',$formData['token']) }}">Click Here</a>

    <p>The token will expire after 1 hour</p>

    <p>Thanks</p>

</body>
</html>