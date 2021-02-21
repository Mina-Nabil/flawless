<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DevicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("devices")->insert([
            "DVIC_NAME" => "Splendor X"
        ]);
        DB::table("devices")->insert([
            "DVIC_NAME" => "MeDioStar Pro XL"
        ]);
        DB::table("devices")->insert([
            "DVIC_NAME" => "Vivace"
        ]);
    }
}
