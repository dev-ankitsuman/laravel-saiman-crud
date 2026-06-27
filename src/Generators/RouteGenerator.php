<?php

declare(strict_types=1);

namespace Saiman\SaimanCrud\Generators;

final class RouteGenerator extends BaseGenerator
{
    /**
     * @param  array<string, mixed>  $options
     * @return array<int, string>
     */
    public function generate(array $options = []): array
    {
        $isApi      = (bool) ($options['api'] ?? false);
        $force      = (bool) ($options['force'] ?? false);
        $stub       = $isApi ? 'routes.api' : 'routes.web';
        $routeFile  = $isApi ? 'api.php' : 'web.php';
        $routesPath = $this->configString('paths.routes', 'routes');
        $baseNs     = $this->configString('namespaces.controller', 'App\\Http\\Controllers');
        $namespace  = $isApi ? $baseNs.'\\Api' : $baseNs;

        $content = $this->renderStub($stub, [
            '{{Namespace}}' => $namespace,
        ]);

        $absolutePath = $this->resolve("{$routesPath}/{$routeFile}");

        if ($this->files->exists($absolutePath)) {
            $existing    = $this->files->get($absolutePath);
            $routeMarker = '// '.$this->nameResolver->getModelName().' Routes';

            if (str_contains($existing, $routeMarker)) {
                return ["Route already registered in [{$routeFile}] — skipped."];
            }

            $this->files->append($absolutePath, PHP_EOL.PHP_EOL.$content);

            return ["Routes appended to [{$routeFile}]"];
        }

        return [$this->writeFile($absolutePath, $content, $force)];
    }
}
