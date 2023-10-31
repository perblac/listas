<?php

if (isset($_GET['query']) && $_GET['query'] != '' ) {
    $songs = SongRepository::searchSongs($_GET['query'],$_GET['campo']);    
}

include('View/searchView.phtml');
?>