<?php declare(strict_types=1);

namespace App\Http\Requests;

class Request
{
    /**
     * @return array
     */
    public function getPost(): array
    {
        return $_POST;
    }
}