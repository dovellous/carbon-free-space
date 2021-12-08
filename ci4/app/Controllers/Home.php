<?php namespace App\Controllers;

class Home extends Viewer
{

	public function index()
	{
		return  $this->header(1).
		        $this->home(1).
		        $this->footer(1);
	}

	//--------------------------------------------------------------------

}
