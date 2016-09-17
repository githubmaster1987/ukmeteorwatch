<div class="row">
   <div class="col-md-12">
      <h2>Meteor showers recorded</h2></div>
</div>

<hr>

<div class="row">
   <div class="col-md-12">
      <table class="table table-bordered table-hover">
         <thead>
            <tr>
               <th>Meteor Shower name</th>
               <th>J5 Catalogue</th>
               <th>Total count</th>
               <th>Stations recorded</th>
            </tr>
         </thead>
         <tbody>
            <?
            foreach ($records as $record):
               $uri = 'archive/index/event/' . $record->event_slug . '/order/date:desc';
               ?>
               <tr>
                  <td><?= anchor($uri, $record->event_name) ?></td>
                  <td><?= $record->event_code ?></td>
                  <td><?= $record->archives_count ?></td>
                  <td><?= $record->stations_count ?> stations</td>
               </tr>
            <? endforeach; ?>
         </tbody>
      </table>
   </div>
</div>
