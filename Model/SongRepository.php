<?php
class SongRepository {
    public static function getIdsSongsInPlaylist($id) {
        $bd = Conectar::conexion();
        $songsIds = [];
        $q = "SELECT * FROM song_playlist WHERE id_playlist = ".$id;
        $result = $bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            $songsIds[] = $datos['id_song'];
        }
        return $songsIds;
    }

    public static function getSongById($id) {
        $bd = Conectar::conexion();
        $q = "SELECT * FROM song WHERE id = ".$id;
        $result = $bd->query($q);
        if ($result->num_rows > 0)  {
            $datos = $result->fetch_assoc();
            $song = new Song($datos);
            return $song;
        } else
        return null;
    }
    
    public static function getIdByTitleAndAuthor($title, $author) {
        $bd = Conectar::conexion();
        $q = "SELECT id FROM song WHERE title = '".$title."' AND author = '".$author."'";
        $result = $bd->query($q);
        $datos = $result->fetch_array();
        return $datos[0];
    }

    public static function getAllSongsIds() {
        $bd = Conectar::conexion();
        $songsIds = [];
        $q = "SELECT id FROM song";
        $result = $bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            $songsIds[] = $datos['id'];
        }
        return $songsIds;
    }

    public static function searchSongs($query,$field) {
        $bd = Conectar::conexion();
        $songs = [];
        $q = "SELECT * FROM song WHERE `".$field."` LIKE '%".$query."%'";
        $result = $bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            $songs[] = new Song($datos);
        }
        return $songs;
    }

    public static function addSong($datos,$files) {
        $datos['id'] = null;
        $datos['user_id'] = $_SESSION['user']->getId();
        if ($files !== null) {
            $directory = 'upload/';
            $filename = $directory . basename($files['mp3file']['name']);
            $mp3file = $_FILES['mp3file']['name'];
            move_uploaded_file($files['mp3file']['tmp_name'], $filename);
            $getID3 = new getID3;
            $mp3song = $getID3->analyze($filename);
            $datos['duration'] = $mp3song['playtime_seconds'];
        } else {
            $mp3file = "<no file>";
        }
        $datos['mp3_file'] = $mp3file;
        $newSong = new Song($datos);
        return $newSong;
    }
}
?>