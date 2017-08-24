<?php
class Dreamcode_Tools_Model_Observer  {

    public function preSelectConfigurable($observer)
    {
        Mage::log(__LINE__.' '.__METHOD__, null, 'mylogfile.log');
        $product    = $observer->getEvent()->getProduct();
        $request    = Mage::app()->getRequest();
        $controller = $request->getControllerName();
        $action     = $request->getActionName();
        $candidates = array();

        if (($action === 'view') && ($controller === 'product') && ($product->getTypeId() === 'configurable')) {
            $configurableAttributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
            $usedProducts = $product->getTypeInstance(true)->getUsedProducts(null, $product);
            foreach ($usedProducts as $childProduct) {
                if (!$childProduct->isSaleable()) {
                    continue;
                }
                foreach ($configurableAttributes as $attribute) {
                    $productAttribute   = $attribute->getProductAttribute();
                    $productAttributeId = $productAttribute->getId();
                    $attributeValue     = $childProduct->getData($productAttribute->getAttributeCode());
                    $candidates[$productAttributeId] =  $attributeValue;
                    if($productAttributeId == 132) $candidates[$productAttributeId] =  "3";
                }
                break;
            }
        }

        Mage::log(__LINE__.' '.__METHOD__, null, 'mylogfile.log');
        Mage::log($candidates, null, 'mylogfile.log');

        $preconfiguredValues = new Varien_Object();
        $preconfiguredValues->setData('super_attribute', $candidates);
        $product->setPreconfiguredValues($preconfiguredValues);

    }


    public function preCollectBefore($observer)
    {
        Mage::log(__LINE__.' '.__METHOD__, null, 'mylogfile.log');
        $quoteAddress = $observer->getEvent()->getQuoteAddress();

        foreach ($quoteAddress->getTotalCollector()->getCollectors() as $model) {
            Mage::log(get_class($model), null, 'mylogfile.log');
        }
    }

}
