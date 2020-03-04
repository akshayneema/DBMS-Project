<?php

    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;

    // list($ci_date,$co_date,$diff,$id) = $_GET['val_list'];
    $id = $_GET['id'];
    $ci_date = $_GET['ci_date'];
    $co_date = $_GET['co_date'];
    $diff = $_GET['diff'];
    $price = $_GET['price'];
    $total_price = $price*$diff;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        try {
            $pdo = Connection::get()->connect();
            $PayRentalDB = new PayRentalDB($pdo);
            echo $PayRentalDB->confirm_booking($id,$ci_date,$co_date,$total_price);
        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>More Details</title>
    <link rel="stylesheet" href="./mycss.css" type="text/css">
</head>
<body id = 'property_detials'>
    <?php include "./header.php"; ?>
    <?php include "./mynav_logged_in.php"; ?>
    <div class="wrapper">
        <h2>Final Booking Step</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?id=".$id."&ci_date=".$ci_date."&co_date=".$co_date."&diff=".$diff."&price=".$price ; ?>" method="post">
            <p><?php echo "Property id: ".$id?></p>
            <p><?php echo "Check in date: ".$ci_date?></p>
            <p><?php echo "Check out date: ".$co_date?></p>
            <p><?php echo "Total number of days: ".$diff?></p>
            <p><?php echo "price per day: ".$price?></p>
            <p><?php echo "Total price: ".$total_price?></p>

            <input type="submit" class="book" id = "book" value="Confirm Booking">

        </form>
    </div>    
    <?php include "./footer.php"; ?>
</body>
</html>