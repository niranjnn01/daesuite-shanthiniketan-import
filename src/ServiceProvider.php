<?php 

// clienta-events-import/src/ServiceProvider.php
namespace Daesuite\ShanthiniketanImport;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Daesuite\ImportCore\IngestorRegistry;
use Daesuite\ShanthiniketanImport\Ingestors\EventIngestor;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(IngestorRegistry $registry): void
    {
        $tenantSlug = 'shanthiniketan';
        $registry->register($tenantSlug, 'event', 'csv_upload', EventIngestor::class);
    }
}