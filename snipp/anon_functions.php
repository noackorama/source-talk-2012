<?php

$a = array(1,3,5,7);
$filter = create_function('$v', 'return $v > 3;');
var_dump(array_filter($a, $filter));
$filter = function($v){ return $v > 3;};
var_dump(array_filter($a, $filter));

$newfilter =
function($i) {
    return function($v) use ($i) {
        return $v > $i;
        };
};

var_dump(array_filter($a, $newfilter(5)));
$class = new ReflectionClass($newfilter);
echo $class->__toString();
