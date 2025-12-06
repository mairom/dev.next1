<?php include_once( APPPATH . 'Views/inc/ciuis_data_table_header.php' ); ?>
<?php $appconfig = get_appconfig(); ?>
<div id="pageContent">
	<style type="text/css">
		.progress-bar {
		  background-color: rgb(34, 194, 129) !important;
		}
	</style>
	<div class="ciuis-body-content" ng-controller="Deposits_Controller">
	  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
	    <div class="col-md-9" style="padding: 0px">
	      <div class="panel-default">
	        <div class="ciuis-invoice-summary"></div>
	      </div>
	      <md-toolbar class="toolbar-white">
	        <div class="md-toolbar-tools">
	          <h2 flex md-truncate class="text-bold"><?php echo lang2('depositstitle'); ?> <small>(<span ng-bind="deposits.length"></span>)</small><br>
	            <small flex md-truncate><?php echo lang2('organizeyourdeposits'); ?></small></h2>
	          <div class="ciuis-external-search-in-table">
	            <input ng-model="deposit_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword')?>">
	            <md-button class="md-icon-button" aria-label="Search" ng-cloak>
	              <md-icon><i class="ion-search text-muted"></i></md-icon>
	            </md-button>
	          </div>
	          <md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>
	            <md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>
	            <md-icon><i class="ion-android-funnel text-muted"></i></md-icon>
	          </md-button>
	          <?php if (check_privilege('deposits', 'create')) { ?>
	          	<md-button ng-href="<?php echo base_url('deposits/create') ?>" class="md-icon-button" aria-label="New" ng-cloak>
	          		<md-tooltip md-direction="bottom"><?php echo lang2('new').' '.lang2('deposit') ?></md-tooltip>
	          		<md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
	          	</md-button>
	          <?php } ?>
	        </div>
	      </md-toolbar>
	      <md-content style="padding-top: 0px;">
	        <div ng-show="depositsLoader" layout-align="center center" class="text-center" id="circular_loader">
	          <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
	          <p style="font-size: 15px;margin-bottom: 5%;">
	            <span><?php echo lang2('please_wait') ?> <br>
	            <small><strong><?php echo lang2('loading'). ' '. lang2('x_menu_deposits').'...' ?></strong></small></span>
	          </p>
	        </div>
			<md-content ng-show="!depositsLoader" class="bg-white" ng-cloak> 
				<md-table-container ng-show="deposits.length > 0">
					<table md-table  md-progress="promise">
						<thead md-head md-order="deposit_list.order">
							<tr md-row>
								<th md-column><span><?php echo lang2('deposit'); ?></span></th>
								<th md-column md-order-by="date"><span><?php echo lang2('date'); ?></span></th>
								<th md-column md-order-by="category"><span><?php echo lang2('category'); ?></span></th>
								<th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
								<th md-column md-order-by="amount"><span><?php echo lang2('amount'); ?></span></th>
							</tr>
						</thead>
						<tbody md-body>
							<tr class="select_row" md-row ng-repeat="deposit in deposits | orderBy: deposit_list.order | limitTo: deposit_list.limit : (deposit_list.page -1) * deposit_list.limit | filter: deposit_search | filter: FilteredData" class="cursor" ng-click="goToLink('deposits/deposit/'+deposit.id)">
								<td md-cell>
									<strong>
										<a class="link" ng-href="<?php echo base_url('deposits/deposit/') ?>{{deposit.id}}"> <span ng-bind="deposit.longid"></span></a>
									</strong><br>
									<small ng-bind="deposit.title"></small>
								</td>
								<td md-cell>
									<strong><span class="badge" ng-bind="deposit.date"></span></strong>
								</td>
								<td md-cell>
									<strong><span ng-bind="deposit.category"></span></strong>
								</td>
								<td md-cell>
								<span class="label label-{{deposit.color}} text-uppercase" ng-bind="deposit.billstatus"></span>
								</td>
								<td md-cell>
									<strong ng-bind-html="deposit.amount | currencyFormat:cur_code:null:true:cur_lct"></strong>
								</td>
							</tr>
						</tbody>
					</table>
				</md-table-container>
				<md-table-pagination ng-show="deposits.length > 0" md-limit="deposit_list.limit" md-limit-options="limitOptions" md-page="deposit_list.page" md-total="{{deposits.length}}" ></md-table-pagination>
				<md-content ng-show="!deposits.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>	
			</md-content>
	      </md-content>
	    </div>
	    <div class="col-md-3" style="padding: 0px;padding-left: 10px;">
				<md-toolbar class="toolbar-white">
					<div class="md-toolbar-tools">
						<h2 flex md-truncate class="text-bold"><?php echo lang2('categories'); ?><br><small flex md-truncate><?php echo lang2('manage').' '.lang2('depositstitle').' '.lang2('categories'); ?></small></h2>
						<?php if (check_privilege('deposits', 'create')) { ?>
							<md-button ng-click="NewCategory()" class="md-icon-button" aria-label="Filter" ng-cloak>
								<md-icon><i class="ion-plus-round text-muted"></i></md-icon>
							</md-button>
						<?php }?>
					</div>
				</md-toolbar>
				<div ng-show="depostisCatLoader" layout-align="center center" class="text-center" id="circular_loader">
				    <md-progress-circular md-mode="indeterminate" md-diameter="25"></md-progress-circular>
				    <p style="font-size: 15px;margin-bottom: 5%;">
				      <span><?php echo lang2('please_wait') ?> <br>
				      <small><strong><?php echo lang2('loading'). ' '. lang2('expensescategories').'...' ?></strong></small></span>
				    </p>
				</div>
				<md-content ng-show="!depostisCatLoader" ng-repeat="category in categories">
					<div class="widget widget-stats red-bg margin-top-0" ng-cloak>
						<?php if (check_privilege('deposits', 'delete')) { ?>
							<md-button ng-click="Remove($index)" class="md-icon-button pull-right" aria-label="Remove" ng-cloak>
								<md-tooltip md-direction="bottom"><?php echo lang2('remove') ?></md-tooltip>
								<md-icon><i class="ion-trash-b text-muted"></i></md-icon>
							</md-button>
						<?php } ?>
						<?php if (check_privilege('deposits', 'edit')) { ?>
							<md-button ng-click="UpdateCategory($index)" class="md-icon-button pull-right" aria-label="Update" ng-cloak>
								<md-tooltip md-direction="bottom"><?php echo lang2('update') ?></md-tooltip>
								<md-icon><i class="ion-gear-a text-muted"></i></md-icon>
							</md-button>
						<?php } ?>
					  <div class="stats-title text-uppercase" ng-bind="category.name" ng-cloak></div>
					  <div class="stats-number"><span ng-bind-html="category.amountby | currencyFormat:cur_code:null:true:cur_lct" ng-cloak></span></div>
					  <div class="stats-progress progress" ng-cloak>
						<div style="width: {{category.percent}}%;" class="progress-bar" ng-cloak></div>
					  </div>
					  <div class="stats-desc" ng-cloak><?php echo lang2('categorypercent') ?> (<span ng-bind="category.percent+'%'"></span>)</div>
					</div>
				</md-content>
			</div>
	  	</div>
	  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter"  ng-cloak style="width: 450px;">
	    <md-toolbar class="md-theme-light" style="background:#262626">
	      <div class="md-toolbar-tools">
	        <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward" ng-cloak></i> </md-button>
	        <md-truncate><?php echo lang2('filter') ?></md-truncate>
	      </div>
	    </md-toolbar>
	    <md-content layout-padding="">
	      <div ng-repeat="(prop, ignoredValue) in deposits[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop != 'title' && prop != 'prefix' && prop != 'longid' && prop != 'amount' && prop != 'staff' && prop != 'color' && prop != 'displayinvoice' && prop != 'date' && prop != 'category' && prop != 'billstatus'" ng-cloak>
	        <div class="filter col-md-12">
	          <h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>
	          <hr>
	          <div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-cloak>
	            <md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>
	          </div>
	        </div>
	      </div>
	    </md-content>
	  </md-sidenav>
	</div>
	<?php include_once( APPPATH . 'Views/inc/other_footer.php' ); ?>
	<script type="text/javascript" src="<?php echo base_url('assets/js/deposits.js') ?>"></script>
	<script type="text/javascript">
		(function umd(root, name, factory)
		{
			'use strict';
			if ('function' === typeof define && define.amd) {
				define(name, ['jquery'], factory);
			} else {
				root[name] = factory();
			}
		}
		(this, 'CiuisInvoiceStats', function UMDFactory()
		{
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
					_elements.$element.append($(getTemplateString()));

					$('.invoice-percent').percentCircle({
						width: 130,
						trackColor: '#ececec',
						barColor: '#22c39e',
						barWeight: 3,
						endPercent: 0.<?php echo $ofx ?>,
						fps: 60
					});
					$('.invoice-percent-2').percentCircle({
						width: 130,
						trackColor: '#ececec',
						barColor: '#ee7a6b',
						barWeight: 3,
						endPercent: 0.<?php echo $ofy ?>,
						fps: 60
					});

					$('.invoice-percent-3').percentCircle({
						width: 130,
						trackColor: '#ececec',
						barColor: '#22c39e',
						barWeight: 3,
						endPercent: 0.<?php echo $vgy ?>,
						fps: 60
					});
				}
				function getTemplateString()
				{
					return [
					'<div>',
					'<div class="row">',
					'<div class="col-md-12">',
					'<div style="border-top-left-radius: 10px;" class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">',
					'<div class="box-header text-uppercase text-bold"><?php echo lang2('total').' '.lang2('depositstitle'); ?></div>',
					'<div class="box-content">',
					'<div class="sentTotal">{{totalinvoicesayisi}}</div>'.replace(/{{totalinvoicesayisi}}/, options.data.totalinvoicesayisi),
					'</div>',
					'<div class="box-foot">',
					'<div class="sendTime box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo amount_format($fam, true) ?></strong></span></div>'.replace(/{{date}}/, options.data.date),
					'</div>',
					'</div>',
					'<div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">',
					'<div class="box-header text-uppercase text-bold"><?php echo lang2('paid'); ?></div>',
					'<div class="box-content invoice-percent">',
					'<div class="percentage">%<?php echo $ofx ?></div>',
					'</div>',
					'<div class="box-foot">',
					'<span class="arrow arrow-up"></span>',
					'<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo amount_format($total_paid, true) ?></strong></span></div>',
					'<span class="arrow arrow-down"></span>',
					'<div class="box-foot-right"><br><span class="box-foot-stats""><strong><?php echo $total_paid_num ?></strong> (%<?php echo $ofx ?>)</span></div>',
					'</div>',
					'</div>',
					'<div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">',
					'<div class="box-header text-uppercase text-bold"><?php echo lang2('unpaidinvoice'); ?></div>',
					'<div class="box-content invoice-percent-2">',
					'<div class="percentage">%<?php echo $ofy ?></div>',
					'</div>',
					'<div class="box-foot">',
					'<span class="arrow arrow-up"></span>',
					'<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo amount_format($total_unPaid, true) ?></strong></span></div>'.replace(/{{OdenmeyenInvoicesAmount}}/, options.data.OdenmeyenInvoicesAmount),
					'<span class="arrow arrow-down"></span>',
					'<div class="box-foot-right"><br><span class="box-foot-stats"><strong><?php echo $total_unPaid_num ?></strong> (%<?php echo $ofy ?>)</span></div>',
					'</div>',
					'</div>',
					'<div style="border-top-right-radius: 10px;" class="ciuis-invoice-summaries-b1">',
					'<div class="box-header text-uppercase text-bold"><?php echo lang2('internal'); ?></div>',
					'<div class="box-content invoice-percent-3">',
					'<div class="percentage">%<?php echo $vgy ?></div>',
					'</div>',
					'<div class="box-foot">',
					'<span class="arrow arrow-up"></span>',
					'<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo amount_format($total_internal, true) ?></strong></span></div>'.replace(/{{VadesiDolanInvoices}}/, options.data.VadesiDolanInvoices),
					'<div class="box-foot-right"><br><span class="box-foot-stats"><strong><?php echo $total_internal_num ?></strong> (%<?php echo $vgy ?>)</span></div>',
					'</div>',
					'</div>'
					].join('');
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
					this.drawInnerArc = function (startAngle, percentFilled, color) {
						var drawingArc = true;
						canvas.beginPath();
						canvas.arc(centerX, centerY, radius, (Math.PI / 180) * (startAngle * 360 - 90), (Math.PI / 180) * (percentFilled * 360 - 90), counterClockwise);
						canvas.strokeStyle = color;
						canvas.lineWidth = settings.barWeight - 2;
						canvas.stroke();
						drawingArc = false;
					};
					this.drawOuterArc = function (startAngle, percentFilled, color) {
						var drawingArc = true;
						canvas.beginPath();
						canvas.arc(centerX, centerY, radius, (Math.PI / 180) * (startAngle * 360 - 90), (Math.PI / 180) * (percentFilled * 360 - 90), counterClockwise);
						canvas.strokeStyle = color;
						canvas.lineWidth = settings.barWeight;
						canvas.lineCap = 'round';
						canvas.stroke();
						drawingArc = false;
					};
					this.fillChart = function (stop) {
						var loop = setInterval(function () {
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
			function getMockData() {
				return {
					totalinvoicesayisi: <?php echo sizeof($deposits) ?>,
				};
			}
		}));
		(function activateCiuisInvoiceStats($) {
			'use strict';
			var $el = $('.ciuis-invoice-summary');
			return new CiuisInvoiceStats({
				element: $el,
				data: {
					totalinvoicesayisi: <?php echo sizeof($deposits) ?>,
				}
			});
		}(jQuery));
	</script>
</div>
	