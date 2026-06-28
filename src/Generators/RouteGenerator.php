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
        $isApi         = (bool) ($options['api'] ?? false);
        $stub          = $isApi ? 'routes.api' : 'routes.web';
        $routeFile     = $isApi ? 'api.php' : 'web.php';
        $routesPath    = $this->configString('paths.routes', 'routes');
        $baseNamespace = $this->configString('namespaces.controller', 'App\\Http\\Controllers');
        $namespace     = $isApi ? $baseNamespace.'\\Api' : $baseNamespace;

        $routeBlock = trim($this->renderStub($stub, [
            '{{Namespace}}' => $namespace,
        ]));

        $absolutePath = $this->resolve("{$routesPath}/{$routeFile}");
        $routeMarker  = '// CRUD-GENERATED:START '.$this->nameResolver->getModelName();

        // If route file exists, append block cleanly (never overwrite)
        if ($this->files->exists($absolutePath)) {
            $existingContent = rtrim($this->files->get($absolutePath));

            // Prevent duplicate route blocks
            if (str_contains($existingContent, $routeMarker)) {
                return ["{$this->nameResolver->getModelName()} routes already exist in [{$routeFile}] — skipped."];
            }

            $newContent = $existingContent.PHP_EOL.PHP_EOL.$routeBlock.PHP_EOL;
            $this->files->put($absolutePath, $newContent);

            return ["Routes appended to [{$routeFile}]"];
        }

        // If route file does not exist, create it
        $this->ensureDirectory(dirname($absolutePath));

        $newContent = '<?php'.PHP_EOL.PHP_EOL.$routeBlock.PHP_EOL;

        if ($this->files->put($absolutePath, $newContent) === false) {
            return ["Failed to create [{$routeFile}]"];
        }

        return [$absolutePath];
    }
}
