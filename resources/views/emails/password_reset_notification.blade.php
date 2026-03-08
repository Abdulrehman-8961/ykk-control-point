<p>Dear {{ $name }},</p>
<p>Your password has been reset.</p>
<p><strong>Temporary Password:</strong> {{ $new_password }}</p>
<p>Please login and change your password immediately:</p>
<p><a href="{{ $login_url }}">{{ $login_url }}</a></p>
<p>Regards,<br>YKK - ControlPoint Team</p>
