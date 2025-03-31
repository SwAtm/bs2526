<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ntw
{
    public function __construct()
    {
        require_once APPPATH.'third_party/ntw/src/NTWIndia.php';
        require_once APPPATH.'third_party/ntw/src/Exception/NTWIndiaInvalidNumber.php';
        require_once APPPATH.'third_party/ntw/src/Exception/NTWIndiaNumberOverflow.php';
    }
}
