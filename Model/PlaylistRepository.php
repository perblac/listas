<?php

use PSpell\Config;

class PlaylistRepository {
    public static function getListsByUser($id) {
        $bd = Conectar::conexion();

        $listas = [];
        $q = "SELECT * FROM playlist WHERE user_id = '".$id."'";
        
        $result=$bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            if ($datos['deleted'] == 0) {
                $listas[] = new Playlist($datos);
            }
        }
        return $listas;
    }

    public static function getListById($id) : Playlist {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM playlist WHERE id = '".$id."'";
        
        $result=$bd->query($q);
        if ($datos = $result->fetch_assoc()) {
            if ($datos['deleted'] == 0) {
                $lista = new Playlist($datos);
                return $lista;
            }
        }
        return null;
    }

    // public static function savePlaylist($name, $user_id, $songsIds) {
    //     $bd = Conectar::conexion();

    //     $q = "INSERT INTO playlist VALUES (NULL, '".$name."', ".$user_id.")";
    //     $result=$bd->query($q);
    //     $q = "SELECT id FROM playlist WHERE name = '".$name."' AND user_id = ".$user_id;
    //     $result = $bd->query($q);
    //     $datos = $result->fetch_array();
    //     $playlistId = $datos[0];

    //     foreach ($songsIds as $song_id) {
    //         $q = "INSERT INTO `song_playlist` VALUES (".$song_id." ,".$playlistId." )";
    //         $result = $bd->query($q);
    //     }

    //     return $playlistId;
    // }
    
    public static function savePlaylist($playlist) {
        $bd = Conectar::conexion();

        $q = "INSERT INTO playlist VALUES (NULL, '".$playlist->getName()."', ".$playlist->getCreator().", 0)";
        $result=$bd->query($q);
        $q = "SELECT id FROM playlist WHERE name = '".$playlist->getName()."' AND user_id = ".$playlist->getCreator();
        $result = $bd->query($q);
        $datos = $result->fetch_array();
        $playlistId = $datos[0];
        $playlist->setId($playlistId);

        foreach ($playlist->getSongsIds() as $song_id) {
            if (!PlaylistRepository::checkSongInPlaylist($song_id,$playlist->getId())) {
                $q = "INSERT INTO `song_playlist` VALUES (".$song_id." ,".$playlist->getId()." )";
                $result = $bd->query($q);
            }
        }

        return $playlistId;
    }

    public static function checkSongInPlaylist($id_song, $id_playlist) {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM song_playlist WHERE id_song = ".$id_song." AND id_playlist = ".$id_playlist;
        $result = $bd->query(($q));
        if ($result->num_rows > 0) return true;
        else return false;
    }

    public static function deletePlaylist($id) {
        $bd = Conectar::conexion();

        $q = "UPDATE playlist SET deleted = 1 WHERE id = ".$id;
        $result = $bd->query($q);
    }

    public static function addSongToPlaylist($s,$pl) {
        $bd = Conectar::conexion();
        $q = "SELECT * FROM `song_playlist` WHERE id_song = ".$s." AND id_playlist = ".$pl;
        $result = $bd->query($q);
        if ($result->num_rows == 0) {
            $q = "INSERT INTO `song_playlist` VALUES (".$s.", ".$pl.")";
            $result = $bd->query($q);
        }
    }

    public static function getPlaylistDuration($pl) {
        $bd = Conectar::conexion();

        $q = "SELECT duration FROM song JOIN song_playlist ON song.id = song_playlist.id_song WHERE id_playlist = ".$pl;
        $result = $bd->query($q);
        $duration = 0;
        while ($datos = $result->fetch_assoc()) {
            $duration += $datos['duration'];
        }
        return $duration;
    }
}
?>