<?php
$page='password';
$reqauth=true;
include ("inc/config.php");
include ("inc/form.php");
include ("inc/crypt.php");

sql_conn();

$auth = is_authed();

$loginname = $_SESSION['login'];

if (!has_status()) set_status("Changing password for '<b>$loginname</b>'");

include ("inc/header.php");

?>
<div class=" w3-padding-16 ">
<div class="w3-card w3-margin div50">
    <div class="">
        <form id='pwd' action="passwordsave.php" method="post">
            <input type="hidden" name="csrftok" value=<?php echo get_csrf() ?>>
    </div>
<div class=" w3-center">
<p class="txtgrey "><i class='material-icons iconoffs isgray'>person</i>
        Changing password for: "<b><?php echo $loginname ?></p>
</div>
    <div class='w3-center w3-margin'>
        <label for="oldpassword">&nbsp;Current Password:</label>
        <input type="password" size=20 maxlength=255 minlength=<?php echo $min_password_length ?> name="currentpassword" id="oldpassword" class='plain focus' required >
    </div>
    <div class='w3-center w3-margin'>
        <label for="newkey">&nbsp;New Password:</label>
        <input type="password" size=20 maxlength=255 minlength=<?php echo $min_password_length ?> name="newkey" id="newkey" class='plain focus' required >
    </div>
    <div class='w3-center w3-margin'>
        <label for="newkey2">Verify password:</label>	
        <input type="password" size=20 maxlength=255 minlength=<?php echo $min_password_length ?> name="newkey2" id="newkey2" class='plain' required >
    </div>
    <div class='w3-margin w3-center w3-bar'>
    <a class='butbut w3-button w3-hover-pale-green w3-round' href="index.php" title='Make No Changes'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
        <button type="submit" class='w3-button w3-hover-pale-green'><i class='material-icons addicon iconoffs'>add</i> Change Password</button>
    </div>
</form>
</div>
<?php
include ("inc/footer.php");
?>
