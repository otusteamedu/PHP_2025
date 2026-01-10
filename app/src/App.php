<?php
declare(strict_types=1);

namespace App;

use App\Http\Request;
use App\Http\Response;
use App\Mapper\EmailMapper;
use App\Validator\Validator;
use Exception;
use Throwable;

class App
{
    private Request $request;
    private EmailMapper $emailMapper;
    private Validator $validator;

    public function __construct()
    {
        $this->request = new Request();
        $this->emailMapper = new EmailMapper();
        $this->validator = new Validator();

    }

    public function run(): Response
    {
        try {
            $data = $this->request->postAll();
            $errors = $this->validator->validate($data, $this->emailMapper->getValidationCollectionEmailList());

            if ($errors) {
                throw new Exception(current($errors)->getFullMessage());
            }

            $valid = [];
            $invalid = [];
            foreach ($data['emails'] as $email) {
                if ($this->validator->hasEmailMxRecord($email)) {
                    $valid[] = $email;
                    continue;
                }
                $invalid[] = $email;
            }

            return new Response(200, compact('valid', 'invalid'));
        } catch (Throwable $e) {
            return new Response(400, null, $e->getMessage());
        }
    }
}
