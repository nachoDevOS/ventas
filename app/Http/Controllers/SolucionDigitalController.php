<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;


class SolucionDigitalController extends Controller
{
    public function settings_code() 
    {
        return DB::connection('solucionDigital')->table('web_systems')->where('code', setting('system.code-system'))->first();
    }

    public function payment_alert()
    {
        $soliciondigital = new SolucionDigitalController();
        $data = $soliciondigital->settings_code();
        $date = $data->finish;

        $now = new DateTime();
        $value= null;
        $d = DateTime::createFromFormat('Y-m-d H:i:s', $date.' 23:59:59');
    
        if($data->type == 'Demo')// si es demo no tiene restrincion
        {
            return $value;
        }

        if($d && $d->format('Y-m-d') === $date )
        {
            if($now > $d) //si la fecha actual es mayor a la fecha de finalizacion de del sistema no se puede realizar ningun metodo
            {
                $value = "finalizado";
            }
            else
            {
                $difference = $now->diff($d);
                if($difference->days <= 3)
                {
                    $value = $difference->days;
                }
                else
                {
                    $value = "vigente";
                }
            }
        }
        else
        {
            $value = null;
        }
        return $value;
    }
}
