<?php

declare(strict_types=1);

namespace Infrastructure\Http\Requests;

class OrderRequest
{
    /**
     * @OA\Schema(
     *     schema="NewOrderRequest",
     *     required={"client_name", "client_phone", "product"},
     *     type="object",
     *     @OA\Property(
     *         property="client_name",
     *         type="string",
     *         minLength=2,
     *         maxLength=20,
     *         example="John Doe"
     *     ),
     *     @OA\Property(
     *         property="client_phone",
     *         type="string",
     *         pattern="^\d{10}$",
     *         example="1234567890"
     *     ),
     *     @OA\Property(
     *         property="product",
     *         type="string",
     *         enum={"Burger", "Sandwich", "HotDog"},
     *         example="Burger"
     *     ),
     *     @OA\Property(
     *         property="ingredients",
     *         type="array",
     *         @OA\Items(
     *             type="string",
     *             enum={"cheese", "onion", "bacon", "mustard", "cucumbers", "salad", "tomatoes"}
     *         ),
     *         example={"cheese", "bacon"}
     *     )
     * )
     */
    public function validate(array $data): array
    {
        $errors = [];
        foreach ($data as $key => $value) {
            if ($key === 'client_name') {
                if (empty($value)) {
                    $errors[] = 'Имя не может быть пустым!';
                } elseif (strlen($value) < 2) {
                    $errors[] = 'Имя должно быть не менее 2 символов!';
                } elseif (strlen($value) > 20) {
                    $errors[] = 'Имя должно быть не более 20 символов!';
                }
            }
            if ($key === 'client_phone') {
                if (empty($value)) {
                    $errors[] = 'Телефон не может быть пустым!';
                } elseif (strlen($value) != 10) {
                    $errors[] = 'Телефон должен содержать 10 цифр!';
                } elseif (!ctype_digit($value)) {
                    $errors[] = 'Телефон должен содержать только цифры!';
                }
            }
        }

        return $errors;
    }
}

