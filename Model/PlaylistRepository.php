<?php
class PlaylistRepository {
    public static function getAllLists() {
        $bd = Conectar::conexion();
        $listas = [];
        $q = "SELECT * FROM playlist";        
        $result=$bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            if ($datos['deleted'] == 0) {
                $listas[] = new Playlist($datos);
            }
        }
        return $listas;
    }

    public static function getListsByUser($user) {
        $bd = Conectar::conexion();
        $listas = [];
        $q = "SELECT * FROM playlist WHERE user_id = ".$user->getId();       
        $result=$bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            if ($datos['deleted'] == 0) {
                $listas[] = new Playlist($datos);
            }
        }
        return $listas;
    }
    
    public static function getFavoritedListsByUser($user) {
        $bd = Conectar::conexion();
        $favIds = [];
        $q = "SELECT * FROM fav_playlist WHERE id_user = ".$user->getId();
        $result=$bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            $favIds[] = $datos['id_playlist'];
        }
        $listas = [];
        foreach ($favIds as $id_playlist) {
            $q = "SELECT * FROM playlist WHERE id = ".$id_playlist;            
            $result=$bd->query($q);
            while ($datos = $result->fetch_assoc()) {
                if ($datos['deleted'] == 0) {
                    $listas[] = new Playlist($datos);
                }
            }
        }
        return $listas;
    }

    public static function getListById($id) {
        $bd = Conectar::conexion();
        $q = "SELECT * FROM playlist WHERE id = ".$id;
        $result = $bd->query($q);
        if ($datos = $result->fetch_assoc()) {
            if ($datos['deleted'] == 0) {
                $lista = new Playlist($datos);
                return $lista;
            }
        }
        return null;
    }

    public static function savePlaylist($playlist) {
        $bd = Conectar::conexion();
        $q = "INSERT INTO playlist VALUES (NULL, '".$playlist->getName()."', ".$playlist->getCreator().", 0)";
        $result=$bd->query($q);
        $playlistId = $bd->insert_id;
        $playlist->setId($playlistId);
        foreach ($playlist->getSongsIds() as $song_id) {
            if (!PlaylistRepository::checkSongInPlaylist($song_id,$playlist->getId())) {
                $q = "INSERT INTO song_playlist VALUES (".$song_id." ,".$playlist->getId()." )";
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
        if (!PlaylistRepository::checkSongInPlaylist($s,$pl)) {
            $q = "INSERT INTO `song_playlist` VALUES (".$s.", ".$pl.")";
            $result = $bd->query($q);
        }
    }

    public static function removeSongFromPlaylist($s,$pl) {
        $bd = Conectar::conexion();
        $q = "SELECT * FROM song_playlist WHERE id_song = ".$s." AND id_playlist = ".$pl;
        $result = $bd->query($q);
        if ($result->num_rows > 0) {
            $q = "DELETE FROM song_playlist WHERE id_song = ".$s." AND id_playlist = ".$pl;
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

    public static function checkIfFavved($playlist, $user) {
        $bd = Conectar::conexion();
        $q = "SELECT * FROM fav_playlist WHERE id_user = ".$user->getId()." AND id_playlist = ".$playlist->getId();
        $result=$bd->query($q);
        if ($result->num_rows > 0) {
            return true;
        }
        return false;
    }

    public static function unfavPlaylist($playlist, $user) {
        if (PlaylistRepository::checkIfFavved($playlist, $user)) {
            $bd = Conectar::conexion();
            $q = "DELETE FROM fav_playlist WHERE id_user = ".$user->getId()." AND id_playlist = ".$playlist->getId();
            $result=$bd->query($q);
        }
    }

    public static function favPlaylist($playlist, $user) {
        if (!PlaylistRepository::checkIfFavved($playlist, $user)) {
            $bd = Conectar::conexion();
            $q = "INSERT INTO fav_playlist VALUES (".$user->getId().", ".$playlist->getId().")";
            $result=$bd->query($q);
        }
    }

    public static function searchPlaylists($query) {
        $bd = Conectar::conexion();
        $playlists = [];
        $q = "SELECT * FROM playlist WHERE name LIKE '%".$query."%'";
        $result = $bd->query($q);
        while ($datos = $result->fetch_assoc()) {
            if ($datos['deleted'] == 0) {
                $playlists[] = new Playlist($datos);
            }
        }
        return $playlists;
    }
}
?>