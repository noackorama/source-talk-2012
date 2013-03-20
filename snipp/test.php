$course = new Course();
$course->name = 'Neue Veranstaltung';
$course->store();

//alternativ
$course->setData(array('name' => 'Neue Veranstaltung'));
$course->store();

//oder
$course = Course::create(array('name' => 'Neue Veranstaltung'));
