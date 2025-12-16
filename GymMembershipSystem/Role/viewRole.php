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
    <link rel="stylesheet" href="../view.css">
</head>

<body>
    <header class="page-header">
        <h1 class="page-title">Roles</h1>
        <div class="header-actions">
            <a class="nav-link" href="../index.php">Back</a>
            <a class="btn btn-primary" href="addRole.php">Add New Role</a>
        </div>
    </header>

    <main class="page-container">

        <section class="form-card">
            <form action="" method="get" class="search-form">
                <label for="search" class="sr-only">Search Role</label>
                <input class="search-input" type="search" name="search" id="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search roles by name or id">
                <button class="btn btn-search" type="submit">Search</button>
            </form>

            <div class="table-container">
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
                        $roles = $roleObj->viewRole($search);
                        if (empty($roles)) {
                            ?>
                            <tr><td colspan="4"><div class="table-empty"><div class="table-empty-icon">🏷️</div><div class="table-empty-text">No roles found.</div></div></td></tr>
                            <?php
                        } else {
                        foreach($roles as $role) {
                            $message = "Are you sure you want to delete this role? This action cannot be undone and may affect profiles with this role.";
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($role["role_id"]) ?></td>
                                <td class="truncate" data-fulltext="<?= htmlspecialchars($role['role_name']) ?>"><?= htmlspecialchars($role["role_name"]) ?></td>
                                <td class="truncate" data-fulltext="<?= htmlspecialchars($role['description']) ?>"><?= htmlspecialchars($role["description"]) ?></td>
                                <td class="table-actions">
                                    <a class="btn btn-action btn-edit" href="editRole.php?role_id=<?= htmlspecialchars($role["role_id"]) ?>">Edit</a>
                                    <button class="btn btn-action btn-delete" data-delete-url="removeRole.php?role_id=<?= htmlspecialchars($role["role_id"]) ?>" data-delete-msg="<?= htmlspecialchars($message) ?>">Delete</button>
                                </td>
                            </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="../scripts/table-utils.js"></script>
</body>
</html>