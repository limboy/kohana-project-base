<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
		$this->request->response = '<a href="'.url::base().'index.php/log">log</a>';
	}

} // End Welcome
