<?php
require_once "../classes/roles.php";

$roleObj = new Roles();

$role = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role["role_name"] = trim(htmlspecialchars($_POST["role_name"]));
    $role["description"] = trim(htmlspecialchars($_POST["description"]));

    if (empty($role["role_name"])) {
        $errors["role_name"] = "Role name is required.";
    } elseif ($roleObj->isRoleExist($role["role_name"])) {
        $errors["role_name"] = "This role name already exists.";
    }

    if (empty($role["description"])) {
        $errors["description"] = "Description is required.";
    }

    if (empty($errors)) {
        $roleObj->role_name = $role["role_name"];
        $roleObj->description = $role["description"];

        if ($roleObj->addRole()) {
            header("Location: viewRole.php");
        } else {
            echo "Failed to add role. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Role</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
    <main class="page-container">
        <section class="form-card">
            <h2>Add Role</h2>
            <p class="helper-note">Fields marked with <span class="required">*</span> are required.</p>

            <form action="" method="POST" >
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
                    <button type="submit" class="btn btn-primary">Save Role</button>
                    <a class="btn btn-outline" href="viewRole.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
