<?php
class DeleteRoute extends Route
{
    public function __construct()
    {
        parent::__construct();
        $this->checkToken(false);
        $url = str_replace( 'php'.'/', '', $_GET['url'] );
        $api = explode( '/', $url );
        switch (sizeof($api)) {
            case 2: 
            {
                $this->postDelete($api[1]);
            } break;
            case 3: 
            {
                $this->response( 404, 'post not found', ['status' => false, 'message' => 'post not found'] );
            } break;
            case 4: 
            {
                $this->commentDelete($api[1], $api[3]);
            } break;
        }
    }
    public function postDelete($id)
    {
        if ($this->db->checkId($id)) {
            if ($this->db->postDelete($id)){
                $this->response( 201, 'Successful delete', ['status' => true] );
            } else {
                $this->response( 500, 'databases error', ['status' => false, 'message' => 'error delete'] );
            }
        } else {
            $this->response( 404, 'post not found', ['status' => false, 'message' => 'post not found'] );
        }
    }
    public function commentDelete($idPost, $idComm)
    {
        if ($this->db->checkId($idPost)) {
            if ($this->db->checkIdComment($idComm)) {
                if ($this->db->commentDelete($idComm)){
                    $this->response( 201, 'Successful delete', ['status' => true] );
                } else {
                    $this->response( 500, 'databases error', ['status' => false, 'message' => 'error delete'] );
                }
            } else {
                $this->response( 404, 'comment not found', ['status' => false, 'message' => 'comment not found'] );
            }
        } else {
            $this->response( 404, 'post not found', ['status' => false, 'message' => 'post not found'] );
        }
    }
}