<?php

namespace ElasticExportGuenstigerDE\IDL_ResultList;

use Plenty\Modules\Item\DataLayer\Contracts\ItemDataLayerRepositoryContract;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\DataLayer\Models\RecordList;


/**
 * Class GuenstigerDE
 * @package ElasticExportGuenstigerDE\IDL_ResultList
 */
class GuenstigerDE
{
    /**
     * Creates and retrieves the extra needed data from ItemDataLayer.
     *
     * @param array $variationIds
     * @param KeyValue $settings
     * @return RecordList|string
     */
    public function getResultList($variationIds, $settings)
    {
        if(is_array($variationIds) && count($variationIds) > 0)
        {
            $searchFilter = array(
                'variationBase.hasId' => array(
                    'id' => $variationIds
                )
            );

            $resultFields = array(

                'itemBase' => array(
                    'id',
                ),

                'variationBase' => array(
                    'id',
                ),

                'variationRetailPrice' => array(
                    'params' => array(
                        'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : -1,
                    ),
                    'fields' => array(
                        'price',    // price
                    ),
                ),
            );

            $itemDataLayer = pluginApp(ItemDataLayerRepositoryContract::class);
            /**
             * @var ItemDataLayerRepositoryContract $itemDataLayer
             */
            $itemDataLayer = $itemDataLayer->search($resultFields, $searchFilter);

            return $itemDataLayer;
        }

        return '';
    }
}