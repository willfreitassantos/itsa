<?php
namespace ITSA\DAO;

use PDO;

class ConnectionManager
{

	public static function getConnection()
	{
		date_default_timezone_set('America/Sao_Paulo');
		//$conexao = new PDO(DB_DRIVER . ':host=' . DB_HOSTNAME . ';dbname=' . DB_DATABASE, DB_USERNAME, DB_PASSWORD);

		$conexao = new PDO('mysql:host=127.0.0.1;dbname=itsa', 'root', ''); //Development

	//		$conexao = new PDO('mysql:host=127.0.0.1;dbname=itsa2', 'root', 'root'); //Development

		//$conexao = new PDO('mysql:host=localhost;dbname=id5195797_itsa', 'id5195797_itsa', '4lv4r0!((#'); //Testing
		//$conexao = new PDO('mysql:host=127.0.0.1;dbname=willia37_itsa', 'user', 'pass'); 
//Production
		$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conexao;
	}
}
