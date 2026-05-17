<h2>User Registration</h2>

<form method="POST">

<label>Full Name</label>
<br>
<small>Please enter your full name</small><br>
<input type="text" name="name" placeholder="e.g. John Doe" required>
<br><br>

<label>Email Address</label><br>
<small>Use a valid email address</small>
<br>
<input type="email" name="email" placeholder="e.g. example@gmail.com" required>
<br><br>

<label>Password</label><br>
<small>Minimum 8 characters required</small>
<br>
<input type="password" name="password" placeholder="Enter secure password" required>
<br><br>

<label>Select Account Type</label>
<br>
<small>Reader = normal user, Author = blog writer request</small><br>

<input type="radio" name="role" value="reader" checked> Reader
<input type="radio" name="role" value="author"> Author

<br>

<br>

<button name="submit">Create Account</button>

</form>