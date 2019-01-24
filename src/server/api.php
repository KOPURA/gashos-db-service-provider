<?php

session_start();

include "API/RESTFactory.php";
include "HTTP/Request/RESTRequest.php";

function setResponseSpecificHeaders($response) {
    # Set the proper response code
    header($response->getStatusLine());

    # Set the headers
    foreach($response->getHeaders() as $header) {
        header($header->asString());
    }
}

$request = new RESTRequest($_SERVER);
$handler = RESTFactory::createRestHandler($request);

# Process then REST call
$response = $handler->respond();

if ($handler->requiresBuffering()) {
    ob_start();

    echo $response->getResponseBody();
    $contentLength = ob_get_length();

    setResponseSpecificHeaders($response);

    header("Content-Encoding: none");
    header("Content-Length: {$contentLength}");
    header("Connection: close");

    ob_end_flush();
    ob_flush();
    flush();

    if (session_id()) {
        session_write_close();
    }

    if ($handler->isSuccess()) {
        set_time_limit(0);
        $handler->postProcess();
    }
} else {
    setResponseSpecificHeaders($response);

    # Output the response
    echo $response->getResponseBody();
}


?>