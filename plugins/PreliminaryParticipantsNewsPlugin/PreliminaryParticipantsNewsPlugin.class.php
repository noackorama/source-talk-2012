<?php
require_once 'lib/meine_seminare_func.inc.php';

class PreliminaryParticipantsNewsPlugin extends StudIPPlugin implements SystemPlugin
{
    function __construct()
    {

        if (!$GLOBALS['perm']->have_perm('admin') && in_array(basename($_SERVER['PHP_SELF']), array('meine_seminare.php', 'details.php'))) {
            $user_id = $GLOBALS['user']->id;
            $db = DBManager::get();
            $my_obj = array();
            $db->exec("CREATE TEMPORARY TABLE IF NOT EXISTS myobj_$user_id ENGINE = MEMORY SELECT seminar_id AS object_id
                        FROM admission_seminar_user
                        WHERE user_id = '$user_id'
                        AND status = 'accepted'");
            $rs = $db->query(get_obj_clause('news_range a {ON_CLAUSE} LEFT JOIN news nw ON(a.news_id=nw.news_id AND UNIX_TIMESTAMP() BETWEEN date AND (date+expire))','range_id','nw.news_id',"(chdate > IFNULL(b.visitdate,0) AND nw.user_id !='$user_id')",'news',false,false,'a.news_id'));
            while($row = $rs->fetch(PDO::FETCH_ASSOC)) {
                if($row["count"]) {
                    $object_id = $row['object_id'];
                    $my_obj[$object_id]['id'] = $object_id;
                    $my_obj[$object_id]["neuenews"] = $row["neue"];
                    $my_obj[$object_id]["news"] = $row["count"];
                    if ($row["neue"]) {
                        $my_obj[$object_id]["image"] = Assets::img('icons/16/red/new/breaking-news.png', array('title' => studip_utf8encode(sprintf(_('%s Ankündigungen, %s neue'), $row["count"], $row["neue"]))));
                    } else {
                        $my_obj[$object_id]["image"] = Assets::img('icons/16/grey/breaking-news.png', array('title' => studip_utf8encode(sprintf(_('%s Ankündigungen'), $row["count"]))));
                    }
                }
            }
            $db->exec("DROP TABLE IF EXISTS myobj_" . $user_id);
            if (basename($_SERVER['PHP_SELF']) == 'meine_seminare.php') {
                $courses = json_encode($my_obj);
                $current_course = 'null';
            } else if (in_array(Request::option('sem_id'), array_keys($my_obj))) {
                $current_course = json_encode($my_obj[Request::option('sem_id')]);
                $courses = '{}';
                $titel = _("Ankündigungen");
                $content_box = addcslashes($this->render_news_box(Request::option('sem_id')),"'\n\r");
            }
            $script = <<<EOT
STUDIP.PreliminaryParticipantsNews = {
  openclose: function (id, admin_link) {
    if (jQuery("#news_item_" + id + "_content").is(':visible')) {
      STUDIP.PreliminaryParticipantsNews.close(id);
    } else {
      STUDIP.PreliminaryParticipantsNews.open(id, admin_link);
    }
  },

  open: function (id, admin_link) {
    jQuery("#news_item_" + id + "_content").load(
      STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/PreliminaryParticipantsNewsPlugin/get_news/' + id,
      {admin_link: admin_link},
      function () {
        jQuery("#news_item_" + id + "_content").slideDown(400);
        jQuery("#news_item_" + id + " .printhead2 img")
            .attr('src', STUDIP.ASSETS_URL + "images/forumgraurunt2.png");
        jQuery("#news_item_" + id + " .printhead2")
            .removeClass("printhead2")
            .addClass("printhead3");
        jQuery("#news_item_" + id + " .printhead b").css("font-weight", "bold");
        jQuery("#news_item_" + id + " .printhead a.tree").css("font-weight", "bold");
      });
  },

  close: function (id) {
    jQuery("#news_item_" + id + "_content").slideUp(400);
    jQuery("#news_item_" + id + " .printhead3 img")
        .attr('src', STUDIP.ASSETS_URL + "images/forumgrau2.png");
    jQuery("#news_item_" + id + " .printhead3")
        .removeClass("printhead3")
        .addClass("printhead2");
    jQuery("#news_item_" + id + " .printhead b").css("font-weight", "normal");
    jQuery("#news_item_" + id + " .printhead a.tree").css("font-weight", "normal");
  },

  showNews: function(id) {
  if (jQuery('#Dialogbox_' + id).length == 0) {
     jQuery.getJSON(STUDIP.ABSOLUTE_URI_STUDIP + 'plugins.php/PreliminaryParticipantsNewsPlugin/get_news_dialog/' + id, function(data) {
        jQuery('<div id="Dialogbox_' + id + '">' + data.content + '</div>').dialog({
            show: '',
            hide: 'scale',
            title: data.title,
            draggable: false,
            modal: false,
            width: Math.min(1000, jQuery(window).width() - 64),
            height: 'auto',
            maxHeight: jQuery(window).height(),
            close: function(){jQuery(this).remove();}
          });
          });
      }
  }
};
jQuery('document').ready(function(){
    jQuery.each($courses, function(id,data) {
       jQuery('a[href*="sem_id=' + id + '"]').after('<div onclick="STUDIP.PreliminaryParticipantsNews.showNews(\''+id+'\');" style="cursor:pointer; float:right;">' + data.image + '</div>');
    });
    var cc = $current_course;
    if (cc) {
        //jQuery('div.topic').before('<div onclick="STUDIP.PreliminaryParticipantsNews.showNews(\''+cc.id+'\');" style="cursor:pointer;font-weight:bold;text-align:center">' + cc.image + '&nbsp; $titel</div>');
        jQuery('div.topic').before('$content_box');
    }
});
EOT;
            if (count($my_obj) &&
                (basename($_SERVER['PHP_SELF']) == 'meine_seminare.php'
                    || in_array(Request::option('sem_id'), array_keys($my_obj))  )
            ) {
                PageLayout::addHeadElement('script', array('type'=>'text/javascript'), $script);
            }
        }
    }

    function render_news_box($seminar_id)
    {
        require_once 'lib/showNews.inc.php';

        $news = StudipNews::GetNewsByRange($seminar_id, true);
        ob_start();
        foreach ($news as $id => $news_item) {
            echo '<div id="news_item_'.$id.'">';
            echo str_replace('STUDIP.News.openclose', 'STUDIP.PreliminaryParticipantsNews.openclose', show_news_item($news_item, array(), false, ''));
            echo '</div>';
        }
        $template = $GLOBALS['template_factory']->open('shared/string.php');
        $template->set_layout('shared/index_box.php');
        $template->set_attribute('content', ob_get_clean());
        $template->set_attribute('title', _("Ankündigungen"));
        $template->set_attribute('icon_url',Assets::image_path('icons/16/white/breaking-news.png'));
        return $template->render();
    }

    function get_news_dialog_action($id = null)
    {

        require_once 'lib/showNews.inc.php';

        if (!$id || preg_match('/[^\\w,-]/', $id)) {
            throw new Exception('wrong parameter');
        }
        $news = StudipNews::GetNewsByRange($id, true);
        $titel = _("Ankündigungen") . ': ' . Seminar::getInstance($id)->name;
        ob_start();
        foreach ($news as $id => $news_item) {
            $news_item['open'] = ($id == $cmd_data["nopen"]);
            echo '<div id="news_item_'.$id.'">';
            echo str_replace('STUDIP.News.openclose', 'STUDIP.PreliminaryParticipantsNews.openclose', show_news_item($news_item, array(), false, ''));
            echo '</div>';
        }
        $content = ob_get_clean();
        header('Content-Type:application/json;charset=utf-8');
        echo json_encode(array('content' => studip_utf8encode($content), 'title' => studip_utf8encode($titel)));
    }

    function get_news_action($id = null)
    {

        require_once 'lib/showNews.inc.php';

        if (!$id || preg_match('/[^\\w,-]/', $id)) {
            throw new Exception('wrong parameter');
        }

        $news = StudipNews::find($id);

        if (is_null($news)) {
            throw new Exception('wrong parameter');
        }
        $allowed = DBManager::get()->query("SELECT seminar_id AS object_id
                        FROM admission_seminar_user
                        WHERE user_id = '{$GLOBALS['user']->id}'
                        AND status = 'accepted'")->fetchAll(PDO::FETCH_COLUMN);

        $permitted = $show_admin = false;
        foreach ($news->getRanges() as $range) {
            if (in_array($range, $allowed)) {
                $permitted = true;
            }
        }
        if (!$permitted) {
            throw new AccessDeniedException();
        }

        $newscontent = $news->toArray();
        // use the same logic here as in show_news_item()
        if ($newscontent['user_id'] != $GLOBALS['auth']->auth['uid']) {
            object_add_view($id);
        }
        $newscontent['allow_comments'] = false;
        object_set_visit($id, "news");
        $content = show_news_item_content($newscontent,
                                          array(),
                                          $show_admin,
                                          Request::get('admin_link')
                                          );
        echo studip_utf8encode($content);
    }
}
