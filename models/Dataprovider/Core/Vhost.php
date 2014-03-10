<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Dataprovider_Core_Vhost
    extends MazelabNginx_Model_Dataprovider_Core_Data
    implements MazelabNginx_Model_Dataprovider_Interface_Vhost
{
    
    /**
     * init mongodb
     */
    public function __construct() {
        parent::__construct();
        
        $this->_getVhostCollection()->deleteIndex(array(
            self::KEY_LABEL => 1
        ));
    }
    
    /**
     * deletes a certain vhost
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function deleteVhost($vhostId)
    {
        $mongoId = new MongoId($vhostId);

        $query = array(
            self::KEY_ID => $mongoId
        );

        $options = array(
            "j" => true
        );

        $result = $this->_getVhostCollection()->remove($query, $options);

        if(!key_exists('ok', $result) || $result['ok'] != true) {
            return false;
        }

        return true;
    }
    
    /**
     * drops the module vhost collection
     * 
     * @return boolean
     */
    public function drop()
    {
        if (($result = $this->_getVhostCollection()->drop()) && $result["ok"] == 1) {
            return true;
        }

        return false;
    }
    
    /**
     * gets a certain vhost by id
     * 
     * @param string $vhostId
     * @return array
     */
    public function getVhost($vhostId)
    {
        $query = array(
            self::KEY_ID => $mongoId = new MongoId($vhostId)
        );
        
        if(($vhost = $this->_getVhostCollection()->findOne($query))) {
            $vhost[self::KEY_ID] = (string) $vhost[self::KEY_ID];
        }
        
        return $vhost;
    }
    
    /**
     * get all vhost of a certain client
     * 
     * @param string $clientId
     * @return array
     */
    public function getVhostsByClient($clientId)
    {
        $result = array();
        $query = array(
            self::KEY_CLIENTID => $clientId
        );
        
        foreach($this->_getVhostCollection()->find($query) as $vhostId => $vhost) {
            $vhost[self::KEY_ID] = $vhostId;
            $result[$vhostId] = $vhost;
        }
        
        return $result;
    }
    
    /**
     * get all vhost of a certain domain
     * 
     * @param string $domainId
     * @return array
     */
    public function getVhostsByDomain($domainId)
    {
        $result = array();
        $query = array(
            self::KEY_DOMAINID => $domainId
        );
        
        foreach($this->_getVhostCollection()->find($query) as $vhostId => $vhost) {
            $vhost[self::KEY_ID] = $vhostId;
            $result[$vhostId] = $vhost;
        }
        
        return $result;
    }
    
    /**
     * get all vhost of a certain node
     * 
     * @param string $nodeId
     * @return array
     */
    public function getVhostsByNode($nodeId)
    {
        $result = array();
        $query = array(
            self::KEY_NODEID => $nodeId
        );
        
        foreach($this->_getVhostCollection()->find($query) as $vhostId => $vhost) {
            $vhost[self::KEY_ID] = $vhostId;
            $result[$vhostId] = $vhost;
        }
        
        return $result;
    }
    
    /**
     * get all vhosts
     * 
     * @return array
     */
    public function getVhosts()
    {
        $result = array();
        
        foreach($this->_getVhostCollection()->find() as $vhostId => $vhost) {
            $vhost[self::KEY_ID] = $vhostId;
            $result[$vhostId] = $vhost;
        }
        
        return $result;
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
        $mongoId = new MongoId($vhostId);

        $data[self::KEY_ID] = $mongoId;
        
        $options = array(
            "j" => true
        );
        
        if(!($result = $this->_getVhostCollection()->save($data, $options))) {
            return false;
        }
        
        return (string) $mongoId;
    }
    
}
