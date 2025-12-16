<?php
require_once "../classes/payment.php";
$paymentObj = new Payments();

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if (isset($_GET["payment_id"])){
        $payment_id = trim(htmlspecialchars($_GET["payment_id"]));
        $payment = $paymentObj->fetchPayment($payment_id);
        if (!$payment){
            echo "<a href='viewPayment.php'>Return to View Payments</a>";
            exit("Payment Not Found!");
        } else {
            $paymentObj->removePayment($payment_id);
            header("Location: viewPayment.php");
        }
    } else {
        echo "<a href='viewPayment.php'>Return to View Payments</a>";
        exit("Failed to delete payment. Please try again.");
    }
}
?>
