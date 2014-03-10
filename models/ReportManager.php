<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_ReportManager
{
    
    /**
     * imports new vhost from node report
     * 
     * @param string $nodeId
     * @param array $data
     * @return boolean
     */
    protected function _importVhost($nodeId, array $data)
    {
        $form = new MazelabNginx_Form_AddVhost();
        $form->initNodeSelect()->initDomainSelect();
        
        $data['nodeId'] = $nodeId;
        if(key_exists('domain', $data) && $data['domain'] &&
                ($domain = Core_Model_DiFactory::getDomainManager()->getDomainByName($data['domain']))) {
            $data['domainId'] = $domain->getId();
        }
        
        if(!$form->isValid($data)) {
            return false;
        }
        
        $form->getElement("status")->setValue($data["status"] == "false" ? 0 : 1);
        $nginxManager = MazelabNginx_Model_DiFactory::getVhostManager();
        if(!($vhostId = $nginxManager->addVhost($form->getValues())) ||
                !($vhost = $nginxManager->getVhost($vhostId))) {
            return false;
        }

        return $vhost->evalReport($data);
    }
    
    /**
     * process report of a certain node
     * 
     * @param string $nodeId
     * @param string $report
     * @return boolean
     */
    public function reportNode($nodeId, $report)
    {
        if(!($node = Core_Model_DiFactory::getNodeManager()->getNode($nodeId)) ||
                !$node->hasService(MazelabNginx_Model_DomainManager::MODULE_NAME)) {
            return false;
        }
        
        if(!($reportedVhosts = json_decode($report, true))) {
            $reportedVhosts = array();
        }

        $unknownVhosts = $reportedVhosts;
        $vhosts = MazelabNginx_Model_DiFactory::getVhostManager()->getVhostsByNode($nodeId);
        foreach($vhosts as $vhost) {
            $md5Vhost = md5($vhost->getLabel());
            if(key_exists($md5Vhost, $reportedVhosts)) {
                unset($unknownVhosts[$md5Vhost]);
            } else {
                $reportedVhosts[$md5Vhost] = array();
            }
            
            $vhost->evalReport($reportedVhosts[$md5Vhost]);
        }
        
        if($unknownVhosts) {
            foreach($unknownVhosts as $vhost) {
                $this->_importVhost($nodeId, $vhost);
            }
        }
        
        return true;
    }
    
}
