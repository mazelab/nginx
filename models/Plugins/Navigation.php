<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Plugins_Navigation extends Zend_Controller_Plugin_Abstract
{
    
    /**
     * @var array
     */
    protected $_identity;
    
    /**
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->_identity = Zend_Auth::getInstance()->getIdentity();

        if ($this->_identity['group'] == Core_Model_UserManager::GROUP_CLIENT) {
            $this->initClient();
        }
    }
    
    /**
     * custom behavior for client mail navigation
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function initClient()
    {
        $client = Core_Model_DiFactory::getClientManager()->getClient($this->_identity['_id']);
        
        if(($client && !$client->hasService('nginx')) || !MazelabNginx_Model_DiFactory::getDomainManager()->getDomains($this->_identity['_id'])) {
            $view = Zend_Layout::getMvcInstance()->getView();
            
            $emailNavi = $view->navigation()->findById('mazelab-nginx_index');
            
            $view->navigation()->removePage($emailNavi);
        }
    }

}

