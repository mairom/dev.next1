<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Ticket_Controller">
	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
		<md-toolbar class="toolbar-white">
			<div class="md-toolbar-tools">
				<md-button class="md-icon-button" aria-label="Settings" ng-disabled="true" ng-cloak>
					<md-icon><i class="ico-ciuis-supports text-muted"></i></md-icon>
				</md-button>
				<h2 ng-bind="ticket.ticket_id"></h2>&nbsp;
				<h2 flex md-truncate ng-bind="ticket.subject"></h2>
			</div>
		</md-toolbar>
		<div class="tab-container">
			<div class="active-view">
				<div class="ciuis-ticket" ng-cloak style="height: unset;">
					<div class="panel borderten">
						<div class="ciuis-ticket-detail full-scroll tab-pane cont" id="ticketdetail">
							<div class="ciuis-ticket-info">
								<div class="ciuis-ticket-row">
									<div class="ciuis-ticket-fieldgroup">
										<div class="ticket-label"><?php echo lang2('assignedstaff')?></div>
										<div class="ticket-data" ng-bind="ticket.assigned_staff_name"></div>
									</div>
									<div class="ciuis-ticket-fieldgroup">
										<div class="ticket-label"><?php echo lang2('customer')?></div>
										<div class="ticket-data"><strong ng-bind="ticket.customer"></strong></div>
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
											<span ng-show="ticket.attachment" class="label label-default pull-right"><i class="ion-android-attach"></i> <a href="<?php echo base_url('uploads/attachments/{{ticket.attachment}}') ?>" ng-bind="ticket.attachment"></a>
											</span>
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
															<span ng-hide="!reply.attachment" class="label label-default pull-right"><i class="ion-android-attach"></i> <a download ng-href="<?php echo base_url('area/tickets/attachments/{{reply.attachment}}') ?>" ng-bind="reply.attachment"></a></span>
														</p>
													</div>
													<p class="attribution"><?php echo lang2('repliedby') ?> <strong ng-bind="reply.name"></strong> at <span ng-bind="reply.date"></span></p>
												</div>
											</article>
										</section>
									</div>
									<div class="col-md-12">
										<section class="md-pb-30">
											<?php helper('form');echo form_open_multipart('tickets/reply/'.$ticket['id'].''); ?>
											<md-input-container class="md-block">
												<label><?php echo lang2('reply') ?></label>
												<textarea name="message" ng-model="reply.message" placeholder="<?php echo lang2('typeSomething'); ?>" class="form-control answer"></textarea>
											</md-input-container>
											<div class="form-group pull-left">
												<input type="file" name="attachment" id="chooseFile" file-model="reply.attachment">
											</div>
											<md-button ng-click="replyToTicket()" class="md-raised md-primary pull-right" ng-disabled="replying == true">
												<span ng-hide="replying == true"><?php echo lang2('reply');?></span>
												<md-progress-circular class="white" ng-show="replying == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
											</md-button>
											<?php echo form_close(); ?>
										</section>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include_once(APPPATH . 'Views/area/inc/sidebar.php'); ?>
</div>
<script>var TICKETID = "<?php echo $ticket['id'];?>";</script>