<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Author Dashboard</h2>

<a href="index.php?action=create">Create Article</a>

<hr>

<?php foreach ($articles as $a): ?>

<div style="border:1px solid black; margin:10px; padding:10px">

    <h3><?= $a['title'] ?></h3>
    <p><?= $a['body'] ?></p>

    <button onclick="toggleStatus(<?= $a['id'] ?>, 'published')">
        Publish
    </button>

    <button onclick="toggleStatus(<?= $a['id'] ?>, 'draft')">
        Unpublish
    </button>

</div>

<?php endforeach; ?>

<script src="app.js"></script>
</body>
</html>