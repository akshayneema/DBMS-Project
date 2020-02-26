<?php

    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;
     
    $processed = false;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        try {
            // connect to the PostgreSQL database
            $pdo = Connection::get()->connect();
            //
            $PayRentalDB = new PayRentalDB($pdo);
            
            $stocks = $PayRentalDB->login($_POST["username"], $_POST["password"], $username_err, $password_err);

            $processed = true;

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>STATS</title>
    <link rel="stylesheet" href="./mycss.css" type="text/css">
</head>
<body id = 'stats'>
    <?php include "./header.php"; ?>
    <?php include "./mynav.php"; ?>
    <div class="wrapper">
        <h2>STATS</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="show stats">
            </div>
            <?php if ($processed){ ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Property Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stocks as $stock) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stock['id']) ?></td>
                                <td><?php echo htmlspecialchars($stock['property_type']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php } ?>
        </form>
    </div>    
    <?php include "./footer.php"; ?>
</body>
</html>