<?php

include 'HTTP/Response.php';

interface RestHandler {

    public function respond(): Response;
}

?>