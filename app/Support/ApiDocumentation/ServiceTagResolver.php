<?php

namespace App\Support\ApiDocumentation;

use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;
use Illuminate\Support\Str;

class ServiceTagResolver
{
    /**
     * Endpoints are tagged by their controller (e.g. "Auth", "Account Security").
     * The owning service (e.g. "Auth Service") is applied afterwards as an
     * `x-tagGroups` grouping so the sidebar renders Service > Controller > endpoints.
     *
     * @return list<string>
     */
    public function __invoke(RouteInfo $routeInfo, Operation $operation): array
    {
        $uri = Str::replace('?}', '}', $routeInfo->route->uri);

        if ($service = ApiServiceCatalog::tagForUri($uri)) {
            $operation->setAttribute('groupWeight', ApiServiceCatalog::tagWeight($service));
        }

        return [$this->controllerTag($routeInfo->className(), $uri)];
    }

    private function controllerTag(?string $className, string $uri): string
    {
        $base = class_basename($className ?? '');

        if ($base !== '') {
            return (string) Str::of($base)
                ->replaceLast('Controller', '')
                ->headline();
        }

        // Closure/no-controller routes: derive a stable tag from the first
        // meaningful URI segment so it does not collide across services.
        $segment = collect(explode('/', $uri))
            ->reject(fn (string $part) => $part === ''
                || in_array($part, ['api', 'v1', 'v2'], true)
                || str_starts_with($part, '{'))
            ->first();

        return $segment ? (string) Str::of($segment)->headline() : 'General';
    }
}
