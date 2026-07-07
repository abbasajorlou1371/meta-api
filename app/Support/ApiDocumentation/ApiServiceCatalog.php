<?php

namespace App\Support\ApiDocumentation;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiServiceCatalog
{
    /**
     * @var list<array{pattern: string, service: string, specificity: int}>|null
     */
    private static ?array $rules = null;

    /**
     * @return list<array{pattern: string, service: string, specificity: int}>
     */
    public static function rules(): array
    {
        return array_merge(
            collect(config('api-services.route_overrides', []))
                ->map(fn (array $override) => [
                    'pattern' => $override['pattern'],
                    'service' => $override['service'],
                    'specificity' => PHP_INT_MAX,
                ])
                ->all(),
            self::documentationRules(),
        );
    }

    /**
     * @return list<array{pattern: string, service: string, specificity: int}>
     */
    private static function documentationRules(): array
    {
        if (self::$rules !== null) {
            return self::$rules;
        }

        $rules = [];

        $docsPath = config('api-services.docs_path');

        if (is_dir($docsPath)) {
            foreach (self::collectMarkdownFiles($docsPath) as $file) {
                $service = self::resolveServiceKey($file, $docsPath);

                foreach (self::extractApiPaths(file_get_contents($file) ?: '') as $path) {
                    $rules[] = [
                        'pattern' => self::pathToRegex($path),
                        'service' => $service,
                        'specificity' => self::pathSpecificity($path),
                    ];
                }
            }
        }

        usort($rules, fn (array $a, array $b) => $b['specificity'] <=> $a['specificity']);

        return self::$rules = $rules;
    }

    public static function tagForUri(string $uri): ?string
    {
        $uri = trim($uri, '/');

        foreach (config('api-services.route_overrides', []) as $override) {
            if (preg_match($override['pattern'], $uri)) {
                return self::tagName($override['service']);
            }
        }

        foreach (self::documentationRules() as $rule) {
            if (preg_match($rule['pattern'], $uri)) {
                return self::tagName($rule['service']);
            }
        }

        return null;
    }

    public static function tagName(string $serviceKey): string
    {
        return config("api-services.tags.{$serviceKey}.name")
            ?? Str::headline(str_replace('-service', '', $serviceKey)).' Service';
    }

    public static function tagWeight(string $tagName): int
    {
        foreach (config('api-services.tags', []) as $service) {
            if (($service['name'] ?? null) === $tagName) {
                return $service['weight'] ?? PHP_INT_MAX;
            }
        }

        return PHP_INT_MAX;
    }

    /**
     * @return list<string>
     */
    private static function collectMarkdownFiles(string $docsPath): array
    {
        return collect(File::allFiles($docsPath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->map(fn ($file) => $file->getPathname())
            ->values()
            ->all();
    }

    private static function resolveServiceKey(string $filePath, string $docsPath): string
    {
        $relative = Str::after($filePath, rtrim($docsPath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);

        if (str_contains($relative, DIRECTORY_SEPARATOR)) {
            return Str::before($relative, DIRECTORY_SEPARATOR);
        }

        return Str::before(basename($relative), '_api');
    }

    /**
     * @return list<string>
     */
    private static function extractApiPaths(string $contents): array
    {
        preg_match_all('~(?:`|(?:\b(?:GET|POST|PUT|PATCH|DELETE)\s+))(/api/[^\s`|]+)~i', $contents, $matches);

        return collect($matches[1] ?? [])
            ->map(fn (string $path) => rtrim(trim($path), '/'))
            ->unique()
            ->values()
            ->all();
    }

    private static function pathToRegex(string $docPath): string
    {
        $uri = trim($docPath, '/');

        $parts = explode('/', $uri);
        $regexParts = [];

        foreach ($parts as $part) {
            if ($part === '*' || $part === '...') {
                $regexParts[] = '.*';

                continue;
            }

            if (preg_match('/^\{[^}]+\}$/', $part) || preg_match('/^\$\{[^}]+\}$/', $part)) {
                $regexParts[] = '[^/]+';

                continue;
            }

            $regexParts[] = preg_quote($part, '#');
        }

        return '#^'.implode('/', $regexParts).'(?:/.*)?$#';
    }

    private static function pathSpecificity(string $path): int
    {
        $staticSegments = collect(explode('/', trim($path, '/')))
            ->reject(fn (string $segment) => $segment === '*'
                || $segment === '...'
                || preg_match('/^\{[^}]+\}$/', $segment)
                || preg_match('/^\$\{[^}]+\}$/', $segment))
            ->count();

        return ($staticSegments * 100) + strlen($path);
    }
}
