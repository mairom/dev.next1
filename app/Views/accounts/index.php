<?php include_once( APPPATH . 'Views/inc/ciuis_data_table_header.php' ); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Accounts_Controller">
	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
		<div class="account-container-ciuis-65343256347">
			<section class="accounts-information-ciuis-1881-sonuzadek">
				<i class="ion-ios-bookmarks-outline icon"></i>
				<section class="account-information-cover-234">
					<h3 class="information"><?php echo lang2('accountswelcome'); ?></h3>
					<?php if (check_privilege('accounts', 'create')) { ?>
						<a ng-click="Create()" class="reconnect-cta cursor"><b><?php echo lang2('newaccount'); ?></b></a>
					<?php } ?>
				</section>
			</section>
			<section class="ciuis-accounts">
				<a ng-repeat="account in accounts" href="<?php echo base_url('accounts/account/{{account.id}}')?>" class="huppur ciuis-account checking account">
					<div class="icon {{account.icon}}" width="23px" height="30px"></div>
					<div class="ciuis-account-information">
						<h4 class="ciuis-account-type" ng-bind="account.name"></h4>
						<p class="ciuis-account-detail" ng-bind="account.status"></p>
					</div>
					<label class="ciuis-account-temprorary"><p ng-bind-html="account.amount | currencyFormat:cur_code:null:true:cur_lct"></p></label>
				</a>
				<strong class="pull-right"><p ng-bind-html="total.accounts_total | currencyFormat:cur_code:null:true:cur_lct"></p></strong>
			</section>
		</div>
		<div class="ciuis-account-attr"></div>
	</div>
	<ciuis-sidebar></ciuis-sidebar>

<md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create"  ng-cloak style="width: 450px;">
  <md-toolbar class="toolbar-white">
  <div class="md-toolbar-tools">
	<md-button ng-click="close()" class="md-icon-button" aria-label="Close">
		 <i class="ion-android-arrow-forward"></i>
	</md-button>
	<md-truncate><?php echo lang2('create') ?></md-truncate>
  </div>
  </md-toolbar>
  <md-content layout-padding="">
	<md-content layout-padding>
		<md-input-container class="md-block">
			<label><?php echo lang2('name') ?></label>
			<input required type="text" ng-model="account.name" class="form-control" id="title" placeholder="<?php echo lang2('account').' '.lang2('name'); ?>"/>
		</md-input-container>
		<md-input-container ng-show="isBankType == true" class="md-block">
			<label><?php echo lang2('bankname') ?></label>
			<input type="text" ng-model="account.bankname" class="form-control" id="title" placeholder="<?php echo lang2('bankname'); ?>"/>
		</md-input-container>
		<md-input-container ng-show="isBankType == true" class="md-block">
			<label><?php echo lang2('branchbank') ?></label>
			<input type="text" ng-model="account.branchbank" class="form-control" id="title" placeholder="<?php echo lang2('branchbank'); ?>"/>
		</md-input-container>
		<md-input-container ng-show="isBankType == true" class="md-block">
			<label><?php echo lang2('account').' '.lang2('number') ?></label>
			<input type="text" ng-model="account.account" class="form-control" id="title" placeholder="<?php echo lang2('account'); ?>"/>
		</md-input-container>
		<md-input-container ng-show="isBankType == true" class="md-block">
			<label><?php echo lang2('iban') ?></label>
			<input type="text" ng-model="account.iban" class="form-control" id="title" placeholder="<?php echo lang2('iban'); ?>"/>
		</md-input-container>
		<md-switch class="pull-left" ng-model="isBankType" aria-label="Type"><strong class="text-muted"><?php echo lang2('bank') ?></strong></md-switch>
		<section layout="row" layout-sm="column" class="pull-right" layout-wrap>
			  <md-button ng-click="AddAccount()" class="md-raised md-primary"><?php echo lang2('add');?></md-button>
		</section>
	</md-content>
 </md-content>
</md-sidenav>
</div>
<?php include_once( APPPATH . 'Views/inc/other_footer.php' ); ?>
<script src="<?php echo base_url('assets/js/accounts.js'); ?>"></script>