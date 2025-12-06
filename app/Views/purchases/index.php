<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Purchases_Controller">
	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
		<div class="panel-default">
			<div class="ciuis-invoice-summary">

				<div>
					<div class="row">
						<div class="col-md-12">
							<div style="border-top-left-radius: 10px;" class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('total') . ' ' . lang2('purchase'); ?></div>
								<div class="box-content">
									<div class="sentTotal">{{dataGraph.total}}</div>
								</div>
								<div class="box-foot">
									<div class="sendTime box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.valor_total | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
								</div>
							</div>
							<div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('paid'); ?></div>
								<div class="box-content invoice-percent">
									<div class="percentage">{{dataGraph.pago_porcet}} %</div>
								</div>
								<div class="box-foot">
									<span class="arrow arrow-up"></span>
									<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.pago_valor | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
									<span class="arrow arrow-down"></span>
									<div class="box-foot-right"><br><span class="box-foot-stats"><strong>{{dataGraph.pago_total}}</strong> ({{dataGraph.pago_porcet}} %)</span></div>
								</div>
							</div>
							<div class=" ciuis-right-border-b1 ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('unpaidinvoice'); ?></div>
								<div class="box-content invoice-percent-2">
									<div class="percentage">{{dataGraph.pendente_porcet}} %</div>
								</div>
								<div class="box-foot">
									<span class="arrow arrow-up"></span>
									<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.pendente_valor | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
									<span class="arrow arrow-down"></span>
									<div class="box-foot-right"><br><span class="box-foot-stats"><strong>{{dataGraph.pendente_total}}</strong> ({{dataGraph.pendente_porcet}} %)</span></div>
								</div>
							</div>
							<div style="border-top-right-radius: 10px;" class="ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('overdue'); ?></div>
								<div class="box-content invoice-percent-3">
									<div class="percentage">{{dataGraph.vencido_porcet}} %</div>
								</div>
								<div class="box-foot">
									<span class="arrow arrow-up"></span>
									<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="dataGraph.vencido_valor | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
									<div class="box-foot-right"><br><span class="box-foot-stats"><strong>{{dataGraph.vencido_total}}</strong> ({{dataGraph.vencido_porcet}} %)</span></div>
								</div>
							</div>

						</div>
					</div>
					<div id="main-content-page">
						<md-toolbar class="toolbar-white filtrosLeads3">
							<div class="md-toolbar-tools">
								<h2 flex md-truncate class="text-bold"><?php echo lang2('purchases'); ?> <small>(<span ng-bind="purchases.length"></span>)</small><br><small flex md-truncate><?php echo lang2('organizeyourPurchases'); ?></small>
								</h2>
								<div class="ciuis-external-search-in-table">
									<input ng-model="search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>">
									<md-button class="md-icon-button" aria-label="Search" ng-cloak>
										<md-icon><i class="ion-search text-muted"></i></md-icon>
									</md-button>
								</div>
								<md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>
									<md-icon><i class="ion-android-funnel text-muted"></i></md-icon>
								</md-button>
								<md-button ng-click="geraPdf()" class="md-icon-button" aria-label="Filter" ng-cloak>
									<md-tooltip md-direction="bottom">PDF</md-tooltip>
									<md-icon><i style="color: #8e44ad;" class="far fa-file-pdf"></i></md-icon>
								</md-button>
								<?php if (check_privilege('purchases', 'create')) { ?>
									<md-button style="padding: 0px 10px;font-size: 11px;" ng-href="<?php echo base_url('purchases/create') ?>" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" aria-label="New" ng-cloak>
										Nova Compra
										<md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
										<md-icon style="vertical-align: unset;"><i class="ion-android-add"></i></md-icon>
									</md-button>

								<?php } ?>
							</div>
						</md-toolbar>
						<div ng-show="purchasesLoader" layout-align="center center" class="text-center" id="circular_loader">
							<!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
							<img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

							<p style="font-size: 15px;margin-bottom: 5%;">
								<span>
									<?php echo lang2('please_wait') ?> <br>
									<small><strong><?php echo lang2('loading') . ' ' . lang2('purchases') . '...' ?></strong></small>
								</span>
							</p>
						</div>
						<md-content ng-show="!purchasesLoader" class="bg-white">
							<md-table-container>
								<table md-table md-progress="promise" ng-cloak>
									<thead md-head md-order="purchase_list.order">
										<tr md-row>
											<th md-column><span><?php echo lang2('purchase'); ?></span></th>
											<th md-column md-order-by="created"><span><?php echo lang2('billeddate'); ?></span></th>
											<th md-column md-order-by="duedate"><span><?php echo lang2('invoiceduedate'); ?></span></th>
											<th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
											<th md-column md-order-by="total"><span><?php echo lang2('amount'); ?></span></th>
										</tr>
									</thead>
									<tbody md-body>
										<div class='filtrosLeads'>
											<md-input-container class="md-block">
												<label>Periodo de</label>
												<input type="date" ng-model="filtros.data_de" ng-change="filtrarCompras()">
											</md-input-container>
											<md-input-container class="md-block">
												<label>Á</label>
												<input type="date" ng-model="filtros.data_ate" ng-change="filtrarCompras()">
											</md-input-container>
											<md-input-container class="md-block">
												<label>Fornecedores</label>
												<md-select ng-model="filtros.fornecedor" ng-change="filtrarCompras()">
													<md-option ng-selected="true" ng-value="-1">Todos</md-option>
													<md-option ng-value="vendor.id" ng-repeat="vendor in all_vendors">{{vendor.name}}</md-option>
												</md-select>
											</md-input-container>
										</div>
										<tr class="select_row" md-row ng-repeat="purchase in purchases | orderBy: purchase_list.order | limitTo: purchase_list.limit : (purchase_list.page -1) * purchase_list.limit | filter: search | filter: FilteredData" class="cursor" ng-click="goToLink('purchases/purchase/'+purchase.id, true)">
											<td md-cell>
												<strong>
													<a target="_blank" class="link" ng-href="<?php echo base_url('purchases/purchase/'); ?>{{purchase.id}}"> <span ng-bind="purchase.longid"></span></a>
												</strong><br>
												<small ng-bind="purchase.customer"></small>
											</td>
											<td md-cell>
												<strong><span class="badge" ng-bind="purchase.created"></span></strong>
											</td>
											<td md-cell>
												<strong><span class="badge" ng-bind="purchase.duedate"></span></strong>
											</td>
											<td md-cell>
												<strong class="text-uppercase text-{{purchase.color}}" ng-bind="purchase.status"></strong>
											</td>
											<td md-cell>
												<strong ng-bind-html="purchase.total | currencyFormat:cur_code:null:true:cur_lct"></strong>
											</td>
										</tr>
									</tbody>
								</table>
							</md-table-container>
							<md-table-pagination ng-show="purchases.length > 0" md-limit="purchase_list.limit" md-limit-options="limitOptions" md-page="purchase_list.page" md-total="{{purchases.length}}"></md-table-pagination>
							<md-content ng-show="!purchases.length" class="md-padding no-item-data" ng-cloak><?php echo lang2('notdata') ?></md-content>
						</md-content>
					</div>
					<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">
						</md-toolbar>
						<md-toolbar class="md-theme-light" style="background:#262626">
							<div class="md-toolbar-tools">
								<md-button ng-click="close()" class="md-icon-button" aria-label="Close">
									<i class="ion-android-arrow-forward"></i>
								</md-button>
								<md-truncate><?php echo lang2('filter') ?></md-truncate>
							</div>
						</md-toolbar>
						<md-content layout-padding="">
							<div ng-repeat="(prop, ignoredValue) in purchases[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop != 'prefix' && prop != 'longid' && prop != 'created' && prop != 'duedate' && prop != 'customer' && prop != 'total' && prop != 'status' && prop != 'color' && prop != 'vendor_id' && prop != 'staff_id' && prop != 'recurring_status'">
								<div class="filter col-md-12">
									<h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>
									<hr>
									<div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-if="prop!='<?php echo lang2('filterbyvendor') ?>'">
										<md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>
									</div>
									<div ng-if="prop=='<?php echo lang2('filterbyvendor') ?>'">
										<md-select aria-label="Filter" ng-model="filter_select" ng-init="filter_select='all'" ng-change="updateDropdown(prop)">
											<md-option value="all"><?php echo lang2('all') ?></md-option>
											<md-option ng-repeat="opt in getOptionsFor(prop) | orderBy:'':true" value="{{opt}}">{{opt}}</md-option>
										</md-select>
									</div>
								</div>
							</div>
						</md-content>
					</md-sidenav>
				</div>
			</div>
			<ciuis-sidebar></ciuis-sidebar>

			<script>
				var dataGraph = [];

				dataGraph.vencido_porcet = '<?= $vgy ?>';
				dataGraph.pendente_total = <?= $tef ?>

				dataGraph.pago_porcet = <?= $ofx ?>;
				dataGraph.valor_total = '<?= amount_format($fam, true) ?>';

				dataGraph.pago_total = <?= $otf ?>;
				dataGraph.pago_valor = '<?= amount_format($ofv, true) ?>';

				dataGraph.pendente_porcet = <?= $ofy ?>;
				dataGraph.pendente_valor = '<?= amount_format($oft, true) ?>';

				dataGraph.vencido_valor = '<?= amount_format($vgf, true) ?>';
				dataGraph.vencido_total = <?= $vdf ?>;

				dataGraph.total = <?= $tfa ?>;
			</script>

			<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
			<script src="<?php echo base_url('assets/js/purchases.js?v=1.5.2'); ?>"></script>
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
								//_elements.$element.html($(getTemplateString()));
								$('.invoice-percent').percentCircle({
									width: 130,
									trackColor: '#ececec',
									barColor: '#22c39e',
									barWeight: 3,
									endPercent: parseFloat(dataGraph.pago_porcet < 100 ? '0.' + dataGraph.pago_porcet : dataGraph.pago_porcet),
									fps: 60
								});
								$('.invoice-percent-2').percentCircle({
									width: 130,
									trackColor: '#ececec',
									barColor: '#ee7a6b',
									barWeight: 3,
									endPercent: parseFloat(dataGraph.pendente_porcet < 100 ? '0.' + dataGraph.pendente_porcet : dataGraph.pendente_porcet),
									fps: 60
								});
								$('.invoice-percent-3').percentCircle({
									width: 130,
									trackColor: '#ececec',
									barColor: '#808281',
									barWeight: 3,
									endPercent: parseFloat(dataGraph.vencido_porcet < 100 ? '0.' + dataGraph.vencido_porcet : dataGraph.vencido_porcet),
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

						function getMockData() {
							return {
								totalinvoicesayisi: dataGraph.total,
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
								totalinvoicesayisi: dataGraph.total,
							}
						});
					}(jQuery));
				}
			</script>