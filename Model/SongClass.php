<?php
class Song {
    private $id, $title, $author, $duration, $mp3file, $creator;

    public function __construct($datos)
    {
        $this->id = $datos['id'];
        $this->title = $datos['title'];
        $this->author = $datos['author'];
        $this->duration = $datos['duration'];
        $this->mp3file = $datos['mp3_file'];
        $this->creator = $datos['user_id'];        
    }

    public function getId() {
        return $this->id;
    }
    public function setId($newId) {
        $this->id =$newId;
    }

    public function getTitle() {
        return $this->title;
    }
    public function setTitle($newTitle) {
        $this->title = $newTitle;
    }
    public function getAuthor() {
        return $this->author;
    }
    public function setAuthor($newAuthor) {
        $this->author = $newAuthor;
    }
    public function getDuration() {
        return $this->duration;
    }
    public function setDuration($newDuration) {
        $this->duration = $newDuration;
    }
    public function getMp3File() {
        return $this->mp3file;
    }
    public function setMp3File($newMp3File) {
        $this->mp3file = $newMp3File;
    }
    public function getCreator() {
        return $this->creator;
    }

    public function saveSong() {
        $bd = Conectar::conexion();
        $song = $this;
        $q = "INSERT INTO song VALUES (NULL, '".$song->getTitle()."', '".$song->getAuthor()."', ".$song->getDuration().", '".$song->getMp3File()."', ".$song->getCreator().")";
        $result = $bd->query($q);        
        return $bd->insert_id;
    }
}
?>