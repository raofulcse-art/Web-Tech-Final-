<?php
// ============================================================
// views/login.php — Login Page
// Simple login: checks MD5 password match, sets $_SESSION
// ============================================================

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/db.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = mysqli_prepare($conn,
            "SELECT id, username, role FROM users
             WHERE username = ? AND password = MD5(?) LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        if ($user) {
            // Set session variables
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Redirect to articles after login
            header('Location: index.php?page=articles');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login — Comment System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="container" style="max-width:420px;margin:80px auto">
    <div class="card">
        <h2 style="margin-bottom:20px">🔐 Login</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="e.g. admin" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%">Login</button>
        </form>

        <p style="margin-top:16px;font-size:13px;color:#666">
            Test accounts: <code>admin/admin123</code> · <code>alice/alice123</code> · <code>bob/bob123</code>
        </p>
    </div>
</div>
</body>
</html>
