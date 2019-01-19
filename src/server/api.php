<?php

session_start();

include "API/RESTFactory.php";
include "HTTP/Request/RESTRequest.php";

$request = new RESTRequest($_SERVER);
$handler = RESTFactory::createRestHandler($request);

# Process then REST call
$response = $handler->respond();

# Set the proper response code
header($response->getStatusLine());

# Set the headers
foreach($response->getHeaders() as $header) {
    header($header->asString());
}

# Output the response
echo $response->getResponseBody();

?>