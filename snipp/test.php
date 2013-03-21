class Course extends SimpleORMap
{
    function __construct($id = null)
    {
        $this->db_table = 'seminare';
        $this->default_values['admission_endtime'] = -1;
        $this->alias_fields['number'] = 'veranstaltungsnummer';
        $this->additional_fields['end_time']['get'] = function($course) {
            return $course->duration_time == -1 ? -1 : $course->start_time + $course->duration_time;
        };
        parent::__construct($id);
    }
}
