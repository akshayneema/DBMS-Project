<!DOCTYPE html>
<html lang="en-ca">
<head>
    <meta charset="utf-8">
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <link rel="stylesheet" href="./mycss.css" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
    <!-- <script src="//geodata.solutions/includes/statecity.js"></script> -->
    <title>HOME (page 1/5)</title>
</head>
<body id="host_booking">  <!-- id indicates page; is used by menu CSS to indicate active page.  No JS needed. -->

    <div class="outer">
        <?php include "./header.php"; ?>
        <?php include "./host_mynav.php"; ?>
        <br><br>
        <!-- <form action="user_booking.php" method="GET"> -->
            <!-- Price Range($): <input type="number" name="lower"> <input type="number" name="upper"> -->
            <!-- User id: <input type="number" name="userid"> -->
            <!-- <input type="submit"> -->
        <!-- </form> -->
        <?php 
            error_reporting(E_PARSE);
            require 'vendor/autoload.php';
            
            use PostgreSQLTutorial\Connection as Connection;
            use PostgreSQLTutorial\PayRentalDB as PayRentalDB;
                
            try {
                // connect to the PostgreSQL database
                $pdo = Connection::get()->connect();
                //
                $PayRentalDB = new PayRentalDB($pdo);
                // // get all stocks data
                // $stocks = $PayRentalDB->all();
                // get all stocks data
                // echo $_GET['userid'];
                $stocks = $PayRentalDB->host_my_bookings();
                
            } catch (\PDOException $e) {
                // echo "<h4 style='color:red'> Please enter the details </h4>";
                echo $e->getMessage();
            }
        ?>
        <div class="container">
            <h3>My Bookings</h3>
            <?php echo count($stocks)." results found!";?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Property Name</th>
                        <th>Check-In Date</th>
                        <th>Check-Out Date</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocks as $stock) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stock['booking_id']) ?></td>
                            <td><?php echo htmlspecialchars($stock['name']); ?></td>
                            <td><?php echo htmlspecialchars($stock['check_in_date']); ?></td>
                            <td><?php echo htmlspecialchars($stock['check_out_date']); ?></td>
                            <td><?php echo htmlspecialchars("$".$stock['price']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php include "./footer.php"; ?>
    </div>

</body>
</html>