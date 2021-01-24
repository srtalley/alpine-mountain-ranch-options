<?php
namespace AlpineMountainRanch;

class AMR_EventsManager {

    public function __construct() {
        add_filter( 'mc4wp_integration_events-manager_subscriber_data', array($this, 'send_em_fields_to_mailchimp'), 10, 2);

		add_filter('em_bookings_is_open', array($this, 'modify_booking_open_status'), 100, 3);
        // add_action('plugins_loaded', array($this,'queue_emails') );
        // add_filter('em_email_users_hook', array($this, 'amr_add_email_header_footer'), 999999999990, 2);
        // add_filter('em_email_users_hook', array($this, 'amr_stonehenge_mailer_before_send'), 999999999990, 2);

        // add_filter('stonehenge_mailer_before_send', array($this, 'amr_stonehenge_mailer_before_send'), 10, 2);
    }


    /**
     * Send additional fields to Mailchimp on signup
     * https://github.com/ibericode/mc4wp-snippets/blob/master/integrations/integration-slugs.md
     */

    public function send_em_fields_to_mailchimp(\MC4WP_MailChimp_Subscriber $subscriber) {

        // Get the event
        $event_id = $_POST['event_id'];
        $em_event =  new \EM_Event( absint($event_id) );

        // Get the categories
        $em_categories = $em_event->get_categories();

        $subscriber->tags[] = 'Booked Event';

        foreach($em_categories->terms as $em_category) {
            $subscriber->tags[] = $em_category->name;

            $term_object = get_term($em_category->id, 'event-categories');
            $parent_term = get_term($term_object->parent, 'event-categories');

            if($parent_term->slug == 'winter') {
                $subscriber->merge_fields[ "EVENTTYPE" ] = sanitize_text_field( 'Winter' );
            }
            if($parent_term->slug == 'summer') {
                $subscriber->merge_fields[ "EVENTTYPE" ] = sanitize_text_field( 'Summer' );
            }
        }

        $subscriber->merge_fields[ "ADDRESS1" ] = sanitize_text_field( $_POST['dbem_address'] );
        $subscriber->merge_fields[ "CITY" ] = sanitize_text_field( $_POST['dbem_city'] );

        $subscriber->merge_fields[ "STATE" ] = sanitize_text_field( $_POST['dbem_state'] );

        $subscriber->merge_fields[ "ZIP" ] = sanitize_text_field( $_POST['dbem_zip'] );

        $subscriber->merge_fields[ "PHONE" ] = sanitize_text_field( $_POST['dbem_phone'] );


        return $subscriber;

    }
    /**
     * Prevent new bookings from 6PM onward on the day prior to the event
     */
    public function modify_booking_open_status($is_open, $item, $include_member_tickets) {

        // see if this is an active event 
        if($is_open) {

            // check if the event manager function exists.
            if(function_exists('em_get_event')) {
            
                // get the event
                $event = em_get_event($item->event_id);

                // [start_time] => 10:15:00
                // [start_date] => 2020-12-23

                // get the timezone of the WP installation
                $zone = new \DateTimeZone(wp_timezone_string());

                // create an datetime object from the event
                $event_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $event->start_date . ' ' . $event->start_time, $zone);

                // copy our time to a variable so we can modify it
                $cutoff_datetime = $event_datetime;

                // subtract one day
                $cutoff_datetime->sub(new \DateInterval('P1D'));

                // set the time to 6 pm
                $cutoff_datetime->settime(18, 00 , 0);

                // see if the event should be cut off
                if($cutoff_datetime > current_datetime()) {
                    return $is_open;
                } else {
                    return false;
                }
            } else {
                return $is_open;
            }
        } else {
            return $is_open;
        } // end if $is_open
    }  
    


    /**
	 * Run on cron and prep emails to go out
	 */
	// public function queue_emails(){
	//     global $wpdb;
	//     //For each event x days on
    //     $days = 90; //get_option('dbem_emp_emails_reminder_days',1);
        

    //     $scope = ($days > 0) ? date('Y-m-d', current_time('timestamp') - (86400*$days)):date('Y-m-d', current_time('timestamp')+86400);
	    //make sure we don't get past events, only events starting that specific date
	    // add_filter('pre_option_dbem_events_current_are_past', '__return_true');
		// $output_type = get_option('dbem_smtp_html') ? 'html':'email';
	    // foreach( \EM_Events::get(array('scope'=>$scope,'private'=>1,'blog'=>get_current_blog_id())) as $EM_Event ){
	    //     /* @var $EM_Event EM_Event */
        //     $emails = array();
            // $this->wl($EM_Event);
	    	//get ppl attending
            // foreach( $EM_Event->get_bookings()->get_bookings()->bookings as $EM_Booking ){ //get confirmed bookings
                
	    	//     /* @var $EM_Booking EM_Booking */
	    	    // if( is_email($EM_Booking->get_person()->user_email) ){
                //     $this->wl($EM_Booking->get_person()->user_email);
	    	//     	do_action('em_booking_email_before_send', $EM_Booking);
	    	//     	if( \EM_ML::$is_ml ){
		    // 	    	if( $EM_Booking->language && \EM_ML::$current_language != $EM_Booking->language ){
		    // 	    		$lang = $EM_Booking->language;
		    // 	    		$subject_format = \EM_ML_Options::get_option('dbem_emp_emails_reminder_subject', $lang);
		    // 	    		$message_format = \EM_ML_Options::get_option('dbem_emp_emails_reminder_body', $lang);
		    // 	    	}
	    	//     	}
	    	//     	if( empty($subject_format) ){
		    // 	    	$subject_format = get_option('dbem_emp_emails_reminder_subject');
		    // 	    	$message_format = get_option('dbem_emp_emails_reminder_body');
	    	//     	}
	    	//     	$subject = $EM_Booking->output($subject_format,'raw');
	    	//     	$message = $EM_Booking->output($message_format,$output_type);
		    // 	    $emails[] = array($EM_Booking->get_person()->user_email, $subject, $message, $EM_Booking->booking_id);
		    // 	    do_action('em_booking_email_after_send', $EM_Booking);
	    	//     }
	    	// }
	    	// if(count($emails) > 0){
	    	//     $attachments = serialize(array());

	    	//     foreach($emails as $email){
			//     	$wpdb->insert(EM_EMAIL_QUEUE_TABLE, array('email'=>$email[0],'subject'=>$email[1],'body'=>$email[2],'attachment'=>$attachments,'event_id'=>$EM_Event->event_id,'booking_id'=>$email[3]));
	    	//     }
	    	// }
	//     }
	//     //cleanup
	// 	remove_filter('pre_option_dbem_events_current_are_past', '__return_true');
    // }
    
    public function amr_add_email_header_footer($mail, $EM_Object) {

    
        if( $mail->ContentType === 'text/plain' || !is_object($EM_Object) ) {
			return $mail;
        }
        // Fallback for EM Test Mail.
		if( !in_array( get_class($EM_Object), array('EM_Booking', 'EM_Multiple_Booking') ) ) {
			return $mail;
        }
        // $this->wl($mail);
        // $this->wl('call begin');
        // $this->wl($mail->Body);
        // $this->wl('call end');

        $mail_header = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><html><body><center><table id="bodyTable" align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="width:100% !important; margin:0 !important; padding:0 !important; border-collapse:collapse; background-color:#eaeaea;"><tr><td align="center" valign="top" id="bodyCell" style="height:100!important; margin:0 !important; padding: 0 !important; width:100%!important"><table id="templateBody" border="0" cellpadding="0" cellspacing="0" width="700" style="width:700px !important; max-width: 100%; margin:0 !important; padding:0 !important; border-collapse:collapse;"><tr><td align="center" valign="top"><table class="templateContainer" border="0" cellpadding="0" cellspacing="0" width="700" style="border-collapse:collapse;"><tr><td valign="top" id="templateTitle" class="headerContent"> <img style="max-width:700px; width:100%; background-color: #fff; display: block;" src="https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/01/amr-events-email-header-winter.jpg" alt="Alpine Mountain Ranch" width="700" height="350" /></td></tr></table></td></tr><tr><td align="center" valign="top"><table border="0" class="templateContainer" cellpadding="20" cellspacing="0" width="700" style="border-collapse:collapse; background-color: #fff; font-family: \'Open Sans\', Arial, Helvetica, sans-serif; font-size:16px; line-height: 24px;"><tr><td valign="top" class="fullwidthOneColumnText nestedContainerCell bodyContent">';

        $mail_footer = '</td></tr></table></td></tr><tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%" style="width:100%!important; border-collapse: collapse;"><tr><td align="center" valign="top" style="width:100% !important"><table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter" style="table-layout:fixed;width:100%!important; border-collapse:collapse; font-family: \'Open Sans\', Arial, Helvetica, sans-serif; background-color:#363636;"><tr mc:repeatable="footer"><td valign="top" class="footerStripe" style="padding-top:15px; padding-left:15px; padding-bottom:10px; padding-right:15px;"></td></tr><tr mc:repeatable="footer"><td><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td><h3 style="text-align: center; color: #fff; font-size: 24px;">Contact Us</h3><p style="text-align: center; color: #fff; font-size:16px; line-height: 24px;"><strong>Suzanne Schlicht</strong><br />Senior Vice President and Director of Sales<br /> <strong><a style="color: #fff; text-decoration: none;" class="gtrackexternal" href="tel:970.846.0817">970.846.0817</a></strong><br /> <a style="color: #fff; text-decoration: none;" class="gtrackexternal" href="mailto:sschlicht@alpinemountainranch.com">sschlicht@alpinemountainranch.com</a></p><hr style=" color: #fff; max-width: 300px; margin: 0 auto;" /></td></tr><tr><td></td></tr></table></td></tr><tr><td valign="top" class="footerContent trueFooterBottom" style="padding-top:15px; padding-left:15px; padding-bottom:30px; padding-right:15px;"><table><tbody><tr><td><td class="footerLeftCol" width="10%"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td valign="top" class="footerContent footerLeft" style="padding-top:0;" mc:edit="footer_content00"></td></tr></table></td><td class="footerMiddleCol" width="80%"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td valign="top" class="footerContent" style="padding-top:0;" mc:edit="footer_content01"><p style="text-align: center; color: #fff; font-size: 14px; line-height: 16px;">Copyright &copy; 2021 Alpine Mountain Ranch & Club, All rights reserved.<br /> @alpinemountainranch | <a href="https://AlpineMountainRanchSteamboat.com" style="color:#fff; text-decoration: none;">https://AlpineMountainRanchSteamboat.com</a></p></td></tr></table></td><td class="footerRightCol" width="10%"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td valign="top" class="footerContent footerLogo" style="padding-top:0;"></td></tr></table></td></td></tr></tbody></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></center></body></html>';

            $wpautop_body = html_entity_decode( wp_kses_allowed( wpautop( $mail->Body ) ) );
            $mail->Body = $mail_header . $wpautop_body . $mail_footer;
            
            // $this->wl($mail);
        return $mail;
    }
    public function amr_stonehenge_mailer_before_send($mail, $EM_Object) {
        // error_log('called this tim AGAIN e');
        // $this->wl($mail);
        // $this->wl($object);
        // return $mail;
        if( $mail->ContentType === 'text/plain' || !is_object($EM_Object) ) {
			return $mail;
        }
        // Fallback for EM Test Mail.
		if( !in_array( get_class($EM_Object), array('EM_Booking', 'EM_Multiple_Booking') ) ) {
			return $mail;
        }
        if($mail == '' || $mail == null) {
            // $this->wl('tis null');
            return $mail;
        } else {
            // $this->wl($mail);
            // $this->wl('---------------------------');
        $mail_header = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><html><body><center><table id="bodyTable" align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="width:100% !important; margin:0 !important; padding:0 !important; border-collapse:collapse; background-color:#eaeaea;"><tr><td align="center" valign="top" id="bodyCell" style="height:100!important; margin:0 !important; padding: 0 !important; width:100%!important"><table id="templateBody" border="0" cellpadding="0" cellspacing="0" width="700" style="width:700px !important; max-width: 100%; margin:0 !important; padding:0 !important; border-collapse:collapse;"><tr><td align="center" valign="top"><table class="templateContainer" border="0" cellpadding="0" cellspacing="0" width="700" style="border-collapse:collapse;"><tr><td valign="top" id="templateTitle" class="headerContent"> <img style="max-width:700px; width:100%; background-color: #fff; display: block;" src="https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/01/amr-events-email-header-winter.jpg" alt="Alpine Mountain Ranch" width="700" height="350" /></td></tr></table></td></tr><tr><td align="center" valign="top"><table border="0" class="templateContainer" cellpadding="20" cellspacing="0" width="700" style="border-collapse:collapse; background-color: #fff; font-family: \'Open Sans\', Arial, Helvetica, sans-serif; font-size:16px; line-height: 24px;"><tr><td valign="top" class="fullwidthOneColumnText nestedContainerCell bodyContent" style="padding: 20px 40px !important;">';

        $mail_footer = '</td></tr></table></td></tr><tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%" style="width:100%!important; border-collapse: collapse;"><tr><td align="center" valign="top" style="width:100% !important"><table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter" style="table-layout:fixed;width:100%!important; border-collapse:collapse; font-family: \'Open Sans\', Arial, Helvetica, sans-serif; background-color:#363636;"><tr mc:repeatable="footer"><td valign="top" class="footerStripe" style="padding-top:15px; padding-left:15px; padding-bottom:10px; padding-right:15px;"></td></tr><tr mc:repeatable="footer"><td><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td><h3 style="text-align: center; color: #fff; font-size: 24px;">Contact Us</h3><p style="text-align: center; color: #fff; font-size:16px; line-height: 24px;"><strong>Suzanne Schlicht</strong><br />Senior Vice President and Director of Sales<br /> <strong><a style="color: #fff; text-decoration: none;" class="gtrackexternal" href="tel:970.846.0817">970.846.0817</a></strong><br /> <a style="color: #fff; text-decoration: none;" class="gtrackexternal" href="mailto:sschlicht@alpinemountainranch.com">sschlicht@alpinemountainranch.com</a></p><hr style=" color: #fff; max-width: 300px; margin: 0 auto;" /></td></tr><tr><td></td></tr></table></td></tr><tr><td valign="top" class="footerContent trueFooterBottom" style="padding-top:15px; padding-left:15px; padding-bottom:30px; padding-right:15px;"><table><tbody><tr><td><td class="footerLeftCol" width="10%"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td valign="top" class="footerContent footerLeft" style="padding-top:0;" mc:edit="footer_content00"></td></tr></table></td><td class="footerMiddleCol" width="80%"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td valign="top" class="footerContent" style="padding-top:0;" mc:edit="footer_content01"><p style="text-align: center; color: #fff; font-size: 14px; line-height: 16px;">Copyright &copy; 2021 Alpine Mountain Ranch & Club, All rights reserved.<br /> @alpinemountainranch | <a href="https://AlpineMountainRanchSteamboat.com" style="color:#fff; text-decoration: none;">https://AlpineMountainRanchSteamboat.com</a></p></td></tr></table></td><td class="footerRightCol" width="10%"><table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><td valign="top" class="footerContent footerLogo" style="padding-top:0;"></td></tr></table></td></td></tr></tbody></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></center></body></html>';
            $mail->Body = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><br>
            <html><body>', '', $mail->Body);


            $mail->Body = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><br>', '', $mail->Body);
            $mail->Body = str_replace('<html><body>', '', $mail->Body);
            
            $mail->Body = str_replace( '</body></html><p style="clear:both;">&nbsp;</p>', '', $mail->Body);
            $mail->Body = $mail_header . $mail->Body . $mail_footer;

            // $this->wl($mail);
            return $mail;
        }
    }

    public function wl ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    } // end public function wl 
} // end class CustomPostTypes

$amr_eventsmanager = new AMR_EventsManager();