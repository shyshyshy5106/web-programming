<?php
require_once "../classes/admin.php";
$adminObj = new Admins();

$admin = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $admin["firstname"] = trim(htmlspecialchars($_POST["firstname"]));
    $admin["lastname"] = trim(htmlspecialchars($_POST["lastname"]));
    $admin["role"] = trim(htmlspecialchars($_POST["role"]));
    $admin["email"] = trim(htmlspecialchars($_POST["email"]));
    $admin["password"] = trim(htmlspecialchars($_POST["password"]));
    $admin["is_active"] = isset($_POST["is_active"]) ? trim(htmlspecialchars($_POST["is_active"])) : '';

    if (empty($admin["firstname"])) {
        $errors["firstname"] = "First name is required.";
    }

    if (empty($admin["lastname"])) {
        $errors["lastname"] = "Last name is required.";
    }

    if (empty($admin["role"])) {
        $errors["role"] = "Role is required.";
    }

    if (empty($admin["email"])) {
        $errors["email"] = "Email is required.";      
    }

    if (empty($admin["password"])) {
        $errors["password"] = "Password is required.";      
    }

        if (empty($admin["is_active"])) {
        $errors["is_active"] = "This is required.";      
    }

    if (empty(array_filter($errors))) {
        $adminObj->firstname = $admin["firstname"];
        $adminObj->lastname = $admin["lastname"];
        $adminObj->role = $admin["role"];
        $adminObj->email = $admin["email"];
        $adminObj->password = $admin["password"];
        $adminObj->is_active = $admin["is_active"];

        if ($adminObj->addadmin()) {
            echo "Admin added successfully.";
        } else {
            echo "Failed to add admin. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../index.css">
    <style>
        /* small page-specific tweaks */
        .page-container{max-width:720px;margin:28px auto;padding:0 16px}
        .form-card{background:var(--white);padding:24px;border-radius:10px;box-shadow:0 8px 20px rgba(0,0,0,0.06)}
        .field-row{display:flex;gap:12px}
        .field-row .form-group{flex:1}
        .radio-group{display:flex;gap:12px;align-items:center}
        .helper-note{font-size:0.95rem;color:#666;margin-bottom:12px}
        .footer-actions{display:flex;gap:12px;align-items:center;margin-top:16px}
    </style>
</head>
<body>
    <main class="page-container">
        <section class="form-card">
            <h2>Register Admin / Staff</h2>
            <p class="helper-note">Fields marked with <span>*</span> are required.</p>

            <form action="" method="POST">
                <div class="field-row">
                    <div class="form-group">
                        <label for="firstname">First Name <span>*</span></label>
                        <input class="form-control" type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($admin['firstname'] ?? '') ?>">
                        <p class="error"><?= htmlspecialchars($errors['firstname'] ?? '') ?></p>
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name <span>*</span></label>
                        <input class="form-control" type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($admin['lastname'] ?? '') ?>">
                        <p class="error"><?= htmlspecialchars($errors['lastname'] ?? '') ?></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email <span>*</span></label>
                    <input class="form-control" type="email" name="email" id="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>">
                    <p class="error"><?= htmlspecialchars($errors['email'] ?? '') ?></p>
                </div>

                <div class="form-group">
                    <label for="password">Password <span>*</span></label>
                    <input class="form-control" type="password" name="password" id="password" value="<?= htmlspecialchars($admin['password'] ?? '') ?>">
                    <p class="error"><?= htmlspecialchars($errors['password'] ?? '') ?></p>
                </div>

                <div class="form-group">
                    <label>Role <span>*</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="role" value="Admin" <?php if (isset($admin['role']) && $admin['role'] == 'Admin') echo 'checked'; ?>> Admin</label>
                        <label><input type="radio" name="role" value="Staff" <?php if (isset($admin['role']) && $admin['role'] == 'Staff') echo 'checked'; ?>> Staff</label>
                    </div>
                    <p class="error"><?= htmlspecialchars($errors['role'] ?? '') ?></p>
                </div>

                <div class="form-group">
                    <label>Is Active <span>*</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="is_active" value="1" <?php if (isset($admin['is_active']) && $admin['is_active'] == '1') echo 'checked'; ?>> Yes</label>
                        <label><input type="radio" name="is_active" value="0" <?php if (isset($admin['is_active']) && $admin['is_active'] == '0') echo 'checked'; ?>> No</label>
                    </div>
                    <p class="error"><?= htmlspecialchars($errors['is_active'] ?? '') ?></p>
                </div>

                <div class="footer-actions">
                    <button type="submit" class="btn btn-primary">Save Admin</button>
                    <a class="btn btn-outline" href="/GymMembershipSystem/index.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
