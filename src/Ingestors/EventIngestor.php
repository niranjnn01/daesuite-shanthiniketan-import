<?php 

namespace Daesuite\ShanthiniketanImport\Ingestors;

use Daesuite\ImportCore\Contracts\IngestorHandler;

use Daesuite\ImportCore\StagingIngestor;
use Daesuite\ImportCore\Exceptions\IngestorException;
use Illuminate\Support\Facades\DB;
use Laravel\Prompts\Concerns\Events;

class EventIngestor implements IngestorHandler
{

    public function __construct(private StagingIngestor $ingestor) {}



    public function handle(string $entityType, string $channel): void
    {
        echo "Inside Shanthiniketan event ingestor \n";

        $records = $this->fetch();

        if (empty($records)) {
            throw IngestorException::noRecordsFound($entityType);
        }

        $config = require __DIR__ . '/../../config/config.php';

        $data = [];

        foreach(DB::connection($config['import_tenant_slug'] . '_import')
        ->table('gallery_images')
        ->get() AS $galleryItem) {
            
            if( ! isset($data[$galleryItem->album_id]) ) {

                $album = DB::connection($config['import_tenant_slug'] . '_import')
                ->table('albums')
                ->where('id', $galleryItem->album_id)
                ->first();

                // dump($album);
                if( $album ) {

                    $data[$galleryItem->album_id] = [
                        'title' => $album->title,
                        'description' => $album->description,
                        'created_on' => $album->created_on,
                        'resources' => []
                    ];
                } else {
                    // image with no album
                    $data[0] = [
                        'title' => 'no album',
                        'description' => '',
                        'created_on' => $galleryItem->created_on,
                        'resources' => []
                    ];
                }

            }

                
            $data[$galleryItem->album_id]['resources'][] = $galleryItem->uid;

        } 

        // dump($data);
        // $payloads = $this->normalize($records);
        
        
        $this->ingestor->ingestBatch($entityType, $channel, $data);
    }

    private function fetch(): array
    {
        // IngestorException::fetchFailed()

        $config = require __DIR__ . '/../../config/config.php';

        try {
            return DB::connection($config['import_tenant_slug'] . '_import')
                ->table('albums')
                ->get()
                ->toArray();
        } catch (\Throwable $e) {
            throw IngestorException::fetchFailed('event', $e);
        }

    }

    private function normalize(array $records): array
    {
        // IngestorException::normalizationFailed()

        // map each album to a consistent payload shape
        return array_map(fn($record) => [
            'id'     => $record->id,
            'title'     => $record->title,
            'venue'     => null,
        ], $records);
    }
}