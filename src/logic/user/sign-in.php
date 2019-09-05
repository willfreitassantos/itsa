<?php
require_once '../../../vendor/autoload.php';

use ITSA\Model\User;

session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	if(isset($_POST['login']) && isset($_POST['passwd'])) {
		$user = new User();
		$user->setLogin($_POST['login']);
		$user->setPasswd($_POST['passwd']);

		try {
			$user = $user->login();
			$_SESSION['logged_user'] = serialize($user);
			header('Location: ../orders/new');
			die();
		} catch (Exception $e) {
			//login failed
		}
	}
}

$_SESSION['login_failed'] = 'Invalid credentials';
header('Location: ../login');
die();
