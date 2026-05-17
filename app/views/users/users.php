<h2>Admin - Users List</h2>

<table border="1" cellpadding="10">

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php while($u=$users->fetch(PDO::FETCH_ASSOC)){ ?>

<tr>
<td><?= $u['id'] ?></td>
<td><?= $u['name'] ?></td>
<td><?= $u['email'] ?></td>
<td><?= $u['role'] ?></td>

<td>
<?php if($u['role'] != 'author'){ ?>
<form method="POST" action="admin.php">
<input type="hidden" name="user_id" value="<?= $u['id'] ?>">
<button>Promote to Author</button>
</form>
<?php } else { ?>
Already Author
<?php } ?>
</td>

</tr>

<?php } ?>

</table>