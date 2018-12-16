<!DOCTYPE html>
<html>
<head>
    <title>Welcome Email</title>
</head>

<body>
<h2>Hello{{$user['name']}} !!! </h2>
<br/>
Your registered email-id is {{$user['email']}} , Click the below link to verify your email Id and Login
<br/>
<a href="{{url('user/verify', $user->verifyToken)}}">Click Here to Verify Your Email</a>
</body>

</html>