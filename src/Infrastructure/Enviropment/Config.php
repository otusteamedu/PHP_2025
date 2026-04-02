<?

declare(strict_types=1);

namespace Kamalo\EventsService\Infrastucture\Enviropment;

class Config
{
    private array $config = [];

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config.php';
    }

    public function get(string $section, string $key): string
    {
        return $this->config[$section][$key];
    }
}