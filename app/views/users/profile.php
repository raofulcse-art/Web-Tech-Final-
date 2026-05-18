<h2>My Profile</h2>

<?php if(isset($_GET['updated'])) 
    echo "<p>Profile updated successfully</p>"; ?>
<?php if(isset($_GET['login']))
     echo "<p>Login successful</p>"; ?>

<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Email:</b> <?= $user['email'] ?></p>
<p><b>Role:</b> <?= $user['role'] ?></p>

<p><b>Bio:</b> <?= $user['bio'] ?></p>

<?php
$social = json_decode($user['social_links'], true);
?>

<p><b>Facebook:</b> <?= $social['facebook'] ?? '' ?></p>

<?php if(!empty($user['profile_pic_path'])) { ?>
    <img src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>" width="120">
<?php } ?>

<hr>

<h3>Update Profile</h3>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label><br>
<textarea name="bio"><?= $user['bio'] ?>
</textarea>
<small>Write something about yourself</small>
<br>


<br>

<label>Facebook:</label><br>
<input type="text" name="facebook" value="<?= $social['facebook'] ?? '' ?>">
<br><br>

<label>Profile Picture:</label><br>
<input type="file" name="image">

<small>Only JPG/PNG ≤ 1MB</small>
<br>

<br>

<button name="submit">Update your Profile</button>

</form>

<br>

<a href="logout.php">Logout</a>
<br>
<a href="back.php">Back (Reset Session)</a>