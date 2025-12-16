<?php
require_once "../classes/roles.php";

$roleObj = new Roles();

$role = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["role_id"])){
        $role_id = trim(htmlspecialchars($_GET["role_id"]));
        $role = $roleObj->fetchRole($role_id);
        if(!$role){
            echo "<a href='viewRole.php'>Return to View Roles</a>";
            exit("Role Not Found!");
        }
    } else {
        echo "<a href='viewRole.php'>Return to View Roles</a>";
        exit("Role Not Found!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role["role_name"] = trim(htmlspecialchars($_POST["role_name"]));
    $role["description"] = trim(htmlspecialchars($_POST["description"]));

    if (empty($role["role_name"])) {
        $errors["role_name"] = "Role name is required.";
    }

    if (empty($role["description"])) {
        $errors["description"] = "Description is required.";
    }

    if (empty($errors)) {
        $roleObj->role_name = $role["role_name"];
        $roleObj->description = $role["description"];

        if ($roleObj->editRole($_GET["role_id"])) {
            header("Location: viewRole.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <main class="page-container">
        <section class="form-card">
            <h2>Edit Role</h2>
            <p class="helper-note">Fields marked with <span class="required">*</span> are required.</p>

            <form action="" method="POST" novalidate>
                <div class="form-group">
                    <label for="role_name">Role Name <span class="required">*</span></label>
                    <input class="form-control" type="text" name="role_name" id="role_name" value="<?= htmlspecialchars($role['role_name'] ?? '') ?>" maxlength="50">
                    <p class="error"><?= htmlspecialchars($errors['role_name'] ?? '') ?></p>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" name="description" id="description" maxlength="255"><?= htmlspecialchars($role['description'] ?? '') ?></textarea>
                    <p class="error"><?= htmlspecialchars($errors['description'] ?? '') ?></p>
                </div>

                <div class="footer-actions">
                    <button type="submit" class="btn btn-primary">Update Role</button>
                    <a class="btn btn-outline" href="viewRole.php">View Roles</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
