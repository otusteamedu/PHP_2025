<?php declare(strict_types=1);

namespace App;

use App\validation\Validator;
use App\http\Request;
use App\http\Response;

class App {
    public function run(): void {
        try {
            $request = new Request($_POST);
            $validator = new Validator();
            $processor = new Processor($validator);
            $validStrings = $processor->process($request);
            Response::send($validStrings);
        } catch (\InvalidArgumentException $e) {
            Response::sendError($e->getMessage(), 400);
        } catch (\Exception $e) {
            Response::sendError("Internal Server Error", 500);
        }
    }
}