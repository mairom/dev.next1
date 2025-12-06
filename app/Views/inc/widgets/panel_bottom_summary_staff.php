<div class="ciuis-an-x" ng-cloak>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center blr-5 tlr-5 md-pt-15 md-mr-10"> <span><?php echo lang2('yourleads')?></span>
    <h1 class="txt-scale-xs no-margin-top text-success xs-28px figures"><span  ng-bind="stats.mlc"></span></h1>
    <p class="secondary-text"><span class="label label-success" ng-bind="stats.clc"></span> <?php echo lang2('leadsconverted')?></p>
    <div class="col-md-12">
      <div class="progress">
        <div style="width:{{stats.clp}}%" class="progress-bar progress-bar-success progress-bar-striped active" ng-bind="stats.clp+'%'"> <?php echo lang2('converted')?></div>
      </div>
    </div>
  </div>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center brr-5 trr-5 md-pt-15 md-mr-10"> <span><?php echo lang2('yourtickets')?></span>
    <h1 class="txt-scale-xs no-margin-top text-muted xs-28px figures"><span  ng-bind="stats.mct"></span></h1>
    <p class="secondary-text"> <span class="label label-default"  ng-bind="stats.mct"></span> <?php echo lang2('ticketclosed')?> </p>
    <div class="col-md-12">
      <div class="progress">
        <div style="width:{{stats.mtp}}%" class="progress-bar progress-bar-success progress-bar-striped active" ng-bind="stats.mtp+'%'"> <?php echo lang2('closed')?></div>
      </div>
    </div>
  </div>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center rad-5 md-pt-15 pull-right"> <span><?php echo lang2('upcomingevents')?></span>
    <h1 class="txt-scale-xs no-margin-top text-muted xs-28px figures"><span  ng-bind="stats.ues"></span></h1>
    <div class="col-md-12">
      <div> <a href="<?php echo base_url('calendar')?>" class="btn btn-space btn-default btn-big"><i class="icon mdi ion-ios-calendar-outline"></i> <?php echo lang2('calendar');?></a> </div>
    </div>
  </div>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center rad-5 md-pt-15"> <span><?php echo lang2('yourcustomers') ?></span>
    <h1 class="txt-scale-xs no-margin-top text-muted xs-28px figures"><span  ng-bind="stats.myc"></span></h1>
    <div class="col-md-12">
      <button class="btn btn-space btn-default btn-big"> <i class="icon mdi ion-ios-people-outline"></i> <?php echo lang2('customers');?> </button>
    </div>
  </div>
</div>