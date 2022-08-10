//version: 1.4.4

jQuery(function($) {
    $(document).ready(function(){
        setupFormEvents();
        setupEventCalendarButtons();
        setupFlyFishingOptions();
        setupHorsebackRidingOptions();
    }); // end document ready

    /**
     * Function for adding days to a date
     */
    function addDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    }

    function setupFormEvents() {

        var selectedDate = new Date();

        var form = $('.wc-appointments-appointment-form-wrap.cart');
        // get changed durations
        form.on( 'addon-duration-changed', function(event, duration) {
            var currentForm = $(this);

            // var appointment_duration = parseInt( form.find('.picker').attr( 'data-appointment_duration' ), 10 );
            var addon_duration = parseInt( duration, 10 );
            var checkin_checkout_div = currentForm.find('.wc-appt-checkin-checkout-dates');

            var type = 'other';
            if(checkin_checkout_div.hasClass('type-guest-cabin')) {
                addon_duration++;
                type = 'guest_cabin';
            }
            var endDate = addDays(selectedDate, addon_duration);
            var month = endDate.toLocaleString('default', { month: 'long' });

            if(type == 'guest_cabin') {
                $(currentForm).find('.checkout-value').html('10 AM, ' + month + ' ' + endDate.getDate().toString() + ', ' + endDate.getFullYear().toString());
            } else {
                $(currentForm).find('.checkout-value').html(month + ' ' + endDate.getDate().toString() + ', ' + endDate.getFullYear().toString());
            }

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
            });
            if(type == 'guest_cabin') {
                $(currentForm).find('.ui-datepicker-checkout-day').removeClass('ui-datepicker-checkout-day');

                var last_selected_day = $(selected_days).last();

                if(last_selected_day.length) {
                    $(currentForm).find('.ui-datepicker-checkout-day').removeClass('ui-datepicker-checkout-day');
                    var add_checkout_day = last_selected_day.nextAll( 'td' ).add( last_selected_day.closest( 'tr' ).nextAll().find( 'td' ) ).slice( 0, 1 ).addClass( 'ui-datepicker-checkout-day' );
                } else {
                    var selected_day = $(currentForm).find('.ui-datepicker-current-day');
                    var add_checkout_day = selected_day.nextAll( 'td' ).add( selected_day.closest( 'tr' ).nextAll().find( 'td' ) ).slice( 0, 1 ).addClass( 'ui-datepicker-checkout-day' );
                }

            }
        } );

        // get the start date
        form.on( 'date-selected', function(event, data) {
            var currentForm = $(this);
            var picker = currentForm.find('.picker');
            var stringDate = data;
            var date = stringDate.split("-"); 
            selectedDate = new Date(date[0],date[1]-1,date[2]);//Date object

            // reset quantity to 1 when changing date
            // fix for number of people when date is partially
            // scheduled 
            var quantity = currentForm.find('.input-text.qty');
            quantity.val('1').trigger("change");

            // and pause and set it again just in case
            setTimeout(function() {
                quantity.val('1').trigger("change");
            },750);
            quantity.val('1').trigger("change");

            if(picker.data('duration_unit') == 'day') {
                // Get the reservation type
                var checkin_checkout_div = currentForm.find('.wc-appt-checkin-checkout-dates');


                // if(type == 'guest-cabin') {
                //     var last_selected_day = $(selected_days).last();
                //     var add_checkout_day = last_selected_day.nextAll( 'td' ).add( last_selected_day.closest( 'tr' ).nextAll().find( 'td' ) ).slice( 0, 1 ).addClass( 'ui-datepicker-checkout-day' );
                // }


                // reset the added days when changing a date
                picker.data( 'combined_duration', 1 );
                picker.data( 'addon_duration', 0 );
                if(checkin_checkout_div.hasClass('type-guest-cabin')) {
                    var type = 'guest_cabin';
                    var combined_duration = picker.data( 'combined_duration' ) ? parseInt(picker.data( 'combined_duration' )) + 1 : 2;

                    // This needs a delay so that the classes are in place
                    setTimeout(function() {
                        $(currentForm).find('.ui-datepicker-checkout-day').removeClass('ui-datepicker-checkout-day');
                        var selected_day = $(picker).find('.ui-datepicker-current-day');
                        var add_checkout_day = selected_day.nextAll( 'td' ).add( selected_day.closest( 'tr' ).nextAll().find( 'td' ) ).slice( 0, 1 ).addClass( 'ui-datepicker-checkout-day' );
                    }, 500);
                   
                } else {
                    var type = 'other';
                    var combined_duration = picker.data( 'combined_duration' ) ? picker.data( 'combined_duration' ) : 1;
                }

                var month = selectedDate.toLocaleString('default', { month: 'long' });

                if(type == 'guest_cabin') {
                    var endDate = addDays(selectedDate, 1);
                    var endMonth = endDate.toLocaleString('default', { month: 'long' });
                    $(currentForm).find('.checkin-value').html('4 PM, ' + month + ' ' + date[2] + ', ' + date[0]);
                    $(currentForm).find('.checkout-value').html('10 AM, ' + endMonth + ' ' + endDate.getDate().toString() + ', ' + endDate.getFullYear().toString());
                } else {
                    $(currentForm).find('.checkin-value').html(month + ' ' + date[2] + ', ' + date[0]);
                    if(combined_duration == 1) {
                        $(currentForm).find('.checkout-value').html(month + ' ' + date[2] + ', ' + date[0]);
                    }
                }


                currentForm.find('.wc-pao-addon-select').val('');
                // uncheck other items
                setTimeout(function() {

                currentForm.find('.highlighted_day:not(:first)').removeClass('ui-datepicker-selected-day');
                currentForm.find('.highlighted_day:not(:first)').removeClass('highlighted_day');

                }, 100);
            } // end day
            if(picker.data('duration_unit') == 'hour') {
                setTimeout(function() {
                    // see if there are blank spots
                    var slotpicker = currentForm.find('.slot-picker');
                    var slot_column = slotpicker.find('.slot_column');
                    $(slot_column).each(function() {
                        if($(this).children().hasClass('slot_empty')){
                            $(this).addClass('hide-slot_column');
                        }

                    });
                },1000);
            }
          
        } );
        form.on( 'time-selected', function(event) {
            var currentForm = $(this);
            var picker = currentForm.find('.picker');

            if(picker.data('duration_unit') == 'hour') {

                var appointment_duration_default = picker.data( 'appointment_duration' );
                if(appointment_duration_default == '' || appointment_duration_default < 0) {
                    appointment_duration_default = 1;
                }

                setTimeout(function() {
                    var time_value = currentForm.find('input[name="wc_appointments_field_start_date_time"]').val().toString();
                    var time_value_array = time_value.split(":");
                    var hour = time_value_array[0];
                    var minute = time_value_array[1];

                    // get the addon time
                    var addon_duration = currentForm.find('input[name="wc_appointments_field_addons_duration"]').val().toString();

                    if(picker.data('duration_unit') == 'hour') {
                        // round down - no decimals
                        var addon_duration_hour = Math.floor(addon_duration / 60);

                        // find the minutes 
                        var addon_duration_hours_to_minutes = addon_duration_hour * 60;
                        var addon_duration_minutes = addon_duration - addon_duration_hours_to_minutes;

                    }

                    // use the date that was defined earlier
                    var start_date_with_hours = new Date(selectedDate);
                    start_date_with_hours.setHours(hour, minute);

                    var start_date_with_hours_locale = start_date_with_hours.toLocaleString('en-US', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    // set the appointment length to get the end time
                    var end_time_hour = (parseInt(hour) + parseInt(addon_duration_hour) + appointment_duration_default).toString();
                    var end_time_minute = addon_duration_minutes;
                    var end_date_with_hours = start_date_with_hours;
                    end_date_with_hours.setHours(end_time_hour, end_time_minute);
                    var end_date_with_hours_locale = end_date_with_hours.toLocaleString('en-US', {
                        hour: 'numeric',
                        minute: 'numeric',
                        hour12: true
                    });

                    // $(currentForm).find('.starttime-value').html((start_date_with_hours.getMonth() + 1) + '/' + start_date_with_hours.getDate().toString() + '/' + start_date_with_hours.getFullYear().toString() + '-' + start_date_with_hours_locale);

                    // $(currentForm).find('.endtime-value').html((end_date_with_hours.getMonth() + 1) + '/' + end_date_with_hours.getDate().toString() + '/' + end_date_with_hours.getFullYear().toString()  + '-' +  end_date_with_hours_locale);

                    $(currentForm).find('.date-value').html((start_date_with_hours.getMonth() + 1) + '/' + start_date_with_hours.getDate().toString() + '/' + start_date_with_hours.getFullYear().toString());

                    $(currentForm).find('.starttime-value').html(start_date_with_hours_locale);

                    $(currentForm).find('.endtime-value').html(end_date_with_hours_locale);

                }, 700);

            // var picker = currentForm.find('.picker');
            // if(picker.data('duration_unit') == 'hour') {
            //     $(currentForm).find('.starttime-value').html('pizza');

            } // end hour
        });
        // form.on( 'click', '.appointable', function() {
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
    // This is not appointments but is related to the Owners Calendar page - it 
    // handles the buttons on the event calendar header.
    function setupEventCalendarButtons() {
        if($('.events-calendar-switch').length) {
            $('.events-calendar-switch').on('click', function(e){
                e.preventDefault();

                // hide all the calendars
                $('.events-calendar-owners-portal').removeClass('show-calendar');
                // unselect all buttons
                $('.events-calendar-switch').removeClass('selected');
                // get the href
                var id = $(this).attr('href');
                $(id).addClass('show-calendar');
                $(this).addClass('selected');
            });
        }
    }

    /**
     * Do things when options are selected for fly fishing
     */
    function setupFlyFishingOptions() {
        var fly_fishing_owner_licensed_guide = $('.wc-pao-addon-212111-type-6-1');
        if(fly_fishing_owner_licensed_guide.length) {
            $('body').on('click', '.wc-pao-addon-212111-type-6-1 input[name="addon-212111-type-6[]"]', function() {
                if(!$('.wc-pao-addon-licensed-guide-company-name, .wc-pao-addon-licensed-guide-contact-number').hasClass('required_added')) {
                    $('.wc-pao-addon-licensed-guide-company-name label, .wc-pao-addon-licensed-guide-contact-number label').append('<em class="required" title="Required field">*</em>');
                }

                $('.wc-pao-addon-licensed-guide-company-name, .wc-pao-addon-licensed-guide-contact-number').addClass('show_fields required_added');
                $('.wc-pao-addon-licensed-guide-company-name input, .wc-pao-addon-licensed-guide-contact-number input').attr('required', true);
                    
            });
            $('body').on('click', '.wc-pao-addon-212111-type-6-0 input[name="addon-212111-type-6[]"],.wc-pao-addon-212111-type-6-2 input[name="addon-212111-type-6[]"]', function() {

                $('.wc-pao-addon-licensed-guide-company-name, .wc-pao-addon-licensed-guide-contact-number').removeClass('show_fields');
                $('.wc-pao-addon-licensed-guide-company-name input, .wc-pao-addon-licensed-guide-contact-number input').attr('required', false);

            });
        }
    }
    /**
     * Do things when options are selected for horseback riding
    */
    function setupHorsebackRidingOptions() {

        var horeseback_riding_form = $('.wc-appointment-product-id[value="212112"]').parent();
        if(!horeseback_riding_form.length) {
            return;
        } 

        // add the error box
        $(horeseback_riding_form).append('<div id="horseback-riding-error"></div>');

        var horseback_riding_number_in_party = $('#addon-212112-number-in-party-3');
        
        if(horseback_riding_number_in_party.length) {
            $('.wc-appointments-appointment-hook.wc-appointments-appointment-hook-after').on('change', $(horseback_riding_number_in_party), function(event) {
                
                var quantity_selector = $(horseback_riding_number_in_party).val();
                var quantity_selector_array = quantity_selector.split('-');
                var quantity_selected = quantity_selector_array[1];

                var current_product_quantity = ($(horseback_riding_number_in_party).parentsUntil('.wc-appointments-appointment-form-wrap.cart').parent().find('.input-text.qty'));
                $(current_product_quantity).val(quantity_selected).trigger("change");

            });

        }
        // set up a mutation observer to handle issues with the number of riders
        var timeout_var;

        var observer = new MutationObserver(function( mutations ) {
            mutations.forEach(function( mutation ) {	
                if(mutation.type == 'attributes') {
                    if($(targetNode).prop('disabled')) {
                        clearTimeout(timeout_var);
                        timeout_var = setTimeout(function() {
                            $('#horseback-riding-error').addClass('error');
                            $('#horseback-riding-error').html('<p>Slots are filling up!</p><p>Please try adding fewer riders or choose a different date.</p>');
                        }, 4000);

                    } else {
                        clearTimeout(timeout_var);
                        timeout_var = setTimeout(function() {
                            $('#horseback-riding-error').removeClass('error');
                            $('#horseback-riding-error').html('');

                        }, 1000);
                    }
                }
            });    
        });
        // Configuration of the observer:
        var config = { 
            childList: true,
            attributes: true,
            subtree: true,
            characterData: true
        }; 
        var targetNode = $('.wc-appointment-product-id[value="212112"]').parent().find('.wc-appointments-appointment-form-button.single_add_to_cart_button')[0];
        observer.observe(targetNode, config);  
    }

});
  