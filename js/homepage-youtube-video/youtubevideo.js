jQuery( document ).ready(function() {

    var videoWidth = jQuery('.col-main.col-lg-12').width();
    var ratio = 1.77;
    var videoHeight = videoWidth / ratio;

    jQuery('.widget-static-block iframe').width(videoWidth); 
    jQuery('.widget-static-block iframe').height(videoHeight); 

});

jQuery( window ).resize(function() {

    var videoWidth = jQuery('.col-main.col-lg-12').width();
    var ratio = 1.77;
    var videoHeight = videoWidth / ratio;

    jQuery('.widget-static-block iframe').width(videoWidth); 
    jQuery('.widget-static-block iframe').height(videoHeight); 
});