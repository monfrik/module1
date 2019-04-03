<?php
class Route
{
    public $db;
    public function __construct()
    {
        $this->db = new Db();
    }
    public function response($headerStatus, $headerText, $responseBody){
        header("HTTP/1.1 $headerStatus $headerText");
        echo json_encode($responseBody);
        die();
    }
    public function checkToken($checkOnly)
    {
        $bearer = getallheaders()['authorization'];
        $token = explode( ' ', $bearer )[1];
        if ( $token == null || !$this->db->checkToken($token)) {
            if ($checkOnly) {
                return false;
            }
            $this->response( 401, 'Unauthorized', ['message' => 'Unauthorized'] );
        }
        return true;
    }
}