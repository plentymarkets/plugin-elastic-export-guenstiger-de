<?php

namespace ElasticExportGuenstigerDE\Generator;

use ElasticExportCore\Helper\ElasticExportCoreHelper;
use Plenty\Modules\DataExchange\Contracts\CSVGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Item\DataLayer\Models\RecordList;
use Plenty\Modules\Helper\Models\KeyValue;


/**
 * Class GuenstigerDE
 * @package ElasticExportGuenstigerDE\Generator
 */
class GuenstigerDE extends CSVGenerator
{
    const DELIMITER = '|';
    
    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportCoreHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var array $idlVariations
     */
    private $idlVariations = [];
    

    /**
     * GuenstigerDE constructor.
     * 
     * @param ElasticExportCoreHelper $elasticExportCoreHelper
     * @param ArrayHelper $arrayHelper
     */
    public function __construct(ElasticExportCoreHelper $elasticExportCoreHelper, ArrayHelper $arrayHelper)
    {
        $this->elasticExportCoreHelper = $elasticExportCoreHelper;
        $this->arrayHelper = $arrayHelper;
    }

    /**
     * Generates and populates the data into the CSV file.
     * 
     * @param mixed $resultData
     * @param array $formatSettings
     */
    protected function generateContent($resultData, array $formatSettings = [])
    {
        if(is_array($resultData) && count($resultData['documents']) > 0)
        {
            $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

            $this->setDelimiter(self::DELIMITER);

            $this->addCSVContent([
                'bezeichnung',
                'preis',
                'deeplink',
                'ean',
                'beschreibung',
                'bilderlink',
                'lieferzeiten',
                'lieferkosten',
                'grundpreis',
            ]);

            //Generates a RecordList form the ItemDataLayer for the given variations
            $idlResultList = $this->generateIdlList($resultData, $settings);

            //Creates an array with the variationId as key to surpass the sorting problem
            if(isset($idlResultList) && $idlResultList instanceof RecordList)
            {
                $this->createIdlArray($idlResultList);
            }

            foreach($resultData['documents'] as $variation)
            {
                $shippingCost = $this->elasticExportCoreHelper->getShippingCost($variation['data']['item']['id'], $settings);
                $basePrice = number_format((float)$this->elasticExportCoreHelper->getBasePrice($variation, $this->idlVariations[$variation['id']], $settings->get('lang')), 2, '.', '');

                if(!is_null($shippingCost))
                {
                    $shippingCost = number_format((float)$shippingCost, 2, '.', '');
                }
                else
                {
                    $shippingCost = '';
                }

                $data = [
                    'bezeichnung'   => $this->elasticExportCoreHelper->getName($variation, $settings, 256),
                    'preis'         => number_format((float)$this->idlVariations[$variation['id']]['variationRetailPrice.price'], 2, '.', ''),
                    'deeplink'      => $this->elasticExportCoreHelper->getUrl($variation, $settings, true, false),
                    'ean'           => $this->elasticExportCoreHelper->getBarcodeByType($variation, $settings->get('barcode')),
                    'beschreibung'  => $this->elasticExportCoreHelper->getDescription($variation, $settings, 256),
                    'bilderlink'    => $this->elasticExportCoreHelper->getMainImage($variation, $settings),
                    'lieferzeiten'  => $this->elasticExportCoreHelper->getAvailability($variation, $settings),
                    'lieferkosten'  => $shippingCost,
                    'grundpreis'    => strlen($basePrice) ? $basePrice : '',
                ];

                $this->addCSVContent(array_values($data));
            }
        }
    }

    /**
     * Creates a list of Records from the given variations.
     *
     * @param array     $resultData
     * @param KeyValue  $settings
     * @return RecordList|string
     */
    private function generateIdlList($resultData, $settings)
    {
        //Create a List of all VariationIds
        $variationIdList = array();
        foreach($resultData['documents'] as $variation)
        {
            $variationIdList[] = $variation['id'];
        }

        //Get the missing fields in ES from IDL(ItemDataLayer)
        if(is_array($variationIdList) && count($variationIdList) > 0)
        {
            /**
             * @var \ElasticExportGuenstigerDE\IDL_ResultList\GuenstigerDE $idlResultList
             */
            $idlResultList = pluginApp(\ElasticExportGuenstigerDE\IDL_ResultList\GuenstigerDE::class);

            //Return the list of results for the given variation ids
            return $idlResultList->getResultList($variationIdList, $settings);
        }

        return '';
    }

    /**
     * Creates an array with the rest of data needed from the IDL(ItemDataLayer).
     *
     * @param RecordList $idlResultList
     */
    private function createIdlArray($idlResultList)
    {
        if($idlResultList instanceof RecordList)
        {
            foreach($idlResultList as $idlVariation)
            {
                if($idlVariation instanceof Record)
                {
                    $this->idlVariations[$idlVariation->variationBase->id] = [
                        'itemBase.id' => $idlVariation->itemBase->id,
                        'variationBase.id' => $idlVariation->variationBase->id,
                        'variationRetailPrice.price' => $idlVariation->variationRetailPrice->price,
                    ];
                }
            }
        }
    }
}
