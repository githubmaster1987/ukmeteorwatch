<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?=$template['title'] ?></title>

		<base href="<?=base_url();?>">

		<!-- Facebook image load -->
		<meta property="og:image" content="<?=METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image?>" />
		<!-- Twitter card details -->
		<meta name="twitter:card" content="summary_large_image" />
		<meta name="twitter:site" content="@UKMeteorWatch" />
		<meta name="twitter:creator" content="@UKMeteorWatch" />
		<meta name="twitter:title" content="<?=$template['title'] ?>" />
		<meta name="twitter:description" content="<?php echo ($record->station_name) ? 'Recorded by '  . $record->station_name . ' station' : 'Bringing meteors across united kingdom to a single website' ?>" />
		<meta name="twitter:image" content="<?=($record->event_id) ? 'http://www.ukmeteorwatch.co.uk/' . METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image : 'http://www.ukmeteorwatch.co.uk/' . METEORS_IMG_URI . 'default.jpg'?>" />

		<?= link_tag('css/bootstrap.min.css') ?>
		<?= link_tag('css/bootstrap-theme.min.css') ?>
		<?= link_tag('css/custom.css') ?>
		<?= link_tag('css/smoothzoom.css') ?>

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

		<?=
		script_tag(array(
//			'js/jquery-1.9.1.min.js'
//			,'js/jquery-migrate-1.2.1.min.js'
			'js/bootstrap.min.js'
//			,'js/jquery.taconite.js'
//			,'js/image-preview.js'
			,'js/easing.js'
			,'js/smoothzoom.min.js'
			,'js/meteor-search.js'
		))
		?>

		<script type="text/javascript">
            $(window).load( function() {
                $('img').smoothZoom({
                    // Options go here
                    zoominSpeed: 100,
                    zoomoutSpeed: 100,
                    resizeDelay: 100,
                });
            });
        </script>


        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
    	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />

        <!-- font awesome -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

		<?=$template['metadata'] ?>
	</head>
	<body>
	<script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-729933-103']);
    _gaq.push(['_trackPageview']);
    (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;

    ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';

    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4e9c82bb06c42d7d" async="async"></script>


    <div <?=($this->router->class == 'meteors' || ($this->router->class == 'pages' && $this->router->method == 'index') ) ? '' : ''?> class="container" id="MeteorList">

          <div class="masthead">
            <ul class="nav nav-pills pull-right">
                  <li class="<?=(($this->router->class == 'pages' && $this->router->method == 'index') ? 'active' : '')?>"><a href="<?= site_url('/') ?>">Home</a></li>
                  <li class="<?=(($this->router->class == 'meteors') ? 'active' : '')?>"><a href="<?= site_url('meteors') ?>">Live!</a></li>
                  <li class="<?=(($this->router->class == 'archive' && !($this->router->method == 'stats' || $this->router->method == 'counts')) ? 'active' : '')?>"><a href="<?= site_url('archive') ?>">Archive</a></li>
                  <li class="<?=(($this->router->class == 'archive' && $this->router->method == 'stats') ? 'active' : '')?>"><a href="<?= site_url('archive/stats') ?>">Showers</a></li>
                  <li class="<?=(($this->router->class == 'pages' && $this->router->method == 'network') ? 'active' : '')?>"><a href="<?= site_url('network') ?>">Network</a></li>
                  <li class="<?=(($this->router->class == 'archive' && $this->router->method == 'counts') ? 'active' : '')?>"><a href="<?= site_url('archive/counts') ?>">Counts</a></li>
            </ul>
            <a href="<?= site_url('/') ?>"><img src="img/logo.gif" alt="UK Meteor Observation Network" class="img-responsive" /></a>
          </div>
        <hr>
            <div class="row">
                <form action="archive/search_name" id="search-meteor-by-name" class="form-inline pull-left col-md-6" method="post">
                    <input name="meteor_name" type="text" class="input-sm form-control" required="required" placeholder="Meteor name"/>
                    <input type="submit" class="btn btn-sm btn-danger" value="Search" />
                </form>
                <div class="SocialMedia pull-right col-md-6">
                	<ul>
                    	<li><a target="_blank" href="https://twitter.com/UKMeteorNetwork"><i class="fa fa-twitter-square"></i> UKMON on Twitter</a></li>
                    	<li><a target="_blank" href="https://www.facebook.com/UkMeteorNetwork"><i class="fa fa-facebook-square"></i> UKMON on Facebook</a></li>
                    	<li><a target="_blank" href="https://plus.google.com/u/1/101554158382717003629/posts"><i class="fa fa-google-plus-square"></i> UKMON on Google+</a></li>
                    </ul>
                </div>
            </div>
        <hr>
        <?= $template['body'] ?>
        <hr>

        <div class="copyright">
            <a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">UKMON</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://www.ukmeteornetwork.co.uk/" property="cc:attributionName" rel="cc:attributionURL">UK Meteor Network</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>.<br />Based on a work at <a xmlns:dct="http://purl.org/dc/terms/" href="http://www.ukmeteornetwork.co.uk/" rel="dct:source">http://www.ukmeteornetwork.co.uk/</a>.
        </div>

        <hr>

        <div class="footer">
            <p class="pull-right">Powered by <a href="http://www.empire-elements.co.uk" class="eeicon">Website Design Agency</a></p>
            <p><?=date('Y') ?> &copy; Meteor Watch Live script v2.0</p>
            <p>Contact: <a href="mailto:ukmeteornetwork@gmail.com">ukmeteornetwork@gmail.com</a></p>
        </div>

    </div>

  </body>
</html>
