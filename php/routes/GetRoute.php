<?php
class GetRoute extends Route
{
    public function __construct()
    {
        parent::__construct();
        $url = str_replace( 'php'.'/', '', $_GET['url'] );
        $api = explode( '/', $url );
        switch ( sizeof($api) ){
            case 1: {
                $this->getAllPosts();
            } break;
            case 2: {
                $id = $api[1];
                $this->getPost($id);
            } break;
        }
    }
    public function getPost($id)
    {
        if ($this->db->checkId($id)) {
            $data = $this->db->getPost($id);
            if ($data){
                $this->response( 200, 'view post', $data );
            } else {
                $this->response( 500, 'databases error', ['status' => false, 'message' => 'error get'] );
            }
        } else {
            $this->response( 404, 'post not found', ['status' => false, 'message' => 'post not found'] );
        }
    }
    public function getAllPosts()
    {
        $data = $this->db->getAllPosts();
        if ($data) {
            $this->response( 200, 'list posts', $data);
        } else {
            $this->response( 500, 'databases error', ['status' => false, 'message' => 'error get'] );
        }
    }
}