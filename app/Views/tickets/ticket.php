<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<md-content class="ciuis-body-content" ng-controller="Ticket_Controller">

	<md-content class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">

		<md-toolbar class="toolbar-white">

			<div class="md-toolbar-tools">

				<md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">

					<md-icon><i class="ico-ciuis-supports text-muted"></i></md-icon>

				</md-button>

				<h2 ng-bind="ticket.ticket_number"></h2>&nbsp;

				<h2 flex md-truncate ng-bind="ticket.subject"></h2>

				<?php if (check_privilege('tickets', 'edit')) { ?> 

					<md-button ng-click="AssigneStaff()" class="md-icon-button md-primary" aria-label="Add Member" ng-cloak>

						<md-tooltip md-direction="bottom"><?php echo lang2('assignstaff') ?></md-tooltip>

						<md-icon class="ion-person-add"></md-icon>

					</md-button>

				<?php } if (check_privilege('tickets', 'delete')) { ?> 

					<md-button ng-click="Delete()" class="md-icon-button" aria-label="Delete" ng-cloak>

						<md-tooltip md-direction="bottom"><?php echo lang2('delete') ?></md-tooltip>

						<md-icon><i class="ion-trash-b text-muted"></i></md-icon>

					</md-button>

				<?php } if (check_privilege('tickets', 'edit')) { ?> 

					<md-menu md-position-mode="target-right target" ng-cloak>

						<md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">

							<md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>

						</md-button>

						<md-menu-content width="4">

							<md-menu-item>

								<md-button ng-click="MarkAs(1,lang.open)" ng-bind="lang.markasopen" aria-label="Open"></md-button>

							</md-menu-item>

							<md-menu-item>

								<md-button ng-click="MarkAs(2,lang.inprogress)" ng-bind="lang.markasinprogress" aria-label="In Progress"></md-button>

							</md-menu-item>

							<md-menu-item>

								<md-button ng-click="MarkAs(3,lang.answered)" ng-bind="lang.markasanswered" aria-label="Answered"></md-button>

							</md-menu-item>

							<md-menu-item>

								<md-button ng-click="MarkAs(4,lang.closed)" ng-bind="lang.markasclosed" aria-label="Closed"></md-button>

							</md-menu-item>

						</md-menu-content>

					</md-menu>

				<?php } ?>

			</div>

		</md-toolbar>

		<div ng-show="ticketsLoader" layout-align="center center" class="text-center" id="circular_loader">

			<md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>

			<p style="font-size: 15px;margin-bottom: 5%;">

				<span>

					<?php echo lang2('please_wait') ?> <br>

					<small><strong><?php echo lang2('loading'). ' '. lang2('ticket').'...' ?></strong></small>

				</span>

			</p>

		</div>

		<md-content ng-show="!ticketsLoader" layout-padding class="bg-white" style="overflow: hidden;" ng-cloak>

			<div class="ciuis-ticket-row">

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label"><?php echo lang2('assignedstaff')?></div>

					<div class="ticket-data" ng-bind="ticket.assigned_staff_name"></div>

				</div>

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label"><?php echo lang2('customer')?></div>

					<div class="ticket-data"><a href="<?php echo base_url('customers/customer/{{ticket.customer_id}}')?>" ng-bind="ticket.customer"></a></div>

				</div>

			</div>

			<div class="ciuis-ticket-row">

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label"><?php echo lang2('contactname')?></div>

					<div class="ticket-data" ng-bind="ticket.contactname"></div>

				</div>

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label"><?php echo lang2('department')?></div>

					<div class="ticket-data" ng-bind="ticket.department"></div>

				</div>

			</div>

			<div class="ciuis-ticket-row">

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label"><?php echo lang2('relationtype')?></div>

					<div class="ticket-data">

						<a ng-if="ticket.relation != NULL" href="<?php echo base_url('projects/project/{{ticket.relation_id}}')?>" ng-bind="ticket.relation"></a>

						<a ng-if="ticket.relation == NULL"><?php echo lang2('n_a') ?></a>

					</div>

				</div>

				<div class="ciuis-ticket-fieldgroup">



				</div>

			</div>

			<div class="ciuis-ticket-row">

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label">

						<?php echo lang2('status')?>

					</div>

					<div class="ticket-data label-status" ng-bind="ticket.status"></div>

				</div>

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label">

						<?php echo lang2('priority')?>

					</div>

					<div class="ticket-data" ng-bind="ticket.priority"></div>

				</div>

			</div>

			<div class="ciuis-ticket-row">

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label">

						<?php echo lang2('datetimeopened')?>

					</div>

					<div class="ticket-data" ng-bind="ticket.opened_date"></div>

				</div>

				<div class="ciuis-ticket-fieldgroup">

					<div class="ticket-label">

						<?php echo lang2('datetimelastreplies')?>

					</div>

					<div class="ticket-data" ng-bind="ticket.last_reply_date"></div>

				</div>

			</div>

			<div class="ciuis-ticket-row">

				<div class="ciuis-ticket-fieldgroup full">

					<div class="ticket-label"><strong><?php echo lang2('message') ?></strong></div>

					<div style="padding: 10px; border-radius: 3px; margin-bottom: 10px; font-weight: 600; background: #f3f3f3;" class="ticket-data">

						<span ng-bind="ticket.message"></span>

						<span ng-show="ticket.attachment" class="label label-default pull-right"><i class="ion-android-attach"></i> <a download href="<?php echo base_url('uploads/attachments/{{ticket.attachment}}') ?>" ng-bind="ticket.attachment"></a></span>

					</div>

				</div>

			</div>

			<div class="ticket-replies row">

				<div class="col-md-12">

					<section class="ciuis-notes show-notes">

						<article class="ciuis-note-detail" ng-repeat="reply in ticket.replies">

							<div class="ciuis-note-detail-img">

								<img src="<?php echo base_url('assets/img/comment.png') ?>" alt="" width="50" height="50" />

							</div>

							<div class="ciuis-note-detail-body">

								<div class="text">

									<p>

										<span ng-bind="reply.message"></span>

										<span ng-hide="!reply.attachment" class="label label-default pull-right"><i class="ion-android-attach"></i> <a download ng-href="<?php echo base_url('tickets/attachments/{{reply.attachment}}') ?>" ng-bind="reply.attachment"></a></span>

									</p>

								</div>

								<p class="attribution"><?php echo lang2('repliedby') ?> <strong ng-bind="reply.name"></strong> <?php echo lang2('ad') ?> <span ng-bind="reply.date"></span></p>

							</div>

						</article> 

					</section>

				</div>

				<?php if (check_privilege('tickets', 'edit')) { ?> 

					<div class="col-md-12">

						<section class="md-pb-30">

							<?php helper('form');echo form_open_multipart('tickets/reply/'.$ticket['id'].''); ?>

							<md-input-container class="md-block">

								<label><?php echo lang2('reply') ?></label>

								<textarea name="message" ng-model="reply.message" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control answer"></textarea>

							</md-input-container>

							<div class="form-group pull-left">

								<input type="file" name="attachment" id="chooseFile" file-model="reply.attachment">

							<!-- <div class="file-upload">

								<div class="file-select">

									<div class="file-select-button" id="fileName"><span class="mdi ion-android-attach"></span>

										<?php echo lang2('attachment')?>

									</div>

									<div class="file-select-name" id="noFile">

										<?php echo lang2('notchoise')?>

									</div>

								</div>

							</div> -->

						</div>

						<md-button ng-click="replyToTicket()" class="md-raised md-primary pull-right" ng-disabled="replying == true">

							<span ng-hide="replying == true"><?php echo lang2('reply');?></span>

							<md-progress-circular class="white" ng-show="replying == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

						</md-button>

						<?php echo form_close(); ?>

					</section>

				</div>

			<?php } ?>

			</div>

		</md-content>

	</md-content>

	<ciuis-sidebar ng-show="!ticketsLoader"></ciuis-sidebar>

	<script type="text/ng-template" id="insert-member-template.html">

		<md-dialog aria-label="options dialog">

			<md-dialog-content layout-padding>

				<h2 class="md-title"><?php echo lang2('assigned'); ?></h2>

				<md-select required ng-model="AssignedStaff" style="min-width: 200px;" aria-label="AddMember">

					<md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>

				</md-select>

			</md-dialog-content>

			<md-dialog-actions>

				<span flex></span>

				<md-button ng-click="close()"><?php echo lang2('cancel') ?>!</md-button>

				<md-button ng-click="AssignStaff()"><?php echo lang2('add') ?>!</md-button>

			</md-dialog-actions>

		</md-dialog>

	</script>

</md-content>





<script>var TICKETID = "<?php echo $ticket['id'];?>";</script>

<script type="text/javascript">

	var lang = {};

	lang.attention = "<?php echo lang2('attention')?>";

	lang.doIt = "<?php echo lang2('doIt')?>";

	lang.cancel = "<?php echo lang2('cancel')?>";

	lang.ticketattentiondetail = "<?php echo lang2('ticketattentiondetail')?>";

	lang.ticket = "<?php echo lang2('ticket')?>";

	lang.delete = "<?php echo lang2('delete')?>";

</script>

<?php include_once( APPPATH . 'Views/inc/footer.php' ); ?>

<script type="text/javascript" src="<?php echo base_url('assets/js/tickets.js') ?>"></script>