<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/credentials/database.php');
    
	class Database {
		
		private static $db = NULL;

		private static function Set() {
		
			Database::$db = new PDO(
				'mysql:host=' . _Database::ServerName . ';dbname=' . _Database::DBName . ';charset=utf8mb4',
				_Database::UserName,
				_Database::Password,
				array(
					PDO::MYSQL_ATTR_FOUND_ROWS => true,
				    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				    PDO::ATTR_EMULATE_PREPARES => false,
				    PDO::ATTR_STRINGIFY_FETCHES => false
				)
			);
		}

		public static function Get() {
			if (Database::$db === NULL) {
				Database::Set();
			}

			return Database::$db;
		}
	}

?>