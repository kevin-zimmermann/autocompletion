<?php

namespace Base\Reply;


use Base\Mvc\Controller;

class Exception extends \Exception
{

	protected $reply;
	protected $controller = null;
	protected $title = "";

    /**
     * Exception constructor.
     * @param Controller $controller
     * @param string $reply
     * @param string $title
     */
    public function __construct(Controller $controller, $reply = "", $title = "Oops! Nous avons rencontré des problèmes.")
    {
        parent::__construct();
        $this->reply = $reply;
        $this->controller = $controller;
        $this->title = $title;
    }

    /**
     *
     */
    public function errorMessage() {
        $this->controller->view('error', ['message' => $this->reply, 'title' => $this->title], $this->title);
    }

}