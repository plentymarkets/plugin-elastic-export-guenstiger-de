<?php

namespace ElasticExportGuenstigerDE;

use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\DataExchangeServiceProvider;


/**
 * Class ElasticExportGuenstigerDEServiceProvider
 * @package ElasticExportGuenstigerDE
 */
class ElasticExportGuenstigerDEServiceProvider extends DataExchangeServiceProvider
{
    /**
     * Abstract function for registering the service provider.
     */
    public function register()
    {

    }

    /**
     * Adds the export format to the export container.
     *
     * @param ExportPresetContainer $container
     */
    public function exports(ExportPresetContainer $container)
    {
        $container->add(
            'GuenstigerDE-Plugin',
            'ElasticExportGuenstigerDE\ResultField\GuenstigerDE',
            'ElasticExportGuenstigerDE\Generator\GuenstigerDE',
            '',
            true,
            true
        );
    }
}