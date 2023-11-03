<?php

if (isset($_GET['query']) && $_GET['query'] != '' ) {
    if ($_GET['campo'] === 'playlist') {
        $playlists = PlaylistRepository::searchPlaylists($_GET['query']);
    } else {
        if ($_GET['campo'] === 'title' || $_GET['campo'] === 'author') {
            $songs = SongRepository::searchSongs($_GET['query'],$_GET['campo']);
        }
    }
}

?>