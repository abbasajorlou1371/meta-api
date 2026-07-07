<?php

namespace App\Support\ApiDocumentation;

class ServiceTagGroups
{
    private const FALLBACK_GROUP = 'General';

    /**
     * Inject `x-tagGroups` into a generated OpenAPI document so controller-level
     * tags are grouped under their owning service in the docs sidebar.
     *
     * @param  array<string, mixed>  $spec
     * @return array<string, mixed>
     */
    public static function inject(array $spec): array
    {
        /** @var array<string, array<string, true>> $grouped */
        $grouped = [];

        foreach ($spec['paths'] ?? [] as $path => $operations) {
            if (! is_array($operations)) {
                continue;
            }

            $service = ApiServiceCatalog::tagForUri('api'.$path) ?? self::FALLBACK_GROUP;

            foreach ($operations as $operation) {
                if (! is_array($operation)) {
                    continue;
                }

                foreach ($operation['tags'] ?? [] as $tag) {
                    $grouped[$service][$tag] = true;
                }
            }
        }

        if ($grouped === []) {
            return $spec;
        }

        $tagGroups = collect($grouped)
            ->map(fn (array $tags, string $service) => [
                'name' => $service,
                'tags' => collect(array_keys($tags))->sort()->values()->all(),
            ])
            ->sort(function (array $a, array $b) {
                return [ApiServiceCatalog::tagWeight($a['name']), $a['name']]
                    <=> [ApiServiceCatalog::tagWeight($b['name']), $b['name']];
            })
            ->values();

        $spec['x-tagGroups'] = $tagGroups->all();

        $spec['tags'] = $tagGroups
            ->flatMap(fn (array $group) => $group['tags'])
            ->unique()
            ->map(fn (string $tag) => ['name' => $tag])
            ->values()
            ->all();

        return $spec;
    }
}
