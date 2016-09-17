<?php

if (is_array($records) && count($records))
{
    $counter = 0;

    $items = array();
	
    foreach ($records as $record)
    {
        if (fmod($counter, $images_per_row) == 0 && count($items))
        {
            echo '<div class="row">' . implode("\n", $items) . '</div><hr>';
            $items = array();
        }

        //perseids/12-08-2013/clanfield-03-08-36-picture5
        $details_uri = 'meteors/detail/'.$record->event_slug.'/'.$record->station_slug.'/'.$record->meteor_id;

        $items[] =
            '<div class="'.$images_row_class_wrapper.'">'
                . anchor($details_uri, '<img alt="Meteor!" src="' . METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image . '" class="img-responsive" />')
                . '<div class="caption">'
                    . '<h4 class="meteordateandtime">' . date('d F Y | H:i:s', $record->created_at) . '</h4>'
                    . '<p>'
                        . '<span class="label label-default station-name"><i class="icon-facetime-video">&nbsp;</i> ' . $record->station_name . '</span> '
                        . anchor($details_uri, 'details', array('class' => 'btn btn-default'))
                        .'&nbsp;'
                        . anchor(
                            'ajax/vote/'.$record->meteor_id.'/'.$record->vote_id
                            , '<i class="fa fa-thumbs-o-up"></i> '.$record->votes_cnt
                            , array(
                                'class' => 'btn btn-danger votes vote_'.$record->meteor_id.'_'.$record->vote_id
                                ,'data-toggle' => 'tooltip'
                                ,'title' => 'Like this meteor'
                            )
                        )
                    . '</p>'
                . '</div>'
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