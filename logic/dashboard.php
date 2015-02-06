<?php

class Dashboard
{
	public function __construct(){
		if(!Auth::hasLoggedIn())
			die('you\'re not logged in.');
	}
	
	public function main()
	{
		global $html;
		$users = User::all(Auth::user()->id);
		$html->render('dashboard',['users'=>$users]);
	}
	
	public function send()
	{
		$sender_id = Auth::user()->id;
		$receiver_id = (int) $_POST['receiver_id'];
		$body = $_POST['body'];
		
		global $sql;
		$q = $sql->prepare('INSERT INTO messages (sender_id,receiver_id,body) VALUES (?,?,?)');
		$q->bindValue(1,$sender_id);
		$q->bindValue(2,$receiver_id);
		$q->bindValue(3,$body);
		$q->execute();
		
		if($sql->lastInsertId()){
			$response['status'] = 'success';
		}
		echo json_encode($response);
		
	}
	
	public function get_messages()
	{
		$timestamp = $_POST['from'];
		
		global $sql;
		if($timestamp)
			$q = $sql->prepare('SELECT * FROM messages WHERE (receiver_id=? OR sender_id=?) AND date > ? ORDER BY date');
		else // timestamp = 0
			$q = $sql->prepare('SELECT * FROM messages WHERE (receiver_id=? OR sender_id=?) AND date > ? ORDER BY date LIMIT 20');
			
		$q->bindValue(1,Auth::user()->id);
		$q->bindValue(2,Auth::user()->id);
		$q->bindValue(3,$timestamp);//echo $timestamp;
		$q->execute();
		
		echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
	}
}