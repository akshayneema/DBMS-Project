<?php
    // require_once "./app/config.php";

    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;
    $username = $password = $confirm_password = "";
    $username_err = $password_err = $confirm_password_err = "";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        try {
            // connect to the PostgreSQL database
            $pdo = Connection::get()->connect();
            //
            $PayRentalDB = new PayRentalDB($pdo);
            
            list($a, $b, $c) = $PayRentalDB->signup($_POST["username"], $_POST["password"], $_POST["confirm_password"]);
            
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
     
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="./mycss.css" type="text/css">
</head>
<body id = 'sign_up'>
    <?php include "./header.php"; ?>
    <?php include "./mynav.php"; ?>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
            <?php if (!empty($a)){ ?>
                <h2> <?php echo $a; ?> </h2>
            <?php } else if (!empty($b)) { ?>
                <h2> <?php echo $b; ?> </h2>
            <?php } else if (!empty($c)) { ?>
                <h2> <?php echo $c; ?> </h2>
            <?php } ?>
        </form>
    </div>   
    <?php include "./footer.php"; ?> 
</body>
</html>