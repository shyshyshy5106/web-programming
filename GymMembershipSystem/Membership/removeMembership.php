<?php
require_once "../classes/membership.php";
$membershipObj = new Memberships();

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if (isset($_GET["membership_id"])){
        $membership_id = trim(htmlspecialchars($_GET["membership_id"]));
        $membership = $membershipObj->fetchMembership($membership_id);
        if (!$membership){
            echo "<a href='viewMembership.php'>Return to View Memberships</a>";
            exit("Membership Not Found!");
        } else {
            $membershipObj->removeMembership($membership_id);
            header("Location: viewMembership.php");
        }
    } else {
        echo "<a href='viewMembership.php'>Return to View Memberships</a>";
        exit("Failed to delete membership. Please try again.");
    }
}
?>