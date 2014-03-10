<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

interface MazelabNginx_Model_Dataprovider_Interface_Vhost
{

    /**
     * deletes a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function deleteVhost($vhostId);

    /**
     * drops the module vhost collection
     * 
     * @return boolean
     */
    public function drop();
    
    /**
     * get a certain vhost by id
     * 
     * @param string $vhostId
     * @return array
     */
    public function getVhost($vhostId);
    
    /**
     * get all vhost of a certain client
     * 
     * @param string $clientId
     * @return array
     */
    public function getVhostsByClient($clientId);
    
    /**
     * get all vhost of a certain domain
     * 
     * @param string $domainId
     * @return array
     */
    public function getVhostsByDomain($domainId);
    
    /**
     * get all vhost of a certain node
     * 
     * @param string $nodeId
     * @return array
     */
    public function getVhostsByNode($nodeId);
    
    /**
     * get all vhosts
     * 
     * @return array
     */
    public function getVhosts();    
    
    /**
     * saves vhost in the data backend
     * 
     * @param array $data
     * @param string $vhostId
     * @return $vhostId
     */
    public function saveVhost($data, $vhostId = null);
    
}
