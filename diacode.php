<?php

if (($basedir = dirname(@$argv[0])) == "")
	$basedir = ".";

include("env.php");
if (@$dir_helper === null)
	$dir_helper = "{$basedir}/helper/";

$content = stream_get_contents(STDIN);
$sha1 = sha1($content);

$helperlist = array(
	"html" => "php {$basedir}/html.php", 
	"syntaxdiagram" => "php {$basedir}/syntaxdiagram.php"
);

$fp0 = popen("cd ".escapeshellarg($dir_helper)."; ls -1 */*", "r") or die("popen failed.");
while (($line = fgets($fp0)) !== FALSE) {
# '-' is for separator.
	if (preg_match('!^(([-.0-9A-Za-z]+/[-._0-9A-Za-z]+)[.]php)$!', trim($line), $a))
		$helperlist[$a[2]] = "php ".escapeshellarg($dir_helper."/".$a[1]);
}
pclose($fp0);

$contentlist = preg_split("/\r\n|\r|\n/", $content);
foreach ($contentlist as $key => $line) {
	if (!preg_match('!^/[*]([-./_0-9A-Za-z]+)!', $line, $a))
		continue;
	if (($helper = @$helperlist[$a[1]]) === null)
		continue;
	$fp0 = popen("{$helper} {$sha1}", "w") or die("popen failed: ".htmlspecialchars($helper));
	for ($i=$key; $i<count($contentlist); $i++)
		fputs($fp0, $contentlist[$i]."\n");
	pclose($fp0);
}



