<?php
	
	namespace Core;
	
	class Request
	{
		private $request;
		
		public function __construct()
		{
			$this->request = $_REQUEST;
		}
		
		public function get($key, $default = null)
		{
			return $this->request[$key] ?? $default;
		}
		
		public function all(): array
		{
			return $this->request;
		}
		
		public function isAjax(): bool
		{
			return $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
		}
		
		public function isGet(): bool
		{
			return $_SERVER['REQUEST_METHOD'] === 'GET';
		}
		
		public function getMethod(): string
		{
			return $_SERVER['REQUEST_METHOD'];
		}
		
		public function getFormData(): array
		{
			$formData = $_POST;
			
			foreach ($formData as $key => $value) {
				$formData[$key] = filter_var($value, FILTER_SANITIZE_STRING);
			}
			
			return $formData;
		}
	}