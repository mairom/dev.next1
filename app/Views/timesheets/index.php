<?php include_once( APPPATH . 'Views/inc/ciuis_data_table_header.php' ); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Timesheets_Controller">

  <style type="text/css">

    rect.highcharts-background {

      fill: #f3f3f3;

    }

  </style>

  <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9"> 

    <md-toolbar class="toolbar-white">

      <div class="md-toolbar-tools">

        <h2 flex md-truncate class="text-bold"><?php echo lang2('timesheets'); ?><br>

          <small flex md-truncate><?php echo lang2('timesheets_description'); ?></small>

        </h2>

        <div class="ciuis-external-search-in-table">

          <input ng-model="search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword')?>">

          <md-button class="md-icon-button" aria-label="Search" ng-cloak>

            <md-icon><i class="ion-search text-muted"></i></md-icon>

          </md-button>

        </div>

        <md-button ng-click="refreshTimeLogs()" class="md-icon-button" aria-label="Filter" ng-cloak>

          <md-progress-circular ng-show="refreshing == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          <md-tooltip ng-hide="refreshing == true" md-direction="top"><?php echo lang2('refresh').' '.lang2('timesheets') ?></md-tooltip>

          <md-icon ng-hide="refreshing == true"><i class="ion-ios-refresh text-muted"></i></md-icon>

        </md-button>

        <?php if (check_privilege('timesheets', 'create')) { ?> 

          <md-button ng-click="LogTime()" class="md-icon-button" aria-label="New" ng-cloak>

            <md-tooltip md-direction="bottom"><?php echo lang2('log').' '.lang2('time') ?></md-tooltip>

            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>

          </md-button>

        <?php } ?>

      </div>

    </md-toolbar>

    <div ng-show="loadingTimesheets" layout-align="center center" class="text-center" id="circular_loader">

        <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
            <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
            

        <p style="font-size: 15px;margin-bottom: 5%;">

         <span>

            <?php echo lang2('please_wait') ?> <br>

           <small><strong><?php echo lang2('loading'). ' '. lang2('timesheets').'...' ?></strong></small>

         </span>

       </p>

     </div>

    <md-content ng-show="!loadingTimesheets" class="md-pt-0 bg-white" ng-cloak>

      <md-table-container ng-show="timesheets.length > 0">

        <table md-table  md-progress="promise" ng-cloak>

          <thead md-head md-order="timesheet_list.order">

            <tr md-row>

              <th md-column><span>#</span></th>

              <th md-column md-order-by="staff"><span><?php echo lang2('staff'); ?></span></th>

              <th md-column md-order-by="name"><span><?php echo lang2('task'); ?></span></th>

              <th md-column md-order-by="start_time"><span><?php echo lang2('start_time'); ?></span></th>

              <th md-column md-order-by="end_time"><span><?php echo lang2('end_time'); ?></span></th>

              <th md-column><span><?php echo lang2('timeCaptured'); ?></span></th>

              <th md-column><span><?php echo lang2('actions'); ?></span></th>

            </tr>

          </thead>

          <tbody md-body>

            <tr class="select_row" md-row ng-repeat="timesheet in timesheets | orderBy: timesheet_list.order | limitTo: timesheet_list.limit : (timesheet_list.page -1) * timesheet_list.limit | filter: search | filter: FilteredData">

              <td md-cell>

                <div data-toggle="tooltip" data-placement="bottom" data-container="body" title="" data-original-title="Assigned: {{lead.assigned}}" class="assigned-staff-for-this-lead user-avatar">

                  <a ng-href="<?php echo base_url('staff/staffmember/')?>{{timesheet.staff_id}}"> 

                    <img src="<?php echo base_url('uploads/images/{{timesheet.avatar}}')?>" alt="{{timesheet.staff}}">

                  </a> 

                </div>

              </td>

              <td md-cell>

                <strong>{{timesheet.staff}}</strong>

                <br>

                <small ng-bind="timesheet.staff_email"></small> 

              </td>

              <td md-cell>

                <a class="link" ng-href="<?php echo base_url('tasks/task/')?>{{timesheet.task_id}}">

                  <span ng-bind="timesheet.task_number"></span>

                </a><br>

                <small class="text-muted" ng-bind="timesheet.name"></small>

              </td>

              <td md-cell>

                <strong ng-bind="timesheet.start_time"></strong>

              </td>

              <td md-cell>

                <strong ng-bind="timesheet.end_time"></strong>

                <span class="badge ng-binding" style="border-color: #fff;background-color: #26c281;" ng-if="!timesheet.end_time"><?php echo lang2('in_progress') ?></span>

              </td>

              <td md-cell>

                <span ng-bind="timesheet.total_time"></span>

              </td>

              <td md-cell>

                <md-icon ng-click="viewTimesheet(timesheet.id)">

                  <i class="ion-eye text-success"></i>

                  <md-tooltip md-direction="bottom"><?php echo lang2('view') ?></md-tooltip>

                </md-icon> &nbsp;&nbsp;

                <?php if (check_privilege('timesheets', 'edit')) { ?> 

                  <md-icon ng-click="editTimeLog(timesheet.id)">

                    <i class="ion-compose text-muted"></i>

                    <md-tooltip md-direction="bottom"><?php echo lang2('edit') ?></md-tooltip>

                  </md-icon> &nbsp;&nbsp;

                <?php } if (check_privilege('timesheets', 'delete')) { ?> 

                  <md-icon ng-click="deleteTimesheet(timesheet.id)" style="margin-left: 10px;">

                    <i class="ion-trash-b text-muted"></i>

                    <md-tooltip md-direction="bottom"><?php echo lang2('delete') ?></md-tooltip>

                  </md-icon>

                <?php } ?>

              </td>

            </tr>

            <tr class="select_row" md-row>

              <td md-cell>

              </td>

              <td md-cell>

              </td>

              <td md-cell>

              </td>

              <td md-cell>

              </td>

              <td md-cell>

                <span class="text-muted"><?php echo lang2('time_captured') ?>: </span>

              </td>

              <td md-cell>

                <strong ng-bind="total_time"></strong>

              </td>

              <td md-cell>

              </td>

            </tr>

          </tbody>

        </table>

      </md-table-container>

      <md-table-pagination ng-show="timesheets.length > 0" md-limit="timesheet_list.limit" md-limit-options="limitOptions" md-page="timesheet_list.page" md-total="{{timesheets.length}}" ></md-table-pagination>

      <md-content ng-show="!timesheets.length" class="md-padding no-item-data" ng-cloak><?php echo lang2('notdata') ?></md-content>

    </md-content>

  </div>

  <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 md-pl-0 lead-left-bar">

    <ciuis-sidebar></ciuis-sidebar>

  </div>

</div>

<script type="text/ng-template" id="add-timer.html">

    <md-dialog>

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2><strong class="text-success"><?php echo lang2('log').' '.lang2('time') ?></strong></h2>

          <span flex></span>

          <md-button class="md-icon-button" ng-click="close()">

            <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

          </md-button>

        </div>

      </md-toolbar>

      <md-dialog-content style="max-width:800px;max-height:810px; ">

        <md-content class="bg-white">

          <md-list flex>

            <md-list-item>

              <md-input-container class="md-block full-width">

                <label><?php echo lang2('select').' '.lang2('task'); ?></label>

                <md-select required placeholder="<?php echo lang2('select').' '.lang2('task'); ?>" ng-model="logtime.task" style="min-width: 200px;">

                  <md-option ng-value="task.id" ng-repeat="task in timerTasks">{{task.name}}</md-option>

                </md-select><br>

              </md-input-container>

              <br>

            </md-list-item>

            <md-list-item>

              <md-input-container class="md-block full-width">

                <label><?php echo lang2('start_time'); ?></label>

                <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" show-icon="true" ng-model="logtime.start_time" class=" dtp-no-msclear dtp-input md-input">

              </md-input-container>

            </md-list-item>

            <md-list-item>

              <md-input-container  class="md-block full-width">

                <label><strong><?php echo lang2('end_time') ?></strong></label>

                <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="logtime.start_time" show-icon="true" ng-model="logtime.end_time" class=" dtp-no-msclear dtp-input md-input">

              </md-input-container>

            </md-list-item>

            <md-list-item>

              <md-input-container  class="md-block full-width">

                <label><strong><?php echo lang2('description') ?></strong></label>

                <textarea required  ng-model="logtime.description" class="form-control" id="title" placeholder="<?php echo lang2('description'); ?>"></textarea>

              </md-input-container>

            </md-list-item>

            <br><br>

            <md-divider>

            </md-divider>

            <md-button ng-click="CreateLogTime()" class="template-button" ng-disabled="adding == true">

              <span ng-hide="adding == true"><?php echo lang2('add');?></span>

              <md-progress-circular class="white" ng-show="adding == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

            </md-button>

            <md-button ng-click="close()" class="">

              <span><?php echo lang2('cancel');?></span>

            </md-button>

          </md-list>

        </md-content>     

      </md-dialog-content>

    </md-dialog>

  </script>



  <script type="text/ng-template" id="update_timer.html">

    <md-dialog>

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2><strong class="text-success"><?php echo lang2('update').' '.lang2('log').' '.lang2('time') ?></strong></h2>

          <span flex></span>

          <md-button class="md-icon-button" ng-click="close()">

            <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

          </md-button>

        </div>

      </md-toolbar>

      <md-dialog-content style="max-width:800px;max-height:810px; ">

        <md-content class="bg-white">

          <md-list flex>

            <md-list-item>

              <md-input-container class="md-block full-width">

                <label><?php echo lang2('select').' '.lang2('task'); ?></label>

                <md-select required placeholder="<?php echo lang2('select').' '.lang2('task'); ?>" ng-model="updatetimer.task_id" style="min-width: 200px;">

                  <md-option ng-value="task.id" ng-repeat="task in timerTasks">{{task.name}}</md-option>

                </md-select><br>

              </md-input-container>

              <br>

            </md-list-item>

            <md-list-item>

              <md-input-container class="md-block full-width">

                <label><?php echo lang2('start_time'); ?></label>

                <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" show-icon="true" ng-model="updatetimer.start_time" class=" dtp-no-msclear dtp-input md-input">

              </md-input-container>

            </md-list-item>

            <md-list-item>

              <md-input-container  class="md-block full-width">

                <label><strong><?php echo lang2('end_time') ?></strong></label>

                <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="updatetimer.start_time" show-icon="true" ng-model="updatetimer.end_time" class=" dtp-no-msclear dtp-input md-input">

              </md-input-container>

            </md-list-item>

            <md-list-item>

              <md-input-container  class="md-block full-width">

                <label><strong><?php echo lang2('description') ?></strong></label>

                <textarea required  ng-model="updatetimer.note" class="form-control" id="title" placeholder="<?php echo lang2('description'); ?>"></textarea>

              </md-input-container>

            </md-list-item>

            <br><br>

            <md-divider>

            </md-divider>

            <md-button ng-click="UpdateLogTime(updatetimer.id)" class="template-button" ng-disabled="saving == true">

              <span ng-hide="saving == true"><?php echo lang2('save');?></span>

              <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

            </md-button>

            <md-button ng-click="close()" class="">

              <span><?php echo lang2('cancel');?></span>

            </md-button>

          </md-list>

        </md-content>     

      </md-dialog-content>

    </md-dialog>

  </script>



  <script type="text/ng-template" id="view_timesheet.html">

    <md-dialog aria-label="Expense Detail">

      <md-toolbar class="toolbar-white">

        <div class="md-toolbar-tools">

          <h2><strong class="text-success"><?php echo lang2('timesheet') ?></strong></h2>

          <span flex></span>

          <md-button class="md-icon-button" ng-click="close()">

            <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

          </md-button>

        </div>

      </md-toolbar>

      <md-dialog-content style="max-width:800px;max-height:810px; ">

        <md-content class="bg-white">

          <md-list flex>

            <md-list-item>

              <h4 class="text-bold money-area">

                <small class="text-muted"><?php echo lang2('logged_by'); ?></small>: 

                <small><strong ng-bind="loggedtime.staff | date : 'MMM d, y h:mm:ss a'"></strong></small>

              </h4>

            </md-list-item>

            <md-list-item>

              <h4 class="text-bold money-area">

                <small class="text-muted"><?php echo lang2('task'); ?></small>: 

                <a ng-show="loggedtime.task_id" class="ciuis_expense_receipt_number" href="<?php echo base_url('tasks/task/') ?>{{loggedtime.task_id}}">

                    <strong ng-bind="loggedtime.name"></strong>

                  </a>

                <a ng-show="loggedtime.task_id" href="<?php echo base_url('tasks/task/') ?>{{loggedtime.task_id}}"><i class="ion-android-open"></i><md-tooltip md-direction="top"><?php echo lang2('go_to').' '.lang2('task') ?></md-tooltip></a>

              </h4>

            </md-list-item>

            <md-list-item>

              <h4 class="text-bold money-area">

                <small class="text-muted"><?php echo lang2('start_time'); ?></small>: 

                <small><strong ng-bind="loggedtime.start_time | date : 'MMM d, y h:mm:ss a'"></strong></small>

              </h4>

            </md-list-item>

            <md-list-item>

              <h4 class="text-bold money-area">

                <small class="text-muted"><?php echo lang2('end_time'); ?></small>: 

                <small><strong ng-bind="loggedtime.end_time | date : 'MMM d, y h:mm:ss a'"></strong></small>

                <span ng-show="!loggedtime.end_time" class="badge ng-binding" style="border-color: #fff;background-color: #26c281;" ng-if="!timesheet.end_time"><?php echo lang2('in_progress') ?></span>

              </h4>

            </md-list-item>

            <md-list-item>

              <h4 class="text-bold money-area">

                <small class="text-muted"><?php echo lang2('total').' ' .lang2('time'); ?></small>: 

                <small><strong ng-bind="loggedtime.total_time | date : 'MMM d, y h:mm:ss a'"></strong></small>

              </h4>

            </md-list-item>

            <md-list-item>

              <h4 class="text-bold money-area">

                <small class="text-muted"><?php echo lang2('description'); ?></small>: <br>

                <small><strong ng-bind="loggedtime.note"></strong></small>

              </h4>

            </md-list-item>

          </md-list>

        </md-content>     

      </md-dialog-content>

    </md-dialog>

  </script>



<script type="text/javascript">

  var langs = {

    delete_timelog: "<?php echo lang2('delete_timelog') ?>",

    delete_timelog_message: "<?php echo lang2('delete_timelog_message') ?>",

    delete: "<?php echo lang2('delete') ?>",

    cancel: "<?php echo lang2('cancel') ?>",

  };

</script>

<?php include_once( APPPATH . 'Views/inc/other_footer.php' ); ?>


<script src="<?php echo base_url('assets/js/timesheets.js'); ?>"></script>

