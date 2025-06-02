<?php
namespace Elisad5791\Phpapp;

class EmailVerifier implements EmailVerifierInterface
{
    protected $mxChecker;

    public function __construct(?MxCheckerInterface $mxChecker = null) {
        $this->mxChecker = $mxChecker ?? new DefaultMxChecker();
    }

    public function verifyEmails(array $emails): string
    {
        $results = [];
        foreach ($emails as $email) {
            $results[$email] = $this->verifyEmail($email);
        }

        $output = $this->formatResult($results);
        return $output;
    }
    
    public function verifyEmail(string $email): array
    {
        $formatIsValid = $this->checkFormat($email);
        
        $mxIsValid = null;
        if ($formatIsValid) {
            $mxIsValid = $this->checkMxRecord($email);
        }
        
        return [
            'format_is_valid' => $formatIsValid,
            'mx_is_valid' => $mxIsValid,
            'valid' => $formatIsValid && $mxIsValid,
        ];
    }
    
    protected function checkFormat(string $email): bool
    {
        $result = filter_var($email, FILTER_VALIDATE_EMAIL);
        return $result !== false;
    }
    
    protected function checkMxRecord(string $email): bool
    {
        $parts = explode('@', $email);
        $domain = $parts[1];
        
        return $this->mxChecker->checkMxRecord($domain);
    }

    protected function formatResult(array $results): string
    {
        $output = '';
        foreach ($results as $email => $result) {
            $output .= "Email: $email\n";
            $output .= "Valid format: " . ($result['format_is_valid'] ? 'Yes' : 'No') . "\n";
            $output .= "Valid MX: " . ($result['mx_is_valid'] ? 'Yes' : 'No') . "\n";
            $output .= "Overall valid: " . ($result['valid'] ? 'Yes' : 'No') . "\n";
            $output .= "-----------------\n";
        }
        
        return $output;
    }
}