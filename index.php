<?php
	ini_set('display_errors', TRUE);
	error_reporting(E_ALL);

/**
 *   @file A collection of configuration options for the website including
 *   constant deinitions.
 */

/**
 *   Database Server Definition:  This should be set to the server IP or name
 *   depending on your system.  If the server is at localhost, it can be defined
 *   as such, otherwise use the IP address.
 */
define('DB_SERVER', '127.0.0.1:3306');

/**
 *	DSN definition to be used in a PDO object.
 */
define('DB_DSN', 'mysql:host=localhost;dbname=cmsDatabase');

/**
 *   Database Name Definition:  This should be set to the name of the database to
 *   be used for the site.
 */
define('DB_DATABASE', 'cmsDatabase');

/**
 *   Database User Name Definition:  This is the user name to connect to the
 *   database with.  The user must have administrative rights to make changes
 *   to the database, including CREATE, DROP, SELECT, INSERT, DELETE.
 */
define('DB_USERNAME', 'cmsUser');

/**
 *   Database User Password Definition:  This is the password associated with
 *   the username entered in DB_USERNAME.
 */
define('DB_PASSWORD', 'a4estLak3');

/**
 *   Member Table Definition:  This is name the database table that contains
 *   the basic member information.  This table is to include the following
 *   fields:
 *       id          => INT, PK, NN, AI
 *       username    => VARCHAR(255), NN
 *       password    => VARCHAR(255), NN
 *       email       => VARCHAR(255), NN
 *       verified    => BOOLEAN, NN, Default = FALSE
 */
define('DB_MEMBER_TABLE', 'members');

/**
 *   Member Roles Table Definition:  This is the name of the table containing
 *   roles of the various members.  This table should include the following
 *   fields:
 *       id          => INT, PK, NN, AI
 *       member_id   => INT, NN
 *       role        => VARCHAR(255)
 */
define('DB_MEMBER_ROLES_TABLE', 'member_roles');

/**
 *   Member Profile Table Definition:  this is the database table that contains
 *   the information regarding member profiles.  At a minimum, it should contain
 *   the following fields:
 *       id          => INT, PK, NN, AI
 *       member_id   => INT
 *       bio         => TEXT
 */
define('DB_MEMBER_PROFILE_TABLE', 'member_profiles');

/**
 * 	Connection Class provides a singleton connection to the database for all classes
 * 	that require it.
 */
class ConnectionClass {
	/**
	 * 	A variable that will be the PDO object.
	 */
	private static $instance;
	
	/**
	 * 	Creating a private __construct will prevent a new ConnectionClass object from 
	 * 	being created.
	 */
	private function __construct() {
	}

	/**
	 * 	A static function that provides the only connection object.
	 */
	public static function getInstance() {
		if (!ConnectionClass::$instance) {
			ConnectionClass::$instance = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		}

		return ConnectionClass::$instance;
	}

}

interface IData {
 	public static function getById( $id );
	public static function getAll();
	public static function insert( $value );
	public static function update( $value );
	public static function delete( $id );
}


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

/**
 * 	The base view controller class that is to be extended by any view controller.
 */
abstract class BaseViewController {
	private $viewString;
	private $viewTemplate;
	private $node;

	private function __construct() {
	}

	public abstract function getView($node);

}

/**
 * 	The standard page view class.  Extends ViewController.
 */
class ViewController extends BaseViewController {
	public function getView($node) {
		$sql = 'SELECT * FROM nodes WHERE id=:id';
		$stmt = ConnectionClass::getInstance() -> prepare($sql);
		$stmt -> execute(array(':id' => $node));
		$row = $stmt -> fetch(PDO::FETCH_ASSOC);

		$pageText = file_get_contents($row['base_file']);

		$title = $row['title'];
		$cssName = $row['css_file'];
		$content = $row['content'];

		/**
		 * If the user is an admin, add the admin menu to the file.
		 */
		$displayText = str_replace('{pTopLogin}', file_get_contents('templates/lineStyleLoginForm.html'), $pageText);
		if (Member::getById(1)) {
			$displayText = str_replace('{pAdminMenu}', file_get_contents('templates/adminMenu.html'), $displayText);
		} else {
			$displayText = str_replace('{pAdminMenu}', '', $displayText);
		}
		$displayText = str_replace('{pAction}', 'index.php', $displayText);
		$displayText = str_replace('{pTitle}', $title, $displayText);
		$displayText = str_replace('{pCssRef}', $cssName, $displayText);
		$displayText = str_replace('{pContent}', $content, $displayText);
		$displayText .= '</br>';
		$displayText .= Member::register( array( 'username' => 'adminBill', 'email' => 'adminBill@manoutdoors.com' ));
		return $displayText;
	}

}
if ($_POST['register'] == 'register') {
	header("Location: http://localhost/~rburrell/Civitas-CMS/templates/registration.php");
}

if ($_GET['node'] == NULL) {
	echo ViewController::getView(1);
} else {
	echo ViewController::getView($_GET['node']);
}
?>

<!-- Everything Below Here is new and unverified!! -->

<?php

?>