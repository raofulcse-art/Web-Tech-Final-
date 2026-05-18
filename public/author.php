<?php

require_once "../core/Database.php";
require_once "../app/models/User.php";

$db = new Database();
$model = new User($db->connect());

if(!isset($_GET['id'])){
    die("Author ID missing");
}

$id = $_GET['id'];

$user = $model->getById($id);

if(!$user){
    die("Author not found");
}

?>

<!DOCTYPE html>

<html>
<head>
    <title>Author Profile</title>
</head>
<body>

<h2>Author Profile</h2>

<?php if(!empty($user['profile_pic_path'])) { ?>
    <img src="uploads/avatars/<?= $user['profile_pic_path'] ?>" width="150">
<?php }
 else { ?>
    <p>No Image</p>
<?php } ?>

<hr>


<p><b>Name:</b> <?= $user['name'] ?></p>

<p><b>Email:</b> <?= $user['email'] ?></p>
<p><b>Role:</b> <?= $user['role'] ?></p>

<p><b>Bio:</b> <?= $user['bio'] ?></p>

<?php
$social = json_decode($user['social_links'], true);
?>

<p><b>Facebook:</b>
    <a href="<?= $social['facebook'] ?? '#' ?>" target="_blank">
        <?= $social['facebook'] ?? 'N/A' ?>
    </a>
</p>

<hr>


<h3>Published Articles</h3>

</body>
</html>