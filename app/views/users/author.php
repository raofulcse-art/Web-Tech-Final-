<h2>Author Profile</h2>

<?php if(!empty($user['profile_pic_path'])) { ?>
    <img src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>" width="120">
<?php } ?>

<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Bio:</b> <?= $user['bio'] ?></p>

<?php
$social = json_decode($user['social_links'], true);
?>

<p><b>Twitter:</b> <?= $social['twitter'] ?? '' ?></p>
<p><b>GitHub:</b> <?= $social['github'] ?? '' ?></p>

<br>

<a href="javascript:history.back()">Back</a>