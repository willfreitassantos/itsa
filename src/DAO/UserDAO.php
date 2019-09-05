<?php
namespace ITSA\DAO;

use ITSA\DAO\ConnectionManager;
use ITSA\Model\User;
use Exception;

abstract class UserDAO
{

	public static function isCredentialsValid(User $user)
	{
		$query = "SELECT user_id, store_id, name, admin FROM users WHERE login = :login AND passwd = :passwd";
		try {
			$conn = ConnectionManager::getConnection();
			$stmt = $conn->prepare($query);
			$stmt->bindValue(':login', $user->getLogin());
			$stmt->bindValue(':passwd', $user->getPasswd());
			$stmt->execute();
			$resultset = $stmt->fetch();
			if($resultset) {
				$user->setId($resultset['user_id']);
				$user->setStoreId($resultset['store_id']);
				$user->setName($resultset['name']);
				$user->setAdmin($resultset['admin']);
				return $user;
			} else {
				throw new Exception("Invalid credentials!");
			}
		} catch (Exception $ex) {
			throw $ex;
		} finally {
			$conn = null;
		}
	}
}
