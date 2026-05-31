<?php 

namespace Daesuite\ShanthiniketanImport;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Daesuite\ImportCore\IngestorRegistry;
use Daesuite\ShanthiniketanImport\Ingestors\EventIngestor;
use Illuminate\Support\Facades\Config;

class ServiceProvider extends BaseServiceProvider
{


    public function register(): void
    {

        $config = require __DIR__ . '/../config/config.php';
        $import_tenant_slug   = $config['import_tenant_slug'];

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', $import_tenant_slug);

        Config::set("database.connections.{$import_tenant_slug}_import", $config['db']);
    }

    public function boot(IngestorRegistry $registry): void
    {
        
        $config = require __DIR__ . '/../config/config.php';
        $import_tenant_slug   = $config['import_tenant_slug'];

        $registry->register($import_tenant_slug, 'event', 'manual', EventIngestor::class);
    }

}