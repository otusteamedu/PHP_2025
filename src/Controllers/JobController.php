<?php
namespace App\Controllers;

use App\Models\Job;
use Redis;

class JobController {
    
    public function submit() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['data'])) {
            http_response_code(422);
            echo json_encode(['error' => 'Field "data" is required']);
            return;
        }
        
        $jobModel = new Job();
        $jobId = $jobModel->create($input['data']);
        
        try {
            $redis = new Redis();
            $redis->connect('redis', 6379);
            $redis->rpush('job_queue', $jobId);
        } catch (\Exception $e) {
            error_log("Redis error: " . $e->getMessage());
        }
        
        http_response_code(202);
        echo json_encode([
            'request_id' => (int)$jobId,
            'status' => 'pending',
            'message' => 'Task accepted for processing',
            'check_url' => "/api/status/{$jobId}"
        ]);
    }
    
    public function status($id) {
        $jobModel = new Job();
        $job = $jobModel->find($id);
        
        if (!$job) {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found']);
            return;
        }
        
        $response = [
            'request_id' => (int)$job['id'],
            'status' => $job['status'],
            'created_at' => $job['created_at']
        ];
        
        if ($job['status'] === 'completed' && $job['output_data']) {
            $response['result'] = json_decode($job['output_data'], true);
        } elseif ($job['status'] === 'failed') {
            $response['error'] = 'Processing failed';
        }
        
        echo json_encode($response);
    }
}