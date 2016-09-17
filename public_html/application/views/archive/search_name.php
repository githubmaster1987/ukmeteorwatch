<h1>Search results for <?=$name?></h1>
<hr>
<?php

if (is_array($records) && count($records))
{
    $counter = 0;

    $items = array();

    foreach ($records as $record)
    {
        if (fmod($counter, 4) == 0 && count($items))
        {
            echo '<div class="row">' . implode("\n", $items) . '</div><hr>';
            $items = array();
        }

        //perseids/12-08-2013/clanfield-03-08-36-picture5
        $details_uri = 'archive/detail/'.$record->event_slug.'/'.$record->station_slug.'/'.$record->archive_id;

        $items[] =
            '<div class="col-md-3">'
                . '<h4>' . date('d/m/Y H:i:s', $record->created_at).'.'.$record->created_at_ms . '</h4>'
                . anchor($details_uri, '<img alt="Meteor!" src="' . ARCHIVES_IMG_URI . $record->image_folder . $record->image . '" class="img-responsive" />')
                . '<h5>' . $record->station_name . '</h5> '
                . '<p>' . str_replace('P.jpg', '', $record->image) . '</p> '
                . '<p>mag="' . $record->mag . '"</p> '
                . '<p>class="' . $record->event_code . '"</p> '
            . '</div>'
        ;

        $counter++;
    }

    if (count($items))
    {
        echo '<div class="row">' . implode("\n", $items) . '</div>';
    }

    echo !empty($pagination) ? $pagination : '';
}
?>