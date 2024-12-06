<?php
namespace Core;

use Core\SimpleTemplate;

class BaseController {

    protected $template;

    public function __construct()
    {
        $this->template = new SimpleTemplate();
    }

    protected function render($view, $data = []) {

        $this->template->render($view, $data);
    }
}
