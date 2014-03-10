<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_NodeManager
{
    
    /**
     * name of this module
     */
    CONST MODULE_NAME = 'nginx';
    
    /**
     * get node vhost config of a certain node
     * 
     * @param string $nodeÍd
     * @return array
     */
    public function getNodeConfig($nodeId)
    {
        if(!($module = Core_Model_DiFactory::getModuleManager()->getModule(self::MODULE_NAME))) {
            return false;
        }
        
        return $module->getNodeConfig($nodeId);
    }
    
    /**
     * get all nodes which are assigned to this module
     * 
     * @return array
     */
    public function getNodes()
    {
        return Core_Model_DiFactory::getNodeManager()->getNodesByService(self::MODULE_NAME);
    }

    /**
     * get all nodes which are assigned to this module as array
     * 
     * @return array
     */
    public function getNodesAsArray()
    {
        return Core_Model_DiFactory::getNodesManager()->getNodesByServiceAsArray(self::MODULE_NAME);
    }
    
}