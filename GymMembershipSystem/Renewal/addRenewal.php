<?php
require_once "../classes/renewal_rec.php";
require_once "../classes/membership.php";
require_once "../classes/membership_plan.php";
require_once "../classes/payment.php";

$renewalObj = new RenewalRecords();
$membershipObj = new Memberships();
$planObj = new MembershipPlans();
$paymentObj = new Payments();

$memberships = $membershipObj->viewMembership();
$plans = $planObj->viewPlan();
$payments = $paymentObj->viewPayment();

// Fetch staff from profile where role_id = 2
$sql_staff = "SELECT id, fname, mname, lname FROM profile WHERE role_id = 2 ORDER BY fname, lname";
$db = new Database();
$qstaff = $db->connect()->prepare($sql_staff);
$employees = [];
if ($qstaff->execute()) {
    $employees = $qstaff->fetchAll();
}

$memberNames = [];
if (!empty($members)) {
    foreach ($members as $m) {
        $id = $m['member_id'] ?? $m['id'] ?? null;
        $memberNames[$id] = trim(($m['first_name'] ?? '') . ' ' . ($m['middle_initial'] ?? '') . ' ' . ($m['last_name'] ?? ''));
    }
}

$renewal = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $renewal["membership_id"] = trim(htmlspecialchars($_POST["membership_id"]));
    $renewal["plan_id"] = trim(htmlspecialchars($_POST["plan_id"]));
    $renewal["renewal_date"] = trim(htmlspecialchars($_POST["renewal_date"]));
    $renewal["previous_start_date"] = trim(htmlspecialchars($_POST["previous_start_date"]));
    $renewal["previous_expiry_date"] = trim(htmlspecialchars($_POST["previous_expiry_date"]));
    $renewal["new_start_date"] = trim(htmlspecialchars($_POST["new_start_date"]));
    $renewal["new_expiry_date"] = trim(htmlspecialchars($_POST["new_expiry_date"]));
    $renewal["payment_id"] = trim(htmlspecialchars($_POST["payment_id"]));
    $renewal["employee_id"] = trim(htmlspecialchars($_POST["employee_id"]));

    if (empty($renewal["membership_id"])) {
        $errors["membership_id"] = "Membership ID is required.";
    }

    if (empty($renewal["plan_id"])) {
        $errors["plan_id"] = "New plan ID is required.";
    }

    if (empty($renewal["renewal_date"])) {
        $errors["renewal_date"] = "Renewal date is required.";
    } elseif (strtotime($renewal["renewal_date"]) > strtotime(date("Y-m-d"))) {
        $errors["renewal_date"] = "Renewal date cannot be in the future.";
    }

    if (empty($renewal["previous_start_date"])) {
        $errors["previous_start_date"] = "Previous start date is required.";
    }  elseif (strtotime($renewal["previous_start_date"]) >= strtotime($renewal["previous_expiry_date"])) {
        $errors["previous_start_date"] = "Previous start date must be before previous expiry date.";
    } elseif (strtotime($renewal["previous_start_date"]) >= strtotime($renewal["new_start_date"])) {
        $errors["previous_start_date"] = "Previous start date must be before new start date.";
    } elseif (strtotime($renewal["previous_start_date"]) >= strtotime($renewal["new_expiry_date"])) {
        $errors["previous_start_date"] = "Previous start date must be before new expiry date.";
    }

    if (empty($renewal["previous_expiry_date"])) {
        $errors["previous_expiry_date"] = "Previous expiry date is required.";
    } elseif (strtotime($renewal["previous_expiry_date"]) <= strtotime($renewal["previous_start_date"])) {
        $errors["previous_expiry_date"] = "Previous expiry date must be after previous start date.";
    } elseif (strtotime($renewal["previous_expiry_date"]) >= strtotime($renewal["new_start_date"])) {
        $errors["previous_expiry_date"] = "Previous expiry date must be before new start date.";
    } elseif (strtotime($renewal["previous_expiry_date"]) >= strtotime($renewal["new_expiry_date"])) {
        $errors["previous_expiry_date"] = "Previous expiry date must be before new expiry date.";
    }   

    if (empty($renewal["new_start_date"])) {
        $errors["new_start_date"] = "New start date is required.";
    } elseif (strtotime($renewal["new_start_date"]) <= strtotime($renewal["previous_expiry_date"])) {
        $errors["new_start_date"] = "New start date must be after previous expiry date.";
    } elseif (strtotime($renewal["new_start_date"]) >= strtotime($renewal["new_expiry_date"])) {
        $errors["new_start_date"] = "New start date must be before new expiry date.";
    }

    if (empty($renewal["new_expiry_date"])) {
        $errors["new_expiry_date"] = "New expiry date is required.";
    } elseif (strtotime($renewal["new_expiry_date"]) <= strtotime($renewal["new_start_date"])) {
        $errors["new_expiry_date"] = "New expiry date must be after new start date.";
    }

    if (empty($renewal["payment_id"])) {
        $errors["payment_id"] = "Payment ID is required.";
    }

    if (empty($renewal["employee_id"])) {
        $errors["employee_id"] = "Employee ID is required.";
    }

    if (empty(array_filter($errors))) {
        $renewalObj->membership_id = $renewal["membership_id"];
        $renewalObj->plan_id = $renewal["plan_id"];
        $renewalObj->renewal_date = $renewal["renewal_date"];
        $renewalObj->previous_start_date = $renewal["previous_start_date"];
        $renewalObj->previous_expiry_date = $renewal["previous_expiry_date"];
        $renewalObj->new_start_date = $renewal["new_start_date"];
        $renewalObj->new_expiry_date = $renewal["new_expiry_date"];
        $renewalObj->payment_id = $renewal["payment_id"];
        $renewalObj->employee_id = $renewal["employee_id"];

        if ($renewalObj->addRenewal()) {
            header("Location: viewRenewal.php");
        } else {
            echo "Failed to add renewal. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Renewal Record</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>

<div class="page-container">
    <div class="form-card">

        <h1>Add Renewal Record</h1>

        <form action="" method="POST">

            <label for="" class="helper-note"><h6>Field with <span>*</span> is required</h6></label><br>

            <div class="form-group">
                <label for="membership_id">Membership ID <span class="required">*</span></label>
                <select class="form-control" name="membership_id" id="membership_id">
                    <option value="">--- Select Membership ---</option>
                    <?php if (!empty($memberships)) : ?>
                        <?php foreach ($memberships as $ms) : ?>
                            <?php 
                                $msId = $ms['membership_id'] ?? $ms['id'] ?? null; 
                                $mId = $ms['member_id'] ?? null; 
                                $mName = $memberNames[$mId] ?? ('Member ' . $mId); 
                                $planId = $ms['plan_id'] ?? ''; 
                            ?>
                            <option value="<?= $msId ?>" 
                                <?= (isset($renewal['membership_id']) && $renewal['membership_id'] == $msId) ? 'selected' : '' ?>>
                                Membership <?= $msId ?> - <?= htmlspecialchars($mName) ?> - Plan <?= $planId ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="error"><?= $errors['membership_id'] ?? '' ?></p>
            </div>

            <div class="form-group">
                <label for="plan_id">Plan ID <span class="required">*</span></label>
                <select class="form-control" name="plan_id" id="plan_id">
                    <option value="">--- Select Plan ---</option>
                    <?php if (!empty($plans)) : ?>
                        <?php foreach ($plans as $p) : ?>
                            <?php 
                                $pId = $p['plan_id'] ?? $p['id'] ?? null; 
                                $pName = $p['plan_name'] ?? ('Plan ' . $pId); 
                            ?>
                            <option value="<?= $pId ?>" 
                                <?= (isset($renewal['plan_id']) && $renewal['plan_id'] == $pId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pName) ?> (ID: <?= $pId ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="error"><?= $errors['plan_id'] ?? '' ?></p>
            </div>

            <div class="form-group">
                <label for="renewal_date">Renewal Date <span class="required">*</span></label>
                <input class="form-control" type="date" name="renewal_date" id="renewal_date"
                    value="<?=$renewal["renewal_date"] ?? "" ?>">
                <p class="error"><?=$errors["renewal_date"] ?? "" ?></p>
            </div>

            <div class="form-group">
                <label for="previous_start_date">Previous Start Date <span class="required">*</span></label>
                <input class="form-control" type="date" name="previous_start_date" id="previous_start_date"
                    value="<?=$renewal["previous_start_date"] ?? "" ?>">
                <p class="error"><?=$errors["previous_start_date"] ?? "" ?></p>
            </div>

            <div class="form-group">
                <label for="previous_expiry_date">Previous Expiry Date <span class="required">*</span></label>
                <input class="form-control" type="date" name="previous_expiry_date" id="previous_expiry_date"
                    value="<?=$renewal["previous_expiry_date"] ?? "" ?>">
                <p class="error"><?=$errors["previous_expiry_date"] ?? "" ?></p>
            </div>

            <div class="form-group">
                <label for="new_start_date">New Start Date <span class="required">*</span></label>
                <input class="form-control" type="date" name="new_start_date" id="new_start_date"
                    value="<?=$renewal["new_start_date"] ?? "" ?>">
                <p class="error"><?=$errors["new_start_date"] ?? "" ?></p>
            </div>

            <div class="form-group">
                <label for="new_expiry_date">New Expiry Date <span class="required">*</span></label>
                <input class="form-control" type="date" name="new_expiry_date" id="new_expiry_date"
                    value="<?=$renewal["new_expiry_date"] ?? "" ?>">
                <p class="error"><?=$errors["new_expiry_date"] ?? "" ?></p>
            </div>

            <div class="form-group">
                <label for="payment_id">Payment ID <span class="required">*</span></label>
                <select class="form-control" name="payment_id" id="payment_id">
                    <option value="">--- Select Payment ---</option>
                    <?php if (!empty($payments)) : ?>
                        <?php foreach ($payments as $pay) : ?>
                            <?php 
                                $payId = $pay['payment_id'] ?? $pay['id'] ?? null; 
                                $payDate = $pay['payment_date'] ?? ''; 
                                $payAmt = $pay['amount'] ?? ''; 
                                $payMs = $pay['membership_id'] ?? ''; 
                            ?>
                            <option value="<?= $payId ?>" 
                                <?= (isset($renewal['payment_id']) && $renewal['payment_id'] == $payId) ? 'selected' : '' ?>>
                                Payment <?= $payId ?> - <?= htmlspecialchars($payDate) ?> - <?= htmlspecialchars($payAmt) ?>
                                (Membership <?= $payMs ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="error"><?= $errors['payment_id'] ?? '' ?></p>
            </div>

            <div class="form-group">
                <label for="employee_id">Processed By <span class="required">*</span></label>
                <select class="form-control" name="employee_id" id="employee_id">
                    <option value="">--- Select Employee ---</option>
                    <?php if (!empty($employees)) : ?>
                        <?php foreach ($employees as $e) : ?>
                            <?php 
                                $eId = $e['id']; 
                                $eName = trim($e['fname'] . ' ' . ($e['mname'] ? $e['mname'] . ' ' : '') . $e['lname']); 
                            ?>
                            <option value="<?= $eId ?>" 
                                <?= (isset($renewal['employee_id']) && $renewal['employee_id'] == $eId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($eName) ?> (ID: <?= $eId ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="error"><?= $errors['employee_id'] ?? '' ?></p>
            </div>

            <div class="footer-actions">
                <input class="btn btn-primary" type="submit" value="Save Renewal">
                <a class="btn btn-outline" href="viewRenewal.php">View Renewals</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
