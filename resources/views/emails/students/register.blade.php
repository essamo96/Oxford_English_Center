<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your Email Template</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-MNq3Kj+mn3B9DvStbHnxBm45nBbId70mDbEWTJrKTQqq0qjCpw+1e9J14v6h0ORoI0VCIdb+YdGJyz0rZw5O5Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
@component('mail::message')
# Dear <strong style="font-size: 16px; color:#002147"><strong >,{{$username}}</strong> </strong>
# <strong style="font-size: 16px; color:#000000">Welcome to<strong style="font-size: 16px; color:#002147"> {{ config('app.name') }}</strong>. Please keep your account information safe.</strong>


Your Account Info:
@component('mail::panel')
<strong style="font-size: 16px; color:#000000">Your username is:  </strong><span>{{$password}}</span><br> 

<strong style="font-size: 16px; color:#000000">Your password is: </strong>{{$password}} <br>

@endcomponent
# <span style="font-size: 16px; color:#000000">Thank you for choosing Oxford Institution, and we look forward to achieving success together.</span>

@component('mail::button', ['url' => 'https://www.oxford.ps/login'])
  Login Page
@endcomponent

Best regards,<br>
<span style="font-size: 16px; color:#002147">{{ config('app.name') }}</span>
@endcomponent
</body> 