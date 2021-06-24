<?php
namespace AlpineMountainRanch;

class AMR_EventsManager {

    public function __construct() {
        add_filter( 'mc4wp_integration_events-manager_subscriber_data', array($this, 'send_em_fields_to_mailchimp'), 10, 2);

        add_filter('em_bookings_is_open', array($this, 'modify_booking_open_status'), 100, 3);
        
        add_filter('em_email_users_hook', array($this, 'amr_add_email_header_footer'), 999999999990, 2);

        add_filter('stonehenge_mailer_before_send', array($this, 'amr_add_email_header_footer'), 10, 2);
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

            // Add the category name as a tag
            $subscriber->tags[] = $em_category->name;

            // Add the category name to a merge field
            $subscriber->merge_fields[ "BKEVNTCAT" ] = sanitize_text_field( $em_category->name );

            $term_object = get_term($em_category->id, 'event-categories');
            $parent_term = get_term($term_object->parent, 'event-categories');

            if($parent_term->slug == 'winter') {
                $subscriber->merge_fields[ "BKEVNTTYPE" ] = sanitize_text_field( 'Winter' );
            }
            if($parent_term->slug == 'summer') {
                $subscriber->merge_fields[ "BKEVNTTYPE" ] = sanitize_text_field( 'Summer' );
            }
        }

        $subscriber->merge_fields[ "STREETADDR" ] = sanitize_text_field( $_POST['dbem_address'] );
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
     * Get the email header
     */
    public function get_amr_header($type = 'regular') {

        if($type == 'followup') {
            // $header_img = 'https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/01/amr_email_header_followup_winter.jpg';
            $header_img = 'https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/06/amr_email_header_followup_summer.jpg';
        } else {
            // $header_img = 'https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/01/amr_email_header_main_winter.jpg';
            $header_img = 'https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/06/amr_email_header_main_summer.jpg';
        }
        $mail_header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1"><style type="text/css">body{margin:0 !important}.spacer56{height:26px}.bodyContent{font-size:18px !important;padding:20px 40px !important}@media only screen and (max-width: 480px){body,table,td,p,a,li,blockquote{-webkit-text-size-adjust:none !important;text-size-adjust:none !important}body{width:100% !important;min-width:100% !important}.bodyContent{font-size:18px !important;padding:10px 20px !important}.templateContainer,.footerContainer{max-width:700px !important;width:100% !important}.flexibleContainer{width:100%}.flexibleContainer td{text-align:center}.spacer56{height:0}}</style></head><body><center><table id="bodyTable" align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="width:100% !important; margin:0 !important; padding:0 !important; border-collapse:collapse; background-color:#eaeaea;"><tr><td align="center" valign="top" id="bodyCell" style="height:100!important; margin:0 !important; padding: 0 !important; width:100%!important"><table id="templateBody" border="0" cellpadding="0" cellspacing="0" width="100%" style="width:100% !important; max-width: 100%; margin:0 !important; padding:0 !important; border-collapse:collapse;"><tr><td align="center" valign="top"><table class="templateContainer" border="0" cellpadding="0" cellspacing="0" width="700" style="border-collapse:collapse;"><tr><td valign="top" id="templateTitle" class="headerContent"> <img style="max-width:700px; width:100%; background-color: #fff; display: block;" src="' . $header_img . '" alt="Alpine Mountain Ranch" width="700" /></td></tr></table></td></tr><tr><td align="center" valign="top"><table border="0" class="templateContainer" cellpadding="20" cellspacing="0" width="700" style="border-collapse:collapse; background-color: #fff; font-family: \'Open Sans\', Arial, Helvetica, sans-serif; font-size:16px; line-height: 24px;"><tr><td valign="top" class="fullwidthOneColumnText nestedContainerCell bodyContent" style="padding: 20px 40px;">';

        return $mail_header;
    }

    /**
     * Get the email footer
     */
    public function get_amr_footer() {

        $mail_footer = '</td></tr></table></td></tr><tr><td align="center"><table border="0" cellpadding="0" cellspacing="0" width="700" style="width:700px; border-collapse: collapse;" class="footerContainer"><tr><td align="center" valign="top" style="width:100% !important"><table border="0" cellpadding="0" cellspacing="0" width="100%" id="templateFooter" style=\'table-layout:fixed;width:100%!important; border-collapse:collapse; font-family: "Open Sans", Arial, Helvetica, sans-serif; background-color:#ffffff;\'><tr mc:repeatable="footer"><td valign="top" class="footerStripe" style="padding: 0;"> <img style="max-width: 100%; width: 100%;" src="https://alpinemountainranchsteamboat.com/wp-content/plugins/alpinemountainranch/images/amr_email_box_shadow_line.jpg" width="700" height="20"></td></tr><tr mc:repeatable="footer"><td><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tr><table align="Left" border="0" cellpadding="0" cellspacing="0" width="350" class="flexibleContainer" style="border-collapse:collapse;"><tr><td> <a href="https://alpinemountainranchsteamboat.com" target="_blank"><img style="max-width: 100%;" src="https://alpinemountainranchsteamboat.com/wp-content/uploads/2021/01/amr_email_footer.jpg" width="320" height="170"></a></td></tr></table><table align="Right" border="0" cellpadding="0" cellspacing="0" width="350" class="flexibleContainer" style="border-collapse:collapse;"><tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;"><tbody><tr ><td valign="top" class="spacer56" height="26"></td></tr><tr><td><p style="text-align: center; color: #695f5f; line-height: 20px;"><strong><span style="color: #b56231; font-size: 22px; line-height: 30px;">SUZANNE SCHLICHT</span></strong><br />Senior Vice President and Director of Sales<br /> <a style="color: #695f5f; text-decoration: none;" class="gtrackexternal" href="tel:970.846.0817">c 970.846.0817</a><br /> <strong><a style="color: #695f5f; text-decoration: none;" class="gtrackexternal" href="mailto:sschlicht@alpinemountainranch.com">sschlicht@alpinemountainranch.com</a></strong><br /> <span style="font-size:14px; font-style: italic;">Licensed broker with Ski Realty</span></p></td></tr></tbody></table></td></tr></table></td></tr><tr><td></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></center></body></html>';
        return $mail_footer;
    }
    
    /**
     * Add the headers and footers to the emails
     */
    public function amr_add_email_header_footer($mail, $EM_Object) {

        if( $mail->ContentType === 'text/plain' || !is_object($EM_Object) ) {
			return $mail;
        }
        // Fallback for EM Test Mail.
		if( !in_array( get_class($EM_Object), array('EM_Booking', 'EM_Multiple_Booking') ) ) {
			return $mail;
        }
        if($mail == '' || $mail == null) {
            return $mail;
        } else {

            if(substr($mail->Subject, 0, strlen('Thank you for attending')) === 'Thank you for attending') {
                $mail_header = $this->get_amr_header('followup');
            } else {
                $mail_header = $this->get_amr_header();
            }
            $mail_footer= $this->get_amr_footer();

            $mail->Body = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><br>', '', $mail->Body);
            $mail->Body = str_replace('<html><body>', '', $mail->Body);
            $mail->Body = str_replace( '</body></html><p style="clear:both;">&nbsp;</p>', '', $mail->Body);

            $wpautop_body = html_entity_decode( wp_kses_allowed( wpautop( $mail->Body ) ) );
            $mail->Body = $mail_header . $wpautop_body . $mail_footer;
            
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
