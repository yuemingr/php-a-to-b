<?php
class MyPDO{
	private static $pdo;
	
	private function __contruct(){
		//
	}
	private function __clone(){
		//
	}
	public static function getInstance($dbConf){
		if(!(self::$pdo instanceof PDO)){
			$dsn = "mysql:host=" . $dbConf['host'] . ";port=" . $dbConf['port'] . ";dbname=" . $dbConf['dbName'] . ";charset=" . $dbConf['charSet'];
			try {
				self::$pdo = new PDO($dsn, $dbConf['user'], $dbConf['passwd'], array(PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
			    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);	
			} catch(Exception $e) {
				print "Error:" . $e->getMessage(). "<br/>";
				die();
			}
		}
		return self::$pdo;
	}
	
}