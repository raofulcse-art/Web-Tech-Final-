<!DOCTYPE html>
<html>
<head>
    <title>Create Article</title>
</head>
<body>

<h2>Create Article</h2>

<form method="POST" action="index.php?action=store" enctype="multipart/form-data">

    <input type="text" name="title" placeholder="Title"><br><br>

    <textarea name="body" placeholder="Body"></textarea><br><br>

    <select name="category_id" id="categoryBox">
        <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <input type="text" id="newCategory" placeholder="New Category">
    <button type="button" onclick="addCategory()">Add</button>

    <br><br>

    <input type="text" name="tags" placeholder="tags separated by spaces"><br><br>

    

    <select name="status">
        <option value="draft">Draft</option>
        <option value="published">Published</option>
    </select><br><br>

    <input type="date" name="publish_at"><br><br>

    <button type="submit">Submit</button>

</form>

<script src="app.js"></script>
</body>
</html>