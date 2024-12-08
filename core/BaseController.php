<?php
namespace Core;

use Core\SimpleTemplate;
use JetBrains\PhpStorm\NoReturn;

class BaseController {

    protected $template;

    public function __construct()
    {
        $this->template = new SimpleTemplate();
    }
	
	/**
	 * @param $view
	 * @param array $data
	 * @return void
	 */
	protected function render($view, array $data = []): string
	{
		
		$this->template->render($view, $data);
		exit;
	}
	
	public function redirect($url, $status = 302): void
	{
		header('Location: ' . $url, true, $status);
		exit;
	}
}
