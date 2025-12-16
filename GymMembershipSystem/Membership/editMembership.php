<?php
require_once "../classes/membership.php";
require_once "../classes/membership_plan.php";

$membershipObj = new Memberships();
$planObj = new MembershipPlans();

// Fetch members and employees directly from profile table with proper role filtering
$sql_members = "SELECT id, fname, mname, lname FROM profile WHERE role_id = 1 ORDER BY fname, lname";
$sql_staff = "SELECT id, fname, mname, lname FROM profile WHERE role_id = 2 ORDER BY fname, lname";

$db = new Database();
$query_members = $db->connect()->prepare($sql_members);
$query_staff = $db->connect()->prepare($sql_staff);

$members = [];
if ($query_members->execute()) {
    $members = $query_members->fetchAll();
}

$employees = [];
if ($query_staff->execute()) {
    $employees = $query_staff->fetchAll();
}

$plans = $planObj->viewPlan();

$membership = [];
$errors = [];

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if(isset($_GET["membership_id"])){
        $membership_id = trim(htmlspecialchars($_GET["membership_id"]));
        $membership = $membershipObj->fetchMembership($membership_id);
        if(!$membership){
            echo "<a href='viewMembership.php'>Return to View Memberships</a>";
            exit("Membership Not Found!");
        }
    } else {
        echo "<a href='viewMembership.php'>Return to View Memberships</a>";
        exit("Membership Not Found!");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $membership["member_id"] = trim(htmlspecialchars($_POST["member_id"]));
    $membership["plan_id"] = trim(htmlspecialchars($_POST["plan_id"]));
    $membership["start_date"] = trim(htmlspecialchars($_POST["start_date"]));
    $membership["expiry_date"] = trim(htmlspecialchars($_POST["expiry_date"]));
    $membership["original_expiry_date"] = trim(htmlspecialchars($_POST["original_expiry_date"]));
    $membership["membership_status"] = trim(htmlspecialchars($_POST["membership_status"]));
    $membership["employee_id"] = trim(htmlspecialchars($_POST["employee_id"]));

    if (empty($membership["member_id"])) {
        $errors["member_id"] = "Member ID is required.";
    }

    if (empty($membership["plan_id"])) {
        $errors["plan_id"] = "Plan ID is required.";
    }

    if (empty($membership["start_date"])) {
        $errors["start_date"] = "Start date is required.";
    } elseif (strtotime($membership["start_date"]) < strtotime(date("Y-m-d"))) {
        $errors["start_date"] = "Start date cannot be in the past.";
    } elseif (!empty($membership["expiry_date"]) && strtotime($membership["start_date"]) >= strtotime($membership["expiry_date"])) {
        $errors["start_date"] = "Start date must be before expiry date.";
    } elseif (!empty($membership["original_expiry_date"]) && strtotime($membership["start_date"]) >= strtotime($membership["original_expiry_date"])) {
        $errors["start_date"] = "Start date must be before original expiry date.";
    }

    if (empty($membership["expiry_date"])) {
        $errors["expiry_date"] = "Expiry date is required.";
    } elseif (strtotime($membership["expiry_date"]) <= strtotime($membership["start_date"])) {
        $errors["expiry_date"] = "Expiry date must be after start date.";
    } elseif (!empty($membership["original_expiry_date"]) && strtotime($membership["expiry_date"]) < strtotime($membership["original_expiry_date"])) {
        $errors["expiry_date"] = "Expiry date cannot be before original expiry date.";
    }

    if (empty($membership["original_expiry_date"])) {
        $errors["original_expiry_date"] = "Original expiry date is required.";
    } elseif (strtotime($membership["original_expiry_date"]) < strtotime($membership["start_date"])) {
        $errors["original_expiry_date"] = "Original expiry date must be after start date.";
    } elseif (strtotime($membership["original_expiry_date"]) > strtotime($membership["expiry_date"])) {
        $errors["original_expiry_date"] = "Original expiry date cannot be after expiry date.";
    }

    if (empty($membership["membership_status"])) {
        $errors["membership_status"] = "Membership status is required.";
    }

     if (empty($membership["employee_id"])) {
        $errors["employee_id"] = "Processed By (Employee ID) is required.";
    } elseif (!is_numeric($membership["employee_id"]) || $membership["employee_id"] <= 0) {
        $errors["employee_id"] = "Employee ID must be a positive number and cannot be zero.";
    }

    if (empty(array_filter($errors))) {
        $membershipObj->member_id = $membership["member_id"];
        $membershipObj->plan_id = $membership["plan_id"];
        $membershipObj->start_date = $membership["start_date"];
        $membershipObj->expiry_date = $membership["expiry_date"];
        $membershipObj->original_expiry_date = $membership["original_expiry_date"];
        $membershipObj->membership_status = $membership["membership_status"];
        $membershipObj->employee_id = $membership["employee_id"];

        if($membershipObj->editMembership($_GET["membership_id"])){
            header("Location: viewMembership.php");
        } else {
            echo "Failed to update membership. Please try again.";  
        }

        if ($membershipObj->isMembershipExist($membership["member_id"], $membership["plan_id"])) {
        $errors["member_id"] = "This member already has this plan assigned.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Membership</title>
    <link rel="stylesheet" href="../index.css">
</head>
<body>
     <main class="page-container">
        <section class="form-card">
                <h1>Edit Membership</h1>
                <p class="helper-note">Fields marked with <span class="required">*</span> are required.</p>

                <form action="" method="POST">

                <div class="form-group">
                    <label for=""><h6>Field with <span class="required">*</span> is required</h6></label><br>
                </div>

                <div class="form-group">
                    <label for="member_id">Member <span class="required">*</span></label>
                        <select class="form-control" name="member_id" id="member_id">
                            <option value="">--- Select Member ---</option>
                            <?php if (!empty($members)) : ?>
                                <?php foreach ($members as $m) : ?>
                                    <?php
                                        $mId = $m['member_id'] ?? $m['id'] ?? null;
                                        $mName = trim(($m['first_name'] ?? '') . ' ' . ($m['middle_initial'] ?? '') . ' ' . ($m['last_name'] ?? ''));
                                    ?>
                                    <option value="<?= $mId ?>" <?= (isset($membership['member_id']) && $membership['member_id'] == $mId) ? 'selected' : '' ?>><?= htmlspecialchars($mName) ?> (ID: <?= $mId ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="error"><?= $errors['member_id'] ?? '' ?></p>
                    </div>

                <div class="form-group">
                    <label for="plan_id">Plan <span class="required">*</span></label>
                        <select class="form-control" name="plan_id" id="plan_id">
                            <option value="">--- Select Plan ---</option>
                            <?php if (!empty($plans)) : ?>
                                <?php foreach ($plans as $p) : ?>
                                    <?php $pId = $p['plan_id'] ?? $p['id'] ?? null; $pName = $p['plan_name'] ?? 'Plan ' . $pId; ?>
                                    <option value="<?= $pId ?>" <?= (isset($membership['plan_id']) && $membership['plan_id'] == $pId) ? 'selected' : '' ?>><?= htmlspecialchars($pName) ?> (ID: <?= $pId ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="error"><?= $errors['plan_id'] ?? '' ?></p>
                    </div>

                <div class="form-group">
                    <label for="start_date">Start Date <span class="required">*</span></label>
                        <input class="form-control" type="date" name="start_date" id="start_date" value="<?=$membership["start_date"] ?? "" ?>">
                        <p class="error"><?=$errors["start_date"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="expiry_date">Expiry Date <span class="required">*</span></label>
                        <input class="form-control" type="date" name="expiry_date" id="expiry_date" value="<?=$membership["expiry_date"] ?? "" ?>">
                        <p class="error"><?=$errors["expiry_date"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="original_expiry_date">Original Expiry Date</label>
                        <input class="form-control" type="date" name="original_expiry_date" id="original_expiry_date" value="<?=$membership["original_expiry_date"] ?? "" ?>">
                        <p class="error"><?=$errors["original_expiry_date"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="membership_status">Membership Status <span class="required">*</span></label>
                        <select class="form-control" name="membership_status" id="membership_status">
                            <option value="">---Select Status---</option>
                            <option value="Active" <?= (isset($membership["membership_status"]) && $membership["membership_status"] == "Active") ? "selected":"" ?>>Active</option>
                            <option value="Expired" <?= (isset($membership["membership_status"]) && $membership["membership_status"] == "Expired") ? "selected":"" ?>>Expired</option>
                            <option value="Freeze" <?= (isset($membership["membership_status"]) && $membership["membership_status"] == "Freeze") ? "selected":"" ?>>Freeze</option>
                            <option value="Suspended" <?= (isset($membership["membership_status"]) && $membership["membership_status"] == "Suspended") ? "selected":"" ?>>Suspended</option>
                        </select>
                        <p class="error"><?=$errors["membership_status"] ?? "" ?></p>
                    </div>

                <div class="form-group">
                    <label for="employee_id">Processed By</label>
                        <select class="form-control" name="employee_id" id="employee_id">
                            <option value="">--- Select Employee ---</option>
                            <?php if (!empty($employees)) : ?>
                                <?php foreach ($employees as $e) : ?>
                                    <?php $eId = $e['employee_id'] ?? $e['id'] ?? null; $eName = trim(($e['first_name'] ?? '') . ' ' . ($e['middle_initial'] ?? '') . ' ' . ($e['last_name'] ?? '')); ?>
                                    <option value="<?= $eId ?>" <?= (isset($membership['employee_id']) && $membership['employee_id'] == $eId) ? 'selected' : '' ?>><?= htmlspecialchars($eName) ?> (ID: <?= $eId ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <p class="error"><?= $errors['employee_id'] ?? '' ?></p>
                    </div>

                <div class="footer-actions">
                    <button type="submit" class="btn btn-primary" value="Update Membership">Update Membership</button>
                    <button><a class="btn btn-outline" href="viewMembership.php">View Memberships</a></button>
                </div>

                </form>
        </section>
     </main>
</body>
</html>
