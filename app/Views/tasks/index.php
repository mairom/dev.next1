<?php include_once( APPPATH . 'Views/inc/ciuis_data_table_header.php' ); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Tasks_Controller">
  <div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3">
    <div class="panel-heading"> <strong><?php echo lang2('tasksituation'); ?></strong> <span class="panel-subtitle"><?php echo lang2('tasksituationsdesc'); ?></span> </div>
    <div class="row" style="padding: 0px 20px 0px 20px;">
      <div class="col-md-6 col-xs-6 border-right text-uppercase">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(tasks | filter:{status_id:'1'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+tasks.length+' '+'<?php echo lang2('task') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(tasks | filter:{status_id:'1'}).length * 100 / tasks.length }}%;"></span> </span> </div>
        <span style="color:#989898"><?php echo lang2('open'); ?></span> </div>
      <div class="col-md-6 col-xs-6 border-right text-uppercase">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(tasks | filter:{status_id:'2'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+tasks.length+' '+'<?php echo lang2('task') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(tasks | filter:{status_id:'2'}).length * 100 / tasks.length }}%;"></span> </span> </div>
        <span style="color:#989898"><?php echo lang2('inprogress'); ?></span> </div>
      <div class="col-md-6 col-xs-6 border-right text-uppercase">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(tasks | filter:{status_id:'3'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+tasks.length+' '+'<?php echo lang2('task') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(tasks | filter:{status_id:'3'}).length * 100 / tasks.length }}%;"></span> </span> </div>
        <span style="color:#989898"><?php echo lang2('waiting'); ?></span> </div>
      <div class="col-md-6 col-xs-6 border-right text-uppercase">
        <div class="tasks-status-stat">
          <h3 class="text-bold ciuis-task-stat-title"> <span class="task-stat-number" ng-bind="(tasks | filter:{status_id:'4'}).length"></span> <span class="task-stat-all" ng-bind="'/'+' '+tasks.length+' '+'<?php echo lang2('task') ?>'"></span> </h3>
          <span class="ciuis-task-percent-bg"> <span class="ciuis-task-percent-fg" style="width: {{(tasks | filter:{status_id:'4'}).length * 100 / tasks.length }}%;"></span> </span> </div>
        <span style="color:#989898"><?php echo lang2('complete'); ?></span> </div>
    </div>
  </div>
  <div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9 md-p-0">
    <md-toolbar class="bg-white toolbar-white">
      <div class="md-toolbar-tools bg-white">
        <h2 flex md-truncate class="text-bold"><?php echo lang2('tasks'); ?> <small>(<span ng-bind="tasks.length"></span>)</small><br>
          <small flex md-truncate><?php echo lang2('organizeyourtasks'); ?></small></h2>
        <div class="ciuis-external-search-in-table">
          <input ng-model="task_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('search_by').' '.lang2('task').' '.lang2('name')   ?>">
          <md-button class="md-icon-button" aria-label="Search" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('search').' '.lang2('tasks') ?></md-tooltip>
            <md-icon><i class="ion-search text-muted"></i></md-icon>
          </md-button>
        </div>
        <md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>
          <md-tooltip md-direction="bottom"><?php echo lang2('filter').' '.lang2('tasks') ?></md-tooltip>
          <md-icon><i class="ion-android-funnel text-muted"></i></md-icon>
        </md-button>
        <?php if (check_privilege('tasks', 'create')) { ?> 
          <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('new').' '.lang2('task') ?></md-tooltip>
            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
          </md-button>
        <?php } ?>
      </div>
    </md-toolbar>
    <md-content>
      <div ng-show="taskLoader" layout-align="center center" class="text-center" id="circular_loader">
        <md-progress-circular md-mode="indeterminate" md-diameter="25"></md-progress-circular>
        <p style="font-size: 15px;margin-bottom: 5%;">
          <span><?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading'). ' '. lang2('tasks').'...' ?></strong></small></span>
        </p>
      </div>
      <div ng-show="!taskLoader" class="bg-white" style="padding: unset;">
        <md-table-container ng-show="tasks.length > 0">
          <table md-table  md-progress="promise" ng-cloak>
            <thead md-head md-order="task_list.order">
              <tr md-row>
                <th md-column md-order-by="name"><span><?php echo lang2('task'); ?></span></th>
                <th md-column md-order-by="startdate"><span><?php echo lang2('startdate'); ?></span></th>
                <th md-column md-order-by="duedate"><span><?php echo lang2('duedate'); ?></span></th>
                <th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
              </tr>
            </thead>
            <tbody md-body>
              <tr class="select_row" md-row ng-repeat="task in tasks | orderBy: task_list.order | limitTo: task_list.limit : (task_list.page -1) * task_list.limit | filter: task_search | filter: FilteredData" class="cursor" ng-click="goToLink('tasks/task/'+task.id)">
                <td md-cell>
                  <strong>
                    <a class="link" ng-href="<?php echo base_url('tasks/task/')?>{{task.id}}"> <strong ng-bind="task.task_number"></strong></a> <br>
                    <small ng-bind="task.name"></small>
                  </strong>
                </td>
                <td md-cell>
                  <strong ng-bind="task.startdate"></strong>
                </td>
                <td md-cell>
                  <strong class="badge" ng-bind="task.duedate"></strong>
                </td>
                <td md-cell>
                  <span>
                    <strong ng-bind="task.status"></strong>
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </md-table-container>
        <md-table-pagination ng-show="tasks.length > 0" md-limit="task_list.limit" md-limit-options="limitOptions" md-page="task_list.page" md-total="{{tasks.length}}" ></md-table-pagination>
        <md-content ng-show="!tasks.length" class="md-padding no-item-data" ng-cloak><?php echo lang2('notdata') ?></md-content>
      </div>
    </md-content>
  </div>
  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">
    <md-toolbar class="md-theme-light" style="background:#262626">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <md-truncate><?php echo lang2('filter') ?></md-truncate>
      </div>
    </md-toolbar>
    <md-content layout-padding="">
      <div ng-repeat="(prop, ignoredValue) in tasks[0]" ng-init="filter[prop]={}" ng-if="prop != 'id'  && prop != 'name' && prop != 'relationtype' && prop != 'duedate' && prop != 'startdate' && prop != 'status' && prop != 'done' && prop != 'status_id' && prop != 'task_number'">
        <div class="filter col-md-12">
          <h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>
          <hr>
          <div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)">
            <md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>
          </div>
        </div>
      </div>
    </md-content>
  </md-sidenav>


  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" style="min-width: 450px;" ng-cloak>
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <h2 flex md-truncate><?php echo lang2('create') ?></h2>
        <md-switch ng-model="isBillable" aria-label="Type"><strong class="text-muted"><?php echo lang2('billable').' '.lang2('task') ?></strong>
          <md-tooltip ng-hide="savingInvoice == true" md-direction="bottom"><?php echo lang2('task_as_billable') ?></md-tooltip>
        </md-switch>
      </div>
    </md-toolbar>
    <md-content>
      <md-content layout-padding="">
        <md-input-container class="md-block" flex-gt-xs style="display: none;">
          <label><?php echo lang2('relationtype'); ?></label>
          <md-select ng-init="relation_types = [{value: 'project',name: '<?php echo lang2('project'); ?>'}, {value: 'ticket',name: '<?php echo lang2('ticket'); ?>'}];" disabled placeholder="<?php echo lang2('relationtype'); ?>" ng-model="Relation_Type" name="relationtype" style="min-width: 200px;">
            <md-option ng-value="relation_type.value" ng-repeat="relation_type in relation_types"><span class="text-uppercase">{{relation_type.name}}</span></md-option>
          </md-select>
          <br>
        </md-input-container>
        <md-input-container ng-show="Relation_Type == 'project'" class="md-block" flex-gt-xs>
          <label><?php echo lang2('project'); ?></label>
          <md-select required ng-model="RelatedProject" name="relation" style="min-width: 200px;">
            <md-option ng-value="project" ng-repeat="project in projects">{{project.name}}</md-option>
          </md-select>
          <br>
        </md-input-container>
        <md-input-container ng-show="Relation_Type == 'project'" class="md-block" flex-gt-xs>
          <label><?php echo lang2('milestone'); ?></label>
          <md-select ng-model="SelectedMilestone" name="relation" style="min-width: 200px;">
            <md-option ng-value="milestone.id" ng-repeat="milestone in RelatedProject.milestones">{{milestone.name}}</md-option>
          </md-select>
          <br>
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('name') ?></label>
          <input required type="text" ng-model="task.name" class="form-control" id="title" placeholder="<?php echo lang2('name'); ?>"/>
        </md-input-container>
        <md-input-container ng-show="isBillable === true" class="md-block">
          <label><?php echo lang2('task').' ' .lang2('hourlyrate') ?></label>
          <input required type="text" ng-model="task.hourlyrate" class="form-control" id="title" placeholder="0.00"/>
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('startdate') ?></label>
          <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" show-icon="true" ng-model="task.startdate" class=" dtp-no-msclear dtp-input md-input">
        </md-input-container>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' ' .lang2('duedate') ?></label>
          <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('chooseadate') ?>" show-todays-date="" minutes="true" min-date="task.startdate" show-icon="true" ng-model="task.duedate" class=" dtp-no-msclear dtp-input md-input">
        </md-input-container>
        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('assigned'); ?></label>
          <md-select required ng-model="task.assigned" name="assigned" style="min-width: 200px;">
            <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
          </md-select>
        </md-input-container>
        <br>
        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('task').' '.lang2('priority'); ?></label>
          <md-select ng-init="priorities = [{id: 1,name: '<?php echo lang2('low'); ?>'}, {id: 2,name: '<?php echo lang2('medium'); ?>'}, {id: 3,name: '<?php echo lang2('high'); ?>'}];task.priority_id = 2;" required placeholder="<?php echo lang2('task').' '.lang2('priority'); ?>" ng-model="task.priority_id" name="priority" style="min-width: 200px;">
            <md-option ng-value="priority.id" ng-repeat="priority in priorities"><span class="text-uppercase">{{priority.name}}</span></md-option>
          </md-select>
        </md-input-container>
        <br>
        <md-input-container class="md-block" flex-gt-xs>
          <label><?php echo lang2('task').' '.lang2('status'); ?></label>
          <md-select ng-init="statuses = [{id: 1,name: '<?php echo lang2('open'); ?>'}, {id: 2,name: '<?php echo lang2('inprogress'); ?>'}, {id: 3,name: '<?php echo lang2('waiting'); ?>'}, {id: 4,name: '<?php echo lang2('complete'); ?>'}];task.status_id = 1;" required placeholder="<?php echo lang2('task').' '.lang2('status'); ?>" ng-model="task.status_id" name="priority" style="min-width: 200px;">
            <md-option ng-value="status.id" ng-repeat="status in statuses"><span class="text-uppercase">{{status.name}}</span></md-option>
          </md-select>
        </md-input-container> 
        <br>
        <md-input-container class="md-block">
          <label><?php echo lang2('task').' '.lang2('description') ?></label>
          <textarea rows="2" required name="description" ng-model="task.description" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>
        </md-input-container>
        <md-switch style="display: none;" ng-model="isPublic" ng-init="isPublic = true" aria-label="Type"><strong class="text-muted"><?php echo lang2('public') ?></strong></md-switch>
        <md-switch style="display: none;" ng-model="isVisible" ng-init="isVisible = true" aria-label="Type"><strong class="text-muted"><?php echo lang2('visiblecustomer') ?></strong></md-switch>
		</md-content>
        <custom-fields-vertical></custom-fields-vertical>
		<md-content>
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="AddTask()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">
            <span ng-hide="saving == true"><?php echo lang2('create');?></span>
            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
          </md-button>
          <br/><br/><br/><br/>
        </section>
      </md-content>
    </md-content>
  </md-sidenav>
</div>
<?php include_once( APPPATH . 'Views/inc/other_footer.php' ); ?>
<script src="<?php echo base_url('assets/js/tasks.js'); ?>"></script>