<?php

use DB;

class UserTableSeeder extends Seeder {

	public function run()
	{
		DB::table('trips')->delete();

		Trip::create([
			'id' => '1',
			'description' => 'first test trip',
			'user_id' => '1',
			'from_location_id' => '5',
			'to_location_id' => '10',
			
		]);
	}

}
