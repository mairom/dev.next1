<md-content class="md-padding bg-white">
	<div class="col-md-6">
		<div class="col-md-12 md-p-0">
			<h4><strong><?php echo lang2('two_factor_authentication') ?></strong></h4>
			<span><?php echo lang2('two_factor_authentication_description') ?></span>
			<!-- <hr> -->
			<ul>
				<li><a href="https://www.google.com/landing/2step/" rel="nofollow"><?php echo lang2('google_2_step_ver') ?></a></li>
				<li><?php echo lang2('google_authenticator') ?>:
					<ul>
						<li><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" rel="nofollow"><?php echo lang2('android_app') ?></a></li>
						<li><a href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8" rel="nofollow"><?php echo lang2('iphone_ipad') ?></a></li>
						<li><a href="http://m.google.com/authenticator" rel="nofollow"><?php echo lang2('blackberry_app') ?></a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="col-md-12 md-p-0">
			<md-switch class="pull-left" ng-model="settings_detail.two_factor_authentication" aria-label="Status"><strong class="text-muted"><?php echo lang2('two_factor_authentication') ?></strong></md-switch>
		</div>
	</div>
	<div class="col-md-6">
		<md-input-container class="md-block">
			<label><?php echo lang2('acceptedfileformats')?></label>
			<input required ng-model="settings_detail.accepted_files_formats">
		</md-input-container>
		<md-input-container class="md-block">
			<label><?php echo lang2('allowedipaddereses')?></label>
			<input required ng-model="settings_detail.allowed_ip_adresses">
		</md-input-container>
		<div class="col-md-12 md-p-0">
			<md-switch disabled class="pull-left" ng-model="settings_detail.pushState" aria-label="Status"><strong class="text-muted"><?php echo lang2('pushstate') ?></strong></md-switch>
			<md-switch class="pull-left" ng-model="settings_detail.voicenotification" aria-label="Status"><strong class="text-muted"><?php echo lang2('voicenotifications') ?></strong></md-switch>
			<md-switch class="pull-left" ng-model="settings_detail.is_mysql" aria-label="Status"><strong class="text-muted"><?php echo lang2('mysql_queries') ?></strong></md-switch>
		</div>
	</div>
</md-content>