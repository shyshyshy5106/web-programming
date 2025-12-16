<?php
require_once "../classes/renewal_rec.php";
$renewalObj = new RenewalRecords();

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if (isset($_GET["renewal_id"])){
        $renewal_id = trim(htmlspecialchars($_GET["renewal_id"]));
        $renewal = $renewalObj->fetchRenewal($renewal_id);
        if (!$renewal){
            echo "<a href='viewRenewal.php'>Return to View Renewals</a>";
            exit("Renewal Record Not Found!");
        } else {
            $renewalObj->removeRenewal($renewal_id);
            header("Location: viewRenewal.php");
        }
    } else {
        echo "<a href='viewRenewal.php'>Return to View Renewals</a>";
        exit("Failed to delete renewal. Please try again.");
    }
}
?>