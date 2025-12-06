<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<div class="ciuis-body-content" ng-controller="Editor_Controller">

	<style type="text/css">
		rect.highcharts-background {

			fill: #f3f3f3;

		}
	</style>

	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-3">

		<md-toolbar ng-show="!customersLoader" class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button class="md-icon-button" aria-label="File" ng-cloak>

					<md-icon><i class="ion-android-folder text-muted"></i></md-icon>

				</md-button>

				<h2 flex md-truncate><?php echo lang2('select_file'); ?></h2>

			</div>

		</md-toolbar>

		<md-content layout-padding class="bg-white" ng-cloak>

			<md-input-container class="md-block">

				<label><?php echo lang2('select_file'); ?></label>

				<md-select placeholder="<?php echo lang2('select_file'); ?>" ng-model="file_name" ng-change="getFileContent(file_name)" style="min-width: 150px;" ng-init="file_name = 1">

					<md-option bg-selected="true" ng-value="1"><?php echo lang2('language') . ' ' . lang2('file'); ?></md-option>

					<md-option ng-value="2"><?php echo lang2('invoice') . ' ' . lang2('pdf'); ?></md-option>

					<md-option ng-value="3"><?php echo lang2('expense') . ' ' . lang2('pdf'); ?></md-option>

					<md-option ng-value="4"><?php echo lang2('purchase') . ' ' . lang2('pdf'); ?></md-option>

					<md-option ng-value="5"><?php echo lang2('proposal') . ' ' . lang2('pdf'); ?></md-option>

					<md-option ng-value="6"><?php echo lang2('order') . ' ' . lang2('pdf'); ?></md-option>

					<md-option ng-value="7"><?php echo lang2('deposit') . ' ' . lang2('pdf'); ?></md-option>

					<md-option ng-value="8"><?php echo lang2('project') . ' ' . lang2('pdf'); ?></md-option>

					<!-- <md-option ng-value="9"><?php echo lang2('countries') . ' ' . 'JSON'; ?> </md-option>

					<md-option ng-value="10"><?php echo lang2('states') . ' ' . 'JSON'; ?> </md-option> -->

				</md-select>

			</md-input-container>

			<md-input-container class="md-block text-center">

				<md-button ng-show="file_name && file_name != 1" ng-click="RestoreDefault(file_name)" class="template-button" ng-disabled="restoring == true">

					<md-tooltip md-direction="top"><?php echo lang2('restore_default') ?></md-tooltip>

					<span ng-hide="restoring == true">

						<?php echo lang2('restore_default') ?>

					</span>

					<md-progress-circular class="white" ng-show="restoring == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

					<md-tooltip md-direction="top"><?php echo lang2('restore_default') ?></md-tooltip>

				</md-button>

				<md-button ng-click="SaveFile(file_name)" class="template-button" ng-disabled="saving == true">

					<span ng-hide="saving == true">

						<?php echo lang2('save'); ?>

					</span>

					<md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

				</md-button>

			</md-input-container>

		</md-content>

	</div>

	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9" style="min-height: 90vh;margin-bottom: 5vh;">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button class="md-icon-button" aria-label="File">

					<md-icon><i class="mdi mdi-edit material-icons text-muted"></i></md-icon>

				</md-button>

				<h2 flex md-truncate><?php echo lang2('editor'); ?></h2>

			</div>

		</md-toolbar>

		<div ng-show="loadingFile" layout-align="center center" class="text-center" id="circular_loader">
			<!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
			<img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">

			<p style="font-size: 15px;margin-bottom: 5%;">

				<span>

					<?php echo lang2('please_wait') ?> <br>

					<small><strong><?php echo lang2('loading') . ' ' . lang2('file') . '...' ?></strong></small>

				</span>

			</p>

		</div>

		<style type="text/css" media="screen">
			#editor {

				position: absolute;

				top: 0;

				right: 0;

				bottom: 0;

				left: 0;

				margin-left: 1%;

				margin-top: 5%;

				margin-bottom: 1%;

				margin-right: 1%;

				overflow-y: auto;

				overflow-x: hidden;

				min-height: 70vh !important;

				z-index: 999;

			}

			.ace-twilight .ace_print-margin {

				display: none;

			}

			.ace_scrollbar-h {

				overflow-x: hidden !important;

			}
		</style>

		<pre ng-show="!loadingFile" id="editor"></pre>

		<script src="<?php echo base_url('assets/lib/editor/ace.js') ?>"></script>

		<script>
			var editor = ace.edit("editor", {

			});

			editor.setTheme("ace/theme/twilight");

			editor.session.setMode("ace/mode/php");
		</script>

	</div>

</div>

<?php include(APPPATH . 'Views/inc/footer.php'); ?>

<script type="text/javascript" src="<?php echo base_url('assets/js/editor.js') ?>"></script>