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
        <li class="active"><a href="<?= site_url('meteors') ?>">Latest meteors</a></li>
        <li><a href="<?= site_url('meteors/favourites') ?>">Favourites</a></li>
        <li><a href="<?= site_url('meteors/combined') ?>">Combined view</a></li>
    </ul>

    <div class="tab-content">

        <div class="tab-pane active liveimage_container" id="tab1">
            <?=$images?>
            
            <input type="hidden" id="latest_date" value="<?php echo $latest_date; ?>" />
        </div>

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

<?php if ( ! $this->uri->segment(2)) : ?>
    // live images update
    $(function(){
			
		if ($('.liveimage_container').length > 0 && $('#latest_date').length > 0)
		{
			poll_images();
		}

		function poll_images()
		{
			var $latest_date = $('#latest_date').val();

			var $delay = 30000;

			$.ajax({
				cache: false,
				type: 'get',
				url: '<?php echo site_url('live_images/has_update/' . $this->uri->segment(2));?>',
				data: 'latest_date='+$latest_date,
				success: function(data)
				{
					if (data == 0)
					{
						console.log('No new images to show');
						setTimeout(function(){
							poll_images();
						}, $delay);
					}
					else
					{
						console.log('Updating live images');
						insert_images();
						setTimeout(function(){
							poll_images();
						}, $delay);
					}
				}
			});
		}
		
		function insert_images($data)
		{
			$.ajax({
				cache: false,
				type: 'get',
				url: '<?php echo site_url('meteors'); ?>',
				success: function(data)
				{
					var $content = $(data).find('.liveimage_container');
					$('.liveimage_container').fadeOut('slow', function(){
						$('.liveimage_container').replaceWith($content);
						$('.liveimage_container').fadeIn('slow');
					});
				}
			});
		}
		
	});
<?php endif; ?>
</script>