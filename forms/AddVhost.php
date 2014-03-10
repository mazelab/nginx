<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Form_AddVhost extends Zend_Form
{
    
    protected $_elementDecorators = array(
        'ViewHelper',
        'TwitterBootstrapError'
    );

    public function init()
    {
        $this->addPrefixPath('MazeLib_Form_Decorator_', 'MazeLib/Form/Decorator/', 'decorator');
        
        $this->addElement('text', 'label', array(
            'label' => 'Label',
            'validators' => array(
                new MazelabNginx_Form_Validate_AvailableLabel()
            ),
            'required' => true
        ));
        
        $this->addElement('select', 'nodeId', array(
            "label" => "On node",
            "multiOptions" => array(
                "" => "unassigned"
            ),
            'validators' => array(
                new MazelabNginx_Form_Validate_AvailableNode()
            ),
            "class" => "selectpicker",
            "value" => array("")
        ));
        
        $this->addElement('select', 'domainId', array(
            "label" => "Of domain",
            "multiOptions" => array(
                "" => "unassigned"
            ),
            'validators' => array(
                new MazelabNginx_Form_Validate_AvailableDomain()
            ),
            "class" => "selectpicker",
            "value" => array("")
        ));
        
        $this->addElement('select', 'status', array(
            "label" => "Virtual host is",
            "multiOptions" => array(
                "0" => "deactivated",
                "1" => "activated"
            ),
            "class" => "selectpicker",
            "value" => array(1)
        ));
        
        $this->addElement('textarea', 'content', array(
            'required' => true,
            'class' => 'row-fluid cssNginxCode',
            'label' => 'Content',
            'rows' => '12'
        ));
        
        $this->setElementDecorators($this->_elementDecorators);
    }
    
    /**
     * add available nodes to node select element
     */
    public function initNodeSelect()
    {
        foreach(MazelabNginx_Model_DiFactory::getNodeManager()->getNodes() as $id => $node) { 
            $this->getElement('nodeId')->addMultiOption($id, $node->getName());
        }
        
        return $this;
    }
    
    /**
     * add available nodes to domain select element
     */
    public function initDomainSelect()
    {
        foreach(MazelabNginx_Model_DiFactory::getDomainManager()->getDomains() as $id => $domain) { 
            $this->getElement('domainId')->addMultiOption($id, $domain->getName());
        }
        
        return $this;
    }
    
}
