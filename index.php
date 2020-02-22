<!DOCTYPE html>
<html>
    <head>
        <title>PostgreSQL PHP Querying Data Demo</title>
        <link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
        <link rel="stylesheet" href="index.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
        <script src="//geodata.solutions/includes/statecity.js"></script>
    </head>
    <body>
        <form action="index.php" method="GET">
            <!-- Price Range($): <input type="number" name="lower"> <input type="number" name="upper"> -->
            <input type="hidden" name="country" id="countryId" value="US"/>
            State: <select name="state" class="states order-alpha" id="stateId">
                <option value="">Select State</option>
            </select>
            City: <select name="city" class="cities order-alpha" id="cityId">
                <option value="">Select City</option>
            </select>
            Distance: <input type="range"  name="dist" min="0" max="200" value="10" step="5" list="tickmarks" id="rangeInput" oninput="output.value = rangeInput.value">
            <output id="output" for="rangeInput">10</output> <!-- Just to display selected value -->
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
                            <input  autoComplete="on" list="psugg" name="ptype" /> 
            Room Type: <datalist id="suggestions">
                            <option value="All"></option>
                            <option value="Entire home/apt"></option>
                            <option value="Private room"></option>
                            <option value="Shared room"></option>
                            <option value="Hotel room"></option>
                        </datalist>
                        <input  autoComplete="on" list="suggestions" name="rtype" /> 
            <input type="submit">
        </form>
        <?php 
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
                $stocks = $PayRentalDB->findByPK($_GET["ptype"], $_GET["rtype"], $_GET["city"], $_GET["state"], $_GET["dist"]);


            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        ?>
        <div class="container">
            <h1>Property List</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Property Type</th>
                        <th>Room Type</th>
                        <th>Price</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Distance(km)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocks as $stock) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stock['id']) ?></td>
                            <td><?php echo htmlspecialchars($stock['property_type']); ?></td>
                            <td><?php echo htmlspecialchars($stock['room_type']); ?></td>
                            <td><?php echo htmlspecialchars($stock['price']); ?></td>
                            <td><?php echo htmlspecialchars($stock['city']); ?></td>
                            <td><?php echo htmlspecialchars($stock['state']); ?></td>
                            <td><?php echo htmlspecialchars($stock['distance']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>