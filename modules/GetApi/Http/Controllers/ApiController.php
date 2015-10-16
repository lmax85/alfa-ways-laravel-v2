<?php namespace Modules\Getapi\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use DB;
use App\Trip;
use App\User;
use Carbon\Carbon;

class ApiController extends Controller {

	public function gettrips($id = '')
	{
		if ($id != '') {
			$result = Trip::find($id);
			if ($result->user_id > 0) {
				$result['driver'] = User::find($result->driver_id);
				$result['from_location'] = DB::select("select city.city_id, city.name, region.name as `region`, country.name as `country` FROM city
			                    join region on city.region_id=region.region_id
			                    join country on city.country_id=country.country_id
			                    WHERE city.city_id=$result->from_location_id");
				$result['to_location'] = DB::select("select city.city_id, city.name, region.name as `region`, country.name as `country` FROM city
			                    join region on city.region_id=region.region_id
			                    join country on city.country_id=country.country_id
			                    WHERE city.city_id=$result->to_location_id");
			}
		} else {
			$trips = Trip::orderBy('created_at', 'desc')->get();
			//orig $trips = DB::table('trips')->orderBy('created_at', 'desc')->get();
			// $trips = Trip::all()->orderBy('user_id')->get();
			$i = 0;
			foreach ($trips as $trip) {
				$result[$i]['trip'] = $trip;
				$result[$i]['driver'] = User::find($trip->driver_id);
				$result[$i]['from_location'] = DB::select("select city.city_id, city.name, region.name as `region`, country.name as `country` FROM city
			                    join region on city.region_id=region.region_id
			                    join country on city.country_id=country.country_id
			                    WHERE city.city_id=$trip->from_location_id");
				$result[$i]['to_location'] = DB::select("select city.city_id, city.name, region.name as `region`, country.name as `country` FROM city
			                    join region on city.region_id=region.region_id
			                    join country on city.country_id=country.country_id
			                    WHERE city.city_id=$trip->to_location_id");
				$i++;
			}
		}
		$responseTest = response()->json($result)->header('Access-Control-Allow-Origin', '*');
		// var_dump($responseTest);
		return $responseTest;
		// return view('home');
	}

	public function posttrip(Request $request)
	{

		$newtrip = $request->all();

		$departure_day = $newtrip['departureDay'].' '.$newtrip['departureTime'];
		$departure = new Carbon($departure_day);

		$trip = new Trip;
		$trip->description = $newtrip['description'];
		$trip->user_id = $newtrip['user_id'];
		$trip->driver_id = $newtrip['driver_id'];
		// $trip->from_location_id = '123';
		$trip->from_location_id = $newtrip['from'];
		$trip->to_location_id = $newtrip['to'];
		// $trip->to_location_id = '321';
		$trip->departure = $departure;
		$trip->arrival = '2015-06-23 03:59:51';

		if($trip->save()) {
			$result = ["message" => ["Поездка создана успешно!"], "tripId" => $trip->id];
			$response = response()->json($result)->header("Access-Control-Allow-Origin", "*");
		} else {
			$result = ["message" => ["Что-то пошло не так, попробуйте позже!"]];
			$response = response()->json($result, 440)->header("Access-Control-Allow-Origin", "*");
		}

		return $response;
	}

	public function updatetrip($id= '', Request $request)
	{

		$newtrip = $request->all();

		$departure_day = $newtrip['departureDay'].' '.$newtrip['departureTime'];
		$departure = new Carbon($departure_day);

		$trip = Trip::find($id);
		$trip->description = $newtrip['description'];
		$trip->user_id = $newtrip['user_id'];
		$trip->driver_id = $newtrip['driver_id'];
		$trip->from_location_id = $newtrip['from'];
		$trip->to_location_id = $newtrip['to'];
		$trip->departure = $departure;
		$trip->arrival = '2015-06-23 03:59:51';

		if($trip->save()) {
			$result = ["message" => ["Поездка успешно изменена!"], "tripId" => $trip->id];
			$response = response()->json($result)->header("Access-Control-Allow-Origin", "*");
		} else {
			$result = ["message" => ["Что-то пошло не так, попробуйте позже!"]];
			$response = response()->json($result, 440)->header("Access-Control-Allow-Origin", "*");
		}

		return $response;
	}

	public function deletetrip($id)
	{
		$trip = Trip::find($id);

		if($trip->delete()) {
			$result = ["message" => ["Поездка удалена!"], "tripId" => $id];
			$response = response()->json($result)->header("Access-Control-Allow-Origin", "*");
		} else {
			$result = ["message" => ["Что-то пошло не так, попробуйте позже!"]];
			$response = response()->json($result, 440)->header("Access-Control-Allow-Origin", "*");
		}

		return $response;
	}

	public function getdrivers($id = '')
	{
		if ($id != '') {
			$result = User::find($id);
		} else {
			$drivers = DB::table('users')->orderBy('created_at', 'desc')->get();
			// $drivers = Trip::all()->orderBy('user_id')->get();
			foreach ($drivers as $driver) {
				$result[] = $driver;
				// var_dump($result);
			}
		}
		$responseTest = response()->json($result)->header('Access-Control-Allow-Origin', '*');

		// var_dump($result);

		// return View::make('getapi::index', compact('api', 'responseTest'));
		return $responseTest;
	}

	public function searchdriver($query = '')
	{
		// if ($query == '') {
		// 	$result = [
		// 		0 => [ 'id' => 1, 'label' => 'Иванов Антон Дмитриевич', 'value' => 'иванов антон дмитриевич', 'post' => 'старший инженер', 'place' => 'ОО Кемеровский' ],
		// 		1 => [ 'id' => 2, 'label' => 'Ланин Максим Юрьевич', 'value' => 'ланин максим юрьевич', 'post' => 'ведущий инженер', 'place' => 'ОО Омский' ],
		// 		2 => [ 'id' => 3, 'label' => 'Осокин Роман Сергеевич', 'value' => 'осокин роман сергеевич', 'post' => 'инженер', 'place' => 'ОО Томский' ],
		// 		3 => [ 'id' => 4, 'label' => 'Чунрев Роман Викторович', 'value' => 'чунарев роман викторович', 'post' => 'инженер', 'place' => 'ОО Челяба' ],
		// 		4 => [ 'id' => 5,'label' => 'Киприянов Павел Игоревич', 'value' => 'киприянов павел игоревич', 'post' => 'начальник отдела', 'place' => 'ОО Кемеровский' ]
		// 	];
		// } else {
		// 	$result = [
		// 		0 => [ 'id' => 10, 'label' => 'Горбачев Михаил Сергеевич', 'value' => 'горбачев михаил сергеевич', 'post' => 'старший инженер', 'place' => 'ОО Кемеровский' ],
		// 		1 => [ 'id' => 20, 'label' => 'Ельцын Борис Николаевич', 'value' => 'ельцын борис николаевич', 'post' => 'ведущий инженер', 'place' => 'ОО Омский' ],
		// 	];
		// }
		if ($query != '') {
			$users = DB::select("select * FROM users WHERE (LOWER(name) like '%".mb_strtolower($query)."%')");
			if (count($users) > 0)
				// var_dump($users);
				foreach ($users as $user) {
					$result[] = [
						'id' => $user->id,
						'label' => $user->name,
						'value' => mb_strtolower($user->name),
						'phone' => $user->phone,
						'email' => $user->email,
						'post' => $user->post,
						'place' => $user->place,
						'department' => $user->department
					];
					// echo '<br />value = '.mb_strtolower($user->name);
			} else {
				$result = [0 => ['id' => 0]];
			}
			// var_dump($result);
		} else {
			$result = [
				// 0 => [ 'id' => 10, 'label' => 'Горбачев Михаил Сергеевич', 'value' => 'горбачев михаил сергеевич', 'post' => 'старший инженер', 'place' => 'ОО Кемеровский' ],
				// 1 => [ 'id' => 20, 'label' => 'Ельцын Борис Николаевич', 'value' => 'ельцын борис николаевич', 'post' => 'ведущий инженер', 'place' => 'ОО Омский' ],
			];
		}
		// print_r($result);
		$response = response()->json($result)->header('Access-Control-Allow-Origin', '*');
		return $response;
	}
	public function searchlocation($query = '')
	{
		// if ($query == '') {
		// 	$result = [
		// 		0 => [ 'id' => 1, 'label' => 'Иванов Антон Дмитриевич', 'value' => 'иванов антон дмитриевич', 'post' => 'старший инженер', 'place' => 'ОО Кемеровский' ],
		// 		1 => [ 'id' => 2, 'label' => 'Ланин Максим Юрьевич', 'value' => 'ланин максим юрьевич', 'post' => 'ведущий инженер', 'place' => 'ОО Омский' ],
		// 		2 => [ 'id' => 3, 'label' => 'Осокин Роман Сергеевич', 'value' => 'осокин роман сергеевич', 'post' => 'инженер', 'place' => 'ОО Томский' ],
		// 		3 => [ 'id' => 4, 'label' => 'Чунрев Роман Викторович', 'value' => 'чунарев роман викторович', 'post' => 'инженер', 'place' => 'ОО Челяба' ],
		// 		4 => [ 'id' => 5,'label' => 'Киприянов Павел Игоревич', 'value' => 'киприянов павел игоревич', 'post' => 'начальник отдела', 'place' => 'ОО Кемеровский' ]
		// 	];
		// } else {
		// 	$result = [
		// 		0 => [ 'id' => 10, 'label' => 'Горбачев Михаил Сергеевич', 'value' => 'горбачев михаил сергеевич', 'post' => 'старший инженер', 'place' => 'ОО Кемеровский' ],
		// 		1 => [ 'id' => 20, 'label' => 'Ельцын Борис Николаевич', 'value' => 'ельцын борис николаевич', 'post' => 'ведущий инженер', 'place' => 'ОО Омский' ],
		// 	];
		// }
		if ($query != '') {
			$locations = DB::select("select city.city_id, city.name, region.name as `region`, country.name as `country` FROM city
			                    join region on city.region_id=region.region_id
			                    join country on city.country_id=country.country_id
			                    WHERE (LOWER(city.name) like '%".mb_strtolower($query)."%')");
			if (count($locations) > 0)
				// var_dump($locations);
				foreach ($locations as $location) {
					$result[] = [
						'city_id' => $location->city_id,
						'label' => $location->name,
						'value' => 'mb_strtolower($location->city)',
						'email' => '$location->regions',
						'post' => $location->country,
						'place' => $location->region
					];
					// echo '<br />value = '.mb_strtolower($user->name);
			} else {
				$result = [0 => ['id' => 0]];
			}
			// var_dump($result);
		} else {
			$result = [
				// 0 => [ 'id' => 10, 'label' => 'Горбачев Михаил Сергеевич', 'value' => 'горбачев михаил сергеевич', 'post' => 'старший инженер', 'place' => 'ОО Кемеровский' ],
				// 1 => [ 'id' => 20, 'label' => 'Ельцын Борис Николаевич', 'value' => 'ельцын борис николаевич', 'post' => 'ведущий инженер', 'place' => 'ОО Омский' ],
			];
		}
		// print_r($result);
		$response = response()->json($result)->header('Access-Control-Allow-Origin', '*');
		return $response;
	}
}