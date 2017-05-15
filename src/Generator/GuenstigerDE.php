<?php

namespace ElasticExportGuenstigerDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use ElasticExport\Helper\ElasticExportStockHelper;
use ElasticExportGuenstigerDE\Helper\PriceHelper;
use ElasticExportGuenstigerDE\Helper\StockHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
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
     * @var ElasticExportStockHelper
     */
    private $stockHelper;

    /**
     * GuenstigerDE constructor.
     *
     * @param ArrayHelper $arrayHelper
     * @param PriceHelper $priceHelper
     * @param ElasticExportStockHelper $stockHelper
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        PriceHelper $priceHelper,
        ElasticExportStockHelper $stockHelper
    )
    {
        $this->arrayHelper = $arrayHelper;
        $this->priceHelper = $priceHelper;
        $this->stockHelper = $stockHelper;
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
            'EAN',
            'ISBN',
            'HerstellerArtNr',
            'Hersteller',
            'Produktname',
            'Beschreibung',
            'Preis',
            'Klick-Out-URL',
            'Kategorie',
            'Bild-URL',
            'Lieferzeit',
            'Lieferkosten',
            'Grundpreis',
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
                $deliveryCost = $this->getDeliveryCosts($variation, $settings);
                $deliveryTime = $this->getDeliveryTime($variation, $settings);

                // Get base price
                $basePrice = $this->elasticExportHelper->getBasePrice($variation, $priceList, $settings->get('lang'));

                $data = [
                    'EAN'               => $this->elasticExportHelper->getBarcodeByType($variation, $settings->get('barcode')),
                    'ISBN'              => $this->elasticExportHelper->getBarcodeByType($variation, ElasticExportCoreHelper::BARCODE_ISBN),
                    'HerstellerArtNr'   => strlen($variation['data']['variation']['model']) ? $variation['data']['variation']['model'] : '',
                    'Hersteller'        => $this->elasticExportHelper->getExternalManufacturerName((int)$variation['data']['item']['manufacturer']['id']),
                    'Produktname'       => $this->elasticExportHelper->getMutatedName($variation, $settings, 256),
                    'Beschreibung'      => $this->elasticExportHelper->getMutatedDescription($variation, $settings, 256),
                    'Preis'             => number_format((float)$priceList['variationRetailPrice.price'], 2, '.', ''),
                    'Klick-Out-URL'     => $this->elasticExportHelper->getMutatedUrl($variation, $settings, true, false),
                    'Kategorie'         => $this->elasticExportHelper->getCategory((int)$variation['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
                    'Bild-URL'          => $this->elasticExportHelper->getMainImage($variation, $settings),
                    'Lieferzeit'        => $deliveryTime,
                    'Lieferkosten'      => $deliveryCost,
                    'Grundpreis'        => strlen($basePrice) > 0 ? $basePrice : '',
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

    /**
     * Get the delivery time for a variation.
     *
     * @param $variation
     * @param KeyValue $settings
     * @return string
     */
    private function getDeliveryTime($variation, KeyValue $settings):string
    {
        $availabilityDays = $this->elasticExportHelper->getAvailability($variation, $settings, false);

        if($availabilityDays > 0)
        {
            return $availabilityDays . ' x Tag(en)';
        }

        return '';
    }
}
