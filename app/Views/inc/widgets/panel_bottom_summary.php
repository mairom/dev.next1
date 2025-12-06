<div class="ciuis-an-x" ng-cloak>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center blr-5 tlr-5 md-pt-15 panel-box1"> <span><?php echo lang2('outstandinginvoices'); ?></span>
    <h1 class="txt-scale-xs no-margin-top text-danger xs-28px figures"><span ng-bind-html="stats.oft | currencyFormat:cur_code:null:true:cur_lct"></span></h1>
    <p class="secondary-text"> <span class="label label-danger" ng-bind="stats.tef"></span> <span><?php echo lang2('xinvoicenotpaid'); ?></span> </p>
    <div class="col-md-12">
      <div class="progress">
        <div style="width:{{stats.ofy}}%" class="progress-bar progress-bar-danger progress-bar-striped active" ng-bind="stats.ofy+'%'"> <?php echo lang2('notpaid'); ?></div>
      </div>
    </div>
  </div>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center brr-5 trr-5 md-pt-15 panel-box2"> <?php echo lang2('monthlyturnover'); ?>
    <h1 class="txt-scale-xs no-margin-top xs-28px figures"><span ng-bind-html="stats.akt | currencyFormat:cur_code:null:true:cur_lct"></span></h1>
    <p class="secondary-text"> <?php echo lang2('lastmonth'); ?><br>
      <strong><span ng-bind-html="stats.oak | currencyFormat:cur_code:null:true:cur_lct"></span></strong> <span class="text-{{stats.montearncolor}}"><i class="{{stats.monicon}}"></i> ( <span ng-bind="stats.monmessage+'%'"></span> )</span> </p>
    <br>
  </div>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center rad-5 md-pt-15 panel-box2 last-box"> <?php echo lang2('monthlyexpense'); ?>:
    <h1 class="txt-scale-xs no-margin-top xs-28px figures"><span ng-bind-html="stats.mex | currencyFormat:cur_code:null:true:cur_lct"></span></h1>
    <p class="secondary-text"> <?php echo lang2('lastmonth'); ?><br>
      <strong><span ng-bind-html="stats.pme | currencyFormat:cur_code:null:true:cur_lct"></span></strong> <span class="text-{{stats.expensecolor}}"><i class="{{stats.expenseicon}}"></i> ( <span ng-bind="stats.expensestatus+'%'"></span> )</span> </p>
    <br>
  </div>
  <div class="ciuis-panel-summary-bg col-md-3 col-xs-6 text-center rad-5 md-pt-15 panel-box3"> <?php echo lang2('annualturnover'); ?>
    <h1 class="txt-scale-xs no-margin-top xs-28px figures"><span ng-bind-html="stats.ycr | currencyFormat:cur_code:null:true:cur_lct"></span></h1>
    <p class="secondary-text"> <?php echo lang2('lastyear'); ?> <br>
      <strong><span ng-bind-html="stats.oyc | currencyFormat:cur_code:null:true:cur_lct"></span></strong> <span class="text-{{stats.yearcolor}}"><i class="{{stats.yearicon}}"></i> ( <span ng-bind="stats.yearmessage+'%'"></span> )</span> </p>
    <br>
  </div>
</div>