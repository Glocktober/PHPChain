<?php
function form_begin ($action, $method, $id="", $enctype="", $class='')
{
	$formstart = "<FORM ACTION=\"$action\" METHOD=\"$method\" ENCTYPE=\"$enctype\" ID=\"".$id."\" class=\"$class\">\n";
	$formstart .= input_hidden('csrftok',get_csrf());
	return $formstart;
}

function old_form_begin ($action, $method, $id="", $enctype="")
{
	return "<FORM ACTION=\"$action\" METHOD=\"$method\" ENCTYPE=\"$enctype\" ID=\"".$id."\">\n";
}

function form_end ()
{
	return "</FORM>";
}

function input_text ($name, $size, $maxlength, $value=NULL, $style="plain", $tip="")
{
	return "<INPUT TYPE=\"TEXT\" NAME=\"".$name."\" SIZE=\"$size\" MAXLENGTH=\"$maxlength\" VALUE=\"".$value."\" CLASS=\"".$style."\">\n";
}

function input_file ($name, $size, $style="plain")
{
	return "<INPUT TYPE=\"FILE\" NAME=\"".$name."\" SIZE=\"$size\" CLASS=\"".$style."\">\n";
}

function input_passwd ($name, $size, $maxlength, $value=NULL, $style="plain")
{
	global $min_password_length;
	return "<INPUT TYPE=\"PASSWORD\" NAME=\"".$name."\" SIZE=\"$size\" MAXLENGTH=\"$maxlength\" VALUE=\"".$value."\" CLASS=\"".$style."\" title=\"Passwords must be $min_password_length characters or longer\">\n";
}

function input_hidden ($name, $value)
{
	return "<INPUT TYPE=\"HIDDEN\" NAME=\"".$name."\" VALUE=\"".$value."\">\n";
}

function input_radio ($name, $value, $checked=FALSE, $style="plain")
{
	if ($checked) {
		return "<INPUT TYPE=\"RADIO\" NAME=\"".$name."\" VALUE=\"".$value."\" CLASS=\"".$style."\" CHECKED>\n";
	} else {
		return "<INPUT TYPE=\"RADIO\" NAME=\"".$name."\" VALUE=\"".$value."\" CLASS=\"".$style."\">\n";
	}
}

function input_select ($name, $default, $data, $style="plain", $tip='')
{
	$output="<SELECT NAME=\"".$name."\" CLASS=\"".$style."\" title=\"$tip\">\n";

	foreach($data as $value){
		if ($value[0]==$default) {
			$output .= "<OPTION SELECTED VALUE=\"".$value[0]."\">".$value[1]."</OPTION>\n";
		} else {
			$output .= "<OPTION VALUE=\"".$value[0]."\">".$value[1]."</OPTION>\n";
		}
	}
	$output .= "</SELECT>\n";

	return $output;
}

function textarea ($name, $text, $rows, $cols, $style="plain")
{
	$output.="<TEXTAREA NAME=\"$name\" ROWS=\"$rows\" COLS=\"$cols\" CLASS=\"$style\">";
	$output.=$text;
	$output.="</TEXTAREA>\n";

	return $output;
}

function submit ($name, $onclick="", $tip="", $style="")
{
	return "<INPUT TYPE=\"SUBMIT\" class=\"butbut w3-btn w3-small w3-ripple $style\" VALUE=\"$name\" onClick=\"".$onclick."\" title=\"$tip\">\n";
}

function input_button ($name, $onclick="", $style="plain")
{# Not used
	return "<INPUT TYPE=\"BUTTON\" CLASS=\"$style\" VALUE=\"$name\" onClick=\"".$onclick."\">\n";
}

function gorp($fieldname)
{
	if (isset($_GET[$fieldname])) $return = $_GET[$fieldname];

	if (isset($return)) return $return;

	if (strtolower($_SERVER["REQUEST_METHOD"])=="post") {
		if (array_key_exists($fieldname,$_POST)) $return=$_POST[$fieldname];
	}
	if (isset($return)) return $return;
	return null;
}

function get_post($tag){
	$value = null;
	if (array_key_exists($tag,$_POST)){
		$value =$_POST[$tag];
		$value = trim($value);
		$value = htmlspecialchars($value);
	}
	return $value;
}

function sanigorp($tag){
	$tval = gorp($tag);
	$tval = trim($tval);
	$tval = htmlspecialchars($tval);
	return $tval;
}

function menu_button($lab, $loc, $tip=''){
	return action_button($lab, $loc, $tip, 'buttext');
}

function action_button($lab, $loc, $tip="", $class=""){
	return "<form action='$loc' method='POST' class='butform' ><button class='butbut w3-btn w3-small w3-ripple $class' title='$tip'><span class='btntext'>$lab</span></button></form>";	
}

function icon_button($lab, $loc, $tip="", $class=""){
	return "<form action='$loc' method='POST' class='butform' ><button class='butbut w3-small w3-ripple $class' title='$tip' >$lab</button></form>";	
}

function icon_post($glyph, $descr='',$elid='', $url='', $valmap=[], $class='', $tip=''){
    $submit = "document.getElementById('$elid').submit();";
    $ret ="";
    $ret.="<form action='$url' class='iconform' id='$elid' method=POST style='display:inline;'>";
    $ret.="<i onclick=$submit class='material-icons posticon $class' title='$tip'>$glyph</i>$descr";
    foreach ($valmap as $k => $v){
        $ret.="<input type=hidden name='$k' value='$v'>";      
    }   
    return $ret.="</form>";
}

function icon_get($glyph,$desc='', $url='' ,$valmap=[], $class='', $tip='',$iconclass=''){
	$icon = "<i class='material-icons $iconclass'>$glyph</i>";
	$qs='';
	foreach ($valmap as $k=>$v){
		$qs.="&$k=$v";
	}
	error_log("qs = $class");
	if ($qs)  $qs[0] = '?';
	return "<a href='$url$qs' class='w3-button butbut $class' title='$tip'>$icon $desc</a>" ;
}

$glyph_back = '&#xe166;';
// $glyph_edit = '&#xe254;';
$glyph_edit = 'edit';
$glyph_delete = '&#xe872;';
$glyph_notes = '&#xe166;';
$glyph_add = '&#xe03b';
$glyph_save = '&#xe161;';
$glyph_editnote = '&#xe745;';
$glyph_note = '&#xe26c;';
$glyph_addnote = '&#xe89c;';
$glyph_info = '&#xe876;';
$glyph_error ='&#xe000;';
$glyph_sort = '&#xe164;';
$glyph_search = '&#xe8b6;';
$glyph_copy = '&#xe14d;';
$glyph_folder = '&#xe2c7;';
$glyph_lock = '&#xe897;';
$glyph_unlock = '&#xe898;';


?>
