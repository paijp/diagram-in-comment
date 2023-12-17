<?php

/*

	diagram-in-code https://github.com/paijp/diagram-in-code
	
	Copyright (c) 2023 paijp

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

*/

/*syntaxdiagram
{[pin name on the project
{( 
[port name
r}
|{(#
|(/
}[comment
}
*/

/*html
<pre>
<h1>sample</h1>
/*jp.pa-i/map32mx2-28
LED1	RPB6
P2W	RPB3	UTX1
W2P	RPB2	URX1
#<h1>&darr;</h1>
*/

/*html
<h1>This shows....</h1>
- LED1 is on pin 9(5V tolerant).

- P2W is on pin 7.
- RPB3R = 1; will be needed.

- W2P is on pin 6.
- U1RXR = 4; will be needed.

<h1>important</h1>
- Please use U1RXR instead of URX1R in your code.
- SS1 will be both input and output port.

</pre>
<hr />
*/

list($body) = explode("*/", stream_get_contents(STDIN), 2);

# T: 5V
# 20: max20mA 15:max15mA other:max10mA
if (@$pinarray === null) {
	$pinarray = array(
		"", 
		"T MCLR", 
		"VREFP CVREFP AN0 C3INC RPA0 CTED1 RA0", 
		"VREFN CVREFN AN1 RPA1 CTED2 RA1", 
		"PGED1 AN2 C1IND C2INB C3IND RPB0 RB0", 
		"PGEC1 AN3 C1INC C2INA RPB1 CTED12 RB1", 
		"AN4 C1INB C2IND RPB2 SDA2 CTED13 RB2", 
		"AN5 C1INA C2INC RTCC RPB3 SCL2 RB3", 
		
		"VSS", 
		"OSC1 CLKI RPA2 RA2", 
		"OSC2 CLKO RPA3 PMA0 RA3", 
		"SOSCI RPB4 RB4", 
		"SOSCO RPA4 T1CK CTED9 PMA1 RA4", 
		"VDD", 
		"T PGED3 RPB5 PMD7 RB5", 
		
		"T PGEC3 RPB6 PMD6 RB6", 
		"T TDI RPB7 CTED3 PMD5 INT0 RB7", 
		"T TCK RPB8 SCL1 CTED10 PMD4 RB8", 
		"T TDO RPB9 SDA1 CTED4 PMD3 RB9", 
		"VSS", 
		"VCAP", 
		"T PGED2 RPB10 CTED11 PMD2 RB10", 
		
		"T PGEC2 TMS RPB11 PMD1 RB11", 
		"AN12 PMD0 RB12", 
		"AN11 RPB13 CTPLS PMRD RB13", 
		"CVREFOUT AN10 C3INB RPB14 SCK1 CTED5 PMWR RB14", 
		"AN9 C3INA RPB15 SCK2 CTED6 PMCS1 RB15", 
		"AVSS", 
		"AVDD"
	);
}
if (@$portarray === null) {
	$portarray = array(
		"INT4 TCK2 IC4 SS1 REFCLKI1 / RPA0=0 RPB3=1 RPB4=2 RPB15=3 RPB7=4 / UTX1=1 URTS2=2 SS1=3 OC1=5 COUT2=7", 
		"INT3 TCK3 IC3 UCTS1 URX2 SDI1 / RPA1=0 RPB5=1 RPB1=2 RPB11=3 RPB8=4 / SDO1=3 SDO2=4 OC2=5 COUT3=7", 
		"INT2 TCK4 IC1 IC5 URX1 UCTS2 SDI2 OCFB1 / RPA2=0 RPB6=1 RPA4=2 RPB13=3 RPB2=4 / SDO1=3 SDO2=4 OC4=5 OC5=6 REFCLKO1=7", 
		"INT1 TCK5 IC2 SS2 OCFA1 / RPA3=0 RPB14=1 RPB0=2 RPB10=3 RPB9=4 / URTS1=1 UTX2=2 SS2=4 OC3=5 C1OUT=7"
	);
}


$portuse = array();
foreach ($pinarray as $val) {
	foreach (explode(" ", $val) as $name)
		if (preg_match('/^R[A-N]/', $name))
			$portuse[$name] = 0;
}


$colorlist = array("#ffff00", "#80ff80", "#80ffff", "#C0C0ff");
$portcolor = array();
foreach ($portarray as $key => $val)
	foreach (explode(" ", $val) as $name)
		if (preg_match('/^RP([^=]*)/', $name, $a)) {
			$portcolor["RP".$a[1]] = $colorlist[$key];
		}


$fullportlist = array();
$partportlist = array();
$localnamecolor = array();
foreach (preg_split("/\r\n|\r|\n/", $body) as $line) {
	if (preg_match('!^/!', $line))
		continue;
	if (preg_match('/^#/', $line))
		continue;
	$a = preg_split("/[ \t]+/", $line);
	$localname = $a[0];
	for ($i=1; $i<count($a); $i++) {
		if (preg_match('/[0-9]$/', $s = $a[$i]))
			@$fullportlist[$s] .= ":".$localname;
		else if ($s != "")
			@$partportlist[$s] .= ":".$localname;
		if (($c = @$portcolor[$s]) !== null)
			$localnamecolor[$localname] = $c;
	}
}

$ret = "";
$ret .= '<TABLE>';
foreach ($pinarray as $key => $val) {
	if ($key == 0)
		continue;
	$ret .= sprintf("<TR><TD valign=top>%s", $key);
	foreach (explode(" ", $val) as $name) {
		if (($name == "T")||($name == "EF")) {
			$ret .= sprintf(' <B style="background:#ff0000;">%s</B>', $name);
			continue;
		}
		if ((int)$name > 0) {
			$ret .= sprintf(' <B style="color:#ff0000;">%s</B>', $name);
			continue;
		}
		if (($color = @$portcolor[$name]) === null)
			$color = "#ffffff";
		
		$ret .= sprintf('<TD valign=top align=right><SPAN style="background:%s;">%s</SPAN><TD>', $color, $name);
		
		if (($s = @$fullportlist[$name]) !== null)
			$ret .= sprintf('<B style="color:#8000ff;">%s</B>', $s);
		foreach ($partportlist as $key2 => $val2)
			if (strpos($name, $key2) === 0)
				$ret .= sprintf('<B style="color:#ff0000;">%s</B>', $val2);
	}
	$s .= "<BR>";
}
$ret .= "</TABLE><BR>";


$ret .= "<TABLE border>\n";
foreach ($portarray as $key => $val) {
	if (($key & 1) == 0)
		$ret .= "<TR><TD>";
	else
		$ret .= "<TD>//<TD>";
	foreach (explode(" ", $val) as $namenum) {
		if (count($a = explode("=", $namenum)) > 1)
			$num = "(".$a[1].") ";
		else
			$num = "";
		$name = $a[0];
		if ($name == "/") {
			$ret .= "\n<TD>&gt;<TD>";
			continue;
		}
		if (($color = @$portcolor[$name]) === null)
			$color = "#ffffff";
		
		$ret .= sprintf('%s<SPAN style="background:%s;">%s</SPAN>', $num, $color, $name);
		if (($num == "")||($color != "#ffffff"))
			$ret .= "R";
		
		if (($s = @$fullportlist[$name]) === null)
			;
		else if ((($c = @$localnamecolor[substr($s, 1)]) === null)||($c == $colorlist[$key]))
			$ret .= sprintf('<B style="color:#8000ff;">%s</B>', $s);
		foreach ($partportlist as $key2 => $val2)
			if (strpos($name, $key2) === 0)
				$ret .= sprintf('<B style="color:#ff0000;">%s</B>', $val2);
		
		$ret .= "<BR>";
	}
	$ret .= "\n";
}
$ret .= "</TABLE><BR>\n";


$portgrouplist = array();
$portbitlist = array();
foreach ($pinarray as $key => $val) {
	if ($key == 0)
		continue;
	$portname = null;
	foreach (explode(" ", $val) as $name) {
		if (preg_match('/^R([A-Z])[0-9]+$/', $name, $a)) {
			$portname = $name;
			$portgrouplist[$a[1]] = 1;
			break;
		}
	}
	if (@$portbitlist[$portname] === null)
		$portbitlist[$portname] = "";
	foreach (explode(" ", $val) as $name) {
		if (($s = @$fullportlist[$name]) !== null)
			$portbitlist[$portname] .= sprintf('<B style="color:#8000ff;">%s</B><BR>', substr($s, 1));
		foreach ($partportlist as $key2 => $val2)
			if (strpos($name, $key2) === 0)
				$portbitlist[$portname] .= sprintf('<B style="color:#ff0000;">%s</B><BR>', substr($val2, 1));
	}
}
ksort($portgrouplist);
$ret .= "<TABLE border>\n";
foreach($portgrouplist as $portgroup => $dummy) {
	$ret .= "<TR>";
	$nextline = "";
	for ($bit=15; $bit>=0; $bit--) {
		$ret .= "<TH>";
		$nextline .= "<TD>";
		$name = "R{$portgroup}{$bit}";
		if (($s = @$portbitlist[$name]) === null)
			continue;
		if (($color = @$portcolor["RP{$portgroup}{$bit}"]))
			$ret .= '<SPAN style="background:'.$color.';">'."{$name}</SPAN>";
		else
			$ret .= $name;
		$nextline .= $s;
	}
	$ret .= "\n<TR>".$nextline."\n";
}
$ret .= "</TABLE>\n";

print $ret;
?>
