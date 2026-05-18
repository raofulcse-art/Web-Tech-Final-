<?php session_start(); ?>

<h2>My Profile</h2>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label><br>
<textarea name="bio"><?= $user['bio'] ?? '' ?></textarea>
<br><br>

<label>Twitter:</label><br>
<input type="text" name="twitter">
<br><br>

<label>GitHub:</label><br>
<input type="text" name="github">
<br><br>

<label>Profile Image:</label><br>
<input type="file" name="image">
<br><br>

<button type="submit" name="submit">Update Profile</button>

</form>

<br>

<?php if(isset($_GET['updated'])) echo "<p style='color:green'>Profile Updated</p>"; ?>

<br>

<a href="logout.php">Logout</a>
<br>
<a href="back.php">Back (Logout & Reset)</a>