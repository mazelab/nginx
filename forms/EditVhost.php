<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Form_EditVhost extends MazelabNginx_Form_AddVhost
{
    
    protected $_elementDecorators = array(
        'ViewHelper'
    );

    public function init()
    {
        parent::init();
        
        $this->addElement('text', 'domainId', array(
            'jsLabel' => 'Name',
            'label' => 'Of domain',
            'helper' => 'formTextAsSpan'
        ));
        
        $this->getElement('label')->setOptions(array(
            'jsLabel' => 'Name',
            'helper' => 'formTextAsSpan',
            'validatory' => null
        ));

        $this->setElementDecorators($this->_elementDecorators);
    }
    
    /**
     * set domain name instead id
     */
    public function setDomainInput()
    {
        if(!($domainId = $this->getElement('domainId')->getValue()) 
                || !($domain = Core_Model_DiFactory::getDomainManager()->getDomain($domainId))) {
            return $this;
        }
        
        $this->getElement('domainId')->setValue($domain->getName());
        
        return $this;
    }

    /**
     * sets validator of nodeId input with vhost dependancy
     * 
     * @param string $vhostId
     */
    public function setNodeValidators($vhostId) {
        $this->getElement('nodeId')->setValidators(array(
           new MazelabNginx_Form_Validate_AvailableNode($vhostId)
        ));
        
        return $this;
    }
    
}
