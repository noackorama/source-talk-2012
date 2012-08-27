<?php
/*
 * seminar_webservice.php - Provides webservices for infos about
 *     Seminars
 *
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

require_once('lib/webservices/api/studip_seminar.php');

class Studip_Booked_Room extends Studip_Ws_Struct {

    function __toString() {
        return get_class($this);
    }

  function init() {
    Studip_Booked_Room::add_element('start_time', 'string');
    Studip_Booked_Room::add_element('end_time', 'string');
    Studip_Booked_Room::add_element('room', 'string');
    Studip_Booked_Room::add_element('room_id', 'string');
    Studip_Booked_Room::add_element('lecture_title', 'string');
    Studip_Booked_Room::add_element('lecture_description', 'string');
    Studip_Booked_Room::add_element('lecture_home_institute', 'string');
    Studip_Booked_Room::add_element('lecturer_name', 'string');
    Studip_Booked_Room::add_element('lecturer_title_front', 'string');
    Studip_Booked_Room::add_element('lecturer_title_rear', 'string');
  }
}

class BookedRoomsService extends AccessControlledService
{

    function BookedRoomsService()
    {
      $this->add_api_method('get_booked_rooms',
                          array('string', 'string', 'string'),
                          array('Studip_Booked_Room'),
                          'returns list of booked rooms for given timespan');
    }

    function get_booked_rooms_action($api_key, $start_timestamp, $end_timestamp)
    {
        $ret = array();
        if (!$start_timestamp) $start_timestamp = strtotime('today');
        if (!$end_timestamp) $end_timestamp = strtotime("+2 weeks", $start_timestamp);
        $db = DBManager::get();
        $rs = $db->query(sprintf("
        SELECT begin, end, s.Name AS lecture_title, s.Beschreibung, i.Name AS lecture_home_institute, r.resource_id, r.name AS room, GROUP_CONCAT( CONCAT_WS( '|', auth_user_md5.Vorname, auth_user_md5.Nachname, user_info.title_front, user_info.title_rear )
        ORDER BY seminar_user.position
        SEPARATOR ';' ) AS lecturer_name
        FROM resources_assign ra
        INNER JOIN resources_objects r ON ra.resource_id = r.resource_id
        INNER JOIN termine t ON termin_id = assign_user_id
        INNER JOIN seminare s ON range_id = Seminar_id
        INNER JOIN Institute i ON i.Institut_id = s.Institut_id
        LEFT JOIN seminar_user ON s.seminar_id = seminar_user.seminar_id
        AND seminar_user.status = 'dozent'
        LEFT JOIN auth_user_md5 ON seminar_user.user_id = auth_user_md5.user_id
        LEFT JOIN user_info ON user_info.user_id = auth_user_md5.user_id
        WHERE begin
        BETWEEN %s
        AND %s
        GROUP BY assign_id", $db->quote($start_timestamp), $db->quote($end_timestamp)));
        while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
            $lecturers = explode(';', $row['lecturer_name']);
            list($vorname,$nachname,$titel1,$titel2) = explode('|', $lecturers[0]);
            $room = new Studip_Booked_Room();
            $room->start_time = $row['begin'];
            $room->end_time = $row['end'];
            $room->room = $row['room'];
            $room->room_id = $row['resource_id'];
            $room->lecture_title = $row['lecture_title'];
            $room->lecture_home_institute = $row['lecture_home_institute'];
            $room->lecture_description = $row['Beschreibung'];
            $room->lecturer_title_front = $titel1;
            $room->lecturer_title_rear = $titel2;
            $room->lecturer_name = $vorname . ' ' . $nachname;
            $ret[] = $room;
        }

        return $ret;
    }
}
