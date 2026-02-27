<div class="pro-wallet-container">
    <div class="pro-wallet-fab">
        {{-- Nota: El ícono de billetera abierta (fa-wallet-open) es de FontAwesome Pro. --}}
        {{-- Si lo tienes, puedes reemplazar 'fa-folder-open' por 'fa-wallet-open' en el ícono de abajo. --}}
        <i class="fa-solid fa-wallet icon-closed"></i>
        <i class="fa-solid fa-folder-open icon-open"></i>
    </div>

    <div class="pro-wallet-window">
        <div class="pro-wallet-header">
            <h6>Resumen de Saldo</h6>
            <div class="pro-wallet-header-actions">
                <a href="#" title="Actualizar" class="pro-refresh-button">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </a>
            </div>
        </div>

        @if (!$globalFuntion_cashierMoney['cashier'])
            <div class="pro-wallet-empty">
                <i class="fa-solid fa-store-slash fa-2x" style="margin-bottom: 15px;"></i>
                <p>No hay ninguna caja abierta.</p>
            </div>
        @else
            <div class="pro-wallet-body">
                <div class="pro-balance-grid">
                    <div class="pro-balance-summary">
                        <small><i class="fa-solid fa-money-bill"></i> Disponible</small>
                        <span class="total-amount">
                            {{ number_format($globalFuntion_cashierMoney['amountEfectivoCashier'], 2, ',', '.') }}</span>
                    </div>
                    <div class="pro-balance-summary">
                        <small><i class="fa-solid fa-qrcode"></i> Disponible</small>
                        <span class="total-amount">
                            {{ number_format($globalFuntion_cashierMoney['amountQrCashier'], 2, ',', '.') }}</span>
                    </div>
                </div>
                <ul class="pro-balance-details">
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-arrow-trend-up detail-icon income"></i>
                            Ingreso Efectivo
                        </div>
                        <span class="detail-amount income">+ Bs.
                            {{ number_format($globalFuntion_cashierMoney['paymentEfectivoIngreso'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-qrcode detail-icon income"></i>
                            Ingreso Qr
                        </div>
                        <span class="detail-amount income">+ Bs.
                            {{ number_format($globalFuntion_cashierMoney['paymentQrIngreso'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-arrow-trend-down detail-icon expense"></i>
                            Egreso Efectivo
                        </div>
                        <span class="detail-amount expense">- Bs.
                            {{ number_format($globalFuntion_cashierMoney['paymentEfectivoEgreso'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-qrcode detail-icon expense"></i>
                            Egreso Qr
                        </div>
                        <span class="detail-amount expense">- Bs.
                            {{ number_format($globalFuntion_cashierMoney['paymentQrEgreso'], 2, ',', '.') }}</span>
                    </li>
                    <li>
                        <div class="detail-label">
                            <i class="fa-solid fa-piggy-bank detail-icon assigned"></i>
                            Asignado a Caja
                        </div>
                        <span class="detail-amount assigned">+ Bs.
                            {{ number_format($globalFuntion_cashierMoney['cashierIn'], 2, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</div>

<style>
    /* --- Professional Wallet Widget --- */
    .pro-wallet-container {
        position: fixed;
        bottom: 50px;
        right: 20px;
        z-index: 1200;
        font-family: 'Open Sans', sans-serif;
    }

    .pro-wallet-fab {
        width: 50px;
        height: 50px;
        background: linear-gradient(45deg, #000000, #2980b9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(41, 128, 185, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: pulse 2.5s infinite;
    }

    .pro-wallet-fab .icon-open {
        display: none;
    }
    .pro-wallet-fab .icon-closed {
        display: inline-block;
    }

    .pro-wallet-container:hover .pro-wallet-fab .icon-open {
        display: inline-block;
    }
    .pro-wallet-container:hover .pro-wallet-fab .icon-closed {
        display: none;
    }

    .pro-wallet-fab:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(41, 128, 185, 0.5);
        animation-play-state: paused;
    }

    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(41, 128, 185, 0.7);
        }
        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(41, 128, 185, 0);
        }
        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(41, 128, 185, 0);
        }
    }

    .pro-wallet-window {
        position: absolute;
        bottom: 75px;
        right: 0;
        width: 350px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 12px 50px rgba(44, 62, 80, 0.25);
        border: 1px solid #e7eaf3;
        overflow: hidden;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: bottom right;
    }

    .pro-wallet-container:hover .pro-wallet-window {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }

    .pro-wallet-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #e7eaf3;
        background-color: #f8f9fa;
        cursor: default;
    }

    .pro-wallet-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
        color: #2c3e50;
    }

    .pro-wallet-header-actions {
        display: flex;
        align-items: center;
    }

    .pro-refresh-button {
        color: #95a5a6;
        transition: all 0.2s ease;
        font-size: 16px;
        text-decoration: none;
        line-height: 1;
    }

    .pro-refresh-button:hover {
        color: #2c3e50;
        transform: rotate(135deg);
    }

    .pro-wallet-empty {
        padding: 40px 20px;
        text-align: center;
        color: #7f8c8d;
    }

    .pro-wallet-body {
        padding: 0;
    }

    .pro-balance-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        background-color: #f8f9fa;
    }

    .pro-balance-summary {
        padding: 20px;
        text-align: center;
    }
    .pro-balance-summary:first-child {
        border-right: 1px solid #e7eaf3;
    }

    .pro-balance-summary small {
        font-size: 0.85rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pro-balance-summary .total-amount {
        display: block;
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
        margin-top: 5px;
    }

    .pro-balance-details {
        list-style: none;
        padding: 10px 20px;
        margin: 0;
    }

    .pro-balance-details li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f5;
        transition: background-color 0.2s ease-in-out;
    }

    .pro-balance-details li:hover {
        background-color: #f8f9fa;
    }

    .pro-balance-details li:last-child {
        border-bottom: none;
    }

    .detail-label {
        display: flex;
        align-items: center;
        font-size: 0.95rem;
        color: #34495e;
    }

    .detail-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: white;
        flex-shrink: 0;
    }

    .detail-icon.income { background-color: #27ae60; }
    .detail-icon.expense { background-color: #c0392b; }
    .detail-icon.assigned { background-color: #2980b9; }

    .detail-amount {
        font-weight: 600;
        font-size: 1rem;
    }

    .detail-amount.income {
        color: #27ae60;
    }

    .detail-amount.expense {
        color: #c0392b;
    }

    .detail-amount.assigned {
        color: #2980b9;
    }

    /* --- Icon Animations on Hover --- */
    .pro-balance-details li:hover .detail-icon.income {
        animation: icon-pop-up 0.5s ease-out;
    }

    .pro-balance-details li:hover .detail-icon.expense {
        animation: icon-shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
    }

    .pro-balance-details li:hover .detail-icon.assigned {
        animation: icon-pulse 0.5s ease-in-out;
    }

    @keyframes icon-pop-up {
        0% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-8px) scale(1.2) rotate(5deg); }
        100% { transform: translateY(0) scale(1); }
    }

    @keyframes icon-shake {
        10%, 90% { transform: translate3d(-1px, 0, 0) rotate(-3deg); }
        20%, 80% { transform: translate3d(2px, 0, 0) rotate(3deg); }
        30%, 50%, 70% { transform: translate3d(-3px, 0, 0) rotate(-3deg); }
        40%, 60% { transform: translate3d(3px, 0, 0) rotate(3deg); }
    }

    @keyframes icon-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const walletContainer = document.querySelector('.pro-wallet-container');
    if (!walletContainer) return;

    const refreshBtn = walletContainer.querySelector('.pro-refresh-button');

    // --- Recargar al hacer clic en Actualizar ---
    if (refreshBtn) {
        refreshBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const icon = refreshBtn.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spin');
            }
            // Pequeño retraso para que se vea la animación del ícono
            setTimeout(() => {
                location.reload();
            }, 500);
        });
    }
});
</script>
