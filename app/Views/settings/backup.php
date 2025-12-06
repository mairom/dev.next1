 <md-content class="md-padding bg-white" style="">
  <div class="col-md-12">
    <div class="form-group clearfix" style="border-bottom: 1px solid gray;">
      <h4 class="pull-left" ><strong><?php echo lang2('backuprestore'); ?></strong></h4>
      <?php if (check_privilege('settings', 'create')) { ?> 
        <md-button ng-click="BackupDatabase()" class="md-raised md-primary pull-right successButton">
          <md-icon>
            <i class="ion-archive"></i>
          </md-icon>
          <?php echo lang2('backupdatabase');?>
        </md-button>
      <?php } if (check_privilege('settings', 'edit')) { ?> 
        <md-button ng-click="RestoreDatabase()" class="md-raised md-primary pull-right successButton">
          <md-icon>
            <i class="ion-android-upload"></i>
          </md-icon>
          <?php echo lang2('restoredatabase');?>
        </md-button>
      <?php } ?>
    </div>
    <md-content  class="bg-white" ng-cloak> 
      <md-table-container ng-show="db_backup.length > 0">
        <table md-table  md-progress="promise">
          <thead md-head md-order="db_list.order">
            <tr md-row>
              <th md-column md-order-by="filename"><span><?php echo lang2('filename'); ?></span></th>
              <th md-column md-order-by="version"><span><?php echo lang2('version'); ?></span></th>
              <th md-column md-order-by="created"><span><?php echo lang2('date'); ?></span></th>
              <th md-column md-order-by="total"><span><?php echo lang2('actions'); ?></span></th>
            </tr>
          </thead>
          <tbody md-body>
            <tr class="select_row" md-row ng-repeat="backup in db_backup | orderBy: db_list.order | limitTo: db_list.limit : (db_list.page -1) * db_list.limit " class="cursor" >
              <td md-cell>
                <strong>
                  <span ng-bind="backup.filename"></span>
                </strong>
              </td>
              <td md-cell>
                <strong><span ng-bind="backup.version"></span></strong>
              </td>
              <td md-cell>
                <strong><span ng-bind="backup.created | date:'dd MMMM YYYY'"></span></strong>
              </td>
              <td md-cell>
                <?php if (check_privilege('settings', 'edit')) { ?> 
                  <md-button ng-click="RestoreBackup(backup.id)" class="md-icon-button md-secondary" aria-label="New">
                    <md-tooltip md-direction="top"><?php echo lang2('restorethisfile');?></md-tooltip>
                    <md-icon><i class="ion-android-upload text-success"></i></md-icon>
                  </md-button>
                <?php } if (check_privilege('settings', 'delete')) { ?> 
                  <md-button ng-click="RemoveBackup(backup.id)" class="md-icon-button md-secondary" aria-label="New">
                    <md-tooltip md-direction="bottom"><?php echo lang2('deletebackup');?></md-tooltip>
                    <md-icon><i class="ion-trash-b text-muted"></i></md-icon>
                  </md-button>
                <?php } ?>
                <a href="<?php echo base_url('settings/download_backup/{{backup.filename}}.zip')?>" class="md-icon-button md-secondary"> 
                  <md-tooltip md-direction="bottom"><?php echo lang2('download');?></md-tooltip>
                  <md-icon><i class="ion-archive text-muted"></i></md-icon>
                </a>
              </td>
            </tr>
          </tbody>
        </table>
      </md-table-container>
      <md-table-pagination ng-show="db_backup.length > 0" md-limit="db_list.limit" md-limit-options="limitOptions" md-page="db_list.page" md-total="{{db_backup.length}}" ></md-table-pagination>
      <md-content ng-show="!db_backup.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>	
    </md-content>
  </div>
</md-content>