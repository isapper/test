<?php
//Класс работы с imap
class imapClass {
	
	private $conect, $hostname;
   
    // Создается подключение imap при создании объекта
	function __construct($hostname, $username, $password, $dir = "INBOX"){
		$this->hostname = $hostname;
		@$this->conect = imap_open($hostname.$dir, $username, $password, NULL, 1, array('DISABLE_AUTHENTICATOR' => 'GSSAPI')) or die(imap_last_error());		
	}
	
	
	// Метод получения дерева деректорий
	function getDirList(){
		$list = imap_list($this->conect, $this->hostname, '*');
		foreach($list as $dir){
			$dirName = str_replace($this->hostname, '', $dir);
			$listEncod[$dirName] = str_replace('[Gmail]/', '', mb_convert_encoding($dirName, "UTF-8", "UTF7-IMAP" ));
		}
		return (isset($listEncod) && is_array($listEncod)) ? $listEncod : array();	
	}
	
	// Метод удаления письма
	function dropMesg($id){
		$ret = imap_delete($this->conect, $id);
		return ($ret);
	}
	
	// Получения списка писем
	function getMsgList($num = 0, $c = 0){
		$msgNum = imap_num_msg($this->conect) - $c;
		for($i=$msgNum; $i>$msgNum-$num && $i>0; $i--){
			$head = imap_fetchheader($this->conect, $i, 0);
			$overview = imap_rfc822_parse_headers($head, $this->hostname); 
			$dateExpl = explode(" ", $overview->date);
			$msg[++$c] = new stdClass;
			$msg[$c]->seen = isset($overview->seen) ? $overview->seen : '';
			$msg[$c]->from = isset($overview->from->personal) ? imap_utf8($overview->from->personal) : imap_utf8($overview->fromaddress);
			$msg[$c]->subject = (isset($overview->subject)) ?  imap_utf8($overview->subject) : '';
			$msg[$c]->date = isset($overview->date) ? $dateExpl[1].' '.$dateExpl[2].' '.$dateExpl[3] : '';
			$msg[$c]->dmarc = strpos($head, 'dkim=pass', 0);
			$msg[$c]->id = $i;
		}
		return (isset($msg) && is_array($msg)) ? $msg : array();
	}
	
	// Получения тела письма
	function getCurrMsg($num){
		$body = imap_fetchstructure($this->conect, $num);
		
		if(!isset($body->parts)){
			$b = $body;
			$struc = 1;
			$encoding = $body->encoding;
		}elseif(isset($body->parts[0]->parts)){
			$b = $body->parts[0]->parts[0];
			$struc = 1.1;
			$encoding = $body->parts[0]->parts[0]->encoding;
		}else{
			$b = $body->parts[1];
			$struc = 1;
			$encoding = $body->parts[0]->encoding;
		}
		
		$html = mb_convert_encoding($this->imap_encoding(imap_fetchbody($this->conect, $num , $struc), $encoding), 'UTF-8', $b->parameters[0]->value);
		return nl2br($html);
	}
	
	// Декодинг письма по номеру "encoding"
	private function imap_encoding($txtEnc, $num){
		$txtDec = '';
		switch ($num) {
			case 0:
				$txtDec = "Not supported charset <b>ENC7BIT</b><br> $txtEnc";
				break;
			case 1:
				$txtDec = "Not supported charset <b>ENC8BIT</b><br> $txtEnc";
				break;
			case 2:
				$txtDec =  $txtEnc . " <br> Not supported charset <b>ENCBINARY</b>";
				break;
			case 3:
				$txtDec = base64_decode($txtEnc);
				break;
			case 4:
				$txtDec = quoted_printable_decode($txtEnc);
				break;
			case 5:
				$txtDec = "Unknown encoding<br> $txtEnc";
				break;
		}
		return $txtDec;
	}
	
	function __destruct(){
		@imap_close($this->conect);
	}
}
?>