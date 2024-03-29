<?php
class Core {
	// database connection info
	private $db_host = 'localhost';
	private $db_name = 'teencour_data';
	private $db_user = 'teencour_web';
	private $db_password = 't33nc0urtw3b12';

	// database handler
    public $dbh;
    private static $instance;

	// construct the core class
    private function __construct() {
    	// building data source name
    	$dsn = 'mysql:host=' . $this->db_host . ';dbname=' . $this->db_name;
		// open the database
		try {
			$this->dbh = new PDO($dsn, $this->db_user, $this->db_password);
		} catch (PDOException $e) {
			print "Error: " . $e->getMessage() . "<br>";
			die();
		}
	}
	 
	// create an instance to open db communication
    public static function dbOpen() {
        if (!isset(self::$instance)) {
          $object = __CLASS__;
        	self::$instance = new $object;
        }
    	return self::$instance;
    }

	// close the db connection
    public static function dbClose() {
		if (!isset(self::$instance)) {
			$this->dbh = null;
		}
	}
	
	public function convertDate( $dateToConvert ) {
		
		date_default_timezone_set('America/Denver');

		if( $dateToConvert ) {
			// build date display
			if( strpos( $dateToConvert, "/") == 0 ) {
				return date("m/d/Y h:i a", $dateToConvert);
			}
			else {
				// build the unix timestamp
				$dateObj = DateTime::createFromFormat('m/d/Y h:i a', $dateToConvert);
				return $dateObj->format('U');
			}
		}
	}
}

?>