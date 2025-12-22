<?php

declare(strict_types=1);

use App\Infrastructure\ElasticsearchBookRepository;
use App\UserInterface\SearchBooksCommand;

// $emailsFilePath = dirname(__DIR__) . '/resources/emails.txt';
// $checkedEmailsFilePath = dirname(__DIR__) . '/resources/checked_emails.txt';

return [
    // 'dnsEmailVerifier' => function() {
    //     return new DnsEmailVerifier();
    // },

    // 'emailsFileReader' => function() use ($emailsFilePath) {
    //     return new EmailsFileReader($emailsFilePath);
    // },

    // 'checkedEmailsFileWriter' => function() use ($checkedEmailsFilePath) {
    //     return new CheckedEmailsFileWriter($checkedEmailsFilePath);
    // },

    // 'verificationEmailService' => function($container) {
    //     return new VerificationEmailService(
    //         $container->get('checkedEmailsFileWriter'),
    //         $container->get('emailsFileReader'),
    //         $container->get('dnsEmailVerifier'),
    //     );
    // },
    //
    'searchBooksCommand' => function($container) {
        return new SearchBooksCommand(
            // $container->get('searchCommand'),
        );
    }
];
