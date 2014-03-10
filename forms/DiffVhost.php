<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Form_DiffVhost extends Zend_Form
{
    
    public function init()
    {
        parent::init();
        
        $this->addElement('textarea', 'content', array(
            'required' => true,
            'label' => 'Content',
            'rows' => '12'
        ));
    }
    
}
