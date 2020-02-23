<?php
    // Initialize the session
    // session_start();
     
    // Check if the user is already logged in, if yes then redirect him to welcome page
    // if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    //     header("location: welcome.php");
    //     exit;
    // }
     
    // Include config file
    // require_once "./app/config.php";

    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;

    // Define variables and initialize with empty values
    $username = $password = "";
    $username_err = $password_err = "";
     
    $processing = false;
    $logged_in = false;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter username.";
        } else{
            $username = trim($_POST["username"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter your password.";
        } else{
            $password = trim($_POST["password"]);
        }
        
        try {
            // connect to the PostgreSQL database
            $pdo = Connection::get()->connect();
            //
            $PayRentalDB = new PayRentalDB($pdo);
            
            list($a, $b) = $PayRentalDB->login($_POST["username"], $_POST["password"], $username_err, $password_err);

            $processing = true;

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="./mycss.css" type="text/css">
</head>
<body id = 'login'>
    <?php include "./header.php"; ?>
    <?php include "./mynav.php"; ?>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="sign_up.php">Sign up now</a>.</p>
            <?php if (!empty($a)){ ?>
                <h2> <?php echo $a; ?> </h2>
            <?php } else if (!empty($b)) { ?>
                <h2> <?php echo $b; ?> </h2>
            <?php } ?>
            <?php if (empty($a) AND empty($b) AND $processing){ ?>
                <?php $logged_in = true ?>
                <h2> <?php echo "LOGGED IN"; ?> </h2>
            <?php } ?>
        </form>
        <?php if ($logged_in){ ?>
            <?php header("location: home.php"); ?>
        <?php } ?>
    </div>    
    <?php include "./footer.php"; ?>
</body>
</html>