$course = Course::find($id);
$course->members[0]->user_id;
$course->members->val('user_id');
//findBy() liefert Collection
$course->members->findBy('status', 'dozent')->pluck('user_id');
$course->members->findBy('status', 'dozent')->toGroupedArray('user_id', 'username vorname nachname');
unset($course->members[0]); //geht zwar
$course->member->unsetBy('username', 'noack'); //sinnvoller
$course->member->getDeleted();
$course->member->refresh();

$courses_collection = SimpleOrMapCollection::createFromArray(Course::findbySQL("name LIKE ?", array('Test%')));
$courses_collection->each(function($c) {$c->visible = 0; $c->store();});
$courses = $courses_collection->pluck('name');
