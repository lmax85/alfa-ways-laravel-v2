<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// $this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{

		// $mytime = Carbon\Carbon::now();
		// echo $mytime->toDateTimeString();

		$x = 'Thu Aug 13 2015';
		$y = '02:00:00 GMT+0700 (KRAT)';
		$departure = new Carbon($x.$y);
		echo $departure;

		$data = [
			'content' => 'date'
		];

		return view('welcome', $data);
	}

		public function ldaptest()
	{
		// echo phpinfo();
		// return view('home');
$username   = 'Administrator';
$password   = 'Qwer1234';
$server = '192.168.1.225';
$domain = '@alfa.local';
$port       = 389;
$ldap_dn = "DC=alfa,DC=local";

$ldap_connection = ldap_connect($server, $port);

if (! $ldap_connection)
{
    echo '<p>LDAP SERVER CONNECTION FAILED</p>';
    exit;
}

// Help talking to AD
ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);

$ldap_bind = @ldap_bind($ldap_connection, $username.$domain, $password);

if (! $ldap_bind)
{
    echo '<p>LDAP BINDING FAILED</p>';
    exit;
}

// You can work now!!!
	$filter = "(sAMAccountName=" . $username . ")";
    // $attr = array("displayname", "title", "department");
    $attr = array("*");
    $result = ldap_search($ldap_connection, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
    $entries = ldap_get_entries($ldap_connection, $result);
    // $givenname = $entries[0]['displayname'];
    var_dump($entries);
    
    echo '<p>bin2hex ====='.bin2hex($entries[0]['objectsid'][0]).'</p>';
    // print_r($entries);
    echo $entries[0]['displayname'][0].'<br />';
    // echo $entries[0]['title'][0].'<br />';
    // echo $entries[0]['department'][0].'<br />';
    echo $entries[0]['objectsid'][0].'<br />';
		return 'asdf';
		
	}

	public function testauth() {
		if (Auth::check())
        {
            return redirect()->intended('ldaptest');
        }

        return redirect()->guest('auth/login');
	}

	public function ldaplogin() {
		return view('ldaplogin');
	}

	public function ldapauth(Request $request) {

		$credentials = $request->only('username', 'password');
		var_dump($credentials);

		if (Auth::attempt($credentials))
        {
            return 'login success';
        }
		return 'no auth...';
	}

}
