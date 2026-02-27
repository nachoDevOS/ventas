<form class="form-edit-add" action="{{ route('expenses.store') }}" method="POST" id="form-create-expense">
    <div class="modal fade" id="modal-create-expense" role="dialog">
        <div class="modal-dialog modal-lg modal-success">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" style="color: #ffffff !important;">
                        <i class="voyager-plus"></i> Registrar Gasto
                    </h4>
                </div>

                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="amount"      id="input-total-amount">
                    <input type="hidden" name="observation" id="input-observation">
                    <input type="hidden" name="amount_cash" id="hidden-amount-cash" value="0">
                    <input type="hidden" name="amount_qr"   id="hidden-amount-qr"   value="0">

                    {{-- ── Tabla de ítems ───────────────────────────────────────── --}}
                    <table class="table table-bordered table-hover" id="table-expenses">
                        <thead>
                            <tr>
                                <th>Detalle</th>
                                <th style="width: 90px;">Cant.</th>
                                <th style="width: 120px;">Precio</th>
                                <th style="width: 120px;">Total</th>
                                <th style="width: 46px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-expenses">
                            <tr class="tr-expense">
                                <td>
                                    <input type="text" name="details[]" class="form-control input-detail" placeholder="Descripción del gasto" required>
                                </td>
                                <td>
                                    <input type="number" name="quantities[]" class="form-control input-quantity"
                                        value="1" min="1" step="1"
                                        onkeyup="calculateTotalExpense()" onchange="calculateTotalExpense()" required>
                                </td>
                                <td>
                                    <input type="number" name="prices[]" class="form-control input-price"
                                        step="0.5" min="0.5" placeholder="0.00"
                                        onkeyup="calculateTotalExpense()" onchange="calculateTotalExpense()" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control input-subtotal" value="0.00" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRowExpense(this)">
                                        <i class="voyager-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right" style="vertical-align: middle;"><strong>TOTAL A PAGAR</strong></td>
                                <td>
                                    <input type="number" class="form-control" id="input-grand-total" value="0.00" readonly
                                        style="font-weight: bold; background: #f8f9fa;">
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- ── Método de pago ───────────────────────────────────────── --}}
                    <div class="form-group" style="margin-top: 10px;">
                        <label>Método de pago <span class="text-danger">*</span></label>
                        <select name="payment_type" id="select-payment_type-expense" class="form-control" required>
                            <option value="" selected disabled>— Seleccione el método —</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Qr">QR / Transferencia</option>
                            <option value="Efectivo y Qr">Efectivo y QR</option>
                        </select>
                    </div>

                    {{-- ── Panel de pago (dinámico) ─────────────────────────────── --}}
                    <div id="payment-panel-expense" style="display: none; margin-top: 5px;">

                        {{-- Efectivo solo --}}
                        <div id="panel-solo-cash" style="display: none;">
                            <div style="background: #e8f5e9; border: 1px solid #a5d6a7; border-radius: 8px; padding: 16px;">
                                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <i class="fa-solid fa-money-bill-wave" style="font-size: 1.4rem; color: #27ae60; margin-right: 10px;"></i>
                                    <strong style="font-size: 1rem; color: #1b5e20;">Pago en Efectivo</strong>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: #27ae60; color: #fff; border-color: #27ae60;">Bs.</span>
                                    <input type="number" id="input-cash-solo"
                                        class="form-control" readonly
                                        style="font-size: 1.2rem; font-weight: bold; background: #fff; color: #27ae60;">
                                </div>
                                <small class="text-muted" style="margin-top: 6px; display: block;">
                                    <i class="fa fa-info-circle"></i> El monto completo del gasto se descuenta del efectivo en caja.
                                </small>
                            </div>
                        </div>

                        {{-- QR solo --}}
                        <div id="panel-solo-qr" style="display: none;">
                            <div style="background: #e3f2fd; border: 1px solid #90caf9; border-radius: 8px; padding: 16px;">
                                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                    <i class="fa-solid fa-qrcode" style="font-size: 1.4rem; color: #1565c0; margin-right: 10px;"></i>
                                    <strong style="font-size: 1rem; color: #0d47a1;">Pago por QR / Transferencia</strong>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: #1565c0; color: #fff; border-color: #1565c0;">Bs.</span>
                                    <input type="number" id="input-qr-solo"
                                        class="form-control" readonly
                                        style="font-size: 1.2rem; font-weight: bold; background: #fff; color: #1565c0;">
                                </div>
                                <small class="text-muted" style="margin-top: 6px; display: block;">
                                    <i class="fa fa-info-circle"></i> El monto completo del gasto se descuenta del saldo QR.
                                </small>
                            </div>
                        </div>

                        {{-- Efectivo y QR --}}
                        <div id="panel-mixed" style="display: none;">
                            <div style="background: #fff8e1; border: 1px solid #ffe082; border-radius: 8px; padding: 16px;">
                                <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                    <i class="fa-solid fa-scale-balanced" style="font-size: 1.2rem; color: #f57f17; margin-right: 8px;"></i>
                                    <strong style="color: #e65100;">Dividir entre Efectivo y QR</strong>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label style="font-size: 12px; color: #555;">
                                            <i class="fa-solid fa-money-bill-wave" style="color: #27ae60;"></i> Monto en Efectivo
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #27ae60; color: #fff; border-color: #27ae60;">Bs.</span>
                                            <input type="number" id="input-cash-mixed"
                                                class="form-control" step="0.5" min="0" placeholder="0.00"
                                                oninput="syncMixedExpense()"
                                                style="font-weight: bold; color: #27ae60;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label style="font-size: 12px; color: #555;">
                                            <i class="fa-solid fa-qrcode" style="color: #1565c0;"></i> Monto en QR / Transferencia
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background: #1565c0; color: #fff; border-color: #1565c0;">Bs.</span>
                                            <input type="number" id="input-qr-mixed"
                                                class="form-control" readonly
                                                style="font-weight: bold; color: #1565c0; background: #f0f4ff;">
                                        </div>
                                    </div>
                                </div>

                                {{-- Balance --}}
                                <div style="margin-top: 12px; padding: 10px 14px; border-radius: 6px; background: #fff; border: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;" id="balance-box-expense">
                                    <span style="font-size: 13px; color: #555;">Total del gasto:</span>
                                    <span style="font-size: 13px;"><strong id="balance-total-label">Bs. 0.00</strong></span>
                                    <span style="font-size: 13px; color: #aaa;">Efectivo + QR:</span>
                                    <span id="balance-sum-label" style="font-size: 13px; font-weight: bold;">Bs. 0.00</span>
                                    <span id="balance-status" style="font-size: 13px; font-weight: bold;"></span>
                                </div>

                                <small class="text-muted" style="display: block; margin-top: 6px;">
                                    <i class="fa fa-info-circle"></i> Ingresa el monto en Efectivo; el QR se calcula automáticamente.
                                </small>
                            </div>
                        </div>

                    </div>{{-- /payment-panel-expense --}}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-submit">
                        <i class="fa fa-save"></i> Guardar Gasto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Cálculo de filas ──────────────────────────────────────────────────────
        function calculateTotalExpense() {
            let total = 0;
            $('.tr-expense').each(function () {
                let qty      = parseFloat($(this).find('.input-quantity').val()) || 0;
                let price    = parseFloat($(this).find('.input-price').val())    || 0;
                let subtotal = qty * price;
                $(this).find('.input-subtotal').val(subtotal.toFixed(2));
                total += subtotal;
            });

            $('#input-grand-total').val(total.toFixed(2));
            $('#input-total-amount').val(total.toFixed(2));

            // Actualizar los paneles con el nuevo total
            let type = $('#select-payment_type-expense').val();
            if (type === 'Efectivo') {
                $('#input-cash-solo').val(total.toFixed(2));
            } else if (type === 'Qr') {
                $('#input-qr-solo').val(total.toFixed(2));
            } else if (type === 'Efectivo y Qr') {
                syncMixedExpense();
            }
        }

        // ── Sincronizar panel mixto (QR = total - Efectivo) ───────────────────────
        function syncMixedExpense() {
            let total = parseFloat($('#input-total-amount').val()) || 0;
            let cash  = parseFloat($('#input-cash-mixed').val())   || 0;

            // Efectivo no puede ser >= total: siempre debe quedar algo para QR
            let maxCash = total - 0.50;
            if (cash >= total) {
                cash = maxCash > 0 ? maxCash : 0;
                $('#input-cash-mixed').val(cash.toFixed(2));
            }

            let qr = Math.max(0, total - cash);
            $('#input-qr-mixed').val(qr.toFixed(2));

            // Balance visual
            $('#balance-total-label').text('Bs. ' + total.toFixed(2));
            $('#balance-sum-label').text('Bs. ' + (cash + qr).toFixed(2));

            let $status = $('#balance-status');
            let $box    = $('#balance-box-expense');

            if (cash > 0 && qr > 0) {
                $status.html('<span class="text-success"><i class="fa fa-check-circle"></i> Cuadrado</span>');
                $box.css('border-color', '#a5d6a7');
            } else if (cash <= 0) {
                $status.html('<span class="text-warning"><i class="fa fa-exclamation-circle"></i> Ingrese monto en Efectivo</span>');
                $box.css('border-color', '#ffe082');
            } else {
                // qr == 0: no debería pasar pero por si acaso
                $status.html('<span class="text-danger"><i class="fa fa-times-circle"></i> QR no puede ser 0</span>');
                $box.css('border-color', '#ef9a9a');
            }
        }

        // ── Agregar / quitar filas ────────────────────────────────────────────────
        function removeRowExpense(btn) {
            if ($('#tbody-expenses tr').length > 1) {
                $(btn).closest('tr').remove();
                calculateTotalExpense();
            }
        }

        // ── Mostrar panel según método de pago ────────────────────────────────────
        function showPaymentPanelExpense(type) {
            $('#panel-solo-cash, #panel-solo-qr, #panel-mixed').hide();
            $('#input-cash-solo, #input-qr-solo, #input-cash-mixed, #input-qr-mixed').val('').removeAttr('required');

            // Limpiar campos ocultos para evitar enviar dobles
            $('#panel-solo-cash input[name="amount_qr"]').val(0);
            $('#panel-solo-qr input[name="amount_cash"]').val(0);

            let total = parseFloat($('#input-total-amount').val()) || 0;

            if (type === 'Efectivo') {
                $('#panel-solo-cash').show();
                $('#input-cash-solo').val(total.toFixed(2)).attr('required', true);
            } else if (type === 'Qr') {
                $('#panel-solo-qr').show();
                $('#input-qr-solo').val(total.toFixed(2)).attr('required', true);
            } else if (type === 'Efectivo y Qr') {
                $('#panel-mixed').show();
                $('#input-cash-mixed, #input-qr-mixed').attr('required', true);
                $('#input-cash-mixed').val('').focus();
                $('#input-qr-mixed').val(total.toFixed(2));
                syncMixedExpense();
            }

            $('#payment-panel-expense').show();
        }

        window.addEventListener('load', function () {
            // Cambio de método de pago
            $('#select-payment_type-expense').on('change', function () {
                showPaymentPanelExpense($(this).val());
            });

            // Submit: construir observation y validar mixto
            $('#form-create-expense').on('submit', function (e) {
                let type  = $('#select-payment_type-expense').val();
                let total = parseFloat($('#input-total-amount').val()) || 0;

                // Validación mixta
                if (type === 'Efectivo y Qr') {
                    let cash = parseFloat($('#input-cash-mixed').val()) || 0;
                    let qr   = parseFloat($('#input-qr-mixed').val())   || 0;
                    if (cash <= 0) {
                        e.preventDefault();
                        toastr.error('Debe ingresar un monto en Efectivo mayor a 0.');
                        $('#input-cash-mixed').focus();
                        return false;
                    }
                    if (qr <= 0) {
                        e.preventDefault();
                        toastr.error('El monto QR no puede ser 0. Si paga todo en efectivo, seleccione solo "Efectivo".');
                        return false;
                    }
                    if (Math.abs((cash + qr) - total) > 0.01) {
                        e.preventDefault();
                        toastr.error('La suma de Efectivo + QR debe ser igual al total del gasto (Bs. ' + total.toFixed(2) + ').');
                        return false;
                    }
                }

                // Validación: total > 0
                if (total <= 0) {
                    e.preventDefault();
                    toastr.error('El total del gasto debe ser mayor a 0.');
                    return false;
                }

                // Poblar hidden amounts antes de enviar
                if (type === 'Efectivo') {
                    $('#hidden-amount-cash').val(total.toFixed(2));
                    $('#hidden-amount-qr').val('0');
                } else if (type === 'Qr') {
                    $('#hidden-amount-cash').val('0');
                    $('#hidden-amount-qr').val(total.toFixed(2));
                } else if (type === 'Efectivo y Qr') {
                    let cashF = parseFloat($('#input-cash-mixed').val()) || 0;
                    let qrF   = parseFloat($('#input-qr-mixed').val())   || 0;
                    $('#hidden-amount-cash').val(cashF.toFixed(2));
                    $('#hidden-amount-qr').val(qrF.toFixed(2));
                }

                // Construir observation
                let observation = '';
                $('.tr-expense').each(function () {
                    let detail   = $(this).find('.input-detail').val();
                    let quantity = $(this).find('.input-quantity').val();
                    let price    = $(this).find('.input-price').val();
                    let subtotal = $(this).find('.input-subtotal').val();
                    if (detail) {
                        observation += detail + ' (' + quantity + ' x ' + price + ' = ' + subtotal + '), ';
                    }
                });
                if (observation.length > 2) {
                    observation = observation.slice(0, -2);
                }
                $('#input-observation').val(observation);
            });

            // Limpiar modal al cerrarse
            $('#modal-create-expense').on('hidden.bs.modal', function () {
                $('#select-payment_type-expense').val('');
                $('#payment-panel-expense').hide();
                $('#input-grand-total, #input-total-amount').val('0.00');
                // Dejar solo la primera fila
                $('#tbody-expenses tr:not(:first)').remove();
                $('#tbody-expenses .input-detail').val('');
                $('#tbody-expenses .input-quantity').val('1');
                $('#tbody-expenses .input-price').val('');
                $('#tbody-expenses .input-subtotal').val('0.00');
            });
        });
    </script>
</form>
