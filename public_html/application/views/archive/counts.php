<div class="row">
   <div class="col-md-12">
      <h2>Meteor counts</h2>
	</div>
</div>

<hr>

<div class="row">
   <div class="col-md-12">

		<? foreach ($records as $year => $st) :

			$months_total = array();
			?>

			<h3>Counts for <?= $year ?></h3>

	      <table class="table table-bordered table-hover">
	         <thead>
	            <tr>
	               <th>Meteor counts</th>
	               <th colspan="13">Month</th>
	            </tr>
	            <tr>
	               <th>Station</th>
	               <th>01</th>
	               <th>02</th>
	               <th>03</th>
	               <th>04</th>
	               <th>05</th>
	               <th>06</th>
	               <th>07</th>
	               <th>08</th>
	               <th>09</th>
	               <th>10</th>
	               <th>11</th>
	               <th>12</th>
	               <th>Grand Total</th>
	            </tr>
	         </thead>
	         <tbody>
					<? foreach ($stations as $station_id => $station_name):

						$station_total = 0;

						?>
						<tr>
							<td><?= $station_name ?></td>
							<? foreach (range(1, 12) as $m):

								$station_month = (!empty($st[$station_name][$m]) ? intval($st[$station_name][$m]) : 0);

								$months_total[$m] = empty($months_total[$m]) ? $station_month : $station_month + $months_total[$m];

								$station_total += $station_month;
								?>
								<td><?= !empty($st[$station_name][$m]) ? $st[$station_name][$m] : '' ?></td>
							<? endforeach; ?>
							<td><?=$station_total?></td>
						</tr>
					<? endforeach; ?>
	         </tbody>
				<tfoot>
	            <tr>
	               <th>Grand Total</th>
						<?
							$grand_total = 0;
							foreach (range(1, 12) as $m):
								$grand_total += $months_total[$m];
							?>
							<th><?=$months_total[$m] ?></th>
						<? endforeach; ?>
	               <th><?=$grand_total?></th>
	            </tr>
				</tfoot>
	      </table>

		<? endforeach; ?>
   </div>
</div>
