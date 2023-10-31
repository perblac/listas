<?php
class SongRepository {
    public static function getIdsSongsInPlaylist($id) {
        $bd = Conectar::conexion();

        $songsIds = [];
        $q = "SELECT * FROM `song_playlist` WHERE id_playlist = '".$id."'";
        $result = $bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            $songsIds[] = $datos['id_song'];
        }
        return $songsIds;
    }

    public static function getSongById($id) {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM `song` WHERE id = '".$id."'";
        $result = $bd->query($q);
        if ($result->num_rows > 0)  {
            $datos = $result->fetch_assoc();
            $song = new Song($datos);
            return $song;
        } else
        return null;
    }
/*
    public static function getIdByTitle($t) {
        $bd = Conectar::conexion();

        $q = "SELECT id FROM `song` WHERE title = '".$t."'";
        echo $q;
        $result = $bd->query($q);
        $datos = $result->fetch_array();
        return $datos[0];
    }
*/
    public static function getIdByTitleAndAuthor($title, $author) {
        $bd = Conectar::conexion();

        $q = "SELECT id FROM `song` WHERE title = '".$title."' AND author = '".$author."'";
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

    public static function saveSong(Song $song) {
        $bd = Conectar::conexion();

        $q = "INSERT INTO song VALUES (NULL, '".$song->getTitle()."', '".$song->getAuthor()."', ".$song->getDuration().", '".$song->getMp3File()."', ".$song->getCreator().")";
        $result = $bd->query($q);        
        
        $q = "SELECT id FROM song WHERE title = '".$song->getTitle()."' AND author = '".$song->getAuthor()."' AND user_id = ".$song->getCreator();
        $result = $bd->query($q);        
        $datos = $result->fetch_array();
        return $datos[0];
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
}
?>