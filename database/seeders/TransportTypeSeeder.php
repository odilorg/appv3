<?php
namespace Database\Seeders;

use App\Models\TransportType;
use Illuminate\Database\Seeder;

class TransportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // road
            ['mode'=>'road','code'=>'SEDAN',   'name'=>'Sedan',   'capacity'=>4],
            ['mode'=>'road','code'=>'MINIBUS', 'name'=>'Minibus', 'capacity'=>12],
            ['mode'=>'road','code'=>'BUS',     'name'=>'Bus',     'capacity'=>40],
            // air & rail as types (simple umbrella types for now)
            ['mode'=>'air', 'code'=>'AIR',     'name'=>'Air',     'capacity'=>null],
            ['mode'=>'rail','code'=>'RAIL',    'name'=>'Rail',    'capacity'=>null],
        ];

        foreach ($rows as $r) {
            TransportType::firstOrCreate(['code'=>$r['code']], $r);
        }
    }
}
