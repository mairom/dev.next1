<script type="text/ng-template" id="update_payment_method.html">

	<md-dialog aria-label="Payment">

	 	<md-toolbar class="toolbar-white">

	 		<div class="md-toolbar-tools">

	 			<h2><strong class="text-success">{{gateway.name}}</strong></h2>

	 			<span flex></span>

	 			<md-switch ng-model="gateway.active" aria-label="Type">

	 				<strong class="text-muted"><?php echo lang2('active') ?></strong>

	 			</md-switch>

	 			<md-button class="md-icon-button" ng-click="close()">

	 				<md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

	 			</md-button>

	 		</div>

	 	</md-toolbar>

	 	<md-dialog-content style="max-width:800px;max-height:810px; ">

	 		<md-content class="bg-white">

	 			<md-list flex>

	 				<md-list-item ng-if="gateway.input_label1">

	 					<md-input-container class="md-block full-width">

	 						<label ng-bind="gateway.input_label1"></label>

	 						<input ng-model="gateway.input_value1">

	 					</md-input-container>

	 				</md-list-item>

	 				<md-list-item ng-if="gateway.input_label2">

	 					<md-input-container class="md-block full-width">

	 						<label ng-bind="gateway.input_label2"></label>

	 						<input ng-model="gateway.input_value2">

	 					</md-input-container>

	 				</md-list-item>

	 				<md-list-item ng-if="gateway.input_label3">

	 					<md-input-container class="md-block full-width">

	 						<label ng-bind="gateway.input_label3"></label>

	 						<input ng-model="gateway.input_value3">

	 					</md-input-container>

	 				</md-list-item>

	 				<md-list-item>

	 					<md-input-container  class="md-block full-width">

	 						<label><strong>{{gateway_relation}} <?php echo lang2('payment_record_account') ?></strong></label>

	 						<md-select ng-model="gateway.payment_record_account">

	 							<md-option ng-value="account.id" ng-repeat="account in accounts">{{account.name}}</md-option>

	 						</md-select>

	 					</md-input-container>

	 				</md-list-item>

	 				<md-list-item ng-hide="gateway.relation == 'bank'">

	 					<md-switch class="pull-left" ng-model="gateway.sandbox_account" aria-label="Status"> <strong class="text-muted"><?php echo lang2('test') . '/' . lang2('sandbox') . ' ' . lang2('account') ?></strong> </md-switch>

	 				</md-list-item>

	 				<md-list-item ng-show="gateway.gateway_note">

	 					<p class="text-danger">

	 						<strong> <?php echo lang2('note') ?>: </strong> 

	 						<span ng-bind="gateway.gateway_note"></span>

	 					</p>

	 					<!-- ion-information-circled -->

	 				</md-list-item>

	 				<br ng-show="gateway.relation == 'bank'"><br ng-show="gateway.relation == 'bank'">

	 				<md-divider>

	 				</md-divider>

	 				<br><br>

	 				<md-button ng-click="UpdatePaymentGateway(gateway.relation, gateway.id)" class="template-button" ng-disabled="saving == true">

	 					<span ng-hide="saving == true"><?php echo lang2('save'); ?></span>

	 					<md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

	 				</md-button>

	 				<md-button ng-click="close()" class="">

	 					<span><?php echo lang2('cancel'); ?></span>

	 				</md-button>

	 			</md-list>

	 		</md-content>

	 	</md-dialog-content>

	</md-dialog>

</script>

<script type="text/ng-template" id="see_smtp_password.html">

	<md-dialog>

	  <md-toolbar class="toolbar-white">

	    <div class="md-toolbar-tools">

	      <h2><strong class="text-success"><?php echo lang2('see_smtp_password') ?></strong></h2>

	      <span flex></span>

	      <md-button class="md-icon-button" ng-click="close()">

	        <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

	      </md-button>

	    </div>

	  </md-toolbar>

	  <md-dialog-content style="max-width:800px;max-height:810px;">

	    <md-content class="bg-white">

	      <md-list flex>

	        <md-list-item>

	          <md-input-container  class="md-block full-width">

	            <label><strong><?php echo lang2('your_login_password') ?></strong></label>

	            <input type="password" class="form-control" ng-model="your_login_password" placeholder="<?php echo lang2('your_login_password'); ?>">

	          </md-input-container>

	        </md-list-item>

	        <md-list-item ng-show="viewPassword == true">

	          <md-input-container  class="md-block full-width">

	            <p><?php echo lang2('your_smtp_password') ?> <strong>{{final_smtp_password}}</strong></p>

	          </md-input-container>

	        </md-list-item>

	        <br><br>

	        <md-divider>

	        </md-divider>

	        <md-button ng-click="viewSMTPPassword()" class="template-button" ng-disabled="viewing == true">

	          <span ng-hide="viewing == true"><?php echo lang2('submit'); ?></span>

	          <md-progress-circular class="white" ng-show="viewing == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

	        </md-button>

	        <md-button ng-click="close()" class="">

	          <span><?php echo lang2('cancel'); ?></span>

	        </md-button>

	      </md-list>

	    </md-content>     

	  </md-dialog-content>

	</md-dialog>        

</script>

<script type="text/ng-template" id="version-check-template.html">

	<md-dialog aria-label="options dialog">

		<?php helper('form');
		echo form_open_multipart('settings/download_update/', array("class" => "form-horizontal")); ?>

		<md-dialog-content layout-padding>

			<h2 class="md-title" style="border-bottom: 1px solid lightgray;">

				<?php echo lang2('version_check'); ?>

			</h2>

			<span><?php echo lang2('you_are_usign_version'); ?> 

			<strong><span>{{version_number}}</span></strong></span><a target="_blank" href="https://suisesoft.tech/topics/ciuiscrm/changelog/" class="link">(<?php echo lang2('view_changelog'); ?>)</a> <br>

			<span><?php echo lang2('last_updated_on'); ?>:    

				<strong><span>{{versions.last_updated | date : 'MMM d, y'}}</span></strong></span>

				<p ng-show="msg=='Already updated'" class="text-success upto-date-message"><?php echo lang2('up_to_date'); ?></p>

				<div ng-if="updated" style="border: 1px dotted #ececec;margin-top: 2%;padding-top: unset;">

					<h2 class="md-title"><?php echo lang2('availableversion'); ?> {{ Version_latest }}</h2>

					<p ng-bind-html="version_log"></p>

					<p ng-bind-html="changeLog"></p> 

					<input type="hidden" name="version_number" value="{{Version_latest}}" ng-model="Version_latest" ng-value="Version_latest">

				</div>

		</md-dialog-content>

		<md-dialog-actions>

			<span flex></span>

			<md-button ng-if="!updated" class="template-button" ng-click="checkForUpdates($event)" aria-label="Update">

				<span><?php echo lang2('check_for_updates'); ?></span>

			</md-button>

			<md-button ng-if="updated" type="button" ng-click="downloadUpdate($event)" class="md-raised md-primary successButton md-button md-ink-ripple" aria-label="Update">

				<md-tooltip md-direction="top"><?php echo lang2('download_update'); ?> </md-tooltip>

				<md-icon aria-label="download update">

					<i class="mdi mdi-download"></i>

				</md-icon> 

				<?php echo lang2('download'); ?>

			</md-button>

			<md-button ng-click="close()"><?php echo lang2('close') ?>!</md-button>

		</md-dialog-actions>

		<?php echo form_close(); ?>

	</md-dialog>      

</script>

<script type="text/ng-template" id="install-update.html">

	<md-dialog aria-label="options dialog">

	  <?php helper('form');
		echo form_open_multipart('settings/download_update/', array("class" => "form-horizontal")); ?>

	  <md-toolbar class="toolbar-white">

	    <div class="md-toolbar-tools">

	      <h2><strong class="text-success"><?php echo lang2('install_update') ?></strong></h2>

	      <span flex></span>

	      <md-button class="md-icon-button" ng-click="close()">

	        <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black" ng-hide="installed == true"></md-icon>

	      </md-button>

	    </div>

	  </md-toolbar>

	  <md-progress-linear md-mode="query" ng-show="installing == true && installed == false"></md-progress-linear>

	  <md-dialog-content layout-padding class="install-button">

	    <div>

	      <span><?php echo lang2('you_are_usign_version'); ?> 

	      <strong><span>{{version_number}}</span></strong></span><a target="_blank" href="https://stellar.ladesk.com/325898-Change-Log" class="link">(<?php echo lang2('view_changelog'); ?>)</a> <br>

	      <span><?php echo lang2('last_updated_on'); ?>:    

	        <strong><span>{{versions.last_updated}}</span></strong>

	      </span>

	    </div>

	    <div class="text-center">

	      <p class="message-box" ng-show="installed == true">{{installed_message}}</p>

	      <md-button ng-if="install_update" ng-hide="installed == true" type="button" ng-click="installUpdate($event)" class="md-raised md-primary successButton md-button md-ink-ripple" aria-label="Update" ng-disabled="installing == true">

	        <md-progress-circular class="white" md-mode="indeterminate" md-diameter="20" ng-show="installing == true"></md-progress-circular> 

	        <span ng-show="installing == false">

	          <md-tooltip md-direction="top"><?php echo lang2('install_update'); ?> </md-tooltip>

	          <md-icon aria-label="download update">

	            <i class="ion-archive"></i>

	          </md-icon>

	          <?php echo lang2('install_update'); ?>

	        </span>

	        <span ng-show="installing == true">

	          <md-tooltip md-direction="top"><?php echo lang2('installing'); ?> </md-tooltip>

	          <?php echo lang2('installing'); ?>

	        </span>

	      </md-button>

	    </div>

	  </md-dialog-content>

	  <?php echo form_close(); ?>

	</md-dialog>        

</script>

<script type="text/ng-template" id="checking.html">

	<md-dialog id="plz_wait" style="box-shadow:none;padding:unset;min-width: 25%;">

    <md-dialog-content layout="row" layout-margin layout-padding layout-align="center center" aria-label="wait">

      <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
            <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
            

      <span><?php echo lang2('checking_for_updates') ?></span>

    </md-dialog-content> 

  </md-dialog>

</script>

<!-- Toggle for downloading the upload -->

<script type="text/ng-template" id="updating.html">

	<md-dialog id="updating" style="box-shadow:none;padding:unset;min-width: 25%;">

        <md-dialog-content layout-padding layout-align="center center" aria-label="wait" style="text-align: center;">

            <md-progress-circular md-mode="indeterminate" md-diameter="40" style="margin-left: auto;margin-right: auto;"></md-progress-circular>

            <span style="font-size: 15px;"><strong><?php echo lang2('downloading'); ?></strong></span>

            <div class="row">

                <div class="col-md-12">

                    <p style="opacity: 0.7;"><br><?php echo lang2('update_note'); ?></p>

                </div>

            </div>

        </md-dialog-content>

    </md-dialog>

</script>

<script type="text/ng-template" id="backup.html">

	<md-dialog id="updating" style="box-shadow:none;padding:unset;min-width: 25%;">

    <md-dialog-content layout-padding layout-align="center center" aria-label="wait" style="text-align: center;">

      <md-progress-circular md-mode="indeterminate" md-diameter="40" style="margin-left: auto;margin-right: auto;"></md-progress-circular>

      <span style="font-size: 15px;"><strong><?php echo lang2('backingup'); ?></strong></span>

      <div class="row">

        <div class="col-md-12">

          <p style="opacity: 0.7;"><br><?php echo lang2('update_note'); ?></p>

        </div>

      </div>

    </md-dialog-content>

  </md-dialog>

</script>

<script type="text/ng-template" id="restoring.html">

	<md-dialog id="updating" style="box-shadow:none;padding:unset;min-width: 25%;">

    <md-dialog-content layout-padding layout-align="center center" aria-label="wait" style="text-align: center;">

      <md-progress-circular md-mode="indeterminate" md-diameter="40" style="margin-left: auto;margin-right: auto;"></md-progress-circular>

      <span style="font-size: 15px;"><strong><?php echo lang2('restoring'); ?></strong></span>

      <div class="row">

        <div class="col-md-12">

          <p style="opacity: 0.7;"><br><?php echo lang2('update_note'); ?></p>

        </div>

      </div>

    </md-dialog-content>

  </md-dialog>

</script>

<script type="text/ng-template" id="system_info.html">

	<md-dialog aria-label="System Info">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<h2><strong class="text-success"><?php echo lang2('system') . ' ' . lang2('info') ?></strong></h2>

				<span flex></span>

				<md-button class="md-icon-button" ng-click="close()">

					<md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

				</md-button>

			</div>

		</md-toolbar>

		<md-dialog-content style="max-width:800px;max-height:810px; ">

			<md-content class="bg-white">

				<div style="display:none">

					<?php

					function listFolderFiles($dir)
					{

						$ffs = scandir($dir);

						unset($ffs[array_search('.', $ffs, true)]);

						unset($ffs[array_search('..', $ffs, true)]);

						if (count($ffs) < 1)

							return;

						echo '<ol>';

						foreach ($ffs as $ff) {

							echo '<li>' . $ff;

							if (is_dir($dir . '/' . $ff)) listFolderFiles($dir . '/' . $ff);

							echo '</li>';
						}

						echo '</ol>';
					}

					?>

				</div>

				<p class="text-center">

					<a class="link cursor badge" ng-click="controllersFiles?controllersFiles=false:controllersFiles=true;viewsFiles=false;assetsFiles=false;uploadsFiles=false;">

						Controllers

					</a>

					<a class="link cursor badge" ng-click="viewsFiles?viewsFiles=false:viewsFiles=true;controllersFiles=false;assetsFiles=false;uploadsFiles=false;">

						Views

					</a>

					<a class="link cursor badge" ng-click="uploadsFiles?uploadsFiles=false:uploadsFiles=true;controllersFiles=false;assetsFiles=false;viewsFiles=false;">

						Uploads

					</a>

					<a class="link cursor badge" ng-click="assetsFiles?assetsFiles=false:assetsFiles=true;controllersFiles=false;viewsFiles=false;viewsFiles=false">

						Assets/Files

					</a>

				</p>

				<div ng-show="viewsFiles == true" class="folders-structure">

					<?php // listFolderFiles(VIEWPATH); ?>

				</div>

				<div ng-show="controllersFiles == true" class="folders-structure">

					<?php  //listFolderFiles(APPPATH . 'controllers'); ?>

				</div>

				<div ng-show="uploadsFiles == true" class="folders-structure">

					<?php //listFolderFiles(base_url('uploads')); 
					?>

				</div>

				<div ng-show="assetsFiles == true" class="folders-structure">

					<?php //listFolderFiles(base_url('assets/files')); 
					?>

				</div>

				<md-list flex>

					<md-list-item>

						<div class="col-md-12">

							<p><strong>
								<?php echo lang2('app') . ' ' . lang2('info'); ?>:</strong></p>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo lang2('php_version') ?>:

									</p>

								</div>

								<div flex="50">

									<p>

										<strong><?php echo phpversion(); ?></strong> 

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo lang2('mysql_version') ?>:

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php

											ob_start();

											phpinfo(INFO_MODULES);

											$mysql = ob_get_contents();

											ob_end_clean();

											$mysql = stristr($mysql, 'Client API version');

											preg_match('/[1-9].[0-9].[1-9][0-9]/', $mysql, $search);

											echo $search[0];

											?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo lang2('database') ?>:

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo lang2('tables_present') ?>:

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php

									

											?>

										</strong>

									</p>

									<p style="display:none">

										<?php

									

										?> 

									</p>  

								</div>

							</div><br>

							<p><strong><?php echo lang2('loaded_extensions'); ?>:</strong></p>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'MySQLi' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('mysqli')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'PDO' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('pdo')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'mcrypt' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('mcrypt')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'OpenSSL' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('openssl')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'iconv' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('iconv')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'cURL' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('curl')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'MBString' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('mbstring')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'GD' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('gd')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'ZIP' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!extension_loaded('zip')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

							<div layout="row" layout-wrap>

								<div flex="50">

									<p>

										<?php echo 'allow_url_fopen' ?>

									</p>

								</div>

								<div flex="50">

									<p>

										<strong>

											<?php if (!ini_get('allow_url_fopen')) { ?>

												<md-icon><i class="ion-ios-close text-danger"></i></md-icon>

											<?php } else { ?>

												<md-icon><i class="ion-ios-checkmark text-success"></i></md-icon>

											<?php } ?>

										</strong>

									</p>

								</div>

							</div>

						</div>

					</md-list-item>

				</md-list>

			</md-content>     

		</md-dialog-content>

	</md-dialog>        

</script>

<script type="text/ng-template" id="uninstall.html">

	<md-dialog aria-label="uninstall_license">

	  <md-toolbar class="toolbar-white">

	    <div class="md-toolbar-tools">

	      <h2><strong class="text-success"><?php echo lang2('uninstall_license') ?></strong></h2>

	      <span flex></span>

	      <md-button class="md-icon-button" ng-click="close()">

	        <md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

	      </md-button>

	    </div>

	  </md-toolbar>

	  <md-progress-linear md-mode="query" ng-show="uninstalling == true"></md-progress-linear>

	  <md-dialog-content layout-padding class="install-button">

	    <div class="text-center">

	      <div>

	        <md-checkbox ng-model="uninstall_confirm" aria-label="license"><?php echo lang2('uninstall_note') ?></md-checkbox>

	      </div>

	      <div>

	        <md-button class="md-raised md-warn" aria-label="Update" ng-disabled="uninstalling == true" ng-click="RemoveLicense()">

	          <md-progress-circular class="white" md-mode="indeterminate" md-diameter="20" ng-show="uninstalling == true"></md-progress-circular> 

	          <span ng-hide="uninstalling == true">

	            <md-tooltip md-direction="top"><?php echo lang2('install_update'); ?> </md-tooltip>

	            <md-icon aria-label="license">

	              <i class="ion-unlocked"></i>

	            </md-icon>

	            <?php echo lang2('uninstall'); ?>

	          </span>

	          <span ng-show="uninstalling == true">

	            <md-tooltip md-direction="top"><?php echo lang2('uninstalling'); ?> </md-tooltip>

	            <?php echo lang2('uninstalling'); ?>

	          </span>

	        </md-button>

	      </div>

	    </div>

	  </md-dialog-content>

	</md-dialog>        

</script>