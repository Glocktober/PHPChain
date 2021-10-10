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

function check_csrf($dest='index.php'){
    global $csrf_force_logout;
    $csrf = get_post('csrftok');
    if (!isset($csrf) OR ($csrf != $_SESSION['csrf_token'])){
        error_log('csrf error');
        if ($csrf_force_logout){
            set_error("CSRF Error - Forced logout.");
            Header("Location: logout.php");
            die();
        } else {
            set_error("CSRF Error - operation not performed");
            return;
            Header("Location: $dest");
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
        return "<span class=errorbar ><i class='material-icons iconoffs' style='color:red'>error</i>&nbsp;$msg</span>";

    } elseif (array_key_exists('status_message', $_SESSION)){

        $msg = $_SESSION['status_message'];
        unset($_SESSION['status_message']);
        return "<span class=success><i class='material-icons iconoffs' style='color:green'>check_circle</i>&nbsp;$msg</span>";
        
    } else {
        return "<span class=error><b>&nbsp;</b></span>";
        $login = "";
        if (array_key_exists('login',$_SESSION)) $login = $_SESSION['login'];

        if (is_authed()) 
            return "<span class=info><i class='material-icons iconoffs' style='color:lightskyblue'>info</i>&nbsp;Current User: \"<i>$login</i>\"</span>";
        else  return "<span class=error><b></b></span>";
    }
}

function has_status(){
    return array_key_exists('error_message', $_SESSION) OR
    array_key_exists('status_message', $_SESSION);
}

function error_out($msg, $loc="index.php"){
    set_error($msg);
    header("Location: $loc");
    die();
}

if (isset($reqauth) and $reqauth and ! is_authed()){
    error_out("Operation requires authentication. Please login.",'login.php');
}

if ($stat_log){
    global $page;
    $method = $_SERVER['REQUEST_METHOD'];
    error_log("$method $page");
}

function getphpver(){
    $vers = explode('.', phpversion());
    return $vers[0]*1000 + $vers[1] * 100 + $vers[2];
}

function strictcookie($key,$val){
    if (getphpver() > 7300){
        setcookie($key, $val, [ 'expires' => 0, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'strict']);
    } else {
        setcookie($key,$val,0,'/;SameSite=strict','',true,true);
    }
}

?>