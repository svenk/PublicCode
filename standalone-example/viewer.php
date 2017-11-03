<?php

/* Standalone PublicCode Viewer demonstrator instance */

require_once '../PublicCode.lib.php';

// the code viewer itself allows inspecting its code. This allows recursive fun
PublicCode::add(__FILE__, PublicCode::MAIN); // self-introspective
PublicCode::add(dirname(__FILE__).'/../PublicCode.lib.php', PublicCode::AUX);
PublicCode::add(dirname(__FILE__).'/../../lib/CryptoLinks.class.php', PublicCode::AUX);
PublicCode::setViewerPath('viewer.php');

$pc = new CodeViewer();
$pc->baseurl = 'viewer.php';
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
#var_dump($data);

// render('source-viewer.html', $data);

?>
<h1>Website Inspection: <?php print $data['current_file']['basename']; ?></h1>

<p>Available files:
<?php
	foreach($data['files'] as $num => $file) {
		print "<li><a href='${file['link']}'>${file['basename']}</a>" . ($num == $data['current_id'] ? ' (currently inspecting)' : '');
	}
?>

<p>View the code of <em><?php print $data['current_file']['basename']; ?></em>,
   located at <tt><?php print $data['current_file']['_filename']; ?></tt>.
<?php if($data['page_url']) { ?>
    The original URL of this page was <?php print "<a href='${data['page_url']}'>${data['page_url']}</a>"; ?>.
<?php } ?>
   Actually, if you want, you can even view the <a href="<?php print PublicCode::get_secure_link(); ?>">code of this page</a>
   which renders the code of the given page.

<hr>

<?php if($data['current_file']['error']) { ?>
	<b>Errror:</b> <?php print $data['current_file']['error']; ?>
<?php 	} ?>

<?php  print $data['current_file']['code']; ?>

