<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\CashierDetailCash;
use App\Models\CashierMovement;
use App\Models\User;
use App\Models\Sale;
use App\Models\VaccinationRecord;
use App\Models\AnamnesisForm;
use App\Models\HairSalon;
use App\Models\Deworming;
use App\Models\HomeService;
use App\Models\Reminder;
use App\Models\Person;
use App\Models\Pet;
use App\Models\Worker;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    function generarMesesRetroactivos($mes, $año) {
  
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        
        // Convertir el nombre del mes a minúsculas para comparación
        // $mes = strtolower($mes);
        
        // Obtener el número del mes
        $numero_mes = array_search($mes, $meses);
        
        if ($numero_mes === false) {
            return "Error: El mes '$mes' no es válido.";
        }
        
        $resultado = [];
        
        // Empezamos desde el mes siguiente al indicado del año anterior
        $mes_actual = $numero_mes + 1;
        $año_actual = $año - 1;
        
        // Generar los 12 meses
        for ($i = 0; $i < 12; $i++) {
            // Si el mes actual es mayor a 12, ajustar a enero del año siguiente
            if ($mes_actual > 12) {
                $mes_actual = 1;
                $año_actual++;
            }
            
            // Agregar el mes al resultado con número y año
            $resultado[] = [
                'month_number' => $mes_actual,
                'year' => $año_actual,
                'month' => $meses[$mes_actual], // Opcional: por si necesitas el nombre también
                'amount' => 0 // Inicializar el monto en 0
            ];
            
            // Avanzar al siguiente mes
            $mes_actual++;
        }
        
        return $resultado;
    }

    function generarDiasSemana($fecha, $sales, $vaccinations , $anamnesis, $hairSalons, $dewormings, $homeServices) {
        // Días de la semana en español
        $daysWeek = [
            'Domingo', 'Lunes', 'Martes', 'Miércoles', 
            'Jueves', 'Viernes', 'Sábado'
        ];
        
        // Convertir la fecha a objeto DateTime
        $fecha_obj = new DateTime($fecha);
        
        // Retroceder 6 días para empezar desde 7 días antes de la fecha dada
        $fecha_inicio = clone $fecha_obj;
        $fecha_inicio->modify("-6 days");
        
        $resultado = [];
        
        // Generar los 7 días de la semana empezando desde 6 días antes
        for ($i = 0; $i < 7; $i++) {
            // Agregar el día al resultado
            $numero_dia_semana = (int)$fecha_inicio->format('w'); // 0=domingo, 6=sábado
            $nombre_dia = $daysWeek[$numero_dia_semana];
            
            $resultado[] = [
                'date' => $fecha_inicio->format('Y-m-d'),
                'dateInverso' => $fecha_inicio->format('d-m-Y'),
                'day' => $numero_dia_semana,
                'name' => $nombre_dia,
                'amount' => 0 // Inicializar el monto en 0
            ];
            
            // Avanzar al siguiente día
            $fecha_inicio->modify('+1 day');
        }
        
        // calculamos el total de las ventas a día
        foreach ($resultado as $index => $dayData) {
            $amount = 0;
            $amount += $sales->where('deleted_at', null)->filter(function ($sale) use ($dayData) {
                return $sale->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');

            $amount += $vaccinations->where('deleted_at', null)->filter(function ($vaccination) use ($dayData) {
                return $vaccination->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');

            $amount += $anamnesis->where('deleted_at', null)->filter(function ($anamnesi) use ($dayData) {
                return $anamnesi->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');

            $amount += $hairSalons->where('deleted_at', null)->filter(function ($hairSalon) use ($dayData) {
                return $hairSalon->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');

            $amount += $dewormings->where('deleted_at', null)->filter(function ($deworming) use ($dayData) {
                return $deworming->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');

            $amount += $homeServices->where('deleted_at', null)->filter(function ($homeService) use ($dayData) {
                return $homeService->created_at->format('Y-m-d') === $dayData['date'];
            })->sum('amount');

            $resultado[$index]['amount'] = $amount;           
        }
        
        return $resultado;
    }


    public function productTop5Day($date ,$sales, $vaccinations , $anamnesis)
    {

        $sales = $sales->where('deleted_at', null)->filter(function ($sale) use ($date) {
                    return $sale->created_at->format('Y-m-d') === $date;
                });

        $vaccinations = $vaccinations->where('deleted_at', null)->filter(function ($vaccination) use ($date) {
                    return $vaccination->created_at->format('Y-m-d') === $date;
                });

        $anamnesis = $anamnesis->where('deleted_at', null)->filter(function ($anamnesi) use ($date) {
                    return $anamnesi->created_at->format('Y-m-d') === $date;
                });
        // 1. Recolectar todos los detalles de venta "saleDetails"
        $saleDetails = $sales->flatMap->saleDetails;
        $vaccinationDetails = $vaccinations->flatMap->dispensions;
        $anamnesisDetails = $anamnesis->flatMap->dispensions;

        $allDetails = $saleDetails->concat($vaccinationDetails)->concat($anamnesisDetails);

        // 2. Agrupar por producto y sumar cantidades
        $productSales = [];

        foreach ($allDetails as $detail) {
            if($detail->itemStock && $detail->itemStock->item){
                $itemId = $detail->itemStock->id;
                $itemName = $detail->itemStock->item->nameGeneric.' | '.$detail->itemStock->item->nameTrade; // Ajusta según tu campo
                $quantity = $detail->quantity; // Ajusta según tu campo de cantidad
                
                if (!isset($productSales[$itemId])) {
                    $productSales[$itemId] = [
                        'name' => $itemName,
                        'total_quantity' => 0,
                        'item_id' => $itemId
                    ];
                }
                
                $productSales[$itemId]['total_quantity'] += $quantity;
            }
        }

        // 3. Ordenar por cantidad descendente
        usort($productSales, function($a, $b) {
            return $b['total_quantity'] - $a['total_quantity'];
        });

        // 4. Obtener los top 5
        $top5Products = array_slice($productSales, 0, 5);
        // Resultado
        // return response()->json($top5Products);
        return $top5Products;
    }
    
    public function IndexSystem($typeCashier)
    {
        $month = date('m');
        $year = date('Y');
        $day = date('d');

        $meses = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');       

        $monthInteractive = $this->generarMesesRetroactivos($meses[intval($month)], $year);
 


        $startDate = $monthInteractive[0]['year'] . '-' . str_pad($monthInteractive[0]['month_number'], 2, '0', STR_PAD_LEFT) . '-01';
        $endDate = date('Y-m-t', strtotime($monthInteractive[11]['year'] . '-' . str_pad($monthInteractive[11]['month_number'], 2, '0', STR_PAD_LEFT) . '-01'));


        // Si el tipo es "Todo", no aplicamos filtro. De lo contrario, filtramos por el tipo de venta.
        if ($typeCashier && $typeCashier !== 'Todo') {
            $typeCashier = "sale = '$typeCashier'";
        } else {
            $typeCashier = 1; // Condición que siempre es verdadera para no filtrar
        }
        // dump($typeCashier);

        // Primero obtienes los cashiers filtrados
        $cashiers = Cashier::where('deleted_at', null)
            ->whereRaw($typeCashier)
            ->get();

        // Extraes los IDs de los cashiers
        $cashierIds = $cashiers->pluck('id');

        // ###########################################################################################
        // ###########################################################################################

        $sales = Sale::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        $vacinationRecords = VaccinationRecord::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();
        
        $anamnesisForms = AnamnesisForm::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();
        
        $hairSalons = HairSalon::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        $dewormings = Deworming::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        $homeServices = HomeService::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            // ->whereIn('cashier_id', $cashierIds) // Filtra por los IDs de los cashiers
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        // ###########################################################################################
        // ###########################################################################################

        $amountDaytotalSale = $sales->where('deleted_at', null)
                ->filter(function ($sale) {
                    return $sale->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');

        $amountDaytotalVaccination = $vacinationRecords->where('deleted_at', null)
                ->filter(function ($vacinationRecord) {
                    return $vacinationRecord->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');

        $amountDaytotalAnamnesis = $anamnesisForms->where('deleted_at', null)
                ->filter(function ($anamnesisForm) {
                    return $anamnesisForm->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');

        $amountDaytotalHairSalon = $hairSalons->where('deleted_at', null)
                ->filter(function ($hairSalon) {
                    return $hairSalon->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');    
        
        $amountDaytotalDeworming = $dewormings->where('deleted_at', null)
                ->filter(function ($deworming) {
                    return $deworming->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');

        $amountDaytotalHomeService = $homeServices->where('deleted_at', null)
                ->filter(function ($homeService) {
                    return $homeService->created_at->format('Y-m-d') === date('Y-m-d');
                })
                ->sum('amount');

        // Para obtener el total de las ventas o servicios del día
        $amountDaytotal = $amountDaytotalSale+$amountDaytotalVaccination+$amountDaytotalAnamnesis +$amountDaytotalHairSalon+$amountDaytotalDeworming+$amountDaytotalHomeService;
        // dd($amountDaytotal);


        $today = date('Y-m-d');
        $count_reminder = Reminder::where('deleted_at', null)->whereDate('date', $today)->count();
        $count_vaccine = VaccinationRecord::where('deleted_at', null)->whereDate('nextDate', $today)->count();
        $count_deworming = Deworming::where('deleted_at', null)->whereDate('nextDate', $today)->count();
        $count_hair = HairSalon::where('deleted_at', null)->whereDate('nextDate', $today)->count();
        $count_service = HomeService::where('deleted_at', null)->whereDate('nextDate', $today)->count();
        $reminder = $count_reminder + $count_vaccine + $count_deworming + $count_hair + $count_service;

        // calculamos el total de las ventas o servicio al mes
        foreach ($monthInteractive as $index => $monthData) {
            $amount=0;
            $amount+= $sales->where('deleted_at', null)->filter(function ($sale) use ($monthData) {
                $saleDate = Carbon::parse($sale->created_at);
                return $saleDate->year == $monthData['year'] && 
                    $saleDate->month == $monthData['month_number'];
            })->sum('amount');
            $amount+= $vacinationRecords->where('deleted_at', null)->filter(function ($vacinationRecord) use ($monthData) {
                $vacinationRecordDate = Carbon::parse($vacinationRecord->created_at);
                return $vacinationRecordDate->year == $monthData['year'] && 
                    $vacinationRecordDate->month == $monthData['month_number'];
            })->sum('amount');
            
            $amount+= $anamnesisForms->where('deleted_at', null)->filter(function ($anamnesisForm) use ($monthData) {
                $anamnesisFormsDate = Carbon::parse($anamnesisForm->created_at);
                return $anamnesisFormsDate->year == $monthData['year'] && 
                    $anamnesisFormsDate->month == $monthData['month_number'];
            })->sum('amount');

            $amount+= $hairSalons->where('deleted_at', null)->filter(function ($hairSalon) use ($monthData) {
                $hairSalonsDate = Carbon::parse($hairSalon->created_at);
                return $hairSalonsDate->year == $monthData['year'] && 
                    $hairSalonsDate->month == $monthData['month_number'];
            })->sum('amount');

            $amount+= $dewormings->where('deleted_at', null)->filter(function ($deworming) use ($monthData) {
                $dewormingsDate = Carbon::parse($deworming->created_at);
                return $dewormingsDate->year == $monthData['year'] && 
                    $dewormingsDate->month == $monthData['month_number'];
            })->sum('amount');

            $amount+= $homeServices->where('deleted_at', null)->filter(function ($homeService) use ($monthData) {
                $homeServicesDate = Carbon::parse($homeService->created_at);
                return $homeServicesDate->year == $monthData['year'] && 
                    $homeServicesDate->month == $monthData['month_number'];
            })->sum('amount');

            $monthInteractive[$index]['amount'] = $amount;           
        }

        // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // Para obtener el top 5 de productos del día
        $productTop5Day = $this->productTop5Day(date('Y-m-d'), $sales, $vacinationRecords , $anamnesisForms);


        // ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
        // Para obtener las ventas del día de la semana 
        $weekDays = $this->generarDiasSemana(date('Y-m-d'), $sales, $vacinationRecords, $anamnesisForms, $hairSalons, $dewormings, $homeServices);

        $people = Person::where('deleted_at', null)->get()->count();//Para obtener la cantidad de clientes 
        $pet = Pet::where('deleted_at', null)->get()->count();//Para obtener la cantidad de mascotas



        
        // Cumpleaños
        $todayMonthDay = Carbon::now()->format('m-d');
        $peopleBirthdayCount = Person::whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') = ?", [$todayMonthDay])
            ->count();
        $workerBirthdayCount = Worker::whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') = ?", [$todayMonthDay])
            ->count();
        $todayBirthdaysCount = $peopleBirthdayCount + $workerBirthdayCount;

        $today = Carbon::today();
        $endOfYear = Carbon::today()->endOfYear();

        $peopleBirthdays = Person::whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$today->format('m-d')])
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$endOfYear->format('m-d')])
            ->select('id', 'first_name', 'middle_name', 'paternal_surname', 'maternal_surname', 'birth_date', 'image', 'phone', 'country_code', DB::raw("'Cliente' as type"))
            ->get();

        $workerBirthdays = Worker::whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$today->format('m-d')])
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$endOfYear->format('m-d')])
            ->select('id', 'first_name', 'middle_name', 'paternal_surname', 'maternal_surname', 'birth_date', 'image', 'phone', 'country_code', DB::raw("'Empleado' as type"))
            ->get();

        $upcomingBirthdays = $peopleBirthdays->concat($workerBirthdays)->map(function ($item) use ($today) {
            $birthDateThisYear = Carbon::createFromFormat('Y-m-d', $item->birth_date)->year($today->year);
            $item->next_birthday = $birthDateThisYear->isBefore($today) ? $birthDateThisYear->addYear() : $birthDateThisYear;
            return $item;
        })->sortBy('next_birthday');
    
        return response()->json([
            'day' => $day,
            'month' => $month,
            'year' => $year,
            'monthInteractive' => $monthInteractive,
            'sales'=> $sales,
            'amountDaytotal'=> $amountDaytotal,
            'reminder'=>$reminder,
            'customer' => $people,
            'pet' => $pet,
            'productTop5Day' => $productTop5Day,
            'weekDays' => $weekDays,
            'todayBirthdaysCount' => $todayBirthdaysCount,
            'upcomingBirthdays' => $upcomingBirthdays->values()->all(),


            // Para mostrar el monto de las ventas
            // 'amountQrDay' => $amountQrDay, // total ventas del día por tipo de pago Qr
            // 'amountEfectivoDay' => $amountEfectivoDay // total ventas del día por tipo de pago Efectivo
        ]);
    }
}
