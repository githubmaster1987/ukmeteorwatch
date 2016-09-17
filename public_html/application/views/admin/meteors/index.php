<div class="row">
    <div class="col-md-12">

        <div class="row">
    		<div class="col-md-12">
                <div class="pull-left">
                    <h3>Live meteor stream administration</h3>
                </div>
                <div class="pull-right">
    
                    <div class="btn-group">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><i class="icon-facetime-video icon-black"></i>
                            Station
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="<?=(empty($conditions['station_slug']) ? 'active' : '') ?>"><?=anchor(ADMIN_PREFIX.$this->router->class . '/' . $this->router->method, 'Any')?></li>
                            <? foreach ($stations as $station) :
    
                                $station_uri = $this->uri_prefix .'station/'. $station->station_slug.(!empty($conditions['status']) ? '/status/'.$conditions['status'] : '' );
    
                                $is_station_selected = (!empty($conditions['station_slug']) && $conditions['station_slug'] == $station->station_slug) ? 'active' : '';
    
                                ?>
                                <li class="<?=$is_station_selected?>">
                                    <?= anchor($station_uri, $station->station_name, array('tabindex' => '-1'))?>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    </div>
    
                    <div class="btn-group">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            Status
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?
                                $prefix = ADMIN_PREFIX.$this->router->class . '/' . $this->router->method.(!empty($conditions['station_slug']) ? '/station/'.$conditions['station_slug'] : '')
                            ?>
                            <li class="<?=(empty($conditions['status']) ? 'active' : '') ?>"><?=anchor($prefix, 'Any')?></li>
                            <li class="<?=(!empty($conditions['status']) && $conditions['status'] == 'pending') ? 'active' : ''?>"><?=anchor($prefix.'/status/pending', 'Pending')?></li>
                            <li class="<?=(!empty($conditions['status']) && $conditions['status'] == 'approved') ? 'active' : ''?>"><?=anchor($prefix.'/status/approved', 'Approved')?></li>
                        </ul>
                    </div>
    
                    <?=anchor(ADMIN_PREFIX.$this->router->class . '/delete_pending/', 'Delete ALL Pending', array('class' => 'btn btn-danger confirm'))?>
                </div>
            </div>
        </div>

        <? if($records && count($records)) :?>
        
        <div class="row">
            
                <? foreach ($records as $record):
                     $details_uri = 'meteors/detail/'.$record->event_slug.'/'.$record->station_slug.'/'.$record->meteor_id;
                    ?>
                    
                <div class="col-md-3">
                
                    <h4 <?=$record->status == 'approved' ? ' class="bg-success"' : ''?>>#<?=$record->meteor_id?> | <?=date('d F Y | H:i:s', $record->created_at)?></h4>
                	<p><a href="<?=METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image?>">
                            <img src="<?=METEORS_IMG_URI . $record->event_id . '/' . $record->station_id . '/' . $record->image?>" alt="" class="img-responsive" rel="zoom">
                        </a></p>

                    <p><span class="label label-default station-name"><i class="icon-facetime-video">&nbsp;</i> <?=$record->station_name?></span>
                    
					<?=anchor($details_uri, 'details', array('class' => 'btn btn-default', 'target' => '_blank'))?></p>
                    
					<p><?=ucfirst($record->status)?>
                    
                    
                        <? if($record->status == 'pending'): ?>
                            <?=anchor(ADMIN_PREFIX.$this->router->class . '/action/'.$record->meteor_id.'/approve', 'Approve', array('class' => 'btn btn-success'))?>
                        <? elseif($record->status == 'approved'): ?>
                            <?=anchor(ADMIN_PREFIX.$this->router->class . '/action/'.$record->meteor_id.'/pending', 'Make pending', array('class' => 'btn btn-default'))?>
                        <? endif; ?>
                        &nbsp;
                        <?=anchor(ADMIN_PREFIX.$this->router->class . '/action/'.$record->meteor_id.'/delete', 'Delete', array('class' => 'btn btn-danger'))?>
                    </p>  
                </div>
                <? endforeach; ?>
            
            </div><hr>
        
        <? endif; ?>
    </div>
</div>

<? echo $pagination ?>

<script type="text/javascript">
	$(document).ready(
      function()
		{
			$('a.confirm').click(
				function(){
					if(confirm('Are you sure?')) {
						return true;
					}
					return false;
				}
			);

      }
   );
</script>

