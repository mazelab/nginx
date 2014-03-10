<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_DiFactory
{

    /**
     * @var MazelabNginx_Model_Apply_Vhost
     */
    static protected $_applyVhost;
    
    /**
     * @var MazelabNginx_Model_ClientManager
     */
    static protected $_clientManager;
    
    /**
     * @var MazelabNginx_Model_DomainManager
     */
    static protected $_domainManager;
    
    /**
     * @var MazelabNginx_Model_IndexManager
     */
    static protected $_indexManager;
    
    /**
     * @var MazelabNginx_Model_NodeManager
     */
    static protected $_nodeManager;
    
    /**
     * @var MazelabNginx_Model_ReportManager
     */
    static protected $_reportManager;
    
    /**
     * @var Core_Model_SearchManager
     */
    static protected $_searchVhosts;
    
    /**
     * @var Core_Model_SearchManager
     */
    static protected $_searchVhostsByClient;
    
    /**
     * contains instances of MazelabNginx_Model_ValueObject_Vhost
     * 
     * @var array
     */
    static protected $_vhosts;
    
    /**
     * @var MazelabNginx_Model_VhostManager
     */
    static protected $_vhostManager;

    /**
     * get actual instance of MazelabNginx_Model_Apply_Vhost
     * 
     * @return MazelabNginx_Model_Apply_Vhost
     */
    static public function getApplyVhost()
    {
        if (!self::$_applyVhost instanceof MazelabNginx_Model_Apply_Vhost) {
            self::$_applyVhost = self::newApplyVhost();
        }

        return self::$_applyVhost;
    }    
    
    /**
     * get actual instance of MazelabNginx_Model_ClientManager
     * 
     * @return MazelabNginx_Model_ClientManager
     */
    static public function getClientManager()
    {
        if (!self::$_clientManager instanceof MazelabNginx_Model_ClientManager) {
            self::$_clientManager = self::newClientManager();
        }

        return self::$_clientManager;
    }
    
    /**
     * get actual instance of MazelabNginx_Model_DomainManager
     * 
     * @return MazelabNginx_Model_DomainManager
     */
    static public function getDomainManager()
    {
        if (!self::$_domainManager instanceof MazelabNginx_Model_DomainManager) {
            self::$_domainManager = self::newDomainManager();
        }

        return self::$_domainManager;
    }
    
    /**
     * get actual instance of MazelabNginx_Model_IndexManager
     * 
     * @return MazelabNginx_Model_IndexManager
     */
    static public function getIndexManager()
    {
        if (!self::$_indexManager instanceof MazelabNginx_Model_IndexManager) {
            self::$_indexManager = self::newIndexManager();
        }

        return self::$_indexManager;
    }
    
    /**
     * get actual instance of MazelabNginx_Model_NodeManager
     * 
     * @return MazelabNginx_Model_NodeManager
     */
    static public function getNodeManager()
    {
        if (!self::$_nodeManager instanceof MazelabNginx_Model_NodeManager) {
            self::$_nodeManager = self::newNodeManager();
        }

        return self::$_nodeManager;
    }
    
    /**
     * get actual instance of MazelabNginx_Model_ReportManager
     * 
     * @return MazelabNginx_Model_ReportManager
     */
    static public function getReportManager()
    {
        if (!self::$_reportManager instanceof MazelabNginx_Model_ReportManager) {
            self::$_reportManager = self::newReportManager();
        }

        return self::$_reportManager;
    }
    
    /**
     * returns instance of Core_Model_SearchManager with dataprovider 
     * MazelabNginx_Model_Dataprovider_DiFactory::getSearchVhosts()
     * 
     * @return Core_Model_SearchManager
     */
    static public function getSearchVhosts()
    {
        if (!self::$_searchVhosts instanceof Core_Model_SearchManager) {
            self::$_searchVhosts = self::newSearchVhosts();
        }

        return self::$_searchVhosts;
    }
    
    /**
     * returns instance of Core_Model_SearchManager with dataprovider 
     * MazelabNginx_Model_Dataprovider_DiFactory::getSearchVhosts()
     * 
     * @param string $clientId
     * @return MazelabNginx_Model_Search_OrderByDomain
     */
    static public function getSearchVhostsByClient($clientId)
    {
        if (!self::$_searchVhostsByClient instanceof MazelabNginx_Model_Search_OrderByDomain) {
            self::$_searchVhostsByClient = self::newSearchVhostsByClient($clientId);
        }

        return self::$_searchVhostsByClient;
    }
    
    /**
     * get actual instance of MazelabNginx_Model_VhostManager
     * 
     * @return MazelabNginx_Model_VhostManager
     */
    static public function getVhostManager()
    {
        if (!self::$_vhostManager instanceof MazelabNginx_Model_VhostManager) {
            self::$_vhostManager = self::newVhostManager();
        }

        return self::$_vhostManager;
    }
    
    /**
     * get new instance of MazelabNginx_Model_Apply_Vhost
     * 
     * @return MazelabNginx_Model_Apply_Vhost
     */
    static public function newApplyVhost()
    {
        return new MazelabNginx_Model_Apply_Vhost();
    }
    
    /**
     * get new instance of MazelabNginx_Model_ClientManager
     * 
     * @return MazelabNginx_Model_ClientManager
     */
    static public function newClientManager()
    {
        return new MazelabNginx_Model_ClientManager();
    }
    
    /**
     * get new instance of MazelabNginx_Model_DomainManager
     * 
     * @return MazelabNginx_Model_DomainManager
     */
    static public function newDomainManager()
    {
        return new MazelabNginx_Model_DomainManager();
    }
    
    /**
     * get new instance of MazelabNginx_Model_NodeManager
     * 
     * @return MazelabNginx_Model_NodeManager
     */
    static public function newNodeManager()
    {
        return new MazelabNginx_Model_NodeManager();
    }
    
    /**
     * get new instance of MazelabNginx_Model_ReportManager
     * 
     * @return MazelabNginx_Model_ReportManager
     */
    static public function newReportManager()
    {
        return new MazelabNginx_Model_ReportManager();
    }
    
    /**
     * get new instance of MazelabNginx_Model_IndexManager
     * 
     * @return MazelabNginx_Model_IndexManager
     */
    static public function newIndexManager()
    {
        return new MazelabNginx_Model_IndexManager();
    }
    
    /**
     * returns new instance of Core_Model_SearchManager with the dataprovider
     * MazelabNginx_Model_DataProvider_DiFactory::getSearchVhosts()
     * 
     * @return Core_Model_SearchManager
     */
    static public function newSearchVhosts()
    {
        $searchVhosts = new Core_Model_SearchManager();
        $searchVhosts->setProvider(MazelabNginx_Model_Dataprovider_DiFactory::getSearchVhosts());
        
        return $searchVhosts;
    }
    
    /**
     * returns new instance of Core_Model_SearchManager with the dataprovider
     * MazelabNginx_Model_DataProvider_DiFactory::getSearchVhosts()
     * 
     * @param string $clientId
     * @return MazelabNginx_Model_Search_OrderByDomain
     */
    static public function newSearchVhostsByClient($clientId)
    {
        $searchVhosts = new MazelabNginx_Model_Search_OrderByDomain();
        $searchVhosts->setProvider(MazelabNginx_Model_Dataprovider_DiFactory::getSearchVhostsByClient($clientId));
        
        return $searchVhosts;
    }
    
    /**
     * gets new MazelabNginx_Model_ValueObject_Vhost instance
     * 
     * @param string $vhostId
     * @return MazelabNginx_Model_ValueObject_Vhost
     */
    static public function newVhost($vhostId = null)
    {
        return new MazelabNginx_Model_ValueObject_Vhost($vhostId);
    }
    
    /**
     * get new instance of MazelabNginx_Model_VhostManager
     * 
     * @return MazelabNginx_Model_VhostManager
     */
    static public function newVhostManager()
    {
        return new MazelabNginx_Model_VhostManager();
    }
    
    /**
     * get new instance of Core_Model_SearchManager with initialized vhost search
     * of a certain client
     * 
     * @return Core_Model_SearchManager
     */
    static public function newVhostPager()
    {
        $pager = Core_Model_DiFactory::newSearchManager();
        
        $pager->setProvider(MazelabNginx_Model_Dataprovider_DiFactory::newVhostPager());
        
        return $pager;
    }
    
    /**
     * sets instance of MazelabNginx_Model_Apply_Vhost
     * 
     * @param MazelabNginx_Model_Apply_Vhost|null $applyVhost 
     */
    static public function setApplyVhost(MazelabNginx_Model_Apply_Vhost $applyVhost = null)
    {
        self::$_applyVhost = $applyVhost;
    }
    
    /**
     * sets instance of MazelabNginx_Model_ClientManager
     * 
     * @param MazelabNginx_Model_ClientManager|null $clientManager 
     */
    static public function setClientManager(MazelabNginx_Model_ClientManager $clientManager = null)
    {
        self::$_clientManager = $clientManager;
    }
    
    /**
     * sets instance of MazelabNginx_Model_VhostManager
     * 
     * @param MazelabNginx_Model_VhostManager|null $domainManager 
     */
    static public function setDomainManager(MazelabNginx_Model_DomainManager $domainManager = null)
    {
        self::$_domainManager = $domainManager;
    }
    
    /**
     * sets instance of MazelabNginx_Model_NodeManager
     * 
     * @param MazelabNginx_Model_NodeManager|null $nodeManager 
     */
    static public function setNodeManager(MazelabNginx_Model_NodeManager $nodeManager = null)
    {
        self::$_nodeManager = $nodeManager;
    }
    
    /**
     * sets instance of MazelabNginx_Model_ReportManager
     * 
     * @param MazelabNginx_Model_ReportManager|null $reportManager 
     */
    static public function setReportManager(MazelabNginx_Model_ReportManager $reportManager = null)
    {
        self::$_reportManager = $reportManager;
    }
    
    /**
     * sets instance of MazelabNginx_Model_VhostManager
     * 
     * @param MazelabNginx_Model_VhostManager|null $vhostManager 
     */
    static public function setVhostManager(MazelabNginx_Model_VhostManager $vhostManager= null)
    {
        self::$_vhostManager = $vhostManager;
    }
    
}
