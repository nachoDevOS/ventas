<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;   
use Illuminate\Support\Facades\URL;
use App\FormFields\Country_codeFormField;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Voyager::addFormField(Country_codeFormField::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // Detectar si estamos detrás de un proxy (como Coolify)
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        Paginator::useBootstrap();

        // 1. Registramos los singletons UNA SOLA VEZ por petición.
        // Esto asegura que la lógica pesada (consultas a BD, etc.) solo se ejecute la primera vez que se necesiten los datos. 
        $this->app->singleton('globalFuntion_cashierMoney', function () {
            $controller = new Controller();
            return $controller->cashierMoney(null, Auth::check() ? 'user_id = "'.Auth::user()->id.'"' : null, 'status = "Abierta"')->original;
        });

        // 2. Usamos el View Composer para COMPARTIR los datos ya resueltos (o por resolver una vez) con todas las vistas.
        View::composer('*', function ($view) {
            $view->with('globalFuntion_cashierMoney', $this->app->make('globalFuntion_cashierMoney'));

            // Para omitir vista mediante rutas
            $currentRouteName = Route::currentRouteName();
            if ($currentRouteName !== 'cashiers.close') {
                // $view->with('globalFuntion_cashierMoney', $this->app->make('globalFuntion_cashierMoney'));
            }
        });

        // Solo Para la vista Index
        View::composer('voyager::index', function ($view) {
            $global_index = new IndexController();
            $view->with('global_index', $global_index->IndexSystem()->original);

            $globalFuntion_cashier = new Controller();
            $view->with('globalFuntion_cashier', $globalFuntion_cashier->cashier(null, Auth::check() ? 'user_id = "'.Auth::user()->id.'"' : null, 'status <> "Cerrada"'));
        });

    }
}
