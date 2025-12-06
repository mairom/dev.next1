<?php include_once(APPPATH . 'Views/inc/header.php'); ?> 
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Calendar_Controller">
  <md-content class="main-content container-fluid col-md-12">
    <md-content class="bg-white">
      <md-content id="calendar" class="bg-white col-md-9" layout-padding></md-content>
      <md-content class="bg-white" style="top: 15px;">
        <md-tabs md-dynamic-height md-border-bottom>
          <md-tab label="<?php echo lang2('events')?>">
            <md-content class="events events_xs calendar-events" style="margin-top: 0px">
              <md-toolbar class="toolbar-white" style="border-bottom:unset">
                <div class="md-toolbar-tools">
                  <h3 flex md-truncate><?php echo lang2('today_events') ?></h3>
                  <md-button ng-click="EventForm()" class="md-icon-button md-primary" aria-label="Add Event" ng-cloak>
                    <md-tooltip md-direction="bottom"><?php echo lang2('addevent') ?></md-tooltip>
                    <md-icon class="ion-android-add-circle text-success"></md-icon>
                  </md-button>
                </div>
              </md-toolbar>
              <ul style="padding: 0px 20px 0px 20px;" ng-cloak>
                <li ng-repeat="event in today_events" class="{{event.status}}">
                  <label class="date"> <span class="weekday" ng-bind="event.day"></span><span class="day"
                      ng-bind="event.aday"></span> </label>
                  <h3 ng-bind="event.title"></h3>
                  <p>
                    <span class='duration' ng-bind="event.start_iso_date | date : 'MMM d, y h:mm:ss a'"></span>
                    <span class='location' ng-bind="event.staff"></span>
                  </p>
                </li>
              </ul>
              <md-content ng-show="!today_events.length" class="text-center bg-white" ng-cloak>
                <h1 class="text-success"><i class="mdi mdi-calendar-check"></i></h1>
                <span class="text-muted"><?php echo lang2('no_event_today') ?></span>
              </md-content>
            </md-content>
          </md-tab>
          <md-tab label="<?php echo lang2('appointments')?>">
            <md-content class="appointments appointments_xs calendar-appointments" style="margin-top: 0px">
              <md-toolbar class="toolbar-white" style="border-bottom:unset">
                <div class="md-toolbar-tools">
                  <h3 flex md-truncate><strong><?php echo lang2('requested_appointments') ?></strong></h3>
                  <!-- <?php if (!session()->get('other')) { ?>
                  <md-button ng-click="EventForm()" class="md-icon-button md-primary" aria-label="Create" ng-cloak>
                    <md-tooltip md-direction="bottom"><?php echo lang2('new_appointment') ?></md-tooltip>
                    <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
                  </md-button>
                  <?php } ?> -->
                </div>
              </md-toolbar> 
              <ul style="padding: 0px 20px 0px 20px;" ng-cloak>
                <li ng-repeat="appointment in requested_appointments" class="{{appointment.status_class}}"
                  ng-click="ShowAppointment(appointment.id)">
                  <label class="date"> <span class="weekday" ng-bind="appointment.day"></span><span class="day"
                      ng-bind="appointment.aday"></span> </label>
                  <h3 ng-bind="appointment.title"></h3>
                  <p>
                    <span class='duration' ng-bind="appointment.start_iso_date | date : 'MMM d, y h:mm:ss a'"></span>
                    <span class='location' ng-bind="appointment.staff"></span>
                  </p>
                </li>
              </ul>
              <md-content ng-show="!requested_appointments.length" class="text-center bg-white" ng-cloak>
                <h1 class="text-success"><i class="mdi mdi-calendar-check"></i></h1>
                <span class="text-muted"><?php echo lang2('no_requested_appointment') ?></span>
              </md-content>
            </md-content>
            <md-content class="appointments appointments_xs calendar-appointments" style="margin-top: 0px">
              <!-- <md-subheader class="md-no-sticky event-subheader"><i class="mdi mdi-calendar-alt"></i>
                <?php echo lang2('today_appointments') ?></md-subheader> -->
              <md-toolbar class="toolbar-white" style="border-bottom:unset">
                <div class="md-toolbar-tools">
                  <h3 flex md-truncate><strong><?php echo lang2('today_appointments') ?></strong></h3>
                </div>
              </md-toolbar>
              <ul style="padding: 0px 20px 0px 20px;" ng-cloak>
                <li ng-repeat="appointment in today_appointments" class="{{appointment.status_class}}"
                  ng-click="ShowAppointment(appointment.id)">
                  <label class="date"> <span class="weekday" ng-bind="appointment.day"></span><span class="day"
                      ng-bind="appointment.aday"></span> </label>
                  <h3 ng-bind="appointment.title"></h3>
                  <p>
                    <span class='duration' ng-bind="appointment.start_iso_date | date : 'MMM d, y h:mm:ss a'"></span>
                    <span class='location' ng-bind="appointment.staff"></span>
                  </p>
                </li>
              </ul>
              <md-content ng-show="!today_appointments.length" class="text-center bg-white" ng-cloak>
                <h1 class="text-success"><i class="mdi mdi-calendar-check"></i></h1>
                <span class="text-muted"><?php echo lang2('no_appointment_today') ?></span>
              </md-content>
            </md-content>
          </md-tab>
          <md-tab>
            <md-tab-label>
              <md-tooltip md-direction="bottom"><?php echo lang2('settings') ?></md-tooltip>
              <md-icon class="ion-gear-b"></md-icon>
            </md-tab-label>
            <md-tab-body>
              <md-toolbar class="toolbar-white">
                <div class="md-toolbar-tools">
                  <h2 flex md-truncate><strong><?php echo lang2('settings') ?></strong></h2>
                  <?php if (!session()->get('other')) { ?>
                    <md-button ng-click="save_colors()" class="md-raised success-button" ng-disabled="saving == true">
                      <span ng-hide="saving == true"><?php echo lang2('save'); ?></span>
                      <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                    </md-button>
                  <?php } ?>
                </div>
              </md-toolbar>
              <md-content class="bg-white">
                <br>
                <div>
                  <md-input-container class="md-block">
                    <label><?php echo lang2('pickacolor') . ' ' . lang2('appointments')?></label>
                    <input color-picker color-picker-model="appointment_color" ng-model="appointment_color"
                    ng-style="{background:appointment_color}" color-picker-position="bottom" />
                  </md-input-container>
                  <md-input-container class="md-block">
                    <label><?php echo lang2('pickacolor') . ' ' . lang2('projects')?></label>
                    <input color-picker color-picker-model="project_color" ng-model="project_color"
                  ng-style="{background:project_color}" color-picker-position="bottom" />
                  </md-input-container>
                  <md-input-container class="md-block">
                    <label><?php echo lang2('pickacolor') . ' ' . lang2('tasks')?></label>
                    <input color-picker color-picker-model="task_color" ng-model="task_color"
                  ng-style="{background:task_color}" color-picker-position="bottom" />
                  </md-input-container>
                </div>
              </md-content>
            </md-tab-body>
          </md-tab>
        </md-tabs>
      </md-content>
    </md-content>
  </md-content>
  <div id="fullCalModalevent" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span
              class="sr-only"><?php echo lang2('close')?></span></button>
          <h4 class="event_type" class="modal-title text-bold"></h4><br>
          <span class="startdate"></span>&nbsp;&nbsp; <b><?php echo lang2('to') ?></b> &nbsp;&nbsp;
          <span class="enddate"></span>
        </div>
        <div id="modalBody" class="modal-body">
          <h4 class="modalTitle" class="modal-title text-bold"></h4>
          <p>
            <b><?php echo lang2('note') ?>: </b> <span id="eventdetail"></span>
          </p>
          <div class="pull-left">
            <p>
              <b><?php echo lang2('staff') ?>: </b> <span id="eventstaffname"></span>
            </p>
          </div>
        </div>
        <div class="modal-footer">
          <md-button class="md-raised" data-dismiss="modal" aria-label="add"><?php echo lang2('close') ?>!</md-button>
          <?php if (!session()->get('other')) { ?>
            <button class="btn btn-default" style="background: red !important;color: #fff !important;border: 1px solid red !important;"><a id="eventUrl"><?php echo lang2('delete')?></a></button>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <div id="fullCalModalappointment" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span
              class="sr-only"><?php echo lang2('close')?></span></button>
          <h4 class="event_type" class="modal-title text-bold"></h4><br>
        </div>
        <div id="modalBody" class="modal-body">
          <span class="startdate"></span>&nbsp;&nbsp; <b><?php echo lang2('to') ?></b> &nbsp;&nbsp;
          <span class="enddate"></span><br>
          <br><br>
          <p>
            <b><?php echo lang2('staff') ?>: </b> <span id="appointmentstaff"></span>
          </p>
        </div>
        <div class="modal-footer">
          <md-button class="md-raised" data-dismiss="modal" aria-label="add"><?php echo lang2('close') ?>!</md-button>
        </div>
      </div>
    </div>
  </div>
  <div id="fullCalModalproject" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> 
            <span class="sr-only"><?php echo lang2('close')?></span>
          </button>
          <h4 class="event_type" class="modal-title text-bold"></h4><br>
        </div>
        <div id="modalBody" class="modal-body">
          <span class="startdate"></span>&nbsp;&nbsp; <b><?php echo lang2('to') ?></b> &nbsp;&nbsp;
          <span class="enddate"></span><br>
          <br><br>
          <p>
            <b><?php echo lang2('summary') ?>: </b> <span id="projecttdescription"></span>
          </p>
        </div>
        <div class="modal-footer">
          <md-button class="md-raised" data-dismiss="modal" aria-label="add"><?php echo lang2('close') ?>!</md-button>
        </div>
      </div>
    </div>
  </div>
  <div id="fullCalModaltask" class="modal fade"> 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> 
            <span class="sr-only"><?php echo lang2('close')?></span>
          </button>
          <h4 class="event_type" class="modal-title text-bold"></h4><br>
        </div>
        <div id="modalBody" class="modal-body">
          <span class="startdate"></span>&nbsp;&nbsp; <b><?php echo lang2('to') ?></b> &nbsp;&nbsp;
          <span class="enddate"></span><br>
          <br><br>
          <p>
            <b><?php echo lang2('summary') ?>: </b> <span id="taskdescription"></span>
          </p>
          <p>
            <b><?php echo lang2('staff') ?>: </b> <span id="taskstaff"></span>
          </p>
          <p>
            <b><?php echo lang2('priority') ?>: </b> <span id="taskpriority"></span>
          </p>
          <p>
            <b><?php echo lang2('status') ?>: </b> <span id="taskstatus"></span>
          </p>
        </div>
        <div class="modal-footer">
          <md-button class="md-raised" data-dismiss="modal" aria-label="add"><?php echo lang2('close') ?>!</md-button>
        </div>
      </div>
    </div>
  </div>
  <div style="visibility: hidden">
    <?php if (!session()->get('other')) { ?>
    <div ng-repeat="appointment in appointments" class="md-dialog-container" id="Appointment-{{appointment.id}}">
      <md-dialog aria-label="Appointment Detail">
        <form>
          <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
              <h2>{{appointment.title}}</h2>
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
                  <md-icon class="ion-person"></md-icon>
                  <p ng-bind="appointment.contact"></p>
                </md-list-item>
                <md-divider></md-divider>
                <md-content layout-padding>
                  <h3 class="md-mt-0" ng-bind="appointment.start_iso_date | date : 'MMM d, y h:mm:ss a'"></h3>
                  <span ng-bind="appointment.detail"></span>
                </md-content>
                <md-list-item>
                  <md-icon class="ion-flag"></md-icon>
                  <p ng-if="appointment.status == '0'"><strong class="text-warning"><?php echo lang2('requested') ?></strong></p>
                  <p ng-if="appointment.status == '1'"><strong class="text-success"><?php echo lang2('confirmed') ?></strong></p>
                  <p ng-if="appointment.status == '2'"><strong class="text-danger"><?php echo lang2('declined') ?></strong></p>
                  <p ng-if="appointment.status == '3'"><strong class="text-success"><?php echo lang2('done') ?></strong>
                  </p>
                </md-list-item>
              </md-list>
            </md-content>
          </md-dialog-content>
          <md-dialog-actions layout="row">
            <md-button ng-click='MarkAsDoneAppointment(appointment.id)' aria-label="Done">
              <?php echo lang2('mark_as_done')?>
            </md-button>
            <span flex></span>
            <md-button ng-click='DeclineAppointment(appointment.id)' aria-label="Decline">
              <?php echo lang2('decline')?> <i class="ion-close-round"></i>
            </md-button>
            <md-button ng-click="ConfirmAppointment(appointment.id)" style="margin-right:20px;" aria-label="Confirm">
              <?php echo lang2('confirm')?> <i class="ion-checkmark-round"></i>
            </md-button>
          </md-dialog-actions>
        </form>
      </md-dialog>
    </div>
    <?php } ?>
  </div>
  <md-sidenav class="md-sidenav-right md-whiteframe-4dp eventFormToggle" md-component-id="EventForm" ng-cloak style="width: 450px;">
    <md-toolbar class="md-theme-light" style="background:#262626">
      <div class="md-toolbar-tools">
        <md-button ng-click="CloseEventForm()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <h2 flex md-truncate><?php echo lang2('addevent') ?></h2>
        <md-switch ng-model="event_reminder" aria-label="Type"><strong class=""><?php echo lang2('notification') ?></strong></md-switch>
      </div>
    </md-toolbar>
    <md-content layout-padding="">
      <md-content layout-padding>
        <md-input-container class="md-block">
          <label><?php echo lang2('enter') . ' ' . lang2('title') ?></label>
          <input ng-model="event_title">
        </md-input-container>
        <div layout="row" layout-wrap>
          <div flex-gt-xs="90" flex-xs="90">
            <md-input-container class="md-block">
              <label><?php echo lang2('event') . ' ' . lang2('type'); ?></label>
              <md-select ng-change="changeEventTypes(eventType)" placeholder="<?php echo lang2('event') . ' ' . lang2('type'); ?>" ng-model="eventType" style="min-width: 200px;">
                <md-option ng-repeat="eventtype in eventtypes" ng-value="eventtype">{{eventtype.name}}
                  &nbsp;&nbsp;&nbsp;&nbsp;
                  <span class="color-select {{eventtype.color}}-color" style="padding: 7px !important;background: {{eventtype.color}}">&nbsp;</span>
                </md-option>
              </md-select>
            </md-input-container><br>
          </div>
          <div flex-gt-xs="10" flex-xs="10">
            <a class="cursor link" ng-click="ManageEventTypes()">
              <md-tooltip md-direction="top">
                <span ng-show="eventtypes.length > 0"><?php echo lang2('manage') ?></span>
                <span ng-show="eventtypes.length == 0 || !eventtypes"><?php echo lang2('add') ?></span>
                <?php echo lang2('event') . ' ' . lang2('type'); ?>
              </md-tooltip>
              <md-icon style="margin-top: 23px;"><i class="ion-gear-a text-muted"></i></md-icon>
            </a>
          </div>
        </div>
        <div layout="row" layout-wrap>
          <div flex-gt-xs="50" flex-xs="50">
            <md-input-container class="md-block">
              <label><?php echo lang2('event') . ' ' . lang2('start') ?></label>
              <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" placeholder="<?php echo lang2('event') . ' ' . lang2('start') ?>" show-todays-date="" minutes="true" min-date="date" show-icon="true" ng-model="event_start" class=" dtp-no-msclear dtp-input md-input" ng-init="event_start=date">
            </md-input-container>
          </div>
          <div flex-gt-xs="50" flex-xs="50">
            <md-input-container class="md-block">
              <label><?php echo lang2('event') . ' ' . lang2('end') ?></label>
              <input mdc-datetime-picker="" date="true" time="true" type="text" id="datetime" minutes="true" min-date="event_start" show-icon="true" ng-model="event_end" class="dtp-no-msclear dtp-input md-input" placeholder="<?php echo lang2('event') . ' ' . lang2('end') ?>">
            </md-input-container>
          </div>
        </div>
        <md-input-container class="md-block">
          <label><?php echo lang2('assign') ?></label>
          <md-select placeholder="<?php echo lang2('assign'); ?>" ng-model="event_staff" ng-init="event_staff = <?php echo session()->get('usr_id') ?>">
            <md-option ng-repeat="staffmember in staff" value="staffmember.id" ng-value="staffmember.id">
            {{staffmember.name}}</md-option>
          </md-select>
        </md-input-container>
        <div layout="row" layout-wrap ng-show="event_reminder">
          <div flex-gt-xs="30" flex-xs="30">
            <md-input-container class="md-block">
              <label><?php echo lang2('notification'); ?></label>
              <md-select ng-change="changeEventType(event_ntf_type)" placeholder="<?php echo lang2('notification'); ?>" ng-model="event_ntf_type" ng-init="event_ntf_type = 'reminder'">
                <md-option value="reminder"><?php echo lang2('reminder'); ?></md-option>
                <md-option value="email"><?php echo lang2('email'); ?></md-option>
              </md-select>
            </md-input-container>
            <div ng-show="send_to_all" class="ciuis-body-checkbox has-primary pull-left">
              <input ng-model="email_to_all" name="email_to_all" class="ci-public-check ng-pristine ng-untouched ng-valid ng-empty" id="email_to_all" type="checkbox" value="1" aria-invalid="false">
              <label for="email_to_all" class="ng-binding"><?php echo lang2('email_to_all'); ?></label>
            </div>
          </div>
          <div flex-gt-xs="30" flex-xs="30">
            <md-input-container class="md-block">
              <input ng-model="event_ntf_duration" type="number" placeholder="<?php echo lang2('period'); ?>" ng-init="event_ntf_duration = 1">
            </md-input-container>
          </div>
          <div flex-gt-xs="30" flex-xs="30">
            <md-input-container class="md-block">
              <label><?php echo lang2('time'); ?></label>
              <md-select ng-model="event_ntf_time" ng-init="event_ntf_time = '2'">
                <md-option value="0"><?php echo lang2('minutes'); ?></md-option>
                <md-option value="1"><?php echo lang2('hours'); ?></md-option>
                <md-option value="2"><?php echo lang2('days'); ?></md-option>
                <md-option value="3"><?php echo lang2('weeks'); ?></md-option>
              </md-select>
            </md-input-container>
          </div>
        </div>
        <md-input-container class="md-block">
          <label><?php echo lang2('event') . ' ' . lang2('note') ?></label>
          <textarea required name="detail" rows="2" ng-model="event_detail" placeholder="<?php echo lang2('note'); ?>" class="form-control note-description"></textarea>
        </md-input-container>
        <div>
          <md-button ng-click="AddEvent()" class="md-raised md-primary btn-report block-button" aria-label='Add Event' ng-disabled="addingEvent == true">
            <span ng-show="addingEvent == false"><?php echo lang2('add') . ' ' . lang2('event') ?></span>
            <md-progress-circular class="white" ng-show="addingEvent == true" md-mode="indeterminate" md-diameter="20">
            </md-progress-circular>
          </md-button>
          <br/><br/><br/><br/>
        </div>
      </md-content>
    </md-content>
  </md-sidenav>
  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ManageEventTypes" ng-cloak style="min-width: 450px;">
    <md-toolbar class="md-theme-light" style="background:#262626">
      <div class="md-toolbar-tools">
        <md-button ng-click="closeEventTypes()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
        <md-truncate><?php echo lang2('manage') . ' ' . lang2('event') . ' ' . lang2('types') ?></md-truncate>
      </div>
    </md-toolbar>
    <md-content layout-padding="">
      <md-toolbar class="toolbar-white" style="background:#262626">
        <div class="md-toolbar-tools">
          <h4 class="text-bold text-muted" flex><?php echo lang2('event') . ' ' . lang2('types') ?></h4>
          <md-button aria-label="Add Status" class="md-icon-button" ng-click="neweventtype = true">
            <md-tooltip md-direction="bottom"><?php echo lang2('new') . ' ' . lang2('event') . ' ' . lang2('type') ?></md-tooltip>
            <md-icon><i class="ion-plus-round text-success"></i></md-icon>
          </md-button>
        </div>
      </md-toolbar><br ng-show="neweventtype">
      <md-input-container ng-show="neweventtype" class="md-block">
        <label><?php echo lang2('enter') . ' ' . lang2('title') ?></label>
        <input ng-model="event_type.name">
      </md-input-container>
      <md-input-container ng-show="neweventtype" class="md-block">
        <label><?php echo lang2('event') . ' ' . lang2('color'); ?></label>
        <input color-picker color-picker-model="event_type.color" ng-model="event_type.color" color-picker-position="bottom" ng-style="{background:event_type.color}"/>
      </md-input-container><br ng-show="neweventtype">
      <div ng-show="neweventtype" class="ciuis-body-checkbox has-primary pull-left">
        <input ng-model="event_type.public" name="public" class="ci-public-check" id="public" type="checkbox" value="1">
        <label for="public" ng-bind='lang.publicevent'></label>
      </div>
      <div ng-show="neweventtype" style="border-bottom: 1px solid #e5e5e5;padding-bottom: 12%;">
        <div class="pull-right">
          <md-button ng-click="AddEventType()" class="md-raised md-primary pull-right" aria-label='Add Event' ng-disabled="addingEventType == true">
            <span ng-show="addingEventType == false"><?php echo lang2('add') . ' ' . lang2('event') . ' ' . lang2('type') ?></span>
            <md-progress-circular class="white" ng-show="addingEventType == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
          </md-button>
        </div>
      </div>
      <md-content>
        <md-list-item ng-repeat="eventtype in eventtypes" class="noright" aria-label="Edit Status">
          <div layout="row" layout-wrap style="width: 100% !important;">
            <div flex-gt-xs="60" flex-xs="60">
              <strong ng-bind="eventtype.name"></strong>&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <div flex-gt-xs="30" flex-xs="30">
              <span ng-show="eventtype.public == '1'" ng-style="{background:eventtype.color}" class="badge" style="padding: 4px 7px !important;color: #000;background: {{eventtype.color}}">
                <?php echo lang2('public') ?>
              </span>
              <span ng-show="eventtype.public == '0'" ng-style="{background:eventtype.color}" class="badge" style="padding: 4px 7px !important;color: #000;background: {{eventtype.color}}">
                <?php echo lang2('private') ?>
              </span>
            </div>
            <div flex-gt-xs="10" flex-xs="10">
              <md-icon ng-click='DeleteEventType(eventtype.id)' aria-label="Remove Event Type" class="md-hue-3 ion-trash-b"></md-icon>
            </div>
          </div>
        </md-list-item>
      </md-content>
    </md-content>
  </md-sidenav>
  <button id="openEventModals" style="display: none;" ng-click="EventForm()"></button>
  <?php include_once( APPPATH . 'Views/inc/other_footer.php' );?>
  <script src='<?php echo base_url('assets/lib/colorpicker/colorpicker.js'); ?>'></script>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/colorpicker/colorpicker.css'); ?>" />
  <script src='<?php echo base_url('assets/js/ciuis_with_colorpicker.js'); ?>'></script>
  <script src='<?php echo base_url('assets/js/calendar.js'); ?>'></script>

  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/jquery.fullcalendar/packages/core/main.min.css'); ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/jquery.fullcalendar/fullcalendar.min.css'); ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/jquery.fullcalendar/fullcalendar.min.css'); ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/jquery.fullcalendar/ciuis_calendar.css'); ?>" />
  <script src='<?php echo base_url('assets/lib/jquery.fullcalendar/packages/core/main.min.js'); ?>'></script>
  <script src='<?php echo base_url('assets/lib/jquery.fullcalendar/packages/interaction/main.min.js'); ?>'></script>
  <script src='<?php echo base_url('assets/lib/jquery.fullcalendar/packages/daygrid/main.min.js'); ?>'></script>
  <script src='<?php echo base_url('assets/lib/jquery.fullcalendar/packages/list/main.min.js'); ?>'></script>
  <script src='<?php echo base_url('assets/lib/jquery.fullcalendar/packages/google-calendar/main.min.js'); ?>'></script>
  <script src='<?php echo base_url('assets/lib/jquery.fullcalendar/locales-all.min.js'); ?>'></script>
  <script>
    var appointment_color = "<?php echo $appconfig['appointment_color'];?>";
    var project_color = "<?php echo $appconfig['project_color'];?>";
    var task_color = "<?php echo $appconfig['task_color'];?>";
    var load_more = "<?php echo lang2('load_more');?>";
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarSection = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarSection, {
        editable: false,
        locale: initialLocaleCode,
        plugins: ['interaction', 'dayGrid', 'list'],
      //plugins: ['interaction', 'dayGrid', 'list', 'googleCalendar'],
      eventLimit: 2,
      //googleCalendarApiKey: '',
      header: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek"
      },
      events: {
        //googleCalendarId: ''
      },
      eventClick: function(event, jsEvent, view) {
        if (event.url) {
          return false;
        }
        var events = event.event._def.extendedProps;
        if (events.relation == 'event') {
          $('#eventdetail').html(events.detail);
          $('#eventdescription').html(events.text);
          $('#eventstaffname').html(events.staff);
          $('.startdate').text(events.start_date);
          $('.enddate').text(events.end_date);
          if (events.event_type) {
            $('.event_type').text(events.event_type);
            $('.modalTitle').html(event.event._def.title);
          } else {
            $('.event_type').text(event.event._def.title);
            $('#eventstaffname').html('');
          }
          $('#eventUrl').attr('href', '<?php echo base_url('calendar/remove/')?>' + event.event._def
            .publicId);
          $('#fullCalModalevent').modal();
        } else if (events.relation == 'appointment') {
          $('.event_type').html(event.event._def.title);
          $('.startdate').text(events.start_date);
          $('.enddate').text(events.end_date);
          $('#appointmentstaff').html(events.staff);
          $('#fullCalModalappointment').modal();
        } else if (events.relation == 'project') {
          $('.event_type').html(event.event._def.title);
          $('.startdate').text(events.start_date);
          $('.enddate').text(events.end_date);
          $('#projecttdescription').html(events.text);
          $('#fullCalModalproject').modal();
        } else if (events.relation == 'task') {
          $('.event_type').html(event.event._def.title);
          $('.startdate').text(events.start_date);
          $('.enddate').text(events.end_date);
          $('#taskdescription').html(events.text);
          $('#taskstaff').html(events.staff);
          $('#taskpriority').html(events.priority);
          $('#taskstatus').html(events.status);
          $('#fullCalModaltask').modal();
        }
      },
      selectable: true,
      select: function(start, end, jsEvent, view) {
        console.log(start, end, jsEvent, view)
        $('#openEventModals').click();
      },
      eventSources: [{
        url: '<?php echo base_url('api/events');?>',
      },
      {
        url: '<?php echo base_url('api/all_appointments');?>',
      },
      {
        url: '<?php echo base_url('api/calendar_projects');?>',
      },
      {
        url: '<?php echo base_url('api/calendar_tasks');?>',
      },
      ],
      maxEventRows: 2,
      eventLimit: true,
      eventLimitText: load_more,
      views: {
        timeGrid: {
          eventLimit: 2
        }
      },
      eventOverlap: false,
    });
      calendar.render();
    });
  </script>
</div>