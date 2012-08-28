<?
require_once 'application.php';

class ShowController extends ApplicationController {

    function before_filter(&$action, &$args) {
        if(!$GLOBALS['perm']->have_perm('root')) throw new Studip_AccessDeniedException('Keine Berechtigung');
        parent::before_filter($action, $args);
    }

    function index_action() {
    }
}