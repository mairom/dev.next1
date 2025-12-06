<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div id="pageContent">
	<div class="ciuis-body-content" ng-controller="Invoices_Controller">

		<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9" id="main-content-page">
			<div class="panel-default">
				<div class="ciuis-invoice-summary">
					<div class="row" id="graphsIvoices" style="display:none; min-height:18.80em">
						<div class="col-md-12">
							<div style="border-top-left-radius: 10px;" class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('totalinvoice'); ?></div>
								<div class="box-content">
									<div class="sentTotal">{{graph.total}}</div>
								</div>

								<div class="box-foot">
									<div class="sendTime box-foot-left" style="width: 100%;"><?php echo lang2('invoiceamount'); ?><br>
										<span class="box-foot-stats">
											<strong ng-bind-html="graph.valor_total | currencyFormat:cur_code:null:true:cur_lct"></strong>
										</span>
									</div>
								</div>
							</div>

							<div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('paid'); ?></div>
								<div class="box-content invoice-percent">
									<div class="percentage">{{graph.pago_porcet}} %</div>

								</div>
								<div class="box-foot">
									<span class="arrow arrow-up"></span>
									<div class="box-foot-left" style="width: 60%;"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="graph.pago_valor | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
									<span class="arrow arrow-down"></span>
									<div class="box-foot-right" style="width: 40%;"><br><span class="box-foot-stats"><strong> {{graph.pago_total}} </strong> ( {{graph.pago_porcet}} % )</span></div>
								</div>
							</div>

							<div class=" ciuis-right-border-b1 ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('unpaidinvoice'); ?></div>
								<div class="box-content invoice-percent-2">
									<div class="percentage">{{graph.pendente_porcet}} %</div>
								</div>

								<div class="box-foot">
									<span class="arrow arrow-up"></span>
									<div class="box-foot-left" style="width: 60%;"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="graph.pendente_valor | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
									<span class="arrow arrow-down"></span>
									<div class="box-foot-right" style="width: 40%;"><br><span class="box-foot-stats"><strong> {{graph.pendente_total}} </strong> ({{graph.pendente_porcet}} %)</span></div>
								</div>
							</div>

							<div style="border-top-right-radius: 10px;" class="ciuis-invoice-summaries-b1">
								<div class="box-header text-uppercase text-bold"><?php echo lang2('overdue'); ?></div>
								<div class="box-content invoice-percent-3">
									<div class="percentage">{{graph.vencido_porcet}} %</div>
								</div>

								<div class="box-foot">
									<span class="arrow arrow-up"></span>
									<div class="box-foot-left" style="width: 60%;"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong ng-bind-html="graph.vencido_valor | currencyFormat:cur_code:null:true:cur_lct"></strong></span></div>
									<div class="box-foot-right" style="width: 40%;"><br><span class="box-foot-stats"><strong> {{graph.vencido_total}} </strong> ({{graph.vencido_porcet}} %)</span></div>
								</div>
							</div>
						</div>
					</div>

					<md-toolbar class="toolbar-white">
						<div class="md-toolbar-tools">
							<h2 flex md-truncate class="text-bold"><?php echo lang2('invoices'); ?> <small>(<span ng-bind="invoices.length"></span>)</small><br><small flex md-truncate><?php echo lang2('organizeyourinvoices'); ?></small></h2>
							<div class="ciuis-external-search-in-table">
								<input ng-model="invoice_search" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('search_by') . ' ' . lang2('customer') ?>">
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

							<md-button ng-click="openPaymentMethodsConfig()" class="md-icon-button" aria-label="Payment Methods" ng-cloak>
								<md-tooltip md-direction="bottom">Formas de Pagamento</md-tooltip>
								<md-icon><i class="ion-card text-muted"></i></md-icon>
							</md-button>

							<?php if (check_privilege('invoices', 'create')) { ?>


								<md-button style="padding: 0px 10px;font-size: 11px;" ng-href="<?php echo base_url('invoices/create') ?>" class="btn-shadow d-inline-flex align-items-center btn2 btn-success" aria-label="New" ng-cloak>
									Nova fatura
									<md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
									<md-icon style="vertical-align: unset;"><i class="ion-android-add"></i></md-icon>
								</md-button>

							<?php } ?>
						</div>

					</md-toolbar>

					<div ng-show="invoiceLoader" layout-align="center center" class="text-center" id="circular_loader">
						<!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
						<img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

						<p style="font-size: 15px;margin-bottom: 5%;">
							<span>
								<?php echo lang2('please_wait') ?> <br>
								<small><strong><?php echo lang2('loading') . ' ' . lang2('invoices') . '...' ?></strong></small>
							</span>
						</p>
					</div>

					<md-content class="md-padding filtros-invoices" ng-show="!invoiceLoader">
						<md-content class="widget-fullwidth ciuis-body-loading">
							<h6>Filtros</h6>
							<div class='filtrosLeads' style="min-height: 0;padding: 0;">
								<md-input-container class="md-block">
									<label>Período</label>
									<input required type=date ng-model="filtros.dt_de" ng-change="getResultados()">
								</md-input-container>

								<md-input-container class="md-block">
									<label>Á</label>
									<input required type=date ng-model="filtros.dt_ate" ng-change="getResultados()">
								</md-input-container>

								<md-input-container class="md-block">
									<md-select required placeholder="<?php echo lang2('choisecustomer'); ?>" ng-change="getResultados()" ng-model="filtros.flt_cliente" name="customer" style="min-width: 200px;" data-md-container-class="selectdemoSelectHeader">
										<md-select-header class="demo-select-header">
											<label style="display: none;"><?php echo lang2('search') . ' ' . lang2('customer') ?></label>
											<input ng-submit="search_customers(search_input)" ng-model="search_input" type="text" placeholder="<?php echo lang2('search') . ' ' . lang2('customers') ?>" class="demo-header-searchbox md-text" ng-keyup="search_customers(search_input)">
										</md-select-header>
										<md-optgroup label="customers">
											<md-option ng-value="-1">
												<span>Todos</span><br>
											</md-option>

											<md-option ng-value="customer.id" ng-repeat="customer in all_customers">
												<span class="blur" ng-bind="customer.customer_number"></span>
												<span ng-bind="customer.name"></span><br>
												<span class="blur">(<small ng-bind="customer.email"></small>)</span>
											</md-option>
										</md-optgroup>
									</md-select>
								</md-input-container>

							</div>
						</md-content>
					</md-content>

					<md-content ng-show="!invoiceLoader" class="bg-white" ng-cloak id="main-content">

						<md-table-container ng-show="invoices.length > 0">
							<table md-table md-progress="promise">
								<thead md-head md-order="invoice_list.order">
									<tr md-row>
										<th md-column><span><?php echo lang2('customer'); ?></span></th>
										<th md-column><span>Cpf/Cnpj</span></th>
										<th md-column><span><?php echo lang2('invoice'); ?></span></th>
										<th md-column md-order-by="created"><span><?php echo lang2('billeddate'); ?></span></th>
										<th md-column md-order-by="duedate"><span><?php echo lang2('invoiceduedate'); ?></span></th>
										<th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
										<th md-column md-order-by="total"><span><?php echo lang2('amount'); ?></span></th>
									</tr>
								</thead>

								<tbody md-body>
									<tr class="select_row" md-row ng-repeat="invoice in invoices | orderBy: invoice_list.order | limitTo: invoice_list.limit : (invoice_list.page -1) * invoice_list.limit | filter: invoice_search | filter: FilteredData" class="cursor" ng-click="goToLink('invoices/invoice/'+invoice.id, true)">
										<td md-cell>

											<small ng-bind="invoice.customer"></small>
										</td>
										<td md-cell>
											<strong><span class="badge" ng-bind="invoice.cpf"></span></strong>
										</td>
										<td md-cell>
											<strong>
												<a target="_blank" class="link" ng-href="<?php echo base_url('invoices/invoice/') ?>{{invoice.id}}"> <span ng-bind="invoice.longid"></span></a>
											</strong>
										</td>
										<td md-cell>
											<strong><span class="badge" ng-bind="invoice.created"></span></strong>
										</td>

										<td md-cell>
											<strong><span class="badge" ng-bind="invoice.duedate"></span></strong>
										</td>

										<td md-cell>
											<strong class="text-uppercase text-{{invoice.color}}" ng-bind="invoice.status"></strong>
										</td>

										<td md-cell>
											<strong ng-bind-html="invoice.total | currencyFormat:cur_code:null:true:cur_lct"></strong>
										</td>
									</tr>
								</tbody>
							</table>

						</md-table-container>
						<md-table-pagination ng-show="invoices.length > 0" md-limit="invoice_list.limit" md-limit-options="limitOptions" md-page="invoice_list.page" md-total="{{invoices.length}}"></md-table-pagination>
						<md-content ng-show="!invoices.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>
					</md-content>

				</div>

				<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="ContentFilter" ng-cloak style="width: 450px;">

					<md-toolbar class="md-theme-light" style="background:#262626">

						<div class="md-toolbar-tools">

							<md-button ng-click="close()" class="md-icon-button" aria-label="Close">

								<i class="ion-android-arrow-forward"></i>

							</md-button>

							<md-truncate><?php echo lang2('filter') ?></md-truncate>

						</div>

					</md-toolbar>

					<md-content layout-padding="">

						<div ng-repeat="(prop, ignoredValue) in invoices[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop != 'prefix' && prop != 'longid' && prop != 'created' && prop != 'duedate' && prop != 'customer' && prop != 'total' && prop != 'status' && prop != 'color' && prop != 'customer_id' && prop != 'staff_id' && prop != 'recurring_status'">

							<div class="filter col-md-12">

								<h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>

								<hr>

								<div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-if="prop!='<?php echo lang2('filterbycustomer') ?>'">

									<md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>

								</div>

								<div ng-if="prop=='<?php echo lang2('filterbycustomer') ?>'">

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

		<!-- Modal de Formas de Pagamento -->
		<div class="modal fade" id="modalPagamento">
			<div class="modal-dialog mdAngular" style="margin: 0 auto;">
				<div class="modal-content">
					<div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
						<button type="button" class="close" ng-click="closePaymentModal()">
							<span aria-hidden="true">×</span> <span class="sr-only">Fechar</span>
						</button>
						<b>Gerenciar Formas de Pagamento</b>
					</div>
					<div id="modalBody" class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<md-button ng-click="addPagamento()" class="md-raised md-primary" style="margin-bottom: 15px;">
									<i class="ion-plus"></i> Adicionar Nova Forma
								</md-button>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Forma de Pagamento</th>
											<th style="width: 100px;">Ações</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="forma in formas_pagamento track by forma.id_forma">
											<td>{{forma.nm_forma}}</td>
											<td>
												<md-button ng-click="editPagamento(forma)" class="md-icon-button" aria-label="Edit">
													<md-icon><i class="ion-edit"></i></md-icon>
												</md-button>
												<md-button ng-click="dellPagamentos(forma.id_forma)" class="md-icon-button" aria-label="Delete">
													<md-icon><i class="ion-trash-a"></i></md-icon>
												</md-button>
											</td>
										</tr>
										<tr ng-if="!formas_pagamento || formas_pagamento.length == 0">
											<td colspan="2" class="text-center">Nenhuma forma de pagamento cadastrada</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" ng-click="closePaymentModal()">Fechar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal de Adicionar/Editar Forma de Pagamento -->
		<div class="modal fade" id="modalAddPagamento">
			<div class="modal-dialog" style="margin: 0 auto;">
				<div class="modal-content">
					<div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
						<button type="button" class="close" ng-click="closeAddPaymentModal()">
							<span aria-hidden="true">×</span> <span class="sr-only">Fechar</span>
						</button>
						<b>{{pagamentoModal.id_forma ? 'Editar' : 'Adicionar'}} Forma de Pagamento</b>
					</div>
					<div id="modalBody" class="modal-body">
						<div layout="row">
							<md-input-container flex="100" style='margin-top: 8px;'>
								<label style="margin-bottom: 0;">Forma de Pagamento</label>
								<input ng-model="pagamentoModal.nm_forma" type="text" required>
							</md-input-container>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" ng-click="closeAddPaymentModal()">Cancelar</button>
						<button type="button" class="btn btn-success" ng-click="salvarPagamentoList(pagamentoModal.id_forma)">Salvar</button>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

<script type="text/javascript">
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
	dataGraph.vencido_porcet = <?= $vgy ?>;

	dataGraph.total = <?= $tfa ?>;

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
						endPercent: parseFloat('0.' + dataGraph.pago_porcet),
						fps: 60
					});

					$('.invoice-percent-2').percentCircle({
						width: 130,
						trackColor: '#ececec',
						barColor: '#ee7a6b',
						barWeight: 3,
						endPercent: parseFloat('0.' + dataGraph.pendente_porcet),
						fps: 60
					});

					$('.invoice-percent-3').percentCircle({
						width: 130,
						trackColor: '#ececec',
						barColor: '#808281',
						barWeight: 3,
						endPercent: parseFloat('0.' + dataGraph.vencido_porcet),
						fps: 60
					});

				}

			}

			function reportCircleGraph() {
				$.fn.percentCircle = function pie(options) {
					this.find('canvas').remove();
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
					//$('.canvGraph').remove();
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
		console.log(dataGraphV)
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

</div>
</div>
<ciuis-sidebar></ciuis-sidebar>
<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/js/invoices.js?v=1.2.2'); ?>"></script>