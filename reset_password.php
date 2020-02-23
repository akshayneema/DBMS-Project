<?php
// Initialize the session
// session_start();
 
// Check if the user is logged in, if not then redirect to login page
// if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    // header("location: login.php");
    // exit;
// }
 

    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;

    // Define variables and initialize with empty values
    $username = $old_password = $new_password = $confirm_password = "";
    $username_err = $old_password_err = $new_password_err = $confirm_password_err = "";

    $processing = false;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter username.";
        } else{
            $username = trim($_POST["username"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["old_password"]))){
            $old_password_err = "Please enter your password.";
        } else{
            $old_password = trim($_POST["old_password"]);
        }

        // Validate new password
        if(empty(trim($_POST["new_password"]))){
            $new_password_err = "Please enter the new password.";     
        } elseif(strlen(trim($_POST["new_password"])) < 6){
            $new_password_err = "Password must have atleast 6 characters.";
        } else{
            $new_password = trim($_POST["new_password"]);
        }

        // Validate confirm password
        if(empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Please confirm the password.";
        } else{
            $confirm_password = trim($_POST["confirm_password"]);
            if(empty($new_password_err) && ($new_password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }

        $a = $username_err;
        $b = $old_password_err;
        $c = $new_password_err;
        $d = $confirm_password_err;
        
        // Check input errors before updating the database
        if(empty($new_password_err) && empty($confirm_password_err) && empty($old_password_err) && empty($username_err)){    
            echo "func call";
            try {
                // connect to the PostgreSQL database
                $pdo = Connection::get()->connect();
                //
                $PayRentalDB = new PayRentalDB($pdo);
                
                list($a, $b, $c, $d) = $PayRentalDB->reset_password($_POST["username"], $_POST["old_password"], $_POST["new_password"], $_POST["confirm_password"]);
            
                $processing = true;

            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="./mycss.css" type="text/css">
</head>
<body id='reset_password'>
    <?php include "./header.php"; ?>
    <?php include "./mynav_logged_in.php"; ?>
    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($old_password_err)) ? 'has-error' : ''; ?>">
                <label>Old Password</label>
                <input type="password" name="old_password" class="form-control" value="<?php echo $old_password; ?>">
                <span class="help-block"><?php echo $old_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="home.php">Cancel</a>
            </div>
            <?php if (!empty($a)){ ?>
                <h2> <?php echo "a ".$a; ?> </h2>
            <?php } else if (!empty($b)) { ?>
                <h2> <?php echo "b ".$b; ?> </h2>
            <?php } else if (!empty($c)) { ?>
                <h2> <?php echo "c ".$c; ?> </h2>
            <?php } else if (!empty($d)) { ?>
                <h2> <?php echo "d ".$d; ?> </h2>
            <?php } ?>
            <?php if (empty($a) AND empty($b) AND empty($c) AND empty($d) AND $processing){ ?>
                <h2> SUCCESS </h2>
                <?php //$processing = true ?>
                <?php header("location: home.php"); ?>
            <?php } ?>
        </form>
    </div>   
    <?php include "./footer.php"; ?> 
</body>
</html>