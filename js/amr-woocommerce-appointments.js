//version: 1.3.3

jQuery(function($) {
    $(document).ready(function(){
        console.log('fac');
        setupFormEvents();
      
    
    }); // end document ready
   

    function setupFormEvents() {

        var selectedDate = new Date();

        var form = $('.wc-appointments-appointment-form-wrap.cart');
        // get changed durations
        form.on( 'addon-duration-changed', function(event ,duration) {
            var currentForm = $(this);
            var picker = currentForm.find('.picker');

            // var appointment_duration = parseInt( form.find('.picker').attr( 'data-appointment_duration' ), 10 );
            var addon_duration = parseInt( duration, 10 );
            // var combined_duration = parseInt( appointment_duration + addon_duration, 10 );
            // $(currentForm).find('.checkout-value').html(duration);

            selectedDate.setDate(selectedDate.getDate() + addon_duration);

            var month = selectedDate.toLocaleString('default', { month: 'long' });

            $(currentForm).find('.checkout-value').html(month + ' ' + selectedDate.getDate().toString() + ',' + selectedDate.getFullYear().toString());

            // see if there are unavailble dates that have been selected

            var selected_days = $(currentForm).find('.ui-datepicker-selected-day');
            $(selected_days).each(function() {
                if($(this).hasClass('fully_scheduled') || $(this).hasClass('not_appointable')) {
                    $(currentForm).find('.wc-appt-error').slideDown();
                    $(currentForm).find('.wc-appointments-appointment-form-button').prop('disabled', true);
                } else {
                    $(currentForm).find('.wc-appt-error').slideUp();
                    $(currentForm).find('.wc-appointments-appointment-form-button').prop('disabled', false);
                }
            })
            // setupFormCheckoutDate(currentForm);
        } );

        // get the start date
        form.on( 'date-selected', function(event, data) {

            var currentForm = $(this);
            var picker = currentForm.find('.picker');

            // reset the added days when changing a date
            picker.data( 'combined_duration', 1 );
            picker.data( 'addon_duration', 0 );

            
            var combined_duration = picker.data( 'combined_duration' ) ? picker.data( 'combined_duration' ) : 1;
            var stringDate = data;
            var date = stringDate.split("-"); 
        
            selectedDate = new Date(date[0],date[1]-1,date[2]);//Date object

            var month = selectedDate.toLocaleString('default', { month: 'long' });
            $(currentForm).find('.checkin-value').html(month + ' ' + date[2] + ', ' + date[0]);


            if(combined_duration == 1) {
                $(currentForm).find('.checkout-value').html(month + ' ' + date[2] + ', ' + date[0]);
            }

            currentForm.find('.wc-pao-addon-select').val('');
            // uncheck other items
            setTimeout(function() {
                console.log(currentForm.find('.highlighted_day:not(:first)'));

            currentForm.find('.highlighted_day:not(:first)').removeClass('ui-datepicker-selected-day');
            currentForm.find('.highlighted_day:not(:first)').removeClass('highlighted_day');

            }, 100);
          
        } );

        // form.on( 'click', '.appointable', function() {
        //     console.log("tits");
        //     console.log(this);
        //     var currentForm = $(this);
        //     currentForm.find('.highlighted_day:not(:first)').removeClass('ui-datepicker-selected-day');
        //     currentForm.find('.highlighted_day:not(:first)').removeClass('highlighted_day');

        //     var picker = currentForm.find('.picker');

        //     picker.data( 'combined_duration', 1 );
        //     picker.data( 'addon_duration', 0 );
        // });


    }

    // function setupFormCheckoutDate(form) {
    //     // var form = $('.wc-appointments-appointment-form-wrap.cart');
    //     var duration_drop_down = $('.wc-pao-addon-check-out select.wc-pao-addon-field')
    //     duration_drop_down.find('option').remove();
    //     duration_drop_down.append('<option data-raw-price="" data-price="" data-price-type="flat_fee" data-raw-duration="1" data-duration=" <span class=&quot;addon-duration&quot;><span class=&quot;amount-symbol&quot;>+</span>1 day</span>" data-duration-type="flat_time" value="2-days-2" data-label="2 Days">2 Days  +1 day</option>');
    //     duration_drop_down.append('<option data-raw-price="" data-price="" data-price-type="flat_fee" data-raw-duration="1" data-duration=" <span class=&quot;addon-duration&quot;><span class=&quot;amount-symbol&quot;>+</span>1 day</span>" data-duration-type="flat_time" value="7-days-7" data-label="7 Days">7 Days  +1 day</option>');
    // }
});
  