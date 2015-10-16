<?php namespace Modules\Auth\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Auth\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Validator;
use Auth;
use Illuminate\Routing\Controller as BaseController;

class LdapAuthController extends BaseController {
	
	public function __construct()
	{
		// Apply the jwt.auth middleware to all methods in this controller
		// except for the authenticate method. We don't want to prevent
		// the user from retrieving their token if they don't already have it
		// $this->middleware('jwt.auth', ['except' => ['authenticate', 'registration']]);
	}

	public function index()
	{
		// TODO: show users
		// Retrieve all the users in the database and return them
		$users = User::all();
		return $users;
	}

	public function authenticate(Request $request)
	{
		$credentials = $request->only('username', 'password');

		$username   = $credentials['username'];
		$password   = $credentials['password'];
		$server = '192.168.1.225';
		$domain = '@alfa.local';
		$port       = 389;
		$ldap_dn = "DC=alfa,DC=local";

		$ldap_connection = ldap_connect($server, $port);

		if (! $ldap_connection)
		{
			return response()->json(['errors' => ['error' => 'Can not connect to the ldap server!']], 401);
		}

		// Help talking to AD
		ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);

		$ldap_bind = @ldap_bind($ldap_connection, $username.$domain, $password);

		if (! $ldap_bind)
		{
			return response()->json(['errors' => ['error' => 'Wrong password or username']], 401);
		}

		// You can work now!!!
		$filter = "(sAMAccountName=" . $username . ")";
		// $attr = array("displayname", "title", "department");
		$attr = array("*");
		$result = ldap_search($ldap_connection, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
		$entries = ldap_get_entries($ldap_connection, $result);
		// $givenname = $entries[0]['displayname'];
		// var_dump($entries);

		// echo '<p>bin2hex ====='.bin2hex($entries[0]['objectsid'][0]).'</p>';
		// print_r($entries);
		// echo $entries[0]['displayname'][0].'<br />';
		// echo $entries[0]['title'][0].'<br />';
		// echo $entries[0]['department'][0].'<br />';
		// echo $entries[0]['objectsid'][0].'<br />';

		$result_auth = ["token" => 'xyz', "username" => $entries[0]['displayname'][0], 'user_id' => bin2hex($entries[0]['objectsid'][0])];
		return response()->json($result_auth);
		// return 'asdf';


		/*if (Auth::attempt(array('username' => $credentials['username'], 'password' => $credentials['password'])))
		{
			$result = ["token" => 'xyz', "username" => Auth::user()->getGroups(), 'user_id' => '0105000000000005150000003caeba9f64a01d3104c991e253040000'];
		} else {
			return response()->json(['errors' => ['error' => 'Wrong password or username']], 401);
		}
		return response()->json($result);*/
	}

}