<h2>User Profile</h2>
<?php if(isset($_GET['updated'])): ?>
    <p style="color:green;">Profile Updated Successfully</p>
<?php endif; ?>

<h3>Current Profile Info</h3>

<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Email:</b> <?= $user['email'] ?></p>
<p><b>Bio:</b> <?= $user['bio'] ?></p>
<?php 
$social = json_decode($user['social_links'], true);
?>
<p><b>Facebook:</b> 
    <?php if(isset($social['facebook'])) echo $social['facebook']; ?>
</p>
<?php if($user['profile_pic_path']) { ?>
    <img src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>" width="120">
<?php } ?>

<hr>

<h3>Update Profile</h3>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label>
<br>
<textarea name="bio"><?= $user['bio'] ?></textarea>
<br>
<br>

<label>Facebook:</label>
<br>
<input type="text" name="facebook"
value="<?php if(isset($social['facebook'])) echo $social['facebook']; ?>">
<br><br>

<label>Change Image:</label>
<br>
<input type="file" name="image">
<br>

<br>

<button type="submit" name="submit">Update Profile</button>

<a href="back.php">

    <button type="button">Create New Account (Back)</button>
</a>

</form>