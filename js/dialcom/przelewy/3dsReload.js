window.setTimeout(function(){
    if (opener && opener.P24_Transaction) {
        opener.P24_Transaction.threeDSReturn(window);
        window.close();
    }
},1000);