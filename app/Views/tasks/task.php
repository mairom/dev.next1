<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Task_Controller">
  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <h2 class="md-pl-10" flex md-truncate ng-bind="task.task_number+' '+task.name"></h2>
        <?php if (check_privilege('tasks', 'edit')) { ?>
          <md-button ng-if="task.billable != false" id="startTaskTimer" ng-show="task.timer == false && task.relation_type === 'project'" ng-hide="task.status_id == 4" ng-click='startTimerforTask()' class="md-icon-button" aria-label="Task" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('starttimer') ?></md-tooltip>
            <md-icon><i class="mdi mdi-time-countdown text-success"></i></md-icon>
          </md-button>
          <md-button ng-if="task.billable != false" id="stopTaskTimer" ng-show="task.timer == true" ng-click='stopTimer()' class="md-icon-button" aria-label="Task" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('stoptimer') ?></md-tooltip>
            <md-icon><i class="mdi mdi-timer-off text-danger"></i></md-icon>
          </md-button>
        <?php } ?>
        <?php if (check_privilege('tasks', 'edit') || check_privilege('tasks', 'delete')) { ?>      
          <md-menu md-position-mode="target-right target" ng-cloak>
            <md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">
              <md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>
            </md-button>
            <md-menu-content width="4">
              <?php if (check_privilege('tasks', 'edit')) { ?>
                <md-menu-item>
                  <md-button ng-click="Update()" ng-bind="lang.update" aria-label="Update"></md-button>
                </md-menu-item>
                <md-menu-item>
                  <md-button ng-click="MarkAsCompleteTask()" ng-bind="lang.markasprojectcomplete" aria-label="Mark as Complete"></md-button>
                </md-menu-item>
                <md-menu-item>
                  <md-button ng-click="MarkAsCancelled()" ng-bind="lang.markascancelled" aria-label="Mark as cancel"></md-button>
                </md-menu-item>
              <?php } if (check_privilege('tasks', 'delete')) { ?>
                <md-menu-item>
                  <md-button ng-click="Delete()" ng-bind="lang.delete" aria-label="Delete"></md-button>
                </md-menu-item>
              <?php } ?>
            </md-menu-content>
          </md-menu>
        <?php } ?>
      </div>
    </md-toolbar>
    <md-content class="bg-white">
      <md-content class="task-detail bg-white" layout-padding>
        <div class="task-description" style="float: left; width: 100%;">
          <h4><strong><?php echo lang2('description') ?></strong></h4>
          <p class="text-muted" ng-bind="task.description"></p>
        </div>
        <div class="clearfix"></div>
      </md-content>
      <md-divider></md-divider>
      <md-content class="ciuis-task-subtask bg-white">
        <div class="todo-checklist-container" ng-cloak>
          <div class="ciuis-sub-task">
            <h2 class="mb0">{{title}}: {{subtasks.length + SubTasksComplete.length}} {{subtasks.length + SubTasksComplete.length === 1 ? 'task' : 'subtasks'}}</h2>
          </div>
          <div class="ciuis-sub-task  ciuis-sub-task--small  ciuis-sub-task--highlight"> <span>{{ SubTasksComplete.length }} <?php echo lang2('of')?> {{ taskLength() }} ({{ taskCompletionTotal(SubTasksComplete.length) }}%) <?php echo lang2('subtask_complete')?>.</span> </div>
          <div class="progress">
            <div style="width: {{ taskCompletionTotal(SubTasksComplete.length) }}%" class="progress-bar progress-bar-success progress-bar-striped active" ng-bind="'Complete '+taskCompletionTotal(SubTasksComplete.length)+'%'"></div>
          </div>
          <ul class="subtask-items">
            <?php if (check_privilege('tasks', 'edit')) { ?>
              <li class="subtask-list-item">
                <form name="addTask" ng-submit="createTask()" novalidate>
                  <input class="input-ui" required="true" type="text" ng-model="newTitle" placeholder="<?php echo lang2('writeAndEnter'); ?>"/>
                  <div class="pull-right">
                    <button ng-hide="true" class="btn" type="submit" ng-disabled="addTask.$invalid"><?php echo lang2('addtask') ?></button>
                  </div>
                </form>
              </li>
            <?php } ?>
            <li class="subtask-list-item" ng-repeat="task in subtasks"> <span ng-bind="task.description"></span>
              <div class="pull-right">
                <?php if (check_privilege('tasks', 'delete')) { ?>
                  <div class="sub-task-button" href ng-click="removeTask($index)"> <span class="ion-trash-b"></span> </div>
                <?php } if (check_privilege('tasks', 'edit')) { ?>
                    <div class="sub-task-button" href ng-click="completeTask($index)"> <span class="ion-checkmark-round"></span> </div>
                <?php } ?>
              </div>
            </li>
            <li class="subtask-list-item" ng-class="{ 'subtask-status subtask-status--done' : task.complete }" ng-repeat="task in SubTasksComplete"> <span ng-bind="task.description"></span>
              <div class="pull-right">
              <?php if (check_privilege('tasks', 'edit')) { ?>
                <div class="sub-task-button" href ng-click="uncompleteTask($index)"> <span class="ion-refresh"></span> </div>
              <?php } ?>
              </div>
            </li>
          </ul>
        </div>
      </md-content>
      <md-content ng-if="task.billable != false" class="time-log-project bg-white" ng-cloak>
        <ul class="timelog-list">
          <li class="timelog-list-item" ng-repeat="timelog in timelogs" ng-class="{ 'timelog-list-item--active' : timelog.status == 0 }">
            <div class="timelog-list-item__clock">
              <div class="timelog-list-item__bar"></div>
              <i class="ion-android-time"></i> <span ng-show="timelog.status != '0'"><strong ng-bind="timelog.timed | time:'mm':'hhh mmm':false"></strong></span> <span ng-show="timelog.status != '1'"><strong>N/A</strong></span> </div>
            <div class="timelog-list-item__info">
              <h3 class="timelog-list-item__description"><strong ng-bind="timelog.staff"></strong></h3>
              <span class="timelog-list-item__details"><strong class="text-uppercase text-black"><?php echo lang2('start') ?>: <span class="text-muted" ng-bind="timelog.start"></span></strong> | <strong class="text-uppercase text-black"><?php echo lang2('end') ?>: <span class="text-muted" ng-bind="timelog.end"></strong></span></span> <span ng-show="timelog.status != '0'" class="timelog-list-item__chargeable-status"><strong ng-bind-html="timelog.amount | currencyFormat:cur_code:null:true:cur_lct"></strong></span> <span ng-show="timelog.status != '1'" class="timelog-list-item__chargeable-status"><strong ng-bind-html="0| currencyFormat:cur_code:null:true:cur_lct"></strong></span> </div>
          </li>
        </ul>
      </md-content>
      <md-content ng-if="custom_fields.length>0" class="bg-white" ng-cloak>
        <md-subheader ng-if="custom_fields"><?php echo lang2('custom_fields')?></md-subheader>
        <md-list-item ng-if="custom_fields" ng-repeat="field in custom_fields">
          <md-icon class="{{field.icon}} material-icons"></md-icon>
          <strong flex md-truncate>{{field.name}}</strong>
          <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>
          <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>
          <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>
          <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>
          <md-divider ng-if="custom_fields"></md-divider>
        </md-list-item>
      </md-content>
    </md-content>
  </div>
  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-3 md-pl-0">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="Task">
          <md-icon><i class="ion-android-clipboard text-muted"></i></md-icon>
        </md-button>
        <md-truncate><?php echo lang2('information') ?></md-truncate>
      </div>
    </md-toolbar>
    <div class="col-md-12 col-xs-12 md-pr-0 md-pl-0 md-pb-10" style="background: white">
      <div class="col-xs-12 task-sidebar-item" ng-cloak>
        <ul class="list-inline task-dates">
          <li class="col-md-6 col-xs-6" ng-show="task.relation_type == 'project'">
            <h5><?php echo lang2('related') ?></h5>
            <strong class="text-bold"><a class="label label-info" href="<?php echo base_url('projects/project/{{task.relation}}')?>"><?php echo lang2('project') ?> <i class="ion-android-open"></i></a></strong> </li>
          <li class="col-md-6 col-xs-6" ng-show="task.relation_type == 'ticket'">
            <h5><?php echo lang2('related') ?></h5>
            <strong class="text-bold"><span class="label label-info"><?php echo lang2('ticket') ?></span></strong> </li>
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('status') ?></h5>
            <strong ng-bind="task.status"></strong> </li>
        </ul>
      </div>
      <div class="col-xs-12 task-sidebar-item" ng-cloak>
        <ul class="list-inline task-dates">
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('startdate') ?></h5>
            <strong ng-bind="task.startdate"></strong> </li>
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('duedate') ?></h5>
            <strong ng-bind="task.duedate"></strong> </li>
        </ul>
      </div>
      <div class="col-xs-12 task-sidebar-item" ng-cloak>
        <ul class="list-inline task-dates">
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('priority') ?></h5>
            <strong ng-bind="task.priority"></strong> </li>
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('billable') ?></h5>
            <strong ng-hide="task.billable != true" class="text-bold label label-success"><?php echo lang2('billable') ?></strong> <strong ng-hide="task.billable != false" class="text-bold label label-danger"><?php echo lang2('unbillable') ?></strong> </li>
        </ul>
      </div>
      <div class="col-xs-12 task-sidebar-item" ng-cloak>
        <ul class="list-inline task-dates">
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('assignedby') ?></h5>
            <span class="mdi mdi-assignment-account"></span> <strong ng-bind="task.staff"></strong> </li>
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('createddate') ?></h5>
            <strong ng-bind="task.created"></strong> </li>
        </ul>
      </div>
      <div class="col-xs-12 task-sidebar-item" ng-cloak>
        <ul class="list-inline task-dates">
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('hourlyrate') ?></h5>
            <strong ng-hide="task.billable != true"><span ng-bind-html="task.hourlyrate | currencyFormat:cur_code:null:true:cur_lct"></span></strong> <strong ng-hide="task.billable != false"><?php echo lang2('none') ?></strong> </li>
          <li class="col-md-6 col-xs-6">
            <h5><?php echo lang2('totaltime') ?></h5>
            <strong ng-bind="getTotal() | time:'mm':'hhh mmm':false"></strong> </li>
        </ul>
      </div>
    </div>
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="Invoice" ng-disabled="true">
          <md-icon><i class="ion-document text-muted"></i></md-icon>
        </md-button>
        <h2 flex md-truncate><?php echo lang2('files') ?></h2>
        <?php if (check_privilege('tasks', 'edit')) { ?>
          <md-button ng-click="UploadFile()" class="md-icon-button md-primary" aria-label="Add File" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('upload').' '.lang2('file') ?></md-tooltip>
            <md-icon class="ion-android-add-circle text-success"></md-icon>
          </md-button>
        <?php } ?>
      </div>
    </md-toolbar>
    <md-content class="bg-white">
      <md-list flex ng-cloak>
        <md-list-item class="md-2-line" ng-repeat="file in files | pagination : currentPage*itemsPerPage | limitTo: 6">
          <div class="md-list-item-text image-preview">
            <a ng-if="file.type == 'image'" class="cursor" ng-click="ViewFile($index, image)">
              <md-tooltip md-direction="left"><?php echo lang2('preview') ?></md-tooltip>
              <img src="{{file.path}}">
            </a>
            <a ng-if="(file.type == 'archive')" class="cursor" ng-href="<?php echo base_url('tasks/download_file/{{file.id}}');?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/zip_icon.png');?>">
            </a>
            <a ng-if="(file.type == 'file')" class="cursor" ng-href="<?php echo base_url('tasks/download_file/{{file.id}}');?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/file_icon.png');?>">
            </a>
            <a ng-if="file.type == 'pdf'" class="cursor" ng-href="<?php echo base_url('tasks/download_file/{{file.id}}');?>">
              <md-tooltip md-direction="left"><?php echo lang2('download') ?></md-tooltip>
              <img src="<?php echo base_url('assets/img/pdf_icon.png');?>">
            </a>
          </div>
          <div class="md-list-item-text">
            <a class="cursor" ng-href="<?php echo base_url('tasks/download_file/{{file.id}}');?>">
              <h3 class="link" ng-bind="file.file_name"></h3>
            </a>
          </div>
          <?php if (check_privilege('tasks', 'delete')) { ?>
            <md-icon  ng-click='DeleteFile(file.id)' class="ion-trash-b cursor"></md-icon>
          <?php } ?>
          <md-divider></md-divider>
        </md-list-item>
        <div ng-show="!files.length" class="text-center"><img width="70%" src="<?php echo base_url('assets/img/nofiles.jpg') ?>" alt=""></div>
      </md-list>
      <div ng-show="files.length>6" class="pagination-div" ng-cloak>
        <ul class="pagination">
          <li ng-class="DisablePrevPage()"> <a href ng-click="prevPage()"><i class="ion-ios-arrow-back"></i></a> </li>
          <li ng-repeat="n in range()" ng-class="{active: n == currentPage}" ng-click="setPage(n)"> <a href="#" ng-bind="n+1"></a> </li>
          <li ng-class="DisableNextPage()"> <a href ng-click="nextPage()"><i class="ion-ios-arrow-right"></i></a> </li>
        </ul>
      </div>
    </md-content>
  </div>
  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" style="min-width: 450px;" ng-cloak>
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <h2 flex md-truncate><?php echo lang2('update') ?></h2>
        <md-switch ng-model="task.billable" aria-label="Type"><strong class="text-muted"><?php echo lang2('billable').' ' .lang2('task') ?></strong></md-switch>
      </div>
    </md-toolbar>
    <md-content>
      <md-content layout-padding>
        <md-input-container class="md-block" flex-gt-xs style="display: none;">
          <label><?php echo lang2('relationtype'); ?></label>
          <md-select ng-init="relation_types = [{value: 'project',name: '<?php echo lang2('project'); ?>'}, {value: 'ticket',name: '<?php echo lang2('ticket'); ?>'}];" disabled placeholder="<?php echo lang2('relationtype'); ?>" ng-model="task.relation_type" name="relationtype" style="min-width: 200px;">
            <md-option ng-value="relation_type.value" ng-repeat="relation_type in relation_types"><span class="text-uppercase">{{relation_type.name}}</span></md-option>
          </md-select>
          <br>
        </md-input-container>
        <md-input-container ng-show="task.relation_type == 'project'" class="md-block" flex-gt-xs>
          <label><?php echo lang2('project'); ?></label>
          <md-select  disabled ng-model="task.relation" name="relation" style="min-width: 200px;">
            <md-option ng-value="project.id" ng-repeat="project in projects">{{project.name}}</md-option>
          </md-select>
          <br>
        </md-input-container>
        <md-input-container ng-show="task.milestone != 0" class="md-block" flex-gt-xs>
          <label><?php echo lang2('milestone'); ?></label>
          <md-select disabled ng-model="task.milestone" name="relation" style="min-width: 200px;">
            <md-option ng-value="milestone.id" ng-repeat="milestone in task.project_data.milestones">{{milestone.name}}</md-option>
          </md-select>
          <br>
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('name') ?></label>
          <input required type="text" ng-model="task.name" class="form-control" id="title" placeholder="<?php echo lang2('name'); ?>"/>
        </md-input-container>
        <md-input-container ng-hide="task.billable == false" class="md-block">
          <label><?php echo lang2('task').' ' .lang2('hourlyrate') ?></label>
          <input required type="text" ng-model="task.hourlyrate" class="form-control" id="title" placeholder="0.00"/>
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('startdate') ?></label>
          <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="date" show-icon="true" ng-model="task.startdate_edit" class=" dtp-no-msclear dtp-input md-input">
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('duedate') ?></label>
          <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="date" show-icon="true" ng-model="task.duedate_edit" class=" dtp-no-msclear dtp-input md-input">
        </md-input-container>
        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('assigned'); ?></label>
          <md-select required ng-model="task.assigned" name="assigned" style="min-width: 200px;">
            <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
          </md-select>
        </md-input-container>
        <br>
        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('task').' ' .lang2('priority'); ?></label>
          <md-select ng-init="priorities = [{id: 1,name: '<?php echo lang2('low'); ?>'}, {id: 2,name: '<?php echo lang2('medium'); ?>'}, {id: 3,name: '<?php echo lang2('high'); ?>'}];" required placeholder="<?php echo lang2('priority'); ?>" ng-model="task.priority_id" name="priority" style="min-width: 200px;">
            <md-option ng-value="priority.id" ng-repeat="priority in priorities"><span class="text-uppercase">{{priority.name}}</span></md-option>
          </md-select>
        </md-input-container>
        <br>
        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('task').' ' .lang2('status'); ?></label>
          <md-select ng-init="statuses = [{id: 1,name: '<?php echo lang2('open'); ?>'}, {id: 2,name: '<?php echo lang2('inprogress'); ?>'}, {id: 3,name: '<?php echo lang2('waiting'); ?>'}, {id: 4,name: '<?php echo lang2('complete'); ?>'}];" required placeholder="<?php echo lang2('status'); ?>" ng-model="task.status_id" name="priority" style="min-width: 200px;">
            <md-option ng-value="status.id" ng-repeat="status in statuses"><span class="text-uppercase">{{status.name}}</span></md-option>
          </md-select>
        </md-input-container>
        <br>
        <br>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('description') ?></label>
          <textarea rows="2" required name="description" ng-model="task.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>
        </md-input-container>
        <md-switch style="display: none;" ng-model="task.public" aria-label="Type"><strong class="text-muted"><?php echo lang2('public') ?></strong></md-switch>
        <md-switch style="display: none;" ng-model="task.visible" aria-label="Type"><strong class="text-muted"><?php echo lang2('visiblecustomer') ?></strong></md-switch>
      </md-content>
      <custom-fields-vertical></custom-fields-vertical>
      <md-content>
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="UpdateTask()" class="md-raised md-primary btn-report block-button"><?php echo lang2('update');?></md-button>
          <br/><br/><br/><br/>
        </section>
      </md-content>
    </md-content>
  </md-sidenav>

  <script type="text/ng-template" id="addfile-template.html">
    <md-dialog aria-label="options dialog">
  <md-dialog-content layout-padding>
    <h2 class="md-title"><?php echo lang2('choosefile'); ?></h2>
    <input type="file" required name="file_name" file-model="project_file">
  </md-dialog-content>
  <md-dialog-actions>
    <span flex></span>
    <md-button ng-click="close()" aria-label="add"><?php echo lang2('cancel') ?>!</md-button>
    <md-button ng-click="uploadTaskFile()" class="template-button" ng-disabled="uploading == true">
      <span ng-hide="uploading == true"><?php echo lang2('upload');?></span>
      <md-progress-circular class="white" ng-show="uploading == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
    </md-button>
  </md-dialog-actions>
  </md-dialog>
  </script> 
</div>
<script>
  var TASKID = "<?php echo $task['id'];?>";
  var lang = {};
  lang.doIt = "<?php echo lang2('doIt')?>";
  lang.cancel = "<?php echo lang2('cancel')?>";
  lang.attention = "<?php echo lang2('attention')?>";
  lang.delete_task = "<?php echo lang2('delete_meesage').' '.lang2('task')?>";
</script> 
<script type="text/ng-template" id="stopTimer.html">
  <md-dialog id="stopTimer" style="box-shadow:none;padding:unset;min-width: 25%;">
    <md-toolbar class="toolbar-white">
          <div class="md-toolbar-tools">
            <h2><strong class="text-success"><?php echo lang2('stoptimer') ?></strong></h2>
            <span flex></span>
            <md-button class="md-icon-button" ng-click="close()">
              <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>
              <md-tooltip md-direction="left"><?php echo lang2('close') ?></md-tooltip>
            </md-button>
          </div>
        </md-toolbar>
    <md-dialog-content layout-padding aria-label="wait" style="text-align: center;">
      <div layput-padding style="text-align: left;">
        <md-input-container class="md-block">
          <label><?php echo lang2('note') ?></label>
          <textarea required name="description" ng-model="stopTimer.note" placeholder="<?php echo lang2('typeSomething');?>" class="form-control"></textarea>
        </md-input-container>
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="stopTimerforTask();" class="start-button"><?php echo lang2('confirm');?></md-button>
        </section>
      </div>
    </md-dialog-content>
  </md-dialog>
</script>

<script type="text/ng-template" id="view_image.html">
  <md-dialog aria-label="options dialog">
  <md-dialog-content layout-padding>
    <?php $path = '{{file.path}}';
    if ($path) { ?>
      <img src="<?php echo $path ?>">
    <?php } ?>
  </md-dialog-content>
  <md-dialog-actions>
    <span flex></span>
    <md-button ng-click='DeleteFiles(file.id)'><?php echo lang2('delete') ?>!</md-button>
    <md-button ng-href="<?php echo base_url('tasks/download_file/') ?>{{file.id}}"><?php echo lang2('download') ?>!</md-button>
    <md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>
  </md-dialog-actions>
  </md-dialog>
</script>
<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/tasks.js') ?>"></script>