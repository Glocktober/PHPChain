<?php
$page="logon";
$reqauth=false;
include ("inc/config.php");
include ("inc/form.php");

// $login = array_key_exists('login',$_SESSION) ? $_SESSION['login']: '';
$login = array_key_exists('chainlogin', $_COOKIE) ? 
    $login=$_COOKIE['chainlogin']: '';

include ("inc/header.php");
?>
<!-- login form  -->
<div class="w3-card">
<div class=" w3-padding-16 ">

<div class='w3-center fullw' >
<p class="txtgrey "><i class='material-icons iconoffs isgreen'>person</i> Login to phpchain</p>
</div><br>

<div class='' >
    <form id="login" action="loginexec.php" method="POST">
    <input type="hidden" name="csrftok" value=<?php echo get_csrf() ?> >
</div>

<div class='w3-center w3-margin full' >
    <label CLASS="plain labform" for='login'><span class=error>*</span>Login name:</label>
    <input type="text" required name="login" size="30" maxlen="255"
        value="<?php echo $login?>" id='login'autocomplete="off" spellcheck="false"
        placeholder="Enter your user name..."
        class='focus' title='Your account name' >
</div><br>

<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="pass"><span class=error>*</span>Password:</label>
    <input id="pass" type="password" name="key" size="30" maxlen="255"
        value="" autocomplete="off" spellcheck="false"
        placeholder="Enter your password..."
        class='' title='Your account password' >
</div><br>

<div class='w3-bar w3-margin-top'>
<div class="labform">&nbsp;</div>
<div>
	<a class='butbut w3-button w3-hover-pale-green w3-round' href="index.php" title='Cancel...'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
	<button type="submit" class='w3-button w3-hover-pale-green'><i class='material-icons addicon iconoffs'>login</i> Login</button>
</div>
</div>
</form>
</div>
<script>
setTimeout(
	()=>{
		window.location.reload();
	}, 8*60*1000
)
</script>
<?php
include("inc/footer.php");
?>

