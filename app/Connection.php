<?php
namespace PostgreSQLTutorial;
 
/**
* Represent the Connection
*/
class Connection {
 
    /**
     * Connection
     * @var type
     */
    private static $conn;
 
    /**
     * Connect to the database and return an instance of \PDO object
     * @return \PDO
     * @throws \Exception
     */
    public function connect() {
 
        // read parameters in the ini configuration file
        $params = parse_ini_file('database.ini');
        if ($params === false) {
            throw new \Exception("Error reading database configuration file");
        }
        // connect to the postgresql database
        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
                $params['host'],
                $params['port'],
                $params['database'],
                $params['user'],
                $params['password']);
 
        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
 
        return $pdo;
    }
 
    /**
     * return an instance of the Connection object
     * @return type
     */
    public static function get() {
        if (null === static::$conn) {
            static::$conn = new static();
        }
 
        return static::$conn;
    }
 
    protected function __construct() {
        
    }
 
    private function __clone() {
        
    }
 
    private function __wakeup() {
        
    }
 
}

class PostgreSQLCreateTable {
 
    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
 
    /**
     * init the object with a \PDO object
     * @param type $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    /**
     * create tables
     */
    public function createTables() {
        $sqlList = ['CREATE TABLE IF NOT EXISTS stocks (
                        id serial PRIMARY KEY,
                        symbol character varying(10) NOT NULL UNIQUE,
                        company character varying(255) NOT NULL UNIQUE
                     );',
            'CREATE TABLE IF NOT EXISTS stock_valuations (
                        stock_id INTEGER NOT NULL,
                        value_on date NOT NULL,
                        price numeric(8,2) NOT NULL DEFAULT 0,
                        PRIMARY KEY (stock_id, value_on),
                        FOREIGN KEY (stock_id) REFERENCES stocks(id)
                    );'];
 
        // execute each sql statement to create new tables
        foreach ($sqlList as $sql) {
            $this->pdo->exec($sql);
        }
        
        return $this;
    }
 
    /**
     * return tables in the database
     */
    public function getTables() {
        $stmt = $this->pdo->query("SELECT table_name
                                   FROM information_schema.tables
                                   WHERE table_schema= 'public'
                                        AND table_type='BASE TABLE'
                                   ORDER BY table_name");
        $tableList = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tableList[] = $row['table_name'];
        }
 
        return $tableList;
    }

}

class PostgreSQLPHPInsert {
 
    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
 
    /**
     * init the object with a \PDO object
     * @param type $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    /**
     * insert a new row into the stocks table
     * @param type $symbol
     * @param type $company
     * @return the id of the inserted row
     */
    public function insertStock($symbol, $company) {
        // prepare statement for insert
        $sql = 'INSERT INTO stocks(symbol,company) VALUES(:symbol,:company)';
        $stmt = $this->pdo->prepare($sql);
        
        // pass values to the statement
        $stmt->bindValue(':symbol', $symbol);
        $stmt->bindValue(':company', $company);
        
        // execute the insert statement
        $stmt->execute();
        
        // return generated id
        return $this->pdo->lastInsertId('stocks_id_seq');
    }

     /**
     * Insert multiple stocks into the stocks table
     * @param array $stocks
     * @return a list of inserted ID
     */
    public function insertStockList($stocks) {
        $sql = 'INSERT INTO stocks(symbol,company) VALUES(:symbol,:company)';
        $stmt = $this->pdo->prepare($sql);
 
        $idList = [];
        foreach ($stocks as $stock) {
            $stmt->bindValue(':symbol', $stock['symbol']);
            $stmt->bindValue(':company', $stock['company']);
            $stmt->execute();
            $idList[] = $this->pdo->lastInsertId('stocks_id_seq');
        }
        return $idList;
    }

}

class PostgreSQLPHPUpdate {
 
    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
 
    /**
     * Initialize the object with a specified PDO object
     * @param \PDO $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
    /**
     * Update stock based on the specified id
     * @param int $id
     * @param string $symbol
     * @param string $company
     * @return int
     */
    public function updateStock($id, $symbol, $company) {
 
        // sql statement to update a row in the stock table
        $sql = 'UPDATE stocks '
                . 'SET company = :company, '
                . 'symbol = :symbol '
                . 'WHERE id = :id';
 
        $stmt = $this->pdo->prepare($sql);
 
        // bind values to the statement
        $stmt->bindValue(':symbol', $symbol);
        $stmt->bindValue(':company', $company);
        $stmt->bindValue(':id', $id);
        // update data in the database
        $stmt->execute();
 
        // return the number of row affected
        return $stmt->rowCount();
    }
}

class PayRentalDB {
 
    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;
 
    /**
     * Initialize the object with a specified PDO object
     * @param \PDO $pdo
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
 
     /**
     * Return all rows in the stocks table
     * @return array
     */
    public function all() {
        $stmt = $this->pdo->query('SELECT id, property_type, room_type, price, city '
                . 'FROM payrental '
                . 'ORDER BY id');
        $stocks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $stocks[] = [
                'id' => $row['id'],
                'property_type' => $row['property_type'],
                'room_type' => $row['room_type'],
                'price' => $row['price'],
                'city' => $row['city']
            ];
        }
        return $stocks;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function findByPK($ptype, $rtype, $city, $dist, $price, $sortby) {
        // prepare SELECT statement
        $lat = 0.00;
        $lng = 0.00;
        if (strcmp($city,"New York")==0)
        {
            $lat = 40.6943;
            $lng = -73.9249;
        } else if (strcmp($city,"Chicago")==0)
        {
            $lat = 41.8373;
            $lng = -87.6862;
        } else if (strcmp($city,"Los Angeles")==0)
        {
            $lat = 34.1139;
            $lng = -118.4068;
        } 
        $query = "SELECT payrental.id, property_type, room_type, cast(price as integer), payrental.city, number_of_reviews as rcount, cast(review_scores_rating as integer) as rating, round(distance(latitude::decimal, longitude::decimal, :lat, :lng)::numeric, 2) as distance, picture_url, (case when id in (select listing_id from calender group by listing_id having count(*) filter (where available = 't') = 0) then 0 else 1 end) as full_booked FROM payrental";
        $count = 0;

        $query=$query." WHERE city_data = :city";

        if(!(strcmp($rtype,"All")==0 OR strcmp($rtype,"")==0))
        {
            $query=$query." AND room_type = :rtype";
        }
        if(!(strcmp($ptype,"All")==0 OR strcmp($ptype,"")==0))
        {
            $query=$query." AND property_type = :ptype";
        }
        $query=$query." AND payrental.price <= :price";
        if(strcmp($sortby,"")==0)
            $query=$query." AND distance(latitude::decimal, longitude::decimal, :lat1, :lng1)<:dist LIMIT 100;";
        else if(strcmp($sortby,"Rating")==0)
            $query=$query." AND distance(latitude::decimal, longitude::decimal, :lat1, :lng1)<:dist AND review_scores_rating is not null order by ".$sortby." desc LIMIT 100;";
        else
            $query=$query." AND distance(latitude::decimal, longitude::decimal, :lat1, :lng1)<:dist order by ".$sortby." LIMIT 100;";

        // echo $query;
        $stmt = $this->pdo->prepare($query);

        // bind value to the :id parameter
        if(!(strcmp($rtype,"All")==0 OR strcmp($rtype,"")==0))
        {
            $stmt->bindValue(':rtype', $rtype);
        }
        if(!(strcmp($ptype,"All")==0 OR strcmp($ptype,"")==0))
        {
            $stmt->bindValue(':ptype', $ptype);
        }
        if(!(strcmp($city,"All")==0 OR strcmp($city,"")==0))
        {
            $stmt->bindValue(':city', $city);
        }
        $stmt->bindValue(':dist', $dist);
        $stmt->bindValue(':price', $price);
        $stmt->bindValue(':lat', $lat);
        $stmt->bindValue(':lng', $lng);
        $stmt->bindValue(':lat1', $lat);
        $stmt->bindValue(':lng1', $lng);

        // echo " stmt: ".$stmt;
        
        
        // execute the statement
        $stmt->execute();
        // // return the result set as an object
        // return $stmt->fetchObject();

        $stocks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $stocks[] = [
                'id' => $row['id'],
                'property_type' => $row['property_type'],
                'room_type' => $row['room_type'],
                'price' => $row['price'],
                'city' => $row['city'],
                'distance' => $row['distance'],
                'rcount' => $row['rcount'],
                'rating' => $row['rating'],
                'picture' => $row['picture_url'],
                'full_booked' => $row['full_booked']
            ];
        }
        return $stocks;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function findCityList($state) {
        // prepare SELECT statement
        $query = "SELECT city FROM cityinfo WHERE state_name = :state;";

        // echo $query;
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':state', $state);
        
        // execute the statement
        $stmt->execute();

        $city_list = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $city_list[] = ['city' => $row['city']];
        }
        return $city_list;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function signup($u, $p, $cp) {
        // Define variables and initialize with empty values
        $username = $password = $confirm_password = "";
        $username_err = $password_err = $confirm_password_err = "";
         
        // Validate username
        if(empty(trim($u))){
            $username_err = "Please enter a username.";
        } else{
            // Prepare a select statement
            $query = "SELECT user_id FROM users WHERE username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['user_id']
                ];
            }
            // echo " stocks count: ".count($stocks)." ";

            if(count($stocks) == 1){
                $username_err = "This username is already taken.";
            } else{
                $username = trim($u);
            } 
        }

        // Validate password
        if(empty(trim($p))){
            $password_err = "Please enter a password.";     
        } elseif(strlen(trim($p)) < 6){
            $password_err = "Password must have atleast 6 characters.";
        } else{
            $password = trim($p);
        }

        // Validate confirm password
        if(empty(trim($cp))){
            $confirm_password_err = "Please confirm password.";     
        } else{
            $confirm_password = trim($cp);
            if(empty($password_err) && ($password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }
        
        // Check input errors before inserting in database
        if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
            
            // Prepare an insert statement
            $query = "INSERT INTO users (user_id ,username, password) VALUES ((select max(user_id + 1) from users),:username, :password)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $password);
            $stmt->execute();
        }

        return array($username_err, $password_err, $confirm_password_err);
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function host_signup($name, $u, $p, $cp) {
        // Define variables and initialize with empty values
        $username = $password = $confirm_password = "";
        $name_err = $username_err = $password_err = $confirm_password_err = "";
         
        // Validate username
        if(empty(trim($u))){
            $username_err = "Please enter a username.";
        } else{
            // Prepare a select statement
            $query = "SELECT host_id FROM hosts WHERE host_username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['host_id']
                ];
            }
            // echo " stocks count: ".count($stocks)." ";

            if(count($stocks) == 1){
                $username_err = "This username is already taken.";
            } else{
                $username = trim($u);
            } 
        }

        // Validate name
        if(empty(trim($name))){
            $name_err = "Please enter a name.";     
        }

        // Validate password
        if(empty(trim($p))){
            $password_err = "Please enter a password.";     
        } elseif(strlen(trim($p)) < 6){
            $password_err = "Password must have atleast 6 characters.";
        } else{
            $password = trim($p);
        }

        // Validate confirm password
        if(empty(trim($cp))){
            $confirm_password_err = "Please confirm password.";     
        } else{
            $confirm_password = trim($cp);
            if(empty($password_err) && ($password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }
        
        // Check input errors before inserting in database
        if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
            
            // Prepare an insert statement
            $query = "INSERT INTO hosts (host_id, host_name, host_username, password) VALUES ((select max(host_id::integer + 1) from hosts),:name, :username, :password)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $password);
            $stmt->execute();
        }

        return array($username_err, $password_err, $confirm_password_err, $name_err);
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function login($u, $p, $ue, $pe) {
        $username = $password = "";
        $username_err = $ue;
        $password_err = $pe;

        // Validate credentials
        if(empty($username_err) && empty($password_err)){
            // Prepare a select statement
            $query = "SELECT user_id, username, password FROM users WHERE username = :username and password = :password";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->bindValue(':password', $p);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['user_id'],
                    'username' => $row['username'],
                    'password' => $row['password']
                ];
            }
            if (count($stocks) == 0) {
                $username_err = "No account found with that username and password.";
            }

            // if (!(strcmp($p, $stocks[0]["password"]) == 0)) {
                // $password_err = "The password you entered was not valid.";
            // }

            
        }
        if(empty($username_err) && empty($password_err))
        {
            $query = "INSERT into curruser(user_id) values (".$stocks[0]['id'].")";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
        }  
        return array($username_err, $password_err);
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function host_login($u, $p, $ue, $pe) {
        $username = $password = "";
        $username_err = $ue;
        $password_err = $pe;

        // Validate credentials
        if(empty($username_err) && empty($password_err)){
            // Prepare a select statement
            $query = "SELECT host_id, host_username, password FROM hosts WHERE host_username = :username and password= :password";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->bindValue(':password', $p);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['host_id'],
                    'username' => $row['host_username'],
                    'password' => $row['password']
                ];
            }

            if (count($stocks) == 0) {
                $username_err = "No account found with that username and password.";
            }

            // if (!(strcmp($p, $stocks[0]["password"]) == 0)) {
                // $password_err = "The password you entered was not valid.";
            // }

            
        }  
        if(empty($username_err) && empty($password_err))
        {
            $query = "INSERT into currhost(user_id) values (".$stocks[0]['id'].")";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
        }  
        return array($username_err, $password_err);
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function count() {

        $query = "SELECT city_data, count(*) as number_of_listings FROM payrental GROUP BY city_data ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $count = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $count[] = 
            [
                'city_data' => $row['city_data'],
                'num_listings_by_city' => $row['number_of_listings'],
            ];
        }  

        return $count;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function counthost() {

        $query = "SELECT city_data, count(DISTINCT(host_id)) as number_of_hosts FROM payrental GROUP BY city_data ORDER BY count(DISTINCT(host_id)) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $counthost = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $counthost[] = 
            [
                'city_data' => $row['city_data'],
                'num_hosts_by_city' => $row['number_of_hosts'],
            ];
        }  
        return $counthost;
    }


     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function countreview() {

        $query = "SELECT city_data, sum(cast(number_of_reviews as integer)) as number_of_reviews FROM payrental GROUP BY city_data ORDER BY sum(cast(number_of_reviews as integer)) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $countreview = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $countreview[] = 
            [
                'city_data' => $row['city_data'],
                'num_reviews_by_city' => $row['number_of_reviews'],
            ];
        }  

        // echo "Size of count is :" .count($count)  ;   
        
      return $countreview;
    }

         /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function averageprice() {

        $query = "SELECT city_data, avg(price) as average_price FROM payrental GROUP by city_data ORDER BY avg(price)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $averageprice = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $averageprice[] = 
            [
                'city_data' => $row['city_data'],
                'avg_price' => $row['average_price'],
            ];
        }  

      return $averageprice;
    }


         /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function averagescore() {

        $query = "SELECT city_data, avg(cast(review_scores_rating as integer)) as score FROM payrental GROUP BY city_data ORDER BY avg(cast(review_scores_rating as integer)) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $averagescore = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $averagescore[] = 
            [
                'city_data' => $row['city_data'],
                'avg_score' => $row['score'],
            ];
        }  

      return $averagescore;
    }

    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function averagelocscore() {

        $query = "SELECT city_data, avg(cast(review_scores_location as integer)) as score FROM payrental GROUP BY city_data ORDER BY avg(cast(review_scores_location as integer)) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $averagelocscore = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $averagelocscore[] = 
            [
                'city_data' => $row['city_data'],
                'avg_loc_score' => $row['score'],
            ];
        }  

      return $averagelocscore;
    }


    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function numsuperhosts() {

        $query = "SELECT city_data, count(*) as number_of_superhosts FROM payrental WHERE host_is_superhost = 't' GROUP BY city_data ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $numsuperhosts = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $numsuperhosts[] = 
            [
                'city_data' => $row['city_data'],
                'num_superhosts' => $row['number_of_superhosts'],
            ];
        }  

      return $numsuperhosts;
    }


    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function reviewpermonth() {

        $query = "SELECT city_data, avg(cast(reviews_per_month as double precision)) as average_reviews_per_month FROM payrental GROUP by city_data ORDER by avg(cast(reviews_per_month as double precision)) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $reviewpermonth = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $reviewpermonth[] = 
            [
                'city_data' => $row['city_data'],
                'num_reviews_per_month' => $row['average_reviews_per_month'],
            ];
        }  

      return $reviewpermonth;
    }


    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function cleanestcity() {

        $query = "SELECT city_data, avg(cast(review_scores_cleanliness as double precision)) as cleanliness_scores FROM payrental WHERE cast(review_scores_cleanliness as double precision) IS NOT NULL GROUP by city_data ORDER by avg(cast(review_scores_cleanliness as double precision)) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $cleanestcity = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $cleanestcity[] = 
            [
                'city_data' => $row['city_data'],
                'clean_score' => $row['cleanliness_scores'],
            ];
        }  

      return $cleanestcity;
    }

    // $highproperty = $PayRentalDB->highproperty();
        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function highproperty() {
        $query = "SELECT listingsperhost.host_id, hosts.host_name, count_listings_per_host as highest_listings FROM listingsperhost, hosts where listingsperhost.host_id = hosts.host_id ORDER by count_listings_per_host desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $highproperty = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $highproperty[] = 
            [
                'host_id' => $row['host_id'],
                'host_name' => $row['host_name'],
                'num_property' => $row['highest_listings'],
            ];
        }  

      return $highproperty;
    }

    // $highreviews = $PayRentalDB->highreviews();
        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function highreviews() {

        $query = "SELECT id, host_name, city_data, sum(cast(number_of_reviews as integer)) as num_reviews FROM payrental GROUP BY id, host_name, city_data ORDER BY sum(cast(number_of_reviews as integer)) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $highreviews = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $highreviews[] = 
            [
                'id' => $row['id'],
                'host_name' => $row['host_name'],
                'city_data' => $row['city_data'],
                'num_reviews' => $row['num_reviews'],
            ];
        }  

      return $highreviews;
    }


    // $goodhost = $PayRentalDB->goodhost();
    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function goodhost() {

        $query = "SELECT city_data, host_name, count(*) as number_goodhosts FROM goodhosts GROUP BY city_data, host_name ORDER BY count(*) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $goodhost = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $goodhost[] = 
            [
                'city_data' => $row['city_data'],
                'host_name' => $row['host_name'],
                'number_goodhosts' => $row['number_goodhosts'],
            ];
        }  

      return $goodhost;
    }


    // $longterm = $PayRentalDB->longterm();
        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function longterm() {

        $query = "SELECT city_data, host_name, count(*) as number_longtermhosts FROM goodhosts GROUP BY city_data, host_name ORDER BY count(*) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $longterm = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $longterm[] = 
            [
                'city_data' => $row['city_data'],
                'host_name' => $row['host_name'],
                'number_longtermhosts' => $row['number_longtermhosts'],
            ];
        }  

      return $longterm;
    }

            // $avgearning = $PayRentalDB->avgearning();
                /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function avgearning() {

        $query = "SELECT host_id, host_name, avg(price) as avg_price FROM payrental GROUP BY host_id, host_name ORDER BY avg(price) DESC LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $avgearning = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $avgearning[] = 
            [
                'host_id' => $row['host_id'],
                'host_name' => $row['host_name'],
                'avg_price' => $row['avg_price'],
            ];
        }  

      return $avgearning;
    }

        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function mostactiveuser() {

        $query = "SELECT reviewer_id, name, count(*) as num_reviews FROM reviews, users WHERE reviews.reviewer_id = users.user_id GROUP BY reviewer_id, name ORDER BY count(*) DESC LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $mostactiveuser = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $mostactiveuser[] = 
            [
                'reviewer_id' => $row['reviewer_id'],
                'name' => $row['name'],
                'num_reviews' => $row['num_reviews'],
            ];
        }  

      return $mostactiveuser;
    }

       /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function mostbookings() {

        $query = "SELECT bookings.user_id, users.name, count(*) as num_bookings FROM bookings, users WHERE bookings.user_id = users.user_id GROUP BY bookings.user_id, name ORDER BY count(*) DESC LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $mostbookings = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $mostbookings[] = 
            [
                'user_id' => $row['user_id'],
                'name' => $row['name'],
                'num_bookings' => $row['num_bookings'],
            ];
        }  

      return $mostbookings;
    }

       /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function highdemand() {

        $query = "SELECT id, city_data, sum(cast(number_of_reviews as integer)) as review_count FROM payrental GROUP BY id, city_data ORDER BY sum(cast(number_of_reviews as integer))  desc LIMIT 10";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $highdemand = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $highdemand[] = 
            [
                'id' => $row['id'],
                'city_data' => $row['city_data'],
                'review_count' => $row['review_count'],
            ];
        }  

      return $highdemand;
    }

       /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function highprice() {

        $query = "SELECT id, city_data, host_id, avg(price) as price FROM payrental GROUP BY id, host_id, city_data ORDER BY avg(price) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $highprice = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $highprice[] = 
            [
                'id' => $row['id'],
                'city_data' => $row['city_data'],
                'host_id' => $row['host_id'],
                'price' => $row['price'],
            ];
        }  

      return $highprice;
    }


   /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function lowprice() {

        $query = "SELECT id, city_data, host_id, avg(price) as price FROM payrental GROUP BY id, host_id, city_data ORDER BY avg(price) asc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $lowprice = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lowprice[] = 
            [
                'id' => $row['id'],
                'city_data' => $row['city_data'],
                'host_id' => $row['host_id'],
                'price' => $row['price'],
            ];
        }  

      return $lowprice;
    }

    // $listingny = $PayRentalDB-> listingny();
    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function listingny() {

        $query = "SELECT city, sum(number_of_listings) AS total_listings FROM listingsbyareany WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_listings) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $listingny = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $listingny[] = 
            [
                'city' => $row['city'],
                'total_listings' => $row['total_listings'],
            ];
        }  

      return $listingny;
    }

    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function listingla() {

        $query = "SELECT city, sum(number_of_listings) AS total_listings FROM listingsbyareala WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_listings) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $listingla = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $listingla[] = 
            [
                'city' => $row['city'],
                'total_listings' => $row['total_listings'],
            ];
        }  

      return $listingla;
    }

            // $listingc = $PayRentalDB-> listingc();
    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function listingc() {

        $query = "SELECT city, sum(number_of_listings) AS total_listings FROM listingsbyareachicago WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_listings) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $listingc = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $listingc[] = 
            [
                'city' => $row['city'],
                'total_listings' => $row['total_listings'],
            ];
        }  

      return $listingc;
    }
            // $hostny = $PayRentalDB-> hostny();
    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function hostny() {

        $query = "SELECT city, sum(number_of_hosts) AS total_hosts FROM hostsbyareany WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_hosts) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $hostny = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $hostny[] = 
            [
                'city' => $row['city'],
                'total_hosts' => $row['total_hosts'],
            ];
        }  

      return $hostny;
    }
            // $hostla = $PayRentalDB-> hostla();
     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function hostla() {

        $query = "SELECT city, sum(number_of_hosts) AS total_hosts FROM hostsbyareala WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_hosts) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $hostla = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $hostla[] = 
            [
                'city' => $row['city'],
                'total_hosts' => $row['total_hosts'],
            ];
        }  

      return $hostla;
    }
            // $hostc= $PayRentalDB-> hostc();
      /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function hostc() {

        $query = "SELECT city, sum(number_of_hosts) AS total_hosts FROM hostsbyareachicago WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_hosts) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $hostc = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $hostc[] = 
            [
                'city' => $row['city'],
                'total_hosts' => $row['total_hosts'],
            ];
        }  

      return $hostc;
    }
            // $reviewny= $PayRentalDB-> reviewny();
      /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function reviewny() {

        $query = "SELECT city, sum(number_of_reviews) AS total_reviews FROM reviewsbyareany WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_reviews) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $reviewny = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $reviewny[] = 
            [
                'city' => $row['city'],
                'total_reviews' => $row['total_reviews'],
            ];
        }  

      return $reviewny;
    }
            // $reviewla= $PayRentalDB-> reviewla();
       /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function reviewla() {

        $query = "SELECT city, sum(number_of_reviews) AS total_reviews FROM reviewsbyareala WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_reviews) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $reviewla = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $reviewla[] = 
            [
                'city' => $row['city'],
                'total_reviews' => $row['total_reviews'],
            ];
        }  

      return $reviewla;
    }
            // $reviewc= $PayRentalDB-> reviewc();
        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function reviewc() {

        $query = "SELECT city, sum(number_of_reviews) AS total_reviews FROM reviewsbyareachicago WHERE city IS NOT NULL GROUP BY city ORDER BY sum(number_of_reviews) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $reviewc = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $reviewc[] = 
            [
                'city' => $row['city'],
                'total_reviews' => $row['total_reviews'],
            ];
        }  

      return $reviewc;
    }
            // $avpriceny= $PayRentalDB-> avpriceny();
        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function avpriceny() {

        $query = "SELECT city, sum(average_price) AS average_price FROM avgpricebyareany WHERE city IS NOT NULL GROUP BY city ORDER BY sum(average_price) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $avpriceny = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $avpriceny[] = 
            [
                'city' => $row['city'],
                'average_price' => $row['average_price'],
            ];
        }  

      return $avpriceny;
    }
            // $avpricela= $PayRentalDB-> avpricela();
        /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function avpricela() {

        $query = "SELECT city, sum(average_price) AS average_price FROM avgpricebyareala WHERE city IS NOT NULL GROUP BY city ORDER BY sum(average_price) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $avpricela = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $avpricela[] = 
            [
                'city' => $row['city'],
                'average_price' => $row['average_price'],
            ];
        }  

      return $avpricela;
    }
            // $avpricec= $PayRentalDB-> avpricec();
     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function avpricec() {

        $query = "SELECT city, sum(average_price) AS average_price FROM avgpricebyareachicago WHERE city IS NOT NULL GROUP BY city ORDER BY sum(average_price) desc LIMIT 3";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $avpricec = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $avpricec[] = 
            [
                'city' => $row['city'],
                'average_price' => $row['average_price'],
            ];
        }  

      return $avpricec;
    }


             /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function roomtype() {

        $query = "SELECT room_type, count(*) as type_of_room FROM payrental GROUP BY room_type ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $roomtype = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $roomtype[] = 
            [
                'room_type' => $row['room_type'],
                'number_of_rooms' => $row['type_of_room'],
            ];
        }  

      return $roomtype;
    }

         /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function roomny() {

        $query = "SELECT room_type, count(*) as type_of_room FROM payrental WHERE city_data = 'New York' GROUP BY room_type ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $roomny = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $roomny[] = 
            [
                'room_type' => $row['room_type'],
                'number_of_rooms' => $row['type_of_room'],
            ];
        }  

      return $roomny;
    }

             /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function roomla() {

        $query = "SELECT room_type, count(*) as type_of_room FROM payrental WHERE city_data = 'Los Angeles' GROUP BY room_type ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $roomla = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $roomla[] = 
            [
                'room_type' => $row['room_type'],
                'number_of_rooms' => $row['type_of_room'],
            ];
        }  

      return $roomla;
    }

             /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function roomc() {

        $query = "SELECT room_type, count(*) as type_of_room FROM payrental WHERE city_data = 'Chicago' GROUP BY room_type ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $roomc = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $roomc[] = 
            [
                'room_type' => $row['room_type'],
                'number_of_rooms' => $row['type_of_room'],
            ];
        }  

      return $roomc;
    }

             /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function propertytype() {

        $query = "SELECT property_type, count(*) as type_of_property FROM payrental GROUP BY property_type ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $propertytype = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $propertytype[] = 
            [
                'property_type' => $row['property_type'],
                'type_of_property' => $row['type_of_property'],
            ];
        }  

      return $propertytype;
    }

             /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function propertyny() {

        $query = "SELECT property_type, count(*) as type_of_property FROM payrental WHERE city_data = 'New York' GROUP BY property_type ORDER BY count(*) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $propertyny = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $propertyny[] = 
            [
                'property_type' => $row['property_type'],
                'type_of_property' => $row['type_of_property'],
            ];
        }  

      return $propertyny;
    }

                 /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function propertyla() {

        $query = "SELECT property_type, count(*) as type_of_property FROM payrental WHERE city_data = 'Los Angeles'  GROUP BY property_type ORDER BY count(*) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $propertyla = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $propertyla[] = 
            [
                'property_type' => $row['property_type'],
                'type_of_property' => $row['type_of_property'],
            ];
        }  

      return $propertyla;
    }

                 /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function propertyc() {

        $query = "SELECT property_type, count(*) as type_of_property FROM payrental WHERE city_data = 'Chicago'  GROUP BY property_type ORDER BY count(*) desc LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $propertyc = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $propertyc[] = 
            [
                'property_type' => $row['property_type'],
                'type_of_property' => $row['type_of_property'],
            ];
        }  

      return $propertyc;
    }




             /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function numreviewsbyyear() {

        $query = "SELECT extract(year from date) as year, count(*) as num_reviews FROM reviews GROUP BY extract(year from date) ORDER BY count(*) desc";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $numreviewsbyyear = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $numreviewsbyyear[] = 
            [
                'year' => $row['year'],
                'number_of_reviews' => $row['num_reviews'],
            ];
        }  

      return $numreviewsbyyear;
     }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function reset_password($u, $op, $np, $cp, $type) {
        // $username = $old_password = $new_password = $confirm_password = "";
        $username_err = $old_password_err = $new_password_err = $confirm_password_err = "";
        // echo "after call: ".$u;
        echo "type: ".$type;
        if (strcmp($type,"user") == 0) 
        {
            $query = "SELECT id, username, password FROM users WHERE username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'password' => $row['password']
                ];
            }

            echo "count(): ".count($stocks);
            if (count($stocks) == 0) {
                $username_err = "No account found with that username.";
            }

            if (!(strcmp($op, $stocks[0]["password"]) == 0)) {
                $old_password_err = "The old password you entered was not valid.";
            }

            if (empty($new_password_err) && empty($confirm_password_err) && empty($old_password_err) && empty($username_err)) {
                $query = "UPDATE users SET password = :new_password WHERE id = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindValue(':new_password', $np);
                $stmt->bindValue(':id', $stocks[0]['id']);
                $stmt->execute();
                echo "update query done";
            }

            return array($username_err, $old_password_err, $new_password_err, $confirm_password_err);
        } else
        {
            $query = "SELECT host_id, host_username, password FROM hosts WHERE host_username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['host_id'],
                    'username' => $row['host_username'],
                    'password' => $row['password']
                ];
            }

            echo "count(): ".count($stocks);
            if (count($stocks) == 0) {
                $username_err = "No account found with that username.";
            }

            echo "compare: ".$op.$stocks[0]["password"];
            if (!(strcmp($op, $stocks[0]["password"]) == 0)) {
                $old_password_err = "The old password you entered was not valid.";
            }

            echo " id: ".$stocks[0]['id']." ";
            if (empty($new_password_err) && empty($confirm_password_err) && empty($old_password_err) && empty($username_err)) {
                $query = "UPDATE hosts SET password = :new_password WHERE host_id = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindValue(':new_password', $np);
                $stmt->bindValue(':id', $stocks[0]['id']);
                $stmt->execute();
                echo "update query done";
            }

            return array($username_err, $old_password_err, $new_password_err, $confirm_password_err);
        }
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function my_bookings() {
        // Prepare a select statement
        // echo $user_id;
        $query = "SELECT booking_id, payrental.name, bookings.check_in_date::date, bookings.check_out_date::date, (check_out_date::date - check_in_date::date)*price as price FROM bookings, payrental, curruser WHERE bookings.user_id = curruser.user_id and bookings.property_id = payrental.id";
        $stmt = $this->pdo->prepare($query);
        // $stmt->bindValue(':userid', $user_id);
        $stmt->execute();

        $stocks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $stocks[] = [
                'booking_id' => $row['booking_id'],
                'name' => $row['name'],
                'check_in_date' => $row['check_in_date'],
                'check_out_date' => $row['check_out_date'],
                'price' => $row['price']
            ];
        }

        if (count($stocks) == 0) {
            $username_err = "No account found with that username.";
        }

        if (!(strcmp($p, $stocks[0]["password"]) == 0)) {
            $password_err = "The password you entered was not valid.";
        }
        return $stocks;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function host_my_bookings() {
        // Prepare a select statement
        // echo $user_id;
        $query = "SELECT booking_id, payrental.name, bookings.check_in_date::date, bookings.check_out_date::date, (check_out_date::date - check_in_date::date)*price as price FROM bookings, payrental, currhost WHERE bookings.host_id = currhost.user_id and bookings.property_id = payrental.id";
        $stmt = $this->pdo->prepare($query);
        // $stmt->bindValue(':userid', $user_id);
        $stmt->execute();

        $stocks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $stocks[] = [
                'booking_id' => $row['booking_id'],
                'name' => $row['name'],
                'check_in_date' => $row['check_in_date'],
                'check_out_date' => $row['check_out_date'],
                'price' => $row['price']
            ];
        }

        if (count($stocks) == 0) {
            $username_err = "No account found with that username.";
        }

        if (!(strcmp($p, $stocks[0]["password"]) == 0)) {
            $password_err = "The password you entered was not valid.";
        }
        return $stocks;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function host_my_property() {
        // Prepare a select statement
        $query = "SELECT user_id FROM currhost";
        $stmt = $this->pdo->prepare($query);
        // $stmt->bindValue(':id', $id);
        $stmt->execute();

        $host_id = "";
        $out = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $out[] = [
                'host_id' => $row['user_id']
            ];
            $host_id = $row['user_id'];
        }
        if (count($out) != 1){
            return 0;
        } 

        // $host_id = strval($host_id);

        $query = "SELECT payrental.id, property_type, room_type, cast(price as integer), payrental.city, number_of_reviews as rcount, cast(review_scores_rating as integer) as rating, picture_url FROM payrental WHERE host_id = :host_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':host_id', $host_id);
        $stmt->execute();

        $stocks = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $stocks[] = [
                'id' => $row['id'],
                'property_type' => $row['property_type'],
                'room_type' => $row['room_type'],
                'price' => $row['price'],
                'city' => $row['city'],
                'rcount' => $row['rcount'],
                'rating' => $row['rating'],
                'picture' => $row['picture_url']
            ];
        }

        return $stocks;
    }


    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function get_property_detials($id) {
        $query = "SELECT name, street, neighbourhood, city, state, zipcode, summary, property_type, room_type, accommodates, bathrooms, bedrooms, beds, amenities, square_feet, host_name, host_since, host_location, host_about, host_response_time, host_response_rate, host_acceptance_rate, host_is_superhost, host_listings_count, host_identity_verified, price, weekly_price, monthly_price, security_deposit, cleaning_fee, guests_included, extra_people, picture_url, instant_bookable, is_business_travel_ready, cancellation_policy, review_scores_rating, review_scores_cleanliness, review_scores_checkin, review_scores_communication, review_scores_location, review_scores_value FROM payrental WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $details = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $details[] = [
                'id' => $id,
                'name' => $row['name'],
                'street' => $row['street'],
                'neighbourhood' => $row['neighbourhood'],
                'city' => $row['city'],
                'state' => $row['state'],
                'zipcode' => $row['zipcode'],
                'summary' => $row['summary'],
                'property_type' => $row['property_type'],
                'room_type' => $row['room_type'],
                'accommodates' => $row['accommodates'],
                'bathrooms' => $row['bathrooms'],
                'bedrooms' => $row['bedrooms'],
                'beds' => $row['beds'],
                'amenities' => $row['amenities'],
                'square_feet' => $row['square_feet'],
                'host_name' => $row['host_name'],
                'host_since' => $row['host_since'],
                'host_location' => $row['host_location'],
                'host_about' => $row['host_about'],
                'host_response_time' => $row['host_response_time'],
                'host_response_rate' => $row['host_response_rate'],
                'host_acceptance_rate' => $row['host_acceptance_rate'],
                'host_is_superhost' => $row['host_is_superhost'],
                'host_listings_count' => $row['host_listings_count'],
                'host_identity_verified' => $row['host_identity_verified'],
                'price' => $row['price'],
                'weekly_price' => $row['weekly_price'],
                'monthly_price' => $row['monthly_price'],
                'security_deposit' => $row['security_deposit'],
                'cleaning_fee' => $row['cleaning_fee'],
                'guests_included' => $row['guests_included'],
                'extra_people' => $row['extra_people'],
                'picture' => $row['picture_url'],
                'instant_bookable' => $row['instant_bookable'],
                'is_business_travel_ready' => $row['is_business_travel_ready'],
                'cancellation_policy' => $row['cancellation_policy'],
                'review_scores_rating' => $row['review_scores_rating'],
                'review_scores_cleanliness' => $row['review_scores_cleanliness'],
                'review_scores_checkin' => $row['review_scores_checkin'],
                'review_scores_communication' => $row['review_scores_communication'],
                'review_scores_location' => $row['review_scores_location'],
                'review_scores_value' => $row['review_scores_value'],
            ];
        }
        return $details;
    }

     /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function logout() {
        // Prepare a select statement
        // echo $user_id;
        $query = "DELETE from curruser";
        $stmt = $this->pdo->prepare($query);
        // $stmt->bindValue(':userid', $user_id);
        $stmt->execute();
        $query = "DELETE from currhost";
        $stmt = $this->pdo->prepare($query);
        // $stmt->bindValue(':userid', $user_id);
        $stmt->execute();
    }
    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function get_cal_values($id) {
        $query = "SELECT listing_id, date, available, price FROM calender WHERE listing_id = :id ORDER BY date";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $cal_values = [];
        $available_list = [];
        $price_list = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $cal_values[] = [
                'id' => $row['listing_id'],
                'date' => $row['date'],
                'available' => $row['available'],
                'price' => $row['price'],
            ];
            $available_list[] = $row['available'];
            $price_list[] = $row['price'];
        }
        return array($cal_values,$available_list,$price_list);
    }

    /**
     * Find stock by id
     * @param int $id
     * @return a stock object
     */
    public function confirm_booking($id,$ci_date,$co_date,$total_price) {
        $query = "SELECT host_id FROM payrental WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $host_id = 0;
        $out = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $out[] = [
                'host_id' => $row['host_id']
            ];
            $host_id = $row['host_id'];
        }
        echo "host_id count: ".count($out)."\n";
        echo "host_id: ".$host_id;
        if (count($out) != 1){
            return 0;
        } 

        $query = "SELECT user_id FROM curruser";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $user_id = 0;
        $out = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $out[] = [
                'user_id' => $row['user_id']
            ];
            $user_id = $row['user_id'];
        }
        echo "user_id: ".$user_id;
        if (count($out) != 1){
            return 0;
        } 

        $ci_date .= " 00:00:00";
        $co_date .= " 00:00:00";

        $query = "INSERT INTO bookings (booking_id, property_id, host_id, user_id, check_in_date, check_out_date) VALUES (nextval('bookings_booking_id_seq'), :property_id, :host_id, :user_id, :ci_date::timestamp, :co_date::timestamp)";
        echo "insert query:".$query;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':property_id', $id);
        $stmt->bindValue(':host_id', $host_id);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':ci_date', $ci_date);
        $stmt->bindValue(':co_date', $co_date);
        $stmt->execute();

        return 1;
    }
    

    // /**
    //  * Find stock by id
    //  * @param int $id
    //  * @return a stock object
    //  */
    // public function find_name() {
    //     $query = "SELECT count(*) from curruser";
    //     $stmt = $this->pdo->prepare($query);
    //     // $stmt->bindValue(':id', $id);
    //     $stmt->execute();
    //     $name = [];
    //     while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    //         $name[] = $row['count'];
    //     }
    //     if($name[0][0]==1)
    //     {
    //         echo 
    //     }
    //     return array($cal_values,$available_list,$price_list);
    // }

}

 
