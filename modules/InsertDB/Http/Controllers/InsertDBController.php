<?php namespace Modules\Insertdb\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Storage;
use DB;
use App\User;

class InsertDBController extends Controller {
	
	public function index()
	{
		return view('insertdb::index');
	}

	public function insertusers()
	{
		$files = Storage::allFiles('data/users');
		$disk = Storage::disk('local');
		if (count($files) > 0) {
			foreach ($files as $file) {
				if (strpos($file, '.DS_Store') != 0) {continue;}
				$contents = Storage::get($file);
				$data = str_getcsv($contents, "\n");
				if (count($data) > 0) {
					$i = 0;
					foreach ($data as $row) {
						$userdata = str_getcsv($row, '^');
						$user = User::firstOrCreate([
							'id'         => $userdata[0],
							'name'       => $userdata[1],
							'phone'      => $userdata[2],
							'email'      => $userdata[3],
							'post'       => $userdata[4],
							'place'      => $userdata[5],
							'department' => $userdata[6]
						]);
					}
				}
			}
		}
	}
	
}