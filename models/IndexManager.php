<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
class MazelabNginx_Model_IndexManager
{

    /**
     * search category string for vhosts
     */
    CONST SEARCH_CATEGORY_VHOST = 'Nginx vhost';
    
    /**
     * Zend_View_Helper_Url
     */
    protected  $_urlHelper;
    
    /**
     * get search index core model
     * 
     * @return Core_Model_Search_Index
     */
    public function _getSearchIndex()
    {
        return Core_Model_DiFactory::getSearchIndex();
    }
    
    /**
     * get zend url helper
     * 
     * @return Zend_View_Helper_Url
     */
    public function _getUrlHelper()
    {
        if(!$this->_urlHelper) {
            $this->_urlHelper = new Zend_View_Helper_Url();
        }
        
        return $this->_urlHelper;
    }
    
    /**
     * builds and save core search index of a certain vhost
     * 
     * @return boolean
     */
    public function setVhost($vhostId)
    {
        if (!($vhost = MazelabNginx_Model_DiFactory::getVhostManager()->getVhost($vhostId))) {
            return false;
        }
        
        $data['id'] = $vhost->getId();
        $data['search'] = $vhost->getLabel();
        $data['teaser'] = $vhost->getLabel();
        $data['headline'] = $vhost->getLabel();
        $data['link'] = $this->_getUrlHelper()->url(array($vhost->getId()), 'mazelab-nginx_editVhost');
        $data['categoryLink'] = $this->_getUrlHelper()->url(array(), 'mazelab-nginx_vhosts');
        
        return $this->_getSearchIndex()->setSearchIndex(self::SEARCH_CATEGORY_VHOST, $vhostId, $data);
    }

    /**
     * builds and save core search index of all vhosts
     * 
     * @return boolean
     */
    public function setVhosts()
    {
        foreach(array_keys(MazelabNginx_Model_DiFactory::getVhostManager()->getVhosts()) as $vhostId) {
            $this->setVhost($vhostId);
        }
    }
    
    /**
     * unsets a certain vhost in core search index
     * 
     * @param string $vhostId
     * @return boolean
     */
    public function unsetVhost($vhostId)
    {
        return $this->_getSearchIndex()->deleteIndex(self::SEARCH_CATEGORY_VHOST, $vhostId);
    }
    
}