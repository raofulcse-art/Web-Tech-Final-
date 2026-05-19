function addCategory() {
    let name = document.getElementById("newCategory").value;

    fetch("index.php?action=category_store", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "name=" + name
    })
    .then(res => res.json())
    .then(data => location.reload());
}

function toggleStatus(id, status) {

    fetch("index.php?action=toggle", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "id=" + id + "&status=" + status
    })
    .then(res => res.json())
    .then(data => location.reload());
}