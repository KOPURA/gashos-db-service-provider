<?php

include 'HTTP/Response.php';

interface IRestHandler {

    public function respond(): Response;
}

?>