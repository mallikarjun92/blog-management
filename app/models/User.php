<?php
	
	namespace App\Models;
	
	use Core\Model;
	
	class User extends Model
	{
		
		const ROLE_ADMIN = 'admin';
		const ROLE_USER  = 'user';
		
		protected $table = "users";
		
		public function __construct()
		{
			parent::__construct($this->table);
		}
		
		/**
		 * @param $username
		 * @return mixed
		 */
		public function findUserByUsername($username)
		{
			$query = "SELECT * FROM users WHERE username = :username";
			$stmt  = $this->db->prepare($query);
			$stmt->bindParam(':username', $username);
			$stmt->execute();
			
			return $stmt->fetch();
		}
		
		/**
		 * @param $userId
		 * @return bool
		 */
		public function isAdmin($userId)
		{
			$query = "SELECT role FROM users WHERE id = :id";
			$stmt  = $this->db->prepare($query);
			$stmt->bindParam(':id', $userId);
			$stmt->execute();
			
			$user = $stmt->fetch();
			return $user && $user['role'] === self::ROLE_ADMIN;
		}
		
		/**
		 *
		 * @param $userId
		 * @return bool
		 */
		public function isUser($userId)
		{
			$query = "SELECT role FROM users WHERE id = :id";
			$stmt  = $this->db->prepare($query);
			$stmt->bindParam(':id', $userId);
			$stmt->execute();
			
			$user = $stmt->fetch();
			return $user && $user['role'] === self::ROLE_USER;
		}
		
		/**
		 * @param $email
		 * @return mixed
		 */
		public function getByEmail($email)
		{
			$query = "SELECT * FROM users WHERE email = :email";
			$stmt  = $this->db->prepare($query);
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			return $stmt->fetch();
		}
		
		/**
		 * Check if the username already exists
		 *
		 * @param $username
		 * @return bool
		 */
		public function usernameExists($username)
		{
			$stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
			$stmt->bindParam(':username', $username);
			$stmt->execute();
			
			return $stmt->fetchColumn() > 0;
		}
		
		/**
		 * Check if the email already exists
		 *
		 * @param $email
		 * @return bool
		 */
		public function emailExists($email)
		{
			$stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			
			return $stmt->fetchColumn() > 0;
		}
		
		public function getUsersByRole($role = self::ROLE_USER): array|false
		{
			$sql  = "SELECT * FROM WHERE role = :role " . $this->table;
			$stmt = $this->db->prepare($sql);
			
			$stmt->bindParam(':role', $role);
			$stmt->execute();
			
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		
	}
