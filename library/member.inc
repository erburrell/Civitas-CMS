<?php
include_once('IData.inc');

class Member implements IData {
	private $id;
	private $username;
	private $verified;
	private $roles = array();
	private $shareStates = array();
	private $memberFields = array('id' => 'int', 
								  'username' => 'string', 
								  'emailactivated' => 'tinyint', 
								  'passwod' => 'string', 
								  'email' => 'string' );
	

	private function __construct( $id, $username, $verified, $roles, $shareStates ) {
		$this->id = $id;
		$this->username = $username;
		$this->verified = $verified;
		$this->roles = $roles;
		$this->shareStates = $shareStates;
	}

	public static function getById( $id ) {
		$sql = 'SELECT * FROM members WHERE id=:id';
		$stmt = ConnectionClass::getInstance()->prepare($sql);
		$stmt->execute(array( ':id' => $id ));

		$memberRow = $stmt->fetch(PDO::FETCH_ASSOC);
		
		$roleSql = 'SELECT role FROM member_roles WHERE member_id=:memberId';
		$roleStmt = ConnectionClass::getInstance()->prepare($roleSql);
		$roleStmt->execute(array(':memberId' => $id));
		
		$memberRoles = $roleStmt->fetchAll(PDO::FETCH_ASSOC);
		
		$testUserArray['1'] = 'user';
		$testShareArray['1'] = '0';
		
		return new Member( $memberRow['id'], 
						   $memberRow['username'], 
						   $memberRow['emailactivated'],  
						   $memberRoles, 
						   $testShareArray );
	}

	public static function getAll() {

	}

	public static function insert( $value ) {

	}

	public static function update( $value ) {

	}

	public static function delete( $id ) {

	}
	
	public function __ToString() {
		return $this->username;
	}
	
	public static function register( $paramArray ) {
		$sql = 'SELECT id FROM members WHERE username=:username OR email=:email';
		$checkUser = ConnectionClass::getInstance()->prepare($sql);
		$success = $checkUser->execute(array(':username' => $paramArray['username'], ':email' => $paramArray['email']));
		
		if ($checkUser->rowCount() > 0) {
			return $checkUser->rowCount();
		} else {
			return 'new user';
		}
		
	}
}
?>