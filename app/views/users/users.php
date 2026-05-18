<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../public/style.css">
</head>

<body>

<div class="container">

<div class="card">

<h2>All Users (Admin)</h2>

<table>

<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Action</th>
</tr>

<?php foreach($users as $u){ ?>

<tr>
    <td><?= $u['name'] ?></td>
    <td><?= $u['email'] ?></td>

    <td id="role-<?= $u['id'] ?>">
        <?= $u['role'] ?>
    </td>

    <td>

        <?php if($u['role'] == 'reader'){ ?>
        <button class="btn-promote" onclick="promote(<?= $u['id'] ?>)">
            Promote to Author
        </button>
        <?php } else { echo "<span style='color:green'>Approved</span>"; } ?>

    </td>
</tr>

<?php } ?>

</table>

</div>

</div>

<script>
function promote(id){

fetch("../api/promote.php", {
    method:"POST",
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:"user_id="+id
})
.then(res=>res.json())
.then(data=>{
    document.getElementById("role-"+id).innerText="author";
});
}
</script>

</body>