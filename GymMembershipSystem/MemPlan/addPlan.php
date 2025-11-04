<?php

    require_once "../classes/membership_plan.php";
    $plansObj = new MembershipPlans();

    $membership_plans = [];
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $membership_plans["plan_name"] = trim(htmlspecialchars($_POST["plan_name"]));
        $membership_plans["description"] = trim(htmlspecialchars($_POST["description"]));
        $membership_plans["duration"] = trim(htmlspecialchars($_POST["duration"])); 
        $membership_plans["price"] = trim(htmlspecialchars($_POST["price"]));
        $membership_plans["plan_type"] = trim(htmlspecialchars($_POST["plan_type"]));
        $membership_plans["status"] = trim(htmlspecialchars($_POST["status"]));

        if (empty($membership_plans["plan_name"])) {
            $errors["plan_name"] = "Plan name is required.";
        }

        if (empty($membership_plans["description"])) {
            $errors["description"] = "Description is required.";
        }

        if (empty($membership_plans["duration"])) {
            $errors["duration"] = "Duration is required.";
        } elseif (!is_numeric($membership_plans["duration"]) || $membership_plans["duration"] <= 0) {
            $errors["duration"] = "Duration must be a positive number and cannot be zero.";
        }

        if (empty($membership_plans["price"])) {
            $errors["price"] = "Price is required.";
        } elseif (!is_numeric($membership_plans["price"]) || $membership_plans["price"] <= 0) {
            $errors["price"] = "Price must be a positive number cannot be zero.";
        }

        if (empty($membership_plans["plan_type"])) {
            $errors["plan_type"] = "Plan type is required.";
        }

        if (empty($membership_plans["status"])) {
            $errors["status"] = "Status is required.";
        }

        if (empty(array_filter($errors))) {
            $plansObj->plan_name = $membership_plans["plan_name"];
            $plansObj->description = $membership_plans["description"];
            $plansObj->duration = $membership_plans["duration"];
            $plansObj->price = $membership_plans["price"];
            $plansObj->plan_type = $membership_plans["plan_type"];
            $plansObj->status = $membership_plans["status"];

            if ($plansObj->addPlan()) {
                header("Location: viewPlan.php");
            } else {
                echo "Failed to add plan. Please try again.";
            }
        }

        if ($plansObj->isPlanExist($membership_plans["plan_name"])) {
            $errors["plan_name"] = "Plan name already exists. Please choose a different name.";
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Membership Plans</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
     <main class="page-container">
        <section class="form-card">
                <h1> Add Membership Plan </h1>
                <p class="helper-note">Fields marked with <span class="required">*</span> are required.</p>

                <form action="" method="POST">

                <div class="form-group">
                    <label for="plan_name"> Plan Name <span>*</span class="required"></label>
                        <input class="form-control" type="text" name="plan_name" id="plan_name" value="<?=$membership_plans["plan_name"] ?? "" ?>">
                        <p class="error"><?=$errors["plan_name"] ?? "" ?></p>
                    </div>
                
                <div class="form-group">
                    <label for="description"> Description <span class="required">*</span></label>
                        <input class="form-control" type="text" name="description" id="description" value="<?=$membership_plans["description"] ?? "" ?>">
                        <p class="error"><?=$errors["description"] ?? "" ?></p>
                    </div>

                <div class="form-group">

                    <label for="duration"> Duration <span class="required">*</span></label>
                        <input class="form-control" type="text" name="duration" id="duration" value="<?=$membership_plans["duration"] ?? "" ?>">
                        <p class="error"><?=$errors["duration"] ?? "" ?></p>
                    </div>
                
                <div class="form-group">
                    <label for="price"> Price <span class="required">*</span></label>
                        <input class="form-control" type="text" name="price" id="price" value="<?=$membership_plans["price"] ?? "" ?>">
                        <p class="error"><?=$errors["price"] ?? "" ?></p>
                    </div>
                
                <div class="form-group">
                    <label for="plan_type"> Plan Type <span class="required">*</span></label>
                        <select class="form-control" name="plan_type" id="plan_type">
                            <option value="">---Select Plan Type---</option>
                            <option value="Individual" <?= (isset($membership_plans["plan_type"]) && $membership_plans["plan_type"] == "Individual") ? "selected":""?>>Individual</option>
                            <option value="Family" <?= (isset($membership_plans["plan_type"]) && $membership_plans["plan_type"] == "Family") ? "selected":""?>>Family</option>
                            <option value="Student" <?= (isset($membership_plans["plan_type"]) && $membership_plans["plan_type"] == "Student") ? "selected":""?>>Student</option>
                            <option value="Corporate" <?= (isset($membership_plans["plan_type"]) && $membership_plans["plan_type"] == "Corporate") ? "selected":""?>>Corporate</option>
                        </select>
                        <p class="error"><?=$errors["plan_type"] ?? "" ?></p>
                    </div>
                
                <div class="form-group">
                    <label for="status"> Status <span class="required">*</span></label>
                        <select class="form-control" name="status" id="status">
                            <option value="">---Select Status---</option>
                            <option value="active" <?= (isset($membership_plans["status"]) && $membership_plans["status"] == "Active") ? "selected":""?>>Active</option>
                            <option value="not_active" <?= (isset($membership_plans["status"]) && $membership_plans["status"] == "Not Active") ? "selected":""?>>Not Active</option>
                        </select>
                        <p class="error"><?=$errors["status"] ?? "" ?></p><br><br>
                    </div>
                
                <div class="footer-actions">
                    <button type="submit" class="btn btn-primary" value="Save Plan">Save Plan</button><button><a class="btn btn-outline" href="viewPlan.php">Cancel</a></button></span>
                    </div>
                </form>
        </section>
     </main>
</body> 

</html>