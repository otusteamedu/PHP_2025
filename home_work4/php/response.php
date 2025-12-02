<?php

function response($success, $message)
{
    if($success) http_response_code(200);
    else
          http_response_code(405);
    echo json_encode(["success"=>$success, "message"=>$message], JSON_UNESCAPED_UNICODE);
}