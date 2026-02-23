@php
    $aux = new \App\Http\Controllers\SolucionDigitalController();
    $payment_alert = $aux->payment_alert();
    $is_numeric_alert = is_numeric($payment_alert) && setting('system.payment-alert');
    $is_finalizado_alert = $payment_alert == 'finalizado';

    if ($is_numeric_alert || $is_finalizado_alert) {
        $solucionDigital = Illuminate\Support\Facades\DB::connection('solucionDigital')->table('settings')->get();

        $alert_type = $is_numeric_alert ? 'warning' : 'error';
        $alert_icon = $is_numeric_alert ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-ban';
        $alert_title = $is_numeric_alert ? '¡Servicio Próximo a Finalizar!' : '¡Atención: Pago Pendiente!';
        $alert_color = $is_numeric_alert ? '#FF9800' : '#F44336';
        $alert_bg = $is_numeric_alert ? '#FFF3E0' : '#FFEBEE';

        if ($is_numeric_alert) {
            if ($payment_alert == 0) {
                $alert_message =
                    '¡Último día de servicio! Su acceso al sistema finaliza <strong>hoy</strong>. Para evitar la suspensión de su cuenta, por favor, <strong>pague su membresía</strong>.<br><strong>WhatsApp: 67285914</strong>';
            } else {
                $alert_message = "Recordatorio: Su servicio finaliza en <strong>{$payment_alert} días</strong>. Para evitar la interrupción de sus operaciones, no olvide <strong>pagar su membresía</strong> a tiempo.<br><strong>WhatsApp: 67285914</strong>'";
            }
        } else {
            $alert_message =
                '¡Servicio suspendido! Su membresía ha expirado y el registro de ventas está desactivado. Para restaurar el acceso completo al sistema, es indispensable que <strong>pague su membresía</strong>.<br><strong>WhatsApp: 67285914</strong>';
        }
    }
@endphp

@if ($is_numeric_alert || $is_finalizado_alert)
    <style>
        @keyframes pulse-animation {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.015);
            }

            100% {
                transform: scale(1);
            }
        }

        #payment-alert-banner {
            animation: pulse-animation 2s infinite;
        }
    </style>
@endif

@if ($is_numeric_alert || $is_finalizado_alert)
    <div class="panel-body">
        <div class="row">
            <div id="payment-alert-banner"
                style="
                                                        display: flex;
                                                        align-items: flex-start;
                                                        background-color: {{ $alert_bg }};
                                                        border-left: 5px solid {{ $alert_color }};
                                                        padding: 16px;
                                                        margin: 20px 0;
                                                        border-radius: 8px;
                                                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                                        font-family: 'Open Sans', sans-serif;
                                                        transition: opacity 0.3s ease;
                                                    ">
                <div style="margin-right: 16px; color: {{ $alert_color }}; font-size: 22px; margin-top: 2px;">
                    <i class="{{ $alert_icon }}"></i>
                </div>

                <div style="flex-grow: 1;">
                    <h4 style="margin: 0 0 8px 0; font-weight: 700; color: {{ $alert_color }}; font-size: 16px;">
                        {{ $alert_title }}
                    </h4>
                    <p style="margin: 0 0 16px 0; color: #555; font-size: 14px; line-height: 1.6;">
                        {!! $alert_message !!}
                    </p>

                    <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                        <a href="https://wa.me/{{ $solucionDigital->where('key', 'contact.phone')->first()->value }}?text={{ urlencode('Hola, me gustaría renovar mi membresía para el sistema ' . setting('admin.title') . '.') }}"
                            target="_blank"
                            style="
                                                                    background-color: #25D366;
                                                                    color: white;
                                                                    padding: 8px 16px;
                                                                    border-radius: 6px;
                                                                    text-decoration: none;
                                                                    font-weight: 600;
                                                                    font-size: 13px;
                                                                    display: inline-flex;
                                                                    align-items: center;
                                                                    gap: 8px;
                                                                    transition: background-color 0.2s ease;
                                                                "
                            onmouseover="this.style.backgroundColor='#128C7E'"
                            onmouseout="this.style.backgroundColor='#25D366'">
                            <i class="fa-brands fa-whatsapp"></i>
                            WhatsApp
                        </a>
                    </div>
                </div>

                <button onclick="document.getElementById('payment-alert-banner').style.display='none';"
                    style="
                                                            background: none;
                                                            border: none;
                                                            color: #999;
                                                            font-size: 20px;
                                                            cursor: pointer;
                                                            padding: 0 8px;
                                                            margin-left: 16px;
                                                            line-height: 1;
                                                            align-self: flex-start;
                                                        "
                    title="Cerrar">
                    &times;
                </button>
            </div>
        </div>
    </div>
@endif