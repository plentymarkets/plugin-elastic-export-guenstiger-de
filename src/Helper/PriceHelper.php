<?php

namespace ElasticExportGuenstigerDE\Helper;

use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceRepositoryContract;
use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceSearchRepositoryContract;
use Plenty\Modules\Item\SalesPrice\Models\SalesPrice;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchRequest;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchResponse;
use Plenty\Modules\Order\Currency\Contracts\CurrencyConversionSettingsRepositoryContract;
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
	 * @var SalesPriceRepositoryContract
	 */
	private $salesPriceRepository;

	/**
	 * @var array
	 */
	private $salesPriceCurrencyList = [];

	/**
	 * @var array
	 */
	private $currencyConversionList = [];
	/**
	 * @var CurrencyConversionSettingsRepositoryContract
	 */
	private $currencyConversionSettingsRepository;

	/**
	 * PriceHelper constructor.
	 *
	 * @param SalesPriceSearchRepositoryContract $salesPriceSearchRepositoryContract
	 * @param SalesPriceSearchRequest $salesPriceSearchRequest
	 * @param SalesPriceRepositoryContract $salesPriceRepository
	 * @param CurrencyConversionSettingsRepositoryContract $currencyConversionSettingsRepository
	 */
    public function __construct(
        SalesPriceSearchRepositoryContract $salesPriceSearchRepositoryContract,
        SalesPriceSearchRequest $salesPriceSearchRequest,
        SalesPriceRepositoryContract $salesPriceRepository, 
        CurrencyConversionSettingsRepositoryContract $currencyConversionSettingsRepository)
    {
        $this->salesPriceSearchRepository = $salesPriceSearchRepositoryContract;
        $this->salesPriceSearchRequest = $salesPriceSearchRequest;
	    $this->salesPriceRepository = $salesPriceRepository;
	    $this->currencyConversionSettingsRepository = $currencyConversionSettingsRepository;
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

	    if(!is_null($settings->get('liveConversion')) &&
		    $settings->get('liveConversion') == true &&
		    count($this->currencyConversionList) == 0)
	    {
		    $this->currencyConversionList = $this->currencyConversionSettingsRepository->getCurrencyConversionList();
	    }

        // getting the retail price
        $salesPriceSearch = $this->salesPriceSearchRepository->search($this->salesPriceSearchRequest);
        if($salesPriceSearch instanceof SalesPriceSearchResponse)
        {
		   if(isset($salesPriceSearch->price) &&
			   ($settings->get('retailPrice') == self::GROSS_PRICE || is_null($settings->get('retailPrice'))))
		   {
		   	    $variationPrice = $this->calculatePriceByCurrency($salesPriceSearch, $salesPriceSearch->price, $settings);
			    $variationPrice = (float)$variationPrice;
		   }
		   elseif(isset($salesPriceSearch->priceNet) && $settings->get('retailPrice') == self::NET_PRICE)
		   {
			   $variationPrice = $this->calculatePriceByCurrency($salesPriceSearch, $salesPriceSearch->price, $settings);
			   $variationPrice = (float)$variationPrice;
		   }
        }

        return array(
            'variationRetailPrice.price' => $variationPrice
        );
    }

	/**
	 * Gets the calculated price for a given currency.
	 *
	 * @param SalesPriceSearchResponse $salesPriceSearch
	 * @param $price
	 * @param KeyValue $settings
	 * @return mixed
	 */
	private function calculatePriceByCurrency(SalesPriceSearchResponse $salesPriceSearch, $price, KeyValue $settings)
	{
		if(!is_null($settings->get('liveConversion')) &&
			$settings->get('liveConversion') == true &&
			count($this->currencyConversionList) > 0 &&
			$price > 0)
		{
			if(array_key_exists($salesPriceSearch->salesPriceId, $this->salesPriceCurrencyList) &&
				$this->salesPriceCurrencyList[$salesPriceSearch->salesPriceId] === true)
			{
				$price = $price * $this->currencyConversionList['list'][$salesPriceSearch->currency]['exchange_ratio'];
				return $price;
			}
			elseif(array_key_exists($salesPriceSearch->salesPriceId, $this->salesPriceCurrencyList) &&
				$this->salesPriceCurrencyList[$salesPriceSearch->salesPriceId] === false)
			{
				return $price;
			}

			$salesPriceData = $this->salesPriceRepository->findById($salesPriceSearch->salesPriceId);

			if($salesPriceData instanceof SalesPrice)
			{
				$salePriceCurrencyData = $salesPriceData->currencies->whereIn('currency', [$this->currencyConversionList['default'], "-1"]);

				if(count($salePriceCurrencyData))
				{
					$this->salesPriceCurrencyList[$salesPriceSearch->salesPriceId] = true;

					$price = $price * $this->currencyConversionList['list'][$salesPriceSearch->currency]['exchange_ratio'];

					return $price;
				}
				else
				{
					$this->salesPriceCurrencyList[$salesPriceSearch->salesPriceId] = false;
				}
			}
		}

		return $price;
	}
}