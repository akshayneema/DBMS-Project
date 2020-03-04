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
                $stocks = $PayRentalDB->host_my_property();
                
            } catch (\PDOException $e) {
                // echo "<h4 style='color:red'> Please enter the details </h4>";
                echo $e->getMessage();
            }
        ?>
        <div class="container">
            <h3>My Properties</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property Type</th>
                        <th>Room Type</th>
                        <th>Price</th>
                        <th>City</th>
                        <th>No. of Reviews</th>
                        <th>Rating</th>
                        <th>Distance(km)</th>
                        <th>Images</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocks as $stock) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stock['id']) ?></td>
                            <td><?php echo htmlspecialchars($stock['property_type']); ?></td>
                            <td><?php echo htmlspecialchars($stock['room_type']); ?></td>
                            <td><?php echo htmlspecialchars("$".$stock['price']); ?></td>
                            <td><?php echo htmlspecialchars($stock['city']); ?></td>
                            <td><?php echo htmlspecialchars($stock['rcount']); ?></td>
                            <td><?php echo htmlspecialchars($stock['rating']); ?></td>
                            <td><?php //echo htmlspecialchars($stock['distance']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($stock['picture']); ?>">
                                    Photos
                                </a>
                            </td>
                            <td>
                                <a href=<?php echo "host_property_details.php?property_id=".$stock['id'] ?>>
                                    More Info
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php include "./footer.php"; ?>
    </div>

</body>
</html>