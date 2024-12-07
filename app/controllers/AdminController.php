<?php
	
	namespace App\Controllers;
	
	use App\Models\User;
	use Core\BaseController;
	
	class AdminController extends BaseController
	{
		
		public function login()
		{
			
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				
				$userModel = new User();
				$username  = $_POST['username'];
				$password  = $_POST['password'];
				
				$user = $userModel->findUserByUsername($username);
				
				if ($user && password_verify($password, $user['password']) && $user['role'] === 'admin') {
					$_SESSION['admin'] = $user['id'];
					header("Location: /admin/dashboard");
					exit;
				} else {
					$this->render('auth/login.html', ['error' => 'Invalid credentials or unauthorized access', 'title' => 'Login']);
				}
				
			} else {
				$this->render('auth/login.html', ['title' => 'Login']);
			}
		}
		
		public function logout()
		{
			session_destroy();
			header("Location: /admin/login");
			exit;
		}
		
		public function register()
		{
			
			// Check if the form is submitted via POST
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				// Sanitize inputs
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				$password = isset($_POST['password']) ? $_POST['password'] : '';
				$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
				
				// Validate inputs
				if (empty($username) || empty($password) || empty($email)) {
					$this->render('auth/register.html', ['error' => 'All fields are required', 'title' => 'Register']);
					return;
				}
				
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$this->render('auth/register.html', ['error' => 'Invalid email format', 'title' => 'Register']);
					return;
				}
				
				// Check for existing username and email
				$userModel = new User();
				if ($userModel->usernameExists($username)) {
					$this->render('auth/register.html', ['error' => 'Username is already taken', 'title' => 'Register']);
					return;
				}
				
				if ($userModel->emailExists($email)) {
					$this->render('auth/register.html', ['error' => 'Email is already registered', 'title' => 'Register']);
					return;
				}
				
				// Hash the password and create the user
				$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
				$userModel->create(
					[
						'username' => $username,
						'password' => $hashedPassword,
						'email'    => $email,
						'role'     => User::ROLE_ADMIN
					]
				);
				
				// Redirect to login page after successful registration
				header("Location: /admin/login");
				exit;
			}
			
			// If form is not submitted, render the registration page without errors
			$this->render('auth/register.html', ['title' => 'Register']);
		}
		
		public function dashboard()
		{
			echo "Admin dashboard!";
			
			var_dump($_SESSION);
		}
		
	}
