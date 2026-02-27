$(document).ready(function(){
    // let cortes = new Array('200', '100', '50', '20', '10', '5', '2', '1', '0.5', '0.2', '0.1');
    let cortes = new Array('200', '100', '50', '20', '10', '5', '2', '1', '0.5');
    cortes.map(function(value){
        $('#lista_cortes').append(`<tr>
            <td><h4 style="margin: 0px"><img src="${APP_URL}/images/cash/${value}.jpg" alt="${value} Bs." width="70px"> ${value} Bs. </h4></td>
            <td>
                <input type="hidden" name="cash_value[]" value="${value}" required>
                <input type="number" name="quantity[]" id="input-cash-${value.replace('.', '-')}" min="0" step="1" style="width:80px" data-value="${value}" class="form-control input-corte" value="0" required>
            </td>
            <td style="font-size: 20px"><label id="label-${value.replace('.', '')}">0.00</label><input type="hidden" class="input-subtotal" id="input-${value.replace('.', '')}"></td>
        </tr>`);
    });

    $('.input-corte').keyup(function(){
        let corte = $(this).data('value');
        let cantidad = $(this).val() ? $(this).val() : 0;
        calcular_subtottal(corte, cantidad);
    });
    $('.input-corte').change(function(){
        let corte = $(this).data('value');
        let cantidad = $(this).val() ? $(this).val() : 0;
        calcular_subtottal(corte, cantidad);
    });
});

function calcular_subtottal(corte, cantidad){
    let total = (parseFloat(corte)*parseFloat(cantidad)).toFixed(2);
    $('#label-'+corte.toString().replace('.', '')).text(total);
    $('#input-'+corte.toString().replace('.', '')).val(total);
    calcular_total();
}

function calcular_total(){
    let total = 0;
    $(".input-subtotal").each(function(){
        total += $(this).val() ? parseFloat($(this).val()) : 0;
    });
    $('#input-total').val(total);
    $('#label-total').text(total.toFixed(2));
}