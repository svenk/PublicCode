<?php

require '../template.php';
require_once 'PublicCode.lib.php';
PublicCode::add(__FILE__, PublicCode::MAIN); // self-introspective
PublicCode::add(dirname(__FILE__).'/PublicCode.lib.php', PublicCode::AUX);
PublicCode::add(dirname(__FILE__).'/../lib/CryptoLinks.class.php', PublicCode::AUX);

$pc = new CodeViewer();
if(!$pc->read_secure_link($_GET))
	die('Need files, sorry');
$pc->check_current_id();


// avoid a certain level of recursion
// (ie. "See code of PublicCode of PublicCode of PublicCode of that file")
if(strlen($_SERVER['QUERY_STRING']) > 2000) {
	header("HTTP/1.1 400 Bad Request");
	print "<html><h1>400 Bad Request</h1><p>Request query string too long.";
	exit;
}


$display_id = $pc->current_file_id;

$data = $pc->vars + array(
	'handler' => 'code-viewer-NG',
	'files' => $pc->get_file_data(),
	'current_file' => $pc->get_file_data($display_id, true),
	'current_id' => $display_id,
);

#print '<pre>';
#var_dump($pc);
#var_dump($data);
#exit;

render('source-viewer.html', $data);

/*


todo:

1. $pc als varquelle nutzen
2. PublicCode trennen von eigentlichem Handler, vgl. JC und talks
3. OPENSSL_RAW_DATA bugfix auf github posten



*/
