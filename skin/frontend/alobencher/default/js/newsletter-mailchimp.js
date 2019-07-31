
var mailchimpConfig = {
        baseUrl: 'mc.us20.list-manage.com',
        uuid: '31ed6da502a5c603fd2c2d8a7',
        lid: '96a6b81744'
    };
	
var chimpPopupLoader = document.createElement("script");
chimpPopupLoader.src = '//downloads.mailchimp.com/js/signup-forms/popup/unique-methods/embed.js';
chimpPopupLoader.setAttribute('data-dojo-config', 'usePlainJson: true, isDebug: false');

// jQuery.noConflict();
// (function($) {
  // $(document).ready(function() {
   		// window.dojoRequire(["mojo/signup-forms/Loader"], function (L) { L.start({"baseUrl": mailchimpConfig.baseUrl, "uuid": mailchimpConfig.uuid, "lid": mailchimpConfig.lid})});
        // document.cookie = 'MCPopupClosed=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
        // document.cookie = 'MCPopupSubscribed=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
  // });
// })(jQuery);
// ##jQuery(function ($) {
    // document.body.appendChild(chimpPopupLoader);

    // jQuery.on("load", function () {
        // require(["mojo/signup-forms/Loader"], function (L) { L.start({"baseUrl": mailchimpConfig.baseUrl, "uuid": mailchimpConfig.uuid, "lid": mailchimpConfig.lid})});
        // document.cookie = 'MCPopupClosed=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
        // document.cookie = 'MCPopupSubscribed=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
    // });

// });
// <script type="text/javascript" 
// src="//downloads.mailchimp.com/js/signup-forms/popup/unique-methods/embed.js" 
// data-dojo-config="usePlainJson: true, isDebug: false">
// </script>
// <script type="text/javascript">
// window.dojoRequire(["mojo/signup-forms/Loader"], function(L) { 
// L.start({"baseUrl":"mc.us20.list-manage.com",
// "uuid":"31ed6da502a5c603fd2c2d8a7",
// "lid":"96a6b81744",
// "uniqueMethods":true}) })
// document.cookie = 'MCPopupClosed=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
// document.cookie = 'MCPopupSubscribed=;path=/;expires=Thu, 01 Jan 1970 00:00:00 UTC;';
// </script>