<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<style type="text/css">
  .progress-bar {
    background-color: rgb(34, 194, 129) !important;
  }
</style>
<div class="ciuis-body-content" ng-controller="Expenses_Controller">
  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
    <div class="col-md-9" style="padding: 0px">
      <div class="panel-default">
        <div class="ciuis-invoice-summary">
          <div>
            <div class="row">
              <div class="col-md-12">
                <div style="border-top-left-radius: 10px;" class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">
                  <div class="box-header text-uppercase text-bold"><?php echo lang2('total') . ' ' . lang2('expenses'); ?></div>
                  <div class="box-content">
                    <div class="sentTotal">{{dataGraph.expenses_num}}</div>
                  </div>
                  <div class="box-foot">
                    <div class="sendTime box-foot-left"><?php echo lang2('amount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.expensesAmount | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
                  </div>
                </div>
                <div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">
                  <div class="box-header text-uppercase text-bold"><?php echo lang2('paid'); ?></div>
                  <div class="box-content invoice-percent">
                    <div class="percentage">{{ dataGraph.billed }}%</div>
                  </div>
                  <div class="box-foot">
                    <span class="arrow arrow-up"></span>
                    <div class="box-foot-left"><?php echo lang2('amount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.billed_expenses | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
                    <span class="arrow arrow-down"></span>
                    <div class="box-foot-right"><br><span class="box-foot-stats"><strong>{{ dataGraph.billed_expenses_num }}</strong> ({{ dataGraph.billed }}%)</span></div>
                  </div>
                </div>
                <div class=" ciuis-right-border-b1 ciuis-invoice-summaries-b1">
                  <div class="box-header text-uppercase text-bold"><?php echo lang2('unpaidinvoice'); ?></div>
                  <div class="box-content invoice-percent-2" style="color:#ffbc00">
                    <div class="percentage">{{ dataGraph.not_billed }}%</div>
                  </div>
                  <div class="box-foot">
                    <span class="arrow arrow-up"></span>
                    <div class="box-foot-left"><?php echo lang2('amount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.not_billed_expenses | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
                    <span class="arrow arrow-down"></span>
                    <div class="box-foot-right"><br><span class="box-foot-stats"><strong>{{ dataGraph.not_billed_expenses_num }}</strong> (%{{ dataGraph.not_billed }})</span></div>
                  </div>
                </div>
                <div style="border-top-right-radius: 10px;" class="ciuis-invoice-summaries-b1">
                  <div class="box-header text-uppercase text-bold"><?php echo lang2('overdue'); ?></div>
                  <div class="box-content invoice-percent-3" style="color:#ee7a6b">
                    <div class="percentage">{{ dataGraph.overdue }}%</div>
                  </div>
                  <div class="box-foot">
                    <span class="arrow arrow-up"></span>
                    <div class="box-foot-left"><?php echo lang2('amount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.overdue_expenses | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
                    <div class="box-foot-right"><br><span class="box-foot-stats"><strong>{{ dataGraph.overdue_expenses_num }}</strong> ({{ dataGraph.overdue }}%)</span></div>
                  </div>
                </div>


              </div>
            </div>
            <div id="main-content-page">
              <md-toolbar class="toolbar-white btn_expenses">
                <div class="md-toolbar-tools">
                  <h2 flex md-truncate class="text-bold"><?php echo lang2('expenses'); ?> <small>(<span ng-bind="expenses.length"></span>)</small><br>
                    <small flex md-truncate><?php echo lang2('organizeyourexpenses'); ?></small>
                  </h2>
                  <div class="ciuis-external-search-in-table">
                    <input ng-model="expense_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>">
                    <md-button class="md-icon-button" aria-label="Search" ng-cloak>
                      <md-icon><i class="ion-search text-muted"></i></md-icon>
                    </md-button>
                  </div>
                  <md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>
                    <md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>
                    <md-icon><i class="ion-android-funnel text-muted"></i></md-icon>
                  </md-button>
                  <md-button ng-click="geraPdf()" class="md-icon-button" aria-label="Filter" ng-cloak>
                    <md-tooltip md-direction="bottom">PDF</md-tooltip>
                    <md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon>
                  </md-button>
                  <?php if (check_privilege('expenses', 'create') == true) { ?>
                    <md-button style="padding: 0px 10px;font-size: 11px;" ng-href="<?php echo base_url('expenses/create') ?>" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" aria-label="New" ng-cloak>
                      Nova Despesa
                      <md-tooltip md-direction="bottom"><?php echo lang2('newexpense') ?></md-tooltip>
                      <md-icon style="vertical-align: unset;"><i class="ion-android-add"></i></md-icon>
                    </md-button>
                  <?php } ?>
                </div>
              </md-toolbar>
              <md-content style="padding-top: 0px;">
                <div ng-show="expensesLoader" layout-align="center center" class="text-center" id="circular_loader">
                  <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                  <p style="font-size: 15px;margin-bottom: 5%;">
                    <span><?php echo lang2('please_wait') ?> <br>
                      <small><strong><?php echo lang2('loading') . ' ' . lang2('expenses') . '...' ?></strong></small></span>
                  </p>
                </div>
                <md-content ng-show="!expensesLoader" class="bg-white" ng-cloak>
                  <div class='filtrosLeads' style = "padding: 10px !important;">
                    <md-input-container class="md-block">
                      <label>Periodo de</label>
                      <input type="date" ng-model="filtros.data_de" ng-change="filtrarDespesas()">
                    </md-input-container>
                    <md-input-container class="md-block">
                      <label>Á</label>
                      <input type="date" ng-model="filtros.data_ate" ng-change="filtrarDespesas()">
                    </md-input-container>

                  </div>

                  <md-table-container ng-show="expenses.length > 0" style = "width: 100%;float: left;">
                    <table md-table md-progress="promise">
                      <thead md-head md-order="expense_list.order">
                        <tr md-row>
                          <th md-column><span><?php echo lang2('expense'); ?></span></th>
                          <th md-column md-order-by="date"><span><?php echo lang2('date'); ?></span></th>
                          <th md-column md-order-by="duedate"><span><?php echo lang2('duedate'); ?></span></th>
                          <th md-column md-order-by="category"><span><?php echo lang2('category'); ?></span></th>
                          <th md-column md-order-by="billstatus"><span><?php echo lang2('status'); ?></span></th>
                          <th md-column md-order-by="amount"><span><?php echo lang2('amount'); ?></span></th>
                        </tr>
                      </thead>
                      <tbody md-body>

                        <tr class="select_row" md-row ng-repeat="expense in expenses" class="cursor" ng-click="goToLink('expenses/receipt/'+expense.id, true)">
                          <td md-cell>
                            <strong>
                              <a class="link" target="_blank" ng-href="<?php echo base_url('expenses/receipt/') ?>{{expense.id}}"> <span ng-bind="expense.longid"></span></a>
                            </strong><br>
                            <small ng-bind="expense.title"></small>
                          </td>
                          <td md-cell>
                            <strong><span class="badge" ng-bind="expense.date"></span></strong>
                          </td>
                          <td md-cell>
                            <strong><span class="badge" ng-bind="expense.duedate"></span></strong>
                          </td>
                          <td md-cell>
                            <strong><span class="badge" ng-bind="expense.category"></span></strong>
                          </td>
                          <td md-cell>
                            <span class="label label-{{expense.color}}" ng-bind="expense.billstatus"></span>
                          </td>
                          <td md-cell>
                            <strong ng-bind-html="expense.amount | currencyFormat:cur_code:null:true:cur_lct"></strong>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </md-table-container>
                  <md-table-pagination ng-show="expenses.length > 0" md-limit="expense_list.limit" md-limit-options="limitOptions" md-page="expense_list.page" md-total="{{expenses.length}}"></md-table-pagination>
                  <md-content ng-show="!expenses.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>
                </md-content>
              </md-content>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3" style="padding: 0px;padding-left: 10px;">
      <md-toolbar class="toolbar-white">
        <div class="md-toolbar-tools">
          <h2 flex md-truncate class="text-bold"><?php echo lang2('expensescategories'); ?><br>
            <small flex md-truncate><?php echo lang2('expensescategoriessub'); ?></small>
          </h2>
          <?php if (check_privilege('expenses', 'create') == true) { ?>
            <md-button ng-click="NewCategory()" class="md-icon-button" aria-label="New Category" ng-cloak>
              <md-icon><i class="ion-plus-round text-muted"></i></md-icon>
            </md-button>
          <?php } ?>
        </div>
      </md-toolbar>
      <div ng-show="expensesCatLoader" layout-align="center center" class="text-center" id="circular_loader">
        <md-progress-circular md-mode="indeterminate" md-diameter="25"></md-progress-circular>
        <p style="font-size: 15px;margin-bottom: 5%;">
          <span><?php echo lang2('please_wait') ?> <br>
            <small><strong><?php echo lang2('loading') . ' ' . lang2('expensescategories') . '...' ?></strong></small></span>
        </p>
      </div>
      <md-content ng-show="!expensesCatLoader" ng-repeat="category in categories">
        <div class="widget widget-stats widget-expenses  red-bg margin-top-0">
          <?php if (check_privilege('expenses', 'edit')) { ?>
            <md-button ng-click="Remove($index)" class="md-icon-button pull-right" aria-label="Remove" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('remove') ?></md-tooltip>
              <md-icon><i class="ion-trash-b text-muted"></i></md-icon>
            </md-button>
          <?php }
          if (check_privilege('expenses', 'delete')) { ?>
            <md-button ng-click="UpdateCategory($index)" class="md-icon-button pull-right" aria-label="Update" ng-cloak>
              <md-tooltip md-direction="bottom"><?php echo lang2('update') ?></md-tooltip>
              <md-icon><i class="ion-gear-a text-muted"></i></md-icon>
            </md-button>
          <?php } ?>
          <div class="stats-title text-uppercase" ng-bind="category.name"></div>
          <div class="stats-number"><span ng-bind-html="category.amountby | currencyFormat:cur_code:null:true:cur_lct"></span></div>
          <div class="stats-progress progress" ng-cloak>
            <div style="width: {{category.percent}}%;" class="progress-bar"></div>
          </div>
          <div class="stats-desc" ng-cloak><?php echo lang2('categorypercent') ?> (<span ng-bind="category.percent+'%'"></span>)</div>
        </div>
      </md-content>
    </div>
    <!-- <ciuis-sidebar ng-show="expensesLoader"></ciuis-sidebar> -->
    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">
      <md-toolbar class="md-theme-light" style="background:#262626">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
          <md-truncate><?php echo lang2('filter') ?></md-truncate>
        </div>
      </md-toolbar>
      <md-content layout-padding="" ng-cloak>
        <div ng-repeat="(prop, ignoredValue) in expenses[indexExpense]" ng-init="filter[prop]={}" ng-if="prop != 'created' && prop != 'category' && prop != 'id' && prop != 'date2' && prop != 'title' && prop != 'prefix' && prop != 'longid' && prop != 'amount' && prop != 'staff' && prop != 'color' && prop != 'displayinvoice' && prop != 'date' && prop != 'billstatus' && prop != 'billable'">
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


    <div class="modal fade" id="modalCategoria" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel" ng-bind="newCatg.id != null ? 'Editar categoria' : 'Nova categoria' "></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <md-input-container class="md-block">
              <label>Categoria</label>
              <input type="text" ng-model="newCatg.name">
            </md-input-container>

            <md-input-container class="md-block">
              <label>Com fornecedor</label>
              <md-select ng-model="newCatg.com_fornecedor">
                <md-option selected ng-value="1">Sim</md-option>
                <md-option ng-value="0">Não</md-option>
              </md-select>
            </md-input-container>

            <md-input-container class="md-block" ng-if="newCatg.com_fornecedor == 1">
              <label>Fornecedor</label>
              <md-select ng-model="newCatg.id_vendor">
                <md-option ng-value="vendor.id" ng-repeat="vendor in all_vendors">{{vendor.name}}</md-option>
              </md-select>
            </md-input-container>


          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary" ng-click="salvaCategory()">Salvar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  var listInvoice = 1;
  var dataGraph = [];

  dataGraph.expensesAmount = '<?= amount_format($expensesAmount, true) ?>';
  dataGraph.expenses_num = <?= $expenses_num ?>

  dataGraph.billed = <?= $billed ?>;
  dataGraph.billed_expenses = '<?= amount_format($billed_expenses, true) ?>';
  dataGraph.billed_expenses_num = <?= $billed_expenses_num ?>;

  dataGraph.not_billed = <?= $not_billed ?>;
  dataGraph.not_billed_expenses = '<?= amount_format($not_billed_expenses, true) ?>';
  dataGraph.not_billed_expenses_num = <?= $not_billed_expenses_num ?>;

  dataGraph.overdue = <?= $overdue ?>;
  dataGraph.overdue_expenses = '<?= amount_format($overdue_expenses, true) ?>';
  dataGraph.overdue_expenses_num = <?= $overdue_expenses_num ?>;
</script>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/expenses.js?v=1.2.10') ?>"></script>

<script type="text/javascript">
  (function umd(root, name, factory) {
      'use strict';
      if ('function' === typeof define && define.amd) {
        define(name, ['jquery'], factory);
      } else {
        root[name] = factory();
      }
    }

    (this, 'CiuisInvoiceStats', function UMDFactory() {
      'use strict';
      var ReportOverview = ReportOverviewConstructor;
      reportCircleGraph();
      return ReportOverview;

      function ReportOverviewConstructor(options) {
        var factory = {
            init: init
          },
          _elements = {
            $element: options.element
          };
        init();
        return factory;

        function init() {

          $('.invoice-percent').percentCircle({
            width: 130,
            trackColor: '#ececec',
            barColor: '#22c39e',
            barWeight: 3,
            endPercent: parseFloat(dataGraph.billed < 100 ? '0.' + dataGraph.billed : dataGraph.billed),
            fps: 60
          });

          $('.invoice-percent-2').percentCircle({
            width: 130,
            trackColor: '#ececec',
            barColor: '#ffbc00',
            barWeight: 3,
            endPercent: parseFloat(dataGraph.not_billed < 100 ? '0.' + dataGraph.not_billed : dataGraph.not_billed),
            fps: 60
          });

          $('.invoice-percent-3').percentCircle({
            width: 130,
            trackColor: '#ececec',
            barColor: '#ee7a6b',
            barWeight: 3,
            endPercent: parseFloat(dataGraph.overdue < 100 ? '0.' + dataGraph.overdue : dataGraph.overdue),
            fps: 60
          });
        }

      }

      function reportCircleGraph() {
        $.fn.percentCircle = function pie(options) {
          var settings = $.extend({
            width: 130,
            trackColor: '#fff',
            barColor: '#fff',
            barWeight: 3,
            startPercent: 0,
            endPercent: 1,
            fps: 60
          }, options);
          this.find('canvas').remove();
          this.css({
            width: settings.width,
            height: settings.width
          });
          var _this = this,
            canvasWidth = settings.width,
            canvasHeight = canvasWidth,
            id = $('canvas').length,
            canvasElement = $('<canvas id="' + id + '" width="' + canvasWidth + '" height="' + canvasHeight + '"></canvas>'),
            canvas = canvasElement.get(0).getContext('2d'),
            centerX = canvasWidth / 2,
            centerY = canvasHeight / 2,
            radius = settings.width / 2 - settings.barWeight / 2,
            counterClockwise = false,
            fps = 500 / settings.fps,
            update = 0.01;
          this.angle = settings.startPercent;
          this.drawInnerArc = function(startAngle, percentFilled, color) {
            var drawingArc = true;
            canvas.beginPath();
            canvas.arc(centerX, centerY, radius, (Math.PI / 180) * (startAngle * 360 - 90), (Math.PI / 180) * (percentFilled * 360 - 90), counterClockwise);
            canvas.strokeStyle = color;
            canvas.lineWidth = settings.barWeight - 2;
            canvas.stroke();
            drawingArc = false;
          };
          this.drawOuterArc = function(startAngle, percentFilled, color) {
            var drawingArc = true;
            canvas.beginPath();
            canvas.arc(centerX, centerY, radius, (Math.PI / 180) * (startAngle * 360 - 90), (Math.PI / 180) * (percentFilled * 360 - 90), counterClockwise);
            canvas.strokeStyle = color;
            canvas.lineWidth = settings.barWeight;
            canvas.lineCap = 'round';
            canvas.stroke();
            drawingArc = false;
          };
          this.fillChart = function(stop) {
            var loop = setInterval(function() {
              canvas.clearRect(0, 0, canvasWidth, canvasHeight);
              _this.drawInnerArc(0, 360, settings.trackColor);
              _this.drawOuterArc(settings.startPercent, _this.angle, settings.barColor);
              _this.angle += update;
              if (_this.angle > stop) {
                clearInterval(loop);
              }
            }, fps);
          };
          this.fillChart(settings.endPercent);
          this.append(canvasElement);
          return this;
        };
      }


    }));

  function initGraph(dataGraphV) {
    dataGraph = dataGraphV;
    (function activateCiuisInvoiceStats($) {
      'use strict';
      var $el = $('.ciuis-invoice-summary');
      return new CiuisInvoiceStats({
        element: $el,
        data: {
          totalinvoicesayisi: dataGraph.expenses_num,
        }
      });
    }(jQuery));
  }
</script>