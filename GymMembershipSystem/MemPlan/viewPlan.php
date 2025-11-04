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
</head>
<body>
    <h2>Hi <?= $_SESSION["user"]["role"] ?></h2><a href="../account/logout.php">LogOut</a>
    <span></button><a href="../index.php"> Home </a></button></span><br><br>
    <h1> View Plans </h1>

    <form action="" method="get">

        <label for="">Search:</label>
            <input type="search" name="search" id="search" value="<?= $search ?>">
            <select name="plan_type" id="plan_type">
                <option value="">All</option>
                <option value="Individual" <?= (isset($plan_type) && $plan_type == "Individual")? "selected":"" ?>>Individual</option>
                <option value="Family" <?= (isset($plan_type) && $plan_type == "Family")? "selected":"" ?>>Family</option>
                <option value="Student" <?= (isset($plan_type) && $plan_type == "Student")? "selected":"" ?>>Student</option>
                <option value="Corporate" <?= (isset($plan_type) && $plan_type == "Corporate")? "selected":"" ?>>Corporate</option>
            </select>
            <input type="submit" value="Search"> <span><button><a href="addPlan.php">Add Plan</a></button></span>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>Plan ID</th>
                <th>Plan Name</th>
                <th>Description</th>
                <th>Duration (days)</th>
                <th>Price</th>
                <th>Plan Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <?php
            foreach($plansObj->viewPlan($search,$plan_type)as $membership_plans) {
                $message = "Are you sure you want to delete this plan ". $membership_plans["plan_name"] . "?";
        ?>

                
            <tr>
                <td> <?= $membership_plans["plan_id"]?> </td>
                <td> <?= $membership_plans["plan_name"] ?> </td>
                <td> <?= $membership_plans["description"] ?> </td>
                <td> <?= $membership_plans["duration"] ?> </td>
                <td> <?= $membership_plans["price"] ?> </td>
                <td> <?= $membership_plans["plan_type"] ?> </td>
                <td> <?= $membership_plans["status"] ?> </td>
                <td>
                    <a href="editPlan.php?plan_id=<?=$membership_plans["plan_id"]?>">Edit</a>
                    <?php
                    if($_SESSION["user"]["role"] == "Admin"){
                    ?>
                    <a href="removePlan.php?plan_id=<?= $membership_plans["plan_id"] ?>" onclick="return confirm('<?= $message ?>')">Delete</a>
                    <?php
                    }
                    ?>
                 </td>
            <tr>

        <?php
        }
        ?>
</body>
</html>
