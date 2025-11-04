<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

require_once "../classes/roles.php";
$roleObj = new Roles();
$search = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Roles</title>
    <link rel="stylesheet" href="../index.css">
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <h1 class="site-title">Roles</h1>
            <div class="header-actions">
                <a class="btn btn-outline" href="../index.php">← Dashboard</a>
                <a class="btn btn-primary" href="addRole.php">Add New Role</a>
            </div>
        </div>
    </header>

    <main class="page-container">

        <section class="form-card">
            <form action="" method="get" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:16px;">
                <label for="search" class="sr-only">Search Role</label>
                <input class="form-control" type="search" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search roles by name or id">
                <button class="btn btn-outline" type="submit">Search</button>
                <a class="btn btn-outline" href="viewRole.php">Reset</a>
            </form>

            <div style="overflow:auto">
                <table class="data-table" role="table">
                    <thead>
                        <tr>
                            <th>Role ID</th>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($roleObj->viewRole($search) as $role) {
                            $message = "Are you sure you want to delete this role? This action cannot be undone and may affect profiles with this role.";
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($role["role_id"]) ?></td>
                            <td><?= htmlspecialchars($role["role_name"]) ?></td>
                            <td><?= htmlspecialchars($role["description"]) ?></td>
                            <td>
                                <a class="btn btn-outline" href="editRole.php?role_id=<?= htmlspecialchars($role["role_id"]) ?>">Edit</a>
                                <a class="btn btn-outline" href="removeRole.php?role_id=<?= htmlspecialchars($role["role_id"]) ?>" 
                                   onclick="return confirm('<?= htmlspecialchars($message) ?>')">Delete</a>
                            </td>
                        </tr>
                        <?php 
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>