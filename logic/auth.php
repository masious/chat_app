<?php

class Auth
{
	private static $_user;
	
	public function __construct()
	{
		self::_session_start();
	}
	
	// logic for logging in a user
	public function login()
	{
		if(!isset($_POST['username']) || !isset($_POST['password']))
			die( 'values are not set.');
			
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		self::$_user = User::checkPassword($username, $password);
		if(!self::$_user)
			die('wrong information');
		else{
			$_SESSION['user'] = self::$_user;
			$_SESSION['hasLoggedIn'] = true;
			header('Location: /dashboard');
		}
	}
	
	// logic for signing up a user
	public function signup()
	{
		// checking values to see if they are actually set
		if(!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['passwordConfirmation']))
			die( 'values are not set.');
			
		// checking password confirmation
		if($_POST['passwordConfirmation'] != $_POST['password'])
			die('password and its confirmation are not the same.');
		
		// if username is duplicated
		if(User::findByUsername($_POST['username'])->id)
			die('This username is already used.');
			
		// if email is duplicated
		if(User::findByEmail($_POST['email'])->id)
			die('This email is already used.');
			
		self::$_user = User::make($_POST);
		
		die('submit completed!');
	}
	
	// returns the signed up/logged in user
	public function user()
	{
		self::_session_start();
		return (isset(self::$_user)) ? self::$_user : (self::$_user = $_SESSION['user']);
	}
	
	public static function hasLoggedIn()
	{
		self::_session_start();
		return isset($_SESSION['hasLoggedIn']) && $_SESSION['hasLoggedIn'];
	}
	
	private static function _session_start(){
		if (session_status() == PHP_SESSION_NONE)
			session_start();
	}
}