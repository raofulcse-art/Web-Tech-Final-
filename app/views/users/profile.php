<head>
    <title>Profile</title>
    <link rel="stylesheet" href="../public/style.css">
</head>

<body>

<div class="container">

<div class="card">

<h2>My Profile</h2>

<?php if(isset($_GET['updated'])) echo "<p style='color:green'>Profile Updated</p>"; ?>

<?php if(!empty($user['profile_pic_path'])) { ?>
    <img class="avatar" src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>">
<?php } else { ?>
    <p>No Image</p>
<?php } ?>

<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Email:</b> <?= $user['email'] ?></p>

</div>

<div class="card">

<h3>Update Profile</h3>

<form method="POST" enctype="multipart/form-data">

<label>Bio:</label>
<textarea name="bio"><?= $user['bio'] ?? '' ?></textarea>

<?php $social = json_decode($user['social_links'], true); ?>

<label>Twitter:</label>
<input type="text" name="twitter" value="<?= $social['twitter'] ?? '' ?>">

<label>GitHub:</label>
<input type="text" name="github" value="<?= $social['github'] ?? '' ?>">

<label>Profile Image:</label>
<input type="file" name="image">

<button type="submit" name="submit">Update Profile</button>

</form>

</div>

<div class="card" style="text-align:center">

<a href="logout.php">
    <button style="background:red">Logout</button>
</a>

</div>

</div>

</body>