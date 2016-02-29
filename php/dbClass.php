<?php
class dbClass {
	const USER = 'root';
	const PASS = 'ZazA410';

	private $db;
	  
	function __construct(){
		try{ 
			$this->db = new PDO('mysql:host=localhost;dbname=mailapp', self::USER, self::PASS);
		}catch (PDOException $e){
			die ('DB Error');
		}
	}
	
	function getUser($id){
		$STH = $this->db->prepare('SELECT id, name, mail, pass FROM users WHERE id=?');
		$STH->execute(array((int)$id));
		return (object)$STH -> fetch(PDO::FETCH_ASSOC);
	}
	
	function addUser($mail, $pass, $name="New"){
		$STH = $this->db->prepare('INSERT INTO users(mail, pass, name) VALUES (?,?,?)');
		$STH->execute(array($mail, $pass, $name));
		return $this->db->lastInsertId();
	}
	
	function getUsers(){
		$STH = $this->db->prepare('SELECT id, name, mail FROM users');
		$STH->execute();
		while($row = $STH->fetch(PDO::FETCH_ASSOC)) { 
			$users[] = $row;
		}
		return (isset($users)) ? $users : false;
	}
}
?>