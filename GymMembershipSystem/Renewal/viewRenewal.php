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
    <link rel="stylesheet" href="../view.css">
</head>

<body>

<header class="page-header">
    <h1 class="page-title">View Renewal Records</h1>
    <div class="header-actions">
        <a class="nav-link" href="../index.php">Back</a>
        <a class="btn btn-primary" href="addRenewal.php">Add Renewal</a>
    </div>
</header>

<main class="page-container">
    <section class="form-card">

        <form action="" method="get" class="search-form">
            <label for="search" class="sr-only">Search Renewals</label>
            <input class="search-input" type="search" name="search" id="search" value="<?= $search ?>" placeholder="Search by renewal id, membership id or staff">
            <span><button class="btn btn-search" type="submit">Search</button></span>
        </form>
    </section>

    <div class="table-container">

        <table class="data-table renewals" role="table">
            <thead>
                <tr>
                    <th>Renewal ID</th>
                    <th>Membership ID</th>
                    <th>Plan ID</th>
                    <th>Renewal Date</th>
                    <th>Previous Start</th>
                    <th>Previous Expiry</th>
                    <th>New Start</th>
                    <th>New Expiry</th>
                    <th>Payment ID</th>
                    <th>Processed By</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php $renewals = $renewalObj->viewRenewal($search);
                    if (empty($renewals)) { ?>
                        <tr><td colspan="11"><div class="table-empty"><div class="table-empty-icon">🔁</div><div class="table-empty-text">No renewal records found.</div></div></td></tr>
                    <?php } else {
                    foreach($renewals as $renewal) {
                    $message = "Are you sure you want to delete this renewal record?";

                    $staffName = trim(
                        ($renewal["staff_fname"] ?? "") . " " .
                        ($renewal["staff_mname"] ? $renewal["staff_mname"] . " " : "") .
                        ($renewal["staff_lname"] ?? "")
                    );
                ?>
                    <tr>
                        <td><?= $renewal["renewal_id"] ?></td>
                        <td><?= $renewal["membership_id"] ?></td>
                        <td><?= $renewal["plan_id"] ?></td>
                        <td><?= htmlspecialchars(!empty($renewal["renewal_date"]) ? date('Y-m-d', strtotime($renewal["renewal_date"])) : '') ?></td>
                        <td><?= htmlspecialchars(!empty($renewal["previous_start_date"]) ? date('Y-m-d', strtotime($renewal["previous_start_date"])) : '') ?></td>
                        <td><?= htmlspecialchars(!empty($renewal["previous_expiry_date"]) ? date('Y-m-d', strtotime($renewal["previous_expiry_date"])) : '') ?></td>
                        <td><?= htmlspecialchars(!empty($renewal["new_start_date"]) ? date('Y-m-d', strtotime($renewal["new_start_date"])) : '') ?></td>
                        <td><?= htmlspecialchars(!empty($renewal["new_expiry_date"]) ? date('Y-m-d', strtotime($renewal["new_expiry_date"])) : '') ?></td>
                        <td><?= $renewal["payment_id"] ?></td>

                        <td title="<?= htmlspecialchars($staffName . ' (ID: ' . $renewal['employee_id'] . ')') ?>" class="truncate" data-fulltext="<?= htmlspecialchars($staffName . ' (ID: ' . $renewal['employee_id'] . ')') ?>"><?= htmlspecialchars($staffName) ?></td>

                        <td class="table-actions">
                            <a class="btn btn-action btn-edit" href="editRenewal.php?renewal_id=<?= $renewal["renewal_id"] ?>">Edit</a>

                            <?php if($_SESSION["user"]["role"] == "Admin"){ ?>
                                <button class="btn btn-action btn-delete" data-delete-url="removeRenewal.php?renewal_id=<?= $renewal["renewal_id"] ?>" data-delete-msg="<?= htmlspecialchars($message) ?>">Delete</button>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } } ?>
            </tbody>

        </table>

    </div>

</main>
</body>
</html>

<script src="../scripts/table-utils.js"></script>
