<?php

/**
 * Auto Expand Product Category Tree
 *
 * @author     Johannes Klein <johannes.klein@kiwigrid.com>
 * @license    http://kiwigrid.mit-license.org/
 */

class Kiwigrid_AutoExpandProductCategoryTree_Block_Catalog_Product_Edit_Tab_Categories extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Categories
{
    protected $_AllParentCategoriesIds;

    /**
     * Sets global
     * Returns array of IDs of all parent categories
     *
     * @return array
     */
    public function getAllParentCategoriesIds()
    {
        if (is_null($this->_AllParentCategoriesIds)) {
            $this->_AllParentCategoriesIds = array();
            foreach ($this->getCategoryIds() as $id) {
                $storeId = (int) $this->getRequest()->getParam('store');
                $path = Mage::getResourceModel('catalog/category')
                    ->getAttributeRawValue($id, 'path', $storeId);
                if (empty($path)) {
                    $path = "";
                }
                $parentIds = explode("/", $path,-1);
                if (!(is_array($parentIds) && count($parentIds) > 0)) {
                    $parentIds = array();
                }
                $this->_AllParentCategoriesIds = array_unique(array_merge($this->_AllParentCategoriesIds, $parentIds));
            }
        }
        
        return $this->_AllParentCategoriesIds;
    }

    /**
     * Returns array with configuration of current node
     *
     * @param Varien_Data_Tree_Node $node
     * @param int                   $level How deep is the node in the tree
     * @return array
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);

        if ($this->_isParentSelectedCategory($node)) {
            $item['expanded'] = true;
        }

        // Expand Category if there are child elements that are selected
        if (in_array($node->getId(), $this->getAllParentCategoriesIds())) {
            $item['expanded'] = true;
        }

        if (in_array($node->getId(), $this->getCategoryIds())) {
            $item['checked'] = true;
        }

        if ($this->isReadonly()) {
            $item['disabled'] = true;
        }

        return $item;
    }
}