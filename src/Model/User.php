<?php
namespace ITSA\Model;

use ITSA\DAO\UserDAO;
use JsonSerializable;

class User implements JsonSerializable
{

	private $id;
	private $store_id;
	private $name;
	private $login;
	private $passwd;
	private $admin;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getStoreId()
	{
		return $this->store_id;
	}

	public function setStoreId($store_id)
	{
		$this->store_id = $store_id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getLogin()
	{
		return $this->login;
	}

	public function setLogin($login)
	{
		$this->login = $login;
	}

	public function getPasswd()
	{
		return $this->passwd;
	}

	public function setPasswd($passwd)
	{
		$this->passwd = md5($passwd);
	}

	public function isAdmin()
	{
		return $this->admin;
	}

	public function setAdmin($admin)
	{
		$this->admin = $admin;
	}

	public static function isLoggedIn()
	{
		session_start();
		return isset($_SESSION['logged_user']);
	}

	public function login()
	{
		try {
			return UserDAO::isCredentialsValid($this);
		} catch(Exception $ex) {
			throw $ex;
		}
	}

	public function jsonSerialize()
	{
        return 
        [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }
}
