<?php
session_start();

if(!isset($_SESSION['logged_user'])) {
	header('Location: ../login');
	die();
}
