<?php include_once(APPPATH . 'Views/area/inc/header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Invoices_Controller">
	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
		<md-toolbar class="toolbar-white">
			<div class="md-toolbar-tools" ng-cloak>
				<h2 flex md-truncate class="text-bold"><?php echo lang2('invoices'); ?><br><small flex md-truncate><?php echo lang2('organizeyourinvoices'); ?></small></h2>
			</div>
		</md-toolbar>
		<md-content ng-cloak>	
			<md-content ng-show="!invoiceLoader" class="bg-white" ng-cloak> 
				<md-table-container ng-show="invoices.length > 0">
					<table md-table  md-progress="promise">
						<thead md-head md-order="invoice_list.order">
							<tr md-row>
								<th md-column><span><?php echo lang2('invoice'); ?></span></th>
								<th md-column md-order-by="created"><span><?php echo lang2('billeddate'); ?></span></th>
								<th md-column md-order-by="duedate"><span><?php echo lang2('invoiceduedate'); ?></span></th>
								<th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
								<th md-column md-order-by="total"><span><?php echo lang2('amount'); ?></span></th>
							</tr>
						</thead>
						<tbody md-body>
							<tr class="select_row" md-row ng-repeat="invoice in invoices | orderBy: invoice_list.order | limitTo: invoice_list.limit : (invoice_list.page -1) * invoice_list.limit | filter: invoice_search | filter: FilteredData">
								<td md-cell>
									<strong>
										<a class="link" ng-href="<?php echo base_url('area/invoices/invoice/{{invoice.token}}'); ?>"> <span ng-bind="invoice.longid"></span></a>
									</strong><br>
									<small ng-bind="invoice.customer"></small>
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
				<md-table-pagination ng-show="invoices.length > 0" md-limit="invoice_list.limit" md-limit-options="limitOptions" md-page="invoice_list.page" md-total="{{invoices.length}}" ></md-table-pagination>
				<md-content ng-show="!invoices.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>	
			</md-content>
		</md-content>
	</div>
</div>
<?php include_once(APPPATH . 'Views/area/inc/sidebar.php');?>
<?php include_once(APPPATH . 'Views/area/inc/footer.php');?>