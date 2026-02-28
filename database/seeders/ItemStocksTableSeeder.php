<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItemStocksTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('item_stocks')->delete();
        
        \DB::table('item_stocks')->insert(array (
            0 => 
            array (
                'id' => 1,
                'item_id' => 1,
                'incomeDetail_id' => NULL,
                'lote' => '4234',
                'expirationDate' => '2026-05-31',
                'quantity' => '11.00',
                'stock' => '11.00',
                'pricePurchase' => '8.00',
                'priceSale' => '12.00',
                'dispensed' => 'Fraccionado',
                'dispensedQuantity' => '10.00',
                'dispensedPrice' => '2.00',
                'type' => 'Ingreso',
                'observation' => NULL,
                'status' => 1,
                'created_at' => '2026-02-28 10:13:42',
                'updated_at' => '2026-02-28 10:13:42',
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