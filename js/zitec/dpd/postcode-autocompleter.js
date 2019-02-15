/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var zitecPostcodeAutocompleter = zitecPostcodeAutocompleter || {};

zitecPostcodeAutocompleter.Autocompleter = Class.create({
    initialize: function(fields, options) {
        var _this = this;
        this.className = 'zitec-postcode-autocompleter';
        this.fields = typeof fields !=='undefined'? fields : [];
        this.options = {}
        this.options.loadingImageUrl = options.loadingImageUrl;
        this.options.loadingText = options.loadingText;
        this.country = options.country;
        this.url = options.url;
        this.ajaxAutocompleter = new Class.create(Ajax.Autocompleter, {
            getUpdatedChoices: function () {
                this.startIndicator();
                var entry = encodeURIComponent(this.options.paramName) + '=' +
                    encodeURIComponent(this.getToken());

                this.options.parameters = this.options.callback ?
                    this.options.callback(this.element, entry) : entry;

                if (this.options.defaultParams)
                    this.options.parameters += '&' + this.options.defaultParams;
                this.options.parameters = (this.element).up('form').serialize();
                console.log(this.options);
                new Ajax.Request(this.url, this.options);
            }
        });
        if (!this.fields) {
            return;
        }
        fields['sections'].each(function(section) {
            _this.initializeForForm(section, fields[section].uniq());
        });
    },
    initializeForForm: function(section,fields) {
        var _this = this;
        fields.each(function(item) {
           var autocompleter = _this;
           $(item).on('change', function(event) {
               var id = $(this).readAttribute('id');
               var parts = id.split(':');
               var section = '';

               if(parts.length > 1) {
                   section = parts[0];
                   var fields = autocompleter.fields[parts[0]];
               }else {
                   var fields = autocompleter.fields['generic'];
               }

               var all_completed = true;
               var country = $(section + ':country_id');
               if(!country) {
                   country = $('country');
               }
               if(country.value !== autocompleter.country) {
                   return;
               }
               fields.each(function(item) {
                   if(item.value === '' && item.classList.contains('required-entry') ) {
                       all_completed = false;
                   }
               });
               if(!all_completed) {
                   return;
               }
               autocompleter.triggerPostcodeAutocomplete(section, this.up('form'));
           });
        });
    },
    triggerPostcodeAutocomplete : function(section, form) {
        if(section !== '') {
            var element = section + ':postcode';
        }
        else {
            var element = 'zip';
        }
        if(!$(element)) {
            return;
        }
        var indicator = element + '_autocomplete-indicator';
        if($(indicator)) {
            $(indicator).remove();
        }
        $(element).insert({
            'before': '<span id="' + indicator + '" class="autocomplete-indicator" style="display: none">' +
                '<img src="' + this.options.loadingImageUrl + '" alt="' + this.options.loadingText + '" class="v-middle"/>' +
                '</span>'
        });


        var postcodeAutocompleter = new this.ajaxAutocompleter(
            element,
            indicator,
            this.url,
            {
                paramName:"query",
                minChars:2,
                indicator:indicator,
                updateElement:getSelectionPostcodeValidateId,
                evalJSON:'force'
            }
        );
        function getSelectionPostcodeValidateId(li) {
            var element = $(section + ':postcode');
            if(!element) {
                element = $('zip');
            }
            element.setValue(li.getAttribute('postcode'));
        }
        postcodeAutocompleter.activate();
    }


}) ;
