<div class="row">
    <div class="col-md-12">
        <div class="pull-left">
            <h2>Live Camera Feeds</h2>
            <p>Meteor images from the UKMON network as they happen. <!--<span class="label label-success"><i class="icon-signal"></i> 8 cameras online</span>--></p>
        </div>
    </div>
</div>


<div class="tabbable"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs">
        <li><a href="<?= site_url('meteors') ?>">Latest meteors</a></li>
        <li><a href="<?= site_url('meteors/favourites') ?>" >Favourites</a></li>
        <li class="active"><a href="<?= site_url('meteors/combined') ?>">Combined view</a></li>
    </ul>
    <div class="tab-content">

        <div class="tab-pane active" id="tab3" style="min-height: 300px;">

            <div class="row">

                <div class="col-md-6">
                    <p>Left column camera:</p>

                    <div class="btn-group">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-facetime-video icon-black"></i>
                            Station
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <? foreach ($stations as $station) :

                                $station_uri = $this->uri_prefix .'left/'. $station->station_slug. (!empty($conds['right']) ? '/right/'.$conds['right'] : '');

                                $is_station_selected = (!empty($conds['left']) && $conds['left'] == $station->station_slug) ? 'active' : '';

                                ?>
                                <li class="<?=$is_station_selected?>">
                                    <?= anchor($station_uri, $station->station_name, array('tabindex' => '-1'))?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </div>

                </div><!-- // span6-->

                <div class="col-md-6">
                    <p>Right column camera:</p>

                    <div class="btn-group">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-facetime-video icon-black"></i>
                            Station
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <? foreach ($stations as $station) :

                                $station_uri =  $this->uri_prefix.(!empty($conds['left']) ? 'left/'.$conds['left'].'/' : '').'right/'. $station->station_slug;

                                $is_station_selected = (!empty($conds['right']) && $conds['right'] == $station->station_slug) ? 'active' : '';

                                ?>
                                <li class="<?=$is_station_selected?>">
                                    <?= anchor($station_uri, $station->station_name, array('tabindex' => '-1'))?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </div>

                </div><!-- // span6-->

            </div><!-- // row-->


            <? if($left_images || $right_images): ?>
            <hr>
            <div class="row liveimages_container">
                <div class="col-md-6">
                    <?=$left_images?>
                    
                    <input type="hidden" id="latest_left" value="<?php echo @$latest_left; ?>" />
                    <input type="hidden" id="station_left" value="<?php echo @$station_left; ?>" />
                </div>
                <div class="col-md-6">
                    <?=$right_images?>
                    
                    <input type="hidden" id="latest_right" value="<?php echo @$latest_right; ?>" />
                    <input type="hidden" id="station_right" value="<?php echo @$station_right; ?>" />
                </div>
            </div>
            <? endif; ?>

        </div>

    </div>
</div>
<script>
    jQuery(function($) {

        $("a").tooltip();

        $('a.votes').on('click', function(e) {
            $.post($(this).attr('href'));
            e.preventDefault();
        });
        
     });
     
     // live images code
     $(function(){
     	
     	var $delay = 30000;
     	
     	if ($('#latest_left').length || $('#latest_right').length)
     	{
     		function poll_images()
     		{
     			var $latest_left = $('#latest_left'),
		     		$station_left = $('#station_left'),
		     		$latest_right = $('#latest_right'),
		     		$station_right = $('#station_right');
		     		
     			$.ajax({
     				cache: false,
     				type: 'get',
     				url: '<?php echo site_url('live_images/has_update/combined'); ?>',
     				data: 'latest_left='+$latest_left.val()+'&station_left='+$station_left.val()+'&latest_right='+$latest_right.val()+'&station_right='+$station_right.val(),
     				success: function (data)
     				{
     					if (data == 0)
     					{
     						console.log('No new images');
     						setTimeout(function(){
     							poll_images();
     						}, $delay);
     					}
     					else
     					{
     						console.log('Updating images');
     						update_images();
     						setTimeout(function(){
     							poll_images();
     						}, $delay);
     					}
     				}
     			});
     		}
     		
     		poll_images();
     		
     		function update_images()
     		{
     			$.ajax({
     				cache: false,
     				type: 'get',
     				url: '<?php echo $_SERVER['REQUEST_URI']; ?>',
     				success: function(data)
     				{
     					var $content = $(data).find('.liveimages_container');
     					$('.liveimages_container').fadeOut('slow', function(){
     						$('.liveimages_container').replaceWith($content).fadeIn('slow');
     						setTimeout(function(){
     							poll_images();
     						}, $delay);
     					});
     				}
     			});
     		}
     	}
     	
     });
</script>