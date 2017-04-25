<?php

namespace ElasticExportGuenstigerDE\Helper;

use Plenty\Modules\Item\Property\Contracts\PropertyMarketReferenceRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyNameRepositoryContract;
use Plenty\Modules\Item\Property\Models\PropertyName;
use Plenty\Plugin\Log\Loggable;

class PropertyHelper
{
    use Loggable;

    const GUENSTIGER_DE = 153.00;

    const PROPERTY_TYPE_TEXT = 'text';
    const PROPERTY_TYPE_SELECTION = 'selection';
    const PROPERTY_TYPE_EMPTY = 'empty';

    /**
     * @var array
     */
    private $itemFreeTextCache = [];

    /**
     * @var array
     */
    private $itemPropertyCache = [];

    /**
     * @var PropertyNameRepositoryContract
     */
    private $propertyNameRepository;

    /**
     * @var PropertyMarketReferenceRepositoryContract
     */
    private $propertyMarketReferenceRepository;

    /**
     * PropertyHelper constructor.
     *
     * @param PropertyNameRepositoryContract $propertyNameRepository
     * @param PropertyMarketReferenceRepositoryContract $propertyMarketReferenceRepository
     */
    public function __construct(
        PropertyNameRepositoryContract $propertyNameRepository,
        PropertyMarketReferenceRepositoryContract $propertyMarketReferenceRepository)
    {
        $this->propertyNameRepository = $propertyNameRepository;
        $this->propertyMarketReferenceRepository = $propertyMarketReferenceRepository;
    }

    /**
     * Get free text.
     *
     * @param  array $variation
     * @return string
     */
    public function getFreeText($variation):string
    {
        if(!array_key_exists($variation['data']['item']['id'], $this->itemFreeTextCache))
        {
            $freeText = array();

            foreach($variation['data']['properties'] as $property)
            {
                if(!is_null($property['property']['id']) &&
                    $property['property']['valueType'] != 'file' &&
                    $property['property']['valueType'] != 'empty')
                {
                    $propertyName = $this->propertyNameRepository->findOne($property['property']['id'], 'de');
                    $propertyMarketReference = $this->propertyMarketReferenceRepository->findOne($property['property']['id'], self::GUENSTIGER_DE);

                    // Skip properties which do not have the Component Id set
                    if(!($propertyName instanceof PropertyName) ||
                        is_null($propertyName) ||
                        is_null($propertyMarketReference) ||
                        $propertyMarketReference->componentId != 1)
                    {
                        continue;
                    }

                    if($property['property']['valueType'] == 'text')
                    {
                        if(is_array($property['texts']))
                        {
                            $freeText[] = $property['texts'][0]['value'];
                        }
                    }

                    if($property['property']['valueType'] == 'selection')
                    {
                        if(is_array($property['selection']))
                        {
                            $freeText[] = $property['selection'][0]['name'];
                        }
                    }
                }
            }

            $this->itemFreeTextCache[$variation['data']['item']['id']] = implode(' ', $freeText);
        }

        return $this->itemFreeTextCache[$variation['data']['item']['id']];
    }

    /**
     * Get property.
     *
     * @param  array $variation
     * @param  string $property
     * @return string
     */
    public function getProperty($variation, string $property)
    {
        $itemPropertyList = $this->getItemPropertyList($variation);

        if(array_key_exists($property, $itemPropertyList))
        {
            return $itemPropertyList[$property];
        }

        return '';
    }

    /**
     * Get item properties for a given variation.
     *
     * @param  array $variation
     * @return array
     */
    private function getItemPropertyList($variation):array
    {
        if(!array_key_exists($variation['data']['item']['id'], $this->itemPropertyCache))
        {
            $list = array();

            foreach($variation['data']['properties'] as $property)
            {
                if(!is_null($property['property']['id']) &&
                    $property['property']['valueType'] != 'file')
                {
                    $propertyName = $this->propertyNameRepository->findOne($property['property']['id'], 'de');
                    $propertyMarketReference = $this->propertyMarketReferenceRepository->findOne($property['property']['id'], self::GUENSTIGER_DE);

                    // Skip properties which do not have the External Component set up
                    if(!($propertyName instanceof PropertyName) ||
                        is_null($propertyName) ||
                        is_null($propertyMarketReference) ||
                        $propertyMarketReference->externalComponent == '0')
                    {
                        $this->getLogger(__METHOD__)->debug('ElasticExportIdealoDE::item.variationPropertyNotAdded', [
                            'ItemId'            => $variation['data']['item']['id'],
                            'VariationId'       => $variation['id'],
                            'ExternalComponent' => $propertyMarketReference->externalComponent
                        ]);

                        continue;
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_TEXT)
                    {
                        if(is_array($property['texts']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = $property['texts'][0]['value'];
                        }
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_SELECTION)
                    {
                        if(is_array($property['selection']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = $property['selection'][0]['name'];
                        }
                    }

                    if($property['property']['valueType'] == self::PROPERTY_TYPE_EMPTY)
                    {
                        $list[$propertyMarketReference->externalComponent] = $propertyMarketReference->externalComponent;
                    }
                }
            }

            $this->itemPropertyCache[$variation['data']['item']['id']] = $list;

            $this->getLogger(__METHOD__)->debug('ElasticExportIdealoDE::item.variationPropertyList', [
                'ItemId'        => $variation['data']['item']['id'],
                'VariationId'   => $variation['id'],
                'PropertyList'  => count($list) > 0 ? $list : 'no properties'
            ]);
        }

        return $this->itemPropertyCache[$variation['data']['item']['id']];
    }
}