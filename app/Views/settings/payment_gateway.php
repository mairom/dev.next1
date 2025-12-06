 <md-content class="md-padding bg-white">
 	<div class="col-md-12">
 		<div class=" col-md-2">
 		</div>
 		<div class=" col-md-10">
 			<md-table-container>
 				<table md-table md-progress="promise">
 					<thead md-head>
 						<tr md-row>
 							<th md-column><span>#</span></th>
 							<th md-column><span><?php echo lang2('gateway_name'); ?></span></th>
 							<th md-column><span><?php echo lang2('status'); ?></span></th>
 							<th md-column><span><?php echo lang2('updated_at'); ?></span></th>
 							<th md-column><span><?php echo lang2('action'); ?></span></th>
 						</tr>
 					</thead>
 					<tbody md-body>
 						<tr class="select_row" md-row ng-repeat="gateway in gateways">
 							<td md-cell style="padding: 15px 10px;">
 								<img src="<?php echo base_url('assets/img/payment-modes/{{gateway.image}}')?>">
 							</td>
 							<td md-cell>
 								<span ng-bind="gateway.name"></span>
 							</td>
 							<td md-cell>
 								<strong ng-show="gateway.active" class="badge green"><?php echo lang2('active'); ?></strong>
 								<strong ng-show="!gateway.active" class="badge red"><?php echo lang2('inactive'); ?></strong>
 							</td>
 							<td md-cell>
 								<span ng-bind="gateway.updated_at"></span>
 							</td>
 							<td md-cell>
 								<?php if (check_privilege('settings', 'edit')) { ?> 
 									<md-icon ng-click="paymentGateway(gateway)" md-menu-align-target class="ion-compose"></md-icon>
 								<?php } ?>
 							</td>
 						</tr>
 					</tbody>
 				</table>
 			</md-table-container>
 			<br><br><br>
 		</div>
 	</div>
 </md-content>