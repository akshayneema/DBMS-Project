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
    public function findByPK($ptype, $rtype, $city, $state, $dist, $price, $sortby) {
        // prepare SELECT statement
        $query = "SELECT payrental.id, property_type, room_type, price, payrental.city, payrental.state, distance(latitude::decimal, longitude::decimal, lat, lng) FROM payrental, cityinfo";
        $count = 0;
        $state_entered = false;
        $city_entered = false;
        if(!(strcmp($rtype,"All")==0 OR strcmp($rtype,"")==0))
        {
            $count=$count+1;
            if($count == 1)
            {
                $query=$query." WHERE room_type = :rtype";
            }
            else{
                $query=$query." AND room_type = :rtype";
            }
        }
        if(!(strcmp($ptype,"All")==0 OR strcmp($ptype,"")==0))
        {
            $count=$count+1;
            if($count == 1)
            {
                $query=$query." WHERE property_type = :ptype";
            }
            else{
                $query=$query." AND property_type = :ptype";
            }
        }
        if(!(strcmp($city,"All")==0 OR strcmp($city,"")==0))
        {
            $count=$count+1;
            $city_entered = true;
            if($count == 1)
            {
                $query=$query." WHERE cityinfo.city = :city";
            }
            else{
                $query=$query." AND cityinfo.city = :city";
            }
        }
        if(!(strcmp($state,"All")==0 OR strcmp($state,"")==0))
        {
            $count=$count+1;
            $state_entered = true;
            if($count == 1)
            {
                $query=$query." WHERE cityinfo.state_name = :state";
            }
            else{
                $query=$query." AND cityinfo.state_name = :state";
            }
        }
        if($count == 1)
        {
            $query=$query." WHERE payrental.price <= :price";
        }
        else{
            $query=$query." AND payrental.price <= :price";
        }
        if ($state_entered AND $city_entered)
        {
            $query=$query." AND distance(latitude::decimal, longitude::decimal, lat, lng)<:dist order by ".$sortby." LIMIT 10;";
        }
        

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
        if(!(strcmp($state,"All")==0 OR strcmp($state,"")==0))
        {
            $stmt->bindValue(':state', $state);
        }
        if ($state_entered AND $city_entered)
        {
            $stmt->bindValue(':dist', $dist);
        }
        $stmt->bindValue(':price', $price);

        
        
        // execute the statement
        if ($state_entered AND $city_entered)
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
                'state' => $row['state'],
                'distance' => $row['distance']
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
            $query = "SELECT id FROM users WHERE username = :username";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindValue(':username', $u);
            $stmt->execute();

            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['id']
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
            $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
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
            $query = "INSERT INTO hosts (host_name, host_username, password) VALUES (:name, :username, :password)";
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
            if (count($stocks) == 0) {
                $username_err = "No account found with that username.";
            }

            if (!(strcmp($p, $stocks[0]["password"]) == 0)) {
                $password_err = "The password you entered was not valid.";
            }

            
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

            if (count($stocks) == 0) {
                $username_err = "No account found with that username.";
            }

            if (!(strcmp($p, $stocks[0]["password"]) == 0)) {
                $password_err = "The password you entered was not valid.";
            }

            
        }  
        return array($username_err, $password_err);
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
}

 
