<?php
class ViewRepository
{
    public static function printUserHeader($user)
    {
        $s  = "Bienvenido " . $user->getName() . " <sub>(Rol: " . $user->getRol() . ")</sub><br>";
        $s .= '<nav>';
        $s .=   '<a href="index.php">Home</a>';
        $s .=   '<a href="index.php?c=search&explore=1">Explorar playlists</a>';
        $s .=   '<a href="index.php?c=list&createList=1">Crear nueva lista</a>';
        $s .=   '<a href="index.php?c=song&createSong=1">Crear nueva canción</a>';
        $s .=   '<a href="index.php?c=user&logout=1">LogOut</a>';
        $s .= '</nav>';
        return $s;
    }

    public static function printPlaylists($playlists)
    {
        $s = '';
        $unfav = "♡";
        $fav = "♥";
        $del = "␡";
        foreach ($playlists as $list) {
            $showDelete = $list->getCreator() == $_SESSION['user']->getId();
            $s .= '<div ' . (($showDelete) ? 'class="aPlaylist"' : 'class="aPlaylistFav"') . ' >';
            $s .=   '<h2><a href="index.php?c=list&list=' . $list->getId() . '">' . $list->getName() . '</a>';
            if ($showDelete) {
                $s .= '&nbsp;<a href="index.php?c=list&deleteList=' . $list->getId() . '">' . $del . '</a>';
            }
            if (PlaylistRepository::checkIfFavved($list, $_SESSION['user'])) {
                $s .= '&nbsp;<a class="fav" href="index.php?c=list&unfavList=' . $list->getId() . '">' . $fav . '</a>';
            } else {
                $s .= '&nbsp;<a class="fav" href="index.php?c=list&favList=' . $list->getId() . '">' . $unfav . '</a>';
            }
            $s .=   '</h2>';
            $s .=   '<h6>(canciones en la lista: ' . sizeof($list->getSongsIds()) . ')</h6>';
            $s .=   '<h6>(duración total: ' . ViewRepository::printDuration(PlaylistRepository::getPlaylistDuration($list->getId())) . ')</h6>';
            $s .= '</div>';
        }
        return $s;
    }

    public static function printDetailedPlaylist($list)
    {
        $s = '';
        $showDelete = $list->getCreator() == $_SESSION['user']->getId();
        $s .= '<div>';
        $s .=   '<div class="headDetalle">';
        $s .=       '<h2>Lista: ' . $list->getName() . '</h2>';
        $s .=       '<h3><i>creada por ' . UserRepository::getUserById($list->getCreator())->getName() . '</i></h3>';
        $s .=       '<h4>(canciones en la lista: ' . sizeof($list->getSongsIds()) . ')</h4>';
        $s .=   '</div>';
        $s .=   '<ul>';
        $list->loadSongs();
        foreach ($list->getSongsObjects() as $song) {
            if ($song != null) {
                $s .= '<li><p>' . $song->getTitle() . ' <sub>by</sub> ' . $song->getAuthor() . '. (duración ' . ViewRepository::printDuration($song->getDuration()) . ')';
                if ($showDelete) {
                    $s .= '(<a href="index.php?c=list&remove=' . $song->getId() . '&list=' . $list->getId() . '">X</a>)';
                }
                if ($song->getMp3File() !== '<no file>') {
                    $s .= '<audio controls style="height: 1em"><source src="upload/' . $song->getMp3File() . '" type="audio/mpeg"></audio></p></li>';
                }
            }
        }
        $s .=   '</ul>';
        if ($_SESSION['user']->getId() == $list->getCreator()) {
            $s .= '<a class="derecha" href="index.php?c=list&addSong=1&list=' . $list->getId() . '">Añadir canción</a><br>';
        }
        $s .=   '<h4>(duración total: ';
        $s .= ViewRepository::printDuration(PlaylistRepository::getPlaylistDuration($list->getId()));
        $s .=   ')</h4>';
        $s .= ViewRepository::printFullPlayback($list);
        $s .= '</div>';
        return $s;
    }

    public static function printSearchResults($songsList)
    {
        $s = sizeof($songsList) . ' resultados:<br>';
        $lists = PlaylistRepository::getListsByUser($_SESSION['user']);
        foreach ($songsList as $song) {
            $s .= ' - ' . $song->getTitle() . ' <sub>by</sub> ' . $song->getAuthor() . ' (' . ViewRepository::printDuration($song->getDuration()) . ' s.)';
            $s .= '<form action="index.php?c=list" method="POST" style="display:inline">';
            $s .=   '<input type="hidden" name="idSong" value="' . $song->getId() . '">';
            $s .=   '<input type="submit" name="addSongToList" value="Añadir a lista:" >';
            $s .=   '<select name="idPlaylist">';
            if (isset($_SESSION['creandoLista'])) {
                $s .= '<option value = 0 >' . $_SESSION['listaTemporal']->getName() . ' (lista en creación)</option>';
            }
            foreach ($lists as $list) {
                $s .= '<option value = ' . $list->getId() . '>' . $list->getName() . '</option>';
            }
            $s .=   '</select>';
            $s .= '</form>';
            $s .= '<br>';
        }
        return $s;
    }

    public static function printAddSongToPlaylist($playlist)
    {
        $s = '<div class="background">';
        $s .=   '<div class="addSong">';
        $s .=       '<a href="index.php?c=list&list=' . $_GET['list'] . '" class="derecha">X</a>';
        $s .=       '<h2>' . $playlist->getName() . '</h2>';
        $s .=       '<h4>(canciones en la lista: ' . sizeof($playlist->getSongsIds()) . ')</h4>';
        $playlist->loadSongs();
        foreach ($playlist->getSongsObjects() as $song) {
            if ($song != null) {
                $s .= '<p>' . $song->getTitle() . '</p>';
            }
        }
        $s .=       '<h4>(duración total: ' . ViewRepository::printDuration(PlaylistRepository::getPlaylistDuration($playlist->getId())) . ')</h4>';
        $s .=       '<form action="index.php?c=list&list=' . $_GET['list'] . '" method="POST">';
        $s .=           '<input list="canciones" name="idSong" id="input-datalist">';
        $s .=           '<datalist id="canciones">';
        $songsIds = SongRepository::getAllSongsIds();
        $unicodeChar = "\u{200B}";
        foreach ($songsIds as $songId) {
            $s .=       '<option value="' . SongRepository::getSongById($songId)->getTitle() . ' -' . $unicodeChar . ' ' . SongRepository::getSongById($songId)->getAuthor() . '">';
        }
        $s .=           '</datalist>';
        $s .=           '<input type="submit" name="addThisSong" value="Añadir" />';
        $s .=       '</form>';
        $s .=   '</div>';
        $s .= '</div>';
        return $s;
    }

    public static function printDuration($seconds)
    {
        $s = '';
        $minDur = floor($seconds / 60);
        if ($minDur > 60) {
            $horDur = floor($minDur / 60);
            $minDur = $minDur % 60;
            $s .= $horDur . ':';
        }
        $segDur = $seconds % 60;
        $strMin = str_pad($minDur, 2, '0', STR_PAD_LEFT);
        $s .= $strMin . ':';
        $strSeg = str_pad($segDur, 2, '0', STR_PAD_LEFT);
        $s .= $strSeg;
        return $s;
    }

    public static function printFullPlayback($playlist)
    {
        $s = '';
        $mp3objects = $playlist->getMp3Objects();
        if (!empty($mp3objects)) {
            $s .= '<nav class="listaCompletaPlayer">';
            $s .=   '<div><h4>Reproducir lista completa</h4>';
            $s .=   '</div>';
            $s .=   '<div><audio id="fullListPlayer" preload="auto" tabindex="0" controls="" type="audio/mpeg">';
            $s .=       '<source type="audio/mp3" src="upload/' . $mp3objects[0]->getMp3File() . '">';
            $s .=       'Sorry, your browser does not support HTML5 audio.';
            $s .=   '</audio></div>';
            $s .=   '<div><ul id="playlist">';
            $s .=       '<li class="active"><a href="upload/' . $mp3objects[0]->getMp3File() . '">' . $mp3objects[0]->getAuthor() . ' - ' . $mp3objects[0]->getTitle() . '</a></li>';
            array_shift($mp3objects);
            foreach ($mp3objects as $song) {
                $s .= '<li><a href="upload/' . $song->getMp3File() . '">' . $song->getAuthor() . ' - ' . $song->getTitle() . '</a></li>';
            }
            $s .=   '</ul></div>';
            $s .= '</nav>';
            $s .= '<script>';
            $s .= 'let audio; let playlist; let tracks; let current;';
            $s .= "init(); function init() {
                    current = 0;
                    audio = document.getElementById('fullListPlayer');
                    playlist = document.getElementById('playlist');
                    tracks = playlist.getElementsByTagName('li');
                    len = tracks.length - 1;
                    audio.volume = .10;";
            $s .=  "for (let i = 0; i < tracks.length; i++) {
                        let link = tracks[i].getElementsByTagName('a')[0];
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            let clickedLink = this;
                            current = Array.prototype.indexOf.call(clickedLink.parentNode.parentNode.children, clickedLink.parentNode);
                            run(clickedLink, audio);
                        } );
                    }";
            $s .= "audio.addEventListener('ended', function(e) {
                        current++;
                        let link = null;
                        if (current == tracks.length) {
                            current = 0;
                            link = playlist.getElementsByTagName('a')[0];
                        } else {
                            link = playlist.getElementsByTagName('a')[current];
                        }
                        run(link, audio);
                        });
                   }";
            $s .= "function run(link, player) {
                    player.src = link.href;
                    let parent = link.parentNode;
                    parent.classList.add('active');
                    let siblings = parent.parentNode.children;
                    for (let i = 0; i < siblings.length; i++) {
                        if (siblings[i] !== parent) {
                            siblings[i].classList.remove('active');
                        }
                    }";
            $s .=  'audio.load(); audio.play();
                   }';
            $s .= '</script>';
        }
        return $s;
    }
}
?>