<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class FeedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $array=[
        ['name'=>'humedad'],
        ['name'=>'lluvia'],
        ['name'=>'suelo'],
        ['name'=>'temperatura'],
        ['name'=>'agua'],
        ['name'=>'bomba'],
        ['name'=>'luz'],
        ['name'=>'movimiento']
       ];

       DB::table('sensors')->insert($array);
    }
}
