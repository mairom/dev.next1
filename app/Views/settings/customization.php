<md-content class="md-padding bg-white">

	<?php helper('form');echo form_open_multipart('settings/save_config/',array("class"=>"form-horizontal", "method" => "POST")); ?>

	<div class="col-md-6">

		<div class="col-md-12 md-p-0 upload-logo">

			<div layout="row" layout-wrap>

				<div flex-gt-xs="50" flex-xs="100">

					<div flex="100">

						<h2 class="md-title"><?php echo lang2('applogo'); ?></h2>

					</div>

					<div flex="100">

						<div class="img">

							<img ng-src="<?php echo base_url('uploads/ciuis_settings/{{settings_detail.app_logo}}')?>"

							on-error-src="<?php echo base_url('assets/img/placeholder.png')?>">

						</div>

						<span><?php echo lang2('applogoinfo') ?></span>

						<div flex-gt-xs="50" flex-xs="100" class="file-upload">

							<input type="file" name="applogo" class="file-type" accept="image/*"

							file-model="applogo_img">

						</div>

					</div>

				</div>

				<div flex-gt-xs="50" flex-xs="100">

					<div flex="100">

						<h2 class="md-title"><?php echo lang2('navlogo'); ?></h2>

					</div>

					<div flex="100">

						<div class="img">

							<img ng-src="<?php echo base_url('uploads/ciuis_settings/{{settings_detail.logo}}')?>"

							on-error-src="<?php echo base_url('assets/img/placeholder.png')?>">

						</div>

						<span><?php echo lang2('reommended_size'); ?> 42 x 42 px.</span>

						<div flex-gt-xs="50" flex-xs="100" class="file-upload">

							<input type="file" name="navlogo" class="file-type" accept="image/*"

							file-model="navlogo_img">

						</div>

					</div>

				</div>

			</div>

			<div layout="row" layout-wrap>

				<div flex-gt-xs="50" flex-xs="100">

					<div flex="100">

						<h2 class="md-title"><?php echo lang2('admin_login_image'); ?></h2>

					</div>

					<div flex="100">

						<div class="login_image">

							<img ng-src="<?php echo base_url('assets/img/images/{{rebrand.admin_login_image}}')?>"

							on-error-src="<?php echo base_url('assets/img/placeholder.png')?>">

						</div>

						<span><?php echo lang2('reommended_size'); ?> 1100 x 1600 px.</span>

						<div flex-gt-xs="50" flex-xs="100" class="file-upload">

							<input type="file" name="admin_login_image" class="file-type"

							accept="image/*" file-model="admin_login_image_img">

						</div>

					</div>

				</div>

				<div flex-gt-xs="50" flex-xs="100">

					<div flex="100">

						<h2 class="md-title"><?php echo lang2('client_login_image'); ?></h2>

					</div>

					<div flex="100">

						<div class="login_image">

							<img ng-src="<?php echo base_url('assets/img/images/{{rebrand.client_login_image}}')?>"

							on-error-src="<?php echo base_url('assets/img/placeholder.png')?>">

						</div>

						<span><?php echo lang2('reommended_size'); ?> 1100 x 1600 px.</span>

						<div flex-gt-xs="50" flex-xs="100" class="file-upload">

							<input type="file" name="client_login_image" class="file-type"

							accept="image/*" file-model="client_login_image_img">

						</div>

					</div>

				</div>

			</div>

			<div layout="row" layout-wrap>

				<div flex-gt-xs="50" flex-xs="100">

					<div flex="100">

						<h2 class="md-title"><?php echo lang2('screen').' '.lang2('loader'); ?></h2>

						<div class="img">

							<img class="login_image" ng-src="<?php echo base_url('assets/img/{{rebrand.preloader}}')?>" on-error-src="<?php echo base_url('assets/img/placeholder.png')?>">

						</div>

						<md-switch class="pull-left" ng-model="rebrand.disable_preloader" aria-label="Status" style="margin: unset;">

							<strong class="text-muted"><?php echo lang2('disable').' '.lang2('screen').' '.lang2('loader') ?></strong>

						</md-switch><br>

						<div flex-gt-xs="50" flex-xs="100" class="file-upload">

							<input type="file" name="favicon" class="file-type" accept="image/*"

							file-model="preloader">

						</div>

					</div>

				</div>

				<div flex-gt-xs="50" flex-xs="100">
					<div flex="100">
						<h2 class="md-title"><?php echo lang2('favicon'); ?></h2>
						<div class="img">
							<img ng-src="<?php echo base_url('assets/img/images/{{rebrand.favicon_icon}}')?>"
							on-error-src="<?php echo base_url('assets/img/placeholder.png')?>"
							style="max-height: 20px !important;padding: 1px !important;">
						</div>

						<span><?php echo lang2('reommended_size'); ?> 16 x 16 px.</span>
						<div flex-gt-xs="50" flex-xs="100" class="file-upload">
							<input type="file" name="favicon" class="file-type" accept="image/*"
							file-model="favicon_img">
						</div>
					</div>
				</div>

				<div flex-gt-xs="50" flex-xs="100">
					<div flex="100">
						<h2 class="md-title">Background Lead1</h2>
						<div class="img">
							<img ng-src="<?php echo base_url('assets/img/images/{{rebrand.back_lead1}}')?>"
							on-error-src="<?php echo base_url('/assets/img/back-lead1.png')?>"
							style="max-height: 20px !important;padding: 1px !important;">
						</div>

						<span><?php echo lang2('reommended_size'); ?> 1080 x 900 px.</span>
						<div flex-gt-xs="50" flex-xs="100" class="file-upload">

							<input type="file" name="favicon" class="file-type" accept="image/*"
							file-model="back_lead1_img">
						</div>
					</div>
				</div>

			</div>

		</div>

	</div>

	<div class="col-md-6">

		<md-input-container class="md-block">

			<label><?php echo lang2('title')?></label>

			<input type="text" ng-model="rebrand.title" name="title">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('admin_login_text')?></label>

			<textarea ng-model="rebrand.admin_login_text" name="admin_login_text"></textarea>

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('client_login_text')?></label>

			<textarea ng-model="rebrand.client_login_text" name="client_login_text" class="maximum-textarea"></textarea>

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('meta_keywords')?></label>

			<textarea ng-model="rebrand.meta_keywords" name="meta_keywords" class="maximum-textarea"></textarea>

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('meta_description')?></label>

			<textarea ng-model="rebrand.meta_description" name="meta_description" class="maximum-textarea"></textarea>

		</md-input-container>

		<md-input-container ng-show="rebrand.enable_support_button_on_client" class="md-block">

			<label><?php echo lang2('support_button_title')?></label>

			<input ng-model="rebrand.support_button_title" type="text" name="support_button_title">

		</md-input-container>

		<md-input-container ng-show="rebrand.enable_support_button_on_client" class="md-block">

			<label><?php echo lang2('support_button_link')?></label>

			<input ng-model="rebrand.support_button_link" type="text" name="support_button_link">

		</md-input-container>

		<md-input-container class="md-block" style="margin-top: unset;">

			<md-switch class="pull-left" ng-model="rebrand.enable_support_button_on_client" aria-label="Status" style="margin: unset;">

				<strong class="text-muted"><?php echo lang2('support_button') ?></strong>

			</md-switch>

		</md-input-container>

		<input type="hidden" name="support_button" value="{{rebrand.enable_support_button_on_client}}" ng-value="rebrand.enable_support_button_on_client" ng-model="rebrand.enable_support_button_on_client">

		<?php if (check_privilege('settings', 'edit')) { ?>

			<md-button ng-click="saveCustomization()" class="md-raised md-primary" ng-disabled="replying == true" type="button">

				<span ng-hide="replying == true"><?php echo lang2('save');?></span>

				<md-progress-circular class="white" ng-show="replying == true" md-mode="indeterminate"

				md-diameter="20"></md-progress-circular>

			</md-button>

		<?php } ?>

	</div>

	<br><br>

	<?php echo form_close(); ?>

</md-content>