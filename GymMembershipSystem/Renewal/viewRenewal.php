<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

require_once "../classes/renewal_rec.php";
$renewalObj = new RenewalRecords();
$search = "";

if($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? trim(htmlspecialchars($_GET["search"])) : "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Renewal Records</title>
</head>
<body>
    <a href="../account/logout.php">LogOut</a>
    <span></button><a href="../index.php"> Home </a></button></span><br><br>
    <h1>View Renewal Records</h1>

    <form action="" method="get">
        <label for="">Search:</label>
            <input type="search" name="search" id="search" value="<?= $search ?>">
            <input type="submit" value="Search"> <span><button><a href="addRenewal.php">Add Renewal</a></button></span>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Renewal ID</th>
                <th>Membership ID</th>
                <th>Plan ID</th>
                <th>Renewal Date</th>
                <th>Previous Start Date</th>
                <th>Previous Expiry Date</th>
                <th>New Start Date</th>
                <th>New Expiry Date</th>
                <th>Payment ID</th>
                <th>Processed By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($renewalObj->viewRenewal($search) as $renewal) {
                $message = "Are you sure you want to delete this renewal record?";
            ?>
                <tr>
                    <td><?= $renewal["renewal_id"] ?></td>
                    <td><?= $renewal["membership_id"] ?></td>
                    <td><?= $renewal["plan_id"] ?></td>
                    <td><?= $renewal["renewal_date"] ?></td>
                    <td><?= $renewal["previous_start_date"] ?></td>
                    <td><?= $renewal["previous_expiry_date"] ?></td>
                    <td><?= $renewal["new_start_date"] ?></td>
                    <td><?= $renewal["new_expiry_date"] ?></td>
                    <td><?= $renewal["payment_id"] ?></td>
                    <?php
                        $staffName = trim(($renewal["staff_fname"] ?? "") . " " . ($renewal["staff_mname"] ? $renewal["staff_mname"] . " " : "") . ($renewal["staff_lname"] ?? ""));
                    ?>
                    <td><?= htmlspecialchars($staffName) ?> (ID: <?= $renewal["employee_id"] ?>)</td>
                    <td>
                        <a href="editRenewal.php?renewal_id=<?=$renewal["renewal_id"]?>">Edit</a>
                        <?php
                        if($_SESSION["user"]["role"] == "Admin"){
                        ?>
                        <a href="removeRenewal.php?renewal_id=<?= $renewal["renewal_id"] ?>" onclick="return confirm('<?= $message ?>')">Delete</a>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>