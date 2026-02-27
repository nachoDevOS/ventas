<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Person;
use App\Models\SaleTransaction;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function generarMesesRetroactivos($mes, $año)
    {
        $meses = [
            1 => 'Enero',    2 => 'Febrero',   3 => 'Marzo',
            4 => 'Abril',    5 => 'Mayo',       6 => 'Junio',
            7 => 'Julio',    8 => 'Agosto',     9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];

        $numero_mes = array_search($mes, $meses);
        if ($numero_mes === false) {
            return [];
        }

        $resultado  = [];
        $mes_actual = $numero_mes + 1;
        $año_actual = $año - 1;

        for ($i = 0; $i < 12; $i++) {
            if ($mes_actual > 12) {
                $mes_actual = 1;
                $año_actual++;
            }
            $resultado[] = [
                'month_number' => $mes_actual,
                'year'         => $año_actual,
                'month'        => $meses[$mes_actual],
                'amount'       => 0,
            ];
            $mes_actual++;
        }

        return $resultado;
    }

    private function generarDiasSemana($fecha, $sales)
    {
        $daysWeek = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

        $fecha_inicio = (new \DateTime($fecha))->modify('-6 days');

        $resultado = [];
        for ($i = 0; $i < 7; $i++) {
            $num = (int) $fecha_inicio->format('w');
            $resultado[] = [
                'date'        => $fecha_inicio->format('Y-m-d'),
                'dateInverso' => $fecha_inicio->format('d-m-Y'),
                'day'         => $num,
                'name'        => $daysWeek[$num],
                'amount'      => 0,
            ];
            $fecha_inicio->modify('+1 day');
        }

        foreach ($resultado as $index => $dayData) {
            $resultado[$index]['amount'] = $sales
                ->where('deleted_at', null)
                ->filter(fn($sale) => $sale->created_at->format('Y-m-d') === $dayData['date'])
                ->sum('amount');
        }

        return $resultado;
    }

    private function productTop5Day($date, $sales)
    {
        $todaySales = $sales
            ->where('deleted_at', null)
            ->filter(fn($sale) => $sale->created_at->format('Y-m-d') === $date);

        $productSales = [];

        foreach ($todaySales->flatMap->saleDetails as $detail) {
            if ($detail->itemStock && $detail->itemStock->item) {
                $itemId   = $detail->itemStock->id;
                $itemName = $detail->itemStock->item->nameGeneric
                          . ($detail->itemStock->item->nameTrade ? ' | ' . $detail->itemStock->item->nameTrade : '');

                if (!isset($productSales[$itemId])) {
                    $productSales[$itemId] = [
                        'name'           => $itemName,
                        'total_quantity' => 0,
                        'item_id'        => $itemId,
                    ];
                }
                $productSales[$itemId]['total_quantity'] += $detail->quantity;
            }
        }

        usort($productSales, fn($a, $b) => $b['total_quantity'] - $a['total_quantity']);

        return array_slice($productSales, 0, 5);
    }

    public function IndexSystem()
    {
        $month = (int) date('m');
        $year  = (int) date('Y');
        $day   = (int) date('d');

        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        $monthInteractive = $this->generarMesesRetroactivos($meses[$month], $year);

        $startDate = $monthInteractive[0]['year']
            . '-' . str_pad($monthInteractive[0]['month_number'], 2, '0', STR_PAD_LEFT)
            . '-01';
        $endDate = date('Y-m-t', strtotime(
            $monthInteractive[11]['year']
            . '-' . str_pad($monthInteractive[11]['month_number'], 2, '0', STR_PAD_LEFT)
            . '-01'
        ));

        // ── Ventas (12 meses) ─────────────────────────────────────────────────
        $sales = Sale::with(['saleDetails.itemStock.item'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->withTrashed()
            ->orderBy('created_at', 'DESC')
            ->get();

        // ── Total del día ─────────────────────────────────────────────────────
        $today         = date('Y-m-d');
        $todaySalesCol = $sales
            ->where('deleted_at', null)
            ->filter(fn($s) => $s->created_at->format('Y-m-d') === $today);

        $amountDaytotal    = (float) $todaySalesCol->sum('amount');
        $saleDaytotalCount = $todaySalesCol->count();

        // ── Tipo de venta del día (Contado / Crédito / Proforma) ──────────────
        $types = ['Venta al Contado', 'Venta al Credito', 'Proforma'];
        $typeSaleBreakdown = array_map(function ($type) use ($todaySalesCol) {
            $group = $todaySalesCol->where('typeSale', $type);
            return [
                'type'   => $type,
                'amount' => (float) $group->sum('amount'),
                'count'  => $group->count(),
            ];
        }, $types);

        // ── Método de pago del día (Efectivo / QR) ────────────────────────────
        $paymentBreakdown = SaleTransaction::whereNull('deleted_at')
            ->whereHas('sale', fn($q) => $q->whereDate('created_at', $today)->whereNull('deleted_at'))
            ->selectRaw('paymentType, SUM(amount) as total, COUNT(*) as qty')
            ->groupBy('paymentType')
            ->get()
            ->map(fn($r) => [
                'type'   => $r->paymentType,
                'amount' => (float) $r->total,
                'count'  => (int) $r->qty,
            ])
            ->values()
            ->all();

        // ── Recordatorios del día (sin modelo disponible) ─────────────────────
        $reminder = 0;

        // ── Ventas mensuales ──────────────────────────────────────────────────
        foreach ($monthInteractive as $index => $monthData) {
            $monthInteractive[$index]['amount'] = $sales
                ->where('deleted_at', null)
                ->filter(function ($sale) use ($monthData) {
                    return $sale->created_at->year  == $monthData['year']
                        && $sale->created_at->month == $monthData['month_number'];
                })
                ->sum('amount');
        }

        // ── Top 5 productos del día ───────────────────────────────────────────
        $productTop5Day = $this->productTop5Day($today, $sales);

        // ── Ventas últimos 7 días ─────────────────────────────────────────────
        $weekDays = $this->generarDiasSemana($today, $sales);

        // ── KPIs extra ───────────────────────────────────────────────────────
        $people = Person::whereNull('deleted_at')->count();

        // ── Cumpleaños ────────────────────────────────────────────────────────
        $todayMonthDay = Carbon::now()->format('m-d');

        $todayBirthdaysCount = Person::whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') = ?", [$todayMonthDay])
            ->count();

        $carbonToday = Carbon::today();
        $endOfYear   = Carbon::today()->endOfYear();

        $upcomingBirthdays = Person::whereNull('deleted_at')
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') >= ?", [$carbonToday->format('m-d')])
            ->whereRaw("DATE_FORMAT(birth_date, '%m-%d') <= ?", [$endOfYear->format('m-d')])
            ->select('id', 'first_name', 'middle_name', 'paternal_surname', 'maternal_surname',
                     'birth_date', 'image', 'phone', 'country_code', DB::raw("'Cliente' as type"))
            ->get()
            ->map(function ($item) use ($carbonToday) {
                $bday = Carbon::createFromFormat('Y-m-d', $item->birth_date)->year($carbonToday->year);
                $item->next_birthday = $bday->isBefore($carbonToday) ? $bday->addYear() : $bday;
                return $item;
            })
            ->sortBy('next_birthday');

        return response()->json([
            'day'               => $day,
            'month'             => $month,
            'year'              => $year,
            'monthInteractive'  => $monthInteractive,
            'amountDaytotal'    => $amountDaytotal,
            'saleDaytotalCount' => $saleDaytotalCount,
            'reminder'          => $reminder,
            'customer'          => $people,
            'pet'               => 0,
            'productTop5Day'    => $productTop5Day,
            'weekDays'          => $weekDays,
            'typeSaleBreakdown' => $typeSaleBreakdown,
            'paymentBreakdown'  => $paymentBreakdown,
            'todayBirthdaysCount'  => $todayBirthdaysCount,
            'upcomingBirthdays'    => $upcomingBirthdays->values()->all(),
        ]);
    }
}
