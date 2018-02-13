<?php

namespace ElasticExportGuenstigerDE\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceSearchRepositoryContract;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchRequest;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchResponse;
use Plenty\Plugin\Log\Loggable;

class PriceHelper
{
    use Loggable;

   const NET_PRICE = 'netPrice';
   const GROSS_PRICE = 'grossPrice';

    /**
     * @var SalesPriceSearchRepositoryContract
     */
    private $salesPriceSearchRepository;

    /**
     * @var SalesPriceSearchRequest
     */
    private $salesPriceSearchRequest;

    /**
     * PriceHelper constructor.
     *
     * @param SalesPriceSearchRepositoryContract $salesPriceSearchRepositoryContract
     * @param SalesPriceSearchRequest $salesPriceSearchRequest
     */
    public function __construct(
        SalesPriceSearchRepositoryContract $salesPriceSearchRepositoryContract,
        SalesPriceSearchRequest $salesPriceSearchRequest)
    {
        $this->salesPriceSearchRepository = $salesPriceSearchRepositoryContract;
        $this->salesPriceSearchRequest = $salesPriceSearchRequest;
    }

    /**
     * Get a list with price and recommended retail price.
     *
     * @param  array $variation
     * @param  KeyValue $settings
     * @return array
     */
    public function getPriceList($variation, KeyValue $settings):array
    {
        $variationPrice = 0.00;

        if($this->salesPriceSearchRequest instanceof SalesPriceSearchRequest)
        {
            $this->salesPriceSearchRequest->variationId = $variation['id'];
            $this->salesPriceSearchRequest->referrerId = $settings->get('referrerId');
            $this->salesPriceSearchRequest->type = 'default';
        }

        // getting the retail price
        $salesPriceSearch = $this->salesPriceSearchRepository->search($this->salesPriceSearchRequest);
        if($salesPriceSearch instanceof SalesPriceSearchResponse)
        {
		   if(isset($salesPriceSearch->price) &&
			   ($settings->get('retailPrice') == self::GROSS_PRICE || is_null($settings->get('retailPrice'))))
		   {
			  $variationPrice = (float)$salesPriceSearch->price;
		   }
		   elseif(isset($salesPriceSearch->priceNet) && $settings->get('retailPrice') == self::NET_PRICE)
		   {
			  $variationPrice = (float)$salesPriceSearch->priceNet;
		   }
        }

        return array(
            'variationRetailPrice.price' => $variationPrice
        );
    }
}