<?php
class A
{
    public $foo = 'foo';
}
class B
{
    public $foo = 'foobar';
}

$a = new A();
$b = $a;
var_Dump($b->foo);
$b = new B();
var_Dump($a->foo);

$a = new A();
$b =& $a;
var_Dump($b->foo);
$b = new B();
var_Dump($a->foo);
