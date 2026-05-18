<h2>Login</h2>

<?php if(isset($_GET['error']))
    
    echo "<p>Invalid email or password</p>"; ?>
<?php if(isset($_GET['success'])) 
    
    echo "<p>Registration successful</p>"; ?>

<form method="POST">

<label>Email:</label>

<br>
<input type="email" name="email" required>

<small>Enter registered email</small>
<br>
<br>

<label>Password:</label><br>
<input type="password" name="password" required>
<small>Enter your password</small>
<br><br>

<label>
<input type="checkbox" name="remember"> Remember Me (30 days login)

</label>

<br>
<br>

<button name="submit">Login</button>

</form>

<a href="register.php">Create new account</a>