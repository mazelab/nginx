<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_ClientManager
{
    
    CONST MODULE_NAME = 'nginx';

    /**
     * add given form data as config of a certain client
     * 
     * @param string $clientId
     * @param MazelabNginx_Form_ConfigClient $form
     * @return boolean
     */
    public function addClientConfig($clientId, MazelabNginx_Form_ConfigClient $form)
    {
        if(!($module = Core_Model_DiFactory::getModuleManager()->getModule(self::MODULE_NAME))) {
            return false;
        }
        
        if(!($validValues = $form->getValidValues($form->getValues())) || empty($validValues)) {
            return false;
        }
        
        if(!$module->addClientConfig($clientId, $validValues)->save()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * returns config of a certain client
     * 
     * @param string $clientId
     * @return array
     */
    public function getClientConfig($clientId)
    {
        if(!($module = Core_Model_DiFactory::getModuleManager()->getModule(self::MODULE_NAME))) {
            return array();
        }
        
        return $module->getClientConfig($clientId);
    }
    
}