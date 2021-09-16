<?php
function form_begin ($action, $method, $id="", $enctype="")
{
	$formstart = "<FORM ACTION=\"$action\" METHOD=\"$method\" ENCTYPE=\"$enctype\" ID=\"".$id."\">\n";
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

function input_text ($name, $size, $maxlength, $value=NULL, $style="plain")
{
	return "<INPUT TYPE=\"TEXT\" NAME=\"".$name."\" SIZE=\"$size\" MAXLENGTH=\"$maxlength\" VALUE=\"".$value."\" CLASS=\"".$style."\">\n";
}

function input_file ($name, $size, $style="plain")
{
	return "<INPUT TYPE=\"FILE\" NAME=\"".$name."\" SIZE=\"$size\" CLASS=\"".$style."\">\n";
}

function input_passwd ($name, $size, $maxlength, $value=NULL, $style="plain")
{
	return "<INPUT TYPE=\"PASSWORD\" NAME=\"".$name."\" SIZE=\"$size\" MAXLENGTH=\"$maxlength\" VALUE=\"".$value."\" CLASS=\"".$style."\">\n";
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


function input_select ($name, $default, $data, $style="plain")
{
	$output="<SELECT NAME=\"".$name."\" CLASS=\"".$style."\">\n";

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

function submit ($name, $onclick="", $style="plain")
{
	return "<INPUT TYPE=\"SUBMIT\" CLASS=\"$style\" VALUE=\"$name\" onClick=\"".$onclick."\">\n";
}

function input_button ($name, $onclick="", $style="plain")
{
	return "<INPUT TYPE=\"BUTTON\" CLASS=\"$style\" VALUE=\"$name\" onClick=\"".$onclick."\">\n";
}
function gorp($fieldname)
{
	if (isset($_GET[$fieldname])) $return = $_GET[$fieldname];
	if (strtolower($_SERVER["REQUEST_METHOD"])=="get") {
		if (isset($_GET[$fieldname])) $return = $_GET[$fieldname];
	} else if (strtolower($_SERVER["REQUEST_METHOD"])=="post") {
		$return=$_POST["$fieldname"];
	}
	if (isset($return)) return $return;
	return;
}

?>
