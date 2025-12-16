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
    <link rel="stylesheet" href="../view.css">
</head>

<body>

<header class="page-header">
    <h1 class="page-title">View Payments</h1>
    <div class="header-actions">
        <a class="nav-link" href="../index.php">Back</a>
        <a class="btn btn-primary" href="addPayment.php">Add Payment</a>
    </div>
</header>

    <main class="page-container">
    <section class="form-card">

        <form action="" method="get" class="search-form">
            <label for="search" class="sr-only">Search Payments</label>
            <input class="search-input" type="search" name="search" id="search" value="<?= $search ?>" placeholder="Search by payment id, membership id or member name">
            <span><button class="btn btn-search" type="submit">Search</button></span>

            <label for="status" class="sr-only">Filter by Payment Status</label>
            <select class="filter-select" name="status" id="status">
                <option value="">All Statuses</option>
                <option value="Completed" <?= (isset($status) && $status == "Completed") ? "selected":"" ?>>Completed</option>
                <option value="Pending" <?= (isset($status) && $status == "Pending") ? "selected":"" ?>>Pending</option>
            </select>
        </form>
    </section>

    <div class="table-container">

        <table class="data-table payments" role="table">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>Membership</th>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Status</th>
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

                        <td class="truncate" data-fulltext="<?= htmlspecialchars($payment["membership_id"] . ' - ' . $payment["member_name"]) ?>">
                            <?= $payment["membership_id"] ?> - <?= htmlspecialchars($payment["member_name"]) ?>
                        </td>

                        <td><?= htmlspecialchars(!empty($payment["payment_date"]) ? date('Y-m-d', strtotime($payment["payment_date"])) : '') ?></td>
                        <td><?= $payment["amount"] ?></td>
                        <td><?= $payment["payment_mode"] ?></td>
                        <?php
                        $ps = strtolower($payment["payment_status"] ?? '');
                        $badgeClass = 'status-inactive';
                        if ($ps === 'completed' || $ps === 'complete') $badgeClass = 'status-active';
                        else if ($ps === 'pending') $badgeClass = 'status-pending';
                        else if ($ps === 'failed' || $ps === 'cancelled') $badgeClass = 'status-inactive';
                        ?>
                        <td><span class="status-badge <?= $badgeClass ?>"><?php echo htmlspecialchars(ucfirst(strtolower($payment["payment_status"]))); ?></span></td>

                        <td><?= htmlspecialchars($payment["staff_name"] ?? 'N/A') ?></td>

                        <td class="table-actions">
                            <a class="btn btn-action btn-edit" href="editPayment.php?payment_id=<?= $payment["payment_id"] ?>" title="Edit">Edit</a>

                            <a class="btn btn-action btn-receipt" href="receipt.php?payment_id=<?= $payment["payment_id"] ?>" target="_blank" title="Receipt">Receipt</a>

                            <?php if($_SESSION["user"]["role"] == "Admin"){ ?>
                                <button class="btn btn-action btn-delete" data-delete-url="removePayment.php?payment_id=<?= $payment["payment_id"] ?>" data-delete-msg="<?= htmlspecialchars($message) ?>" title="Delete">Delete</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
    <script src="../scripts/table-utils.js"></script>

        </table>
    </div>

</main>

</body>
</html>
