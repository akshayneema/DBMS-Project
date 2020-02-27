<!DOCTYPE html>
<html lang="en-ca">
<head>
    <meta charset="utf-8">
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
    <link rel="stylesheet" href="./mycss.css" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
    <script src="//geodata.solutions/includes/statecity.js"></script>
    <title>HOME (page 1/5)</title>
</head>
<body id="property">  <!-- id indicates page; is used by menu CSS to indicate active page.  No JS needed. -->

    <div class="outer">
        <?php include "./header.php"; ?>
        <?php include "./mynav_logged_in.php"; ?>

        <form action="property.php" method="GET">
            <!-- Price Range($): <input type="number" name="lower"> <input type="number" name="upper"> -->
            
            <input type="hidden" name="country" id="countryId" value="US"/>
            
            State: <select name="state" class="states order-alpha" id="stateId">
                <option value="">Select State</option>
            </select>
            
            City: <select name="city" class="cities order-alpha" id="cityId">
                <option value="">Select City</option>
            </select>
            
            Distance: <br> <input type="range" name="dist" min="0" max="200" step = "5" id="a" value="0" class ="slider" oninput="x.value=parseInt(a.value)"><output name="x" for="a"></output>
            
            <br>
            Price: <br><input type="range" name="price" min="0" max="1500" step = "5" id="b" value="0" class ="slider" oninput="y.value=parseInt(b.value)"><output name="y" for="b"></output>

            <br> <br>
            
            Property Type: <datalist id="psugg" style="height: 150px;">
                                <option value="All"></option>
                                <option value="Aparthotel"></option>
                                <option value="Apartment"></option>
                                <option value="Barn"></option>
                                <option value="Bed and breakfast"></option>
                                <option value="Boat"></option>
                                <option value="Boutique hotel"></option>
                                <option value="Bungalow"></option>
                                <option value="Bus"></option>
                                <option value="Cabin"></option>
                                <option value="Camper/RV"></option>
                                <option value="Campsite"></option>
                                <option value="Casa particular (Cuba)"></option>
                                <option value="Castle"></option>
                                <option value="Cave"></option>
                                <option value="Chalet"></option>
                                <option value="Condohotel"></option>
                                <option value="Condominium"></option>
                                <option value="Cottage"></option>
                                <option value="Dome house"></option>
                                <option value="Dorm"></option>
                                <option value="Earth house"></option>
                                <option value="Farm stay"></option>
                                <option value="Guesthouse"></option>
                                <option value="Guest suite"></option>
                                <option value="Hostel"></option>
                                <option value="Hotel"></option>
                                <option value="House"></option>
                                <option value="Houseboat"></option>
                                <option value="Hut"></option>
                                <option value="Igloo"></option>
                                <option value="In-law"></option>
                                <option value="Island"></option>
                                <option value="Lighthouse"></option>
                                <option value="Loft"></option>
                                <option value="Minsu (Taiwan)"></option>
                                <option value="Nature lodge"></option>
                                <option value="Other"></option>
                                <option value="Pension (South Korea)"></option>
                                <option value="Plane"></option>
                                <option value="Resort"></option>
                                <option value="Serviced apartment"></option>
                                <option value="Tent"></option>
                                <option value="Timeshare"></option>
                                <option value="Tiny house"></option>
                                <option value="Tipi"></option>
                                <option value="Townhouse"></option>
                                <option value="Train"></option>
                                <option value="Treehouse"></option>
                                <option value="Vacation home"></option>
                                <option value="Villa"></option>
                                <option value="Windmill"></option>
                                <option value="Yurt"></option>
                            </datalist>
                            <input  autoComplete="on" list="psugg" name="ptype"/> 
            Room Type: <datalist id="suggestions">
                            <option value="All"></option>
                            <option value="Entire home/apt"></option>
                            <option value="Private room"></option>
                            <option value="Shared room"></option>
                            <option value="Hotel room"></option>
                        </datalist>
                        <input  autoComplete="on" list="suggestions" name="rtype"/> 
            Sort By: <datalist id="options">
                            <option value="Price"></option>
                            <option value="Distance"></option>
                            <option value="Rating"></option>
                        </datalist>
                        <input  autoComplete="on" list="options" name="sort"/> 
            <input type="submit">
        </form>
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
                $stocks = $PayRentalDB->findByPK($_GET["ptype"], $_GET["rtype"], $_GET["city"], $_GET["state"], $_GET["dist"], $_GET["price"], $_GET["sort"]);
                
            } catch (\PDOException $e) {
                echo "<h4 style='color:red'> Please enter the details </h4>";
                // echo $e->getMessage();
            }
        ?>
        <div class="container">
            <h1>Property List</h1>
            <?php echo count($stocks)." results found!";?>
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
                            <td><?php echo htmlspecialchars($stock['distance']); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($stock['picture']); ?>">
                                    Photos
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