<?php

class MySql extends PDO
{
	private $con;
	
	function __construct($server,$username,$password,$dbName)
	{
		try{
			//echo $server,' '.$username,' '.$password,' '.$dbName;
			parent::__construct('mysql:host='.$server.';dbname='.$dbName.';charset=utf8;',$username,$password);
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		} catch( PDOExeption $e){
			echo $sql . "<br><pre>" . $e->getMessage()."</pre>";
		}
	}
}