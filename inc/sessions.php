<?php
#
# Moved from using cookies to sessions r.k. 09/15/2021
#
session_name('passchain');
session_set_cookie_params(28800, '/', null , True, True);
session_start();

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
    $csrf = gorp('csrftok');
    if (!isset($csrf) OR ($csrf != $_SESSION['csrf_token'])){
        echo "CSRF Failure";
        Header("Location: logout.php?error=\"CSRF ERROR - forced logout\"");
        die();
    }
    set_csrf();
}

?>