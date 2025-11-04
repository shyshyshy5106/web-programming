<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

require_once "../classes/payment.php";
$paymentObj = new Payments();
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
    <title>View Payments</title>
</head>
<body>
    <h2>Hi <?= $_SESSION["user"]["role"] ?></h2><a href="../account/logout.php">LogOut</a>
    <span></button><a href="../index.php"> Home </a></button></span><br><br>
    <h1>View Payments</h1>

    <form action="" method="get">
        <label for="">Search:</label>
            <input type="search" name="search" id="search" value="<?= $search ?>">
            <select name="status" id="status">
                <option value="">All</option>
                <option value="Completed" <?= (isset($status) && $status == "Completed") ? "selected":"" ?>>Completed</option>
                <option value="Pending" <?= (isset($status) && $status == "Pending") ? "selected":"" ?>>Pending</option>
            </select>
            <input type="submit" value="Search"> <span><button><a href="addPayment.php">Add Payment</a></button></span>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Membership ID</th>
                <th>Payment Date</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Payment Status</th>
                <th>Received By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($paymentObj->viewPayment($search, $status) as $payment) {
                $message = "Are you sure you want to delete this payment?";
            ?>
                <tr>
                    <td><?= $payment["payment_id"] ?></td>
                    <td><?= $payment["membership_id"] ?> - <?= htmlspecialchars($payment["member_name"]) ?></td>
                    <td><?= $payment["payment_date"] ?></td>
                    <td><?= $payment["amount"] ?></td>
                    <td><?= $payment["payment_mode"] ?></td>
                    <td><?= $payment["payment_status"] ?></td>
                    <td><?= htmlspecialchars($payment["staff_name"] ?? 'N/A') ?></td>
                    <td>
                        <a href="editPayment.php?payment_id=<?=$payment["payment_id"]?>">Edit</a>
                        <?php
                        if($_SESSION["user"]["role"] == "Admin"){
                        ?>
                        <a href="removePayment.php?payment_id=<?= $payment["payment_id"] ?>" onclick="return confirm('<?= $message ?>')">Delete</a>
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