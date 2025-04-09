<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Infrastructure\Console\Request;

class AddAction extends BaseAction
{
    public function __invoke(Request $request)
    {
        $data = $request->getParams();
        var_dump($data);
        die;
        return $this->responseSuccess($data);
    }

}