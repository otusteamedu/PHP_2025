<?php

namespace App\Services;

use Exception;

class InputService
{
    /** @var string */
    protected string $commandName;

    /** @var array|null */
    protected ?array $inputs;

    /** @var array|null */
    protected ?array $flags = [];

    /**
     * @throws Exception
     */
    public function __construct() {
        $argv = $_SERVER['argv'];
        $this->setCommandName($argv[1]);
        $this->setCommandInput(array_slice($argv, 2) ?? []);
    }

    /**
     * @return string|null
     */
    public function getCommandName(): ?string {
        return $this->commandName;
    }

    /**
     * @param array $argv
     * @return void
     * @throws Exception
     */
    protected function setCommandInput(array $argv) {
        foreach ($argv as $item) {
            if (preg_match('/^(-{2}[a-z][a-z\-]{2,})(=[^-^\s][^=]*)?$/', $item)) {
                $this->setFlag($item);
            } else {
                throw new Exception("Неправильный ввод аргументов и параметров --> '$item'.", 400);
            }
        }

        $this->inputs = $argv;
    }

    /**
     * @param string $commandName
     * @return void
     * @throws Exception
     */
    protected function setCommandName(string $commandName): void {
        if (preg_match('/^(([a-z]+)|([a-z]+:[a-z]+))$/', $commandName)) {
            $this->commandName = $commandName;
        } else {
            throw new Exception("Неправильный ввод наименования команды --> '$commandName'.", 400);
        }
    }

    /**
     * @param string $input
     * @return void
     */
    protected function setFlag(string $input): void {
        $inputExplode = explode('=', $input);
        $key = substr($inputExplode[0], 2);
        $value = $inputExplode[1];

        if (isset($this->flags[$key])) {
            if (empty($value) === false) {
                $this->flags[$key][] = $value;
            }
        } else {
            $this->flags[$key] = $value ? [$value] : [];
        }
    }

    /**
     * @param string|null $flagName
     * @return mixed
     */
    public function get(?string $flagName = null) {
        if (empty($flagName)) {
            $data = $this->flags;
        } else {
            if (count($this->flags[$flagName]) === 1) {
                $data = $this->flags[$flagName][0];
            } else if (empty($this->flags[$flagName]) === false) {
                $data = $this->flags[$flagName];
            } else {
                $data = null;
            }
        }

        return $data;
    }

    /**
     * @param string $flagName
     * @return bool
     */
    public function exist(string $flagName): bool {
        return array_key_exists($flagName, $this->flags);
    }
}