<?php
$page='password';
$reqauth=true;
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

$page="password";

sql_conn();


if ($is_get) set_status("Updating Password for \"<b>$loginname</b>\": Passwords must be at least $min_password_length long");
$output='';
$output.=form_begin('passwordsave.php',"POST");
$output.=input_hidden("action","save");
$output.="<TABLE BORDER=\"0\" CELLPADDING=\"2\" CELLSPACING=\"0\">\n";
$output.="<TR><TD CLASS=\"plain\">New Password: </TD><TD CLASS=\"plain\">".input_passwd("newkey",20,255,'','plain focus')."</TD></TR>\n";
$output.="<TR><TD CLASS=\"plain\">Verify new password: &nbsp;&nbsp;</TD><TD CLASS=\"plain\">".input_passwd("newkey2",20,255)."</TD></TR>\n";

$output.="<TR><td></td><TD CLASS=\"w3-center\" COLSPAN=\"2\" ALIGN=\"RIGHT\"><a class='butbut w3-button w3-border w3-hover-pale-green' href=\"index.php\" title='Make No Changes'>Back</a>&nbsp;&nbsp;";
$output.=submit("Save",'',"Change password, reencrypting all entries","w3-border w3-hover-pale-green")."</TD></TR>\n";
$output.="</TABLE>\n";
$output.=form_end();


include ("inc/header.php");

echo $output;

include ("inc/footer.php");
?>
