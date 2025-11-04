<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

require_once "../classes/profile.php";
require_once "../classes/roles.php";

$profileObj = new Profiles();
$roleObj = new Roles();
$search = $role_id = $status = $sex = "";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
    $role_id = isset($_GET["role_id"]) ? trim(htmlspecialchars($_GET["role_id"])) : "";
    $status = isset($_GET["status"]) ? trim(htmlspecialchars($_GET["status"])) : "";
    $sex = isset($_GET["sex"]) ? trim(htmlspecialchars($_GET["sex"])) : "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profiles</title>

</head>
<body>
     <h2>Hi <?= $_SESSION["user"]["role"] ?></h2><a href="../account/logout.php">LogOut</a>
    <span></button><a href="../index.php"> Home </a></button></span><br><br>

    <h1>Profiles</h1>

        <form action="" method="get">
            <label for="search">Search name:</label>
            <input type="search" name="search" id="search" value="<?= $search ?>">

            <label for="role_id">Role:</label>
            <select name="role_id" id="role_id">
                <option value="">All</option>
                <?php 
                $roles = $roleObj->viewRole();
                if (!empty($roles)) : 
                    foreach ($roles as $r): ?>
                        <option value="<?= $r['role_id'] ?>" <?= ($role_id !== '' && $role_id == $r['role_id']) ? 'selected' : '' ?>><?= htmlspecialchars($r['role_name']) ?></option>
                    <?php endforeach; 
                endif; ?>
            </select>

            <label for="sex">Sex:</label>
            <select name="sex" id="sex">
                <option value="">All</option>
                <option value="Male" <?= (isset($sex) && $sex == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= (isset($sex) && $sex == 'Female') ? 'selected' : '' ?>>Female</option>
            </select>

            <label for="status">Status:</label>
            <select name="status" id="status">
                <option value="">All</option>
                <option value="Active" <?= (isset($status) && $status == 'Active') ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= (isset($status) && $status === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>

            <input type="submit" value="Search">
            <span><button><a href="addProfile.php">Add Profile</a></button></span>
        </form>
    

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Sex</th>
                <th>DOB</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($profileObj->viewProfile($search, $role_id, $status, $sex) as $profile) {
                $message = "Are you sure you want to delete this profile?";
            ?>
            
            <tr>
                <td><?= $profile['id'] ?></td>
                <td><?php 
                    $role = $roleObj->fetchRole($profile['role_id']);
                    echo htmlspecialchars($role['role_name'] ?? 'Unknown');
                ?></td>
                <td><?= $profile['fname'] . ' ' . $profile['mname']. ' ' . $profile['lname'] ?></td>
                <td><?= $profile['phone_num'] ?></td>
                <td><?= $profile['address'] ?></td>
                <td><?= $profile['sex'] ?></td>
                <td><?= $profile['dob'] ?></td>
                <td><?= $profile['join_date'] ?></td>
                <td><?= $profile['status'] ?></td>
                <td><?= $profile['created_at'] ?></td>
                <td><?= $profile['updated_at'] ?></td>
                <td>
                    <a href="editProfile.php?profile_id=<?= $profile['id'] ?>">Edit</a>
                    <?php if ($_SESSION['user']['role'] == 'Admin') { ?>
                        <a href="removeProfile.php?profile_id=<?= $profile['id'] ?>" onclick="return confirm('<?= $message ?>')">Delete</a>
                    <?php } ?>
                </td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</body>
</html>