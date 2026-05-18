<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../public/style.css">
</head>

<body>

<div class="form-box">

<h2>Register</h2>

<form method="POST">

<label>Name:</label>
<input type="text" name="name" required>
<small>Enter your full name</small>

<label>Email:</label>
<input type="email" name="email" required>
<small>Use valid email</small>

<label>Password:</label>
<input type="password" name="password" required>
<small>Min 8 characters</small>

<label>Role:</label>
<select name="role">
    <option value="reader">Reader</option>
    <option value="author">Author Request</option>
</select>
<small>Author will be pending admin approval</small>

<button type="submit" name="submit">Register</button>

</form>

<br>
<a href="login.php">Already have account? Login</a>

</div>

</body>
</html>