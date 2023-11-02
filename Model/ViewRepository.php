<?php
class ViewRepository {
    public static function printUserHeader($user) {
        $s  = "Usuario: ".$user->getName()." Rol: ".$user->getRol()."<br>";
        $s .= '<a href="index.php?c=user&logout=1">LogOut</a><br>';
        return $s;
    }

    public static function printPlaylists($playlists) {
        $s = '';
        foreach ($playlists as $list) {
            $s .= '<div style="margin:2px;border:1px solid lightgray;">';
            $s .= '<h2><a href="index.php?c=list&list='.$list->getId().'">'.$list->getName().'</a>';
            if ($list->getCreator() == $_SESSION['user']->getId()) {
                $s .= '&nbsp;<a href="index.php?c=list&deleteList='.$list->getId().'">X</a>';
            }
            if (PlaylistRepository::checkIfFavved($list, $_SESSION['user'])) {
                $s .= '&nbsp;<a href="index.php?c=list&unfavList='.$list->getId().'">-F</a>';
            } else {
                $s .= '&nbsp;<a href="index.php?c=list&favList='.$list->getId().'">+F</a>';
            }
            $s .= '</h2>';
            $s .= '<h4>(canciones en la lista: '.sizeof($list->getSongsIds()).')</h4>';
            $s .= '<h4>(duración total: '.ViewRepository::printDuration(PlaylistRepository::getPlaylistDuration($list->getId())).')</h4>';
            $s .= '</div>';
        }
        return $s;
    }

    public static function printDuration($seconds) {
        $s = '';
        $minDur = floor($seconds/60);
        if ($minDur > 60) {
            $horDur = floor($minDur/60);
            $minDur = $minDur % 60;
            $s .= $horDur . ':';
        }
        $segDur = $seconds % 60;
        $strMin = str_pad($minDur,2,'0',STR_PAD_LEFT);
        $s .= $strMin . ':';
        $strSeg = str_pad($segDur,2,'0',STR_PAD_LEFT);
        $s .= $strSeg;
        return $s;
    }

    public static function printFullPlayback($playlist) {
        $s = '';
        $mp3objects = $playlist->getMp3Objects();
        if (!empty($mp3objects)) {
            $s .= '<nav style="border:1px solid black;margin: 5px;width:fit-content;padding: 5px;"><h4>Reproducir lista completa</h4>';
            $s .= '<audio id="fullListPlayer" preload="auto" tabindex="0" controls="" type="audio/mpeg">';
            $s .= '<source type="audio/mp3" src="upload/'.$mp3objects[0]->getMp3File().'">';
            $s .= 'Sorry, your browser does not support HTML5 audio.';
            $s .= '</audio>';
            $s .= '<ul id="playlist">';
            $s .= '<li class="active"><a href="upload/'.$mp3objects[0]->getMp3File().'">'.$mp3objects[0]->getAuthor().' - '.$mp3objects[0]->getTitle().'</a></li>';
            array_shift($mp3objects);
            foreach ($mp3objects as $song) {
                $s .= '<li><a href="upload/'.$song->getMp3File().'">'.$song->getAuthor().' - '.$song->getTitle().'</a></li>';
            }
            $s .= '</ul></nav>';
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
                            console.log(current);
                            run(clickedLink, audio);
                        } );
                    }";
            $s .= "audio.addEventListener('ended', function(e) {
                        current++;
                        if (current == tracks.length) {
                            current = 0;
                            let link = playlist.getElementsByTagName('a')[0];
                        } else {
                            let link = playlist.getElementsByTagName('a')[current];
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