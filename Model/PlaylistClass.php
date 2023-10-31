<?php
class Playlist {
    private $id,
            $name,
            // $songs,
            $songsIds,
            $creator,
            $deleted,
            $songsObjects,
            $songsLoaded = false;
    public function __construct($datos)
    {
        $this->id = $datos['id'];
        $this->name = $datos['name'];
        // $this->songs = SongRepository::getIdsSongsInPlaylist($datos['id']);
        $this->songsIds = ($datos['id'] == 0)?[]:SongRepository::getIdsSongsInPlaylist($datos['id']);
        $this->creator = $datos['user_id'];
        $this->songsObjects = [];
        $this->deleted = $datos['deleted'];
    }
    public function getId() {
        return $this->id;
    }
    public function setId($newId) {
        $this->id = $newId;
    }
    public function getName() {
        return $this->name;
    }
    public function setName($newName) {
        $this->name = $newName;
    }
    // public function getSongs() {
    //     return $this->songs;
    public function getSongsIds() {
        return $this->songsIds;
    }
    public function getCreator() {
        return $this->creator;
    }
    public function getDeleted() {
        return $this->deleted;
    }
    public function getSongsObjects() {
        return $this->songsObjects;
    }
    public function loadSongs() {
        $this->songsObjects = [];
        foreach ($this->songsIds as $id) {
            $this->songsObjects[] = SongRepository::getSongById($id);
        }
        $this->songsLoaded = true;
    }
    public function getLoadedStatus() {
        return $this->songsLoaded;
    }
    public function addSong($song) {
        if ($song != null){
            $this->songsIds[] = $song->getId();
            if ($this->songsLoaded) {
                $this->songsObjects[] = $song;
            }
        }
        // PlaylistRepository::addSongToPlaylist($song->getId(), $this->id);
    }
}
?>