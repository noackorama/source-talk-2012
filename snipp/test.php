$course = Course::find($id);
$course->name = 'Testveranstaltung 2';
$changed = $course->store();

$data = array('name' => 'Testveranstaltung 2', 'nummer' => '12345');
$course->setData($data);

$data['members'][] = array('status' => 'dozent', 'id' => 5);

$course = Course::import($data);
$course->store(); 
//speichert neuen Kurs mit einem zugewiesenen Dozenten
