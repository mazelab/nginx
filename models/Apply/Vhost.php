<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Apply_Vhost
{
    
    /**
     * @var MazelabNginx_Model_ValueObject_Vhost
     */
    protected $_vhost;

    /**
     * gets current vhost instance
     * 
     * @return MazelabNginx_Model_ValueObject_Vhost
     */
    protected function _getVhost()
    {
        return $this->_vhost;
    }
    
    /**
     * gets commands in order to achieve desired state
     * 
     * @return array|null
     */
    protected function _getCommands()
    {
        if($this->_getVhost()->getData('delete') === true) {
            return $this->_getCommandsDelete();
        }
        
        // no conflicts then do nothing and manuell conflicts must be resolved before
        if(!$this->_getVhost()->getConflicts() || 
                $this->_getVhost()->getConflicts(MazeLib_Bean::STATUS_MANUALLY)) {
            return null;
        }
        
        if(!$this->_getVhost()->getStatus() && $this->_getVhost()->getBean()->hasConflict('status')) {
            return $this->_getCommandsDisable();
        }
        
        return $this->_getCommandsSet();
    }
    
    /**
     * get deactivate commands for current vhost
     * 
     * @return array
     */
    protected function _getCommandsDisable()
    {
        $commands[] = "vhost -N {$this->_getEscapedLabel()} disable";
        return $commands;
    }
    
    /**
     * get delete commands for current vhost
     * 
     * @return array
     */
    protected function _getCommandsDelete()
    {
        $commands[] = "vhost -N {$this->_getEscapedLabel()} delete";
        return $commands;
    }
    
    /**
     * get deactivate commands for current vhost
     * 
     * @return array
     */
    protected function _getCommandsSet()
    {
        if(!($content = $this->_getEscapedContent())) {
            return array();
        }
        
        $commands[] = "vhost -N {$this->_getEscapedLabel()} -C {$content} set";
        if(!$this->_getVhost()->getStatus()) {
            $commands[] = "vhost -N {$this->_getEscapedLabel()} disable";
        } else {
            $commands[] = "vhost -N {$this->_getEscapedLabel()} enable";
        }
        
        return $commands;
    }
    
    /**
     * get escaped content from current vhost
     * 
     * @return string
     */
    protected function _getEscapedContent()
    {
        return escapeshellarg($this->_getVhost()->getData('content'));
    }
    
    /**
     * get escaped label from current vhost
     * 
     * @return string
     */
    protected function _getEscapedLabel()
    {
        return escapeshellarg($this->_getVhost()->getLabel());
    }
    
    /**
     * apply given vhost instance
     * 
     * @param MazelabNginx_Model_ValueObject_Vhost $vhost
     * @param boolean $save (default true) save or only set commands
     * @return boolean
     */
    public function apply(MazelabNginx_Model_ValueObject_Vhost $vhost, $save = true)
    {
        $this->_vhost = $vhost;
        if(!($node = $this->_getVhost()->getNode())) {
            return false;
        }
        
        if(!($commands = $this->_getCommands())) {
            return true;
        }
        
        $key = "vhost {$this->_getVhost()->getLabel()}";
        if(($result = $node->getCommands()->addContextCommands('nginx', $key, $commands)) && $save) {
            return $node->getCommands()->save();
        }
        
        return $result;
    }
    
}
