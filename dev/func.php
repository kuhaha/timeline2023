<?php
function _debug($data){
    echo '<pre>'; print_r($data); echo '</pre>';
}

function _print($events){
    echo '<table class="table">' . PHP_EOL;
    echo '<tr><td>start</td><td>end</td><td>title</td></tr>' . PHP_EOL;
    foreach ($events as $start=>$data){
        echo '<tr><td>', $start, '</td><td>', $data[0], '</td><td>', $data[1],'</td></tr>' . PHP_EOL;
    }
    echo '</table>' . PHP_EOL;
}
  