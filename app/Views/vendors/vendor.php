<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Vendor_Controller">

	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<h2 class="md-pl-10" flex md-truncate ng-bind="vendor.vendor_number+' '+vendor.name"></h2>

				<?php if (check_privilege('vendors', 'edit')) { ?>

					<md-button ng-click="Update()" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>

						<md-icon class="mdi mdi-edit"></md-icon>

					</md-button>

				<?php }
				if (check_privilege('vendors', 'delete')) { ?>

					<md-button ng-click="Delete()" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>

						<md-icon class="ion-trash-b"></md-icon>

					</md-button>

				<?php } ?>

			</div>

		</md-toolbar>

		<div ng-show="vendorsLoader" layout-align="center center" class="text-center" id="circular_loader">

			<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

			<p style="font-size: 15px;margin-bottom: 5%;">

				<span>

					<?php echo lang2('please_wait') ?> <br>

					<small><strong><?php echo lang2('loading') . ' ' . lang2('vendor') . '...' ?></strong></small>

				</span>

			</p>

		</div>

		<section ng-show="!vendorsLoader" layout="row" flex>

			<md-sidenav class="md-sidenav-left" md-component-id="left" md-is-locked-open="$mdMedia('gt-md')" style="z-index:0" ng-cloak>

				<md-subheader class="md-primary" style="background-color: white; border-bottom: 1px #e0e0e0 solid; padding-bottom: 2px; border-right: 1px #f3f3f3 solid;">

					<?php echo lang2('informations'); ?>

				</md-subheader>

				<md-content class="bg-white" style="border-right:1px solid #e0e0e0;">

					<md-list flex class="md-p-0 sm-p-0 lg-p-0">

						<md-list-item>

							<md-icon class="ion-android-call"></md-icon>

							<p ng-bind="vendor.phone"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="mdi mdi-http"></md-icon>

							<p ng-bind="vendor.web"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="ion-android-mail"></md-icon>

							<p ng-bind="vendor.email"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="ion-earth"></md-icon>

							<p ng-bind="vendor.country"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="mdi mdi-map"></md-icon>

							<p ng-bind="vendor.state_name"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="mdi mdi-city"></md-icon>

							<p ng-bind="vendor.city"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="mdi mdi-city-alt"></md-icon>

							<p ng-bind="vendor.town"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="ion-ios-home"></md-icon>

							<p ng-bind="vendor.address"></p>

						</md-list-item>

						<md-divider></md-divider>

						<md-list-item>

							<md-icon class="mdi mdi-markunread-mailbox"></md-icon>

							<p ng-bind="vendor.zipcode"></p>

						</md-list-item>

					</md-list>

				</md-content>

			</md-sidenav>

			<md-content class="bg-white information-section-hide" flex>

				<md-tabs md-dynamic-height md-border-bottom>

					<md-tab label="<?php echo lang2('summary'); ?>">

						<md-content class="md-padding bg-white">

							<div style="border-right: 1px solid rgb(234, 234, 234);" class="col-md-4 hidden-xs xs-pt-20 lg-pt-0">

								<div class='customer-42525'>

									<div class='customer-42525__inner'>

										<h2><?php echo lang2('riskstatus'); ?></h2>

										<small><?php echo lang2('customerrisksubtext'); ?></small>

										<div ng-hide="vendor.risk != '0'" class="stat">

											<span style="color:#eaeaea;"><i class="text-success mdi mdi-shield-check"></i> <?php echo lang2('norisk') ?></span>

										</div>

										<div ng-show="vendor.risk > '50'" class="stat"><span ng-bind="vendor.risk+'%'"></span></div>

										<div ng-show="vendor.risk > '50'" class="progress">
											<div style="width:{{vendor.risk}}%" class="progress-bar progress-bar-danger"></div>
										</div>

										<div ng-show="vendor.risk > '0' && vendor.risk < 50" class="stat"><span ng-bind="vendor.risk+'%'"></span></div>

										<div ng-show="vendor.risk > '0' && vendor.risk < 50" class="progress">
											<div style="width:{{vendor.risk}}%" class="progress-bar progress-bar-primary"></div>
										</div>

										<p><?php echo lang2('customerrisksubtext'); ?></p>

									</div>

								</div>

							</div>

							<div style="border-right: 1px solid rgb(234, 234, 234);" class="col-md-4 col-xs-6 xs-pt-20 lg-pt-0">

								<div class='customer-42525'>

									<div class='customer-42525__inner'>

										<h2><?php echo lang2('netrevenue'); ?></h2>

										<small><?php echo lang2('netrevenuedetail'); ?></small>

										<div class='stat'>

											<span ng-show="vendor.netrevenue" ng-bind-html="vendor.netrevenue | currencyFormat:cur_code:null:true:cur_lct"></span>

											<span class="text-success font-10" ng-show="!vendor.netrevenue"><?php echo lang2('nosalesyet') ?></span>

										</div>

										<p><?php echo lang2('netrevenuedescription'); ?></p>

									</div>

								</div>

							</div>

							<div class="col-md-4 col-xs-6 xs-pt-20 lg-pt-0">

								<div class='customer-42525'>

									<div class='customer-42525__inner'>

										<h2><?php echo lang2('grossrevenue'); ?></h2>

										<small><?php echo lang2('grossrevenuedetail'); ?></small>

										<div class='stat'>

											<span ng-show="vendor.grossrevenue" ng-bind-html="vendor.grossrevenue | currencyFormat:cur_code:null:true:cur_lct"></span>

											<span ng-show="!vendor.grossrevenue"><?php echo lang2('nosalesyet') ?></span>

										</div>

										<p><?php echo lang2('grossrevenuedescription'); ?></p>

									</div>

								</div>

							</div>

							<hr style="margin-bottom: 10px;">

						</md-content>

					</md-tab>

					<md-tab label="<?php echo lang2('purchases'); ?>">

						<md-content class="bg-white">

							<md-list flex class="md-p-0 sm-p-0 lg-p-0">

								<md-list-item ng-repeat="invoice in purchases" ng-click="GoPuchases($index)" aria-label="Invoice">

									<md-icon class="ico-ciuis-invoices"></md-icon>

									<p><strong ng-bind="invoice.longid"></strong></p>

									<h4><strong ng-bind-html="invoice.total | currencyFormat:cur_code:null:true:cur_lct"></strong></h4>

									<md-divider></md-divider>

								</md-list-item>

							</md-list>

							<md-content ng-show="!purchases.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?></md-content>

						</md-content>

					</md-tab>

					<md-tab label="<?php echo lang2('vendoractivities'); ?>">

						<md-content class="md-padding bg-white">

							<ul class="user-timeline">

								<li ng-repeat="log in logs | filter: { vendor_id: '<?php echo $vendors['id']; ?>' }">

									<div class="user-timeline-title" ng-bind="log.date"></div>

									<div class="user-timeline-description" ng-bind-html="log.detail|trustAsHtml"></div>

								</li>

							</ul>

						</md-content>

					</md-tab>

				</md-tabs>

			</md-content>

			<md-content class="bg-white information-section-show" flex>

				<md-tabs md-dynamic-height md-border-bottom>

					<md-tab label="<?php echo lang2('informations'); ?>">

						<md-content class="md-padding bg-white">

							<md-list flex class="md-p-0 sm-p-0 lg-p-0">

								<md-list-item>

									<md-icon class="ion-android-call"></md-icon>

									<p ng-bind="vendor.phone"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="mdi mdi-http"></md-icon>

									<p ng-bind="vendor.web"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="ion-android-mail"></md-icon>

									<p ng-bind="vendor.email"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="ion-earth"></md-icon>

									<p ng-bind="vendor.country"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="mdi mdi-map"></md-icon>

									<p ng-bind="vendor.state_name"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="mdi mdi-city"></md-icon>

									<p ng-bind="vendor.city"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="mdi mdi-city-alt"></md-icon>

									<p ng-bind="vendor.town"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="ion-ios-home"></md-icon>

									<p ng-bind="vendor.address"></p>

								</md-list-item>

								<md-divider></md-divider>

								<md-list-item>

									<md-icon class="mdi mdi-markunread-mailbox"></md-icon>

									<p ng-bind="vendor.zipcode"></p>

								</md-list-item>

							</md-list>

						</md-content>

					</md-tab>

					<md-tab label="<?php echo lang2('summary'); ?>">

						<md-content class="md-padding bg-white">

							<div style="border-right: 1px solid rgb(234, 234, 234);" class="col-md-4 hidden-xs xs-pt-20 lg-pt-0">

								<div class='customer-42525'>

									<div class='customer-42525__inner'>

										<h2><?php echo lang2('riskstatus'); ?></h2>

										<small><?php echo lang2('customerrisksubtext'); ?></small>

										<div ng-hide="vendor.risk != '0'" class="stat">

											<span style="color:#eaeaea;"><i class="text-success mdi mdi-shield-check"></i> <?php echo lang2('norisk') ?></span>

										</div>

										<div ng-show="vendor.risk > '50'" class="stat"><span ng-bind="vendor.risk+'%'"></span></div>

										<div ng-show="vendor.risk > '50'" class="progress">
											<div style="width:{{vendor.risk}}%" class="progress-bar progress-bar-danger"></div>
										</div>

										<div ng-show="vendor.risk > '0' && vendor.risk < 50" class="stat"><span ng-bind="vendor.risk+'%'"></span></div>

										<div ng-show="vendor.risk > '0' && vendor.risk < 50" class="progress">
											<div style="width:{{vendor.risk}}%" class="progress-bar progress-bar-primary"></div>
										</div>

										<p><?php echo lang2('customerrisksubtext'); ?></p>

									</div>

								</div>

							</div>

							<div style="border-right: 1px solid rgb(234, 234, 234);" class="col-md-4 col-xs-6 xs-pt-20 lg-pt-0">

								<div class='customer-42525'>

									<div class='customer-42525__inner'>

										<h2><?php echo lang2('netrevenue'); ?></h2>

										<small><?php echo lang2('netrevenuedetail'); ?></small>

										<div class='stat'>

											<span ng-show="vendor.netrevenue" ng-bind-html="vendor.netrevenue | currencyFormat:cur_code:null:true:cur_lct"></span>

											<span class="text-success font-10" ng-show="!vendor.netrevenue"><?php echo lang2('nosalesyet') ?></span>

										</div>

										<p><?php echo lang2('netrevenuedescription'); ?></p>

									</div>

								</div>

							</div>

							<div class="col-md-4 col-xs-6 xs-pt-20 lg-pt-0">

								<div class='customer-42525'>

									<div class='customer-42525__inner'>

										<h2><?php echo lang2('grossrevenue'); ?></h2>

										<small><?php echo lang2('grossrevenuedetail'); ?></small>

										<div class='stat'>

											<span ng-show="vendor.grossrevenue" ng-bind-html="vendor.grossrevenue | currencyFormat:cur_code:null:true:cur_lct"></span>

											<span ng-show="!vendor.grossrevenue"><?php echo lang2('nosalesyet') ?></span>

										</div>

										<p><?php echo lang2('grossrevenuedescription'); ?></p>

									</div>

								</div>

							</div>

							<hr style="margin-bottom: 10px;">



						</md-content>

					</md-tab>

					<md-tab label="<?php echo lang2('purchases'); ?>">

						<md-content class="bg-white">

							<md-list flex class="md-p-0 sm-p-0 lg-p-0">

								<md-list-item ng-repeat="invoice in purchases" ng-click="GoPuchases($index)" aria-label="Invoice">

									<md-icon class="ico-ciuis-invoices"></md-icon>

									<p><strong ng-bind="invoice.longid"></strong></p>

									<h4><strong ng-bind-html="invoice.total | currencyFormat:cur_code:null:true:cur_lct"></strong></h4>

									<md-divider></md-divider>

								</md-list-item>

							</md-list>

							<md-content ng-show="!purchases.length" class="md-padding bg-white no-item-data"><?php echo lang2('notdata') ?></md-content>

						</md-content>

					</md-tab>

					<md-tab label="<?php echo lang2('vendoractivities'); ?>">

						<md-content class="md-padding bg-white">

							<ul class="user-timeline">

								<li ng-repeat="log in logs | filter: { vendor_id: '<?php echo $vendors['id']; ?>' }">

									<div class="user-timeline-title" ng-bind="log.date"></div>

									<div class="user-timeline-description" ng-bind-html="log.detail|trustAsHtml"></div>

								</li>

							</ul>

						</md-content>

					</md-tab>

				</md-tabs>

			</md-content>

		</section>

	</div>

	<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Update" ng-cloak style="width: 450px;">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>

				<md-truncate flex><?php echo lang2('update') ?></md-truncate>

				<md-switch ng-model="vendor.vendor_status_id" aria-label="Active"><strong class="text-muted"><?php echo lang2('active') ?></strong></md-switch>

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

					<md-select placeholder="<?php echo lang2('vendor') . ' ' . lang2('group'); ?>" ng-model="vendor.group_id" style="min-width: 200px;" required>

						<md-option ng-value="name.id" ng-repeat="name in groups">{{name.name}}</md-option>

					</md-select>

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

					<md-select placeholder="<?php echo lang2('state'); ?>" ng-model="vendor.state" name="state" style="min-width: 200px;">

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

					<md-button ng-click="UpdateVendor()" class="md-raised md-primary btn-report block-button" ng-disabled="savingCustomer == true">

						<span ng-hide="savingCustomer == true"><?php echo lang2('update'); ?></span>

						<md-progress-circular class="white" ng-show="savingCustomer == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

					</md-button>

					<br /><br /><br /><br />

				</section>

			</md-content>

		</md-content>

	</md-sidenav>

</div>

<script>
	var lang = {};

	var VENDORRID = "<?php echo $vendors['id']; ?>";

	lang.doIt = '<?php echo lang2('doIt') ?>';

	lang.cancel = '<?php echo lang2('cancel') ?>';

	lang.attention = '<?php echo lang2('attention') ?>';

	lang.delete_vendor = "<?php echo lang2('delete_vendor') ?>";
</script>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script src="<?php echo base_url('assets/js/vendors.js?v=1.1.6'); ?>"></script>