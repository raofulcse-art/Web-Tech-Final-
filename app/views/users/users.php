<h2>All Users (Admin)</h2>

<table border="1">
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
        <button onclick="promote(<?= $u['id'] ?>)">
            Promote
        </button>
    </td>
</tr>

<?php } ?>

</table>

<script>
function promote(id){

fetch("api/promote.php", {
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