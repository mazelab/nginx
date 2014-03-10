<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_ConfigController extends Zend_Controller_Action
{

    /**
     * use zend context switch for ajax requests
     */
    public function init() {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('clientconfig', array('json', 'html'))
                    ->addActionContext('nodeconfig', array('json', 'html'))
                    ->addActionContext('domainconfig', array('json', 'html'))
                    ->initContext();
    }
    
    public function clientconfigAction()
    {
    }
    
    public function domainconfigAction()
    {
    }
    
    public function nodeconfigAction()
    {
    }
    
    /**
     * sets forbidden http code in response object and disable rendering
     */
    public function setForbiddenStatus()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        $this->getResponse()->setHttpResponseCode(403);
    }
    
}

