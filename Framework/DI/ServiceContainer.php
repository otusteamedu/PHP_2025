<?php

namespace Framework\DI;

use Blarkinov\Hw1500\Domain\Controller\ControllerInterface;
use Exception;
use Framework\DI\Trait\ServiceLoaderTrait;

class ServiceContainer
{
    use ServiceLoaderTrait;

    private const CONTAINER_PARAMETER_NAME = 'serviceContainer';

    private array $services = [];
    private array $controllers = [];

    public function __construct(
        private  string $projectDir,
        private  array $config
    ) {
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function init(): void
    {
        $this->initSpecialObjects();

        $servicesPaths = $this->config[self::CONTAINER_PARAMETER_NAME]['services'] ?? [];
        foreach ($servicesPaths as $servicePathData) {
            $this->services = [
                ...$this->services,
                ...$this->loadServices(
                    projectDir: $this->projectDir,
                    pathData: $servicePathData,
                    _services: $this->services
                )
            ];
        }

        $controllersPaths = $this->config[self::CONTAINER_PARAMETER_NAME]['controllers'] ?? [];

        foreach ($controllersPaths as $controllerPathData) {
            $this->controllers = [
                ...$this->controllers,
                ...$this->loadServices(
                    projectDir: $this->projectDir,
                    pathData: $controllerPathData,
                    _services: $this->services
                )
            ];
        }
    }

    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @param string $className
     * @return ControllerInterface
     *
     * @throws \Exception
     */
    public function getController(string $className): ControllerInterface
    {
        if (!isset($this->controllers[$className])) {
            throw new Exception('Не найдено', 404);
        }

        return $this->controllers[$className];
    }

}