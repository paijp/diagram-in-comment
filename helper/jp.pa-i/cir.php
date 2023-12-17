<?php

/*

	diagram-in-code https://github.com/paijp/diagram-in-code
	
	Copyright (c) 2023 paijp

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.

*/


class	drawobject {
	var	$l, $t, $r, $b;
	function	draw($g, $offx = 0, $offy = 0) {
	}
}


class	dotobject extends drawobject {
	function	__construct($x, $y) {
		$this->l = $x - 1;
		$this->t = $y - 1;
		$this->r = $x + 1;
		$this->b = $y + 1;
	}
	function	draw($g, $offx = 0, $offy = 0) {
		$c1 = imagecolorresolve($g, 0, 0, 0);
		$l = $this->l + $offx;
		$t = $this->t + $offy;
		$r = $this->r + $offx;
		$b = $this->b + $offy;
		imagefilledrectangle($g, $l, $t, $r, $b, $c1);
	}
}


class	lineobject extends drawobject {
	var	$sx, $sy, $ex, $ey;
	function	__construct($sx, $sy, $ex, $ey) {
		$this->sx = $sx;
		$this->sy = $sy;
		$this->ex = $ex;
		$this->ey = $ey;
		$this->l = min($sx, $ex);
		$this->r = max($sx, $ex);
		$this->t = min($sy, $ey);
		$this->b = max($sy, $ey);
	}
	function	draw($g, $offx = 0, $offy = 0) {
		$c1 = imagecolorresolve($g, 0, 0, 0);
		imageline($g, $offx + $this->sx, $offy + $this->sy, $offx + $this->ex, $offy + $this->ey, $c1);
	}
}


class	boxobject extends drawobject {
	function	__construct($l, $t, $r, $b) {
		$this->l = $l;
		$this->t = $t;
		$this->r = $r;
		$this->b = $b;
	}
	function	draw($g, $offx = 0, $offy = 0) {
		$c0 = imagecolorresolve($g, 255, 255, 255);
		$c1 = imagecolorresolve($g, 0, 0, 0);
		$l = $this->l + $offx;
		$t = $this->t + $offy;
		$r = $this->r + $offx;
		$b = $this->b + $offy;
		$a = array($l, $t, $l, $b - 8, $l + 8, $b, $r, $b, $r, $t);
		imagefilledpolygon($g, $a, count($a) / 2, $c0);
		imagepolygon($g, $a, count($a) / 2, $c1);
	}
}


class	hexobject extends drawobject {
	var	$x, $y, $vx, $vy;
	var	$s;
	var	$color;
	function	__construct($x, $y, $vx, $vy, $s = "", $color = 0) {
		$this->x = $x;
		$this->y = $y;
		$this->vx = $vx;
		$this->vy = $vy;
		$this->s = $s;
		$this->color = $color;
		if (($vx)) {
			$this->l = min($x, $x + $vx);
			$this->r = max($x, $x + $vx);
			$this->t = $y - 6;
			$this->b = $y + 6;
		} else {
			$this->l = $x - 6;
			$this->r = $x + 6;
			$this->t = min($y, $y + $vy);
			$this->b = max($y, $y + $vy);
		}
	}
	function	draw($g, $offx = 0, $offy = 0) {
		$c0 = imagecolorresolve($g, 255, 255, 255);
		$c1 = imagecolorresolve($g, 0, 0, 0);
		$size = 3;
		$offc = 4;
		$offl = 0;
		if (($this->vx)) {
			$l = $this->l + $offx;
			$r = $this->r + $offx;
			$t = $this->t + $offy;
			$b = $this->b + $offy;
			$y = $this->y + $offy;
			$a = array($l, $y, $l + 3, $t, $r - 3, $t, $r, $y, $r - 3, $b, $l + 3, $b);
			if (($this->color)) {
				imagefilledpolygon($g, $a, count($a) / 2, $c1);
				imagestring($g, $size, $l + $offc, $t + $offl, $this->s, $c0);
			} else {
				imagefilledpolygon($g, $a, count($a) / 2, $c0);
				imagepolygon($g, $a, count($a) / 2, $c1);
				imagestring($g, $size, $l + $offc, $t + $offl, $this->s, $c1);
			}
		} else {
			$l = $this->l + $offx;
			$r = $this->r + $offx;
			$t = $this->t + $offy;
			$b = $this->b + $offy;
			$x = $this->x + $offx;
			$a = array($x, $t, $r, $t + 3, $r, $b - 3, $x, $b, $l, $b - 3, $l, $t + 3);
			if (($this->color)) {
				imagefilledpolygon($g, $a, count($a) / 2, $c1);
				imagestringup($g, $size, $l + $offl, $b - $offc, $this->s, $c0);
			} else {
				imagefilledpolygon($g, $a, count($a) / 2, $c0);
				imagepolygon($g, $a, count($a) / 2, $c1);
				imagestringup($g, $size, $l + $offl, $b - $offc, $this->s, $c1);
			}
		}
	}
}


class	stringobject extends drawobject {
	var	$s;
	function	__construct($x, $y, $s = "") {
		$this->l = $x;
		$this->t = $y;
		$this->s = $s;
		$this->r = $x + strlen($s) * 7;
		$this->b = $y + 11;
	}
	function	draw($g, $offx = 0, $offy = 0) {
		$c1 = imagecolorresolve($g, 0, 0, 0);
		imagestring($g, 3, $offx + $this->l, $offy + $this->t, $this->s, $c1);
	}
}


class	vstringobject extends drawobject {
	var	$s;
	function	__construct($x, $y, $s = "") {
		$this->l = $x;
		$this->t = $y;
		$this->s = $s;
		$this->r = $x + 11;
		$this->b = $y + strlen($s) * 7;
	}
	function	draw($g, $offx = 0, $offy = 0) {
		$c1 = imagecolorresolve($g, 0, 0, 0);
		imagestringup($g, 3, $offx + $this->l, $offy + $this->b, $this->s, $c1);
	}
}


$fglist = array();
$midlist = array();
$bglist = array();
$poslist = array();
foreach (preg_split("/\r\n|\r|\n/", stream_get_contents(STDIN)) as $line) {
	list($line) = explode("#", $line, 2);
	$line = trim($line);
	
	if (preg_match('!/[*][^ \t]+[ \t]+([-_A-Za-z]+)([0-9]+)(.*)!', $line, $a)) {
		$num = (int)$a[2];
		$par = trim($a[3]);
		$fglist = array();
		$midlist = array();
		$bglist = array();
		$poslist = array();
		switch ($a[1]) {
			case	"sip":
				$midlist[] = new boxobject(0, 0, 16 * $num, 16);
				$fglist[] = new stringobject(8, 4, $par);
				for ($i=0; $i<$num; $i++)
					$poslist[] = array(8 + 16 * $i, 8);
				break;
			case	"dip":
				$midlist[] = new boxobject(0, 0, 8 * $num, 48);
				$fglist[] = new stringobject(8, 18, $par);
				for ($i=0; $i<$num; $i+=2)
					$poslist[] = array(8 + 8 * $i, 40);
				while (($i -= 2) >= 0)
					$poslist[] = array(8 + 8 * $i, 8);
				break;
			case	"qfp":
				$midlist[] = new boxobject(0, 0, $w = 4 * $num, $h = 4 * $num);
				$fglist[] = new stringobject(8, 4 * $num - 24, $par);
				for ($i=0; $i<$num/4; $i++)
					$poslist[] = array(8 + 16 * $i, $h - 8);
				for ($i=0; $i<$num/4; $i++)
					$poslist[] = array($w - 8, $h - 8 - 16 * $i);
				for ($i=0; $i<$num/4; $i++)
					$poslist[] = array($w - 8 - 16 * $i, 8);
				for ($i=0; $i<$num/4; $i++)
					$poslist[] = array(8, 8 + 16 * $i);
				break;
		}
		continue;
	}
	if ($line == "*/") {
		if (count($midlist) == 0)
			continue;
		$l = $t = $r = $b = null;
		foreach (array($bglist, $midlist, $fglist) as $list)
			foreach ($list as $obj) {
				if ($l === null) {
					$l = $obj->l;
					$r = $obj->r;
					$t = $obj->t;
					$b = $obj->b;
				}
				$l = min($l, $obj->l);
				$r = max($r, $obj->r);
				$t = min($t, $obj->t);
				$b = max($b, $obj->b);
			}
		$g0 = imagecreate($w = $r - $l + 32, $h = $b - $t + 32);
#		$c0 = imagecolorresolve($g0, 255, 255, 255);
		$c0 = imagecolorresolve($g0, 224, 224, 224);
		imagefilledrectangle($g0, 0, 0, $w, $h, $c0);
		foreach (array($bglist, $midlist, $fglist) as $list)
			foreach ($list as $obj)
				$obj->draw($g0, 16 - $l, 16 - $t);
#		header("Content-Type: image/png");
		imagepng($g0, $tmpfn = dirname(@$argv[0])."/tmp/".getmypid());
		imagedestroy($g0);
		print '<img src="data:image/png;base64,'.base64_encode(file_get_contents($tmpfn)).'">'."\n";
		unlink($tmpfn);
		die();
	}
	if (count($midlist) == 0)
		continue;
	if (count($poslist) == 0)
		continue;
	list($x, $y) = array_shift($poslist);
	$vx = $vy = 0;
	$stack = array();
	while (($c = substr($line, 0, 1)) != "") {
		$line = substr($line, 1);
		$x0 = $x;
		$y0 = $y;
		switch ($c) {
			default:
				continue 2;
			case	"2":
				$bglist[] = new lineobject($x0, $y0, $x += ($vx = 0) * 16, $y += ($vy = 1) * 16);
				continue 2;
			case	"4":
				$bglist[] = new lineobject($x0, $y0, $x += ($vx = -1) * 16, $y += ($vy = 0) * 16);
				continue 2;
			case	"6":
				$bglist[] = new lineobject($x0, $y0, $x += ($vx = 1) * 16, $y += ($vy = 0) * 16);
				continue 2;
			case	"8":
				$bglist[] = new lineobject($x0, $y0, $x += ($vx = 0) * 16, $y += ($vy = -1) * 16);
				continue 2;
			case	"(":
				$bglist[] = new dotobject($x, $y);
				$stack[] = array($x, $y, $x0, $y0, $vx, $vy);
				continue 2;
			case	")":
				if (count($stack) > 0)
					list($x, $y, $x0, $y0, $vx, $vy) = array_pop($stack);
				continue 2;
			case	"<":
				list($s, $line) = explode(">", $line, 2);
				$color = 0;
				if (substr($s, 0, 1) == "<") {
					$color = 1;
					$s = substr($s, 1);
				}
				$len = 64;
				$midlist[] = new hexobject($x0, $y0, $vx * $len, $vy * $len, $s, $color);
				$x += $vx * $len;
				$y += $vy * $len;
				continue 2;
			case	"{":
				list($s, $line) = explode("}", $line, 2);
				$c = substr($s, 0, 1);
				$s = substr($s, 1);
				
				if ($s == "")
					;
				else if (($vx))
					$fglist[] = new stringobject($x + $vx * 8 - 8, $y + 4, $s);
				else
					$fglist[] = new vstringobject($x + 4, $y + $vy * 8 - 8, $s);
				
				$list = array();
				switch ($c) {
					case	"r":
						$list[] = array(0, 0, 4, 0);
						$list[] = array(4, 0, 6, -4);
						$list[] = array(6, -4, 10, 4);
						$list[] = array(10, 4, 12, 0);
						$list[] = array(12, 0, 16, 0);
						break;
					case	"c":
						$list[] = array(0, 0, 6, 0);
						$list[] = array(6, -4, 6, 4);
						$list[] = array(10, -4, 10, 4);
						$list[] = array(10, 0, 16, 0);
						break;
					case	"d":
						$list[] = array(0, 0, 4, 0);
						$list[] = array(4, -4, 4, 4);
						$list[] = array(4, -4, 12, 0);
						$list[] = array(4, 4, 12, 0);
						$list[] = array(12, -4, 12, 4);
						$list[] = array(12, 0, 16, 0);
						break;
					case	"D":
						$list[] = array(0, 0, 4, 0);
						$list[] = array(4, -4, 4, 4);
						$list[] = array(4, 0, 12, -4);
						$list[] = array(4, 0, 12, 4);
						$list[] = array(12, -4, 12, 4);
						$list[] = array(12, 0, 16, 0);
						break;
				}
				$ex0 = $x;
				$ey0 = $y;
				foreach ($list as $a) {
					list($sx, $sy, $ex, $ey) = $a;
					$sx0 = $sx * $vx - $sy * $vy + $x;
					$sy0 = $sx * $vy + $sy * $vx + $y;
					$ex0 = $ex * $vx - $ey * $vy + $x;
					$ey0 = $ex * $vy + $ey * $vx + $y;
					$bglist[] = new lineobject($sx0, $sy0, $ex0, $ey0);
				}
				$x = $ex0;
				$y = $ey0;
				continue 2;
		}
	}
}


