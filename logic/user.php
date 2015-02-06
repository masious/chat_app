<?php

class User
{
	public function __construct($arr)
	{
		foreach(self::_getUserParams($arr) as $key=>$val)
			$this->$key = $val;
	}
	
	public static function all($exception = 0)
	{
		global $sql;
		$q = $sql->prepare('SELECT id,username FROM users WHERE id!=?');
		$q->bindValue(1,$exception);
		$q->execute();
		$res = $q->fetchAll(PDO::FETCH_ASSOC);
		
		foreach($res as $r)
			$users[] = new self($r);
			
		return $users;
	}
	
	// returns a user by its ID
	public static function findById($id)
	{
	
	}
	
	// returns userObject/false by checking username/email and password
	public static function checkPassword($usernameOrMail, $password)
	{
		global $sql;
		$q = $sql->prepare('SELECT * FROM users WHERE username=? OR password=?');
		$q->bindValue(1,$usernameOrMail);
		$q->bindValue(2,$usernameOrMail);

		$q->execute();
		$res = $q->fetch(PDO::FETCH_ASSOC);
		
		if($res['password'] == User::_hashPassword($password))
			return new self($res);
		else
			return false;
	}
	
	public static function findByUsername($username)
	{
		global $sql;
		$q = $sql->prepare('SELECT id FROM users WHERE username=?');
		$q->bindValue(1,$username);
		$q->execute();
		return new self($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public static function findByEmail($email)
	{
		global $sql;
		$q = $sql->prepare('SELECT id FROM users WHERE email=?');
		$q->bindValue(1,$email);
		$q->execute();
		return new self($q->fetch(PDO::FETCH_ASSOC));
	}
	
	public static function make($arr)
	{
		extract(self::_getUserParams($arr));
		$password = self::_hashPassword($arr['password']);

		global $sql;
		$q = $sql->prepare('INSERT INTO users(username,email,password) VALUES (?,?,?)');
		$q->bindValue(1,$username);
		$q->bindValue(2,$email);
		$q->bindValue(3,$password);
		$q->execute();
		return new self(['id'=>$sql->lastInsertId(),'username'=>$username,'email'=>$email]);
	}
	
	private static function _hashPassword($password)
	{
		return md5('~`\|]'.$password);
	}
	
	private static function _getUserParams($arr)
	{
		$result = [];
		return getByIndexes($arr,['id','username','email']);
	}
}