<?php

if (isset($_GET['query']) && $_GET['query'] != '' ) {
    if ($_GET['campo'] !== 'playlist') {
        $songs = SongRepository::searchSongs($_GET['query'],$_GET['campo']);
    } else {
        $playlists = PlaylistRepository::searchPlaylists($_GET['query']);
    }
}

if (isset($_GET['explore'])) {
    $playlists = PlaylistRepository::getAllLists();
}
?>