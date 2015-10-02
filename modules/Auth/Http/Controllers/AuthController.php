<?php namespace Modules\Auth\Http\Controllers;

use Pingpong\Modules\Routing\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Auth\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use Validator;


class AuthController extends Controller {
	
	public function __construct()
	{
		// Apply the jwt.auth middleware to all methods in this controller
		// except for the authenticate method. We don't want to prevent
		// the user from retrieving their token if they don't already have it
		$this->middleware('jwt.auth', ['except' => ['authenticate', 'registration']]);
	}

	public function index()
	{
		// TODO: show users
		// Retrieve all the users in the database and return them
		$users = User::all();
		return $users;
	}

	public function registration(Request $request)
	{
		$credentials = $request->only('email', 'name', 'password', 'password_confirmation', 'post', 'place');
		$validateUser = Validator::make($credentials, [
				'name' => 'required|max:255',
				'email' => 'required|email|max:255|unique:users',
				'post' => 'required|max:255',
				'place' => 'required|max:255',
				'password' => 'required|confirmed|min:6',
			]);
		if ($validateUser->fails()){
			$errors = $validateUser->messages();
			return response()->json(['errors' => $errors], 401);
		}
		try {
			$user = User::create([
				'name' => $credentials['name'],
				'email' => $credentials['email'],
				'post' => $credentials['post'],
				'place' => $credentials['place'],
				'password' => bcrypt($credentials['password']),
			]);
		} catch (Exception $e) {
			return response()->json(['errors' => 'This email is busy']);
		}
		$token = JWTAuth::fromUser($user);
		$result = ["token" => $token, "email" => $user->email, 'user_id' => $user->id];
		// if no errors are encountered we can return a JWT
		return response()->json($result);
	}

	public function authenticate(Request $request)
	{
		$credentials = $request->only('email', 'password');

		try {
			// verify the credentials and create a token for the user
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['errors' => ['error' => 'Wrong password or email']], 401);
				// return response()->json(['errors' => 'Wrong password or email']);
			}
		} catch (JWTException $e) {
			// something went wrong
			// return response()->json(['errors' => 'could_not_create_token'], 500);
			return response()->json(['errors' => 'could_not_create_token']);
		}
		$user = JWTAuth::authenticate($token);
		$result = ["token" => $token, "email" => $user->email, 'user_id' => $user->id];
		// if no errors are encountered we can return a JWT
		return response()->json($result);
		// return response()->json(compact('token'));
	}
	
	public function getAuthenticatedUser()
	{
		
		try {

			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}

		// the token is valid and we have found the user via the sub claim
		return json(compact('user'));
	}	
	public function getAuthenticatedUser_orig()
	{
		try {

			if (! $user = JWTAuth::parseToken()->authenticate()) {
				return response()->json(['user_not_found'], 404);
			}

		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

			return response()->json(['token_expired'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

			return response()->json(['token_invalid'], $e->getStatusCode());

		} catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

			return response()->json(['token_absent'], $e->getStatusCode());

		}

		// the token is valid and we have found the user via the sub claim
		return response()->json(compact('user'));
	}
}