<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_DomainManager
{
    
    /**
     * name of this module
     */
    CONST MODULE_NAME = 'nginx';
    
    /**
     * adds given data set to domain config and saves it
     * 
     * @param string $domainId
     * @param array $data
     * @return boolean
     */
    public function addDomainConfig($domainId, array $data)
    {
        if(!($module = Core_Model_DiFactory::getModuleManager()->getModule(self::MODULE_NAME))) {
            return false;
        }
        
        if(!$module->addDomainConfig($domainId, $data)->save()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * get domain vhost config of a certain domain
     * 
     * @param string $domainId
     * @return array
     */
    public function getDomainConfig($domainId)
    {
        if(!($module = Core_Model_DiFactory::getModuleManager()->getModule(self::MODULE_NAME))) {
            return false;
        }
        
        return $module->getDomainConfig($domainId);
    }
    
    /**
     * gets all domains which are assigned to this module
     * 
     * @param string|null $clientId only domains of this client
     * @return array
     */
    public function getDomains($clientId = null)
    {
        return Core_Model_DiFactory::getDomainManager()->getDomainsByService(self::MODULE_NAME, $clientId);
    }

    /**
     * gets all domains which are assigned to this module as array
     * 
     * @param string|null $clientId only domains of this client
     * @return array
     */
    public function getDomainsAsArray($clientId = null)
    {
        return Core_Model_DiFactory::getDomainManager()->getDomainsByServiceAsArray(self::MODULE_NAME, $clientId);
    }

}
