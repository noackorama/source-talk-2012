
        $this->has_many = array(
                'members' => array(
                        'class_name' => 'CourseMember',
                        'on_delete' => 'delete', 'on_store' => 'store'),
        );
        $this->belongs_to = array(
                'start_semester' => array(
                        'class_name' => 'Semester',
                        'foreign_key' => 'start_time',
                        'assoc_func' => 'findByTimestamp',
                        'assoc_foreign_key' => 'beginn'),
                'end_semester' => array(
                        'class_name' => 'Semester',
                        'foreign_key' => 'end_time',
                        'assoc_func' => 'findByTimestamp',
                        'assoc_foreign_key' => 'beginn'),
                'home_institut' => array(
                        'class_name' => 'Institute',
                        'foreign_key' => 'institut_id',
                        'assoc_func' => 'find')
        );
        $this->has_and_belongs_to_many = array(
                'study_areas' => array(
                        'class_name' => 'StudipStudyArea',
                        'thru_table' => 'seminar_sem_tree',
                        'on_delete' => 'delete', 'on_store' => 'store'),
                'institutes' => array(
                        'class_name' => 'Institute',
                        'thru_table' => 'seminar_inst',
                        'on_delete' => 'delete', 'on_store' => 'store'));
