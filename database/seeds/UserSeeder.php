<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder{
	public function run(){
		DB::table('users')->delete();
		$password = Hash::make('mht301');
		User::create([
			'uid'=>'admin',
			'password'=>$password,
			'auth'=>0,
			'last_ip'=>'0.0.0.0'
			]);

	}

}