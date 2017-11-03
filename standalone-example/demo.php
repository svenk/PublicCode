<html>
<head>
<link rel="stylesheet" href="style.css" type="text/css">
<body>

<h1>Standalone example of the PHP <em>PublicCode</em> viewer</h1>

<p>This is a PHP file which demonstrates the capabilities of the
encryptic code viewer.

<p>Link for reading the code:
<?php

require_once'../PublicCode.lib.php';

PublicCode::add(__FILE__, PublicCode::MAIN);
PublicCode::add('style.css', PublicCode::AUX);

PublicCode::setViewerPath('viewer.php');

$secure_link = PublicCode::get_secure_link();

print "<a href='$secure_link'>Read my code</a>";

