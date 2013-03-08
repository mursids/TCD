<?php
class Workshop {
	private $workshopID;
	private $programID;
	private $date;
	private $title;
	private $description;
	private $instructor;
	private $officerID;
	
	public function __construct()
	{
		$this->workshopID = 0;
		$this->programID = 0;
		$this->date = NULL;
		$this->title = NULL;
		$this->description = NULL;
		$this->instructor = NULL;
		$this->officerID = NULL;
	}
	
	public function addWorkshop()
	{
		//open connection and build sql string
		$core = Core::dbOpen();
		$sql = "INSERT INTO workshop (programID,date,title,description,instructor,officerID)
		        VALUES (:workshopID, :date, :title, :description, :instructor, :officerID)";
		$stmt = $core->dbh->prepare($sql);
		
		//bind values
		$stmt->bindParam(':workshopID', $this->programID);
		$stmt->bindParam(':date', $this->date);
		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':instructor', $this->instructor);
		$stmt->bindParam(':officerID', $this->officerID);
		
		Core::dbClose();
		
		try
		{
			if( $stmt->execute() )
			{
				$this->programID = $core->dbh->lastInsertId(); 
				return true;
			}
		} 
		catch ( PDOException $e )
		{
			echo "Add Workshop failed!";
		}
		return false;
	}
	
	public function editWorkshop()
	{
		
	}
	
	private function deleteWorkshop()
	{
		
	}
	
	private function printWorkshopInformation()
	{
		
	}
	
	public function getWorkshop( $id )
	{
		//database connection and sql query
		$core = Core::dbOpen();
		$sql = "SELECT w.* from workshop w where w.workshopID = :id";
		$stmt = $core->dbh->prepare($sql);
		$stmt->bindParam(':id', $id);
		Core::dbClose();
		
		try
		{
			if( $stmt->execute() )
			{
				$row = $stmt->fetch();
				
				$this->workshopID = $id;
				$this->programID = $row["programID"];
				$this->date = $row["date"];
				$this->title = $row["title"];
				$this->description = $row["description"];
				$this->instructor = $row["instructor"];
				$this->officerID = $row["officerID"];
			}
		}
		catch ( PDOException $e )
		{
			echo "Get Workshop Failed!";
		}
	}
	
	//getters
	public function getWorkshopID() { return $this->workshopID; }
	public function getProgramID() { return $this->programID; }
	public function getDate() { return $this->date; }
	public function getTitle() { return $this->title; }
	public function getDescription() { return $this->description; }
	public function getInstructor() { return $this->instructor; }
	public function getOfficerID() { return $this->officerID; }
	
	//setters
	public function setWorkshopID( $val ) { $this->workshopID = $val; }
	public function setProgramID( $val ) { $this->programID = $val; }
	public function setDate( $val ) { $this->date = $val; }
	public function setTitle( $val ) { $this->title = $val; }
	public function setDescription( $val ) { $this->description = $val; }
	public function setInstructor( $val ) { $this->instructor = $val; }
	public function setOfficerID( $val ) { $this->officerID = $val; }
	
	public function display()
	{
		echo "WorkshopID: " . $this->workshopID . "<br>";
		echo "ProgramID: " . $this->programID . "<br>";
		echo "Date: " . $this->date . "<br>";
		echo "Title: " . $this->title . "<br>";
		echo "Description: " . $this->description . "<br>";
		echo "Instructor: " . $this->instructor . "<br>";
		echo "OfficerID: " . $this->officerID . "<br>";
	}
}
?>