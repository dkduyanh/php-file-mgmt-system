<p>Dear <?php echo $user->full_name; ?>,</p>

<p>Welcome to <?php echo APP_NAME; ?>. </p>
<p>Your account has been created and is now ready to use.</p>

<br/>

<p><?php echo ucfirst($loginType); ?>: <strong><?php echo $loginId; ?></strong></p>
<p>Password: <strong><?php echo $password; ?></strong></p>

<br/>

<p>If you have any questions, please contact administrator.</p>
<p>Sincerely,<br>
<p><?php echo APP_NAME; ?></p>