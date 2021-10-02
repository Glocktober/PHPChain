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
<div class="w3-card w3-margin">
    <div class="">
        <form id='pwd' action="passwordsave.php" method="post">
            <input type="hidden" name="csrftok" value=<?php echo get_csrf() ?>>
    </div>
<div class=" w3-center w3-margin">
<p class="txtgrey w3-margin-top"><i class='material-icons iconoffs isgray'>person</i>
        Changing password for: "<b><?php echo $loginname ?></p>
</div>
    <div class='w3-center w3-margin'>
        <label for="oldpassword" class="plain labform">&nbsp;Current Password:</label>
        <input type="password" size=20 maxlength=255 minlength=<?php echo $min_password_length ?> name="currentpassword" id="oldpassword" class='plain focus' required >
    </div><br>
    <div class='w3-center w3-margin'>
        <label for="newkey" class="plain labform">&nbsp;New Password:</label>
        <input type="password" size=20 maxlength=255 minlength=<?php echo $min_password_length ?> name="newkey" id="newkey" class='plain focus' required >
    </div><br>
    <div class='w3-center w3-margin'>
        <label for="newkey2" class="plain labform">Verify password:</label>	
        <input type="password" size=20 maxlength=255 minlength=<?php echo $min_password_length ?> name="newkey2" id="newkey2" class='plain' required >
    </div><br>
    <div class='w3-margin'>
        <label for="" class="plain labform">&nbsp;</label>
    <a class='w3-btn w3-hover-pale-green w3-round' href="index.php" title='Make No Changes'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
        <button type="submit" class='w3-btn w3-hover-pale-green'><i class='material-icons addicon iconoffs'>add</i> Change Password</button>
    </div>
</form>
</div>
<?php
include ("inc/footer.php");
?>
