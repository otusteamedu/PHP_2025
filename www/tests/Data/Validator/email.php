<?php

return
    [
        "correct_email_1" => [
            "input" => "user@example.com",
            "expected" => "void"
        ],
        "correct_email_2" => [
            "input" => "user.name@example.com",
            "expected" => "void"
        ],
        "correct_email_3" => [
            "input" => "user_name@ya.ru",
            "expected" => "void"
        ],
        "correct_email_4" => [
            "input" => "user-name@gmail.com",
            "expected" => "void"
        ],
        "correct_email_5" => [
            "input" => "user123@domain.io",
            "expected" => "void"
        ],
        "incorrect_email_1" => [
            "input" => "",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailParameters"
        ],
        "incorrect_email_2" => [
            "input" => "emptyTest",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailParameters"
        ],
        "incorrect_email_3" => [
            "input" => "useruser",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_4" => [
            "input" => "@username.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_5" => [
            "input" => "username@.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_6" => [
            "input" => "username@domain..com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_7" => [
            "input" => "username@domain,com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_8" => [
            "input" => "username@-domain.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_9" => [
            "input" => "username@domain-.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_10" => [
            "input" => "user name@example.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_11" => [
            "input" => "user@.example.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_12" => [
            "input" => "13user@.example.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_13" => [
            "input" => "useruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruser@example.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailValidation"
        ],
        "incorrect_email_14" => [
            "input" => "user@domaindomain.com",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailHost"
        ],
        "incorrect_email_15" => [
            "input" => "user@incorrect.su",
            "expected" => "Larkinov\\Myapp\\Exceptions\\Email\\ExceptionEmailHost"
        ]
    ];
