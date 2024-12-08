<?php
	
	namespace App\Services;
	
	use Core\LocalMailer;
	use App\Models\User;
	
	class EmailService
	{
		private $mailer;
		
		public function __construct()
		{
			$this->mailer = new LocalMailer();
		}
		public function sendPasswordResetEmail($email): void
		{
			
			$userModel = new User();
			$user = $userModel->findBy(['email' => $email]);
			
			if ($user) {
				// generate reset token
				$reset_token = bin2hex(random_bytes(6));
				
				// send email with reset token
				$to = $email;
				$from = "noreply@localhost";
				$subject = "Reset Password";
				$message = "Click this link to reset your password \r\n";
				$message .= "http://localhost:8000/reset-password/".$reset_token."\r\n";
				$headers = "From: $from\r\n";
				$headers .= "Reply-To: $from\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$this->mailer->sendEmail($to, $subject, $message, $headers);
				
				// update users table with reset token and expiry time
				$userModel->update(
					$user['id'], [
					'reset_token'  => $reset_token,
					'token_expiry' => date("Y-m-d H:i:s", strtotime("+30 minutes"))
				]);
			}
		}
	}