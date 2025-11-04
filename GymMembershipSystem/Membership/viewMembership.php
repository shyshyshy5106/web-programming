<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

require_once "../classes/membership.php";
$membershipObj = new Memberships();
$search = $status = "";

    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
        $status = isset($_GET["status"]) ? trim(htmlspecialchars($_GET["status"])) : "";
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Memberships</title>
</head>
<body>
    <h2>Hi <?= $_SESSION["user"]["role"] ?></h2><a href="../account/logout.php">LogOut</a>
    <span></button><a href="../index.php"> Home </a></button></span><br><br>
    <h1>View Memberships</h1>

    <form action="" method="get">
        <label for="">Search:</label>
            <input type="search" name="search" id="search" value="<?= $search ?>">
            <select name="status" id="status">
                <option value="">All</option>
                <option value="Active" <?= (isset($status) && $status == "Active") ? "selected":"" ?>>Active</option>
                <option value="Expired" <?= (isset($status) && $status == "Expired") ? "selected":"" ?>>Expired</option>
                <option value="Freeze" <?= (isset($status) && $status == "Freeze") ? "selected":"" ?>>Freeze</option>
                <option value="Suspended" <?= (isset($status) && $status == "Suspended") ? "selected":"" ?>>Suspended</option>
            </select>
            <input type="submit" value="Search"> <span><button><a href="addMembership.php">Add Membership</a></button></span>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Membership ID</th>
                <th>Member Name</th>
                <th>Plan Name</th>
                <th>Start Date</th>
                <th>Expiry Date</th>
                <th>Original Expiry Date</th>
                <th>Membership Status</th>
                <th>Processed By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($membershipObj->viewMembership($search, $status) as $membership) {
                    $message = "Are you sure you want to delete this membership?";
                    $memberName = trim($membership["member_fname"] . " " . 
                                ($membership["member_mname"] ? $membership["member_mname"] . " " : "") . 
                                $membership["member_lname"]);
                    $staffName = trim($membership["staff_fname"] . " " . 
                               ($membership["staff_mname"] ? $membership["staff_mname"] . " " : "") . 
                               $membership["staff_lname"]);
            ?>
                <tr>
                    <td><?= $membership["membership_id"] ?></td>
                    <td><?= htmlspecialchars($memberName) ?> (ID: <?= $membership["member_id"] ?>)</td>
                    <td><?= htmlspecialchars($membership["plan_name"]) ?> (ID: <?= $membership["plan_id"] ?>)</td>
                    <td><?= $membership["start_date"] ?></td>
                    <td><?= $membership["expiry_date"] ?></td>
                    <td><?= $membership["original_expiry_date"] ?></td>
                    <td><?= $membership["membership_status"] ?></td>
                    <td><?= htmlspecialchars($staffName) ?> (ID: <?= $membership["employee_id"] ?>)</td>
                    <td>
                        <a href="editMembership.php?membership_id=<?=$membership["membership_id"]?>">Edit</a>
                        <?php
                        if($_SESSION["user"]["role"] == "Admin"){
                        ?>
                        <a href="removeMembership.php?membership_id=<?= $membership["membership_id"] ?>" onclick="return confirm('<?= $message ?>')">Delete</a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php 
            } 
            ?>
        </tbody>
    </table>
</body>
</html>