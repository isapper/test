<?php
//Класс обработки входящих запросов
class routeClass {
	
	private $dbObj;
	public $htmlON;
	private $url = '{imap.gmail.com:993/imap/ssl/novalidate-cert/norsh}';
	function __construct($inData){
		
		$this->dbObj = new dbClass();
		
		// Регистрация
		if(isset($inData->mail) && isset($inData->pass)){
			$imap = new imapClass($this->url, $inData->mail , $inData->pass);
			if(is_object($imap)){
				$uID = $this->dbObj->addUser($inData->mail, $inData->pass, $inData->name);
				if($uID>0){
					$this->setConectUser($uID);
					echo 'mail.html';
				}else
					echo "Unknown error";
			}else
				echo "Unknown error";
			
		// Инициализация
		}elseif(isset($inData->load) && $inData->load == 'content'){
			$this->htmlON = 0;
			if($this->getConectUser()){
				$user = $this->dbObj->getUser($this->getConectUser());
				if(isset($user->id))
					echo  'mail.html';
				else
					echo 'aut.html?='.rand();
			}else
				echo 'aut.html?='.rand();
		
		// Авторизация
		}elseif(isset($inData->login) && (int)$inData->login > 0){
			$this->setConectUser((int)$inData->login);
			echo  'mail.html';

		// Загрузка директорий и загрузка последних письм
		}elseif(isset($inData->load) && $inData->load == 'menu'){
			$user = $this->dbObj->getUser($this->getConectUser());
			$imap = new imapClass($this->url, $user->mail , $user->pass);
			$out[] = $imap->getDirList();
			$out[] = $imap->getMsgList(15);
			echo json_encode($out);
			
		// Подгружает список письм
		}elseif(isset($inData->load) && $inData->load == 'msg' && $inData->id>=0){
			$dir = (isset($inData->dir)) ? $inData->dir : '';
			$user = $this->dbObj->getUser($this->getConectUser());
			$imap = new imapClass($this->url, $user->mail , $user->pass, $dir);
			$out[] = $imap->getMsgList(10, $inData->id);
			echo json_encode($out);
		
		// Открытие письма
		}elseif(isset($inData->load) && $inData->load == 'open' && $inData->id>=0){
			$user = $this->dbObj->getUser($this->getConectUser());
			$imap = new imapClass($this->url, $user->mail , $user->pass, $inData->dir);
			$out = $imap->getCurrMsg($inData->id);
			echo ($out);
		
		// Удаления письма
		}elseif(isset($inData->drop) && $inData->drop > 0){
			$user = $this->dbObj->getUser($this->getConectUser());
			$imap = new imapClass($this->url, $user->mail , $user->pass, $inData->dir);
			echo $imap->dropMesg($inData->drop);
		
		// Получение списка акаунтов
		}elseif(isset($inData->load) && $inData->load == "accaunts"){
			$user = $this->dbObj->getUsers();
			echo json_encode($user);
			
		// Выход
		}elseif(isset($inData->exit) && $inData->exit){
			unset($_SESSION['conect']);
			echo 'aut.html?='.rand();
		}else{
			$this->htmlON = 1;
		}
	}
	
	
	function getConectUser(){
		if(isset($_SESSION['conect']))
			return (int)$_SESSION['conect'];
		else
			return 0;
	}
	
	function setConectUser($id){
		if((int)$id>0)
			$_SESSION['conect'] = (int)$id;
	}

	
}
?>