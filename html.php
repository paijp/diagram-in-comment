<?php

list($body) = explode("*/", stream_get_contents(STDIN), 2);
@list(, $s) = explode("\n", $body, 2);

print $s;
