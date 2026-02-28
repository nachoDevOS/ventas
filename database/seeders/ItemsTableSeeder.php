<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('items')->delete();
        
        \DB::table('items')->insert(array (
            0 => 
            array (
                'id' => 1,
                'category_id' => 1,
                'presentation_id' => 1,
                'laboratory_id' => 1,
                'line_id' => 1,
                'image' => NULL,
                'nameGeneric' => 'Paracetamol G',
                'nameTrade' => 'Paracetamol C',
                'fraction' => 1,
                'fractionPresentation_id' => 2,
                'fractionQuantity' => '10.00',
                'observation' => NULL,
                'status' => 1,
                'stockMinimum' => NULL,
                'created_at' => '2026-02-28 10:12:50',
                'updated_at' => '2026-02-28 10:12:50',
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