<?php
require_once 'classes/APIPlugin.php';

/**
 * RestipPlugin.class.php
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @version 0 alpha
 */
class RestipPlugin extends StudIPPlugin implements SystemPlugin, HomepagePlugin
{
    /**
     *
     **/
    public function __construct()
    {
        parent::__construct();
        
        $config = Config::getInstance();
        if (!$config['OAUTH_ENABLED']) {
            return;
        }

        if ($GLOBALS['perm']->have_perm('autor')) {
            $navigation = new AutoNavigation(_('Apps'));
            $navigation->setURL(PluginEngine::getLink($this, array(), 'user'));
            Navigation::addItem('/links/settings/oauth', $navigation);
        }

        if ($GLOBALS['perm']->have_perm('root')) {
            $navigation = new AutoNavigation(_('OAuth'));
            $navigation->setURL(PluginEngine::getLink($this, array(), 'admin'));
            Navigation::addItem('/admin/config/oauth', $navigation);
        }
    }

    /**
     *
     **/
    public function initialize()
    {
        require_once 'config_plugin.inc.php';
        require_once 'bootstrap.php';

        $this->addStylesheet('assets/form-settings.less');
        $this->addStylesheet('assets/oauth.less');
        PageLayout::addScript($this->getPluginURL() . '/assets/oauth.js');
    }

    /**
     *
     **/
    public function perform ($unconsumed_path)
    {
        $dispatcher = new Trails_Dispatcher(
            $this->getPluginPath() . DIRECTORY_SEPARATOR . 'app',
            rtrim(PluginEngine::getLink($this, array(), null), '/'),
            'admin'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    /**
     * Fake homepage plugin to ensure plugin gets loaded first
     **/
    public function getHomepageTemplate($user_id)
    {
        return null;
    }
    
    /**
     *
     **/
    public static function onEnable($pluginId)
    {
        # TODO performance - use cache on success ?
        $role_persistence = new RolePersistence;
        $plugin_roles     = $role_persistence->getAssignedPluginRoles($pluginId);
        $role_names       = array_map(function ($role) { return $role->getRolename(); }, $plugin_roles);

        if (!in_array('Nobody', $role_names)) {
            $message   = _('Das OAuth-Plugin ist aktiviert, aber nicht f�r die Rolle "Nobody" freigegeben.');
            $details   = array();
            $details[] = _('Dies behindert die Kommunikation externer Applikationen mit dem System.');
            $details[] = sprintf(_('Klicken Sie <a href="%s">hier</a>, um die Rollenzuweisung zu bearbeiten.'),
                               URLHelper::getLink('dispatch.php/admin/role/assign_plugin_role/' . $pluginId));
            PageLayout::postMessage(Messagebox::info($message, $details));
        }
    }
}
