<!DOCTYPE html>
<html>
<head>
    <title>Author Profile</title>
</head>
<body>

<h2>Author Public Profile</h2>


<?php if(!empty($user['profile_pic_path'])) { ?>
    <img src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>" width="150">
<?php } else { ?>
    <p>No Profile Image</p>
<?php } ?>

<hr>


<p><b>Name:</b> <?= $user['name'] ?></p>



<p><b>Bio:</b> <?= $user['bio'] ?? 'No bio available' ?></p>

<?php
$social = json_decode($user['social_links'], true);
?>


<p><b>Facebook:</b>
<?php if(!empty($social['facebook'])) { ?>
    <a href="<?= $social['facebook'] ?>" target="_blank">
        <?= $social['facebook'] ?>
    </a>
<?php } else { ?>
    N/A
<?php } ?>
</p>

<hr>

<h3>Published Articles</h3>
<p>Coming soon (Task-2 integration)</p>

<br>

<a href="javascript:history.back()">Back</a>

</body>
</html>