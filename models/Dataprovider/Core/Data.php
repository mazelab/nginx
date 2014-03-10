<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Dataprovider_Core_Data
{
    
    /**
     * name of the used collection
     */
    CONST COLLECTION = 'nginx_vhosts';

    /**
     * name of the client id key
     */
    CONST KEY_CLIENTID = 'clientId';

    /**
     * name of the domain id key
     */
    CONST KEY_DOMAINID = 'domainId';
    
    /**
     * name of the domain name key
     */
    CONST KEY_DOMAINNAME = 'domainName';
    
    /**
     * name of the id key
     */
    CONST KEY_ID = '_id';
    
    /**
     * name of the label key
     */
    CONST KEY_LABEL = 'label';
    
    /**
     * name of the nodeId key
     */
    CONST KEY_NODEID = 'nodeId';
    
    /**
     * @var MongoDb_Mongo
     */
    protected $_mongoDb;

    /**
     * @var MongoCollection
     */
    protected $_vhostCollection;
    
    /**
     * init mongo db
     */
    public function __construct()
    {
        $this->_mongoDb = Core_Model_DiFactory::getMongoDb();
    }
    
    /**
     * gets mongo db
     * 
     * @return MongoDb_Mongo
     */
    protected function _getDatabase()
    {
        return $this->_mongoDb;
    }

    /**
     * get vhost collection
     * 
     * @return MongoCollection
     */
    protected function _getVhostCollection()
    {
        if (!$this->_vhostCollection instanceof MongoCollection) {
            $this->_vhostCollection = $this->_getDatabase()->getCollection(self::COLLECTION);
        }

        return $this->_vhostCollection;
    }

}
