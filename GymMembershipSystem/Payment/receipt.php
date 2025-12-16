<?php
session_start();

// Require login and roles
if (!isset($_SESSION["user"]) || ($_SESSION["user"]["role"] != "Staff" && $_SESSION["user"]["role"] != "Admin")) {
	header('location: ../account/login.php');
	exit();
}

require_once __DIR__ . '/../classes/payment.php';
require_once __DIR__ . '/../classes/membership.php';
require_once __DIR__ . '/../classes/database.php';

$paymentObj = new Payments();
$membershipObj = new Memberships();

$payment_id = isset($_GET['payment_id']) ? (int)$_GET['payment_id'] : 0;
if ($payment_id <= 0) {
	echo "Invalid payment id.";
	exit();
}

$payment = $paymentObj->fetchPayment($payment_id);
if (!$payment) {
	echo "Payment not found.";
	exit();
}

$membership = $membershipObj->fetchMembership($payment['membership_id']);

// Fetch staff name if present
$db = (new Database())->connect();
$staff_name = 'N/A';
if (!empty($payment['employee_id'])){
	$stmt = $db->prepare("SELECT CONCAT(fname, ' ', mname, ' ', lname) as name FROM profile WHERE id = :id AND role_id = 2");
	$stmt->bindParam(':id', $payment['employee_id'], PDO::PARAM_INT);
	if($stmt->execute()){
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($row && !empty($row['name'])) $staff_name = $row['name'];
	}
}

// Member name
$member_name = 'N/A';
if ($membership){
	$member_name = trim(($membership['member_fname'] ?? '') . ' ' . ($membership['member_lname'] ?? ''));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Receipt #<?= htmlspecialchars($payment_id) ?></title>
	<link rel="stylesheet" href="../index.css">
	<style>
		/* Small overrides for receipt layout to ensure print friendliness */
		.receipt-card { background: #fff; padding: 18px; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
		.receipt-meta { font-size: 13px; color: #666; }
		.receipt-table th { text-align:left; width:35%; background:#f7f7f7 }
		.amount { font-size: 18px; font-weight: 600; }
		@media print {
			.no-print { display: none !important; }
			body { background: #fff; }
		}
	</style>
</head>
<body>
	<header class="site-header">
		<div class="header-inner">
			<h1 class="site-title">Gym Management System</h1>
			<div class="header-actions no-print">
				<a href="../index.php" class="btn btn-outline">← Back</a>
			</div>
		</div>
	</header>

	<main class="page-container">
		<section class="mt-6">
			<div class="receipt-card">
				<div style="display:flex; justify-content:space-between; align-items:flex-start">
					<div>
						<h2 class="site-title">Receipt</h2>
						<div class="receipt-meta">Gym Management System</div>
					</div>
					<div style="text-align:right">
						<div class="receipt-meta">Receipt #: <strong><?= htmlspecialchars($payment['payment_id']) ?></strong></div>
						<div class="receipt-meta">Date: <strong><?= htmlspecialchars($payment['payment_date']) ?></strong></div>
					</div>
				</div>

				<table class="receipt-table" style="width:100%; margin-top:12px; border-collapse:collapse;">
					<tr>
						<th>Member</th>
						<td><?= htmlspecialchars($member_name) ?></td>
					</tr>
					<tr>
						<th>Membership #</th>
						<td><?= htmlspecialchars($payment['membership_id']) ?></td>
					</tr>
					<tr>
						<th>Plan</th>
						<td><?= htmlspecialchars($membership['plan_name'] ?? 'N/A') ?></td>
					</tr>
					<tr>
						<th>Amount</th>
						<td class="amount">Php <?= number_format((float)$payment['amount'], 2) ?></td>
					</tr>
					<tr>
						<th>Payment Mode</th>
						<td><?= htmlspecialchars($payment['payment_mode']) ?></td>
					</tr>
					<tr>
						<th>Payment Status</th>
						<td><?= htmlspecialchars($payment['payment_status']) ?></td>
					</tr>
					<tr>
						<th>Received By</th>
						<td><?= htmlspecialchars($staff_name) ?></td>
					</tr>
				</table>

				<div style="margin-top:16px" class="no-print">
					<a href="#" class="btn btn-primary" onclick="window.print(); return false;">Print / Download PDF</a>
					<a href="receipt.php?payment_id=<?= $payment_id ?>&print=1"  class="btn btn-primary" onclick="setTimeout(()=>window.print(),300); return false;">Open Print Dialog</a>
					<a href="../Payment/viewPayment.php" class="btn btn-outline">Back to Payments</a>
				</div>
			</div>
		</section>
	</main>

	<footer class="site-footer no-print">
		<p>&copy; <?= date('Y') ?> Gym Management System</p>
	</footer>

	<script>
		// Auto print when ?print=1 is provided
		(function(){
			const url = new URL(window.location.href);
			if (url.searchParams.get('print') === '1') {
				setTimeout(function(){ window.print(); }, 300);
			}
		})();
	</script>
</body>
</html>

