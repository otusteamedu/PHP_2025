<?php
namespace Aovchinnikova\Hw15\Controller;

use Aovchinnikova\Hw15\Service\EmailValidationService;
use Exception;
use Predis\Client;

class EmailController
{
    private $emailValidationService;
    private $redis;

    public function __construct()
    {
        $this->emailValidationService = new EmailValidationService();
        $this->redis = new Client([
            'host' => getenv('REDIS_HOST') ?: 'redis',
            'port' => getenv('REDIS_HOST_PORT') ?: 6379,
        ]);
    }

    public function handleValidationRequest()
    {
        try {
            $requestPayload = file_get_contents('php://input');
            $parsedInput = json_decode($requestPayload, true);

            if ($parsedInput === null || !isset($parsedInput['emails'])) {
                throw new Exception('Invalid JSON or missing "emails" field.');
            }

            // Generate unique request ID
            $requestId = uniqid('req_', true);
            
            // Store request data in Redis with pending status
            $this->redis->set($requestId, json_encode([
                'status' => 'pending',
                'emails' => $parsedInput['emails'],
                'results' => []
            ]));
            
            // Add to processing queue
            $this->redis->rpush('email_validation_queue', $requestId);

            header('Content-Type: application/json');
            echo json_encode(['request_id' => $requestId]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function checkRequestStatus(string $requestId)
    {
        try {
            $data = $this->redis->get($requestId);
            
            if (!$data) {
                throw new Exception('Request not found');
            }
            
            $result = json_decode($data, true);
            
            header('Content-Type: application/json');
            echo json_encode([
                'request_id' => $requestId,
                'status' => $result['status'],
                'results' => $result['results']
            ]);

        } catch (Exception $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function processQueue()
    {
        while (true) {
            // Blocking pop from queue
            $requestId = $this->redis->blpop('email_validation_queue', 30);
            
            if ($requestId) {
                $requestId = $requestId[1];
                $data = $this->redis->get($requestId);
                
                if ($data) {
                    $data = json_decode($data, true);
                    $results = [];
                    
                    foreach ($data['emails'] as $email) {
                        $results[$email] = $this->emailValidationService->validate($email)->toArray();
                    }
                    
                    // Update status and results
                    $this->redis->set($requestId, json_encode([
                        'status' => 'completed',
                        'emails' => $data['emails'],
                        'results' => $results
                    ]));
                }
            }
        }
    }
}
