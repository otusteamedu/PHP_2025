<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Enviropment;

interface ConfigInterface
{
    public function get(string $section, string $key);
}