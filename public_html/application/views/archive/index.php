<?= script_tag(array('js/bootstrap3-typeahead.min.js', 'js/bootstrap-datepicker.js')); ?>
<?= link_tag('css/datepicker3.css'); ?>

<div class="row">
<div class="col-md-12">

    <div class="pull-left">
        <form action="" method="post" class="form-horizontal">
            <div class="btn-group">
                <select name="station" class="input-sm form-control">
                    <option value="">Station:</option>
                    <?php foreach ($stations as $station) :
                        $selected = ($station->station_slug == $filter['station'] ? 'selected' : '');
                    ?>
                    <option value="<?=$station->station_slug?>" <?=$selected?>><?=$station->station_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="btn-group">
                <!-- datepicker start / end -->
                <div class="input-daterange input-group" id="datepicker">
                    <input type="text" class="input-sm form-control" name="start" value="<?=@$filter['start']?>" placeholder="Date from" />
                    <span class="input-group-addon">to</span>
                    <input type="text" class="input-sm form-control" name="end" value="<?=@$filter['end']?>" placeholder="Date to" />
                </div>
            </div>
    
            <div class="btn-group">
                <input type="text" name="event" class="input-sm form-control typeahead" placeholder="Search for shower" autocomplete="off" value="<?=trim(@$filter['event'])?>" id="shower-filter" />
                <input type="hidden" name="event_id" class="" value="<?=@$filter['event_id']?>" id="event_id_filter" />
            </div>
            
            <div class="btn-group">
                <select name="order" class="input-sm form-control">
                    <option value="">Sort by:</option>
                    <?php  
                        $options = array(
                            'created_at:desc' => 'Date desc'
                            ,'created_at:asc' => 'Date asc'
                            ,'mag:desc' => 'Mag desc'
                            ,'mag:asc' => 'Mag asc'
                            ,'sec:desc' => 'Duration desc'
                            ,'sec:asc' => 'Duration asc'
                        );
                        foreach ($options as $id=>$option) :
                            $selected = ($id == $filter['order'] ? 'selected' : ''); 
                        ?>
                        <option value="<?=$id?>" <?=$selected?>><?=$option ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <input type="submit" name="submit" value="Filter" class="btn btn-sm btn-danger" />
            <input type="submit" name="reset" value="Clear" class="btn btn-sm btn-default" />
        </form>
    </div>
    <div class="pull-right">
        <h5 class="text-info"><?= number_format($records_count) ?> meteors from <?=$stations_count?> stations</h5>
    </div>
</div>
</div>
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
                . '<p>Mag="' . $record->mag . '"</p> '
                . '<p>Duration="' . $record->sec . '"</p> '
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

<script>
    //dates range
    $('.input-daterange').datepicker({
        autoclose: true,
        todayHighlight: true
    });
    //Live search for shower 
    var $input = $('.typeahead');
    $input.typeahead({source: <?=json_encode($event_list)?>, 
            autoSelect: true}); 
    $input.change(function() {
        var current = $input.typeahead("getActive");
        if (current) {
            // Some item from your model is active!
            if (current.name == $input.val()) {
                // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
                //redirect to url
                //window.location = "<?=$this->uri_prefix?>"+current.url;
                //setup id
                $('#event_id_filter').val(current.id);
            } else {
                // This means it is only a partial match, you can either add a new item 
                // or take the active if you don't want new items
            }
        } else {
            // Nothing is active so it is a new value (or maybe empty value)
        }
    });
</script>