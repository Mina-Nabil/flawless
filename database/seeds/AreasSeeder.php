<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("areas")->insert([ 
            ["AREA_NAME" => "Underarm"],
            ["AREA_NAME" => "Bikini"],
            ["AREA_NAME" => "Half arm"],
            ["AREA_NAME" => "Full arm"],
            ["AREA_NAME" => "Half leg"],
            ["AREA_NAME" => "Full leg"],
            ["AREA_NAME" => "Full leg + Full arm"],
            ["AREA_NAME" => "Back"],
            ["AREA_NAME" => "Abdomen"],
            ["AREA_NAME" => "Full body"],
        ]);
    }
}
