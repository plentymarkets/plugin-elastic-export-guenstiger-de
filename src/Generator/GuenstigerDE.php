<?php

namespace ElasticExportGuenstigerDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExportGuenstigerDE\Helper\PriceHelper;
use ElasticExportGuenstigerDE\Helper\PropertyHelper;
use ElasticExportGuenstigerDE\Helper\StockHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Item\DataLayer\Models\RecordList;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\Search\Contracts\VariationElasticSearchScrollRepositoryContract;
use Plenty\Plugin\Log\Loggable;


/**
 * Class GuenstigerDE
 * @package ElasticExportGuenstigerDE\Generator
 */
class GuenstigerDE extends CSVPluginGenerator
{
    use Loggable;

    const DELIMITER = "|";

    /**
     * @var ElasticExportCoreHelper
     */
    private $elasticExportHelper;

    /**
     * @var ArrayHelper
     */
    private $arrayHelper;

    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var StockHelper
     */
    private $stockHelper;

    /**
     * @var PropertyHelper
     */
    private $propertyHelper;

    /**
     * GuenstigerDE constructor.
     *
     * @param ArrayHelper $arrayHelper
     * @param PriceHelper $priceHelper
     * @param StockHelper $stockHelper
     * @param PropertyHelper $propertyHelper
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        PriceHelper $priceHelper,
        StockHelper $stockHelper,
        PropertyHelper $propertyHelper
    )
    {
        $this->arrayHelper = $arrayHelper;
        $this->priceHelper = $priceHelper;
        $this->stockHelper = $stockHelper;
        $this->propertyHelper = $propertyHelper;
    }

    /**
     * Generates and populates the data into the CSV file.
     *
     * @param VariationElasticSearchScrollRepositoryContract $elasticSearch
     * @param array $formatSettings
     * @param array $filter
     */
    protected function generatePluginContent($elasticSearch, array $formatSettings = [], array $filter = [])
    {
        $this->elasticExportHelper = pluginApp(ElasticExportCoreHelper::class);

        $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

        // Delimiter accepted is PIPE
        $this->setDelimiter(self::DELIMITER);

        // Add the header of the CSV file
        $this->addCSVContent($this->head());

        $startTime = microtime(true);

        if($elasticSearch instanceof VariationElasticSearchScrollRepositoryContract)
        {
            // Initiate the counter for the variations limit
            $limitReached = false;
            $limit = 0;

            do
            {
                // Stop writing if limit is reached
                if($limitReached === true)
                {
                    break;
                }

                $this->getLogger(__METHOD__)->debug('ElasticExportGuenstigerDE::log.writtenLines', [
                    'Lines written' => $limit,
                ]);

                $esStartTime = microtime(true);

                // Get the data from Elastic Search
                $resultList = $elasticSearch->execute();

                $this->getLogger(__METHOD__)->debug('ElasticExportGuenstigerDE::log.esDuration', [
                    'Elastic Search duration' => microtime(true) - $esStartTime,
                ]);

                if(count($resultList['error']) > 0)
                {
                    $this->getLogger(__METHOD__)->error('ElasticExportGuenstigerDE::log.occurredElasticSearchErrors', [
                        'Error message' => $resultList['error'],
                    ]);
                }

                $buildRowsStartTime = microtime(true);

                if(is_array($resultList['documents']) && count($resultList['documents']) > 0)
                {
                    foreach ($resultList['documents'] as $variation)
                    {
                        // Stop and set the flag if limit is reached
                        if($limit == $filter['limit'])
                        {
                            $limitReached = true;
                            break;
                        }

                        // If filtered by stock is set and stock is negative, then skip the variation
                        if($this->stockHelper->isFilteredByStock($variation, $filter) === true)
                        {
                            $this->getLogger(__METHOD__)->info('ElasticExportGuenstigerDE::log.variationNotPartOfExportStock', [
                                'VariationId' => (string)$variation['id']
                            ]);

                            continue;
                        }

                        // New line printed in the CSV file
                        $this->buildRow($variation, $settings);

                        // Count the new printed line
                        $limit += 1;
                    }

                    $this->getLogger(__METHOD__)->debug('ElasticExportGuenstigerDE::log.buildRowsDuration', [
                        'Build rows duration' => microtime(true) - $buildRowsStartTime,
                    ]);
                }

            } while ($elasticSearch->hasNext());
        }

        $this->getLogger(__METHOD__)->debug('ElasticExportGuenstigerDE::log.fileGenerationDuration', [
            'Whole file generation duration' => microtime(true) - $startTime,
        ]);
    }

    /**
     * Creates the header of the CSV file.
     *
     * @return array
     */
    private function head():array
    {
        return array(
            'bezeichnung',
            'preis',
            'deeplink',
            'ean',
            'beschreibung',
            'bilderlink',
            'lieferzeiten',
            'lieferkosten',
            'grundpreis',
        );
    }

    /**
     * Creates the variation row and prints it into the CSV file.
     *
     * @param array $variation
     * @param KeyValue $settings
     */
    private function buildRow($variation, KeyValue $settings)
    {
        $this->getLogger(__METHOD__)->debug('ElasticExportGuenstigerDE::log.variationConstructRow', [
            'Data row duration' => 'Row printing start'
        ]);

        $rowTime = microtime(true);

        try
        {
            // Get the price list
            $priceList = $this->priceHelper->getPriceList($variation, $settings);

            // Only variations with the Retail Price greater than zero will be handled
            if($priceList['variationRetailPrice.price'] > 0)
            {
                // Get delivery costs
                $shippingCost = $this->getDeliveryCosts($variation, $settings);

                // Get base price
                $basePrice = number_format((float)$this->elasticExportHelper->getBasePrice($variation, $priceList, $settings->get('lang')), 2, '.', '');

                $data = [
                    'bezeichnung'   => $this->elasticExportHelper->getName($variation, $settings, 256),
                    'preis'         => number_format((float)$priceList['variationRetailPrice.price'], 2, '.', ''),
                    'deeplink'      => $this->elasticExportHelper->getUrl($variation, $settings, true, false),
                    'ean'           => $this->elasticExportHelper->getBarcodeByType($variation, $settings->get('barcode')),
                    'beschreibung'  => $this->elasticExportHelper->getDescription($variation, $settings, 256),
                    'bilderlink'    => $this->elasticExportHelper->getMainImage($variation, $settings),
                    'lieferzeiten'  => $this->elasticExportHelper->getAvailability($variation, $settings),
                    'lieferkosten'  => $shippingCost,
                    'grundpreis'    => strlen($basePrice) ? $basePrice : '',
                ];

                $this->addCSVContent(array_values($data));

                $this->getLogger(__METHOD__)->debug('ElasticExportGuenstigerDE::log.variationConstructRowFinished', [
                    'Data row duration' => 'Row printing took: ' . (microtime(true) - $rowTime),
                ]);
            }
            else
            {
                $this->getLogger(__METHOD__)->info('ElasticExportGuenstigerDE::log.variationNotPartOfExportPrice', [
                    'VariationId' => (string)$variation['id']
                ]);
            }
        }
        catch (\Throwable $throwable)
        {
            $this->getLogger(__METHOD__)->error('ElasticExportGuenstigerDE::log.fillRowError', [
                'Error message' => $throwable->getMessage(),
                'Error line'    => $throwable->getLine(),
                'VariationId'   => $variation['id']
            ]);
        }
    }

    /**
     * Get the delivery costs for a variation.
     *
     * @param $variation
     * @param KeyValue $settings
     * @return string
     */
    private function getDeliveryCosts($variation, KeyValue $settings):string
    {
        $shippingCost = $this->elasticExportHelper->getShippingCost($variation['data']['item']['id'], $settings);

        if(!is_null($shippingCost))
        {
            return number_format((float)$shippingCost, 2, '.', '');
        }

        return '';
    }
}
