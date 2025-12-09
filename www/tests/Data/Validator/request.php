<?php

return
    [
        "correct_request_1" => [
            "input" => "POST",
            "expected" => "void"
        ],
        "incorrect_request_1" => [
            "input" => "EMPTY",
            "expected" => "Exception"
        ],
        "incorrect_request_2" => [
            "input" => "GET",
            "expected" => "Exception"
        ],
    ];
