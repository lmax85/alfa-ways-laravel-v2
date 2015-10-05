<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use DB;

class ApiController extends Controller {

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
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		$users = DB::table('users')->get();
		$api = DB::table('api_tests')->get();
		// var_dump($users);

		// foreach ($users as $user)
		// {
		// 	$data = $user;
		// 	var_dump($user);
		// }

		// $data = ['content' => 'Hello API'];
		return view('api/index', compact('users', 'api'));
	}

}
