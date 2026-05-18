<head>
    <title>Author Profile</title>
    <link rel="stylesheet" href="../public/style.css">
</head>

<body>

<div class="container">

<div class="card">

<h2>Author Profile</h2>

<?php if(!empty($user['profile_pic_path'])) { ?>
    <img class="avatar" src="../public/uploads/avatars/<?= $user['profile_pic_path'] ?>">
<?php } ?>

<p><b>Name:</b> <?= $user['name'] ?></p>
<p><b>Bio:</b> <?= $user['bio'] ?></p>

<?php
$social = json_decode($user['social_links'], true);
?>

<p><b>Twitter:</b>
<?php if(!empty($social['twitter'])) { ?>
    <a href="<?= $social['twitter'] ?>" target="_blank">
        <?= $social['twitter'] ?>
    </a>
<?php } else { echo "N/A"; } ?>
</p>

<p><b>GitHub:</b>
<?php if(!empty($social['github'])) { ?>
    <a href="<?= $social['github'] ?>" target="_blank">
        <?= $social['github'] ?>
    </a>
<?php } else { echo "N/A"; } ?>
</p>

</div>

</div>

</body>