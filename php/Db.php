<?php
class Db
{
    private $PDO;
    public function __construct()
    {
        $this->PDO = new PDO('mysql:host=localhost;dbname=module1', 'root', '');
    }
    public function auth($login, $password)
    {
        $sth = $this->PDO->prepare('SELECT `bearer` FROM `admin` WHERE `login` = ? AND `password` = ?');
        $sth->execute(array($login, $password));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
        return ($data == NULL) ? false : $data['bearer'];
    }
    public function checkToken($token)
    {
        $sth = $this->PDO->prepare('SELECT `bearer` FROM `admin` WHERE `bearer` = ?');
        $sth->execute(array($token));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
        return ($data == NULL) ? false : true;
    }
    public function checkTitle($title)
    {
        $sth = $this->PDO->prepare('SELECT `title` FROM `posts` WHERE `title` = ?');
        $sth->execute(array($title));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
        return ($data == NULL) ? false : true;
    }
    public function checkId($id)
    {
        $sth = $this->PDO->prepare('SELECT `title` FROM `posts` WHERE `id` = ?');
        $sth->execute(array($id));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
        return ($data == NULL) ? false : true;
    }
    public function checkIdComment($id)
    {
        $sth = $this->PDO->prepare('SELECT * FROM `comments` WHERE `id` = ?');
        $sth->execute(array($id));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
        return ($data == NULL) ? false : true;
    }
    public function addPost($title, $anons, $text, $image)
    {
        $sth = $this->PDO->prepare('INSERT INTO `posts` (title, anons, text, image, datatime) VALUES (?, ?, ?, ?, ?)');
        if ($sth->execute(array($title, $anons, $text, $image, date('Y-m-d')))){
            return $this->PDO->lastInsertId(); 
        } else {
            return false;
        }
    }
    public function updatePost($id, $title, $anons, $text, $image)
    {
        $sth = $this->PDO->prepare('UPDATE `posts` SET title=?, anons=?, text=?, image=? WHERE id=?');
        $sth->execute(array($title, $anons, $text, $image, $id));
        $sth2 = $this->PDO->prepare('SELECT title, datatime, anons, text, image FROM `posts` WHERE id=?');
        $sth2->execute(array($id));
        $data = $sth2->fetchAll(PDO::FETCH_ASSOC)[0];
        return ($data == NULL) ? false : $data;
    }
    public function postDelete($id)
    {
        $sth = $this->PDO->prepare('DELETE FROM `posts` WHERE id=?');
        return $sth->execute(array($id));
    }
    public function getPost($id)
    {
        $sth = $this->PDO->prepare("SELECT posts.title, posts.datatime, posts.anons, posts.text, posts.rating, posts.image FROM `posts` WHERE posts.id = ?");
        $sth->execute(array($id));
        $data = $sth->fetchAll(PDO::FETCH_ASSOC)[0];
        $sth2 = $this->PDO->prepare("SELECT comments.id, comments.datatime, comments.author, comments.comment FROM `comments` WHERE comments.id_post = ?");
        $sth2->execute(array($id));
        $data['comments'] = $sth2->fetchAll(PDO::FETCH_ASSOC);
        return ($data == NULL) ? false : $data;
    }
    public function getAllPosts()
    {
        $sth = $this->PDO->prepare('SELECT title, datatime, anons, text, rating, image FROM `posts`');
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        return ($data == NULL) ? false : $data;
    }
    public function addComments($id, $author, $comment, $rating)
    {
        $sth = $this->PDO->prepare('INSERT INTO `comments` (author, comment, id_post, datatime) VALUES (?, ?, ?, ?)');
        if ($sth->execute(array($author, $comment, $id, date('Y-m-d')))){
            if (!empty($rating)) {
                $sth2 = $this->PDO->prepare('UPDATE `posts` SET rating=? WHERE id=?');
                if (!$sth2->execute(array($rating, $id))){
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }
    public function commentDelete($id)
    {
        $sth = $this->PDO->prepare('DELETE FROM `comments` WHERE id=?');
        return $sth->execute(array($id));
    }
} 