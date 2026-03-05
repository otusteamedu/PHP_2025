<?php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../../src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use App\Models\Job;
use Redis;

echo "Worker started. Waiting for jobs....\n";

$jobModel = new Job();
$redis = new Redis();
$redis->connect('redis', 6379);

while (true) {
    $jobData = $redis->blpop('job_queue', 5);
    
    if ($jobData) {
        $jobId = $jobData[1];
        echo "Processing job #{$jobId}\n";
        
        try {
            $jobModel->updateStatus($jobId, 'processing');
            
            $job = $jobModel->find($jobId);
            $inputData = json_decode($job['input_data'], true);
            
            sleep(5);
            
            if (isset($inputData['text'])) {
                $result = [
                    'original' => $inputData['text'],
                    'reversed' => strrev($inputData['text']),
                    'length' => strlen($inputData['text']),
                    'processed_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $result = [
                    'message' => 'Data processed successfully',
                    'received' => $inputData,
                    'processed_at' => date('Y-m-d H:i:s')
                ];
            }
            
            $jobModel->updateStatus($jobId, 'completed', json_encode($result));
            echo "Job #{$jobId} completed\n";
            
        } catch (\Exception $e) {
            $jobModel->updateStatus($jobId, 'failed');
            echo "Job #{$jobId} failed: " . $e->getMessage() . "\n";
        }
    }
    
    usleep(100000);
}