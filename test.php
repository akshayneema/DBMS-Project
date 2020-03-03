<?php
    
    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
                
        try {
            // connect to the PostgreSQL database
            $pdo = Connection::get()->connect();
            //
            $PayRentalDB = new PayRentalDB($pdo);
            
            $PayRentalDB->test();


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
        <h2>User Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            
        </form>
    </div>    
    <?php include "./footer.php"; ?>
</body>
</html>