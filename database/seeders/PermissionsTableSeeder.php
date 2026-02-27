<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        Permission::firstOrCreate([
            'key'        => 'browse_admin',
            'keyDescription'=>'vista de acceso al sistema',
            'table_name' => 'admin',
            'tableDescription'=>'Panel del Sistema'
        ]);

        $keys = [
            // 'browse_admin',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
            'browse_clear-cache',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');

        Permission::generateFor('roles');
        Permission::generateFor('permissions');
        Permission::generateFor('settings');

        Permission::generateFor('users');

        Permission::generateFor('posts');
        Permission::generateFor('categories');
        Permission::generateFor('pages');

        

        // Administracion
        $permissions = [
            'browse_people' => 'Ver lista de personas',
            'read_people' => 'Ver detalles de una persona',
            'edit_people' => 'Editar información de personas',
            'add_people' => 'Agregar nuevas personas',
            'delete_people' => 'Eliminar personas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'people',
                'tableDescription'=>'Personas'
            ]);
        }

        $permissions = [
            'browse_cashiers' => 'Ver lista de cajas',
            'read_cashiers' => 'Ver detalles de cajas',
            'edit_cashiers' => 'Editar información de cajas',
            'add_cashiers' => 'Agregar nuevas cajas',
            'delete_cashiers' => 'Eliminar cajas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'cashiers',
                'tableDescription'=>'Cajas'
            ]);
        }

        $permissions = [
            'browse_sales' => 'Ver lista de ventas',
            'read_sales' => 'Ver detalles de ventas',
            'edit_sales' => 'Editar información de ventas',
            'add_sales' => 'Agregar nuevas ventas',
            'delete_sales' => 'Eliminar ventas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'sales',
                'tableDescription'=>'Ventas'
            ]);
        }



        // Parametros de Inventario

        $permissions = [
            'browse_items' => 'Ver lista de productos o items',
            'read_items' => 'Ver detalles de productos o items',
            'edit_items' => 'Editar información de productos o items',
            'add_items' => 'Agregar nuevos productos o items',
            'delete_items' => 'Eliminar productos o items',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'items',
                'tableDescription'=>'Productos o Items'
            ]);
        }
        
        $permissions = [
            'browse_categories' => 'Ver lista de categorias',
            'read_categories' => 'Ver detalles de categorias',
            'edit_categories' => 'Editar información de categorias',
            'add_categories' => 'Agregar nuevas categorias',
            'delete_categories' => 'Eliminar categorias',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'categories',
                'tableDescription'=>'Categorias'
            ]);
        }

        $permissions = [
            'browse_presentations' => 'Ver lista de presentaciones',
            'read_presentations' => 'Ver detalles de presentaciones',
            'edit_presentations' => 'Editar información de presentaciones',
            'add_presentations' => 'Agregar nuevas presentaciones',
            'delete_presentations' => 'Eliminar presentaciones',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'presentations',
                'tableDescription'=>'Presentaciones'
            ]);
        }

        $permissions = [
            'browse_laboratories' => 'Ver lista de laboratorio',
            'read_laboratories' => 'Ver detalles de los laboratorio',
            'edit_laboratories' => 'Editar información de laboratorio',
            'add_laboratories' => 'Agregar nuevos laboratorio',
            'delete_laboratories' => 'Eliminar laboratorio',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'laboratories',
                'tableDescription'=>'Laboratorio'
            ]);
        }

        $permissions = [
            'browse_lines' => 'Ver lista de lineas',
            'read_lines' => 'Ver detalles de los lineas',
            'edit_lines' => 'Editar información de lineas',
            'add_lines' => 'Agregar nuevos lineas',
            'delete_lines' => 'Eliminar lineas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'lines',
                'tableDescription'=>'Lineas'
            ]);
        }

     



        
        
    }
}