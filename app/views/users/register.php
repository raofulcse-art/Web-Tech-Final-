<h2>Register</h2>
<?php if(isset($_GET['error']) && $_GET['error']=='pass') 
    echo "<p>Password must be 8+ characters</p>"; ?>

<?php if(isset($_GET['error']) && $_GET['error']=='email') 
    echo "<p>Email already exists</p>"; ?>

<form method="POST">

<label>Name:</label>
<br>
<input type="text" name="name" required>

<small>Enter your full name</small>
<br>
<br>

<label>Email:</label>
<br>
<input type="email" name="email" required>

<small>Enter valid email address</small>
<br>
<br>

<label>Password:</label>
<br>
<input type="password" name="password" required>

<small>Minimum 8 characters required</small>
<br>
<br>

<label>Role:</label><br>
<input type="radio" name="role" value="reader" checked> Reader

<input type="radio" name="role" value="author"> Author

<small>If author , admin approval required</small>
<br><br>

<button name="submit">Register</button>

</form>

<a href="login.php">Already have account? Login</a>