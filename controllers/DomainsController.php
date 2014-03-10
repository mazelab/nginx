<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_DomainsController extends Zend_Controller_Action
{
    
    /**
     * @var Core_Model_ValueObject_Client
     */
    protected $_client;
    
    public function init()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        if(key_exists('_id', $identity) &&
                ($client = Core_Model_DiFactory::getClientManager()->getClient($identity['_id']))) {
            $this->_client = $client;
        }
    }

    public function indexAction()
    {
        if(!$this->_client) {
            return $this->noClient();
        }
        
        $domainManager = MazelabNginx_Model_DiFactory::getDomainManager();
        
        $this->view->domains = $domainManager->getDomainsAsArray($this->_client->getId());
    }
    
    public function noClient()
    {
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->goToRoute(array(), 'index');
        return;
    }
    
}

