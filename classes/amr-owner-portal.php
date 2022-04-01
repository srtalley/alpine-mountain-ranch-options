<?php
namespace AlpineMountainRanch;

class AMR_OwnerPortal {

    public function __construct() {
        add_filter('the_password_form', array($this, 'change_password_protection_message'), 9999, 1 );
        add_filter( 'post_password_required', array($this, 'hoa_docs_bypass_link'), 100, 1);
    }

    /**
     * Adds a way to bypass the password protection with a special link
     */
    public function hoa_docs_bypass_link ($value) {
        // get the current page slug
        $current_page = get_page_by_path('owner-portal/owner-resources');
        if(is_object($current_page)) {
            $owner_portal_docs_id = $current_page->ID;
            if(get_the_ID() == $owner_portal_docs_id) {
                // get the current get variable
                if(isset($_GET['owner_resources_access']) && $_GET['owner_resources_access'] == 'guest2022') {
                    return false;
                }
            }
        }
       
        return $value;
    }

    /** 
     * Change the password message displayed
     */
    public function change_password_protection_message ($output) {
        $owner_portal_id = get_page_by_path('owner-portal')->ID;

        // see if the ID matches. If not check for parent pages that match.
        if(get_the_ID() != 	$owner_portal_id) {
            // get post parent IDs
            $parent_ids = get_post_ancestors( get_the_ID() );
            // see if it's a parent id
            if ( ! $parent_ids || empty( $parent_ids ) ) {
                return $output;
            }
            if(!in_array($owner_portal_id, $parent_ids)) {
                return $output;
            }
        }
        $default_text = 'To view this protected post, enter the password below:';
        $new_text = 'Please enter the password for the Owner Portal below.';
        $output = str_replace($default_text, $new_text, $output);
        return $output;
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
} // end class AMR_OwnerPortal

$amr_owner_portal = new AMR_OwnerPortal();
