<?php
$a = new SplStack; 
for($i = 0; $i < $argv[1]; $i++) {
    $a[] = $i;
}
for($i = 0; $i < $argv[1]; $i++) {
    $a->pop();
}
