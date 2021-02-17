<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("dash_types")->insert([
            "id" => 1, "DHTP_NAME" => "admin"
        ]);
        DB::table("dash_types")->insert([
            "id" => 2, "DHTP_NAME" => "doctor"
        ]);

        DB::table('dash_users')->insert([
            "DASH_USNM" => "mina",
            "DASH_FLNM" => "Mina Nabil",
            "DASH_PASS" => bcrypt('mina@flawless'),
            "DASH_TYPE_ID" => 1,
        ]);
    }
}
