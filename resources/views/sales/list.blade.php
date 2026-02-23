<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    {{-- <th style="text-align: center; width: 15%">Codigo</th> --}}
                    <th style="text-align: center; width: 4%">Id</th>
                    <th style="text-align: center">Cliente</th>
                    <th style="text-align: center; width: 8%">Monto</th>     
                    <th style="text-align: center">Detalles</th>

                    <th style="text-align: center; width: 15%">Fecha Venta</th>
                    <th style="text-align: center; width: 12%">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                <tr>
                    <td style="text-align: center">{{ $item->id }}</td>
                    <td>
                        @if ($item->person)
                            @php
                                $image = asset('images/default.jpg');
                                if ($item->person->image) {
                                    $image = asset('storage/' . str_replace('.avif', '', $item->person->image) . '-cropped.webp');
                                }
                            @endphp
                            <div style="display: flex; align-items: center;">
                                <img src="{{ $image }}" alt="{{ $item->person->first_name }}" class="image-expandable"
                                    style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px; object-fit: cover;">
                                <div>
                                    <b style="font-size: 15px;">{{ strtoupper($item->person->first_name) }} {{ $item->person->middle_name ?? '' }} {{ strtoupper($item->person->paternal_surname) }} {{ strtoupper($item->person->maternal_surname) }}</b><br>
                                    <small>CI: {{ $item->person->ci }}</small>
                                </div>
                            </div>
                        @else
                            Sin Datos 
                        @endif                        
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        @php
                            $paymentQr = $item->saleTransactions->where('paymentType', 'Qr')->sum('amount') > 0;
                            $paymentEfectivo = $item->saleTransactions->where('paymentType', 'Efectivo')->sum('amount') > 0;
                        @endphp
                        <b style="font-size: 16px;">{{ number_format($item->amount, 2, ',', '.') }}</b><br>
                        @if ($paymentEfectivo && $paymentQr)
                            <span class="label label-info" style="font-size: 10px; background-color: #5bc0de;">Ambos</span>
                        @elseif ($paymentEfectivo)
                            <span class="label label-success" style="font-size: 10px; background-color: #5cb85c;">
                                <i class="fa-solid fa-money-bill-1-wave"></i> Efectivo
                            </span>
                        @elseif ($paymentQr)
                            <span class="label label-primary" style="font-size: 10px; background-color: #337ab7;"><i class="fa-solid fa-qrcode"></i> QR</span>
                        @endif
                        {{-- @if ($item->status!='Pendiente')  
                            <label class="label label-success" style="padding: 5px 10px; font-size: 12px;">
                                <i class="voyager-check"></i> 
                                Pagado
                            </label>
                        @else
                            <label class="label label-warning" style="padding: 5px 10px; font-size: 12px;">
                                <i class="voyager-watch"></i>
                                Pendiente
                            </label>
                        @endif   --}}
                    </td>
              
                    <td style="text-align: center">

                        <table style="width: 100%; font-size: 12px;">
                            @foreach ($item->saleDetails->groupBy('itemStock_id') as $details)
                                @php
                                    $detail = $details->first();
                                @endphp
                                <tr>
                                    <td>
                                        <small style="font-size: 10px">{{strtoupper($detail->itemStock->item->nameGeneric)}} {{ $detail->itemStock->item->nameTrade? '  |  '.strtoupper($detail->itemStock->item->nameTrade):null }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </td>


                    <td style="text-align: center; vertical-align: middle;">
                        <i class="fa-solid fa-user"></i> <small>{{\Illuminate\Support\Str::words($item->register->name, 2, '')}}</small> <br>
                        <b style="font-size: 12px">{{date('d/m/Y h:m:s a', strtotime($item->dateSale))}}</b> <br>
                        <small>{{\Carbon\Carbon::parse($item->dateSale)->diffForHumans()}}</small>
                    </td>
          
                    <td style="width: 12%" class="no-sort no-click bread-actions text-right">
                        {{-- <a onclick="handlePrintClick(this,'{{ setting('print.typePrint') }}', '{{ setting('print.url') }}', '{{ setting('print.ip') }}', '{{ setting('print.port') }}', '{{ setting('admin.print') }}', '{{ setting('admin.title') }}', {{ json_encode($item) }}, '{{ url('admin/sales/ticket') }}')"  title="Imprimiendo..." class="btn btn-sm btn-dark print-btn">
                            <i class="fa-solid fa-print"></i>
                        </a> --}}
                        
                        @if ($item->status == 'Pendiente')
                            <a onclick="successItem('{{ route('sales-status.success', ['id' => $item->id]) }}')" data-toggle="modal" data-target="#success-modal" title="Entregar Pedido" class="btn btn-sm btn-success">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        @endif
                        
                        @if (auth()->user()->hasPermission('read_sales'))
                            <a href="{{ route('sales.show', ['sale' => $item->id]) }}" title="Ver" class="btn btn-sm btn-warning view">
                                <i class="voyager-eye"></i>
                            </a>
                        @endif
                        @if (auth()->user()->hasPermission('edit_sales'))
                            <a href="{{ route('sales.edit', ['sale' => $item->id]) }}" title="Editar" class="btn btn-sm btn-primary edit">
                                <i class="voyager-edit"></i>
                            </a>
                        @endif
                        
                        @if (auth()->user()->hasPermission('delete_sales'))
                            <a href="#" onclick="deleteItem('{{ route('sales.destroy', ['sale' => $item->id]) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                <i class="voyager-trash"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center" style="margin-top: 50px">
                                <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                                <br><br>
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if(count($data)>0)
            <p class="text-muted">Mostrando del {{$data->firstItem()}} al {{$data->lastItem()}} de {{$data->total()}} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $data->links() }}
        </nav>
    </div>
</div>

<script>
    async function handlePrintClick(element, typePrint, url, ip, port, print, title, sale, fallbackUrl) {
        const button = $(element);
        const icon = button.find('i');
        const originalIconClass = icon.attr('class');

        // Evitar múltiples clics si ya se está procesando
        if (button.hasClass('disabled')) {
            return;
        }

        // Cambiar ícono a spinner y desactivar botón
        button.addClass('disabled');
        icon.removeClass(originalIconClass).addClass('fa-solid fa-spinner fa-spin');

        try {
            await printTicket(typePrint, url, ip, port, print, title, sale, fallbackUrl);
        } finally {
            // Restaurar el botón después de un par de segundos para que el usuario vea el resultado (toastr)
            setTimeout(() => {
                button.removeClass('disabled');
                icon.removeClass('fa-solid fa-spinner fa-spin').addClass(originalIconClass);
            }, 2000);
        }
    }
   
   var page = "{{ request('page') }}";
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            if(link){
                page = link.split('=')[1];
                list(page);
            }
        });
    });
</script>