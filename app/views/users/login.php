<h2>Login Account</h2>

<form method="POST">

<label>Email:</label><br>
<input type="email" name="email" required>
<small>Enter your registered email</small>
<br><br>

<label>Password:</label><br>
<input type="password" name="password" required>
<small>Enter your password</small>
<br><br>

<button name="submit">Login</button>

</form>

<?php if(isset($_GET['error'])) { ?>
<p style="color:red;">Invalid email or password</p>
<?php } ?>

<?php if(isset($_GET['success'])) { ?>
<p style="color:green;">Registration successful! Please login</p>
<?php } ?>