<?

declare(strict_types=1);

namespace Kamalo\Balancer\Class;
class Validator
{
    public function isValid(string $value): bool
    {
        $closedBracketsCount = 0;

        for ($i = 0; $i < strlen($value); $i++) {

            if (
                $value[$i] === ')'
                && $closedBracketsCount === 0
            ) {
                $closedBracketsCount--;
                break;
            }

            if ($value[$i] === ')') {
                $closedBracketsCount--;
            }

            if ($value[$i] === '(') {
                $closedBracketsCount++;
            }

        }

        return $closedBracketsCount === 0;
    }
}