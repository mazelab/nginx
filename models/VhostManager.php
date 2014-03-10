<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_VhostManager
{
    
    /**
     * message when changes can't be applied because the domain ist not assigned to a node
     */
    CONST MESSAGE_APPLY_FAILED_DOMAIN_NOT_NODE_ASSIGNED = 'Changes in vhost %1$s won\'t be applied on the node because the domain is not assigned to a node yet. Please Contact your administrator to resolve this issue';
    
    /**
     * message when vhost was succesful created
     */
    CONST MESSAGE_VHOST_CREATED = 'Vhost %1$s was created';
    
    /**
     * message when vhost was deleted
     */
    CONST MESSAGE_VHOST_DELETED = 'Vhost %1$s was deleted';
    
    /**
     * message when vhost should be removed from nodes and then to be deleted
     */
    CONST MESSAGE_VHOST_DELETE_MARKED = 'Vhost %1$s has been marked to be deleted';
    
    /**
     * message when vhost was disabled
     */
    CONST MESSAGE_VHOST_DISABLED = 'Vhost %1$s was disabled';
    
    /**
     * message when vhost was enabled
     */
    CONST MESSAGE_VHOST_ENABLED = 'Vhost %1$s was enabled';
    
    /**
     * message when vhost was assigned to a node
     */
    CONST MESSAGE_VHOST_NODE_ASSINGED = 'Vhost %1$s was assigned to node %2$s';
    
    /**
     * message when node was unassigned
     */
    CONST MESSAGE_VHOST_NODE_UNASSIGNED = 'Vhost %1$s was unassigned from node %2$s';

    /**
     * message when saving vhost failed
     */
    CONST MESSAGE_VHOST_SAVE_FAILED = 'Vhost %1$s could not be saved';
    
    /**
     * message when vhost was updated
     */
    CONST MESSAGE_VHOST_UPDATE = 'Vhost %1$s was updated';
    
    /**
     * name of the module
     */
    CONST MODULE_NAME = 'nginx';
    
    /**
     * @var array contains MazelabNginx_Model_ValueObject_Vhost
     */
    protected $_vhosts = array();
    
    /**
     * @return Core_Model_Logger
     */
    protected function _getLogger()
    {
        return Core_Model_DiFactory::getLogger();
    }
    
    /**
     * get module object
     * 
     * @return Core_Model_ValueObject_Module|null
     */
    protected function _getModule()
    {
        return Core_Model_DiFactory::getModuleManager()->getModule(self::MODULE_NAME);
    }
    
    /**
     * loads and registers a certain vhost instance
     * 
     * @param string $vhostId
     * @return boolean
     */
    protected function _loadVhost($vhostId)
    {
        $data = $this->getProvider()->getVhost($vhostId);
        if(empty($data)) {
            return false;
        }
        
        return $this->registerVhost($vhostId, $data);
    }
    
    /**
     * sets standard vhost log params and saves it
     * 
     * other log params should be set to _getLogger() before calling this method
     * 
     * @param MazelabNginx_Model_ValueObject_Vhost $vhost vhost instance
     * @param string $type log type definition
     * @param boolean $client message can also be viewed by the client
     * @return string|null returns log id on success
     */
    protected function _logVhost(MazelabNginx_Model_ValueObject_Vhost $vhost, $type = Core_Model_Logger::TYPE_NOTIFICATION, $client = false)
    {
        if($client) {
            $this->_getLogger()->setClientRef($vhost->getData('clientId'));
        }
        
        $this->_getLogger()->setType($type)->setNodeRef($vhost->getData('nodeId'))
                ->setDomainRef($vhost->getData('domainId'))
                ->setModuleRef(self::MODULE_NAME);
        
        return $this->_getLogger()->save();
    }
    
    /**
     * add a new vhost
     * 
     * @param array $data
     * @return string|null vhost id
     */
    public function addVhost(array $data)
    {
        if(key_exists('nodeId', $data) && $data['nodeId'] &&
                !Core_Model_DiFactory::getNodeManager()->getNode($data['nodeId'])) {
            return null;
        }
        
        $vhost = MazelabNginx_Model_DiFactory::newVhost();
        if(!$vhost->setLoaded(true)->setData($data)->setFlags()->save()) {
            Core_Model_DiFactory::getMessageManager()
                    ->addError(self::MESSAGE_VHOST_SAVE_FAILED, $vhost->getLabel());
            return null;
        }

        $vhost->apply();

        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_CREATED)
                ->setData($vhost->getData())->setMessageVars($vhost->getLabel());
        $this->_logVhost($vhost);
        
        return $vhost->getId();
    }
    
    /**
     * assigns a certain vhost to a node or unassign if no nodeId is given
     * 
     * @param string $vhostId
     * @param string|null $nodeId
     * @return boolean
     */
    public function assignToNode($vhostId, $nodeId = null)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        if($nodeId && (!($node = Core_Model_DiFactory::getNodeManager()->getNode($nodeId)) ||
                !$node->hasService(self::MODULE_NAME))) {
            return false;
        }
        
        if(!$nodeId) {
            return $this->unassignNode($vhostId);
        }
        
        if(($actualNode = $vhost->getNode()) 
                && $actualNode->getId() == $nodeId) {
            return true;
        }
        
        if($actualNode && !$this->unassignNode($vhostId) ||
                !$vhost->setData(array('nodeId' => $nodeId))->save()) {
            return false;
        }
        
        $vhost->apply();
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_NODE_ASSINGED)
                ->setMessageVars($vhost->getLabel(), $vhost->getData('nodeName'));
        $this->_logVhost($vhost, Core_Model_Logger::TYPE_WARNING);
        
        return true;
    }
    
    /**
     * delete a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function deleteVhost($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        if(!$this->getProvider()->deleteVhost($vhostId)) {
            return false;
        }
        
        if(key_exists($vhostId, $this->_vhosts)) {
            unset($this->_vhosts[$vhostId]);
        }
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_DELETED)
                ->setMessageVars($vhost->getLabel());
        $this->_logVhost($vhost);

        MazelabNginx_Model_DiFactory::getIndexManager()->unsetVhost($vhostId);
        
        return true;
    }
    
    /**
     * deactivates a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function disableVhost($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        if($vhost->getStatus() === false) {
            return true;
        }
        if(!$vhost->disable()) {
            return false;
        }
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_DISABLED)
                ->setMessageVars($vhost->getLabel());
        $this->_logVhost($vhost);
        
        return true;
    }
    
    /**
     * activates a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function enableVhost($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        if($vhost->getStatus() === true) {
            return true;
        }
        if(!$vhost->enable()) {
            return false;
        }
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_ENABLED)
                ->setMessageVars($vhost->getLabel());
        $this->_logVhost($vhost);
        
        return true;
    }    
    
    /**
     * get vhost data provider
     * 
     * @return MazelabNginx_Model_Dataprovider_Interface_Vhost
     */
    public function getProvider()
    {
        return MazelabNginx_Model_Dataprovider_DiFactory::getVhost();
    }
    
    /**
     * get a certain vhost instance
     * 
     * @param string $vhostId
     * @return MazelabNginx_Model_ValueObject_Vhost
     */
    public function getVhost($vhostId)
    {
        if(isset($this->_vhosts[$vhostId])) {
            return $this->_vhosts[$vhostId];
        }
        
        if(!$this->_loadVhost($vhostId) || !isset($this->_vhosts[$vhostId])) {
            return null;
        }
        
        return $this->_vhosts[$vhostId];
    }
    
    /**
     * get a certain vhost instance
     * 
     * @param string $vhostId
     * @return array
     */
    public function getVhostAsArray($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return array();
        }
        
        return $vhost->getData();
    }
    
    /**
     * gets remote data of a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function getVhostRemoteAsArray($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        return $vhost->getRemoteData();
    }
    
    /**
     * gets all vhosts
     * 
     * @return array contains MazelabNginx_Model_ValueObject_Vhost
     */
    public function getVhosts()
    {
        if(!($vhosts = $this->getProvider()->getVhosts()) || !is_array($vhosts)) {
            return array();
        }
        
        foreach ($vhosts as $vhostId => $vhost) {
            if($this->registerVhost($vhostId, $vhost)) {
                $vhosts[$vhostId] = $this->_vhosts[$vhostId];
            }
        }

        return $vhosts;
    }
    
    /**
     * gets all vhosts as array
     * 
     * @return array
     */
    public function getVhostsAsArray()
    {
        $result = array();
        foreach($this->getVhosts() as $id => $vhost) {
            $result[$id] = $vhost->getData();
        }
        
        return $result;
    }
    
    /**
     * get all vhosts of a certain client
     * 
     * @param string $clientId
     * @return array contains MazelabNginx_Model_ValueObject_Vhost
     */
    public function getVhostsByClient($clientId)
    {
        $vhosts = array();
        
        foreach($this->getProvider()->getVhostsByClient($clientId) as $vhostId => $vhost) {
            if($this->registerVhost($vhostId, $vhost)) {
                $vhosts[$vhostId] = $this->_vhosts[$vhostId];
            }
        }
        
        return $vhosts;
    }
    
    /**
     * get all vhosts of a certain domain
     * 
     * @param string $domainId
     * @return array contains MazelabNginx_Model_ValueObject_Vhost
     */
    public function getVhostsByDomain($domainId)
    {
        $vhosts = array();
        
        foreach($this->getProvider()->getVhostsByDomain($domainId) as $vhostId => $vhost) {
            if($this->registerVhost($vhostId, $vhost)) {
                $vhosts[$vhostId] = $this->_vhosts[$vhostId];
            }
        }
        
        return $vhosts;
    }
    
    /**
     * get all vhosts of a certain node
     * 
     * @param string $nodeId
     * @return array contains MazelabNginx_Model_ValueObject_Vhost
     */
    public function getVhostsByNode($nodeId)
    {
        $vhosts = array();
        
        foreach($this->getProvider()->getVhostsByNode($nodeId) as $vhostId => $vhost) {
            if(!key_exists($vhostId, $this->_vhosts)) {
                $this->registerVhost($vhostId, $vhost);
            }
            
            $vhosts[$vhostId] = $this->_vhosts[$vhostId];
        }
        
        return $vhosts;
    }
    
    /**
     * gets all vhosts as array
     * 
     * @return array
     */
    public function getVhostsWithDependanciesAsArray()
    {
        $result = array();
        foreach($this->getVhosts() as $id => $vhost) {
            $result[$id] = $vhost->getData();
            if(($domain = $vhost->getDomain())) {
                $result[$id]['domain'] = $domain->getData();
            }
            if(($node = $vhost->getNode())) {
                $result[$id]['node'] = $node->getData();
            }            
        }
        
        return $result;
    }
    
    /**
     * checks if the given vhost has a manuell diff
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function hasManuellDiff($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        return $vhost->getConflicts(MazeLib_Bean::STATUS_MANUALLY);
    }
    
    /**
     * delete a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function setVhostDeleteFlag($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        if(!$vhost->getData('nodeId')) {
            return $this->deleteVhost($vhostId);
        }
        if (!$vhost->setDeleteFlag()) {
            return false;
        }
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_DELETE_MARKED)
                ->setMessageVars($vhost->getLabel());
        $this->_logVhost($vhost);
        
        return true;
    }
    
    /**
     * registers a vhost instance
     * 
     * overwrites existing instances
     * 
     * @param string $vhostId
     * @param array|MazelabNginx_Model_ValueObject_Vhost $context
     * @param boolean $setLoadedFlag only when $context is array states if
     * loading flag will be set to avoid double loading
     * @return boolean
     */
    public function registerVhost($vhostId, $context, $setLoadedFlag = true)
    {
        $vhost = null;
        
        if($context instanceof MazelabNginx_Model_ValueObject_Vhost) {
            $vhost = $context;
        } elseif (is_array($context)) {
            $vhost = MazelabNginx_Model_DiFactory::newVhost($vhostId);
            
            if($setLoadedFlag) {
                $vhost->setLoaded(true);
            }
            
            $vhost->getBean()->setBean($context);
        }
        
        if(!$vhost) {
            return false;
        }
        
        $this->_vhosts[$vhostId] = $vhost;
        
        return true;
    }

    /**
     * resolves diffences of a certain vhost
     * 
     * @param string $vhostId
     * @param array $data
     * @return boolean
     */
    public function resolveDiff($vhostId, array $data)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        if(!$vhost->setData($data)->setFlags()->save()) {
            return false;
        }
        
        $vhost->apply();
        
        $vhost->resolveDiff();
        
        return true;
    }
    
    /**
     * removes vhost assignment to nodes
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function unassignNode($vhostId)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        if(!$vhost->getData('nodeId')) {
            return true;
        }
        
        $node = Core_Model_DiFactory::getNodeManager()->getNode($vhost->getData('nodeId'));
        if(!$vhost->unsetProperty('nodeId')->unsetProperty('nodeName')->save()) {
            return false;
        }
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_NODE_UNASSIGNED)
                ->setMessageVars($vhost->getLabel(), $node->getName());
        $this->_logVhost($vhost, Core_Model_Logger::TYPE_WARNING);
        
        return true;
    }
    
    /**
     * updates a certain vhost with the given data
     * 
     * @param string $vhostId
     * @param array $data
     * @return boolean
     */
    public function updateVhost($vhostId, array $data)
    {
        if(!($vhost = $this->getVhost($vhostId))) {
            return false;
        }
        
        if(key_exists('nodeId', $data) && !$this->assignToNode($vhostId, $data['nodeId'])) {
            return false;
        }
        if(key_exists('nodeId', $data) && count($data) === 1) {
            return true;
        }
        
        if(!$vhost->setData($data)->setFlags()->save()) {
            return false;
        }
        
        $vhost->apply();
        
        $this->_getLogger()->setMessage(self::MESSAGE_VHOST_UPDATE)
                ->setMessageVars($vhost->getLabel())->setData($data);
        $this->_logVhost($vhost);
        
        return true;
    }
    
}
