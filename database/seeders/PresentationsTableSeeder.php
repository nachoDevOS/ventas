<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PresentationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('presentations')->delete();
        
        \DB::table('presentations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => '5235',
                'name' => 'Blister',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2026-02-27 19:55:52',
                'updated_at' => '2026-02-27 19:55:52',
                'registerUser_id' => 2,
                'registerRole' => 'administrador',
                'deleted_at' => NULL,
                'deleteUser_id' => NULL,
                'deleteRole' => NULL,
                'deleteObservation' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => '2341234',
                'name' => 'Tableta',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2026-02-27 19:56:04',
                'updated_at' => '2026-02-27 19:56:04',
                'registerUser_id' => 2,
                'registerRole' => 'administrador',
                'deleted_at' => NULL,
                'deleteUser_id' => NULL,
                'deleteRole' => NULL,
                'deleteObservation' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => '5345234',
                'name' => 'Sobre',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2026-02-27 19:56:19',
                'updated_at' => '2026-02-27 19:56:19',
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