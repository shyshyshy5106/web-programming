<?php
require_once "../classes/profile.php";
require_once "../classes/roles.php";

$profileObj = new Profiles();
$roleObj = new Roles();

$profile = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profile["role_id"] = trim(htmlspecialchars($_POST["role_id"]));
    $profile["fname"] = trim(htmlspecialchars($_POST["fname"]));
    $profile["mname"] = trim(htmlspecialchars($_POST["mname"] ?? ""));
    $profile["lname"] = trim(htmlspecialchars($_POST["lname"]));
    $profile["phone_num"] = trim(htmlspecialchars($_POST["phone_num"]));
    $profile["address"] = trim(htmlspecialchars($_POST["address"]));
    $profile["sex"] = trim(htmlspecialchars($_POST["sex"]));
    $profile["dob"] = trim(htmlspecialchars($_POST["dob"]));
    $profile["join_date"] = trim(htmlspecialchars($_POST["join_date"]));
    $profile["status"] = trim(htmlspecialchars($_POST["status"]));
    $profile["created_at"] = trim(htmlspecialchars($_POST["created_at"]));
    $profile["updated_at"] = trim(htmlspecialchars($_POST["updated_at"]));

    if (empty($profile["role_id"])) {
        $errors["role_id"] = "Role is required.";
    } 

    if (empty($profile["fname"])) {
        $errors["fname"] = "First name is required.";
    }

    if (empty($profile["lname"])) {
        $errors["lname"] = "Last name is required.";
    }

    if (empty($profile["phone_num"])) {
        $errors["phone_num"] = "Phone number is required.";
    } elseif (!preg_match("/^[0-9\-\(\)\/\+\s]*$/", $profile["phone_num"])) {
        $errors["phone_num"] = "Invalid phone number format.";
    } elseif (strlen($profile["phone_num"]) != 11) {
        $errors["phone_num"] = "Phone number must be exactly 11 digits.";
    }

    if (empty($profile["address"])) {
        $errors["address"] = "Address is required.";
    }

    if (empty($profile["sex"])) {
        $errors["sex"] = "Sex is required.";
    }

    if (empty($profile["dob"])) {
        $errors["dob"] = "Date of birth is required.";
    } elseif (strtotime($profile["dob"]) > strtotime(date("Y-m-d"))) {
        $errors["dob"] = "Date of birth cannot be in the future.";
    }

    if (empty($profile["join_date"])) {
        $errors["join_date"] = "Join date is required.";
    } elseif (strtotime($profile["join_date"]) > strtotime(date("Y-m-d"))) {
        $errors["join_date"] = "Join date cannot be in the future.";
    }

    if (empty($profile["status"])) {
        $errors["status"] = "Status is required.";
    }

    if (empty($profile["created_at"])) {
        $errors["created_at"] = "Creation date is required.";
    } elseif (strtotime($profile["created_at"]) > strtotime(date("Y-m-d H:i:s"))) {
        $errors["created_at"] = "Creation date cannot be in the future.";
    }

    if (empty($profile["updated_at"])) {
        $errors["updated_at"] = "Update date is required.";
    } elseif (strtotime($profile["updated_at"]) > strtotime(date("Y-m-d H:i:s"))) {
        $errors["updated_at"] = "Update date cannot be in the future.";
    }

    if (!empty($profile["fname"]) && !empty($profile["lname"]) && !empty($profile["role_id"]) &&
        $profileObj->isProfileExist($profile["fname"], $profile["lname"], $profile["role_id"])) {
        $errors["fname"] = "A profile with this name and role already exists.";
    }

    if (empty($errors)) {
        $profileObj->role_id = $profile["role_id"];
        $profileObj->fname = $profile["fname"];
        $profileObj->mname = $profile["mname"];
        $profileObj->lname = $profile["lname"];
        $profileObj->phone_num = $profile["phone_num"];
        $profileObj->address = $profile["address"];
        $profileObj->sex = $profile["sex"];
        $profileObj->dob = $profile["dob"];
        $profileObj->join_date = $profile["join_date"];
        $profileObj->status = $profile["status"];
        $profileObj->created_at = date("Y-m-d H:i:s");
        $profileObj->updated_at = date("Y-m-d H:i:s");

        if ($profileObj->addProfile()) {
            header("Location: viewProfile.php");
        } else {
            echo "Failed to add profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Profile</title>
     <link rel="stylesheet" href="../index.css">
</head>
<body>
    <main class="page-container">
        <section class="form-card">

                <h1>Add Profile</h1>
                <p class="helper-note">Fields marked with <span class="required">*</span> are required.</p>
    
                <form action="" method="POST">

                <div class="form-group">
                    <label for="role_id">Role ID <span class="required">*</span></label>
                        <select class="form-control" name="role_id" id="role_id">
                            <option value="">--- Select Role ---</option>
                            <?php $roles = $roleObj->viewRole(); ?>
                            <?php if (!empty($roles)) : ?>
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?= $role['role_id'] ?>" <?= (isset($profile['role_id']) && $profile['role_id'] == $role['role_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="error"><?= $errors['role_id'] ?? '' ?></p><br>
                </div>

                <div class="form-group">
                    <label for="fname">First Name <span class="required">*</span></label>
                        <input class="form-control" type="text" name="fname" id="fname" value="<?= $profile['fname'] ?? '' ?>">
                        <p class="error"><?= $errors['fname'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="mname">Middle Name</label>
                        <input class="form-control" type="text" name="mname" id="mname" value="<?= $profile['mname'] ?? '' ?>">
                        <p class="error"><?= $errors['mname'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="lname">Last Name <span class="required">*</span></label>
                        <input class="form-control" type="text" name="lname" id="lname" value="<?= $profile['lname'] ?? '' ?>">
                        <p class="error"><?= $errors['lname'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="phone_num">Phone Number <span class="required">*</span></label>
                        <input class="form-control" type="text" name="phone_num" id="phone_num" maxlength=11 value="<?= $profile['phone_num'] ?? '' ?>">
                        <p class="error"><?= $errors['phone_num'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="address">Address <span class="required">*</span></label>
                        <textarea class="form-control" name="address" id="address" rows="3"><?= $profile['address'] ?? '' ?></textarea>
                        <p class="error"><?= $errors['address'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="sex">Sex <span class="required">*</span></label>
                        <select class="form-control" name="sex" id="sex">
                            <option value="">--- Select Sex ---</option>
                            <option value="Male" <?= (isset($profile['sex']) && $profile['sex'] == 'Male') ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= (isset($profile['sex']) && $profile['sex'] == 'Female') ? 'selected' : '' ?>>Female</option>
                        </select>
                        <p class="error"><?= $errors['sex'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="dob">Date of Birth <span class="required">*</span></label>
                        <input class="form-control" type="date" name="dob" id="dob" value="<?= $profile['dob'] ?? '' ?>">
                        <p class="error"><?= $errors['dob'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="join_date">Join Date <span class="required">*</span></label>
                        <input class="form-control" type="date" name="join_date" id="join_date" value="<?= $profile['join_date'] ?? date('Y-m-d') ?>">
                        <p class="error"><?= $errors['join_date'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                        <select class="form-control" name="status" id="status">
                            <option value="">--- Select Status ---</option>
                            <option value="Active" <?= (isset($profile['status']) && $profile['status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                            <option value="Inactive" <?= (isset($profile['status']) && $profile['status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                        <p class="error"><?= $errors['status'] ?? '' ?></p><br><br>
                </div>

                <div class="form-group"> 
                    <label for="created_at">Created At <span class="required">*</span></label>
                        <input class="form-control" type="datetime-local" name="created_at" id="created_at" value="<?= $profile['created_at'] ?? date('Y-m-d\TH:i') ?>">
                        <p class="error"><?= $errors['created_at'] ?? '' ?></p>
                </div>

                <div class="form-group">
                    <label for="updated_at">Updated At <span class="required">*</span></label>
                        <input class="form-control" type="datetime-local" name="updated_at" id="updated_at" value="<?= $profile['updated_at'] ?? date('Y-m-d\TH:i') ?>">
                        <p class="error"><?= $errors['updated_at'] ?? '' ?></p><br><br>
                </div>

                <div class="footer-actions">
                    <input type="submit" class="btn btn-primary" value="Save Profile"><button><a class="btn btn-outline" href="viewProfile.php">Cancel</a></button>
                    </div>
                </form>
        </section>
    </main>
</body>
</html>
