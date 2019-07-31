function onResize() {
	if ($j(window).width() <= 640) {
		$j('.payMethodList').addClass('mobile');
	} else {
		$j('.payMethodList').removeClass('mobile');
	}
}
onResize();
$j(window).resize(function() { onResize(); });

function getBankName(id) {
	return JSON.parse($j('#p24bankNames').val())[parseInt(id)] ;
}

function setP24method(method) {
	method = parseInt(method);
	$j('input[name="payment[method_id]"]').val( method > 0 ? method : "" );
	$j('input[name="payment[method_name]"]').val( method > 0 ? getBankName(method): "" );
}

function setP24recurringId(id,name) {
	id = parseInt(id);
	if (name ==  undefined) name = $j('[data-cc='+id+'] .bank-name').text().trim() + ' - ' + $j('[data-cc='+id+'] .bank-logo span').text().trim();
	$j('input[name="payment[cc_id]"]').val( id > 0 ? id : "" );
	$j('input[name="payment[cc_name]"]').val( id > 0 ? name : "" );
	if (id > 0) setP24method(0);
}

$j('.bank-box').click(function(){
	$j('.bank-box').removeClass('selected').addClass('inactive');
	$j(this).addClass('selected').removeClass('inactive');
	setP24method($j(this).attr('data-id'));
	setP24recurringId($j(this).attr('data-cc'));
});
$j('.bank-item input').change(function(){
	setP24method($j(this).attr('data-id'));
	setP24recurringId($j(this).attr('data-cc'), $j(this).attr('data-text'));
});

function clickFakePaymentMethod(ob, hideDiv) {
	$j('.bank-box').removeClass('selected inactive');
    setP24method();
    setP24recurringId("");
	payment.switchMethod('dialcom_przelewy');
	if (hideDiv != false) {
		$j('#payment_form_dialcom_przelewy').hide();
	}

	$j('[data-value*=dialcom_przelewy]').each(function(){
		$j(this).val($j(this).attr('data-value'));
	});

	var newValue = $j(ob).attr('data-fake');
	var obChange = $j('[name="payment[method]"][value="'+newValue+'"]');
		obChange.val(obChange.attr('data-value'));
	$j(ob).val(newValue);
	if (parseInt($j(ob).attr('data-method')) > 0) {
        var chosenMethod = $j(ob).attr('data-method').split('|');
        setP24method(chosenMethod[0]);
        setP24recurringId("");
        if (chosenMethod.size() == 2) {
            setP24recurringId(chosenMethod[1], $j(ob).next('label').text());
        }
	}
	$j('[name="payment[method]"][id*=dialcom]').each(function(){ console.log($j(this).attr('data-value') + ' -> ' + $j(this).val()); });
}

$j(document).ready(function(){
	$j('[name=payment_method_id]:checked').trigger("change");
});