<?php
namespace Elisad5791\Phpapp;

class EmailVerifier implements EmailVerifierInterface
{
    public function __construct(protected MxCheckerInterface $mxChecker) {}

    public function verifyEmails(array $emails): array
    {
        $results = [];
        
        foreach ($emails as $email) {
            $results[$email] = $this->verifyEmail($email);
        }
        
        return $results;
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
}