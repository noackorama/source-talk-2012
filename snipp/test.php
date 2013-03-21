<?php

$courses = Course::findBySql('start_time = ? ORDER BY Name', array($semester_start));

$courses = Course::findBystart_time($semester_start, 'ORDER BY Name');

$courses = Course::findMany($course_ids);

$course_names = Course::findEachMany(function($s) {return array($s->name, $s->nummer);}, $course_ids);
