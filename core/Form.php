<?php
	
	namespace Core;
	
	use Random\RandomException;
	
	class Form
	{
		private array  $fields            = [];
		private string $csrfToken;
		private string $method            = 'POST';
		private string $action            = '';
		private string $submitButtonValue = 'Submit';
		private array  $errors            = [];
		
		/**
		 * @throws RandomException
		 */
		public function __construct()
		{
			if(Session::get('csrf_token'))
			{
				$this->csrfToken = Session::get('csrf_token');
			}
			else {
				$this->csrfToken = bin2hex(random_bytes(32));
				Session::set('csrf_token', $this->csrfToken);
			}
		}
		
		public function setMethod($method): void
		{
			$this->method = $method;
		}
		
		public function setAction($action): void
		{
			$this->action = $action;
		}
		
		public function setSubmitButtonName($value): void
		{
			$this->submitButtonValue = $value;
		}
		
		public function addField($type, $name, $label = null, $value = null, $attributes = []): void
		{
			$this->fields[] = [
				'type'       => $type,
				'name'       => $name,
				'label'      => $label,
				'value'      => $value,
				'attributes' => $attributes,
			];
		}
		
		public function render(): string
		{
			$html = '<form method="' . $this->method . '" action="' . $this->action . '">';
			$html .= '<input type="hidden" name="csrf_token" value="' . $this->csrfToken . '">';
			foreach ($this->fields as $field) {
				$html .= '<label for="' . $field['name'] . '">' . $field['label'] . '</label>';
				$html .= '<input type="' . $field['type'] . '" name="' . $field['name'] . '" value="' . $field['value'] . '"';
				foreach ($field['attributes'] as $attr => $val) {
					$html .= ' ' . $attr . '="' . $val . '"';
				}
				$html .= '>';
			}
			$html .= '<button type="submit">' . $this->submitButtonValue . '</button>';
			$html .= '</form>';
			return $html;
		}
		
		public function validateCSRFToken($token): bool
		{
			if ($token !== $this->csrfToken) {
				$this->addError('CSRF token invalid');
				return false;
			}
			
			return true;
		}
		
		public function handle(Request $request)
		{
			if ($request->getMethod() === 'POST') {
				foreach ($this->fields as $index => $field) {
					$this->fields[$index]['value'] = $request->get($field['name']);
				}
			}
		}
		
		public function clearForm(Request $request)
		{
			if ($request->getMethod() === 'POST') {
				foreach ($this->fields as $index => $field) {
					$this->fields[$index]['value'] = null;
				}
			}
		}
		
		public function validate(Request $request): bool
		{
			$this->errors = [];
			
			foreach ($this->fields as $field) {
				$value = $field['value'];
				$rules = $field['attributes'] ?? [];
				
				if ($rules['required'] && empty($value)) {
					$this->addError("{$field['name']} is required.");
				}
			}
			
			$this->validateCSRFToken($request->get('csrf_token'));
			
			return empty($this->errors);
		}
		
		public function getErrors(): array
		{
			return $this->errors;
		}
		
		public function isSubmitted(): bool
		{
			return $_SERVER['REQUEST_METHOD'] === 'POST';
		}
		
		public function addError($error): void
		{
			$this->errors[] = ['error' => $error];
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