<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Bootstrap extends Zend_Application_Module_Bootstrap
{

    /**
     * init the acl of your module
     */
    protected function _initAcl()
    {
        $aclPath = __DIR__ . '/configs/acl.ini';

        if (file_exists($aclPath)) {
            // use own acl builder
            $acl = Zend_Registry::getInstance()->get('MazeLib_Acl_Builder');
            $acl->addConfig(new Zend_Config_Ini($aclPath));
        }
    }
    
    protected function _initNavigation()
    {
        if(!($identity = Zend_Auth::getInstance()->getIdentity()) || !key_exists('group', $identity)) {
            return false;
        }
        
        $bootstrap = $this->getApplication();
        $group = $identity['group'];

        $view = $bootstrap->bootstrap('layout')->getResource('view');
        if($group === Core_Model_AdminManager::GROUP_ADMIN) {
            $naviPath = __DIR__ . '/configs/navigation/admin.ini';

        } elseif($group === Core_Model_ClientManager::GROUP_CLIENT){ 
            $naviPath = __DIR__ . '/configs/navigation/client.ini';
        }

        if (isset($naviPath) && file_exists($naviPath)) {
            $config = new Zend_Config_Ini($naviPath);
            if($group === Core_Model_AdminManager::GROUP_ADMIN) {
                $view->navigation()->getContainer()->findBy('resource', 'dashboard')->addPages($config);
            } elseif($group === Core_Model_ClientManager::GROUP_CLIENT) {
                $view->navigation()->addPages($config);
            }
        }
    }
    
    protected function _initTranslate()
    {
        $tranlations = $aclPath = __DIR__ . '/data/locales/';
        
        if (file_exists($tranlations) && Zend_Registry::getInstance()->isRegistered("Zend_Translate")){
            $translate = Zend_Registry::getInstance()->get("Zend_Translate");
            $translate->getAdapter()->addTranslation($tranlations);
        }
    }
    
    protected function _initPlugins()
    {
        $bootstrap = $this->getApplication();
        $bootstrap->bootstrap('FrontController');
        $front = $bootstrap->getResource('FrontController');
        
        $front->registerPlugin(new MazelabNginx_Model_Plugins_Navigation)
              ->registerPlugin(new MazelabNginx_Model_Plugins_InitLayout)
              ->registerPlugin(new MazelabNginx_Model_Plugins_Events());
    }
    
    /**
     * init the routes of your module
     */
    public function _initRouter(array $options = null)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $routingFile = $aclPath = __DIR__ . '/configs/routes.ini';

        if (file_exists($routingFile)) {
            $router->addConfig(new Zend_Config_Ini($routingFile, $this->getEnvironment()), 'routes');
        }
        
        return $router;
    }
    
}
