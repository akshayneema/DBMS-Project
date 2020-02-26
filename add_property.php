<?php
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
            
            list($a, $b) = $PayRentalDB->host_login($_POST["username"], $_POST["password"], $username_err, $password_err);

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
    <title>Add Property</title>
    <link rel="stylesheet" href="./mycss.css" type="text/css">
</head>
<body id = 'login'>
    <?php include "./header.php"; ?>
    <?php include "./host_mynav.php"; ?>
    <div class="wrapper">
        <h2>Property Details</h2>
        <p>Please fill in property details.</p>
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
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
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
            <?php header("location: host_home.php"); ?>
        <?php } ?>
    </div>    
    <?php include "./footer.php"; ?>
</body>
</html>