<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_ValueObject_Vhost extends Core_Model_ValueObject
{
    
    /**
     * log action for conflicted vhost
     */
    CONST LOG_ACTION_VHOST_DIFF = 'vhost differences';
    
    /**
     * log action for a resolved vhost conflict
     */
    CONST LOG_ACTION_VHOST_DIFF_RESOLVED = 'resolved vhost differences';
    
    /**
     * message when vhost is conflicted
     */
    CONST MESSAGE_VHOST_DIFF = 'Differences in vhost %1$s';
    
    /**
     * message when vhost conflict was resolved
     */
    CONST MESSAGE_VHOST_DIFF_RESOLVED = 'Resolved differences of vhost %1$s';
    
    /**
     * message when saving vhost failed
     */
    CONST MESSAGE_VHOST_SAVE_FAILED = 'Vhost %1$s could not be saved';
    
    /**
     * search category string
     */
    CONST SEARCH_CATEGORY = 'nginx-vhost';
    
    /**
     * @var boolean
     */
    protected $_rebuildSearchIndex;
    
    /**
     * sets conflict entry in log
     */
    protected function _addConflictToLog()
    {
        $this->_getLogger()->setType(Core_Model_Logger::TYPE_CONFLICT)
                ->setMessage(self::MESSAGE_VHOST_DIFF)
                ->setMessageVars($this->getLabel())
                ->setAction(self::LOG_ACTION_VHOST_DIFF)
                ->setData($this->getConflicts())->setDomainRef($this->getData('domainId'))
                ->setModuleRef(MazelabNginx_Model_VhostManager::MODULE_NAME)
                ->setRoute(array($this->getId()), 'mazelab-nginx_diffVhost')
                ->setNodeRef($this->getData('nodeId'))
                ->saveByContext($this->getLabel());
    }
    
    /**
     * loads context from data backend with 
     * 
     * @return array
     */
    public function _load()
    {
        return $this->getProvider()->getVhost($this->getId());
    }
    
    /**
     * saves context into data backend
     * 
     * @return string $id data backend identification
     */
    protected function _save(array $unmappedData)
    {
        $id = $this->getProvider()->saveVhost($unmappedData, $this->getId());
        
        if(!$id || ($this->getId() && $id !== $this->getId())) {
            Core_Model_DiFactory::getMessageManager()
                    ->addError(self::MESSAGE_VHOST_SAVE_FAILED, $this->getLabel());
            return false;
        }
        
        return $id;
    }
    
    /**
     * apply current configuration
     * 
     * @param boolean $save (default true) save or only set commands
     * @return boolean
     */
    public function apply($save = true)
    {
        return MazelabNginx_Model_DiFactory::getApplyVhost()->apply($this, $save);
    }
    
    /**
     * disable this vhost
     * 
     * @return boolean
     */
    public function disable()
    {
        if($this->getStatus() === false) {
            return true;
        }

        if(!$this->setData(array('status' => false))->setFlags()->save()) {
            return false;
        }
        
        $this->apply();
        
        return true;
    }
    
    /**
     * saves allready seted Data into the data backend
     * 
     * calls _save
     * 
     * @return boolean
     */
    public function save()
    {
        if(!parent::save()) {
            return false;
        }
        
        if($this->_rebuildSearchIndex) {
            $this->_rebuildSearchIndex = false;
            MazelabNginx_Model_DiFactory::getIndexManager()->setVhost($this->getId());
        }
        
        return true;
    }
    
    /**
     * delete vhost on actual node
     * 
     * @return boolean
     */
    public function setDeleteFlag()
    {
        if(!$this->setData(array('delete' => true))->save()) {
            return false;
        }
        
        $this->apply();
        
        return true;
    }
    
    /**
     * enable this vhost
     * 
     * @return boolean
     */
    public function enable()
    {
        if($this->getStatus() === true) {
            return true;
        }

        if(!$this->setData(array('status' => true))->setFlags()->save()) {
            return false;
        }
        
        $this->apply();
        
        return true;
    }
    
    /**
     * evaluates given report
     * 
     * @param array $data report data
     * @return boolean
     */
    public function evalReport(array $data)
    {
        if($this->getData('delete')) {
            if(!$data) {
                return MazelabNginx_Model_DiFactory::getVhostManager()->deleteVhost($this->getId());
            }
            return $this->apply(false);
        }
        
        $oldDiffs = $this->getConflicts();
        $this->setRemoteData($data);

        if($this->getConflicts(MazeLib_Bean::STATUS_MANUALLY)) {
            $this->_addConflictToLog();
        } elseif ($this->getConflicts()) {
            $this->apply(false);
        } elseif($oldDiffs) {
            $this->resolveDiff();
        }
        
        return $this->setFlags()->save();
    }
    
    /**
     * returns the Bean with the loaded data from data backend
     * 
     * @param boolean $new force new bean struct
     * @return MazelabNginx_Model_Bean_Vhost
    */
    public function getBean($new = false)
    {
        if($new || !$this->_valueBean || !$this->_valueBean instanceof MazelabNginx_Model_Bean_Vhost) {
            $this->_valueBean = new MazelabNginx_Model_Bean_Vhost();
        }
        
        $this->load();
        
        return $this->_valueBean;
    }
    
    /**
     * get client (owner) of this vhost
     * 
     * @return null|Core_Model_ValueObject_Client
     */
    public function getClient()
    {
        if(!($clientId = $this->getData('clientId'))) {
            return null;
        }
        
        return Core_Model_DiFactory::getClientManager()->getClient($clientId);
    }
    
    /**
     * get client config of this vhost
     * 
     * @return array
     */
    public function getClientConfig()
    {
        if(!$this->getData('clientId')) {
            return array();
        }
        
        return MazelabNginx_Model_DiFactory::getClientManager()
                ->getClientConfig($this->getData('clientId'));
    }
    
    /**
     * returns domain object of this vhost
     * 
     * @return Core_Model_ValueObject_Domain|null
     */
    public function getDomain()
    {
        if(!($domainId = $this->getData('domainId'))) {
            return null;
        }
        
        return Core_Model_DiFactory::getDomainManager()->getDomain($domainId);
    }
    
    /**
     * gets vhost label string
     * alias of getData('label')
     * 
     * @return string
     */
    public function getLabel()
    {
        return $this->getData('label');
    }
    
    /**
     * gets node of this vhost
     * 
     * @return Core_Model_ValueObject_Node|null
     */
    public function getNode()
    {
        if(!($nodeId = $this->getData('nodeId'))) {
            return null;
        }
        
        return Core_Model_DiFactory::getNodeManager()->getNode($nodeId);
    }
    
    /**
     * get node config of this vhost
     * 
     * @return array
     */
    public function getNodeConfig()
    {
        if(!$this->getData('nodeId')) {
            return array();
        }
        
        return MazelabNginx_Model_DiFactory::getNodeManager()
                ->getNodeConfig($this->getData('nodeId'));
    }

    /**
     * get vhost provider
     * 
     * @return MazelabNginx_Model_Dataprovider_Interface_Vhost
     */
    public function getProvider()
    {
        return MazelabNginx_Model_Dataprovider_DiFactory::getVhost();
    }
    
    /**
     * get status of this vhost
     * 
     * @return boolean
     */
    public function getStatus()
    {
        if($this->getData('status') == true) {
            return true;
        }
        
        return false;
    }
    
    /**
     * if corresponding log entry exists it will be changed into resolved state
     * 
     * @return boolean
     */
    public function resolveDiff()
    {
        if(!Core_Model_DiFactory::getLogManager()->getContextLog($this->getLabel(),
                Core_Model_Logger::TYPE_CONFLICT, self::LOG_ACTION_VHOST_DIFF)) {
            return true;
        }

        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_DIFF_RESOLVED)
                ->setMessageVars($this->getLabel())
                ->setType(Core_Model_Logger::TYPE_NOTIFICATION)
                ->setAction(self::LOG_ACTION_VHOST_DIFF_RESOLVED);
        
        $this->_getLogger()->saveByContext($this->getLabel(),
                Core_Model_Logger::TYPE_CONFLICT, self::LOG_ACTION_VHOST_DIFF);
        
        return true;
    }
    
    /**
     * sets/adds new data set
     * 
     * @param array $data
     * @return MazelabNginx_Model_ValueObject_Vhost
     */
    public function setData(array $data)
    {
        if(key_exists('status', $data)) {
            $data["status"] = (boolean) $data["status"];
        }
        if(key_exists('content', $data)) {
            $data['content'] = str_replace("\r\n","\n", $data['content']);
            // normalize tabs
            $data['content'] = str_replace("\t","    ", $data['content']);
            $data['content'] = str_replace("    ","\t", $data['content']);
        }
        if(key_exists('nodeId', $data) && 
                ($node = Core_Model_DiFactory::getNodeManager()->getNode($data['nodeId']))) {
            $data['nodeName'] = $node->getName();
        }
        if(key_exists('domainId', $data) && 
                ($domain = Core_Model_DiFactory::getDomainManager()->getDomain($data['domainId']))) {
            $data['domainName'] = $domain->getName();
            $data['clientId'] = $domain->getData('owner');
        }
        if(key_exists('label', $data)) {
            $this->_rebuildSearchIndex = true;
        }
        
        return parent::setData($data);
    }
    
    /**
     * sets or unsets pending flag
     * 
     * @return MazelabNginx_Model_ValueObject_Vhost
     */
    public function setFlags()
    {
        if($this->getData('nodeId') && $this->getConflicts(MazeLib_Bean::STATUS_MANUALLY)) {
            $this->unsetProperty('pending')->setProperty('conflicted', true);
        } elseif($this->getData('nodeId') && $this->getConflicts()) {
            $this->unsetProperty('conflicted')->setProperty('pending', true);
        } else {
            $this->unsetProperty('pending')->unsetProperty('conflicted');
        }
        
        return $this;
    }
    
    /**
     * sets/adds new data set as remote data
     * 
     * additional boolean cast
     * 
     * @param array $data
     * @return MazelabNginx_Model_ValueObject_Vhost
     */
    public function setRemoteData($data)
    {
        if(key_exists('label', $data)) {
            unset($data['label']);
        }
        if(key_exists('domain', $data)) {
            unset($data['domain']);
        }
        
        if(key_exists('status', $data) && $data['status'] == 'false') {
            $data['status'] = false;
        } elseif(key_exists('status', $data)) {
            $data['status'] = true;
        }
        
        return parent::setRemoteData($data);
    }
    
}
