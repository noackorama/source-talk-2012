class seminare extends SimpleORMap
{
}
$s = new Seminare($id);
echo $s->getValue('name');
//ab 2.0
echo $s->name;
echo $s['name'];
