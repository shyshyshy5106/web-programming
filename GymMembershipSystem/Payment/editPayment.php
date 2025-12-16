<?php
require_once "../classes/payment.php";
require_once "../classes/membership.php";
require_once "../classes/profile.php";

$paymentObj = new Payments();
$membershipObj = new Memberships();
$profileObj = new Profiles();

$memberships = $membershipObj->viewMembership();
$staff_profiles = $profileObj->viewProfile("", "2");

$memberNames = [];
if (!empty($members)) {
    foreach ($members as $m) {
        $id = $m['member_id'] ?? $m['id'] ?? null;
        $memberNames[$id] = trim(($m['first_name'] ?? '') . ' ' . ($m['middle_initial'] ?? '') . ' ' . ($m['last_name'] ?? ''));
    }
}

$payment = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["payment_id"])){
        $payment_id = trim(htmlspecialchars($_GET["payment_id"]));
        $payment = $paymentObj->fetchPayment($payment_id);
        if(!$payment){
            echo "<a href='viewPayment.php'>Return to View Payments</a>";
            exit("Payment Not Found!");
        }
    } else {
        echo "<a href='viewPayment.php'>Return to View Payments</a>";
        exit("Payment Not Found!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $payment["membership_id"] = trim(htmlspecialchars($_POST["membership_id"]));
    $payment["payment_date"] = trim(htmlspecialchars($_POST["payment_date"]));
    $payment["amount"] = trim(htmlspecialchars($_POST["amount"]));
    $payment["payment_mode"] = trim(htmlspecialchars($_POST["payment_mode"]));
    $payment["payment_status"] = trim(htmlspecialchars($_POST["payment_status"]));
    $payment["employee_id"] = trim(htmlspecialchars($_POST["employee_id"]));

    if (empty($payment["membership_id"])) {
        $errors["membership_id"] = "Membership ID is required.";
    }

    if (empty($payment["payment_date"])) {
        $errors["payment_date"] = "Payment date is required.";
    } elseif (strtotime($payment["payment_date"]) > strtotime(date("Y-m-d"))) {
        $errors["payment_date"] = "Payment date cannot be in the future.";
    }

    if (empty($payment["amount"])) {
        $errors["amount"] = "Amount is required.";
    } elseif (!is_numeric($payment["amount"]) || $payment["amount"] <= 0) {
        $errors["amount"] = "Amount must be a positive number.";
    }

    if (empty($payment["payment_mode"])) {
        $errors["payment_mode"] = "Payment mode is required.";
    }

    if (empty($payment["payment_status"])) {
        $errors["payment_status"] = "Payment status is required.";
    }

     if (empty($payment["employee_id"])) {
        $errors["employee_id"] = "Employee ID is required.";
    }

    if (empty(array_filter($errors))) {
    $paymentObj->membership_id = $payment["membership_id"];
    $paymentObj->payment_date = $payment["payment_date"];
    $paymentObj->amount = $payment["amount"];
    $paymentObj->payment_mode = $payment["payment_mode"];
    $paymentObj->payment_status = $payment["payment_status"];
    $paymentObj->employee_id = $payment["employee_id"];

        if($paymentObj->editPayment($_GET["payment_id"])){
            header("Location: viewPayment.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
     <main class="page-container">
        <section class="form-card">
                <h1>Edit Payment</h1>
                <p class="helper-note">Fields marked with <span class="required">*</span> are required.</p>

                <form action="" method="POST">

                <div class="form-group">
                    <label for="membership_id">Membership <span class="required">*</span></label>
                        <select class="form-control" name="membership_id" id="membership_id">
                            <option value="">--- Select Membership ---</option>
                            <?php if (!empty($memberships)) : ?>
                                <?php foreach ($memberships as $ms) : ?>
                                    <?php $msId = $ms['membership_id'] ?? $ms['id'] ?? null; $mId = $ms['member_id'] ?? null; $mName = $memberNames[$mId] ?? ('Member ' . $mId); $planId = $ms['plan_id'] ?? ''; ?>
                                    <option value="<?= $msId ?>" <?= (isset($payment['membership_id']) && $payment['membership_id'] == $msId) ? 'selected' : '' ?>>Membership <?= $msId ?> - <?= htmlspecialchars($mName) ?> - Plan <?= $planId ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="error"><?= $errors['membership_id'] ?? '' ?></p>
                    </div>

                <div class="form-group">
                    <label for="payment_date">Payment Date <span class="required">*</span></label>
                        <input class="form-control" type="date" name="payment_date" id="payment_date" value="<?=$payment["payment_date"] ?? "" ?>">
                        <p class="error"><?=$errors["payment_date"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="amount">Amount <span class="required">*</span></label>
                        <input class="form-control" type="text" name="amount" id="amount" value="<?=$payment["amount"] ?? "" ?>">
                        <p class="error"><?=$errors["amount"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="payment_mode">Payment Mode <span class="required">*</span></label>
                        <select class="form-control" name="payment_mode" id="payment_mode">
                            <option value="">---Select Payment Mode---</option>
                            <option value="Cash" <?= (isset($payment["payment_mode"]) && $payment["payment_mode"] == "Cash") ? "selected":"" ?>>Cash</option>
                            <option value="Card" <?= (isset($payment["payment_mode"]) && $payment["payment_mode"] == "Card") ? "selected":"" ?>>Card</option>
                            <option value="Bank Transfer" <?= (isset($payment["payment_mode"]) && $payment["payment_mode"] == "Bank Transfer") ? "selected":"" ?>>Bank Transfer</option>
                            <option value="E-wallet" <?= (isset($payment["payment_mode"]) && $payment["payment_mode"] == "E-wallet") ? "selected":"" ?>>E-wallet</option>
                        </select>
                        <p class="error"><?=$errors["payment_mode"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="payment_status">Payment Status <span class="required">*</span></label>
                        <select class="form-control" name="payment_status" id="payment_status">
                            <option value="">---Select Payment Status---</option>
                            <option value="Completed" <?= (isset($payment["payment_status"]) && $payment["payment_status"] == "Completed") ? "selected":"" ?>>Completed</option>
                            <option value="Pending" <?= (isset($payment["payment_status"]) && $payment["payment_status"] == "Pending") ? "selected":"" ?>>Pending</option>
                        </select>
                        <p class="error"><?=$errors["payment_status"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="employee_id">Received By<span class="required">*</span></label>
                        <select class="form-control" name="employee_id" id="employee_id">
                            <option value="">--- Select Staff ---</option>
                            <?php 
                            // Get only staff profiles (role_id = 2)
                            $staff_profiles = $profileObj->viewProfile("", "2");
                            if (!empty($staff_profiles)) : 
                                foreach ($staff_profiles as $staff) : ?>
                                    <option value="<?= $staff['id'] ?>" <?= (isset($payment['employee_id']) && $payment['employee_id'] == $staff['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($staff['fname'] . ' ' . $staff['mname'] . ' ' . $staff['lname']) ?>
                                    </option>
                                <?php endforeach; 
                            endif; ?>
                        </select>
                        <p class="error"><?= $errors['employee_id'] ?? '' ?></p>
                    </div>

                <div class="footer-actions">
                    <button type="submit" class="btn btn-primary" value="Update Payment">Update Payment</button>
                    <button><a class="btn btn-outline" href="viewPayment.php">View Payments</a></button>
                </div>

                </form>
        </section>
     </main>
</body>
</html>
