<?php
namespace AlpineMountainRanch;

/**
 * Pairs with the Magnific Lightbox which is already part of the Divi theme
 */
class AMR_Divi {

    public function __construct() {
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
} // end class AMR_Lightbox

$amr_lightbox = new AMR_Lightbox();
