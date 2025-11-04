<?php
    require_once "../classes/membership_plan.php";
    $plansObj = new MembershipPlans();

    if ($_SERVER["REQUEST_METHOD"] == "GET"){
        if (isset($_GET["plan_id"])){
            $pplan_id = trim(htmlspecialchars($_GET["plan_id"]));
            $membership_plans = $plansObj->fetchPlan($pplan_id);
            if (!$membership_plans){
                echo "<a href='viewPlan.php'> Return to View Plans </a>";
                exit("Plan Not Found!");
            } else {
                $plansObj->removePlan($pplan_id);
                    header("Location: viewPlan.php");
                }
            } else {
                 echo "<a href='viewPlan.php'> Return to View Plans </a>";
                 exit("Failed to delete plan. Please try again.");
                }
    }
?>