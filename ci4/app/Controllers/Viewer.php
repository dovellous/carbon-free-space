<?php namespace App\Controllers;

class Viewer extends BaseController
{

	public function header($type=1)
	{
		return  view("layout/headers/head").
		        view("layout/headers/{$type}/header").
		        view("layout/headers/header_styles").
		        view("layout/headers/main_begin");
	}

	public function footer($type=1)
	{
		return  view("layout/footers/main_end").
		        view("layout/footers/{$type}/footer").
		        view("layout/footers/footer_scripts");
		        view("layout/footers/close_html");
	}

	public function home()
	{
		return  view("pages/home");
	}

	//--------------------------------------------------------------------

}
