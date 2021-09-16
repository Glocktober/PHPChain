<?php
# converted from mcrypt to openss encryption r.k. 09/15/2021
function make_iv()
{
	$method = 'aes-256-cbc';
	$ivlen = openssl_cipher_iv_length($method);
	$isCryptoStrong = false;
	$efforts = 0;

	do {
		if ($efforts >= 50){
			throw new Exception("Non-crypto string algorithm used for iv generation");
			break;
		}
		$efforts +=1;
		$iv = openssl_random_pseudo_bytes($ivlen, $isCryptoStrong);
		
	} while (!$isCryptoStrong);
	return $iv;
}

function encrypt($key, $data, $iv)
{
	$method = 'aes-256-cbc';

	$output = openssl_encrypt($data, $method, $key, 0, $iv);
	return $output;
}

function decrypt($key, $data, $iv)
{
	$method = 'aes-256-cbc';
	$output = openssl_decrypt($data, $method, $key, 0, $iv);
	return $output;
}


function maketeststring ()
{
	$s="";
	srand((double)microtime()*1000000);
	for ($i=0;$i<12;$i++) {
		$s.=chr(rand(48,122));
	}
	return $s.$s;
}

function testteststring ($teststring)
{
	if (strlen($teststring) != 24){
		return FALSE;
	}
	if (substr($teststring,0,12)==substr($teststring,12,12)) {
		return TRUE;
	} else {
		return FALSE;
	}
}

?>
