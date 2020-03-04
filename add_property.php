<?php
    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;

    // Define variables and initialize with empty values

    $name = $picture_url = $city = $state = $zipcode = $country = $latitude = $longitude = $property_type = $room_type = $accommodates = $bathrooms = $bedrooms = $beds = $price = $security_deposit = $cleaning_fee = $minimum_nights = $maximum_nights = "";
    $name_err = $picture_url_err = $city_err = $state_err = $zipcode_err = $country_err = $latitude_err = $longitude_err = $property_type_err = $room_type_err = $accommodates_err = $bathrooms_err = $bedrooms_err = $beds_err = $price_err = $security_deposit_err = $cleaning_fee_err = $minimum_nights_err = $maximum_nights_err = "";
     
    $processing = false;
    $logged_in = false;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        // Check if username is empty
        if(empty(trim($_POST["name"]))){
            $name_err = "Please enter name.";
        } else{
            $name = trim($_POST["name"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["picture_url"]))){
            $picture_url_err = "Please enter picture_url.";
        } else{
            $picture_url = trim($_POST["picture_url"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["city"]))){
            $city_err = "Please enter city.";
        } else{
            $city = trim($_POST["city"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["state"]))){
            $state_err = "Please enter state.";
        } else{
            $state = trim($_POST["state"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["zipcode"]))){
            $zipcode_err = "Please enter zipcode.";
        } else{
            $zipcode = trim($_POST["zipcode"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["country"]))){
            $country_err = "Please enter country.";
        } else{
            $country = trim($_POST["country"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["latitude"]))){
            $latitude_err = "Please enter latitude.";
        } else{
            $latitude = trim($_POST["latitude"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["longitude"]))){
            $longitude_err = "Please enter longitude.";
        } else{
            $longitude = trim($_POST["longitude"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["property_type"]))){
            $property_type_err = "Please enter property_type.";
        } else{
            $property_type = trim($_POST["property_type"]);
        }

        // Check if username is empty
        if(empty(trim($_POST["room_type"]))){
            $room_type_err = "Please enter room_type.";
        } else{
            $room_type = trim($_POST["room_type"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["accommodates"]))){
            $accommodates_err = "Please enter your accommodates.";
        } else{
            $accommodates = trim($_POST["accommodates"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["bathrooms"]))){
            $bathrooms_err = "Please enter your bathrooms.";
        } else{
            $bathrooms = trim($_POST["bathrooms"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["bedrooms"]))){
            $bedrooms_err = "Please enter your bedrooms.";
        } else{
            $bedrooms = trim($_POST["bedrooms"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["beds"]))){
            $beds_err = "Please enter your beds.";
        } else{
            $beds = trim($_POST["beds"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["price"]))){
            $price_err = "Please enter your price.";
        } else{
            $price = trim($_POST["price"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["security_deposit"]))){
            $security_deposit_err = "Please enter your security_deposit.";
        } else{
            $security_deposit = trim($_POST["security_deposit"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["cleaning_fee"]))){
            $cleaning_fee_err = "Please enter your cleaning_fee.";
        } else{
            $cleaning_fee = trim($_POST["cleaning_fee"]);
        }

        // Check if password is empty
        if(empty(trim($_POST["minimum_nights"]))){
            $minimum_nights_err = "Please enter your minimum_nights.";
        } else{
            $minimum_nights = trim($_POST["minimum_nights"]);
        }
        
        // Check if password is empty
        if(empty(trim($_POST["maximum_nights"]))){
            $maximum_nights_err = "Please enter your maximum_nights.";
        } else{
            $maximum_nights = trim($_POST["maximum_nights"]);
        }
        

        try {
            // connect to the PostgreSQL database
            $pdo = Connection::get()->connect();
            //
            $PayRentalDB = new PayRentalDB($pdo);
            
            $list_err = $PayRentalDB->add_property($name , $picture_url , $city , $state , $zipcode , $country , $latitude , $longitude , $property_type , $room_type , $accommodates , $bathrooms , $bedrooms , $beds , $price , $security_deposit , $cleaning_fee , $minimum_nights , $maximum_nights);

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
<body id = 'add_property'>
    <?php include "./header.php"; ?>
    <?php include "./host_mynav.php"; ?>
    <div class="wrapper">
        <h2>Property Details</h2>
        <p>Please fill in property details.</p>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($name_err)) ? 'has-error' : ''; ?>">
                <label>name             </label>
                <input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
                <span class="help-block"><?php echo $name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($picture_url_err)) ? 'has-error' : ''; ?>">
                <label>Picture_url      </label>
                <input type="text" name="picture_url" class="form-control" value="<?php echo $picture_url; ?>">
                <span class="help-block"><?php echo $picture_url_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($city_err)) ? 'has-error' : ''; ?>">
                <label>City             </label>
                <input type="text" name="city" class="form-control" value="<?php echo $city; ?>">
                <span class="help-block"><?php echo $city_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($state_err)) ? 'has-error' : ''; ?>">
                <label>State            </label>
                <input type="text" name="state" class="form-control" value="<?php echo $state; ?>">
                <span class="help-block"><?php echo $state_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($zipcode_err)) ? 'has-error' : ''; ?>">
                <label>zipcode          </label>
                <input type="text" name="zipcode" class="form-control" value="<?php echo $zipcode; ?>">
                <span class="help-block"><?php echo $zipcode_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($country_err)) ? 'has-error' : ''; ?>">
                <label>country          </label>
                <input type="text" name="country" class="form-control" value="<?php echo $country; ?>">
                <span class="help-block"><?php echo $country_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($latitude_err)) ? 'has-error' : ''; ?>">
                <label>latitude         </label>
                <input type="text" name="latitude" class="form-control" value="<?php echo $latitude; ?>">
                <span class="help-block"><?php echo $latitude_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($longitude_err)) ? 'has-error' : ''; ?>">
                <label>longitude        </label>
                <input type="text" name="longitude" class="form-control" value="<?php echo $longitude; ?>">
                <span class="help-block"><?php echo $longitude_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($property_type_err)) ? 'has-error' : ''; ?>">
                <label>Property type    </label>
                <input type="text" name="property_type" class="form-control" value="<?php echo $property_type; ?>">
                <span class="help-block"><?php echo $property_type_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($room_type_err)) ? 'has-error' : ''; ?>">
                <label>Room type        </label>
                <input type="text" name="room_type" class="form-control" value="<?php echo $room_type; ?>">
                <span class="help-block"><?php echo $room_type_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($accommodates_err)) ? 'has-error' : ''; ?>">
                <label>Accommodates     </label>
                <input type="text" name="accommodates" class="form-control" value="<?php echo $accommodates; ?>">
                <span class="help-block"><?php echo $accommodates_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($bathrooms_err)) ? 'has-error' : ''; ?>">
                <label>Bathrooms        </label>
                <input type="text" name="bathrooms" class="form-control" value="<?php echo $bathrooms; ?>">
                <span class="help-block"><?php echo $bathrooms_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($bedrooms_err)) ? 'has-error' : ''; ?>">
                <label>Bedrooms         </label>
                <input type="text" name="bedrooms" class="form-control" value="<?php echo $bedrooms; ?>">
                <span class="help-block"><?php echo $bedrooms_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($beds_err)) ? 'has-error' : ''; ?>">
                <label>Beds             </label>
                <input type="text" name="beds" class="form-control" value="<?php echo $beds; ?>">
                <span class="help-block"><?php echo $beds_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                <label>Price            </label>
                <input type="text" name="price" class="form-control" value="<?php echo $price; ?>">
                <span class="help-block"><?php echo $price_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($security_deposit_err)) ? 'has-error' : ''; ?>">
                <label>Security deposit </label>
                <input type="text" name="security_deposit" class="form-control" value="<?php echo $security_deposit; ?>">
                <span class="help-block"><?php echo $security_deposit_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($cleaning_fee_err)) ? 'has-error' : ''; ?>">
                <label>Cleaning fee     </label>
                <input type="text" name="cleaning_fee" class="form-control" value="<?php echo $cleaning_fee; ?>">
                <span class="help-block"><?php echo $cleaning_fee_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($minimum_nights_err)) ? 'has-error' : ''; ?>">
                <label>minimum nights   </label>
                <input type="text" name="minimum_nights" class="form-control" value="<?php echo $minimum_nights; ?>">
                <span class="help-block"><?php echo $minimum_nights_err; ?></span>
            </div>   
            <div class="form-group <?php echo (!empty($maximum_nights_err)) ? 'has-error' : ''; ?>">
                <label>maximum nights   </label>
                <input type="text" name="maximum_nights" class="form-control" value="<?php echo $maximum_nights; ?>">
                <span class="help-block"><?php echo $maximum_nights_err; ?></span>
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