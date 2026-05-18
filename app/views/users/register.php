<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../public/style.css">
</head>
<body>

<h2>Register</h2>

<form method="POST">

<label>Name:</label><br>
<input type="text" name="name" required>
<small>Enter your full name</small>
<br>
<br>

<label>Email:</label><br>
<input type="email" name="email" required>
<small>Use valid email</small>
<br>
<br>

<label>Password:</label><br>
<input type="password" name="password" required>
<small>Min 8 characters</small>
<br>
<br>

<label>Role:</label><br>
<select name="role">
    <option value="reader">Reader</option>
    <option value="author">Author Request</option>
</select>
<small>Author will be pending admin approval</small>
<br>
<br>

<button type="submit" name="submit">Register</button>

</form>

<br>
<a href="login.php">Already have account? Login</a>

</body>
</html>