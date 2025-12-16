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
    <link rel="stylesheet" href="../view.css">
</head>

<body>

    <header class="page-header">
        <h1 class="page-title">View Memberships</h1>
        <div class="header-actions">
            <a class="nav-link" href="../index.php">Back</a>
            <a class="btn btn-primary" href="addMembership.php">Add Membership</a>
        </div>
    </header>

    <main class="page-container">
    <section class="form-card">

        <form action="" method="get" class="search-form">
            <label for="search" class="sr-only">Search Membership</label>
            <input class="search-input" type="search" name="search" id="search" value="<?= $search ?>" placeholder="Search by member name, membership id or plan">
            <button class="btn btn-search" type="submit">Search <span id="membership-spinner" hidden class="spinner"></span></button>

            <label for="status" class="sr-only">Filter by Status</label>
            <select class="filter-select" name="status" id="status">
                <option value="">All Statuses</option>
                <option value="Active" <?= (isset($status) && $status == "Active") ? "selected":"" ?>>Active</option>
                <option value="Expired" <?= (isset($status) && $status == "Expired") ? "selected":"" ?>>Expired</option>
                <option value="Freeze" <?= (isset($status) && $status == "Freeze") ? "selected":"" ?>>Freeze</option>
                <option value="Suspended" <?= (isset($status) && $status == "Suspended") ? "selected":"" ?>>Suspended</option>
            </select>
        </form>

    </section>

    <div class="table-container">
        <table class="data-table" role="table">
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
            $rows = $membershipObj->viewMembership($search, $status);
            if (empty($rows)):
            ?>
            <tr>
                <td colspan="9">
                    <div class="table-empty">
                        <div class="table-empty-icon">📦</div>
                        <div class="table-empty-text">No memberships found.</div>
                        <div class="table-empty-subtext">Try adjusting your filters.</div>
                    </div>
                </td>
            </tr>
            <?php else:
            foreach($rows as $membership) {
                $message = "Are you sure you want to delete this membership?";
                $memberName = trim($membership["member_fname"] . " " .
                            ($membership["member_mname"] ? $membership["member_mname"] . " " : "") .
                            $membership["member_lname"]);
                $staffName = trim($membership["staff_fname"] . " " .
                           ($membership["staff_mname"] ? $membership["staff_mname"] . " " : "") .
                           $membership["staff_lname"]);

                $expiryDate = new DateTime($membership["expiry_date"]);
                $today = new DateTime();
                $today->setTime(0, 0, 0);
                $expiryDate->setTime(0, 0, 0);

                $displayStatus = $expiryDate < $today ? "Expired" : $membership["membership_status"];
            ?>
                <tr tabindex="0">
                    <td><?= $membership["membership_id"] ?></td>
                    <td title="<?= htmlspecialchars($memberName . ' (ID: ' . $membership['member_id'] . ')') ?>" class="truncate" data-fulltext="<?= htmlspecialchars($memberName . ' (ID: ' . $membership['member_id'] . ')') ?>"><?= htmlspecialchars($memberName) ?></td>
                    <td title="<?= htmlspecialchars($membership["plan_name"] . ' (ID: ' . $membership['plan_id'] . ')') ?>" class="truncate" data-fulltext="<?= htmlspecialchars($membership["plan_name"] . ' (ID: ' . $membership['plan_id'] . ')') ?>"><?= htmlspecialchars($membership["plan_name"]) ?></td>
                    <td><?= htmlspecialchars(!empty($membership["start_date"]) ? date('Y-m-d', strtotime($membership["start_date"])) : '') ?></td>
                    <td><?= htmlspecialchars(!empty($membership["expiry_date"]) ? date('Y-m-d', strtotime($membership["expiry_date"])) : '') ?></td>
                    <td><?= htmlspecialchars(!empty($membership["original_expiry_date"]) ? date('Y-m-d', strtotime($membership["original_expiry_date"])) : '') ?></td>
                    <td><?= htmlspecialchars($displayStatus) ?></td>
                    <td title="<?= htmlspecialchars($staffName . ' (ID: ' . $membership['employee_id'] . ')') ?>" data-fulltext="<?= htmlspecialchars($staffName . ' (ID: ' . $membership['employee_id'] . ')') ?>"><?= htmlspecialchars($staffName) ?></td>

                    <td class="table-actions">
                        <a class="btn btn-action btn-edit" href="editMembership.php?membership_id=<?= $membership["membership_id"] ?>" title="Edit">Edit</a>

                        <?php if($_SESSION['user']['role'] == 'Admin') { ?>
                            <button class="btn btn-action btn-delete" data-delete-url="removeMembership.php?membership_id=<?= $membership['membership_id'] ?>" data-delete-msg="<?= htmlspecialchars($message) ?>">Delete</button>
                        <?php } ?>
                    </td>
                </tr>
            <?php }
            endif; ?>
            </tbody>
        </table>
    </div>

</main>

</body>
</html>

<!-- Delete confirmation modal (un-styled; uses hidden attribute so it's not visible by default) -->
<div id="confirm-delete-modal" hidden>
    <div>
        <h3>Confirm delete</h3>
        <p id="confirm-delete-msg">Are you sure you want to delete this item?</p>
        <div>
            <button id="confirm-delete-cancel">Cancel</button>
            <a id="confirm-delete-go" href="#">Delete</a>
        </div>
    </div>
</div>

<script>
    // Delete modal for membership list
    document.querySelectorAll('button[data-delete-url]').forEach(function(btn){
            btn.addEventListener('click', function(e){
            var url = btn.getAttribute('data-delete-url');
            var msg = btn.getAttribute('data-delete-msg') || 'Are you sure?';
            var modal = document.getElementById('confirm-delete-modal');
            document.getElementById('confirm-delete-msg').textContent = msg;
            var go = document.getElementById('confirm-delete-go');
            go.setAttribute('href', url);
            modal.hidden = false;
        });
    });
    document.getElementById('confirm-delete-cancel').addEventListener('click', function(){ document.getElementById('confirm-delete-modal').hidden = true; });
    // extra accessibility helpers
    document.addEventListener('DOMContentLoaded', function(){
        // ensure truncate cells are focusable
        document.querySelectorAll('.truncate').forEach(function(el){ if(!el.hasAttribute('tabindex')) el.setAttribute('tabindex','0'); });
    });
</script>
