<h2>Register Account</h2>

<form method="POST">

<label>Name:</label><br>
<input type="text" name="name" required>
<small>Enter your full name</small>
<br><br>

<label>Email:</label><br>
<input type="email" name="email" required>
<small>Enter valid email address</small>
<br><br>

<label>Password:</label><br>
<input type="password" name="password" required>
<small>Minimum 8 characters required</small>
<br><br>

<label>Role:</label><br>
<input type="radio" name="role" value="reader" checked> Reader
<input type="radio" name="role" value="author"> Author
<br><br>

<button name="submit">Register</button>

<a href="login.php">
<button type="button">Already have account? Login</button>
</a>

</form>

<?php if(isset($_GET['error'])) { ?>
<p style="color:red;">Email already exists or error occurred</p>
<?php } ?>