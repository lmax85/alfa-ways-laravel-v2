<?php namespace Modules\Getapi\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

class GetApiController extends Controller {
	
	public function index()
	{
		return view('getapi::index');
	}
	
}