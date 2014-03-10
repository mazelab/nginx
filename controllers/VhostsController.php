<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_VhostsController extends Zend_Controller_Action
{

    /**
     * message when vhost could not be found
     */
    CONST MESSAGE_VHOST_NOT_FOUND = 'Vhost not found';

    /**
     * message when vhost diff could not be resolved
     */
    CONST MESSAGE_VHOST_RESOLVE_FAILED = 'Could not resolve differences of vhost';
    
    /**
     * @var mixed | null
     */
    protected $_identity;
    
    public function init()
    {
        $this->_identity = Zend_Auth::getInstance()->getIdentity();
        
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('edit', 'json')
                    ->addActionContext('activate', 'json')
                    ->addActionContext('deactivate', 'json')
                    ->addActionContext('delete', 'json')
                    ->addActionContext('indexadmin', 'html')
                    ->addActionContext('indexclient', 'html')
                    ->initContext();
        
        // set view messages from MessageManager
        $this->_helper->getHelper("SetDefaultViewVars");
    }
    
    public function activateAction()
    {
        $this->view->result = MazelabNginx_Model_DiFactory::getVhostManager()
                ->enableVhost($this->getParam('vhostId'));
    }
    
    public function addAction()
    {
        $vhostManager = MazelabNginx_Model_DiFactory::getVhostManager();
        $form = new MazelabNginx_Form_AddVhost();
        $form->initNodeSelect()->initDomainSelect();
        
        if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) &&
                $vhostManager->addVhost($form->getValues())) {
            $redirector = $this->_helper->getHelper('Redirector');
            return $redirector->goToRoute(array(), 'mazelab-nginx_vhosts');
        }
        
        $this->view->form = $form;
    }
    
    public function deleteAction()
    {
        $this->view->result = MazelabNginx_Model_DiFactory::getVhostManager()
                ->setVhostDeleteFlag($this->getParam('vhostId'));
    }
    
    public function detailAction()
    {
        $vhostManager = MazelabNginx_Model_DiFactory::getVhostManager();
        $vhostId = $this->getParam('vhostId');
        
        if(!$vhostManager->getVhost($vhostId)) {
            Core_Model_DiFactory::getMessageManager()
                    ->addError(self::MESSAGE_VHOST_NOT_FOUND);
            return $this->_forward('index');
        }

        $this->view->vhost = $vhostManager->getVhostAsArray($vhostId);
    }
    
    public function deactivateAction()
    {
        $this->view->result = MazelabNginx_Model_DiFactory::getVhostManager()
                ->disableVhost($this->getParam('vhostId'));
    }
    
    public function editAction()
    {
        $vhostManager = MazelabNginx_Model_DiFactory::getVhostManager();
        $vhostId = $this->getParam('vhostId');
        
        if(!($vhost = $vhostManager->getVhostAsArray($vhostId))) {
            Core_Model_DiFactory::getMessageManager()
                    ->addError(self::MESSAGE_VHOST_NOT_FOUND);
            return $this->_forward('index');
        }
        
        if($vhostManager->hasManuellDiff($vhostId)) {
            $redirector = $this->_helper->getHelper('Redirector');
            return $redirector->gotoRoute(array($vhostId), 'mazelab-nginx_diffVhost');
        }
        
        $form = new MazelabNginx_Form_EditVhost();
        $form->initNodeSelect()->setNodeValidators($vhost['label']);
        
        if($this->getRequest()->isPost() && ($values = $form->getValidValues($this->getRequest()->getPost()))) {
            $this->view->result = $vhostManager->updateVhost($vhostId, $values);
        }
        
        $this->view->vhost = $vhost;
        $this->view->formErrors = $form->getMessages(null, true);
        $this->view->form = $form->setDefaults($vhost)->setDomainInput();
    }
    
    public function diffAction()
    {
        $vhostManager = MazelabNginx_Model_DiFactory::getVhostManager();
        $vhostId = $this->getParam('vhostId');
        
        if(!($vhost = $vhostManager->getVhost($vhostId))) {
            Core_Model_DiFactory::getMessageManager()
                    ->addError(self::MESSAGE_VHOST_NOT_FOUND);
            return $this->_forward('index');
        }
        
        $form = new MazelabNginx_Form_DiffVhost();

        $this->view->local = $vhostManager->getVhostAsArray($vhostId);
        $this->view->remote = $vhostManager->getVhostRemoteAsArray($vhostId);
        
        if($this->getRequest()->isPost()) {
            if($form->isValid($this->getRequest()->getParams()) &&
                    $vhostManager->resolveDiff($vhostId, $form->getValues())) {
                $redirector = $this->_helper->getHelper('Redirector');
                return $redirector->gotoRoute(array($vhostId), 'mazelab-nginx_editVhost');
            }
            
            Core_Model_DiFactory::getMessageManager()->addError(self::MESSAGE_VHOST_RESOLVE_FAILED);
            Core_Model_DiFactory::getMessageManager()->addZendFormErrorMessages($form);
        }
        
        $this->view->form = $form;

        $navigation = $this->view->navigation();
        if (($active = $navigation->findActive($navigation->getContainer()))){
            $active["page"]->setLabel($this->view->translate('Differences in vhost %1$s', $vhost->getLabel()));
        }
    }
    
    public function indexAction()
    {
        if (isset($this->_identity['group']) &&
                $this->_identity['group'] == Core_Model_UserManager::GROUP_ADMIN) {
            return $this->forward('indexadmin');
        }

        $this->forward('indexclient');
    }
    
    public function indexadminAction()
    {
        $this->_setPager(MazelabNginx_Model_DiFactory::getSearchVhosts());
    }
    
    public function indexclientAction()
    {
        $this->_setPager(MazelabNginx_Model_DiFactory::getSearchVhostsByClient($this->_identity["_id"]));
    }
    
    protected function _setPager(Core_Model_SearchManager $pager)
    {
        $pager->setLimit($this->getParam('limit', 10));
        
        if($this->getParam('term')) {
            $pager->setSearchTerm($this->getParam('term'));
        }
        
        $action = $this->getParam('pagerAction');
        if($action == 'last') {
            $pager->last();
        } else {
            $pager->setPage($this->getParam('page', 1))->page();
        }
        
        $this->view->pager = $pager->asArray();
    }
    
}
