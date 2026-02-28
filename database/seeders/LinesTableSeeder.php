<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LinesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('lines')->delete();
        
        \DB::table('lines')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'antelo',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2026-02-27 19:56:37',
                'updated_at' => '2026-02-27 19:56:37',
                'registerUser_id' => 2,
                'registerRole' => 'administrador',
                'deleted_at' => NULL,
                'deleteUser_id' => NULL,
                'deleteRole' => NULL,
                'deleteObservation' => NULL,
            ),
        ));
        
        
    }
}