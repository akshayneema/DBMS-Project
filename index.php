<!DOCTYPE html>
<html>
    <head>
        <title>PostgreSQL PHP Querying Data Demo</title>
        <link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
    </head>
    <body>
        <form action="index.php" method="GET">
            <!-- Price Range($): <input type="number" name="lower"> <input type="number" name="upper"> -->
            City: <input type="text" name="city">
            Property Type: <input type="text" name="ptype">
            Room Type: <datalist id="suggestions" name="rtype">
                            <option value="Entire home/apt"></option>
                            <option value="Private room"></option>
                            <option value="Shared room"></option>
                            <option value="Hotel room"></option>
                        </datalist>
                        <input  autoComplete="on" list="suggestions"/> 
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
                $stocks = $PayRentalDB->findByPK($_GET["ptype"], $_GET["rtype"], $_GET["city"]);


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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </body>
</html>