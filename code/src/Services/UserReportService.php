<?php

declare(strict_types=1);

namespace Ak\Hw\Services;

final class UserReportService
{

    /**
     * @param int $userId
     * @param string $email
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function generateReport(int $userId, string $email, string $dateFrom, string $dateTo): array
    {
        // 1. Find user by ID and email (using repository)
        // $user = $this->userRepository->findByIdAndEmail($userId, $email);
        // if (!$user) {
        //     throw new \Exception('User not found.');
        // }


        // 2. For now, just return the input data
        return [
            'user_id' => $userId,
            'email' => $email,
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ]
        ];
    }
}
