<?php

class Court {

	private $courtID;
	private $programID;
	private $defendantID;
	public $courtDate;
	public $type;
	public $contractSigned;
	public $closed;
	public $courtLocationID;
	public $timeEntered;
	
	public function __construct( $user_programID )
	{
		$this->courtID = 0;
		$this->programID = $user_programID;
		$this->courtDate = NULL;
		$this->type = NULL;
		$this->contractSigned = NULL;
		$this->closed = NULL;
		$this->courtLocationID = NULL;
		$this->timeEntered = NULL;
	}
	
	/*************************************************************************************************
		function: updateCourt
		purpose: adds court if courtID is 0, otherwise updates court
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function updateCourt()
	{
			
		// add new defendant or update existing record
		if( $this->courtID == 0 ) {
			$sql = "INSERT INTO court (programID, defendantID, courtLocationID, type, contract, date, closed)
							VALUES (:programID, :defendantID, :courtLocationID, :type, :contract, :date, :closed )";
		} else {
			$sql = "UPDATE court SET programID = :programID, defendantID = :defendantID, courtLocationID = :courtLocationID, 
							type = :type,	contract = :contract, date = :date, closed = :closed
							WHERE courtID = :courtID";
		}
		
		// database connection and sql query			
		$core = Core::dbOpen();
		$stmt = $core->dbh->prepare($sql);
		if( $this->courtID > 0 ) { $stmt->bindParam(':courtID', $this->courtID); }
		$stmt->bindParam(':programID', $this->programID);
		$stmt->bindParam(':defendantID', $this->defendantID);
		$stmt->bindParam(':courtLocationID', $this->courtLocationID);
		$stmt->bindParam(':type', $this->type);
		$stmt->bindParam(':contract', $this->contractSigned);
		$stmt->bindParam(':date', $core->convertToServerDate( $this->courtDate, $_SESSION["timezone"] ));
		$stmt->bindParam(':closed', $this->closed);
		Core::dbClose();
				
		try
		{
			if( $stmt->execute()) {
				// if it's a new defendant, get the last insertId
				if( $this->courtID == 0 )
					$this->courtID = $core->dbh->lastInsertId(); 
				return true;
			}
		} catch ( PDOException $e ) {
			echo "Update Court information failed!";
		}
		return false;			
	}
	
	/*************************************************************************************************
		function: getFromID
		purpose: gets court information from courtID
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function getFromID( $id )
	{
		 // database connection and sql query
    $sql = "SELECT *, UNIX_TIMESTAMP( date ) AS date, UNIX_TIMESTAMP( closed ) AS closed
						FROM court 
						WHERE courtID = :courtID";
    $core = Core::dbOpen();
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $id);
    Core::dbClose();
    
    try
    {
      if( $stmt->execute())
      {
        $row = $stmt->fetch();
        $this->courtID = $id;
        $this->programID = $row["programID"];
        $this->defendantID = $row["defendantID"];
				$this->courtDate =  $row["date"]; // keep as unix time for easier date seperation on form
				$this->type = $row["type"];
				$this->contractSigned = ($row["contract"] == 1) ? "Yes": "No";
				$this->closed = ( $row["closed"] ) ? date("n/j/y h:i a", $row["closed"]) : NULL;
				$this->courtLocationID =  $row["courtLocationID"];
				$this->timeEntered =  $row["timeEntered"];				
			}
		} catch ( PDOException $e ) {
      echo "Get court information failed!";
    }
    return false;
	}

  /*************************************************************************************************
    function: compareProgramID
    purpose: compares defendant's programID to user's programID to determine if it should be listed
    input: $id = defendant id
           $user_program = program id for user trying to view defendant
    output: boolean true/false
  *************************************************************************************************/
  public function compareProgramID( $id, $user_program )
  {
    // database connection and sql query
    $sql = "SELECT programID FROM court WHERE courtID = :courtID";
    $core = Core::dbOpen();
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $id);
    Core::dbClose();
    
    try
    {
      if( $stmt->execute() )
      {
        $row = $stmt->fetch();
        if( $user_program == $row["programID"] )
          return true;
      }
    } catch( PDOException $e ) {
      echo "ProgramID Compare Failed!";
    }
    return false;
  }

	/*************************************************************************************************
		function: deleteCourt
		purpose: removes everything from this court
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function deleteCourt()
	{
    $core = Core::dbOpen();

	  // database connection and sql queries		
    $sql = "DELETE FROM court_guardian WHERE courtID = :courtID";
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
		
    $sql = "DELETE FROM court_jury_defendant WHERE courtID = :courtID";
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
		
    $sql = "DELETE FROM court_jury_volunteer WHERE courtID = :courtID";
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();

    $sql = "DELETE FROM court_member WHERE courtID = :courtID";
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
		
    $sql = "DELETE FROM court WHERE courtID = :courtID";
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
		
		Core::dbClose();
	}
	
	/*************************************************************************************************
		function: getCourtMembers
		purpose: get court members for this program
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function getCourtMembers()
	{
		
		$data = array();
		
		 // database connection and sql query
    $sql1 = "SELECT cp.positionID, cp.position
						FROM court_position cp
						WHERE cp.programID = :programID AND position != 'Jury'
						ORDER BY position";
    $core = Core::dbOpen();
    $stmt1= $core->dbh->prepare($sql1);
    $stmt1->bindParam(':programID', $this->programID);
		
		try
    {
      if( $stmt1->execute())
      {
					$index = 0;
					
					// loop through each of the programs court positions
					while ($cpRow = $stmt1->fetch(PDO::FETCH_ASSOC)) {
						$row = array();
						
						// and get members for each positions to build the return array
						$sql2 = "SELECT vp.volunteerID, v.firstName, v.lastName
										FROM volunteer_position vp
										LEFT JOIN volunteer v ON v.volunteerID = vp.volunteerID
										WHERE vp.positionID = :positionID AND v.active = 1
										ORDER BY lastName, firstName";
						$stmt2 = $core->dbh->prepare($sql2);
						$stmt2->bindParam(':positionID', $cpRow["positionID"]);
						
						// write out members to the array
						if( $stmt2->execute() ) {

							while ($vRow = $stmt2->fetch(PDO::FETCH_ASSOC)) {
								$row[$vRow["volunteerID"]] = $vRow["lastName"] . ", " . $vRow["firstName"];
							}
							
							$data[$index]['id'] = $cpRow["positionID"];
							$data[$index]['position'] = $cpRow["position"];
							$data[$index]['members'] = $row;
							$index++;
						}
					}
			}
		} catch ( PDOException $e ) {
      echo "Get court members failed!";
    }
    Core::dbClose();
    return $data;
	}
	
	/*************************************************************************************************
		function: updateCourtMembers
		purpose: update all assigned court members
		input: $members = array of positionIDs as key and volunteer IDs as value
  	output: boolean true/false
	*************************************************************************************************/
	public function updateCourtMembers( $members )
	{		
		// only run if this is an existing court
		if( $this->courtID > 0 ) 
		{
			$core = Core::dbOpen();
			
			// loop through each position, checking if it exists
			foreach( $members as $posID => $volID ) 
			{				
				$sql = "SELECT volunteerID FROM court_member WHERE courtID = :courtID AND positionID = :positionID";
				$stmt = $core->dbh->prepare($sql);
				$stmt->bindParam(':courtID', $this->courtID);
				$stmt->bindParam(':positionID', $posID);
				$stmt->execute();
								
				// if a record exists, check position and update if necessary
				// if it doesn't, add it to the table				
				if(  $stmt->rowCount() > 0 )
				{
						$row = $stmt->fetch(PDO::FETCH_ASSOC);


					  // volunteer was changed, update and reset hours to null
						if( $row["volunteerID"] != $volID )
						{
							
							if( $volID == NULL ) { 
								$sql = "DELETE FROM court_member WHERE courtID = :courtID AND positionID = :positionID"; 
							}
							else { 
								$sql = "UPDATE court_member SET volunteerID = :volunteerID, hours = NULL WHERE courtID = :courtID AND positionID = :positionID";  
							}

							$stmtU = $core->dbh->prepare($sql);
							if( $volID != NULL ) { $stmtU->bindParam(':volunteerID', $volID); }
							$stmtU->bindParam(':courtID', $this->courtID);
							$stmtU->bindParam(':positionID', $posID);													
							$stmtU->execute();
						}
				}
				else // add it
				{
						$sql = "INSERT INTO court_member ( courtID, volunteerID, positionID ) 
										VALUES ( :courtID, :volunteerID, :positionID )";
						$stmtA = $core->dbh->prepare($sql);
						$stmtA->bindParam(':courtID', $this->courtID);
						$stmtA->bindParam(':volunteerID', $volID);
						$stmtA->bindParam(':positionID', $posID);
						$stmtA->execute();
				}
			}
			Core::dbClose();
			return true;
		}
		Core::dbClose();	
    return false;
	}
	
	/*************************************************************************************************
		function: existingCourtMembers
		purpose: Gets a list of exsting court members for a particular court, used to make volunteer
						 active in the court member dropdown lists
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function existingCourtMembers()
	{		
		$data = array();
		
		 // database connection and sql query
    $sql = "SELECT positionID, volunteerID FROM court_member WHERE courtID = :courtID";
    $core = Core::dbOpen();
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
    Core::dbClose();
    
    try
    {
      if( $stmt->execute())
      {
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        	$data[$row["positionID"]] = $row["volunteerID"];
				}
			}
		} catch ( PDOException $e ) {
      echo "Get existing court member array failed!";
    }
		
		return $data;
	}
	
	/*************************************************************************************************
		function: getJuryMembers
		purpose: Gets a list of exsting jury members for a particular court
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function getJuryMembers()
	{
		$data = array();
		
		 // database connection and sql query
    $sql = "( SELECT cv.volunteerID as id, 'Volunteer' as type, v.lastName, v.firstName 
						FROM court_jury_volunteer cv
						JOIN volunteer v ON v.volunteerID = cv.volunteerID
						WHERE cv.courtID = :courtID AND v.active = 1 )
						UNION
						( SELECT cd.defendantID as id, 'Defendant' as type, d.lastName, d.firstName 
						FROM court_jury_defendant cd
						JOIN defendant d ON d.defendantID= cd.defendantID
						WHERE cd.courtID = :courtID AND d.closedate is NULL )";
    $core = Core::dbOpen();
    $stmt = $core->dbh->prepare($sql);
    $stmt->bindParam(':courtID', $this->courtID);
    Core::dbClose();
    
    try
    {
      if( $stmt->execute())
      {
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					$data[] = $row;
			}
		} catch ( PDOException $e ) {
      echo "Get existing jury member array failed!";
    }
		
		return $data;
	}
	
	/*************************************************************************************************
		function: updateJuryMembers
		purpose: update all assigned jury members
		input: $members = array of jury members
  	output: none
	*************************************************************************************************/
	public function updateJuryMembers( $members )
	{		
    $core = Core::dbOpen();
		
		// insert jury member into database
		foreach( $members as $juror )
		{
			$parts = split( ":", $juror );
			
		  // database connection and sql query			
			if( $parts[1] == 'Volunteer' )
				$sql = "INSERT INTO court_jury_volunteer ( courtID, volunteerID ) VALUES ( :courtID, :jurorID )";
			else
				$sql = "INSERT INTO court_jury_defendant ( courtID, defendantID ) VALUES ( :courtID, :jurorID )";
						
			$stmt = $core->dbh->prepare($sql);
			$stmt->bindParam(':courtID', $this->courtID);
			$stmt->bindParam(':jurorID', $parts[0]);
			$stmt->execute();
		}
		
    Core::dbClose();
	}
	
	/*************************************************************************************************
		function: deleteJuryMember
		purpose: deletes a jury member from the assigned jury pool
		input: $id = assigned jury member id
           $type = string to mark if member is volunteer or defendant
  	output: boolean true/false
	*************************************************************************************************/
	public function deleteJuryMember( $id, $type )
	{		
    $core = Core::dbOpen();
		
		// database connection and sql query			
		if( $type == 'Volunteer' )
			$sql = "DELETE FROM court_jury_volunteer WHERE courtID = :courtID AND volunteerID = :jurorID";
		else
			$sql = "DELETE FROM court_jury_defendant WHERE courtID = :courtID AND defendantID = :jurorID";
						
			$stmt = $core->dbh->prepare($sql);
			$stmt->bindParam(':courtID', $this->courtID);
			$stmt->bindParam(':jurorID', $id);
    	Core::dbClose();
		
		try
		{
      if( $stmt->execute())
     		return true;
				
			print_r( $stmt->errorInfo() );
		} catch ( PDOException $e ) {
      echo "Get existing jury member array failed!";
    }
		
		return false;
	}

	/*************************************************************************************************
		function: updateCourtGuardians
		purpose: updates guardians attending a particular court
		input: $guardians = array of guardians
  	output: none
	*************************************************************************************************/
	public function updateCourtGuardians( $guardians )
	{
		$core = Core::dbOpen();
		
		// delete existing
		$sql = "DELETE FROM court_guardian WHERE courtID = :courtID";
		$stmt = $core->dbh->prepare($sql);
		$stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
				
		// insert jury member into database if attending
		foreach( $guardians as $key => $attending )
		{		
			if( $attending == "Yes" )
			{		
				$sql = "INSERT INTO court_guardian ( courtID, guardianID ) VALUES ( :courtID, :guardianID )";
				$stmt = $core->dbh->prepare($sql);
				$stmt->bindParam(':courtID', $this->courtID);
				$stmt->bindParam(':guardianID', $key);
				$stmt->execute();
			}
		}
    Core::dbClose();
	}
	
	/*************************************************************************************************
		function: checkGuardianAttending
		purpose: returns an array of guardians attending a particular court, used to check dropdowns
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function checkGuardianAttending()
	{
		$data = array();
		
		// delete existing
		$core = Core::dbOpen();		
		$sql = "SELECT guardianID FROM court_guardian WHERE courtID = :courtID";
		$stmt = $core->dbh->prepare($sql);
		$stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
    Core::dbClose();
		
		try {
      if( $stmt->execute() ) {
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					$data[] = $row["guardianID"];
			}
		} catch ( PDOException $e ) {
      echo "Get existing guardians array failed!";
    }
		
		return $data;
	}
	
	/*************************************************************************************************
		function: getMembersForTime
		purpose: returns an array of members for a particular court and their time spent
		input: none
  	output: boolean true/false
	*************************************************************************************************/
	public function getMembersForTime( $type )
	{
		$data = array();
	
		// delete existing
		$core = Core::dbOpen();	
		
		if( $type == "positions" )
		{
			$sql = "SELECT cm.volunteerID, cm.positionID, position, firstName, lastName, hours
							FROM court_member cm
							JOIN volunteer v ON v.volunteerID = cm.volunteerID
							JOIN court_position cp ON cp.positionID = cm.positionID 
							WHERE cm.courtID = :courtID
							ORDER BY position";
		}
		else if( $type == "jury" )
		{
			$sql = "( SELECT cv.volunteerID as id, 'Volunteer' as type, v.lastName, v.firstName, cv.hours
					FROM court_jury_volunteer cv
					JOIN volunteer v ON v.volunteerID = cv.volunteerID
					WHERE cv.courtID = :courtID )
					UNION
					( SELECT cd.defendantID as id, 'Defendant' as type, d.lastName, d.firstName, cd.hours
					FROM court_jury_defendant cd
					JOIN defendant d ON d.defendantID= cd.defendantID
					WHERE cd.courtID = :courtID )";
		}
		$stmt = $core->dbh->prepare($sql);
		$stmt->bindParam(':courtID', $this->courtID);
		$stmt->execute();
    Core::dbClose();
		
		try {
      if( $stmt->execute() ) {
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
					$data[] = $row;
			}
		} catch ( PDOException $e ) {
      echo "Get members for time entry failed!";
    }
		
		return $data;
	}

	/*************************************************************************************************
		function: setMembersTime
		purpose: sets time spent for members/jury on a particular court
		input: $global = time to set all participants to
		       $members = court position members
					 $jury = volunteer/defendant jurors
  	output: boolean true/false
	*************************************************************************************************/
	public function setMembersTime( $globalHrs, $members, $jury )
	{
		if( $members && $jury )
		{
			$core = Core::dbOpen();		
			
			// update court members hours
			$sql = "UPDATE court_member SET hours = :hours WHERE courtID = :courtID AND volunteerID = :volunteerID AND positionID = :positionID";
			foreach( $members as $key => $person )
			{	
				$stmt = $core->dbh->prepare($sql);
				$stmt->bindParam(':courtID', $this->courtID);
				$stmt->bindParam(':volunteerID', $person["volunteerID"]);
				$stmt->bindParam(':positionID', $person["positionID"]);
				$stmt->bindParam(':hours', $hours = ( $globalHrs > 0 ) ? $globalHrs : $person["hours"]);
				$stmt->execute();
			}
			
			// update jury hours
			foreach( $jury as $key => $juror )
			{
				if( $juror["type"] == "Volunteer" )
					$sql = "UPDATE court_jury_volunteer SET hours = :hours WHERE courtID = :courtID AND volunteerID = :id";
				else
					$sql = "UPDATE court_jury_defendant SET hours = :hours WHERE courtID = :courtID AND defendantID = :id";
				
				$stmt = $core->dbh->prepare($sql);
				$stmt->bindParam(':courtID', $this->courtID);
				$stmt->bindParam(':id', $juror["id"]);
				$stmt->bindParam(':hours', $hours = ( $globalHrs ) ? $globalHrs : $juror["hours"]);
				$stmt->execute();
			}
			
			// mark the time has been entered for this court
			$sql = "UPDATE court SET timeEntered = 1 WHERE courtID = :courtID";
			$stmt = $core->dbh->prepare($sql);
			$stmt->bindParam(':courtID', $this->courtID);
			$stmt->execute();
						
    	Core::dbClose();
			return true;
		}
		return false;
	}

	// setters
	public function setDefendantID( $val ) { $this->defendantID = $val; }
	public function setCourtID( $val ) { $this->courtID = $val; }

	// getters
	public function getDefendantID() { return $this->defendantID; }
	public function getCourtID() { return $this->courtID; }
}

?>