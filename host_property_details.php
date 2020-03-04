

<?php

    /* draws a calendar */
    function draw_calendar($month,$year,$cal,$available_list,$price_list){

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

        /* table headings */
        $headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        $calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

        /* days and weeks vars now ... */
        $running_day = date('w',mktime(0,0,0,$month,1,$year));
        $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar.= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for($x = 0; $x < $running_day; $x++):
            $calendar.= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for($list_day = 1; $list_day <= $days_in_month; $list_day++):
            // echo " cal: ".($available_list[$list_day - 1] == 't')."\n";
            if ($available_list[$list_day - 1] == 't')
            {
                $calendar.= '<td class="calendar-day" bgcolor="#00FF00">';
            } else {
                $calendar.= '<td class="calendar-day" bgcolor="#FF0000">';

            }
                

                /* add in the day number */
                $calendar.= '<div class="day-number" ><font size="+3">'.$list_day."</font> ".$price_list[$list_day-1].'</div>';

                /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
                $calendar.= str_repeat('<p> </p>',2);
                
            $calendar.= '</td>';
            if($running_day == 6):
                $calendar.= '</tr>';
                if(($day_counter+1) != $days_in_month):
                    $calendar.= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++; $running_day++; $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if($days_in_this_week < 8):
            for($x = 1; $x <= (8 - $days_in_this_week); $x++):
                $calendar.= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar.= '</tr>';

        /* end the table */
        $calendar.= '</table>';
        
        /* all done, return result */
        return $calendar;
    }
    
    require 'vendor/autoload.php';

    use PostgreSQLTutorial\Connection as Connection;
    use PostgreSQLTutorial\PayRentalDB as PayRentalDB;

    $err="";    
    $processed = false;
    $id = $_GET['property_id'];
    try {
        // connect to the PostgreSQL database
        $pdo = Connection::get()->connect();
        //
        $PayRentalDB = new PayRentalDB($pdo);
        
        $details = $PayRentalDB->get_property_detials($id);
        list($cal_values, $available_list, $price_list) = $PayRentalDB->get_cal_values($id);

    } catch (\PDOException $e) {
        echo "Broken link";
        echo $e->getMessage();
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
    <?php include "./host_mynav.php"; ?>
    <div class="wrapper">
        <?php if (!strcmp($err, "") == 0) {?>
            <a style="color:Red;text-transform: capitalize;"><?php echo $err?></a>
        <?php } ?>
        <?php if ($processed){ 
            header("location: booking.php?id=".$id."&ci_date=".$_POST['checkin_date']."&co_date=".$_POST['checkout_date']."&diff=".($checkout_day-$checkin_day)."&price=".$details[0]['price']);
        } ?>

        <h2>More Details</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])."?property_id=".$id ; ?>" method="post">
            <p><?php echo "Property id: ".$id?></p>
            <h2><?php echo $details[0]['name']?></h2>

            <img alt="Qries" src="<?php echo htmlspecialchars($details[0]['picture']); ?>">

            <h4>LOCATION: </h4>
            <p><?php echo $details[0]['neighbourhood'].", ".$details[0]['city'].", ".$details[0]['state'].", ".$details[0]['zipcode']?></p>
            <h4>DESCRIPTION: </h4>
            <p><?php echo $details[0]['summary']?></p>

            <h4>PROPERTY DETAILS: </h4>
            <a style="color:Black;text-transform: capitalize;"><?php echo "property_type: ".$details[0]['property_type']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "room_type: ".$details[0]['room_type']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "accommodates: ".$details[0]['accommodates']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "bathrooms: ".$details[0]['bathrooms']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "bedrooms: ".$details[0]['bedrooms']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "beds: ".$details[0]['beds']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "amenities: ".str_replace(array('"'), '',trim($details[0]['amenities'],'{}'));?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "square_feet: ".$details[0]['beds']?></a><br>

            <h4>HOST DETAILS: </h4>
            <a style="color:Black;text-transform: capitalize;"><?php echo "name: ".$details[0]['host_name']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "since: ".$details[0]['host_since']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "location: ".$details[0]['host_location']?></a><br>
            <a style="color:Black;text-transform: lowercase;"><?php echo "Message from host: ".$details[0]['host_about']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "response time: ".$details[0]['host_response_time']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "response rate: ".$details[0]['host_response_rate']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "acceptance rate: ".$details[0]['host_acceptance_rate']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "superhost(T/F): ".$details[0]['host_is_superhost']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "listings count: ".$details[0]['host_listings_count']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "identity verified(T/F): ".$details[0]['host_identity_verified']?></a><br>

            <h4>BOOKING DETAILS: </h4>
            <a style="color:Black;text-transform: capitalize;"><?php echo "price: ".$details[0]['price']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "weekly price: ".$details[0]['weekly_price']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "monthly price: ".$details[0]['monthly_price']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "security deposit: ".$details[0]['security_deposit']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "cleaning fee: ".$details[0]['cleaning_fee']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "guests cost: ".$details[0]['guests_included']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "extra people: ".$details[0]['extra_people']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "instant bookable (T/F): ".$details[0]['instant_bookable']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "is business travel ready (T/F): ".$details[0]['is_business_travel_ready']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "cancellation policy: ".str_replace(array('_'), ' ',$details[0]['cancellation_policy'])?></a><br>
            
            <h4>REVIEWS: </h4>
            <a style="color:Black;text-transform: capitalize;"><?php echo "rating scores: ".$details[0]['review_scores_rating']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "cleanliness scores: ".$details[0]['review_scores_cleanliness']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "checkin scores: ".$details[0]['review_scores_checkin']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "communication scores: ".$details[0]['review_scores_communication']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "location scores: ".$details[0]['review_scores_location']?></a><br>
            <a style="color:Black;text-transform: capitalize;"><?php echo "value scores: ".$details[0]['review_scores_value']?></a><br>
            
            <h2>MARCH 2020</h2>
            <?php echo draw_calendar(3,2020,$cal_values,$available_list,$price_list);?>

        </form>
    </div>    
    <?php include "./footer.php"; ?>
</body>
</html>