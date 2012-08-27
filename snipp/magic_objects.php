<?php
class Magic {

    private function runTest()
    {
        echo "Rufe die private Objektmethode 'runTest' "
             . implode(', ', func_get_args()). "\n";
    }

    public function __construct()
    {
        $this->runTest('aus dem Objekt auf');
    }

    public function __get($name)
    {
        return "Der Wert von '$name'";
    }

    public function __call($name, $arguments)
    {
        // Achtung: Der Wert von $name beachtet die Groﬂ-/Kleinschreibung
        echo "Rufe die Objektmethode '$name' "
             . implode(', ', $arguments). "\n";
    }

    /**  Seit PHP 5.3.0  */
    public static function __callStatic($name, $arguments)
    {
        // Achtung: Der Wert von $name beachtet die Groﬂ-/Kleinschreibung
        echo "Rufe die statische Methode '$name' "
             . implode(', ', $arguments). "\n";
        echo "in der Klasse: " . __CLASS__ . "\n";
        echo "aufgerufen in der Klasse: " . get_called_class() . "\n";
    }
}
class extendedMagic extends Magic
{
}
echo '<pre>';
$obj = new Magic;
echo $obj->magic . "\n";
$obj->runTest('eines Objektes auf');
Magic::runTest('aus statischem Kontext auf');
extendedMagic::runTest('aus anderem statischem Kontext auf');

