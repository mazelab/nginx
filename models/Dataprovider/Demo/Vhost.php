<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Dataprovider_Demo_Vhost
    implements MazelabNginx_Model_Dataprovider_Interface_Vhost
{
    
    /**
     * deletes a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function deleteVhost($vhostId)
    {
        return false;
    }

    /**
     * drops the module vhost collection
     * 
     * @return boolean
     */
    public function drop()
    {
        $this->_setCollection(self::COLLECTION, null);
        
        return empty($this->_getCollection(self::COLLECTION));
    }
    
    /**
     * gets a certain vhost by id
     * 
     * @param string $vhostId
     * @return array
     */
    public function getVhost($vhostId)
    {
        return array();
    }
    
    /**
     * get all vhost of a certain client
     * 
     * @param string $clientId
     * @return array
     */
    public function getVhostsByClient($clientId)
    {
        return array();
    }
    
    /**
     * get all vhost of a certain domain
     * 
     * @param string $domainId
     * @return array
     */
    public function getVhostsByDomain($domainId)
    {
        return array();
    }
    
    /**
     * get all vhost of a certain node
     * 
     * @param string $nodeId
     * @return array
     */
    public function getVhostsByNode($nodeId)
    {
        return array();
    }
    
    /**
     * get all vhosts
     * 
     * @return array
     */
    public function getVhosts()
    {
        return array();
    }
    
    /**
     * saves vhost in the data backend
     * 
     * @param array $data
     * @param string $vhostId
     * @return $vhostId
     */
    public function saveVhost($data, $vhostId = null)
    {
        return array();
    }
    
}
