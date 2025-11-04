<?php
    session_start();

        if (isset($_SESSION['user']) && ($_SESSION['user']['role'] == 'Staff' || $_SESSION['user']['role'] == 'Admin')){
            // for now will send user to Homepage
            header('location: ../index.php');
        }

    //if the login button is clicked
    require_once('../classes/account.php');
    $account = new Account();
    $error = "";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $account->email = htmlentities($_POST['email']);
        $account->password = htmlentities($_POST['password']);
        if ($account->login()){
            $_SESSION["user"] = $account->getAdminByEmail();
            // for now will send user to view Memberships
            header('location: ../index.php');
        }else{
            $error =  'Invalid email/password. Try again.';
        }
    }
    
    //if the above code is false then html below will be displayed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../index.css">
    <title>Login - Gym Management System</title>

</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1>Admin Login</h1>
            <p class="subtitle">Welcome back! Please login to your admin account to manage the gym membership system.</p>
        </div>

        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control" type="email" id="email" name="email" 
                       value="<?php if(isset($_POST['email'])) { echo htmlspecialchars($_POST['email']); } ?>"
                       required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input class="form-control" type="password" id="password" name="password" 
                       value="<?php if(isset($_POST['password'])) { echo htmlspecialchars($_POST['password']); } ?>"
                       required>
                <?php if($error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>

        <div style="text-align: center;">
            <a href="../index.php" class="back-link">← Back to Homepage</a>
        </div>
    </div>
</body>
</html>