<?php
namespace AlpineMountainRanch;

class AMR_PinpointBooking {

    public function __construct() {

        add_filter('dopbsp_filter_translation_text', array($this, 'change_text'), 1000, 1);
    }

    public function change_text($text_array) {
        // wl($text_array);
        foreach($text_array as $key => $text) {
            // wl($key);
            if($text['key'] == 'EXTRAS_TITLE' || $text['key'] == 'EXTRAS_FRONT_END_TITLE' || $text['key'] == 'PARENT_EXTRAS') {
                $text_array[$key]['text'] = 'Make Selection';
            }
        }
        // wl($text_array);
        return $text_array;
    }
    /**
     * Send additional fields to Mailchimp on signup
     * https://github.com/ibericode/mc4wp-snippets/blob/master/integrations/integration-slugs.md
     */

} // end class AMR_PinpointBooking

$amr_pinpoint_booking = new AMR_PinpointBooking();
