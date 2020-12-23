<?php
namespace AlpineMountainRanch;

class AMR_EventsManager {

    public function __construct() {

		add_filter('em_bookings_is_open', array($this, 'modify_booking_open_status'), 100, 3);
        // add_action('plugins_loaded', array($this,'queue_emails') );

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
	public function queue_emails(){
        $this->wl('bonkers');
	    global $wpdb;
	    //For each event x days on
        $days = 90; //get_option('dbem_emp_emails_reminder_days',1);
        

        $scope = ($days > 0) ? date('Y-m-d', current_time('timestamp') - (86400*$days)):date('Y-m-d', current_time('timestamp')+86400);
        $this->wl($scope);
	    //make sure we don't get past events, only events starting that specific date
	    // add_filter('pre_option_dbem_events_current_are_past', '__return_true');
		$output_type = get_option('dbem_smtp_html') ? 'html':'email';
	    foreach( \EM_Events::get(array('scope'=>$scope,'private'=>1,'blog'=>get_current_blog_id())) as $EM_Event ){
	        /* @var $EM_Event EM_Event */
            $emails = array();
            // $this->wl($EM_Event);
	    	//get ppl attending
            foreach( $EM_Event->get_bookings()->get_bookings()->bookings as $EM_Booking ){ //get confirmed bookings
                
                $this->wl($EM_Booking);
	    	//     /* @var $EM_Booking EM_Booking */
	    	    if( is_email($EM_Booking->get_person()->user_email) ){
                    $this->wl($EM_Booking->get_person()->user_email);
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
	    	    }
	    	}
	    	// if(count($emails) > 0){
	    	//     $attachments = serialize(array());

	    	//     foreach($emails as $email){
			//     	$wpdb->insert(EM_EMAIL_QUEUE_TABLE, array('email'=>$email[0],'subject'=>$email[1],'body'=>$email[2],'attachment'=>$attachments,'event_id'=>$EM_Event->event_id,'booking_id'=>$email[3]));
	    	//     }
	    	// }
	    }
	    //cleanup
		remove_filter('pre_option_dbem_events_current_are_past', '__return_true');
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
