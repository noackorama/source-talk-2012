$course = Course::find($id);
$course->delete();
if ($course->isDeleted()) {
  $course->setNew();
  $course->store();
}
