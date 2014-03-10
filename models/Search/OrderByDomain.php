<?php
/**
 * nginx
 *
 * @license http://opensource.org/licenses/MIT MIT
 */

class MazelabNginx_Model_Search_OrderByDomain
    extends Core_Model_SearchManager
{
    
    /**
     * returns count from current data set
     * 
     * count deeper struct than originaly
     * 
     * @return int
     */
    public function getCount()
    {
        $count = 0;
        
        if(!$this->getData()) {
            return $count;
        }
        
        foreach($this->getData() as $vhosts) {
            $count = ($count + count($vhosts));
        }
        
        return $count;
    }
    
}