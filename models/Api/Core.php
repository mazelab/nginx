<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
class MazelabNginx_Model_Api_Core extends Core_Model_Module_Api_Abstract
{

    /**
     * removes the module collection
     *
     * @return boolean
     */
    public function deinstall()
    {
        return MazelabNginx_Model_DiFactory::getVhostManager()->getProvider()->drop();
    }

    /**
     * returns all domains which are set in a particular module
     * 
     * @return array contains Core_Model_ValueObject_Domain
     */
    public function getDomains()
    {
        return MazelabNginx_Model_DiFactory::getDomainManager()->getDomains();
    }
    
    /**
     * returns all domains of a certain client on a certain node
     * 
     * @param string $nodeId
     * @param string $clientId
     * @return array contains Core_Model_ValueObject_Domain
     */
    public function getDomainsByNode($nodeId, $clientId = null)
    {
        $domains = array();
        foreach(MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByNode($nodeId) as $vhost) {
            if($vhost->getData('domainId') && !key_exists($vhost->getData('domainId'), $domains) && 
                    ($domain = Core_Model_DiFactory::getDomainManager()->getDomain($vhost->getData('domainId')))) {
                $domains[$vhost->getData('domainId')] = $domain;
            }
        }
        
        return $domains;
    }
    
    /**
     * returns all nodes
     * 
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodes()
    {
        return MazelabNginx_Model_DiFactory::getNodeManager()->getNodes();
    }

    /**
     * returns all nodes of a certain domain
     * 
     * @param string $domainId
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodesByDomain($domainId)
    {
        $nodes = array();
        foreach(MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByDomain($domainId) as $vhost) {
            if($vhost->getData('nodeId') && !key_exists($vhost->getData('nodeId'), $nodes) && 
                    ($node = Core_Model_DiFactory::getNodeManager()->getNode($vhost->getData('nodeId')))) {
                $nodes[$vhost->getData('nodeId')] = $node;
            }
        }
        
        return $nodes;
    }
    
    /**
     * returns all nodes of a certain client which are set in a particular module
     * 
     * @param string $clientId 
     * @return array contains Core_Model_ValueObject_Node
     */
    public function getNodesByClient($clientId)
    {
        $nodes = array();
        foreach(MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByClient($clientId) as $vhost) {
            if($vhost->getData('nodeId') && !key_exists($vhost->getData('nodeId'), $nodes) && 
                    ($node = Core_Model_DiFactory::getNodeManager()->getNode($vhost->getData('nodeId')))) {
                $nodes[$vhost->getData('nodeId')] = $node;
            }
        }
        
        return $nodes;
    }
    
    /**
     * returns all clients of a certain node
     * 
     * @param string $nodeId
     * @return array contains Core_Model_ValueObject_Client
     */
    public function getClientsByNode($nodeId)
    {
        $clients = array();
        foreach(MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByNode($nodeId) as $vhost) {
            if($vhost->getData('clientId') && !key_exists($vhost->getData('clientId'), $clients) && 
                    ($client = Core_Model_DiFactory::getClientManager()->getClient($vhost->getData('clientId')))) {
                $clients[$vhost->getData('clientId')] = $client;
            }
        }
        
        return $clients;
    }

    /**
     * process report of a certain node
     * 
     * if false will be returned then the report will be abort
     * 
     * @param string $nodeId
     * @param string $report
     * @return boolean
     */
    public function reportNode($nodeId, $report)
    {
        return MazelabNginx_Model_DiFactory::getReportManager()->reportNode($nodeId, $report);
    }
    
}

