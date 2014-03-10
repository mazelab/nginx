<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Form_Validate_AvailableDomain extends Zend_Validate_Abstract
{

    const NOT_FOUND = 'Domain not found';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_FOUND => 'Domain not found',
    );
    
    public function isValid($value)
    {
        if (!$this->validate($value)){
            return false;
        }
 
        return true;

    }
    
    /**
     * validates domain availibility
     * 
     * @param string $value
     * @return boolean
     */
    protected function validate($value)
    {
        if(!$value) {
            return true;
        }
        
        if(!($domain = Core_Model_DiFactory::getDomainManager()->getDomain($value))) {
            $this->_error(self::NOT_FOUND);
            return false;
        }
        
        return true;
    }
    
}
