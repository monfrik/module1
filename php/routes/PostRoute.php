<?php
class PostRoute extends Route
{
    public function __construct()
    {
        parent::__construct();
        $url = str_replace( 'php'.'/', '', $_GET['url'] );
        $api = explode( '/', $url );
        switch ( $api[0] ){
            case 'auth': {
                $this->authorization();
            } break;
            case 'posts': {
                $isComments = strripos( $_GET['url'], 'comments');
                if ( !$isComments ) {
                    $this->checkToken(false);
                    switch ( sizeof($api) ){
                        case 1: {
                            $this->addPost();
                        } break;
                        case 2: {
                            $this->updatePost($api[1]);
                        } break;
                    }
                } else {
                    $this->addComments($api[1]);
                }
            } break;
        }
    }

    public function addPost()
    {
        if (!empty($_POST)){
            if (!empty($_FILES)){
                if (!$this->db->checkTitle($_POST['title'])) {
                    $image = Image::saveImage();
                    if ($image){
                        $post = $this->db->addPost($_POST['title'], $_POST['anons'], $_POST['text'], $image);
                        if ($post){
                            $this->response( 201, 'Successful creation', ['status' => true, 'post_id' => $post] );
                        } else {
                            $this->response( 500, 'databases error', ['status' => false, 'message' => 'error insert'] );
                        }
                    } else {
                        $this->response( 400, 'creating error', ['status' => false, 'message' => ['image'=>'unaploaded']] );
                    }
                } else {
                    $this->response( 400, 'creating error', ['status' => false, 'message' => 'this title already exist'] );
                }
            } else {
                $this->response( 400, 'creating error', ['status' => false, 'message' => ['file'=>'empty']] );
            }
        } else {
            $this->response( 400, 'creating error', ['status' => false, 'message' => ['form'=>'empty']] );
        }
    }
    public function updatePost($id)
    {
        if ($this->db->checkId($id)) {
            if (!empty($_POST)){
                if (!empty($_FILES)){
                    $image = Image::saveImage();
                    if ($image){
                        $post = $this->db->updatePost($id, $_POST['title'], $_POST['anons'], $_POST['text'], $image);
                        if ($post){
                            $this->response( 201, 'Successful creation', ['status' => true, 'post' => $post] );
                        } else {
                            $this->response( 500, 'databases error', ['status' => false, 'message' => 'error insert'] );
                        }
                    } else {
                        $this->response( 400, 'editing error', ['status' => false, 'message' => ['image'=>'unaploaded']] );
                    }
                } else {
                    $this->response( 400, 'editing error', ['status' => false, 'message' => ['file'=>'empty']] );
                }
            } else {
                $this->response( 400, 'editing error', ['status' => false, 'message' => ['form'=>'empty']] );
            }
        } else {
            $this->response( 404, 'post not found', ['status' => false, 'message' => 'post not found'] );
        }
    }
    public function addComments($id){
        if ($this->db->checkId($id)) {
            $success = true;
            $error = [];
                if (empty($_POST['author'])) {
                    if ( $this->checkToken(true) ) {
                        $_POST['author'] = 'admin';
                    }
                    else {
                        $success = false;
                        $error['author'] = 'empty';
                    }
                }
                if ( strlen($_POST['comment']) > 255 || strlen($_POST['comment']) <= 0){
                    $success = false;
                    $error['comment'] = 'more than 255 characters';
                }
                if ( intval($_POST['rating']) >= 5 || intval($_POST['rating']) <= 1 ) {
                    $success = false;
                    $error['rating'] = 'must be > 1 and < 5';
                }
                if ($success) {
                    $data = $this->db->addComments( $id, $_POST['author'], $_POST['comment'], $_POST['rating'] );
                    if ($data){
                        $this->response( 201, 'Successful creation', ['status' => true] );
                    } else {
                        $this->response( 500, 'databases error', ['status' => false, 'message' => 'error insert'] );
                    }
                } else {
                    $this->response( 400, 'creating error', ['status' => false, 'message' => $error] );
                }
        } else {
            $this->response( 404, 'post not found', ['status' => false, 'message' => 'post not found'] );
        }
    }
    public function authorization()
    {
        $data = $this->db->auth( $_POST['login'], $_POST['password'] );
        if ( !$data ){
            $this->response( 401, 'invalid authorization data', ['status' => false, 'message' => 'invalid authorization data'] );
        } else {
            $this->response( 200, 'successful authorization', ['status' => true, 'token' => $data] );
        }
    }
}