<?php
class Playlist {
    private $id, $name, $songs, $creator;
    public function __construct($datos)
    {
        $this->id = $datos['id'];
        $this->name = $datos['name'];
        $this->songs = SongRepository::getIdsSongsInPlaylist($datos['id']);
        $this->creator = $datos['user_id'];
    }
    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getSongs() {
        return $this->songs;
    }
    public function getCreator() {
        return $this->creator;
    }
}
?>