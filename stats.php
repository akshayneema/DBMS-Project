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
            
            // $stocks = $PayRentalDB->login($_POST["username"], $_POST["password"], $username_err, $password_err);

            //city based 
            $count = $PayRentalDB->count();
            $counthost = $PayRentalDB->counthost();
            $countreview = $PayRentalDB->countreview();
            $averageprice = $PayRentalDB->averageprice();
            $averagescore = $PayRentalDB->averagescore();
            $averagelocscore = $PayRentalDB->averagelocscore();
            $numsuperhosts = $PayRentalDB->numsuperhosts();
            $reviewpermonth = $PayRentalDB->reviewpermonth();
            $cleanestcity = $PayRentalDB->cleanestcity();

            //host based
            $highproperty = $PayRentalDB->highproperty();
            $highreviews = $PayRentalDB->highreviews();
            $goodhost = $PayRentalDB->goodhost();
            $longterm = $PayRentalDB->longterm();
            $avgearning = $PayRentalDB->avgearning();

            //user based 
            $mostactiveuser = $PayRentalDB-> mostactiveuser();
            $mostbookings = $PayRentalDB->mostbookings();

            //listingsbased
            $highdemand = $PayRentalDB-> highdemand();
            $highprice = $PayRentalDB-> highprice();
            $lowprice = $PayRentalDB-> lowprice();

            //areabased
            $listingny = $PayRentalDB-> listingny();
            $listingla = $PayRentalDB-> listingla();
            $listingc = $PayRentalDB-> listingc();
            $hostny = $PayRentalDB-> hostny();
            $hostla = $PayRentalDB-> hostla();
            $hostc= $PayRentalDB-> hostc();
            $reviewny= $PayRentalDB-> reviewny();
            $reviewla= $PayRentalDB-> reviewla();
            $reviewc= $PayRentalDB-> reviewc();
            $avpriceny= $PayRentalDB-> avpriceny();
            $avpricela= $PayRentalDB-> avpricela();
            $avpricec= $PayRentalDB-> avpricec();

            //room based
            $roomtype = $PayRentalDB->roomtype();
            $roomny = $PayRentalDB->roomny();
            $roomla = $PayRentalDB->roomla();
            $roomc = $PayRentalDB->roomc();

            //property based
            $propertytype = $PayRentalDB->propertytype();
            $propertyny =$PayRentalDB->propertyny();
            $propertyla = $PayRentalDB->propertyla();
            $propertyc = $PayRentalDB->propertyc();

            //reviews based
            $numreviewsbyyear = $PayRentalDB->numreviewsbyyear();
            // $reviewby2016 = $PayRentalDB->reviewby2016();
            // $reviewby2017 = $PayRentalDB->reviewby2017();
            // $reviewby2018 = $PayRentalDB->reviewby2018();
            // $reviewby2019 = $PayRentalDB->reviewby2019();

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
    <?php include "./mynav_logged_in.php"; ?>
    <div class="wrapper">
        <h2>STATS</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="show stats">
            </div>
            <?php if ($processed){ ?>

                <h2> CITY BASED COMPARISONS </h2> 
                <h3> City With Maximum Number of Hosts </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Number of Hosts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($counthost as $counthost) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($counthost['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($counthost['num_hosts_by_city']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Number of Listings per City : Supply </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Number of Listings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($count as $count) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($count['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($count['num_listings_by_city']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Number of Reviews per City : Demand </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Number of Reviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($countreview as $countreview) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($countreview['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($countreview['num_reviews_by_city']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Average Prices of Properties per City : Costly </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Average Price (in dollars)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($averageprice as $averageprice) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($averageprice['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($averageprice['avg_price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Average Scores per City </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Average Score(out of 100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($averagescore as $averagescore ) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($averagescore['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($averagescore['avg_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Best Location Citywise </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Average Location Score(out of 10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($averagelocscore as $averagelocscore ) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($averagelocscore['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($averagelocscore['avg_loc_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Number of Superhosts Per City </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Number of Superhosts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($numsuperhosts as $numsuperhosts) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($numsuperhosts['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($numsuperhosts['num_superhosts']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                 <h3> Maximum number of reviews per month Per City </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Total Number of Reviews per Month</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviewpermonth as $reviewpermonth) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reviewpermonth['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($reviewpermonth['num_reviews_per_month']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                 <h3> Cleanliness with City </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>City</th>
                            <th>Average Cleanliness Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cleanestcity as $cleanestcity) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cleanestcity['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($cleanestcity['clean_score']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <h2> HOST BASED COMPARISONS </h2> 
            <h3> Hosts with Highest Listings : Supply </h3> 
             <table>
                    <thead>
                        <tr>
                            <th>Hosts</th>
                            <th>Host Name</th>
                            <th>Number of Properties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($highproperty as $highproperty) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($highproperty['host_id']) ?></td>
                                <td><?php echo htmlspecialchars($highproperty['host_name']) ?></td>
                                <td><?php echo htmlspecialchars($highproperty['num_property']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Hosts with high number of reviews : Demand </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>Host ID</th>
                            <th>Host Name</th>
                            <th>City Data</th>
                            <th>Number of Reviews</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($highreviews as $highreviews) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($highreviews['id']) ?></td>
                                <td><?php echo htmlspecialchars($highreviews['host_name']) ?></td>
                                <td><?php echo htmlspecialchars($highreviews['city_data']); ?></td>
                                <td><?php echo htmlspecialchars($highreviews['num_reviews']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            
            <!-- $goodhost = $PayRentalDB->goodhost(); -->
            <h3> Lists of Good Hosts </h3> 
            <table>
                    <thead>
                        <tr>
                            <th>City Data</th>
                            <th>Host name</th>
                            <th>Good Hosts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($goodhost as $goodhost) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($goodhost['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($goodhost['host_name']) ?></td>
                                <td><?php echo htmlspecialchars($goodhost['number_goodhosts']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- $longterm = $PayRentalDB->longterm(); -->
            <h3> Long Term Hosts </h3> 
            <table>
                    <thead>
                        <tr>
                            <th>City Data</th>
                            <th>Hosts</th>
                            <th>Long Term Hosts</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($longterm as $longterm) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($longterm['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($longterm['host_name']) ?></td>
                                <td><?php echo htmlspecialchars($longterm['number_longtermhosts']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- $avgearning = $PayRentalDB->avgearning(); -->
            <h3> Highest Average Earning by Hosts </h3> 
            <table>
                    <thead>
                        <tr>
                            <th>Host ID</th>
                            <th>Hosts</th>
                            <th>Highest Average Earning</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avgearning as $avgearning) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($avgearning['host_id']) ?></td>
                                <td><?php echo htmlspecialchars($avgearning['host_name']) ?></td>
                                <td><?php echo htmlspecialchars($avgearning['avg_price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2> USER BASED COMPARISONS </h2> 
                <h3> Most Active Users </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>User ID </th>
                            <th>Users</th>
                            <th>Highest number of Comments : Most Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mostactiveuser as $mostactiveuser) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mostactiveuser['reviewer_id']) ?></td>
                                <td><?php echo htmlspecialchars($mostactiveuser['name']) ?></td>
                                <td><?php echo htmlspecialchars($mostactiveuser['num_reviews']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Users with highest Bookings </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>User ID </th>
                            <th>Users</th>
                            <th>Highest number of Bookings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mostbookings as $mostbookings) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($mostbookings['user_id']) ?></td>
                                <td><?php echo htmlspecialchars($mostbookings['name']) ?></td>
                                <td><?php echo htmlspecialchars($mostbookings['num_bookings']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2> LISTINGS BASED COMPARISONS </h2> 
                <h3> Listing with Highest Demand </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>Listing ID </th>
                            <th>City Data</th>
                            <th>Highest Demand</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($highdemand as $highdemand) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($highdemand['id']) ?></td>
                                <td><?php echo htmlspecialchars($highdemand['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($highdemand['review_count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h3> Listing with Highest Prices </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Listings </th>
                            <th>City  </th>
                            <th>Host ID</th>
                            <th>Highest prices</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($highprice as $highprice) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($highprice['id']) ?></td>
                                <td><?php echo htmlspecialchars($highprice['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($highprice['host_id']) ?></td>
                                <td><?php echo htmlspecialchars($highprice['price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <h3> Listing with Lowest Prices </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Listings </th>
                            <th>City  </th>
                            <th>Host ID</th>
                            <th>Lowestprices</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lowprice as $lowprice) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($lowprice['id']) ?></td>
                                <td><?php echo htmlspecialchars($lowprice['city_data']) ?></td>
                                <td><?php echo htmlspecialchars($lowprice['host_id']) ?></td>
                                <td><?php echo htmlspecialchars($lowprice['price']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- $listingny = $PayRentalDB-> listingny(); -->
            <h2> AREA BASED COMPARISONS </h2> 
            <table>
                    <thead>
                        <tr>
                            <th>NY City </th>
                            <th>Number of Listings  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listingny as $listingny) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($listingny['city']) ?></td>
                                <td><?php echo htmlspecialchars($listingny['total_listings']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $listingla = $PayRentalDB-> listingla(); -->
            <table>
                    <thead>
                        <tr>
                            <th>LA City </th>
                            <th>Number of Listings  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listingla as $listingla) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($listingla['city']) ?></td>
                                <td><?php echo htmlspecialchars($listingla['total_listings']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $listingc = $PayRentalDB-> listingc(); -->
            <table>
                    <thead>
                        <tr>
                            <th>Chicago City </th>
                            <th>Number of Listings  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listingc as $listingc) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($listingc['city']) ?></td>
                                <td><?php echo htmlspecialchars($listingc['total_listings']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $hostny = $PayRentalDB-> hostny(); -->
            <table>
                    <thead>
                        <tr>
                            <th>NY City </th>
                            <th>Number of Hosts  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hostny as $hostny) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($hostny['city']) ?></td>
                                <td><?php echo htmlspecialchars($hostny['total_hosts']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $hostla = $PayRentalDB-> hostla(); -->
            <table>
                    <thead>
                        <tr>
                            <th>LA City </th>
                            <th>Number of Hosts  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hostla as $hostla) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($hostla['city']) ?></td>
                                <td><?php echo htmlspecialchars($hostla['total_hosts']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- $hostc= $PayRentalDB-> hostc(); -->
            <table>
                    <thead>
                        <tr>
                            <th>Chicago City </th>
                            <th>Number of Hosts  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hostc as $hostc) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($hostc['city']) ?></td>
                                <td><?php echo htmlspecialchars($hostc['total_hosts']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <!-- $reviewny= $PayRentalDB-> reviewny(); -->
            <table>
                    <thead>
                        <tr>
                            <th>NY City </th>
                            <th>Number of Reviews  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviewny as $reviewny) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reviewny['city']) ?></td>
                                <td><?php echo htmlspecialchars($reviewny['total_reviews']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $reviewla= $PayRentalDB-> reviewla(); -->
            <table>
                    <thead>
                        <tr>
                            <th>LA City </th>
                            <th>Number of Reviews  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviewla as $reviewla) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reviewla['city']) ?></td>
                                <td><?php echo htmlspecialchars($reviewla['total_reviews']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $reviewc= $PayRentalDB-> reviewc(); -->
            <table>
                    <thead>
                        <tr>
                            <th>Chicago City </th>
                            <th>Number of Reviews  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviewc as $reviewc) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($reviewc['city']) ?></td>
                                <td><?php echo htmlspecialchars($reviewc['total_reviews']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $avpriceny= $PayRentalDB-> avpriceny(); -->
            <table>
                    <thead>
                        <tr>
                            <th>NY City </th>
                            <th>Average Price  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avpriceny as $avpriceny) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($avpriceny['city']) ?></td>
                                <td><?php echo htmlspecialchars($avpriceny['average_price']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $avpricela= $PayRentalDB-> avpricela(); -->
            <table>
                    <thead>
                        <tr>
                            <th>LA City </th>
                            <th>Average Price  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avpricela as $avpricela) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($avpricela['city']) ?></td>
                                <td><?php echo htmlspecialchars($avpricela['average_price']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- $avpricec= $PayRentalDB-> avpricec(); -->
            <table>
                    <thead>
                        <tr>
                            <th>Chicago City </th>
                            <th>Average Price  </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($avpricec as $avpricec) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($avpricec['city']) ?></td>
                                <td><?php echo htmlspecialchars($avpricec['average_price']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>



                <h2> ROOM BASED COMPARISONS </h2>  
                <table>
                    <thead>
                        <tr>
                            <th>Room Type</th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roomtype as $roomtype) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($roomtype['room_type']) ?></td>
                                <td><?php echo htmlspecialchars($roomtype['number_of_rooms']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <!-- $roomny = $PayRentalDB->roomny(); -->
            <h3> NY </h3>
            <table>
                    <thead>
                        <tr>
                            <th>Room Type </th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roomny as $roomny) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($roomny['room_type']) ?></td>
                                <td><?php echo htmlspecialchars($roomny['number_of_rooms']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $roomla = $PayRentalDB->roomla(); -->
            <h3> LA </h3>
            <table>
                    <thead>
                        <tr>
                            <th>Room Type </th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roomla as $roomla) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($roomla['room_type']) ?></td>
                                <td><?php echo htmlspecialchars($roomla['number_of_rooms']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $roomc = $PayRentalDB->roomc(); -->
            <h3> Chicago </h3>
            <table>
                    <thead>
                        <tr>
                            <th>Room Type </th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roomc as $roomc) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($roomc['room_type']) ?></td>
                                <td><?php echo htmlspecialchars($roomc['number_of_rooms']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2> PROPERTY BASED COMPARISONS </h2> 
                <table>
                    <thead>
                        <tr>
                            <th>Property Type</th>
                            <th>Type of Properties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($propertytype as $propertytype) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($propertytype['property_type']) ?></td>
                                <td><?php echo htmlspecialchars($propertytype['type_of_property']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            <h3> NY </h3>
            <table>
                    <thead>
                        <tr>
                            <th>Room Type </th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($propertyny as $propertyny) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($propertyny['property_type']) ?></td>
                                <td><?php echo htmlspecialchars($propertyny['type_of_property']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $roomla = $PayRentalDB->roomla(); -->
            <h3> LA </h3>
            <table>
                    <thead>
                        <tr>
                            <th>Room Type </th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($propertyla as $propertyla) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($propertyla['property_type']) ?></td>
                                <td><?php echo htmlspecialchars($propertyla['type_of_property']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- $roomc = $PayRentalDB->roomc(); -->
            <h3> Chicago </h3>
            <table>
                    <thead>
                        <tr>
                            <th>Room Type </th>
                            <th>Number of Rooms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($propertyc as $propertyc) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($propertyc['property_type']) ?></td>
                                <td><?php echo htmlspecialchars($propertyc['type_of_property']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>


                <h2> REVIEW BASED COMPARISONS </h2> 
                <h3> Popularity of Airbnb through years </h3> 
                <table>
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Number of Reviews (50% of bookings)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($numreviewsbyyear as $numreviewsbyyear) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($numreviewsbyyear['year']) ?></td>
                                <td><?php echo htmlspecialchars($numreviewsbyyear['number_of_reviews']); ?></td>
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