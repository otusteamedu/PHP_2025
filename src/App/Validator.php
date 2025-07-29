<?

declare(strict_types=1);

class Validator
{
    public function validate(string $value): void
    {
        $this->validateEmptyString($value);
        $this->validateCorrectString($value);
    }

    private function validateEmptyString(string $value): void
    {
        if (empty($value)) {
            throw new Exception('Строка не может быть пустой');
        }
    }

    private function validateCorrectString(string $value): void
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

        if ($closedBracketsCount !== 0) {
            throw new Exception("Не корретное количество открывающих и закрывающих скобок в строке");
        }
    }
}