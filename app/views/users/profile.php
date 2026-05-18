<h2>Update Profile</h2>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label><br>
<textarea name="bio"><?= $user['bio'] ?? '' ?></textarea>
<br><br>

<label>Twitter:</label><br>
<input type="text" name="twitter"
value="<?= json_decode($user['social_links'],true)['twitter'] ?? '' ?>">
<br><br>

<label>GitHub:</label><br>
<input type="text" name="github"
value="<?= json_decode($user['social_links'],true)['github'] ?? '' ?>">
<br><br>

<label>Profile Image:</label><br>
<input type="file" name="image">
<br><br>

<button type="submit" name="submit">Update</button>

</form>

<?php if(isset($_GET['updated'])) echo "<p style='color:green'>Updated Successfully</p>"; ?>