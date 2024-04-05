<?php

namespace App\Controller;
use App\Controller\BaseController;

class DialogController extends BaseController
{
    
	/**
	 * show a html dialog with a message
	 * @param string text - the message 
	*/
	public function showDialog($text): string 
	{
		$id = $this->randomPassword(10);
		$dialog = "<dialog open id=\"{$id}\"><p>{$text}</p><button class=\"closeButton\" autofocus>Close</button></dialog>";
		
		return $dialog;
	}

	/**
	 * generating random characters
	 * @param int $length character's length
	 * @return string the random characther chain 
	 */
	function randomPassword($length): string 
	{  
		$possibleCharacters =  "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";  
		$characterLength = strlen($possibleCharacters);  
		$seed = (double) microtime() * 1000000;  
		srand($seed);  
		$password = "";  
		for($i=1;$i<=$length;$i++){  
			$character = rand(1,$characterLength);  
			$character = substr($possibleCharacters,$character, 1);  
			$password .= $character;  
		}  
		return $password;  
	}
}
