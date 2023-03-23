<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Qrcodeg {  

    function __construct ( $options = array() )
    {
        require_once( APPPATH . 'third_party/phpqrcode/qrlib.php' );
    }

    public function __get( $var ) { return get_instance()->$var; }
}
?>
