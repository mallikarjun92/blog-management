<?php
	
	namespace App\Controllers;
	
	use App\Models\Blog;
	use App\Models\User;
	use App\services\BlogDataService;
	use App\Services\EmailService;
	use Core\BaseController;
	use Core\Form;
	use Core\Request;
	use Core\Session;
	
	class AdminController extends BaseController
	{
		
		public function login(Request $request)
		{
			$form = new Form();
			$form->setMethod('POST');
			$form->setAction('/admin/login');
			$form->addField('text', 'username', 'Username', null, ['required' => true]);
			$form->addField('password', 'password', 'Password', null, ['required' => true]);
			$form->setSubmitButtonName('Login');
			
			$form->handle($request);
			
			if ($form->isSubmitted() && $form->validate($request)) {
				$formData = $form->getFormData();
				
				$userModel = new User();
				$user      = $userModel->findUserByUsername($formData['username']);
				
				if ($user && password_verify($formData['password'], $user['password']) && $user['role'] === User::ROLE_ADMIN) {
					Session::set('admin', $user['id']);
					Session::set('username', $formData['username']);
					$userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
					
					$this->redirect('/admin/dashboard');
				} else {
					$form->addError('Invalid credentials or unauthorized access');
				}
			}
			
			$this->render(
				'auth/login.html', [
				'form'      => $form->render(),
				'title' => 'Login',
				'errors'    => $form->getErrors()
			]);
		}
		
		public function logout(): void
		{
			Session::destroy();
			$this->redirect('/admin/login');
		}
		
		public function register(Request $request)
		{
			$form = new Form();
			
			$form->setMethod('POST');
			$form->setAction('/admin/register');
			$form->addField('text', 'username', 'Username', null, ['required' => true]);
			$form->addField('email', 'email', 'Email', null, ['required' => true, 'email' => true]);
			$form->addField('password', 'password', 'Password', null, ['required' => true]);
			$form->addField('password', 'password2', 'Confirm Password', null, ['required' => true]);
			$form->setSubmitButtonName('Register');
			
			$form->handle($request);
			
			if ($form->isSubmitted() && $form->validate($request)) {
				$formData = $form->getFormData();
				
				$userModel = new User();
				
				if ($userModel->usernameExists($formData['username'])) {
					$form->addError('Username is already taken');
				}
				
				if ($userModel->emailExists($formData['email'])) {
					$form->addError('Email is already registered');
				}
				
				if ($formData['password'] != $formData['password2']) {
					$form->addError('Passwords do not match');
				}
				
				if (empty($form->getErrors())) {
					$hashedPassword = password_hash($formData['password'], PASSWORD_BCRYPT);
					$userModel->create(
						[
							'username'   => $formData['username'],
							'password'   => $hashedPassword,
							'email'      => $formData['email'],
							'role'       => User::ROLE_ADMIN,
							'created_at' => date('Y-m-d H:i:s')
						]);
					
					Session::set('success', 'Registration successful. Please login.');
					$this->redirect('/admin/login');
				}
			}
			
			$this->render(
				'auth/register.html', [
				'form'   => $form->render(),
				'title'  => 'Register',
				'errors' => $form->getErrors()
			]);
		}
		
		public function dashboard()
		{
			
			$userModel = new User();
			$user      = $userModel->findBy(['id' => Session::get('admin')]);
			
			$blogModel = new Blog();
			$blogPosts = $blogModel->getAll();
			
			foreach ($blogPosts as &$post) {
				$post = (new BlogDataService())->getBlogDataForDashboard($post);
			}
			
			$placeholders = [
				'title' => 'Admin Dashboard',
				'posts' => $blogPosts,
			];
			
			$placeholders = array_merge($placeholders, $user);
			
			$this->render('admin/dashboard.html', $placeholders);
		}
		
		public function recoverPassword(Request $request)
		{
			$form = new Form();
			$form->setMethod('POST');
			$form->setAction('/admin/forgot-password');
			$form->addField(
				'email', 'email', 'Email', '', [
				'required'    => 'required',
				'placeholder' => 'Enter email',
				'class'       => 'form-control',
			]);
			$form->setSubmitButtonName('Send Recovery Email');
			
			$form->handle($request);
			
			if ($form->isSubmitted() && $form->validate($request)) {
				$formData = $form->getFormData();
				
				// send password recover email
				$mailService = new EmailService();
				$mailService->sendPasswordResetEmail($formData['email']);
				Session::set('message', "if an account exists with email {$formData['email']} a reset password email will be sent.");
				$form->clearForm($request);
				$this->redirect('/admin/forgot-password');
			}
			
			$this->render(
				'auth/recover_password.html', [
				'form'   => $form->render(),
				'title'  => 'Recover Password',
				'errors' => $form->getErrors(),
			]);
		}
		
		public function resetPassword(Request $request, $resetToken): void
		{
			
			$userModel = new User();
			$user      = $userModel->findBy(['reset_token' => $resetToken]);
			
			if (!empty($user)) {
				
				if ($user['token_expiry'] < date('Y-m-d H:i:s')) {
					// Token has expired
					Session::set('message', 'Reset token expired.');
					
				} else {
					
					$form = new Form();
					
					$form->setMethod('POST');
					$form->setAction('/reset-password/' . $user['reset_token']);
					$form->addField('password', 'password', 'New Password', null, ['required' => true]);
					$form->addField('password', 'password2', 'Confirm Password', null, ['required' => true]);
					$form->setSubmitButtonName('Reset Password');
					
					$form->handle($request);
					
					if ($form->isSubmitted() && $form->validate($request)) {
						
						$formData       = $form->getFormData();
						$hashedPassword = password_hash($formData['password'], PASSWORD_BCRYPT);
						
						if ($formData['password'] != $formData['password2']) {
							
							$form->addError('Passwords do not match');
						} else {
							
							$userModel->update($user['id'], ['password' => $hashedPassword]);
							
							Session::set('message', 'Your password has been reset. Please login.');
							
							$this->redirect('/admin/login');
						}
					}
					
					$this->render(
						'auth/reset_password.html', [
						'form'  => $form->render(),
						'title' => 'Reset Password',
						'errors' => $form->getErrors()
					]);
				}
				
			} else {
				Session::set('message', 'Reset token invalid.');
			}
			
			$this->render('notice.html');
			
		}
		
	}
