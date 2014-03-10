<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Form_Validate_AvailableLabel extends Zend_Validate_Abstract
{

    const ALLREADY_EXISTS = 'allready exists';
    
    const NODE_NOT_FOUND = 'node not found';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::ALLREADY_EXISTS => "A vhost with name '%value%' allready exists on node '%nodeName%'",
        self::NODE_NOT_FOUND => "Node not found"
    );
    
    /**
     * @var array
     */
    protected $_messageVariables = array(
        'nodeName' => '_nodeName'
    );

    /**
     * node name
     *
     * @var string
     */
    protected $_nodeName;
    
    public function isValid($value)
    {
        $nodeId = null;
        
        $allArgs = func_get_args();
        if(key_exists('nodeId', $allArgs[1])) {
            $nodeId = $allArgs[1]['nodeId'];
        }
        if (!$this->validate($value, $nodeId)){
            return false;
        }
 
        return true;

    }
    
    /**
     * validates label availability based on node dependancy
     * 
     * @param string $value
     * @param string $nodeId
     * @return boolean
     */
    protected function validate($value, $nodeId)
    {
        if(!$nodeId) {
            return true;
        }
        
        if(!($node = Core_Model_DiFactory::getNodeManager()->getNode($nodeId))) {
            $this->_error(self::NODE_NOT_FOUND);
            return false;
        }
        
        $this->_nodeName = $node->getName();
        foreach(MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByNode($nodeId) as $vhosts) {
            if($vhosts->getLabel() === $value) {
                $this->_error(self::ALLREADY_EXISTS, $value);
                return false;
            }
        }
        
        return true;
    }
    
}
