<h2>User Profile</h2>

<?php if(isset($_GET['updated'])) { ?>
<p style="color:green;">Profile updated successfully</p>
<?php } ?>

<!-- SHOW PROFILE DATA -->
<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Email:</b> <?= $user['email'] ?></p>
<p><b>Bio:</b> <?= $user['bio'] ?></p>

<?php 
$social = json_decode($user['social_links'], true);
?>

<p><b>Facebook:</b> 
<?= isset($social['facebook']) ? $social['facebook'] : '' ?>
</p>

<!-- PROFILE IMAGE -->
<?php if(!empty($user['profile_pic_path'])) { ?>
    <img src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>" width="120">
<?php } else { ?>
    <p>No profile image uploaded</p>
<?php } ?>

<hr>

<!-- UPDATE FORM -->
<h3>Update Profile</h3>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label><br>
<textarea name="bio"><?= $user['bio'] ?></textarea>
<small>Write something about yourself</small>
<br><br>

<label>Facebook:</label><br>
<input type="text" name="facebook"
value="<?= isset($social['facebook']) ? $social['facebook'] : '' ?>">
<small>Paste your Facebook profile link</small>
<br><br>

<label>Profile Image:</label><br>
<input type="file" name="image">
<small>Upload JPG/PNG only</small>
<br><br>

<button name="submit">Update Profile</button>

</form>

<br>

<!-- BACK BUTTON -->
<a href="logout.php">
<button type="button">Logout & Back to Home</button>
</a>