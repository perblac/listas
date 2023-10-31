<?php
class PlaylistRepository {
    public static function getListsByUser($id) {
        $bd = Conectar::conexion();

        $listas = [];
        $q = "SELECT * FROM playlist WHERE user_id = '".$id."'";
        
        $result=$bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            $listas[] = new Playlist($datos);
        }
        return $listas;
    }

    public static function getListById($id) : Playlist {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM playlist WHERE id = '".$id."'";
        
        $result=$bd->query($q);
        if ($datos = $result->fetch_assoc()) {
            $lista = new Playlist($datos);
            return $lista;
        }
        return null;
    }

    public static function savePlaylist($name, $user_id, $songsIds) {
        $bd = Conectar::conexion();

        $q = "INSERT INTO playlist VALUES (NULL, '".$name."', ".$user_id.")";
        $result=$bd->query($q);
        $q = "SELECT id FROM playlist WHERE name = '".$name."' AND user_id = ".$user_id;
        $result = $bd->query($q);
        $datos = $result->fetch_array();
        $playlistId = $datos[0];

        foreach ($songsIds as $song_id) {
            $q = "INSERT INTO `song-playlist` VALUES (".$song_id." ,".$playlistId." )";
            $result = $bd->query($q);
        }

        return $playlistId;
    }

    public static function deletePlaylist($id) {
        $bd = Conectar::conexion();

        $q = "DELETE FROM playlist WHERE id = ".$id;
        $result = $bd->query($q);
    }

    public static function addSongToPlaylist($s,$pl) {
        $bd = Conectar::conexion();

        $q = "SELECT * FROM `song-playlist` WHERE id_song = ".$s." AND id_playlist = ".$pl;
        $result = $bd->query($q);
        if ($result->num_rows == 0) {
            $q = "INSERT INTO `song-playlist` VALUES (".$s.", ".$pl.")";
            $result = $bd->query($q);
        }

    }
}
?>