<?php

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
?>