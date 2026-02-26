@extends('voyager::master')

@section('page_title', isset($sale) ? 'Editar Venta' : 'Añadir Venta')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body" style="padding: 0px">
                        <div class="col-md-8" style="padding: 0px">
                            <h1 class="page-title">
                                <i class="fa-solid fa-cart-plus"></i> {{ isset($sale) ? 'Editar Venta' : 'Añadir Venta' }}
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px">
                            <a href="{{ route('sales.index') }}" class="btn btn-warning">
                                <i class="voyager-plus"></i> <span>Volver</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <form class="form-edit-add" action="{{ isset($sale) ? route('sales.update', ['sale' => $sale->id]) : route('sales.store') }}" method="post">
            @csrf
            @if (isset($sale))
                @method('PUT')
            @endif
            <div class="row">
                {{-- @if (!$globalFuntion_cashierMoney['cashier'])
                    <div class="col-md-12 col-sm-12">
                        <div class="panel panel-bordered alert alert-warning">
                            <strong><i class="voyager-info-circled"></i> Advertencia:</strong>
                            <p class="mt-1">No puedes realizar ventas porque no tienes una caja abierta.</p>
                        </div>
                    </div>
                @endif --}}
                <div class="col-md-12">
                    <style>
                        .payment-panel-container .form-group {
                            margin-bottom: 20px;
                        }
                        .payment-summary {
                            background-color: #f9f9f9;
                            border: 1px solid #eee;
                            border-radius: 8px;
                            padding: 15px;
                            margin-top: 15px;
                        }
                        .payment-summary ul {
                            list-style: none;
                            padding: 0;
                            margin: 0;
                        }
                        .payment-summary li {
                            display: flex;
                            justify-content: space-between;
                            padding: 8px 0;
                            border-bottom: 1px solid #eee;
                            font-size: 1.1em;
                        }
                        .payment-summary li:last-child {
                            border-bottom: none;
                        }
                        .payment-summary .total-pay-li {
                            font-size: 1.4em;
                            font-weight: bold;
                            color: #333;
                            padding-top: 12px;
                            border-top: 2px solid #ddd;
                            margin-top: 10px;
                        }
                        .payment-summary .total-pay-li .value {
                            color: #27ae60;
                        }
                        .change-display {
                            font-size: 1.3em;
                            font-weight: bold;
                            color: #3498db;
                            text-align: right;
                            margin-top: 10px;
                        }
                        .payment-error {
                            font-size: 1em;
                            font-weight: bold;
                            color: #e74c3c;
                            text-align: right;
                            margin-top: 10px;
                        }
                        .input-group-addon {
                            background-color: #ecf0f1;
                            border-right: 0;
                        }
                    </style>
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa-solid fa-money-bill"></i> DATOS DE LA VENTA</h3>
                        </div>
                        <div class="panel-body payment-panel-container" style="padding: 15px;">
                            <div class="row">
                                @if (setting('admin.customer'))  
                                    <div class="form-group col-md-12">
                                        <label for="person_id">Cliente</label>
                                        <div class="input-group">
                                            <select name="person_id" id="select-person_id" class="form-control" @if(isset($sale) && $sale->person_id) data-id="{{$sale->person_id}}" data-text="{{$sale->person->first_name}} {{$sale->person->paternal_surname}}" @endif></select>
                                            <span class="input-group-btn">
                                                <button id="trash-person" class="btn btn-default" title="Quitar Cliente" style="margin: 0px" type="button"><i class="voyager-trash"></i></button>
                                                <button class="btn btn-primary" title="Nuevo cliente" data-target="#modal-create-person" data-toggle="modal" style="margin: 0px" type="button"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></button>
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <input type="hidden" name="typeSale" id="typeSale" value="{{ isset($sale) ? $sale->typeSale : 'Venta al Contado' }}">
                                
                                <div class="form-group col-md-12">
                                    <label for="payment_type">Método de pago</label>
                                    <select name="payment_type" id="select-payment_type" class="form-control select2" required>
                                        <option value="" disabled selected>--Seleccione una opción--</option>
                                        <option value="Efectivo" @if(isset($sale) && $sale->payment_type == 'Efectivo') selected @endif>Efectivo</option>
                                        <option value="Qr" @if(isset($sale) && $sale->payment_type == 'Qr') selected @endif>Qr/Transferencia</option>                                    
                                        <option value="Efectivo y Qr" @if(isset($sale) && ($sale->payment_type == 'Efectivo y Qr' || $sale->payment_type == 'Ambos')) selected @endif>Efectivo y Qr</option>
                                    </select>
                                </div>
                                <div id="cash-payment-section" class="form-group col-md-12" style="display: none;">
                                    <label for="amount_cash">Monto en Efectivo</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa-solid fa-money-bill-wave"></i></span>
                                        <input type="number" name="amount_cash" id="amount_cash" class="form-control" value="{{ isset($sale) ? $sale->amount_cash : 0 }}" step="0.01" style="text-align: right" placeholder="0.00">
                                    </div>
                                </div>
                                <div id="qr-payment-section" class="form-group col-md-12" style="display: none;">
                                    <label for="amount_qr">Monto en Qr/Transferencia</label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa-solid fa-qrcode"></i></span>
                                        <input type="number" name="amount_qr" id="amount_qr" class="form-control" value="{{ isset($sale) ? $sale->amount_qr : 0 }}" step="0.01" style="text-align: right" placeholder="0.00">
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="payment-summary">
                                        <ul>
                                            <li class="total-pay-li"><span>Total a Pagar:</span> <span class="value">Bs. <b id="label-total">0.00</b></span></li>
                                        </ul>
                                    </div>
                                    <div id="change-message-error" class="payment-error" style="display: none;"><small>Error en el monto</small></div>
                                    <div id="change-message-error-credito" class="payment-error" style="display: none;"><small>Monto excede la deuda</small></div>
                                    <div id="change-message" class="change-display" style="display: none;">Cambio: Bs. <b id="change-amount">0.00</b></div>
                                    <div id="change-message-credito" class="change-display" style="display: none;">Deuda Pendiente: Bs. <b id="change-amount-credito">0.00</b></div>

                                    <input type="hidden" name="amountReceived" id="amountReceived" value="0">
                                    <input type="hidden" id="amountTotalSale" name="amountTotalSale" value="0">
                                </div>

                                <div class="form-group col-md-12" style="margin-top: 20px;">
                                    <label class="checkbox-inline"><input type="checkbox" required> Confirmar y finalizar registro</label>
                                </div>

                                <div class="form-group col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-block save btn-submit" disabled>
                                        <i class="voyager-basket"></i> {{ isset($sale) ? 'Actualizar' : 'Registrar' }}
                                    </button>
                                    <a href="{{ route('sales.index') }}" class="btn btn-link" style="margin-top:10px;">Cancelar y Volver</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title"><i class="fa-solid fa-pills"></i> PRODUCTOS</h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group col-md-12">
                                <label for="product_id">Buscar producto</label>
                                <select class="form-control" id="select-product_id"></select>
                            </div>
                            <div class="col-md-12" style="height: 800px; max-height: 400px; overflow-y: auto">
                                <div class="table-responsive">
                                    <table id="dataTable" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">N&deg;</th>
                                                <th style="">Detalles</th>
                                                <th style="text-align: center; width:15%">Precio</th>
                                                <th style="text-align: center; width:8%">Cantidad</th>
                                                <th style="text-align: center; width:12%">Descuento</th>
                                                <th style="text-align: center; width:15%">Subtotal</th>
                                                <th style="width: 1%"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body">
                                            <tr id="tr-empty" @if(isset($sale) && count($sale->saleDetails) > 0) style="display: none" @endif>
                                                <td colspan="6" style="height: 320px">
                                                    <h4 class="text-center text-muted" style="margin-top: 50px">
                                                        <i class="glyphicon glyphicon-shopping-cart"
                                                            style="font-size: 50px"></i> <br><br>
                                                        Lista de venta vacía
                                                    </h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="observation">Observaciones</label>
                                <textarea name="observation" class="form-control" rows="2" placeholder="Observaciones">{{ isset($sale) ? $sale->observation : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Popup para imprimir el recibo --}}
    <div id="popup-button">
        <div class="col-md-12" style="padding-top: 5px">
            <h4 class="text-muted">Desea imprimir el recibo?</h4>
        </div>
        <div class="col-md-12 text-right">
            <button onclick="javascript:$('#popup-button').fadeOut('fast')" class="btn btn-default">Cerrar</button>
            <a id="btn-print" href="#" target="_blank" title="Imprimir" class="btn btn-danger">Imprimir <i
                    class="glyphicon glyphicon-print"></i></a>
        </div>
    </div>

    {{-- Modal crear cliente --}}
    @include('partials.modal-registerPerson')
@stop

@section('css')
    <style>
        .form-group {
            margin-bottom: 10px !important;
        }

        .label-description {
            cursor: pointer;
        }

        #popup-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 400px;
            height: 100px;
            background-color: white;
            box-shadow: 5px 5px 15px grey;
            z-index: 1000;

            /* Mostrar/ocultar popup */
            @if (session('sale_id'))
                animation: show-animation 1s;
            @else
                right: -500px;
            @endif
        }

        @keyframes show-animation {
            0% {
                right: -500px;
            }

            100% {
                right: 20px;
            }
        }

        /* Estilos para mejorar la legibilidad en el selector de productos */
        .select2-results__option--highlighted {
            background-color: #f5f5f5 !important;
            color: #333 !important;
        }
        .select2-results__option--highlighted div, .select2-results__option--highlighted span, .select2-results__option--highlighted strong, .select2-results__option--highlighted label {
            color: #333;
        }
        .select2-results__option--highlighted .select2-results__option__highlighted {
            color: #22A7F0 !important;
        }
        .select2-results__option .select2-results__option__highlighted {
            font-weight: bold;
            color: #22A7F0;
        }
    </style>
@endsection

@section('javascript')
    <script src="{{ asset('js/btn-submit.js') }}"></script>
    <script src="{{ asset('js/input-numberBlock.js') }}"></script>

    <script src="{{ asset('js/include/person-select.js') }}"></script>
    <script src="{{ asset('js/include/person-register.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.min.js"></script>

    <script src="{{ asset('vendor/tippy/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/tippy/tippy-bundle.umd.min.js') }}"></script>
    <script>
        var productSelected, customerSelected, totalAmount = 0;
        var originalSaleDetails = {};

        $(document).ready(function() {
            $('<style>.select2-results__options { max-height: 450px !important; }</style>').appendTo('head');

            $('#select-product_id').select2({
                width: '100%',
                placeholder: '<i class="fa fa-search"></i> Buscar...',
                escapeMarkup: function(markup) {
                    return markup;
                },
                language: {
                    inputTooShort: function(data) {
                        return `Por favor ingrese ${data.minimum - data.input.length} o más caracteres`;
                    },
                    noResults: function() {
                        return `<i class="far fa-frown"></i> No hay resultados encontrados`;
                    }
                },
                quietMillis: 250,
                minimumInputLength: 2,
                ajax: {
                    url: "{{ url('admin/item/stock/ajax') }}",

                    processResults: function(data) {
                        let results = [];
                        data.map(data => {
                            results.push({
                                ...data,
                                disabled: false
                            });
                        });
                        return {
                            results
                        };
                    },
                    cache: true
                },
                templateResult: formatResultProducts,
                templateSelection: (opt) => {
                    productSelected = opt;
                    return productSelected.id;
                },
                escapeMarkup: function (markup) { return markup; }
            }).change(function() {

                if ($('#select-product_id option:selected').val()) {
                    let product = productSelected;

                    if ($('.table').find(`#tr-item-${product.id}`).length > 0) {
                        toastr.info('El producto ya está agregado.', 'Información');
                    } else {
                        // NUEVO: Verificar si este producto estaba en la venta original
                        if (originalSaleDetails[product.id]) {
                            // Restaurar el stock con los datos originales
                            product = restoreOriginalStock(product, originalSaleDetails[product.id]);
                        }

                        if (product.stock > 0 || (product.stock_details && (product.stock_details.full_units > 0 || product.stock_details.remaining_fractions > 0))) {
                            addProductToCart(product);
                            
                            // NUEVO: Si era parte de la venta original, restaurar cantidades y precios
                            if (originalSaleDetails[product.id]) {
                                restoreOriginalQuantities(product.id, originalSaleDetails[product.id]);
                            }

                            setNumber();
                            getSubtotal(product.id);
                            toastr.success(`+1 ${product.item.nameGeneric}`, 'Producto agregado');
                        } else {
                            toastr.error('No hay stock disponible para este producto.', 'Error');
                        }
                    }
                    $('#select-product_id').val('').trigger('change');
                }
            });

            function addProductToCart(product) {
                let stockInfo;
                if (product.stock_details) {
                    let fullUnits = product.stock_details.full_units;
                    let remainingFractions = product.stock_details.remaining_fractions;
                    let fractionName = product.item.fraction_presentation.name;
                    let presentationName = product.item.presentation.name;
                    stockInfo = `<span class="label label-info" style="font-size:12px; padding: 5px 8px;">
                                    <i class="fa-brands fa-dropbox"></i> Stock: ${fullUnits} ${presentationName} y ${remainingFractions} ${fractionName}
                                 </span>`;
                } else {
                    let stockLabel = product.stock > 10 ? 'success' : (product.stock > 0 ? 'warning' : 'danger');
                    stockInfo = `<span class="label label-${stockLabel}" style="font-size:12px; padding: 5px 8px;">
                                    <i class="fa-brands fa-dropbox"></i> Stock: ${product.stock} ${product.item.presentation.name}
                                 </span>`;
                }

                let contentInfo = '';
                if (product.dispensed === 'Fraccionado' && product.dispensedQuantity) {
                    contentInfo = `<div><i class="fa-solid fa-vial" style="color: #e74c3c; width: 14px;"></i> <strong>Contenido:</strong> ${product.dispensedQuantity} ${product.item.fraction_presentation ? product.item.fraction_presentation.name : 'Frac.'} / ${product.item.presentation ? product.item.presentation.name : 'Unid.'}</div>`;
                }

                let observationInfo = '';
                if (product.item.observation) {
                    observationInfo = `<div style="font-size: 12px; color: #7f8c8d; margin-bottom: 8px; padding: 5px 8px; background: #f8f9fa; border-radius: 4px; border: 1px solid #f1f1f1; width: 100%;">
                                            <i class="fa-solid fa-clipboard-list" style="color: #f39c12;"></i> <strong>Detalle:</strong> ${product.item.observation}
                                       </div>`;
                }

                let quantityInputs = `
                    <label for="input-quantity-unit-${product.id}">${product.item.presentation.name}(s)</label>
                    <div style="margin-bottom: 5px;">
                        <input type="number" name="products[${product.id}][quantity_unit]" step="1" min="0" style="text-align: right" class="form-control" id="input-quantity-unit-${product.id}" value="0" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})">
                    </div>
                `;

                if (product.dispensed === 'Fraccionado' && product.dispensedPrice > 0) {
                    quantityInputs += `
                        <label for="input-quantity-fraction-${product.id}">${product.item.fraction_presentation.name}(s)</label>
                        <div>
                            <input type="number" name="products[${product.id}][quantity_fraction]" step="0.1" min="0" style="text-align: right" class="form-control" id="input-quantity-fraction-${product.id}" value="0" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})">
                        </div>
                    `;
                }

                let discountInputs = `
                    <label for="input-discount-unit-${product.id}" style="font-size: 12px; color: #c0392b; font-weight: 600; margin-bottom: 3px;">
                        <i class="fa-solid fa-scissors"></i> Dto. ${product.item.presentation.name}
                    </label>
                    <div class="input-group" style="margin-bottom: 8px;">
                        <span class="input-group-addon" style="background-color: #fdf2f2; color: #e74c3c; border-color: #ebccd1; font-weight: bold;">Bs.</span>
                        <input type="number" name="products[${product.id}][discount_unit]" step="0.01" min="0"
                               style="text-align: right; border-color: #ebccd1;"
                               class="form-control" id="input-discount-unit-${product.id}" value="0"
                               onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})">
                    </div>
                `;

                if (product.dispensed === 'Fraccionado' && product.dispensedPrice > 0) {
                    discountInputs += `
                        <label for="input-discount-fraction-${product.id}" style="font-size: 12px; color: #c0392b; font-weight: 600; margin-bottom: 3px;">
                            <i class="fa-solid fa-scissors"></i> Dto. ${product.item.fraction_presentation.name}
                        </label>
                        <div class="input-group">
                            <span class="input-group-addon" style="background-color: #fdf2f2; color: #e74c3c; border-color: #ebccd1; font-weight: bold;">Bs.</span>
                            <input type="number" name="products[${product.id}][discount_fraction]" step="0.01" min="0"
                                   style="text-align: right; border-color: #ebccd1;"
                                   class="form-control" id="input-discount-fraction-${product.id}" value="0"
                                   onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})">
                        </div>
                    `;
                }

                let newRow = `
                    <tr class="tr-item" id="tr-item-${product.id}">
                        <td class="td-item"></td>
                        <td>
                            <input type="hidden" name="products[${product.id}][item_stock_id]" value="${product.id}"/>
                            <input type="hidden" name="products[${product.id}][id]" value="${product.id}"/>
                            <input type="hidden" name="products[${product.id}][detail_id]" value="0"/>
                            <div style="display: flex; align-items: flex-start;">
                                <div style="flex-grow: 1; line-height: 1.4;">
                                    <div style="font-size: 15px; font-weight: bold; color: #2c3e50; margin-bottom: 5px;">
                                        <i class="fa-solid fa-pills" style="color: #3498db;"></i> ${product.item.nameGeneric} ${product.item.nameTrade ? `<span style="color: #7f8c8d; font-weight: normal;">| ${product.item.nameTrade}</span>` : ''}
                                    </div>
                                    <div style="font-size: 12px; color: #555;">
                                        <div style="display: flex; flex-wrap: wrap; gap: 5px 15px; margin-bottom: 5px;">
                                            <div><i class="fa-solid fa-tags" style="color: #2ecc71; width: 14px;"></i> <strong>Categoría:</strong> ${product.item.category.name}</div>
                                            <div><i class="fa-solid fa-flask" style="color: #3498db; width: 14px;"></i> ${product.item.laboratory ? product.item.laboratory.name : 'SN'}</div>
                                            <div><i class="fa-solid fa-copyright" style="color: #9b59b6; width: 14px;"></i> ${product.item.line ? product.item.line.name : 'SN'}</div>
                                            ${contentInfo}
                                        </div>
                                        ${observationInfo}
                                        <div id="stock-label-${product.id}" style="margin-top: 5px;">${stockInfo}</div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align: middle; padding: 5px;">
                            <label for="input-price-unit-${product.id}" style="font-size: 12px; color: #2c3e50; font-weight: 600; margin-bottom: 3px;">
                                <i class="fa-solid fa-tag" style="color: #3498db;"></i> ${product.item.presentation.name}
                            </label>
                            <div class="input-group" style="margin-bottom: 8px;">
                                <span class="input-group-addon" style="font-weight: bold;">Bs.</span>
                                <input type="number" name="products[${product.id}][price_unit]" step="0.01" min="0.1" style="text-align: right" class="form-control" id="input-price-unit-${product.id}" value="${product.priceSale || 0}" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})">
                            </div>
                            ${ (product.dispensed === 'Fraccionado' && product.dispensedPrice > 0) ? `
                            <label for="input-price-fraction-${product.id}" style="font-size: 12px; color: #2c3e50; font-weight: 600; margin-bottom: 3px;">
                                <i class="fa-solid fa-tag" style="color: #3498db;"></i> ${product.item.fraction_presentation.name}
                            </label>
                            <div class="input-group">
                                <span class="input-group-addon" style="font-weight: bold;">Bs.</span>
                                <input type="number" name="products[${product.id}][price_fraction]" step="0.01" min="0.1" style="text-align: right" class="form-control" id="input-price-fraction-${product.id}" value="${product.dispensedPrice || 0}" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})">
                            </div>` : '' }
                        </td>
                        <td style="vertical-align: middle; padding: 5px;">
                            ${quantityInputs}
                        </td>
                        <td style="vertical-align: middle; padding: 5px;">
                            ${discountInputs}
                        </td>
                        <td style="vertical-align: middle; padding: 8px; min-width: 130px;">
                            <div id="subtotal-unit-container-${product.id}" style="margin-bottom: 10px; padding-bottom: 8px; ${ (product.dispensed === 'Fraccionado' && product.dispensedPrice > 0) ? 'border-bottom: 1px dashed #ddd;' : '' }">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">
                                    <i class="fa-solid fa-cube" style="width: 12px;"></i> <em>${product.item.presentation.name}</em>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #95a5a6; margin-bottom: 2px;">
                                    <span>Bruto:</span>
                                    <span><b id="label-bruto-unit-${product.id}" style="font-family: monospace;">0.00</b></span>
                                </div>
                                <div id="discount-unit-display-${product.id}" style="display: none; justify-content: space-between; font-size: 11px; color: #e74c3c; margin-bottom: 2px;">
                                    <span><i class="fa-solid fa-minus"></i> Dto:</span>
                                    <span><b id="label-dto-unit-${product.id}" style="font-family: monospace;">0.00</b></span>
                                </div>
                                <div style="border-top: 1px solid #ddd; margin-top: 5px; padding-top: 5px; display: flex; justify-content: space-between; align-items: center;">
                                    <small class="text-muted">Bs.</small>
                                    <b id="label-subtotal-unit-${product.id}" style="font-size: 1.15em; color: #27ae60;">0.00</b>
                                </div>
                            </div>
                            ${ (product.dispensed === 'Fraccionado' && product.dispensedPrice > 0) ? `
                            <div id="subtotal-fraction-container-${product.id}" style="margin-bottom: 8px;">
                                <div style="font-size: 11px; color: #7f8c8d; margin-bottom: 4px;">
                                    <i class="fa-solid fa-vial" style="width: 12px;"></i> <em>${product.item.fraction_presentation.name}</em>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #95a5a6; margin-bottom: 2px;">
                                    <span>Bruto:</span>
                                    <span><b id="label-bruto-fraction-${product.id}" style="font-family: monospace;">0.00</b></span>
                                </div>
                                <div id="discount-fraction-display-${product.id}" style="display: none; justify-content: space-between; font-size: 11px; color: #e74c3c; margin-bottom: 2px;">
                                    <span><i class="fa-solid fa-minus"></i> Dto:</span>
                                    <span><b id="label-dto-fraction-${product.id}" style="font-family: monospace;">0.00</b></span>
                                </div>
                                <div style="border-top: 1px solid #ddd; margin-top: 5px; padding-top: 5px; display: flex; justify-content: space-between; align-items: center;">
                                    <small class="text-muted">Bs.</small>
                                    <b id="label-subtotal-fraction-${product.id}" style="font-size: 1.15em; color: #27ae60;">0.00</b>
                                </div>
                            </div>` : '' }
                            <input type="hidden" class="label-subtotal" id="label-subtotal-${product.id}" value="0.00" />
                        </td>
                        <td style="width: 5%">
                            <button type="button" onclick="removeTr(${product.id})" class="btn btn-link"><i class="voyager-trash text-danger"></i></button>
                        </td>
                    </tr>
                `;

                $('#table-body').append(newRow);
                $(`#tr-item-${product.id}`).data('product', product);
            }

            $('#form-sale').submit(function(e) {
                if (this.checkValidity()) {
                    $('.btn-confirm').val('Guardando...');
                    $('.btn-confirm').attr('disabled', true);
                }
            });

            // Ocultar mensajes al inicio
            $('#change-message-credito, #change-message, #change-message-error, #change-message-error-credito').hide();

            // Eventos que disparan la actualización de la lógica de pago
            $('#select-payment_type').on('change', updatePaymentLogic);
            $('#amount_cash, #amount_qr').on('keyup change', handleAmountInputs);

            // Si estamos editando, inicializamos los valores
            @if(isset($sale))
                @php
                    // Cargar relaciones necesarias para que el JSON tenga toda la info que requiere addProductToCart
                    $sale->saleDetails->load([
                        'itemStock',
                        'itemStock.item.category', 
                        'itemStock.item.line', 
                        'itemStock.item.laboratory', 
                        'itemStock.item.presentation', 
                        'itemStock.item.fractionPresentation',
                        'itemStock.itemStockFractions',
                        'itemStockFraction'
                    ]);

                    // Agrupamos por item_stock_id para calcular el stock total restaurado de todos los detalles de ese producto
                    $groupedDetailsPHP = $sale->saleDetails->groupBy('itemStock_id');

                    foreach($groupedDetailsPHP as $itemStockId => $details) {
                        $itemStock = $details->first()->itemStock;

                        if ($itemStock && $itemStock->dispensed === 'Fraccionado' && $itemStock->dispensedQuantity > 0) {
                            $fractions_sold = $itemStock->itemStockFractions->sum('quantity');

                            // Sumar lo que se está editando actualmente (restaurar al stock virtualmente)
                            $fractions_now = 0;
                            $unit_now = 0;
                            foreach($details as $detail) {
                                if($detail->itemStockFraction) {
                                    $fractions_now += $detail->itemStockFraction->quantity;
                                } else {
                                    $unit_now += $detail->quantity;
                                }
                            }

                            $opened_units = 0;
                            $remaining_fractions = 0;
                            $fractions_sold_adjusted = max(0, $fractions_sold - $fractions_now);
                            
                            if ($fractions_sold_adjusted > 0) {
                                $opened_units = ceil($fractions_sold_adjusted / $itemStock->dispensedQuantity);
                                $remaining_fractions = ($opened_units * $itemStock->dispensedQuantity) - $fractions_sold_adjusted;
                            }

                            $full_units = max(0, $itemStock->stock - $opened_units);
                            $unit = $full_units + $unit_now;
                            
                            $itemStock->setAttribute('stock_details', [
                                'full_units' => $unit,
                                'remaining_fractions' => $remaining_fractions
                            ]);
                        }
                        else {
                            // Restaurar stock para productos enteros
                            $unit = $itemStock->stock + $details->sum('quantity');
                            $itemStock->stock = $unit;
                        }
                    }
                @endphp
                
                let existingDetails = @json($sale->saleDetails);
                let groupedDetails = {};

                // 1. Agrupar detalles por item_stock_id para evitar duplicados visuales
                existingDetails.forEach(detail => {
                    if (!groupedDetails[detail.itemStock_id]) {
                        groupedDetails[detail.itemStock_id] = {
                            stock: detail.item_stock,
                            details: []
                        };
                    }
                    groupedDetails[detail.itemStock_id].details.push(detail);
                });
                
                // 2. Iterar sobre los grupos y renderizar una sola fila por producto
                Object.values(groupedDetails).forEach(group => {
                    let stock = group.stock;
                    let item = stock.item;
                    let details = group.details;
                    
                    // Reconstruir el objeto producto tal como lo espera addProductToCart
                    let product = {
                        id: stock.id,
                        stock: stock.stock,
                        priceSale: stock.priceSale,
                        dispensed: stock.dispensed,
                        dispensedPrice: stock.dispensedPrice,
                        dispensedQuantity: stock.dispensedQuantity,
                        item: item,
                        stock_details: stock.stock_details
                    };

                    let originalData = {
                        stock_details: stock.stock_details ? JSON.parse(JSON.stringify(stock.stock_details)) : null,
                        original_stock: stock.stock,
                        details: [],
                        dispensed: stock.dispensed,
                        dispensedQuantity: stock.dispensedQuantity,
                        dispensedPrice: stock.dispensedPrice
                    };
                    
                    addProductToCart(product);
                    
                    let rowId = stock.id;
                    let quantityRestored = 0;
                    
                    // 3. Llenar los inputs correspondientes (Unidad y/o Fracción)
                    details.forEach(detail => {
                        if (detail.dispensed === 'Fraccionado') {
                            $(`#input-quantity-fraction-${rowId}`).val(detail.quantity);
                            $(`#input-price-fraction-${rowId}`).val(detail.price);
                            $(`#input-discount-fraction-${rowId}`).val(detail.discount ?? 0);

                            originalData.details.push({
                                type: 'fraction',
                                quantity: detail.quantity,
                                price: detail.price,
                                discount: detail.discount ?? 0
                            });

                            quantityRestored += parseFloat(detail.quantity) / (stock.dispensedQuantity || 1);
                        } else {
                            $(`#input-quantity-unit-${rowId}`).val(detail.quantity);
                            $(`#input-price-unit-${rowId}`).val(detail.price);
                            $(`#input-discount-unit-${rowId}`).val(detail.discount ?? 0);
                            $(`input[name="products[${rowId}][detail_id]"]`).val(detail.id);

                            originalData.details.push({
                                type: 'unit',
                                quantity: detail.quantity,
                                price: detail.price,
                                discount: detail.discount ?? 0
                            });

                            quantityRestored += parseFloat(detail.quantity);
                        }
                    });
                    
                    // NUEVO: Almacenar datos originales para este producto
                    originalSaleDetails[rowId] = originalData;
                    
                    // Ajustar el stock en el objeto data para permitir la validación (Stock actual + Cantidad en esta venta)
                    let rowData = $(`#tr-item-${rowId}`).data('product');
                    rowData.stock = parseFloat(rowData.stock) + quantityRestored;
                    $(`#tr-item-${rowId}`).data('product', rowData);
                    
                    getSubtotal(rowId);
                });
                
                setNumber();

                let personId = $('#select-person_id').data('id');
                if(personId){
                    let personText = $('#select-person_id').data('text');
                    var newOption = new Option(personText, personId, true, true);
                    $('#select-person_id').append(newOption).trigger('change');
                }
            @endif


            // Inicializar la lógica de pago al cargar la página
            updatePaymentLogic();
        });

        // NUEVA FUNCIÓN: Restaurar stock original cuando se vuelve a agregar un producto
        function restoreOriginalStock(product, originalData) {
            // Clonar el producto para no modificar el original
            let restoredProduct = JSON.parse(JSON.stringify(product));
            
            if (originalData.stock_details) {
                // Para productos fraccionados
                restoredProduct.stock_details = JSON.parse(JSON.stringify(originalData.stock_details));
            } else {
                // Para productos enteros
                restoredProduct.stock = originalData.original_stock;
            }
            
            // Restaurar precios si existen
            if (originalData.dispensedPrice) {
                restoredProduct.dispensedPrice = originalData.dispensedPrice;
            }
            
            return restoredProduct;
        }

        // NUEVA FUNCIÓN: Restaurar cantidades, precios y descuentos originales
        function restoreOriginalQuantities(productId, originalData) {
            originalData.details.forEach(detail => {
                if (detail.type === 'fraction') {
                    $(`#input-quantity-fraction-${productId}`).val(detail.quantity);
                    $(`#input-price-fraction-${productId}`).val(detail.price);
                    $(`#input-discount-fraction-${productId}`).val(detail.discount ?? 0);
                } else if (detail.type === 'unit') {
                    $(`#input-quantity-unit-${productId}`).val(detail.quantity);
                    $(`#input-price-unit-${productId}`).val(detail.price);
                    $(`#input-discount-unit-${productId}`).val(detail.discount ?? 0);
                }
            });
        }

        function funtion_typeSale() {
            getTotal();
        }

        function getSubtotal(id) {
            let product = $(`#tr-item-${id}`).data('product');

            let price_unit = parseFloat($(`#input-price-unit-${id}`).val()) || 0;
            let quantity_unit = parseInt($(`#input-quantity-unit-${id}`).val()) || 0;
            let discount_unit = parseFloat($(`#input-discount-unit-${id}`).val()) || 0;

            let price_fraction = 0;
            let quantity_fraction = 0;
            let discount_fraction = 0;
            if (product.dispensed === 'Fraccionado') {
                price_fraction = parseFloat($(`#input-price-fraction-${id}`).val()) || 0;
                quantity_fraction = parseFloat($(`#input-quantity-fraction-${id}`).val()) || 0;
                discount_fraction = parseFloat($(`#input-discount-fraction-${id}`).val()) || 0;
            }

            let total_stock_in_fractions;
            if (product.stock_details) {
                total_stock_in_fractions = (product.stock_details.full_units * (product.dispensedQuantity || 1)) + product.stock_details.remaining_fractions;
            } else {
                total_stock_in_fractions = product.stock * (product.dispensedQuantity || 1);
            }

            let requested_fractions = (quantity_unit * (product.dispensedQuantity || 1)) + quantity_fraction;

            if (requested_fractions > total_stock_in_fractions) {
                toastr.warning('La cantidad solicitada excede el stock disponible.', 'Stock Insuficiente');

                let max_units = Math.floor(total_stock_in_fractions / (product.dispensedQuantity || 1));
                let remaining_fractions_for_max = total_stock_in_fractions % (product.dispensedQuantity || 1);

                $(`#input-quantity-unit-${id}`).val(max_units);
                if (product.dispensed === 'Fraccionado') {
                    $(`#input-quantity-fraction-${id}`).val(remaining_fractions_for_max);
                }

                quantity_unit = parseFloat($(`#input-quantity-unit-${id}`).val()) || 0;
                quantity_fraction = parseFloat($(`#input-quantity-fraction-${id}`).val()) || 0;
            }
            // --- FIN: Lógica de validación de stock mejorada ---

            // Validar que el descuento no exceda el bruto de la línea
            let bruto_unit = price_unit * quantity_unit;
            if (discount_unit > bruto_unit) {
                discount_unit = bruto_unit;
                $(`#input-discount-unit-${id}`).val(discount_unit.toFixed(2));
            }

            let subtotal_unit = Math.max(0, bruto_unit - discount_unit);
            $(`#label-bruto-unit-${id}`).text(bruto_unit.toFixed(2));
            if (discount_unit > 0) {
                $(`#label-dto-unit-${id}`).text(discount_unit.toFixed(2));
                $(`#discount-unit-display-${id}`).css('display', 'flex');
            } else {
                $(`#discount-unit-display-${id}`).hide();
            }
            $(`#label-subtotal-unit-${id}`).text(subtotal_unit.toFixed(2));

            let subtotal_fraction = 0;
            if (product.dispensed === 'Fraccionado' && product.dispensedPrice > 0) {
                let bruto_fraction = price_fraction * quantity_fraction;
                if (discount_fraction > bruto_fraction) {
                    discount_fraction = bruto_fraction;
                    $(`#input-discount-fraction-${id}`).val(discount_fraction.toFixed(2));
                }
                subtotal_fraction = Math.max(0, bruto_fraction - discount_fraction);
                $(`#label-bruto-fraction-${id}`).text(bruto_fraction.toFixed(2));
                if (discount_fraction > 0) {
                    $(`#label-dto-fraction-${id}`).text(discount_fraction.toFixed(2));
                    $(`#discount-fraction-display-${id}`).css('display', 'flex');
                } else {
                    $(`#discount-fraction-display-${id}`).hide();
                }
                $(`#label-subtotal-fraction-${id}`).text(subtotal_fraction.toFixed(2));
            }

            // Actualizar el valor oculto que suma ambos subtotales para el total general
            let subtotal = subtotal_unit + subtotal_fraction;
            $(`#label-subtotal-${id}`).val(subtotal.toFixed(2));
            getTotal();
        }

        function setNumber() {
            var length = 0;
            $(".td-item").each(function(index) {
                $(this).text(index + 1);
                length++;
            });
            if (length > 0) {
                $('#tr-empty').css('display', 'none');
            } else {
                $('#tr-empty').fadeIn('fast');
            }
        }

        function removeTr(id) {
            $(`#tr-item-${id}`).remove();
            $('#select-product_id').val("").trigger("change");
            setNumber();
            getTotal();
            toastr.info('Producto eliminado del carrito', 'Eliminado');
        }


        function formatResultProducts(option) {
             if (option.loading) {
                 return '<span class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</span>';
             }
             let image = "{{ asset('images/default.jpg') }}";
             if (option.item && option.item.image) {
                 const lastDotIndex = option.item.image.lastIndexOf('.');
                 const baseName = lastDotIndex !== -1 ? option.item.image.substring(0, lastDotIndex) : option.item.image;
                 image = `{{ asset('storage') }}/${baseName}-cropped.webp`;
             }
 
             // --- Precios ---
             let unitPriceLabel = `<span class="label label-primary" style="font-size:13px; padding: 5px 8px; box-shadow: 0 2px 2px rgba(0,0,0,0.1);">Bs. ${option.priceSale} / ${option.item.presentation.name}</span>`;
             let fractionPriceLabel = '';
             if (option.dispensed === 'Fraccionado' && option.dispensedPrice) {
                 fractionPriceLabel = `<span class="label label-success" style="font-size:13px; padding: 5px 8px; box-shadow: 0 2px 2px rgba(0,0,0,0.1);">Bs. ${option.dispensedPrice} / ${option.item.fraction_presentation.name}</span>`;
             }
 
             // --- Stock ---
             let stockInfo;
             if (option.stock_details) {
                 let fullUnits = option.stock_details.full_units;
                 let remainingFractions = option.stock_details.remaining_fractions;
                 let fractionName = option.item.fraction_presentation.name;
                 let presentationName = option.item.presentation.name;
                 stockInfo = `<span class="label label-info" style="font-size:14px; padding: 6px 10px;">
                                 <i class="fa-brands fa-dropbox"></i> Stock: ${fullUnits} ${presentationName} y ${remainingFractions} ${fractionName}
                              </span>`;
             } else {
                 let stockLabel = option.stock > 10 ? 'success' : (option.stock > 0 ? 'warning' : 'danger');
                 stockInfo = `<span class="label label-${stockLabel}" style="font-size:14px; padding: 6px 10px;">
                                 <i class="fa-brands fa-dropbox"></i> Stock: ${option.stock} ${option.item.presentation.name}
                              </span>`;
             }
 
             // --- Contenido (para fraccionados) ---
             let contentInfo = '';
             if (option.dispensed === 'Fraccionado' && option.dispensedQuantity) {
                 contentInfo = `<div><i class="fa-solid fa-vial" style="color: #e74c3c; width: 14px;"></i> <strong>Contenido:</strong> ${option.dispensedQuantity} ${option.item.fraction_presentation ? option.item.fraction_presentation.name : 'Frac.'} / ${option.item.presentation ? option.item.presentation.name : 'Unid.'}</div>`;
             }
 
             // --- Observación ---
             let observationInfo = '';
             if (option.item.observation) {
                 observationInfo = `<div style="font-size: 12px; color: #7f8c8d; margin-bottom: 8px; padding: 5px 8px; background: #f8f9fa; border-radius: 4px; border: 1px solid #f1f1f1; width: 100%;">
                                         <i class="fa-solid fa-clipboard-list" style="color: #f39c12;"></i> <strong>Detalle:</strong> ${option.item.observation}
                                    </div>`;
             }
 
             // --- Expiración ---
             let expirationHtml = '';
             if (option.expirationDate) {
                 let expirationDate = moment(option.expirationDate);
                 let today = moment().startOf('day');
                 let diffDays = expirationDate.diff(today, 'days');
                 let colorStyle = 'color: #454545'; // Normal
                 let iconStyle = 'color: #454545';
                 let extraText = '';

                 let expirationDaysSetting = "{{ setting('items-productos.notificateExpiration') }}";
                 let expirationDays = !isNaN(parseInt(expirationDaysSetting)) ? parseInt(expirationDaysSetting) : 15;

                 if (diffDays < 0) {
                     colorStyle = 'color: #e74c3c; font-weight: bold'; // Rojo (Vencido)
                     iconStyle = 'color: #e74c3c';
                     extraText = ' (Vencido)';
                 } else if (diffDays <= expirationDays) {
                     colorStyle = 'color: #e67e22; font-weight: bold'; // Naranja (Advertencia)
                     iconStyle = 'color: #e67e22';
                     extraText = ' (Por vencer)';
                 }
                 expirationHtml = `<div><i class="fa-solid fa-calendar" style="${iconStyle}; width: 16px;"></i> <strong style="${colorStyle}">Fecha de Expiración:</strong> <span style="${colorStyle}">${option.expirationDate}${extraText}</span></div>`;
             } else {
                 expirationHtml = `<div><i class="fa-solid fa-calendar" style="color: #454545; width: 16px;"></i> <strong>Fecha de Expiración:</strong> Sin fecha de expiración</div>`;
             }

             // --- HTML Final ---
             return $(`<div style="display: flex; align-items: flex-start; padding: 12px 8px; border-bottom: 1px solid #f0f0f0;">
                             <div style="flex-shrink: 0; margin-right: 15px;">
                                 <img src="${image}" style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 1px solid #eee;" />
                             </div>
                             <div style="flex-grow: 1; line-height: 1.4;">
                                 <!-- Fila 1: Nombre y Precios -->
                                 <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                     <div style="font-size: 16px; font-weight: bold; color: #2c3e50; padding-right: 10px;">
                                         <i class="fa-solid fa-pills" style="color: #3498db;"></i> ${option.item.nameGeneric} ${option.item.nameTrade ? `<span style="color: #7f8c8d; font-weight: normal;">| ${option.item.nameTrade}</span>` : ''}
                                     </div>
                                     
                                     <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 5px;">
                                         ${unitPriceLabel}
                                         ${fractionPriceLabel}
                                     </div>

                                 </div>
                                <div><i class="fa-solid fa-barcode" style="color: #2ecc71; width: 16px;"></i> <strong>Lote / Código:</strong> ${option.lote??'Sin Lote'}</div> 
                                ${expirationHtml}
                                <br>

 
                                 <!-- Fila 2: Detalles y Stock -->
                                 <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                                     <div style="font-size: 12px; color: #555;">
                                         <div style="display: flex; flex-wrap: wrap; gap: 5px 15px; margin-bottom: 8px;">
                                             <div><i class="fa-solid fa-tags" style="color: #2ecc71; width: 14px;"></i> <strong>Categoría:</strong> ${option.item.category.name}</div>
                                             <div><i class="fa-solid fa-flask" style="color: #3498db; width: 14px;"></i> ${option.item.laboratory ? option.item.laboratory.name : 'Sin Laboratorio'}</div>
                                             <div><i class="fa-solid fa-copyright" style="color: #9b59b6; width: 14px;"></i> ${option.item.line ? option.item.line.name : 'Sin Marca'}</div>
                                             ${contentInfo}
                                         </div>
                                         ${observationInfo}
                                     </div>
                                     <div style="white-space: nowrap; padding-left: 10px;">
                                         ${stockInfo}
                                     </div>
                                 </div>
                             </div>
                         </div>`);
        }

        $('#trash-person').on('click', function() {
            $('#input-dni').val('');
            $('#select-person_id').val('').trigger('change');
            toastr.success('Cliente eliminado', 'Eliminado');
        });

        // =================================================================
        // ====================== NEW PAYMENT LOGIC ========================
        // =================================================================

        function updatePaymentLogic() {
            let typeSale = $('#typeSale').val();
            let paymentType = $('#select-payment_type').val();
            let total = totalAmount || 0;

            // Hide all dynamic sections by default
            $('#cash-payment-section, #qr-payment-section').fadeOut('fast');
            $('#change-message-credito, #change-message, #change-message-error, #change-message-error-credito').hide();
            $('.btn-submit').prop('disabled', true);

            if (typeSale === 'Proforma') {
                // paymentSelect.prop('required', false);
                $('.btn-submit').prop('disabled', false);
            } 
            else if (typeSale === 'Venta al Contado') {
                // $('#change-message-error').show(); // Show "monto faltante" by default

                switch (paymentType) {
                    case 'Efectivo':
                        $('#cash-payment-section').fadeIn('fast');
                        $('#amount_cash').prop('readonly', false).removeAttr('max').attr('min', total.toFixed(2));
                        $('#amount_qr').prop('readonly', true).val(0).attr('min', 0).attr('max', 0);
                        break;
                    case 'Qr':
                        $('#qr-payment-section').fadeIn('fast');
                        $('#amount_cash').prop('readonly', true).val(0).attr('min', 0).attr('max', 0);
                        $('#amount_qr').prop('readonly', true).val(total.toFixed(2)).attr('min', total.toFixed(2)).attr('max', total.toFixed(2));
                        break;
                    case 'Efectivo y Qr':
                        $('#cash-payment-section, #qr-payment-section').fadeIn('fast');
                        $('#amount_cash').prop('readonly', false).val(0).attr('min', 0).attr('max', total.toFixed(2));
                        $('#amount_qr').prop('readonly', false).val(0).attr('min', 0).attr('max', total.toFixed(2));
                        break;
                    default:
                        // No payment method selected
                        $('#amount_cash').prop('readonly', true).val(0);
                        $('#amount_qr').prop('readonly', true).val(0);
                }
            } 
            else if (typeSale === 'Venta al Credito') {
                // $('#change-message-credito').show();

                switch (paymentType) {
                    case 'Efectivo':
                        $('#cash-payment-section').fadeIn('fast');
                        $('#amount_cash').prop('readonly', false).val(0).attr('min', 0).attr('max', total.toFixed(2));
                        $('#amount_qr').val(0);
                        break;
                    case 'Qr':
                        $('#qr-payment-section').fadeIn('fast');
                        $('#amount_cash').val(0);
                        $('#amount_qr').prop('readonly', false).val(0).attr('min', 0).attr('max', total.toFixed(2));
                        break;
                    case 'Efectivo y Qr':
                        $('#cash-payment-section, #qr-payment-section').fadeIn('fast');
                        $('#amount_cash').prop('readonly', false).val(0).attr('min', 0).attr('max', total.toFixed(2));
                        $('#amount_qr').prop('readonly', false).val(0).attr('min', 0).attr('max', total.toFixed(2));
                        break;
                    default:
                        $('#amount_cash').val(0);
                        $('#amount_qr').val(0);
                        break;
                }
            }
            
            handleAmountInputs();
        }

        function handleAmountInputs() {
            let typeSale = $('#typeSale').val();
            let paymentType = $('#select-payment_type').val();
            const EPSILON = 0.001; // Small tolerance for float comparisons

            let total = totalAmount || 0;
            let cash = parseFloat($('#amount_cash').val()) || 0;
            let qr = parseFloat($('#amount_qr').val()) || 0;

            // Logic for 'Venta al Contado' with 'Efectivo y Qr'
            if (typeSale === 'Venta al Contado' && paymentType === 'Efectivo y Qr') {
                if ($(document.activeElement).is('#amount_cash') || $(document.activeElement).is('#amount_qr')) {
                    let activeInput = $(document.activeElement);
                    let otherInput;
                    if (activeInput.is('#amount_cash')) {
                        otherInput = $('#amount_qr');
                    } else if (activeInput.is('#amount_qr')) {
                        otherInput = $('#amount_cash');
                    }

                    let currentVal = parseFloat(activeInput.val()) || 0;
                    if (currentVal > total) {
                        currentVal = total;
                        activeInput.val(currentVal.toFixed(2));
                    }

                    otherInput.val((total - currentVal).toFixed(2));
                }
            }
            
            // Recalculate values after potential changes
            cash = parseFloat($('#amount_cash').val()) || 0;
            qr = parseFloat($('#amount_qr').val()) || 0;
            let totalPaid = cash + qr;

            // For credit sales, prevent payment from exceeding the total
            if (typeSale === 'Venta al Credito') {
                // Compare with a small tolerance for floating point issues
                if (totalPaid > total + EPSILON) {
                    $('#change-message-error-credito').fadeIn('fast');
                    
                    // Adjust the currently active input to not exceed the total
                    if ($(document.activeElement).is('#amount_cash')) {
                        let maxCash = total - qr;
                        $('#amount_cash').val(maxCash > 0 ? maxCash.toFixed(2) : 0);
                    } else if ($(document.activeElement).is('#amount_qr')) {
                        let maxQr = total - cash;
                        $('#amount_qr').val(maxQr > 0 ? maxQr.toFixed(2) : 0);
                    }
                } else {
                    $('#change-message-error-credito').hide();
                }
            }

            // Recalculate totalPaid after adjustments
            cash = parseFloat($('#amount_cash').val()) || 0;
            qr = parseFloat($('#amount_qr').val()) || 0;
            totalPaid = cash + qr;

            $('#amountReceived').val(totalPaid.toFixed(2));
            calculateChange();
        }

        function calculateChange() {
            let typeSale = $('#typeSale').val();
            let totalPaid = parseFloat($('#amountReceived').val()) || 0;
            let total = totalAmount || 0;
            const EPSILON = 0.001;

            // Hide all messages by default, then show the correct one
            $('#change-message, #change-message-credito, #change-message-error, #change-message-error-credito').hide();

            if (typeSale === 'Venta al Contado') {
                let paymentType = $('#select-payment_type').val();
                if (paymentType === 'Efectivo y Qr') {
                    if (Math.abs(totalPaid - total) < EPSILON && total > 0) {
                        $('.btn-submit').prop('disabled', false);
                    } else if (total > 0) {
                        $('.btn-submit').prop('disabled', true);
                        $('#change-message-error').fadeIn('fast').find('small').text('La suma de Efectivo y Qr debe ser igual al total.');
                    }
                } else if (totalPaid >= total - EPSILON && total > 0) {
                    $('.btn-submit').prop('disabled', false);
                    $('#change-message').fadeIn('fast');
                    let change = totalPaid - total;
                    $('#change-amount').text(change > 0 ? change.toFixed(2) : '0.00');
                } else if (total > 0) {
                    $('.btn-submit').prop('disabled', true);
                    $('#change-message-error').fadeIn('fast').find('small').text('Monto insuficiente. Debe ser igual o mayor al total.');
                }
            } else if (typeSale === 'Venta al Credito') {
                $('.btn-submit').prop('disabled', false);
                $('#change-message-credito').fadeIn('fast');
                let pending = total - totalPaid;
                
                // Prevent showing -0.00 or tiny negative values due to float issues
                if (Math.abs(pending) < EPSILON) {
                    pending = 0;
                }
                $('#change-amount-credito').text(pending.toFixed(2));
            }
        }

        function getTotal() {
            totalAmount = 0;
            $(".label-subtotal").each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });
            $('#label-total').text(totalAmount.toFixed(2));
            $('#amountTotalSale').val(totalAmount.toFixed(2));
            updatePaymentLogic();
        }

    </script>
@stop
