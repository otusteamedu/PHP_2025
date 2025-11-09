<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Enviropment;

use Kamalo\BurgersShop\Application\Enviropment\ConfigInterface;

class Config implements ConfigInterface
{
    private array $config = [];

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config.php';
    }

    public function get(string $section, string $key)
    {
        if (!isset($this->config[$section][$key])) {
            return false;
        }

        return $this->config[$section][$key];
    }
}