<?php
require 'booked_rooms_webservice.php';

class BookedRoomsWebservice extends StudipPlugin implements WebServicePlugin
{
  function getWebServices()
  {
      return array("BookedRoomsService");
  }
}
?>
