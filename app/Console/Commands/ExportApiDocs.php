<?php

namespace App\Console\Commands;

use App\Support\ApiDocumentation\ServiceTagGroups;
use Dedoc\Scramble\Generator;
use Dedoc\Scramble\Scramble;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportApiDocs extends Command
{
    protected $signature = 'api:docs:export
        {--path= : The path to save the exported JSON file}
        {--api=default : The API to export a documentation for}';

    protected $description = 'Export the OpenAPI document (with service tag groups) to a JSON file.';

    public function handle(Generator $generator): void
    {
        $api = $this->option('api');
        $config = Scramble::getGeneratorConfig($api);

        $specification = ServiceTagGroups::inject($generator($config));

        $filename = $this->option('path')
            ?: $config->get('export_path')
            ?? 'api'.($api === 'default' ? '' : "-$api").'.json';

        File::ensureDirectoryExists(dirname(base_path($filename)));
        File::put(base_path($filename), json_encode(
            $specification,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ));

        $this->info("OpenAPI document exported to {$filename}.");
    }
}
