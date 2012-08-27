<?php
class obj implements ArrayAccess,Countable,IteratorAggregate
{
    private $container = array();
    public function __construct()
   {
        $this->container = array(
            "one"   => 1,
            "two"   => 2,
            "three" => 3,
        );
    }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
    public function count()
    {
        return count($this->container);
    }
    public function getIterator()
    {
        return new ArrayIterator($this->container);
    }
}

$obj = new obj;

var_dump(isset($obj["two"]));
var_dump($obj["two"]);
unset($obj["two"]);
var_dump(isset($obj["two"]));
$obj["two"] = "A value";
var_dump($obj["two"]);
$obj[] = 'Append 1';
$obj[] = 'Append 2';
$obj[] = 'Append 3';
var_dump(count($obj));
foreach($obj as $value) {
    var_Dump($value);
}
var_Dump(array_map('strtolower', $obj)); //damn
var_Dump(array_map('strtolower', (array)$obj)); //damn damn
var_Dump(array_map('strtolower', iterator_to_array($obj))); //hmpf

?>
