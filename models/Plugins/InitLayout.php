<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Plugins_InitLayout extends Zend_Controller_Plugin_Abstract
{
    /**
     * Called after Zend_Controller_Router exits.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function routeShutdown($request)
    {
        if ($request->getModuleName() == "mazelab-nginx"){
            $view = Zend_Layout::getMvcInstance()->getView();
            $view->headLink()->prependStylesheet("/module/mazelab/nginx/css/default.css");
            
            if(($identity = Zend_Auth::getInstance()->getIdentity()) && key_exists('group', $identity) &&
                    $identity['group'] == Core_Model_ClientManager::GROUP_CLIENT) {
                $view->headLink()->prependStylesheet("/module/mazelab/nginx/css/client.css");
            }
        }
    }

}