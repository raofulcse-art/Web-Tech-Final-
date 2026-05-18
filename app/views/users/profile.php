<h2>My Profile</h2>

<?php if(isset($_GET['updated'])) echo "<p style='color:green'>Profile Updated</p>"; ?>

<!-- IMAGE SHOW -->
<?php if(!empty($user['profile_pic_path'])) { ?>
    <img src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>" width="120">
<?php } else { ?>
    <p>No Image</p>
<?php } ?>

<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Email:</b> <?= $user['email'] ?></p>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label><br>
<textarea name="bio"><?= $user['bio'] ?? '' ?></textarea>
<br><br>

<?php
$social = json_decode($user['social_links'], true);
?>

<label>Twitter:</label><br>
<input type="text" name="twitter"
value="<?= $social['twitter'] ?? '' ?>">
<br><br>

<label>GitHub:</label><br>
<input type="text" name="github"
value="<?= $social['github'] ?? '' ?>">
<br><br>

<label>Profile Image:</label><br>
<input type="file" name="image">
<br><br>

<button type="submit" name="submit">Update Profile</button>

</form>

<br>
<a href="logout.php">Logout</a>