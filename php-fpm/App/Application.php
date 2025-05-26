<?php
namespace App;

class Application
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger();
    }

    public function run(): array
    {
        $string = $_POST['string'] ?? '';

        try {
            Session::updateSessionData($this->logger);

            Validation::checkMethod($_SERVER['REQUEST_METHOD']);
            Validation::checkNotEmptyString($string);
            Validation::checkCorrectString($string);

            $response = 'OK: String is balanced' . PHP_EOL;
            $this->logger->log($response);
            return ['code' => 200, 'message' => $response];

        } catch (\Exception $e) {
            $this->logger->log($e->getMessage());
            return ['code' => 400, 'message' => $e->getMessage()];
        }
    }
}