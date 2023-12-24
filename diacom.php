<?php

/*

	diagram-in-comment https://github.com/paijp/diagram-in-comment
	
	Copyright (c) 2023 paijp

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

*/

if (($basedir = dirname(@$argv[0])) == "")
	$basedir = ".";

include("env.php");
if (@$dir_helper === null)
	$dir_helper = "{$basedir}/helper/";

$content = stream_get_contents(STDIN);
$sha1 = sha1($content);

$helperlist = array();

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



