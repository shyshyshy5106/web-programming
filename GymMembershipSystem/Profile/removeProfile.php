<?php
require_once "../classes/profile.php";
$profileObj = new Profiles();

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if (isset($_GET["profile_id"])){
        $profile_id = trim(htmlspecialchars($_GET["profile_id"]));
        $profile = $profileObj->fetchProfile($profile_id);
        if (!$profile){
            echo "<a href='viewProfile.php'>Return to View Profiles</a>";
            exit("Profile Not Found!");
        } else {
            $profileObj->removeProfile($profile_id);
            header("Location: viewProfile.php");
        }
    } else {
        echo "<a href='viewProfile.php'>Return to View Profiles</a>";
        exit("Failed to delete profile. Please try again.");
    }
}
?>