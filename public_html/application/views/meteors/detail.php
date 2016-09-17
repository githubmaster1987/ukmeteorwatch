<div class="row">
    <div class="col-md-2">
        <div class="pull-left">
            <div class="back-button">
            <a class="btn btn-large btn-danger" href="<?= site_url('meteors') ?>">Back to all images</a>
            </div>
        </div>
    </div>
    <div class="col-md-10">
        <div class="pull-left">
            <h2>Live Camera Feeds</h2>
            <p>Meteor images from the UKMON network as they happen. <!--<span class="label label-success"><i class="icon-signal"></i> 8 cameras online</span>--></p>
        </div>
    </div>
</div>

<hr>
<div class="row">

    <div class="col-md-8">
        <h3><?=date('d F Y | H:i:s', $record->created_at)?> UTC</h3>
        <p>
            (United Kingdom is one hour ahead of UTC / GMT during summer)
        </p>
        <div class="imageBorder">
        	<img class="img-responsive" src="<?=METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image?>" alt="<?=date('d F Y | H:i:s', $record->created_at)?> | UK Meteor Observation Network" />
            <h5>Station name: <?=$record->station_name?></h5>
        </div>
    </div>
    <div class="col-md-4">
        <h3>Live radio feed</h3>
        <section class="NLOfeed"> <a href="http://www.merriott-astro.co.uk/meteor/Spectrum/uploads/latest3d.jpg" target="_blank" title="Live radio feed S.P.A.M. network"><img src="http://www.merriott-astro.co.uk/meteor/Spectrum/uploads/latest3d.jpg" alt="Latest radio feed from S.P.A.M at Norman Lockyer Observatory" / class="img-responsive"></a>
        </section>
        <h5>Provided by S.P.A.M network</h5>
        <p>Visit : <a href="http://www.merriott-astro.co.uk/About.htm" target="_blank">www.merriott-astro.co.uk</a></p>
    </div>

</div>
<hr />
<div class="row">
	<div class="col-md-8" style="text-align: center;">
		<?php if (count($previous_record) > 0) : ?>
			<?php $pr = $previous_record[0]; ?>
			<a href="<?php echo site_url('meteors/detail/'.$pr->event_slug.'/'.$pr->station_slug.'/'.$pr->meteor_id); ?>" class="btn"><i class="icon-chevron-left"></i> Prev</a>
		<?php endif; ?>
			<a href="<?php echo site_url('ajax/vote/'.$record->meteor_id.'/'.$record->vote_id); ?>" class="btn btn-danger votes"><i class="fa fa-thumbs-o-up" data-toggle="tooltip" title="Like this meteor"></i> <?php echo $record->votes_cnt; ?></a>
		<?php if (count($next_record) > 0) : ?>
			<?php $nr = $next_record[0]; ?>
			<a href="<?php echo site_url('meteors/detail/'.$nr->event_slug.'/'.$nr->station_slug.'/'.$nr->meteor_id); ?>" class="btn">Next <i class="icon-chevron-right"></i></a>
		<?php endif; ?>
	</div>
</div>
<hr />
<div class="row">

    <div class="col-md-8">

        <!-- social share box  -->
        <div class="social-box">
            <!-- Go to www.addthis.com/dashboard to customize your tools -->
            <div class="addthis_sharing_toolbox"></div>
        </div>

				<!--<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed sed sem   eget tortor dictum ultricies vel ac dui.</p>-->

        <div id="disqus_thread"></div>
        <script type="text/javascript">
            /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
            var disqus_shortname = 'ukmeteorwatchlive'; // required: replace example with your forum shortname

            /* * * DON'T EDIT BELOW THIS LINE * * */
            (function() {
                var dsq = document.createElement('script');
                dsq.type = 'text/javascript';
                dsq.async = true;
                dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
            })();
        </script>
        <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
        <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>

    </div>

    <div class="col-md-4">
        <a class="twitter-timeline" href="https://twitter.com/UKMeteorNetwork" data-widget-id="337595541049446400">Tweets by @UKMeteorNetwork</a>
        <script>!function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + "://platform.twitter.com/widgets.js";
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, "script", "twitter-wjs");</script>

    </div>

</div>
<script>
    jQuery(function($) {

        $("a").tooltip();

        $('a.votes').on('click', function(e) {
        	var $this = $(this);
            e.preventDefault();
            $.ajax({
            	cache: false,
            	type:'post',
            	url:$this.attr('href'),
            	success: function(data)
            	{
            		if (data.match(/^\d+$/))
            		{
            			$this.html('<i class="fa fa-thumbs-o-up">&nbsp;</i> '+data);
            		}
            		else
            		{
            			alert(data);
            		}
            	}
            });
        });

    });
</script>
