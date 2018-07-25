<?php

class Auth {

    public function check_user($binds, $reload = false) {
        
        // We hardcode the user here since we'll have only once
        // g0shThisFr3nchGuyR0cks!
        if($binds['email']=='jordanbangbros@gmail.com' && md5($binds['pwd']) == 'd41d8cd98f00b204e9800998ecf8427e' ) {
            $session = md5(date("YmdHis") . $result['email'] . rand(99999,999999));
            Cookie::set('au_666_FR', $session, false);
            $_SESSION[$session] = array('admin'=>1);
            return true;
        } else {
            return false;
        }

    } 

    public static function is_user_logged_in() {
        if (!isset($_SESSION[Cookie::get('au_666_FR')]) || !$_SESSION[Cookie::get('au_666_FR')]) {
           return false;
        }
        return true;
    }
    
   
  

}
