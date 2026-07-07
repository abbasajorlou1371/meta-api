<?php

namespace App\Http\Controllers;

use App\Support\ApiDocumentation\ServiceTagGroups;
use Dedoc\Scramble\CacheableGenerator;
use Dedoc\Scramble\GeneratorConfig;
use Dedoc\Scramble\Scramble;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ApiDocsController extends Controller
{
    public function __construct(private readonly CacheableGenerator $generator) {}

    public function ui(): View
    {
        $config = $this->config();

        return view($config->renderer()->view, [
            'spec' => $this->specification($config),
            'config' => $config,
        ]);
    }

    public function document(): JsonResponse
    {
        return response()->json(
            $this->specification($this->config()),
            options: JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function specification(GeneratorConfig $config): array
    {
        return ServiceTagGroups::inject(($this->generator)($config));
    }

    private function config(): GeneratorConfig
    {
        return Scramble::getGeneratorConfig(Scramble::DEFAULT_API);
    }
}
