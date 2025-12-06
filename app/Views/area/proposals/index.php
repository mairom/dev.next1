<?php include_once(APPPATH . 'Views/area/inc/header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Proposals_Controller">
	<div class="main-content container-fluid col-xs-12 col-md-3 col-lg-3 hidden-xs" ng-cloak>
		<div class="panel-heading">
			<strong><?php echo lang2('proposalsituation') ?></strong>
			<span class="panel-subtitle"><?php echo lang2('proposalsituationsdesc') ?></span>
		</div>
		<div class="row" style="padding: 0px 20px 0px 20px;">
			<div class="col-md-6 col-xs-6 border-right">
				<div class="tasks-status-stat">
					<h3 class="text-bold ciuis-task-stat-title">
						<span class="task-stat-number" ng-bind="(proposals | filter:{status_id:'1'}).length"></span>
						<span class="task-stat-all" ng-bind="'/'+' '+proposals.length+' '+'<?php echo lang2('proposals') ?>'"></span>
					</h3>
					<span class="ciuis-task-percent-bg">
						<span class="ciuis-task-percent-fg" style="width: {{(proposals | filter:{status_id:'1'}).length * 100 / proposals.length }}%;"></span>
					</span>
				</div>
				<span class="text-uppercase" style="color:#989898"><?php echo lang2('draft')?></span>
			</div>
			<div class="col-md-6 col-xs-6 border-right">
				<div class="tasks-status-stat">
					<h3 class="text-bold ciuis-task-stat-title">
						<span class="task-stat-number" ng-bind="(proposals | filter:{status_id:'2'}).length"></span>
						<span class="task-stat-all" ng-bind="'/'+' '+proposals.length+' '+'<?php echo lang2('proposals') ?>'"></span>
					</h3>
					<span class="ciuis-task-percent-bg">
						<span class="ciuis-task-percent-fg" style="width: {{(proposals | filter:{status_id:'2'}).length * 100 / proposals.length }}%;"></span>
					</span>
				</div>
				<span class="text-uppercase" style="color:#989898"><?php echo lang2('sent')?></span>
			</div>
			<div class="col-md-6 col-xs-6 border-right">
				<div class="tasks-status-stat">
					<h3 class="text-bold ciuis-task-stat-title">
						<span class="task-stat-number" ng-bind="(proposals | filter:{status_id:'3'}).length"></span>
						<span class="task-stat-all" ng-bind="'/'+' '+proposals.length+' '+'<?php echo lang2('proposals') ?>'"></span>
					</h3>
					<span class="ciuis-task-percent-bg">
						<span class="ciuis-task-percent-fg" style="width: {{(proposals | filter:{status_id:'3'}).length * 100 / proposals.length }}%;"></span>
					</span>
				</div>
				<span class="text-uppercase" style="color:#989898"><?php echo lang2('open')?></span>
			</div>
			<div class="col-md-6 col-xs-6 border-right">
				<div class="tasks-status-stat">
					<h3 class="text-bold ciuis-task-stat-title">
						<span class="task-stat-number" ng-bind="(proposals | filter:{status_id:'4'}).length"></span>
						<span class="task-stat-all" ng-bind="'/'+' '+proposals.length+' '+'<?php echo lang2('proposals') ?>'"></span>
					</h3>
					<span class="ciuis-task-percent-bg">
						<span class="ciuis-task-percent-fg" style="width: {{(proposals | filter:{status_id:'4'}).length * 100 / proposals.length }}%;"></span>
					</span>
				</div>
				<span class="text-uppercase" style="color:#989898"><?php echo lang2('revised')?></span>
			</div>
			<div class="col-md-6 col-xs-6 border-right">
				<div class="tasks-status-stat">
					<h3 class="text-bold ciuis-task-stat-title">
						<span class="task-stat-number" ng-bind="(proposals | filter:{status_id:'5'}).length"></span>
						<span class="task-stat-all" ng-bind="'/'+' '+proposals.length+' '+'<?php echo lang2('proposals') ?>'"></span>
					</h3>
					<span class="ciuis-task-percent-bg">
						<span class="ciuis-task-percent-fg" style="width: {{(proposals | filter:{status_id:'5'}).length * 100 / proposals.length }}%;"></span>
					</span>
				</div>
				<span class="text-uppercase" style="color:#989898"><?php echo lang2('declined')?></span>
			</div>
			<div class="col-md-6 col-xs-6 border-right">
				<div class="tasks-status-stat">
					<h3 class="text-bold ciuis-task-stat-title">
						<span class="task-stat-number" ng-bind="(proposals | filter:{status_id:'6'}).length"></span>
						<span class="task-stat-all" ng-bind="'/'+' '+proposals.length+' '+'<?php echo lang2('proposals') ?>'"></span>
					</h3>
					<span class="ciuis-task-percent-bg">
						<span class="ciuis-task-percent-fg" style="width: {{(proposals | filter:{status_id:'6'}).length * 100 / proposals.length }}%;"></span>
					</span>
				</div>
				<span class="text-uppercase" style="color:#989898"><?php echo lang2('accepted')?></span>
			</div>
		</div>
	</div>
	<div class="main-content container-fluid col-xs-12 col-md-9 col-lg-9" ng-cloak>
		<md-toolbar class="toolbar-white">
			<div class="md-toolbar-tools" ng-cloak>
				<h2 flex md-truncate class="text-bold"><?php echo lang2('proposals'); ?><br><small flex md-truncate><?php echo lang2('organizeyourproposals'); ?></small></h2>
				<div class="ciuis-external-search-in-table">
					<input ng-model="search.subject" class="search-table-external" id="search" name="search" type="text" placeholder="<?php echo lang2('searchword')?>">
					<md-button class="md-icon-button" aria-label="Search">
						<md-icon><i class="ion-search text-muted"></i></md-icon>
					</md-button>
				</div>
			</div>
		</md-toolbar>
		<md-content ng-show="!proposalsLoader" class="bg-white" ng-cloak>
			<md-table-container ng-show="proposals.length > 0">
				<table md-table md-progress="promise">
					<thead md-head md-order="proposal_list.order">
						<tr md-row>
							<th md-column><span><?php echo lang2('proposal'); ?></span></th>
							<th md-column md-order-by="customer"><span><?php echo lang2('customer') ?></span>
							</th>
							<th md-column md-order-by="date"><span><?php echo lang2('date'); ?></span></th>
							<th md-column md-order-by="opentill"><span><?php echo lang2('opentill'); ?></span></th>
							<th md-column md-order-by="status"><span><?php echo lang2('status'); ?></span></th>
							<th md-column md-order-by="total"><span><?php echo lang2('amount'); ?></span></th>
							<th md-column md-order-by="staff"><span><?php echo lang2('staff'); ?></span></th>
						</tr>
					</thead>
					<tbody md-body>
						<tr class="select_row" md-row ng-repeat="proposal in proposals | orderBy: proposal_list.order | limitTo: proposal_list.limit : (proposal_list.page -1) * proposal_list.limit | filter: proposal_search | filter: FilteredData">
							<td md-cell>
								<strong>
									<a class="link" href="<?php echo base_url('area/proposals/proposal/'); ?>{{proposal.token}}"> <span ng-bind="proposal.proposal_number"></span></a>
								</strong><br>
								<small ng-bind="proposal.subject"></small>
							</td>
							<td md-cell>
								<strong><span ng-bind="proposal.customer"></span></strong><br><small class="blur" ng-bind="proposal.customer_email"></small>
							</td>
							<td md-cell>
								<strong><span class="badge" ng-bind="proposal.date"></span></strong>
							</td>
							<td md-cell>
								<strong><span class="badge" ng-bind="proposal.opentill"></span></strong>
							</td>
							<td md-cell>
								<span class="label {{proposal.class}} label-default" ng-bind="proposal.status"></span>
							</td>
							<td md-cell>
								<strong ng-bind-html="proposal.total | currencyFormat:cur_code:null:true:cur_lct"></strong>
							</td>
							<td md-cell>
								<div style="margin-top: 5px;" data-toggle="tooltip" data-placement="left" data-container="body" title="" data-original-title="Created by: {{proposal.staff}}" class="assigned-staff-for-this-lead user-avatar"><img ng-src="<?php echo base_url('uploads/images/{{proposal.staffavatar}}')?>" alt="{{proposal.staff}}"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</md-table-container>
			<md-table-pagination ng-show="proposals.length > 0" md-limit="proposal_list.limit" md-limit-options="limitOptions" md-page="proposal_list.page" md-total="{{proposals.length}}"></md-table-pagination>
			<md-content ng-show="!proposals.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>
		</md-content>

	</div>
</div>
<?php include_once(APPPATH . 'Views/area/inc/footer.php');?>