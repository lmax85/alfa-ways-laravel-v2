<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	/*public function index()
	{
		return view('home');
	}*/
	public function index()
	{
		if (Auth::attempt(array('username' => 'u_32104', 'password' => 'G0ldSt@r')))
		{
		    // return Redirect::intended('home');
		    // return view('home');
		    return 'logged in';
		} else {
			// return view('home');
			return 'no logged ...';
		}
	}

}
