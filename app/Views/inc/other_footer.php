</md-content>


<script src="<?php echo base_url('assets/js/Ciuis.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/moment.js/min/moment.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/bootstrap/dist/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/jquery.gritter/js/jquery.gritter.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/angular-datepicker/src/js/angular-datepicker.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/material/angular-material.min.js') ?>"></script>
<script src="<?php echo base_url('assets/lib/currency-format/currency-format.min.js?v=1.2') ?>"></script>
<script src="<?php echo base_url('assets/lib/angular-datetimepicker/angular-material-datetimepicker.min.js') ?>"></script>
<script src="<?php echo base_url('assets/lib/data-table/md-data-table.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/lib/select2/select2.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/ciuis_data_table.js?v=1.1.73'); ?>"></script>


<?php include_once(APPPATH . 'Views/inc/templates.php'); ?>

<script type="text/javascript">
	<?php
	$report_Model = new App\Models\Report_Model();
	$settings_Model = new App\Models\Settings_Model();

	$newreminder = $report_Model->newreminder();
	$openticket = $report_Model->otc();
	$settings = $settings_Model->get_settings_ciuis();
	?>


	function speak(CiuisVoiceNotification) {

		var s = new SpeechSynthesisUtterance();

		s.volume = 0.5;

		s.rate = 1;

		s.pitch = 1;

		s.lang = VOICENOTIFICATIONLANG;

		s.text = CiuisVoiceNotification;

		window.speechSynthesis.speak(s);

	}

	var voice = document.querySelectorAll('body');

	var reminder = '<?php echo $message = sprintf(lang2('reminder_voice'), $newreminder)  ?>';

	var oepnticket = '<?php echo $message = sprintf(lang2('open_ticket_voice'), $openticket)  ?>';

	<?php if (session()->getFlashdata('ntf1')) { ?>

		$.gritter.add({

			title: '<b><?php echo lang2('notification') ?></b>',

			text: '<?php echo session()->getFlashdata('ntf1'); ?>',

			class_name: 'color success'

		});

	<?php } ?>

	<?php if (session()->getFlashdata('ntf2')) { ?>

		$.gritter.add({

			title: '<b><?php echo lang2('notification') ?></b>',

			text: '<?php echo session()->getFlashdata('ntf2'); ?>',

			class_name: 'color primary'

		});

	<?php } ?>

	<?php if (session()->getFlashdata('ntf3')) { ?>

		$.gritter.add({

			title: '<b><?php echo lang2('notification') ?></b>',

			text: '<?php echo session()->getFlashdata('ntf3'); ?>',

			class_name: 'color warning'

		});

	<?php } ?>

	<?php if (session()->getFlashdata('ntf4')) { ?>

		$.gritter.add({

			title: '<b><?php echo lang2('notification') ?></b>',

			text: '<?php echo session()->getFlashdata('ntf4'); ?>',

			class_name: 'color danger'

		});

	<?php } ?>

	<?php if (session()->getFlashdata('login_notification')) {
		if (session()->get('admin')) { ?>

			$.gritter.add({

				title: '<?php echo lang2('welcome_message') . ' ' . session()->get('staffname'); ?>!',

				text: '<?php echo session()->get('admin_notification'); ?>',

				image: '<?php echo base_url(); ?>uploads/images/<?php echo session()->get('staffavatar'); ?>',

				class_name: 'img-rounded',

				time: '',

			});

		<?php } else { ?>

			$.gritter.add({

				title: '<?php echo lang2('welcome_message') . ' ' . session()->get('staffname'); ?>!',

				text: '<?php echo lang2('login_message') ?>',

				image: '<?php echo base_url(); ?>uploads/images/<?php echo session()->get('staffavatar'); ?>',

				class_name: 'img-rounded',

				time: '',

			});

		<?php } ?>

		var staffname = "<?php echo $message = sprintf(lang2('welcome_once_message'), session()->get('staffname')) ?> ";

		<?php if ($settings['voicenotification'] == 1) {
			echo 'speak(staffname);';
		} ?>

		<?php if ($newreminder > 0 && $settings['voicenotification'] == 1) {
			echo 'speak(reminder);';
		} ?>

		<?php if ($openticket > 0 && $settings['voicenotification'] == 1) {
			echo 'speak(oepnticket);';
		} ?>

	<?php } ?>
</script>

<script type="text/ng-template" id="timerTasks.html">

	<md-dialog id="timerTasks" style="box-shadow:none;padding:unset;min-width: 25%;">

		<md-toolbar class="toolbar-white" ng-show="!taskTimer.loader">

			<div class="md-toolbar-tools">

				<h2 ng-show="!taskTimer.loader && taskTimer.stop"><strong class="text-success"><?php echo lang2('stoptimer') ?></strong></h2>

				<h2 ng-show="!taskTimer.loader &&  taskTimer.assign"><strong class="text-success"><?php echo lang2('assign_task') ?></strong></h2>

				<span flex></span>

				<md-button class="md-icon-button" ng-click="close()">

					<md-icon class="ion-close-round" aria-label="Close dialog" style="color:black"></md-icon>

					<md-tooltip md-direction="left"><?php echo lang2('close') ?></md-tooltip>

				</md-button>

			</div>

		</md-toolbar>

		<md-dialog-content layout-padding aria-label="wait" style="text-align: center;">

			<div layout-align="center center">

				<md-progress-circular ng-if="taskTimer.loader == true || taskTimer.loader == 'true'" md-mode="indeterminate" md-diameter="30"></md-progress-circular>

				<p ng-show="taskTimer.loader" style="font-size: 15px;margin-bottom: 5%;">

					<span>

						<?php echo lang2('please_wait') ?> <br>

						<small><strong><?php echo lang2('loading') . ' ' . lang2('tasks') . '...' ?></strong></small>

					</span>

				</p>

			</div>

			<div layput-padding ng-show="!taskTimer.loader" style="text-align: left;">

				<md-input-container>

					<label><?php echo lang2('timer') . ' ' . lang2('task') ?></label>

					<md-select required ng-model="timerData.task_id" style="min-width: 250px;">

						<md-select-header>

							<md-toolbar class="toolbar-white">

							<div class="md-toolbar-tools">

								<h4 flex md-truncate><?php echo lang2('tasks') ?></h4>

								<md-button class="md-icon-button" ng-href="tasks" aria-label="New Task">

								<md-icon><i class="mdi mdi-plus text-muted"></i></md-icon>

								</md-button>

							</div>

							</md-toolbar>

						</md-select-header>

						<md-option ng-value="task.id" ng-repeat="task in timerTasks">{{task.name}}

						</md-option>

					</md-select>

				</md-input-container>

				<md-input-container class="md-block">

					<label><?php echo lang2('timer') . ' ' . lang2('note') ?></label>

					<textarea required name="description" ng-model="timerData.note" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control"></textarea>

				</md-input-container>

				<section layout="row" layout-sm="column" layout-align="center center" layout-wrap>

					<md-button ng-click="timerStopConfirm(timerData.id)" class="start-button" ng-disabled="savingTimer == true">

						<span ng-hide="savingTimer == true"><?php echo lang2('confirm'); ?></span>

            			<md-progress-circular class="white" ng-show="savingTimer == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

          			</md-button>

				</section>

			</div>

		</md-dialog-content>

	</md-dialog>

</script>

</body>

</html>