<?php

class workshopLocation {
	
	private $workshopLocationID;
	private $programID;
	public $locationID;
	public $name;
	public $address;
	public $city;
	public $state;
	public $zip;
	
	public function __construct( $user_programID )
	{
		$this->workshopLocationID = 0;
		$this->programID = $user_programID;
		$this->locationID = NULL;
		$this->name = NULL;
		$this->address = NULL;
		$this->city = NULL;
		$this->state = NULL;
		$this->zip = NULL;
	}
	
	public function updateWorkshopLocation()
	{
		
		$core = Core::dbOpen();
		
		//  get workshopLocationID if exists
		$sql = "SELECT workshopLocationID, locationID FROM workshop_location 
						WHERE programID	= :programID AND name	= :name AND address = :address";
		$stmt = $core->dbh->prepare($sql);
		$stmt->bindParam(':programID', $this->programID);
		$stmt->bindParam(':name', $this->name);
		$stmt->bindParam(':address', $this->address);
		
		try {
			if ( $stmt->execute() && $stmt->RowCount() > 0 ) // location exists
			{
				$row = $stmt->fetch();
				$this->workshopLocationID = $row["workshopLocationID"];
				
				// if the current location ID differs than what is in the location, update it
				if( $this->locationID != $row["locationID"] ) {				
						$sql = "UPDATE workshop_location SET locationID = :locationID WHERE workshopLocationID = :id";
						$stmt = $core->dbh->prepare($sql);
						$stmt->bindParam(':locationID', $this->locationID);
						$stmt->bindParam(':id', $this->workshopLocationID);
						$stmt->execute();
				}
				
				return true;
			}
			else // add it
			{
				$sql = "INSERT INTO workshop_location (programID,name,address,locationID)
		    	   	  VALUES (:programID, :name, :address, :locationID)";
				$stmt = $core->dbh->prepare($sql);
				$stmt->bindParam(':programID', $this->programID);
				$stmt->bindParam(':locationID', $this->locationID);
				$stmt->bindParam(':name', $this->name);
				$stmt->bindParam(':address', $this->address);
				Core::dbClose();
				
				try {
					if ( $stmt->execute() )
						$this->workshopLocationID = $core->dbh->lastInsertId(); 		
					return true;				
				} catch ( PDOException $e ) {
					echo "Add Workshop Location Failed";
				}
			}
		} catch ( PDOException $e ) {
			echo "Get Workshop Location ID Failed";
		}
		return false;
	}

	public function getWorkshopLocation( $workshopLocID )
	{
		$core = Core::dbOpen();
		$sql = "SELECT wl.name, wl.address, l.city, l.state, l.zip, l.locationID FROM workshop_location wl
		        JOIN program_locations l ON wl.workshopLocationID = :workshopLocID AND wl.locationID = l.locationID";
		$stmt = $core->dbh->prepare($sql);
		$stmt->bindParam(':workshopLocID', $workshopLocID );
		
		try{
			if($stmt->execute()) {
					$row = $stmt->fetch();
					
					$this->workshopLocationID = $workshopLocID;
					$this->name = $row["name"];
					$this->address = $row["address"];
					$this->city = $row["city"];
					$this->state = $row["state"];
					$this->zip = $row["zip"];
					$this->locationID = $row["locationID"];
			}
		} catch ( PDOException $e ) {
			echo "Get Workshop Location Failed!";
		}
	}
	
	//getters
	public function getWorkshopLocationID() { return $this->workshopLocationID; }
	public function getProgramID() { return $this->programID; }
	
	//settters
	public function setWorkshopLocationID( $val ) { $this->workshopLocationID = $val; }
	public function setProgramID( $val ) { $this->programID = $val; }
}

?>