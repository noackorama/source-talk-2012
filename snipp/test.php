$a = array(1,3,5,7);
$filter = create_function('$v', 'return $v > 3;');
var_dump(array_filter($a, $filter));
$filter = function($v){ return $v > 3;};
var_dump(array_filter($a, $filter));
