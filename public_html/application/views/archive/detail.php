<div class="row">
    <div class="col-md-12">
        <div class="pull-left">
            <h2>UKMON meteor archive</h2>
            <p>All meteors recorded by our UK stations</p>
        </div>
    </div>
</div>
<hr>
<div class="row">

    <div class="col-md-8">
        <h3>Meteor: <?=date('d F Y | H:i:s', $record->created_at)?></h3>
        <div class="imageBorder">
        	<img class="img-responsive" src="<?=ARCHIVES_IMG_URI . $record->image_folder . $record->image?>" alt="<?=date('d F Y | H:i:s', $record->created_at)?> | UK Meteor Observation Network" />
        </div>
        
        <!-- social share box  -->
        <div class="social-box">
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
        </div>

    </div>
    <div class="col-md-4">
        <section class="meteorDetails">
            <h4>Station: <?=$record->station_name?></h4>
            <h4>Meteor ID: <?=str_replace('P.jpg', '', $record->image)?></h4>
            <h4>Mag: <?=$record->mag?></h4>
            <h4>Camera name: <?=$record->cam?></h4>
            <h4>Lens name: <?=$record->lens?></h4>
            <h4>Number of reference stars: <?=$record->rstar?></h4>
            <h4>Duration: <?=$record->sec?>sec</h4>
            <h4>Angular velocity at ram: <?=$record->av?>dcm</h4>
            <h4>Calculated velocity: <?=$record->vo?>km/s</h4>
            <h4>Initial height: <?=$record->h1?>km</h4>
            <h4>Terminal height: <?=$record->h2?>km</h4>
            <h4>Path length: <?=$record->len?>km</h4>
            <h4>Shower name: <?=$record->event_name?></h4>
        </section>
        <hr>
    </div>

</div>
