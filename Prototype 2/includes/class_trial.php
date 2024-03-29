<?php
class Trial {
	private $ID;
	private $date;
	private $time;
	private $type;
	private $location;
	private $judge;
	private $defenseAttorney;
	private $prosecutingAttorney;
	private $bailiff;
	private $courtClerk;
	private $exitInterviewer;
	private $advisor;
	private $juryMembers;
	private $contractSigned;
	private $parentPresent;
	
	public function __construct();
	
	private function setTrialStaff();
	private function setJuryPool();
	private function printTrialInformation();
	private function printJurySummons();
}
?>