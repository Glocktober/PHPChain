<?php
#
# Moved from using cookies to sessions r.k. 09/15/2021
#

function is_authed(){
    if (isset($_SESSION['isauth'])){
        return $_SESSION['isauth'];
    }
    return FALSE;
}

function set_csrf(){
    $_SESSION['csrf_token'] = md5(uniqid(mt_rand(), true));
}

function get_csrf(){
    if (!isset($_SESSION['csrf_token'])){
        set_csrf();
    }
    return $_SESSION['csrf_token'];
}

function check_csrf(){
    global $csrf_force_logout;
    $csrf = gorp('csrftok');
    if (!isset($csrf) OR ($csrf != $_SESSION['csrf_token'])){
        error_log('csrf error');
        if ($csrf_force_logout){
            set_error("CSRF Error - Forced logout.");
            Header("Location: logout.php");
            die();
        } else {
            set_error("CSRF Error - operation not performed");
            Header("Location: ".$_SERVER['PHP_SELF']);
            die();
        }
    }
    set_csrf();
}

function status_log($msg){
    global $stat_log;
    if ($stat_log) error_log($msg);
}

function set_status($msg){
    status_log($msg);
    $_SESSION['status_message'] = $msg;
}

function set_error($msg){
    status_log($msg);
    $_SESSION['error_message'] = $msg;
}

function status_message(){

    if (array_key_exists('error_message', $_SESSION)){

        $msg = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        if (array_key_exists('status_message',$_SESSION)) 
            unset($_SESSION['status_message']);
        return "<span class=\"errorbar\">$msg</span>";

    } elseif (array_key_exists('status_message', $_SESSION)){

        $msg = $_SESSION['status_message'];
        unset($_SESSION['status_message']);
        return "<span class=success>$msg</span";
        
    } else {

        $login = "";
        if (array_key_exists('login',$_SESSION)) $login = $_SESSION['login'];

        if (is_authed()) 
            return "<span class=info> Current User: \"<b>$login</b>\"</span>";
        else  return "<span class=error><b>Please log in</b></span>";
    }
}

?>