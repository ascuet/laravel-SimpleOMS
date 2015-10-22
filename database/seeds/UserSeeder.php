<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder{
	public function run(){
		DB::table('users')->delete();
		User::create([
			'uid'=>'admin',
			'password'=>'mht301',
			'auth'=>0,
			'last_ip'=>'0.0.0.0'
			]);

	}

}