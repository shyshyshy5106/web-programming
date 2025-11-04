<?php
require_once "../classes/roles.php";
$roleObj = new Roles();

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    if (isset($_GET["role_id"])){
        $role_id = trim(htmlspecialchars($_GET["role_id"]));
        $role = $roleObj->fetchRole($role_id);
        if (!$role){
            echo "<a href='viewRole.php'>Return to View Roles</a>";
            exit("Role Not Found!");
        } else {
            $roleObj->removeRole($role_id);
            header("Location: viewRole.php");
        }
    } else {
        echo "<a href='viewRole.php'>Return to View Roles</a>";
        exit("Failed to delete role. Please try again.");
    }
}
?>