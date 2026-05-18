<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../public/style.css">
</head>

<body>

<div class="form-box">

<h2>Login</h2>

<form method="POST">

<label>Email:</label>
<input type="email" name="email" required>

<label>Password:</label>
<input type="password" name="password" required>

<label>
<input type="checkbox" name="remember">
Remember Me
</label>

<button type="submit" name="submit">Login</button>

</form>

<?php if(isset($_GET['error'])) echo "<p style='color:red'>Invalid login</p>"; ?>
<?php if(isset($_GET['success'])) echo "<p style='color:green'>Registration success</p>"; ?>

<br>
<a href="register.php">Create new account</a>

</div>

</body>
</html>