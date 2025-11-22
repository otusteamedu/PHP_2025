<?php

namespace Framework\DI\Trait;

use Framework\Http\Request;

trait ServiceLoaderTrait
{
    protected array $specialObjects = [];

    public function getRequest(): Request
    {
        return $this->specialObjects[Request::class];
    }

    protected function initSpecialObjects(): void
    {
        $this->specialObjects[Request::class] = new Request();
    }

    /**
     * @param string $projectDir
     * @param array $pathData
     * @param array $_services
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function loadServices(string $projectDir, array $pathData, array $_services): array
    {
        $path = $this->getPath(
            projectDir: $projectDir,
            path: $pathData['path']
        );

        $iterator = $this->iterateDir(
            namespace: $pathData['namespace'],
            path: $path
        );

        $servicesReflections = [];

        foreach ($iterator as $reflection) {
            if (!is_null($reflection)) {
                $servicesReflections[] = $reflection;
            }
        }

        $services = [];

        /** @var \ReflectionClass $reflection */
        foreach ($servicesReflections as $reflection) {
            $constructor = $reflection->getConstructor();

            if (is_null($constructor)) {
                $services[$reflection->getName()] = $reflection->newInstanceWithoutConstructor();
            }
        }

        $reflectionsAmount = count($servicesReflections);
        $servicesAmount = count($services);

        while ($reflectionsAmount > $servicesAmount) {
            /** @var \ReflectionClass $reflection */
            foreach ($servicesReflections as $reflection) {
                $constructor = $reflection->getConstructor();

                if (is_null($constructor)) {
                    continue;
                }

                $pInjections = [];
                $parameters = $constructor->getParameters();
                foreach ($parameters as $parameter) {
                    $pType = $parameter->getType();
                    if ($pType->isBuiltin()) {
                        continue;
                    }

                    $pTypeName = $pType->getName();
                    if (array_key_exists($pTypeName, $this->specialObjects)) {
                        $pInjections[] = $this->specialObjects[$pTypeName];

                        continue;
                    }

                    if (array_key_exists($pTypeName, $services)) {
                        $pInjections[] = $services[$pTypeName];

                        continue;
                    }

                    if (array_key_exists($pTypeName, $_services)) {
                        $pInjections[] = $_services[$pTypeName];
                    }
                }

                if (count($parameters) == count($pInjections)) {
                    $services[$reflection->getName()] = $reflection->newInstanceArgs($pInjections);
                }
            }

            $servicesAmount = count($services);
        }

        return $services;
    }

    private function getPath(string $projectDir, string $path): string
    {
        return $projectDir . DIRECTORY_SEPARATOR . $path;
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return \Generator
     *
     * @throws \ReflectionException
     */
    private function iterateDir(string $namespace, string $path): \Generator
    {
        $dirContent = scandir($path);

        if (!is_array($dirContent)) {
            return null;
        }

        foreach ($dirContent as $file) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $file;

            if (!is_file($fullPath) || !$this->isClass($fullPath)) {
                continue;
            }

            yield $this->loadClass($namespace, $fullPath);
        }
    }

    private function isClass(string $fullPath): bool
    {
        $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
        return $ext === 'php';
    }

    /**
     * @param string $namespace
     * @param string $fullPath
     * @return \ReflectionClass
     *
     * @throws \ReflectionException
     */
    private function loadClass(string $namespace, string $fullPath): \ReflectionClass
    {
        $fileName = pathinfo($fullPath, PATHINFO_FILENAME);
        $className = $namespace . '\\' . $fileName;
        return new \ReflectionClass($className);
    }
}