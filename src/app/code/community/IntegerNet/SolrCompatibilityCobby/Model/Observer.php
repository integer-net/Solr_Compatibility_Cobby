<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_SolrCompatibilityCobby
 * @copyright  Copyright (c) 2016 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_SolrCompatibilityCobby_Model_Observer
{
    /**
     * Reindexes IntegerNet_Solr after Cobby import
     *
     * @param Varien_Event_Observer $observer
     * @event cobby_after_product_import
     */
    public function reindexSolrAfterCobbyImport(Varien_Event_Observer $observer)
    {
        /** @var $indexer Mage_Index_Model_Process */
        $indexer = Mage::getModel('index/process')->load('integernet_solr', 'indexer_code');
        if ($indexer->getMode() != Mage_Index_Model_Process::MODE_REAL_TIME) {
            return;
        }
        
        $productIds = array();
        $transport = $observer->getEvent()->getTransport();
        foreach ($transport->getRows() as $row) {
            if(isset($row['_id'])) {
                $productIds[] = $row['_id'];
            }
        }

        if (empty($productIds)) {
            return;
        }
        
        Mage::helper('integernet_solr')->factory()->getProductIndexer()->reindex($productIds);
    }
}
