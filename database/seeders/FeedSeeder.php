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
        ['name'=>'Humedad'],
        ['name'=>'Lluvia'],
        ['name'=>'Suelo'],
        ['name'=>'Temperatura'],
        ['name'=>'agua'],
        ['name'=>'bomba'],
        ['name'=>'luz'],
        ['name'=>'movimiento']
       ];

       DB::table('sensors')->insert($array);
    }
}
