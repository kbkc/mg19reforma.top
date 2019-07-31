function getBanksList() {
	var banksList = [];
	$j('#przelewytab1_paymethods_paymethods_all option').each(function(){
		banksList.push({ id: $j(this).val(), name: $j(this).text() });
	});
    if (banksList.length == 0) {
        banksList.push({id: 25, name: ""});
        banksList.push({id: 31, name: ""});
        banksList.push({id: 112, name: ""});
        banksList.push({id: 20, name: ""});
        banksList.push({id: 65, name: ""});
    }
	return banksList;
}

function getBankBox(id,name) {
	if (name == undefined) name='';
	return '<a class="bank-box" data-id="'+id+'"><div class="bank-logo bank-logo-'+id+'"></div><div class="bank-name">'+name+'</div></a>';
}

function toggleSomething(toggle, selector) {
	if (toggle) {
		$j(selector).show();
	} else {
		$j(selector).hide();
	}
}

function updatePaymethods() {
	$j('.bank-box').removeClass('ui-helper-unrotate');
	var maxNo = parseInt($j('.paymethod .selected').attr('data-max'));
	if (maxNo > 0) {
		if ($j('.paymethod .selected a[data-id]').length > maxNo) {
			var i = 0;
			$j('.paymethod .selected a[data-id]').each(function(){
				i++;
				if (i > maxNo) {
					$j('.paymethod .available')
						.prepend($j(this))
						.append($j('#clear'));
				}
			});
		}
	}
	$j('#przelewytab1_paymethods_paymethod_first').val('');
	$j('.paymethod .selected a[data-id]').each(function(){
		$j('#przelewytab1_paymethods_paymethod_first').val(
			$j('#przelewytab1_paymethods_paymethod_first').val() +
			($j('#przelewytab1_paymethods_paymethod_first').val().length ?',':'') +
			$j(this).attr('data-id')
		);
	});
	$j('#przelewytab1_paymethods_paymethod_second').val('');
	$j('.paymethod .available a[data-id]').each(function(){
		$j('#przelewytab1_paymethods_paymethod_second').val(
			$j('#przelewytab1_paymethods_paymethod_second').val() +
			($j('#przelewytab1_paymethods_paymethod_second').val().length ?',':'') +
			$j(this).attr('data-id')
		);
	});
}
function updatePaymethodPromoted() {
    var paymethod_promoted = [];
    $j('.paylistprom:checked').each(function() {
        paymethod_promoted.push($j(this).attr('data-val'));
    });
    $j('#przelewytab1_promoted_paymethod_promoted').val(paymethod_promoted.join(','));
}

$j(document).ready(function() {

    if ($j('fieldset#przelewytab1_paymethods').length) {

        var checkbox = $j('tr#row_przelewytab1_paymethods_paymethod_first .use-default').html();
        var scope_label = $j('tr#row_przelewytab1_paymethods_paymethod_first .scope-label').html();
        var use_parent_scope_payment_methods = checkbox ? '<div id="use-parent-scope-payment-methods-list">' + checkbox + '<span id="use-parent-scope-payment-methods-list-scope-name">' + scope_label + '</span>' + '</div>' : '';

        var checkbox = $j('tr#row_przelewytab1_promoted_paymethod_promoted .use-default').html();
        var scope_label = $j('tr#row_przelewytab1_promoted_paymethod_promoted .scope-label').html();
        var use_parent_scope_payment_promoted = checkbox ? '<div id="use-parent-scope-payment-promoted">' + checkbox + '<span id="use-parent-scope-payment-promoted-scope-name">' + scope_label + '</span>' + '</div>' : '';

        // kolejność metod płatności
        $j('tr#row_przelewytab1_paymethods_paymethod_first,tr#row_przelewytab1_paymethods_paymethod_second,tr#row_przelewytab1_paymethods_paymethods_all,tr#row_przelewytab1_promoted_paymethod_promoted').hide();
        $j('#przelewytab1_paymethods').append(
            '<style>' +
            '#use-parent-scope-payment-promoted { margin: 10px 16px; margin-left: 520px; }' +
            '#use-parent-scope-payment-methods-list { display: inline-block; margin-left: 327px; }' +
            '#use-parent-scope-payment-methods-list label,#use-parent-scope-payment-promoted label { margin-right: 10px; }' +
            '#use-parent-scope-payment-methods-list-scope-name, #use-parent-scope-payment-promoted-scope-name { color: #6f8992; font-size: .9em; }' +
            '.bank-placeholder { opacity: 0.6; } ' +
            '.sortable.available .bank-box:last-child { clear: both; } ' +
            '.sortable.selected::before { content: "Upuść tutaj max. 5 metod płatności"; color: gray; position: absolute; text-align: center; vertical-align: middle; margin: 3em 0 0 22em; } ' +
            '.sortable.selected .bank-box { position: relative; z-index: 2; } ' +

            '.bank-box.ui-sortable-helper { transform: rotate(10deg); box-shadow: 10px 10px 10px lightgray; } ' +
            '.ui-helper-unrotate { transform: rotate(0deg) !important; box-shadow: 0 0 0 lightgray !important; } ' +
            '.bank-box { transition: transform 0.2s ease, box-shadow 0.2s ease; } ' +

            'a.bank-box { text-decoration: none; } ' +
            '.paylistprom_item { cursor: pointer; display: block !important; background: white; background: rgba(255,255,255,0.8); font-weight: normal !important; } ' +
            '</style>' +

            '<div class="paymethod">' +
            '<div id="first-methods-payments-przelewy24">Metody płatności widoczne od razu: ' + use_parent_scope_payment_methods + '</div><br>' +
            '<div class="sortable selected" data-max="5" style="width: 730px; border: 5px dashed lightgray; height: 80px; padding: 0.5em; overflow: hidden;"></div>' +
            '<div style="clear:both"></div> <span id="more-payments-text">Metody płatności widoczne po kliknięciu przycisku (więcej...): </span><br>' +
            '<div class="sortable available"></div>' +
            '</div>' +
            ''
        );

        $j('#use-parent-scope-payment-methods-list input').on('change', function() {
            $j('tr#row_przelewytab1_paymethods_paymethod_first .use-default input').trigger('click');
            $j('tr#row_przelewytab1_paymethods_paymethod_second .use-default input').trigger('click');
            $j('tr#row_przelewytab1_paymethods_paymethods_all .use-default input').trigger('click');
        });

        $j('#przelewytab1_promoted').append('<div class="promoted">' + use_parent_scope_payment_promoted + '<div id="paymethod_promote_list"></div>' + '</div>');
        $j.each(getBanksList(), function() {
            $j('#paymethod_promote_list').append(
                    '<label class="paylistprom_item paylistprom_item_' + this.id + '" ' +
                    'for="paylistprom_' + this.id + '"><span ' +
                    'style="cursor: ns-resize; display: inline-block" class="ui-icon ui-icon-grip-dotted-vertical"></span><input '+
                    'class="paylistprom" id="paylistprom_' + this.id + '" type="checkbox" data-val="' + this.id + '" style="position:relative; top: -4px"> <span '+
                    'style="position:relative; top: -2px">' + this.name + '</span></label>' +
                    ''
            );
        });

        $j('#use-parent-scope-payment-promoted input').on('change', function() {
            $j('tr#row_przelewytab1_promoted_paymethod_promoted .use-default input').trigger('click');
        });

        $j('#paymethod_promote_list')
            .css({ marginLeft: $j('tr#row_przelewytab1_promoted_show_promoted > td:first').outerWidth()})
            .sortable({ stop: function() { updatePaymethodPromoted(); }, axis: 'y', })
            .disableSelection()
        ;
        $j('.paylistprom').change(function(){ updatePaymethodPromoted(); });

        $j.each($j('#przelewytab1_promoted_paymethod_promoted').val().split(',').reverse(), function() {
            $j('.paylistprom_item_' + this.toString()).prependTo('#paymethod_promote_list').find('input').attr('checked', true);
        });


        $j('#przelewytab1_paymethods_showpaymethods').change(function(){
            toggleSomething($j('#przelewytab1_paymethods_showpaymethods').val()=='1', '.paymethod');
        });

        $j('#przelewytab1_promoted_show_promoted').change(function(){
            toggleSomething($j('#przelewytab1_promoted_show_promoted').val()=='1', '.promoted');
        });

        $j('#przelewytab1_paymethods_showpaymethods,#przelewytab1_promoted_show_promoted').trigger('change');

        $j.each(getBanksList(), function() {
            $j('.sortable.available').append(getBankBox(this.id, this.name));
        });

        $j('.sortable.available').append('<div style="clear:both" id="clear"></div>');

        if ($j('#przelewytab1_paymethods_paymethod_first').val().length > 0) {
            $j.each($j('#przelewytab1_paymethods_paymethod_first').val().split(','), function(i,v) {
                $j('.bank-box[data-id=' + v + ']').appendTo('.paymethod .selected');
            });
        }
        if ($j('#przelewytab1_paymethods_paymethod_second').val().length > 0) {
            $j.each($j('#przelewytab1_paymethods_paymethod_second').val().split(',').reverse(), function(i,v) {
                $j('.bank-box[data-id=' + v + ']').prependTo('.paymethod .available');
            });
        }
        updatePaymethods();

        $j(".sortable.selected,.sortable.available").sortable({
            connectWith: ".sortable.selected,.sortable.available",
            placeholder: "bank-box bank-placeholder",
            stop: function(){ updatePaymethods(); },
            revert: true,
            start:function(e,ui) {
                window.setTimeout(function(){
                    $j('.bank-box.ui-sortable-helper').on('mouseup', function() {
                        $j(this).addClass('ui-helper-unrotate');
                    });
                }, 100);
            },
        }).disableSelection();

    }

    if ($j('fieldset#przelewytab1_multicurr').length) {
        // subkonta walutowe
		// unchecking default values checkboxes
		var optionsArray = ['shopid', 'merchantid', 'salt', 'api'];
        optionsArray.each(function(index, element)
        {
        if ($j('#przelewytab1_multicurr_multicurr_'+index+'_inherit').attr('checked'))
        {
            
            $j('#przelewytab1_multicurr_multicurr_'+index+'_inherit').click();
        }
        });
        if ($j('select#przelewytab1_multicurr_multicurr_list option').length == 0) {
            $j('fieldset#przelewytab1_multicurr').closest('.section-config').hide();
        } else {
            $j('fieldset#przelewytab1_multicurr > table').hide();
            $j('select#przelewytab1_multicurr_multicurr_list option').each(function(){
                var key = $j(this).val();
                var name = $j(this).text();
                $j('fieldset#przelewytab1_multicurr').append('<div><h3>'+key+'</h3><table class="form-list multicurr" data-curr="'+key+'">'+
                '<tr><td class="merchantIdLabel label"></td><td class="value"><input type="text" class="input-text" id="'+key+'_merchantid"></td></tr>'+
                '<tr><td class="shopIdLabel label"></td><td class="value"><input type="text" class="input-text" id="'+key+'_shopid"></td></tr>'+
                '<tr><td class="crcKeyLabel label"></td><td class="value"><input type="text" class="input-text" id="'+key+'_salt"></td></tr>'+
                '<tr><td class="apiKeyLabel label"></td><td class="value"><input type="text" class="input-text" id="'+key+'_api"></td></tr>'+
                '</table></div>');
            });
            multicurrRead();
            $j('table[data-curr] input').on('keyup', function() { multicurrWrite(); });
            $j('table[data-curr] input').on('change', function() { multicurrWrite(); });
            $j('form#config_edit_form').on('submit', function() { multicurrWrite(); });
        }
    }
});

function multicurrReadField(name) {
    var val = $j('#przelewytab1_multicurr_multicurr_' + name).val();
    if (val.length) {
        var vals = val.split(',');
        $j.each(vals, function(index, item) {
            var props = item.split(':');
            if (props.length == 2) {
                $j('#' + props[0] + '_' + name).val(props[1]);
            }
        });
    }
}

function multicurrRead() {
    multicurrReadField('merchantid');
    multicurrReadField('shopid');
    multicurrReadField('salt');
    multicurrReadField('api');
}

function multicurrWriteField(name) {
    var newValArr = [];
    $j('select#przelewytab1_multicurr_multicurr_list option').each(function(){
        var key = $j(this).val();
        newValArr.push(key + ':' + $j('#' + key + '_' + name).val());
    });
    $j('#przelewytab1_multicurr_multicurr_' + name).val(newValArr.join(','));
}

function multicurrWrite() {
    multicurrWriteField('merchantid');
    multicurrWriteField('shopid');
    multicurrWriteField('salt');
    multicurrWriteField('api');
}

