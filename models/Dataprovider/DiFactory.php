<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Dataprovider_DiFactory
{

    /**
     * default adapter for class building
     */
    CONST DEFAULT_ADAPTER = 'Core';
    
    /**
     * class prefix for provider class building
     */
    CONST PROVIDER_CLASS_PATH_PRE = 'MazelabNginx_Model_Dataprovider_';
    
    /**
     * current adapter for data provider
     * 
     * @var string
     */
    static protected $_adapter;
    
    /**
     * search instance
     * 
     * @var Core_Model_Dataprovider_Interface_Search
     */
    static protected $_searchVhosts;
    
    /**
     * search by client instance
     * 
     * @var Core_Model_Dataprovider_Interface_Search
     */
    static protected $_searchVhostsByClient;
    
    /**
     * current instance of vhost provider
     *
     * @var MazelabNginx_Model_Dataprovider_Interface_Vhost 
     */
    static protected $_vhost;
    
    /**
     * returns the current adapter
     * 
     * @return string
     */
    static public function getAdapter()
    {
        if (is_null(self::$_adapter)) {
            self::setAdapter(self::DEFAULT_ADAPTER);
        }

        return self::$_adapter;
    }
    
    /**
     * get search vhost instance
     * 
     * @return Core_Model_Dataprovider_Interface_Search
     */
    static public function getSearchVhosts()
    {
        if (!self::$_searchVhosts instanceof Core_Model_Dataprovider_Interface_Search) {
            self::$_searchVhosts = self::newSearchVhosts();
        }

        return self::$_searchVhosts;
    }
    
    /**
     * get search vhost instance
     * 
     * @param string $clientId
     * @return Core_Model_Dataprovider_Interface_Search
     */
    static public function getSearchVhostsByClient($clientId)
    {
        if (!self::$_searchVhostsByClient instanceof Core_Model_Dataprovider_Interface_Search) {
            self::$_searchVhostsByClient = self::newSearchVhostsByClient($clientId);
        }

        return self::$_searchVhostsByClient;
    }
    
    /**
     * get vhost instance
     * 
     * @return MazelabNginx_Model_Dataprovider_Interface_Vhost
     */
    static public function getVhost()
    {
        if (!self::$_vhost instanceof MazelabNginx_Model_Dataprovider_Interface_Vhost) {
            self::$_vhost = self::newVhost();
        }

        return self::$_vhost;
    }
    
    /**
     * returns new search instance
     * 
     * @return Core_Model_Dataprovider_Interface_Search
     * @throws Core_Model_DataProvider_Exception
     */
    static public function newSearchVhosts()
    {
        $currentAdapter = self::getAdapter();
        $className = self::PROVIDER_CLASS_PATH_PRE . $currentAdapter . '_Search';

        $newOne = new $className();
        if ($newOne instanceof Core_Model_Dataprovider_Interface_Search) {
            return $newOne;
        }
        
        throw new Core_Model_DataProvider_Exception(
            'The data provider: ' . $currentAdapter . ' doesn\'t have a valid search implementation.'
        );
    }
    
    /**
     * returns new search by client instance
     * 
     * @param string $clientId
     * @return Core_Model_Dataprovider_Interface_Search
     * @throws Core_Model_DataProvider_Exception
     */
    static public function newSearchVhostsByClient($clientId)
    {
        $currentAdapter = self::getAdapter();
        $className = self::PROVIDER_CLASS_PATH_PRE . $currentAdapter . '_SearchByClient';

        $newOne = new $className($clientId);
        if ($newOne instanceof Core_Model_Dataprovider_Interface_Search) {
            return $newOne;
        }
        
        throw new Core_Model_DataProvider_Exception(
            'The data provider: ' . $currentAdapter . ' doesn\'t have a valid search by client implementation.'
        );
    }
    
    /**
     * create vhost instance
     * 
     * @return MazelabNginx_Model_Dataprovider_Interface_Vhost
     * @throws MazelabNginx_Model_DataProvider_Exception
     */
    static public function newVhost()
    {
        $currentAdapter = self::getAdapter();
        $className = self::PROVIDER_CLASS_PATH_PRE . $currentAdapter . '_Vhost';

        $newOne = new $className();
        if ($newOne instanceof MazelabNginx_Model_Dataprovider_Interface_Vhost) {
            return $newOne;
        }

        throw new MazelabNginx_Model_DataProvider_Exception(
            'The data provider: ' . $currentAdapter . ' doesn\'t have a valid vhost implementation.'
        );
    }
    
    /**
     * create specials pager instance
     * 
     * @return Core_Model_Dataprovider_Interface_Search
     * @throws MazelabNginx_Model_DataProvider_Exception
     */
    static public function newVhostPager()
    {
        $currentAdapter = self::getAdapter();
        $className = self::PROVIDER_CLASS_PATH_PRE . $currentAdapter . '_Pager_Vhosts';

        $newOne = new $className();
        if ($newOne instanceof Core_Model_Dataprovider_Interface_Search) {
            return $newOne;
        }
        
        throw new Core_Model_DataProvider_Exception(
            'The data provider: ' . $currentAdapter . ' doesn\'t have a valid pager vhosts implementation.'
        );
    }
    
    /**
     * resets instance
     */
    static public function reset()
    {
        self::setVhost();
    }
    
    /**
     * sets adapter for the dataprovider
     * 
     * if no adapter is given it will reset the current adapter to default
     * 
     * @param string $adapter
     */
    static public function setAdapter($adapter = null)
    {
        if (self::$_adapter) {
            self::reset();
        }

        self::$_adapter = $adapter;
    }
    
    /**
     * set vhost instance
     * 
     * @param MazelabNginx_Model_Dataprovider_Interface_Vhost $vhost
     */
    static public function setVhost(MazelabNginx_Model_Dataprovider_Interface_Vhost $vhost = null)
    {
        self::$_vhost = $vhost;
    }
    
}
