<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Form_Validate_AvailableNode extends Zend_Validate_Abstract
{

    CONST LABEL_ALLREADY_EXISTS = 'Label allready exists';
    
    CONST NOT_FOUND = 'Node not found';
    
    protected $_messageTemplates = array(
        self::NOT_FOUND => 'Node not found',
        self::LABEL_ALLREADY_EXISTS => "A vhost with name '%vhostLabel%' allready exists on node '%value%'",
    );
    
    /**
     * @var array
     */
    protected $_messageVariables = array(
        'vhostLabel' => '_vhostLabel'
    );
    
    /**
     * label of vhost
     *
     * @var string
     */
    protected $_vhostLabel;
    
    /**
     * @param string $vhostLabel
     */
    public function __construct($vhostLabel = null) {
        $this->_vhostLabel = $vhostLabel;
    }
    
    public function isValid($value)
    {
        if (!$this->validate($value)){
            return false;
        }
 
        return true;

    }
    
    /**
     * validates node availibility
     * 
     * @param string $value
     * @return boolean
     */
    protected function validate($value)
    {
        if(!$value) {
            return true;
        }
        
        if(!($node = Core_Model_DiFactory::getNodeManager()->getNode($value))) {
            $this->_error(self::NOT_FOUND);
            return false;
        }
        
        if($this->_vhostLabel) {
            foreach(MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByNode($value) as $vhosts) {
                if($vhosts->getLabel() === $this->_vhostLabel) {
                    $this->_error(self::LABEL_ALLREADY_EXISTS, $node->getName());
                    return false;
                }
            }
        }
        
        return true;
    }
    
}
