<header>
    <figure>
        <h1>Pa(y)rental: Appartment Renting Marketplace</h1>
        <figcaption>
            <p>Everything You Need<strong><?php
// Initialize the session
// session_start();
 
// Unset all of the session variables
// $_SESSION = array();
 
// Destroy the session.
// session_destroy();
 
// Redirect to login page
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
    $PayRentalDB->find_name();
    
} catch (\PDOException $e) {
    // echo "<h4 style='color:red'> Please enter the details </h4>";
    // echo $e->getMessage();
}
?></strong>. All Right Here.</p>
        </figcaption>
    </figure>
</header>