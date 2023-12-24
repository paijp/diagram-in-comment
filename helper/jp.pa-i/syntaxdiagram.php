<?php

/*

	diagram-in-comment https://github.com/paijp/diagram-in-comment
	
	Copyright (c) 2023 paijp

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

*/

/*jp.pa-i/syntaxdiagram
{{|({
|(}
|(|
|(r
|(?
r}{|((
[literal
|([
[name
}|(#
[config key
(=
[number
}
*/

/*jp.pa-i/html
<pre>
<h1>simple</h1>
/*jp.pa-i/syntaxdiagram
[variable name
(=
[expression
(;

*/

/*jp.pa-i/html
<h1>selectable / optional</h1>
/*jp.pa-i/syntaxdiagram
{(http
|(https
}(://
[domain
{|(:
[port
}

*/

/*jp.pa-i/html
<h1>repeat</h1>
/*jp.pa-i/syntaxdiagram
{[body
r[repeat
}

*/

/*jp.pa-i/html
<h1></h1>
/*jp.pa-i/syntaxdiagram
{[body
r[repeat
[more
}

*/

/*jp.pa-i/html
<h1></h1>
/*jp.pa-i/syntaxdiagram
{[body
r}

*/

/*jp.pa-i/html
<h1></h1>
/*jp.pa-i/syntaxdiagram
{|(?
{[key
(=
[value
r(&
}}

*/

/*jp.pa-i/html
<h1>highlight</h1>
/*jp.pa-i/syntaxdiagram
(abc
?(def
(ghi

*/

/*jp.pa-i/html
<h1>size</h1>
/*jp.pa-i/syntaxdiagram
#fontsize=30
(abc

*/

/*jp.pa-i/html
</pre>
*/


if (!function_exists("mb_strwidth")) {
	function	mb_strwidth($s)
	{
		return strlen($s);
	}
}


list($body) = explode("*/", stream_get_contents(STDIN), 2);


class	systeminfo {
	var	$v_fontsize = 14;
	function	__construct() {
		$this->font = array();
	}
	function	pos($mul = 1, $div = 1) {
		return $this->v_fontsize * $mul / $div;
	}
}


class	holder {
	var	$itemlist;
	function	__construct() {
		$this->itemlist = array();
	}
	function	p($mul = 1, $div = 1) {
		global	$systeminfo;
		
		return $systeminfo->pos($mul, $div);
	}
	function	draw($left, $top, &$right, &$bottom, &$gid = -1, $pixel = -1) {
		$bottom = $top;
		for ($i=0; $i<count($this->itemlist); $i++) {
			$this->itemlist[$i]->draw($left, $top, $r, $b, $gid, $pixel);
			$left = $r;
			$bottom = max($bottom, $b);
		}
		$right = $left;
		return 0;
	}
	function	additem($item) {
		$this->itemlist[] = $item;
	}
}


class	functionholder extends holder {
	var	$selecter = 0;
	function	__construct() {
		parent::__construct();
		$this->itemlist[0] = array();
		$this->itemlist[1] = array();
	}
	function	draw($left, $top, &$right, &$bottom, &$gid = -1, $pixel = -1) {
		if (count($this->itemlist[1]) == 0)
			return $this->draw_inner($left, $top, $right, $bottom, $gid, $pixel);
		
		$this->draw_inner($left, $top, $right, $bottom);
		$bodywidth = $right - $left;
		$widthlist = array();
		for ($i=0; $i<count($this->itemlist[1]); $i++) {
			$t = $bottom;
			$this->itemlist[1][$i]->draw($left, $t, $r, $bottom);
			$widthlist[$i] = $r - $left;
			$right = max($right, $r + $this->p());
		}
		$right += $this->p(2);
		$v = $this->p(4 * (pow(2, 0.5) - 1), 6);
		$gid .= sprintf('<path fill="none" d="M%d,%d C%d,%d %d,%d %d,%d L%d,%d"/>', $left + $this->p(), $top + $this->p(5, 4), $left + $this->p() - $v, $top + $this->p(5, 4), $left + $this->p(1, 2), $top + $this->p(7, 4) - $v, $left + $this->p(1, 2), $top + $this->p(7, 4), $left + $this->p(1, 2), $t + $this->p(3, 4));
		$gid .= sprintf('<path fill="none" d="M%d,%d L%d,%d C%d,%d %d,%d %d,%d" />', $right - $this->p(1, 2), $t + $this->p(3, 4), $right - $this->p(1, 2), $top + $this->p(7, 4), $right - $this->p(1, 2), $top + $this->p(7, 4) - $v, $right - $this->p() + $v, $top + $this->p(5, 4), $right - $this->p(), $top + $this->p(5, 4));
		
		$width = $right - $left;
		$offset = ($width - $bodywidth) / 2;
		$linepos = $top + $this->p(5, 4);
		$gid .= sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" />', $left, $linepos, $left + $offset, $linepos);
		$gid .= sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" />', $right - $offset, $linepos, $right, $linepos);
		$this->draw_inner($left + $offset, $top, $r, $bottom, $gid, $pixel);
		for ($i=0; $i<count($this->itemlist[1]); $i++) {
			$offset = ($width - $widthlist[$i]) / 2;
			$linepos = $bottom + $this->p(5, 4);
			$gid .= sprintf('<path fill="none" d="M%d,%d C%d,%d %d,%d %d,%d L%d,%d" />', $left + $this->p(1, 2), $linepos - $this->p(1, 2), $left + $this->p(1, 2), $linepos - $this->p(1, 2) + $v, $left + $this->p() - $v, $linepos, $left + $this->p(), $linepos, $left + $offset, $linepos);
			$gid .= sprintf('<path fill="none" d="M%d,%d L%d,%d C%d,%d %d,%d %d,%d" />', $right - $offset, $linepos, $right - $this->p(), $linepos, $right - $this->p() + $v, $linepos, $right - $this->p(1, 2), $linepos - $this->p(1, 2) + $v, $right - $this->p(1, 2), $linepos - $this->p(1, 2));
			$gid .= sprintf('<path fill="none" d="M%d,%d L%d,%d L%d,%d" />', $right - $offset + $this->p(1, 2), $linepos + round($this->p(1, 4)), $right - $offset, $linepos, $right - $offset + $this->p(1, 2), $linepos - round($this->p(1, 4)));
			$t = $bottom;
			$this->itemlist[1][$i]->draw($left + $offset, $t, $r, $bottom, $gid, $pixel);
		}
		return 3;
	}
	function	draw_inner($left, $top, &$right, &$bottom, &$gid = -1, $pixel = -1) {
		$right = $left;
		$bottom = $top;
		switch (count($this->itemlist[0])) {
			case	0:
				return 0;
			case	1:
				return $this->itemlist[0][0]->draw($left, $top, $right, $bottom, $gid, $pixel);
		}
		$widthlist = array();
		for ($i=0; $i<count($this->itemlist[0]); $i++) {
			$t = $bottom;
			$this->itemlist[0][$i]->draw($left, $t, $r, $bottom);
			$widthlist[$i] = $r - $left;
			$right = max($right, $r);
		}
		$right += $this->p(2);
		$v = $this->p(4 * (pow(2, 0.5) - 1), 6);
		$gid .= sprintf('<path fill="none" d="M%d,%d C%d,%d %d,%d %d,%d L%d,%d"/>', $left, $top + $this->p(5, 4), $left + $v, $top + $this->p(5, 4), $left + $this->p(1, 2), $top + $this->p(7, 4) - $v, $left + $this->p(1, 2), $top + $this->p(7, 4), $left + $this->p(1, 2), $t + $this->p(3, 4));
		$gid .= sprintf('<path fill="none" d="M%d,%d L%d,%d C%d,%d %d,%d %d,%d" />', $right - $this->p(1, 2), $t + $this->p(3, 4), $right - $this->p(1, 2), $top + $this->p(7, 4), $right - $this->p(1, 2), $top + $this->p(7, 4) - $v, $right - $v, $top + $this->p(5, 4), $right, $top + $this->p(5, 4));
		$width = $right - $left;
		$bottom = $top;
		for ($i=0; $i<count($this->itemlist[0]); $i++) {
			$offset = ($width - $widthlist[$i]) / 2;
			$linepos = $bottom + $this->p(5, 4);
			if ($i == 0) {
				$gid .= sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" />', $left, $linepos, $left + $offset, $linepos);
				$gid .= sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" />', $right - $offset, $linepos, $right, $linepos);
			} else {
				$gid .= sprintf('<path fill="none" d="M%d,%d C%d,%d %d,%d %d,%d L%d,%d" />', $left + $this->p(1, 2), $linepos - $this->p(1, 2), $left + $this->p(1, 2), $linepos - $this->p(1, 2) + $v, $left + $this->p() - $v, $linepos, $left + $this->p(), $linepos, $left + $offset, $linepos);
				$gid .= sprintf('<path fill="none" d="M%d,%d L%d,%d C%d,%d %d,%d %d,%d" />', $right - $offset, $linepos, $right - $this->p(), $linepos, $right - $this->p() + $v, $linepos, $right - $this->p(1, 2), $linepos - $this->p(1, 2) + $v, $right - $this->p(1, 2), $linepos - $this->p(1, 2));
			}
			$t = $bottom;
			$this->itemlist[0][$i]->draw($left + $offset, $t, $r, $bottom, $gid, $pixel);
		}
		return 3;
	}
	function	additem($item) {
		$this->itemlist[$this->selecter][] = $item;
	}
}


class	linerholder extends holder {
	function	__construct($color = "000000") {
		parent::__construct();
		$this->color = $color;
	}
	function	draw($left, $top, &$right, &$bottom, &$gid = -1, $pixel = -1) {
		$flagleft = 0;
		$flagright = 0;
		$right = $left;
		$bottom = $top + $this->p(5, 2);
		for ($i=0; $i<count($this->itemlist); $i++) {
			$left = $right;
			$f = $this->itemlist[$i]->draw($left, $top, $r, $b);
			if ($i == 0)
				$flagleft = $f & 1;
			else if (($f & 1)||($flagright))
				;
			else {
				$left = $right + $this->p();
				if (is_string($gid))
					$gid .= sprintf('<line stroke="#%s" x1="%d" y1="%d" x2="%d" y2="%d" />', $this->color, $right, $top + $this->p(5, 4), $left, $top + $this->p(5, 4));
				else if ($gid >= 0)
					imageline($gid, $right, $top + $this->p(5, 4), $left, $top + $this->p(5, 4), $pixel);
			}
			$flagright = $f & 2;
			$this->itemlist[$i]->draw($left, $top, $right, $b, $gid, $pixel);
			$bottom = max($bottom, $b);
		}
		return $flagleft | $flagright;
	}
}


class	constholder extends holder {
	var	$string = "";
	var	$color = "";
	function	__construct($string = "", $color = "000000") {
		parent::__construct();
		$this->string = $string;
		$this->color = $color;
	}
	function	draw($left, $top, &$right, &$bottom, &$gid = -1, $pixel = -1) {
		global	$systeminfo;
		
		$len = mb_strwidth($this->string);
		$offset = max(4 - $len, 2) * $this->p(1, 4);
		$bottom = $top + $this->p(5, 2);
		$right = $left + $len * $this->p(1, 2) + $offset * 2;
		$gid .= sprintf('<rect stroke="#%s" fill="none" x="%d" y="%d" width="%d" height="%d" rx="%d" ry="%d"/>', $this->color, $left, $top + $this->p(1, 4), $right - $left, $this->p(8, 4), $this->p(), $this->p());
		$gid .= sprintf('<text stroke="none" fill="#%s" x="%d" y="%d" text-anchor="middle" font-size="%d">%s</text>', $this->color, ($left + $right) / 2, $top + $this->p(6.75, 4) - 1, $this->p(), htmlspecialchars($this->string));
		return 0;
	}
}


class	variableholder extends holder {
	var	$string = "";
	var	$color = "";
	function	__construct($string = "", $color = "000000") {
		parent::__construct();
		$this->string = $string;
		$this->color = $color;
	}
	function	draw($left, $top, &$right, &$bottom, &$gid = -1, $pixel = -1) {
		global	$systeminfo;
		
		$bottom = $top + $this->p(5, 2);
		$right = $left + mb_strwidth($this->string) * $this->p(1, 2) + $this->p();
		$gid .= sprintf('<rect stroke="#%s" fill="none" x="%d" y="%d" width="%d" height="%d" />', $this->color, $left, $top + $this->p(1, 4), $right - $left, $this->p(8, 4));
		$gid .= sprintf('<text stroke="none" fill="#%s" x="%d" y="%d" text-anchor="middle" font-size="%d">%s</text>', $this->color, ($left + $right) / 2, $top + $this->p(6.75, 4) - 1, $this->p(), htmlspecialchars($this->string));
		return 0;
	}
}


$systeminfo = new systeminfo();

$rootholder = new linerholder();
$stack = array($rootholder);
foreach (preg_split("/\r|\n|\r\n/", $body) as $line) {
	if (preg_match('/^#([_A-Za-z][_0-9A-Za-z]*)=([0-9]+)/', $line, $a)) {
		$s = "v_".$a[1];
		$systeminfo->$s = $a[2] + 0;
		continue;
	}
	$color = "000000";
	
	if (!preg_match('/^([^[(]*)(.*)/', $line, $a))
		continue;
	for ($i=0; $i<strlen($a[1]); $i++) {
		switch (substr($a[1], $i, 1)) {
			case	"?":
				$color = "ffa000";
				break;
			case	"{":
				array_unshift($stack, new functionholder());
				$stack[1]->additem($stack[0]);
				array_unshift($stack, new linerholder($color));
				$stack[1]->additem($stack[0]);
				break;
			case	"}":
				if (count($stack) < 3)
					break;
				array_shift($stack);
				array_shift($stack);
				break;
			case	"|":
				if (count($stack) < 3)
					break;
				array_shift($stack);
				array_unshift($stack, new linerholder($color));
				$stack[1]->selecter = 0;
				$stack[1]->additem($stack[0]);
				break;
			case	"r":
				if (count($stack) < 3)
					break;
				array_shift($stack);
				array_unshift($stack, new linerholder($color));
				$stack[1]->selecter = 1;
				$stack[1]->additem($stack[0]);
				break;
		}
	}
	switch (substr($a[2], 0, 1)) {
# '/*' line will be ignored.
		case	"(":
			$obj = new constholder(substr($a[2], 1) ,$color);
			$stack[0]->additem($obj);
			break;
		case	"[":
			$obj = new variableholder(substr($a[2], 1), $color);
			$stack[0]->additem($obj);
			break;
	}
}

$w = $h = 0;
$s = "";
$rootholder->draw(2, 0, $w, $h, $s);
$w += 2;
print <<<EOO
<svg xmlns="http://www.w3.org/2000/svg" width="{$w}px" height="{$h}px" stroke="#000000" fill="#000000" stroke-width="2px">
{$s}
</svg>
EOO;

