<?php
$page="newlogin";
$reqauth=false;
include ("inc/config.php");
include ("inc/crypt.php");
include ("inc/form.php");

# policy checks
if (!$allow_new_accounts) 
	error_out("Error: ($page) <B>New accounts are disabled</b>: contact your administrator.", 'index.php');

if ($auth_for_new_accounts AND !is_authed())  
	error_out("Error: ($page) <b>Authentication required</b> to create an account", 'index.php');

include("inc/header.php");
?>
<div class="w3-card">
<div class=" w3-padding-16 ">

<div class='w3-center fullw' >
<p class="txtgrey "><i class='material-icons iconoffs isgreen'>manage_accounts</i> Creating a new account</p>
<p>Password must be at least <?php echo $min_password_length?> characters long</p>
</div><br>

<div class='' >
    <form action="newloginsave.php" method="POST">
    <input type="hidden" name="csrftok" value=<?php echo get_csrf() ?> >
</div>

<div class='w3-center w3-margin full2' >
    <label CLASS="plain labform" for='login'><span class=error>*</span>Login name:</label>
    <input type="text" required name="login" size="30" maxlen="255"
        value="" id='login'autocomplete="off" spellcheck="false"
        placeholder="Enter a user name..."
        class='focus' title='Select a unique account name' >
</div><br>

<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="pass"><span class=error>*</span>Password:</label>
    <input id="pass" type="password" name="key" size="30" maxlen="255"
        value="" autocomplete="off" spellcheck="false"
        placeholder="Enter the password..."
        class='password' title='Enter a secure password' >
</div><br>
<div class='w3-center w3-margin' >
    <label CLASS="plain labform" for="key2"><span class=error>*</span>Verify Password:</label>
    <input id="key2" type="password" name="key2" size="30" maxlen="255"
        value="" min autocomplete="off" spellcheck="false"
        placeholder="Repeat password...."
        class='password' title='Verify the password' >
</div><br>

<div class='w3-margin w3-center w3-bar'>
    <a class='butbut w3-button w3-hover-pale-green w3-round' href="index.php" title='Cancel...'><i class='material-icons backicon iconoffs'>chevron_left</i>Back</a>
	<button type="submit" class='w3-button w3-hover-pale-green'><i class='material-icons addicon iconoffs'>person_add</i> Create</button>
</div>
</form>
</div>
<?php
include("inc/footer.php");
?>
