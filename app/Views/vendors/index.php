<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Vendors_Controller">

	<style type="text/css">
		rect.highcharts-background {

			fill: #f3f3f3;

		}
	</style>

	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button class="md-icon-button" aria-label="File">

					<md-icon><i class="ion-document text-muted"></i></md-icon>

				</md-button>

				<h2 flex md-truncate><?php echo lang2('vendors'); ?> <small>(<span ng-bind="vendors.length"></span>)</small></h2>

				<div class="ciuis-external-search-in-table">

					<input ng-model="search" x-webkit-speech='x-webkit-speech' class="search-table-external" id="searchcustomer" name="search" type="text" placeholder="<?php echo lang2('searchword') ?>">

					<md-button class="md-icon-button" aria-label="Search" ng-cloak>

						<md-icon><i class="ion-search text-muted"></i></md-icon>

					</md-button>

				</div>

				<md-button ng-click="toggleFilter()" class="md-icon-button" aria-label="Filter" ng-cloak>

					<md-tooltip md-direction="bottom"><?php echo lang2('filter') ?></md-tooltip>

					<md-icon><i class="ion-android-funnel text-muted"></i></md-icon>

				</md-button>

				<?php if (check_privilege('vendors', 'create')) { ?>

					<md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>

						<md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>

						<md-icon><i class="ion-android-add-circle text-success"></i></md-icon>

					</md-button>

				<?php } ?>

			</div>

		</md-toolbar>

		<div ng-show="vendorsLoader" layout-align="center center" class="text-center" id="circular_loader">

			<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

			<p style="font-size: 15px;margin-bottom: 5%;">

				<span>

					<?php echo lang2('please_wait') ?> <br>

					<small><strong><?php echo lang2('loading') . ' ' . lang2('vendors') . '...' ?></strong></small>

				</span>

			</p>

		</div>

		<ul ng-show="!vendorsLoader" class="custom-ciuis-list-body bg-white" style="padding: 0px;" ng-cloak>

			<md-table-container ng-show="vendors.length > 0">

				<table md-table md-progress="promise" ng-cloak>

					<thead md-head md-order="vendor_list.order">

						<tr md-row>

							<th md-column><span>#</span></th>

							<th md-column md-order-by="name"><span><?php echo lang2('vendor'); ?></span></th>

							<th md-column md-order-by="group_name"><span><?php echo lang2('group'); ?></span></th>

							<th md-column md-order-by="address"><span><?php echo lang2('address'); ?></span></th>

							<th md-column md-order-by="balance"><span><?php echo lang2('balance'); ?></span></th>

						</tr>

					</thead>

					<tbody md-body>

						<tr class="select_row" md-row ng-repeat="vendor in vendors | orderBy: vendor_list.order | limitTo: vendor_list.limit : (vendor_list.page -1) * vendor_list.limit | filter: search | filter: FilteredData" class="cursor" ng-click="goToLink('vendors/vendor/'+vendor.id)">

							<td md-cell>

								<strong>

									<a class="link" ng-href="<?php echo base_url('vendors/vendor/') ?>{{vendor.id}}"> <strong ng-bind="vendor.vendor_number"></strong></a>

								</strong>

							</td>

							<td md-cell>

								<strong>

									<strong ng-bind="vendor.name"></strong>

								</strong><br>

								<span class="blur" ng-bind="vendor.email"></span>

							</td>

							<td md-cell>

								<span class="badge" ng-bind="vendor.group_name"></span>

							</td>

							<td md-cell>

								<span>

									<small class="text-muted" ng-bind="vendor.address"></small><br>

									<strong ng-bind="vendor.phone"></strong>

								</span>

							</td>

							<td md-cell>

								<strong ng-show="vendor.balance !== 0" style="font-size: 20px;">

									<span ng-bind-html="vendor.balance | currencyFormat:cur_code:null:true:cur_lct"></span>

								</strong>

								<strong ng-show="vendor.balance === 0" style="font-size: 22px;">

									<i class="text-success ion-android-checkmark-circle"></i>

									<span class="text-success" style="font-size:10px"><?php echo lang2('nobalance') ?></span>

								</strong>

							</td>

						</tr>

					</tbody>

				</table>

			</md-table-container>

			<md-table-pagination ng-show="vendors.length > 0" md-limit="vendor_list.limit" md-limit-options="limitOptions" md-page="vendor_list.page" md-total="{{vendors.length}}"></md-table-pagination>

		</ul>

		<md-content ng-show="!vendors.length && vendorsLoader == false" class="md-padding no-item-data" ng-cloak><?php echo lang2('notdata') ?></md-content>

	</div>

	<div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 md-pl-0 lead-left-bar">

		<div class="panel-default panel-table borderten lead-manager-head">

			<md-toolbar class="toolbar-white">

				<div class="md-toolbar-tools">

					<h2 flex md-truncate class="text-bold"><?php echo lang2('vendor') . ' ' . lang2('group'); ?>

						<md-button ng-click="CreateGroup()" class="md-icon-button" aria-label="New" ng-cloak>

							<md-icon><i class="ion-gear-a text-muted"></i></md-icon>

						</md-button>

						<br>

				</div>

			</md-toolbar>

			<div class="tasks-status-stat">

				<div class="widget-chart-container" ng-cloak>

					<div class="widget-counter-group widget-counter-group-right">

						<div style="width: auto" class="pull-left"> <i style="font-size: 38px;color: #bfc2c6;margin-right: 10px" class="ion-stats-bars pull-left"></i>

							<div class="pull-right" style="text-align: left;margin-top: 10px;line-height: 10px;">

								<h4 style="padding: 0px;margin: 0px;"><b><?php echo lang2('vendors') . ' ' . lang2('noteby') . ' ' . lang2('group') ?></b>

								</h4>

								<small><?php echo lang2('vendor') . ' ' . lang2('stats') ?></small>

							</div>

						</div>

					</div>

					<div id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>

				</div>

			</div>

		</div>

	</div>

	<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button ng-click="close()" class="md-icon-button" aria-label="Close">

					<i class="ion-android-arrow-forward"></i>

				</md-button>

				<h2 flex md-truncate><?php echo lang2('create') ?></h2>

			</div>

		</md-toolbar>

		<md-content layout-padding="">

			<md-content layout-padding>

				<md-input-container class="md-block">

					<label><?php echo lang2('vendor') . ' ' . lang2('name'); ?></label>

					<md-icon md-svg-src="<?php echo base_url('assets/img/icons/company.svg') ?>"></md-icon>

					<input name="name" ng-model="vendor.name">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('vendor') . ' ' . lang2('group'); ?></label>

					<md-select placeholder="<?php echo lang2('vendor') . ' ' . lang2('group') ?>" ng-model="vendor.group_id" style="min-width: 200px;" required>

						<md-select-header>

							<md-toolbar class="toolbar-white">

								<div class="md-toolbar-tools">

									<h4 flex md-truncate><?php echo lang2('groups') ?></h4>

									<md-button class="md-icon-button" ng-click="NewGroup()" aria-label="Create New">

										<md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>

									</md-button>

								</div>

							</md-toolbar>

						</md-select-header>

						<md-option ng-value="name.id" ng-repeat="name in group">{{name.name}}</md-option>

					</md-select>

					<br />

				</md-input-container>
				<!--
				<md-input-container class="md-block">
					<label><?php echo $appconfig['tax_label'] . ' ' . lang2('taxofficeedit'); ?></label>
					<input name="taxoffice" ng-model="vendor.taxoffice">
				</md-input-container>

				<md-input-container class="md-block">
					<label><?php echo $appconfig['tax_label'] . ' ' . lang2('taxnumberedit'); ?></label>
					<input name="taxnumber" ng-model="vendor.taxnumber">
				</md-input-container>

				<md-input-container class="md-block">
					<label><?php echo lang2('vendorupdate'); ?></label>
					<input name="executive" ng-model="vendor.executive">
				</md-input-container>
				-->


				<md-input-container class="md-block">

					<label><?php echo lang2('phone'); ?></label>

					<input name="phone" class="phone" ng-model="vendor.phone">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('fax'); ?></label>

					<input name="fax" ng-model="vendor.fax">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('email'); ?></label>

					<input name="email" ng-model="vendor.email" required minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/" />

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('customerweb'); ?></label>

					<input name="web" ng-model="vendor.web">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('country'); ?></label>

					<md-select placeholder="<?php echo lang2('country'); ?>" ng-change="getStates(vendor.country_id)" ng-model="vendor.country_id" name="country_id" style="min-width: 200px;">

						<md-option ng-value="country.id" ng-repeat="country in countries">{{country.shortname}}</md-option>

					</md-select>

				</md-input-container>

				<br>

				<md-input-container class="md-block">

					<label><?php echo lang2('state'); ?></label>

					<md-select placeholder="<?php echo lang2('state'); ?>" ng-model="vendor.state_id" name="state" style="min-width: 200px;">

						<md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}</md-option>

					</md-select>

				</md-input-container>

				<br>

				<md-input-container class="md-block">

					<label><?php echo lang2('city'); ?></label>

					<input name="city" ng-model="vendor.city">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('town'); ?></label>

					<input name="town" ng-model="vendor.town">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('zipcode'); ?></label>

					<input name="zipcode" ng-model="vendor.zipcode">

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('address') ?></label>

					<textarea ng-model="vendor.address" name="address" md-maxlength="500" rows="3" md-select-on-focus></textarea>

				</md-input-container>

				<p id="vendorsError" style="color: red"></p>

				<section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

					<md-button ng-click="AddVendor()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">

						<span ng-hide="saving == true"><?php echo lang2('create'); ?></span>

						<md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

					</md-button>

					<br /><br /><br /><br />

				</section>

			</md-content>

		</md-content>

	</md-sidenav>

	<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="CreateGroup" ng-cloak style="width: 450px;">

		<md-toolbar class="toolbar-white" style="background:#262626">

			<div class="md-toolbar-tools">

				<md-button ng-click="close()" class="md-icon-button" aria-label="Close"><i class="ion-android-arrow-forward"></i></md-button>

				<md-truncate><?php echo lang2('groups') ?></md-truncate>

			</div>

		</md-toolbar>

		<md-content>

			<md-toolbar class="toolbar-white" style="background:#262626">

				<div class="md-toolbar-tools">

					<h4 class="text-bold text-muted" flex><?php echo lang2('vendor') . ' ' . lang2('groups') ?></h4>

					<?php if (check_privilege('vendors', 'create')) { ?>

						<md-button aria-label="Add Status" class="md-icon-button" ng-click="NewGroup()">

							<md-tooltip md-direction="bottom"><?php echo lang2('add') . ' ' . lang2('vendor') . ' ' . lang2('group') ?></md-tooltip>

							<md-icon><i class="ion-plus-round text-success"></i></md-icon>

						</md-button>

					<?php } ?>

				</div>

			</md-toolbar>

			<md-list-item ng-repeat="name in group" class="noright" ng-click="EditGroup(name.id,name.name, $event)" aria-label="Edit Status"> <strong ng-bind="name.name"></strong>

				<?php if (check_privilege('vendors', 'edit')) { ?>

					<md-icon class="md-secondary md-hue-3 ion-compose " aria-hidden="Edit group"></md-icon>

				<?php }
				if (check_privilege('vendors', 'delete')) { ?>

					<md-icon ng-click='DeleteVendorGroup($index)' aria-label="Remove Status" class="md-secondary md-hue-3 ion-trash-b"></md-icon>

				<?php } ?>

			</md-list-item>

		</md-content>

	</md-sidenav>

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

			<div ng-repeat="(prop, ignoredValue) in vendors[0]" ng-init="filter[prop]={}" ng-if="prop != 'id' && prop!='vendor' && prop != 'name' && prop != 'address' && prop != 'email' && prop != 'phone' && prop != 'balance' && prop != 'vendor_id' && prop != 'contacts' && prop != 'vendor_number' && prop != 'group_name'">

				<div class="filter col-md-12">

					<h4 class="text-muted text-uppercase"><strong>{{prop}}</strong></h4>

					<hr>

					<div class="labelContainer" ng-repeat="opt in getOptionsFor(prop)" ng-if="prop!='<?php echo lang2('filterbycountry') ?>' && prop!='<?php echo lang2('filterbyassigned') ?>'">

						<md-checkbox id="{{[opt]}}" ng-model="filter[prop][opt]" aria-label="{{opt}}"><span class="text-uppercase">{{opt}}</span></md-checkbox>

					</div>

					<div ng-if="prop=='<?php echo lang2('filterbycountry') ?>'">

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

<script type="text/javascript">
	var lang = {};

	lang.vendor = '<?php echo lang2('vendor') ?>';

	lang.vendor_title = "<?php echo lang2('vendor_title') ?>";

	lang.type_group = "<?php echo lang2('type_group') ?>";

	lang.delete_group = "<?php echo lang2('delete_group') ?>";

	lang.group = '<?php echo lang2('group') ?>';

	lang.new = '<?php echo lang2('new') ?>';

	lang.name = '<?php echo lang2('name') ?>';

	lang.add = '<?php echo lang2('add') ?>';

	lang.cancel = '<?php echo lang2('cancel') ?>';

	lang.save = '<?php echo lang2('save') ?>';

	lang.edit = '<?php echo lang2('edit') ?>';

	lang.doIt = '<?php echo lang2('doIt') ?>';

	lang.attention = '<?php echo lang2('attention') ?>';
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>

<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>



<script src="<?php echo base_url('assets/js/vendors.js'); ?>"></script>