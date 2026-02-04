<?php

namespace Core\Controllers;

use Core\Services\BracketValidator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BracketController extends Controller
{
    private $validator;

    public function __construct(BracketValidator $validator)
    {
        $this->validator = $validator;
    }

    public function handle(Request $request)
    {
        // Получаем параметр string из POST
        $string = $request->input('string');

        if ($this->validator->verify($string)) {
            return response('Всё хорошо', 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Всё плохо', 400)
            ->header('Content-Type', 'text/plain');
    }
}
