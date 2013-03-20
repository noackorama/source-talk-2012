$id = 1;
$course = new Course($id);
if (!$course->isNew()) {
    echo $course->name;
} else {
    //durch Aufruf über Konstruktor wird id gesetzt
    //$course->id ist 1
}

$course = Course::find($id);
if (!is_null($course)) {
    echo $course->name;
}

if (!Course::exists($id)) {
    $course = new Course($id);
    $course->store
}
