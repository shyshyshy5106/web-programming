<?php

session_start();

// if user attempts to access this page without logging in
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
    header('location: ../account/login.php');
    exit(); // always add exit() after header redirect
}

    require_once "../classes/membership_plan.php";
    $plansObj = new MembershipPlans();
    $search = $plan_type = "";

    if($_SERVER["REQUEST_METHOD"] == "GET") {
        $search = isset($_GET["search"])? trim(htmlspecialchars($_GET["search"])) : "";
        $plan_type = isset($_GET["plan_type"])? trim(htmlspecialchars($_GET["plan_type"])) : "";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Membership Plans</title>
    <link rel="stylesheet" href="../view.css">
</head>
<body>
    <header class="page-header">
        <h1 class="page-title">Membership Plans</h1>
        <div class="header-actions">
            <a class="nav-link" href="../index.php">Back</a>
            <a class="btn btn-primary" href="addPlan.php">Add New Plan</a>
        </div>
    </header>

    <main class="page-container">
        <section class="form-card">
            <form action="" method="get" class="search-form">
                <label for="search" class="sr-only">Search Plan</label>
                <input class="search-input" type="search" name="search" id="search" value="<?= $search ?>" placeholder="Search plans by name or id"> <span> <button class="btn btn-search" type="submit">Search</button></span>
                
                <label for="plan_type" class="sr-only">Filter by Plan Type</label>
                <select class="filter-select" name="plan_type" id="plan_type">
                    <option value="">All Plan Types</option>
                    <option value="Individual" <?= (isset($plan_type) && $plan_type == "Individual")? "selected":"" ?>>Individual</option>
                    <option value="Family" <?= (isset($plan_type) && $plan_type == "Family")? "selected":"" ?>>Family</option>
                    <option value="Student" <?= (isset($plan_type) && $plan_type == "Student")? "selected":"" ?>>Student</option>
                    <option value="Corporate" <?= (isset($plan_type) && $plan_type == "Corporate")? "selected":"" ?>>Corporate</option>
                </select>
            </form>

            <div class="table-container">
                <table class="data-table" role="table">
                    <thead>
                        <tr>
                            <th>Plan ID</th>
                            <th>Plan Name</th>
                            <th>Description</th>
                            <th>Duration (days)</th>
                            <th>Price</th>
                            <th>Plan Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $plans = $plansObj->viewPlan($search,$plan_type);
                            if (empty($plans)) {
                                ?>
                                <tr><td colspan="8"><div class="table-empty"><div class="table-empty-icon">🗂️</div><div class="table-empty-text">No plans found.</div></div></td></tr>
                                <?php
                            } else {
                            foreach($plans as $membership_plans) {
                                $message = "Are you sure you want to delete this plan ". $membership_plans["plan_name"] . "?";
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($membership_plans["plan_id"]) ?></td>
                                <td class="truncate" data-fulltext="<?= htmlspecialchars($membership_plans['plan_name']) ?>"><?= htmlspecialchars($membership_plans["plan_name"]) ?></td>
                                <td class="truncate" data-fulltext="<?= htmlspecialchars($membership_plans['description']) ?>"><?= htmlspecialchars($membership_plans["description"]) ?></td>
                                <td><?= htmlspecialchars($membership_plans["duration"]) ?></td>
                                <td><?= htmlspecialchars($membership_plans["price"]) ?></td>
                                <td><?= htmlspecialchars($membership_plans["plan_type"]) ?></td>
                                <td><?= ucfirst(strtolower(htmlspecialchars($membership_plans["status"]))) ?></td>
                                <td class="table-actions">
                                    <a class="btn btn-action btn-edit" href="editPlan.php?plan_id=<?= htmlspecialchars($membership_plans["plan_id"]) ?>">Edit</a>
                                    <?php if($_SESSION["user"]["role"] == "Admin") { ?>
                                        <button class="btn btn-action btn-delete" data-delete-url="removePlan.php?plan_id=<?= htmlspecialchars($membership_plans["plan_id"]) ?>" data-delete-msg="<?= htmlspecialchars($message) ?>">Delete</button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
<script src="../scripts/table-utils.js"></script>