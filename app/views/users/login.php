<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST">

<label>Email:</label><br>
<input type="email" name="email" required>
<br><br>

<label>Password:</label><br>
<input type="password" name="password" required>
<br><br>

<label>
<input type="checkbox" name="remember">
Remember Me
</label>

<br><br>

<button type="submit" name="submit">Login</button>

</form>

<br>

<?php if(isset($_GET['error'])) echo "<p style='color:red'>Invalid login</p>"; ?>
<?php if(isset($_GET['success'])) echo "<p style='color:green'>Registration success</p>"; ?>

<br>
<a href="register.php">Create new account</a>

</body>
</html>