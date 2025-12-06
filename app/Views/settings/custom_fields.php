 <md-content class="bg-white" style="padding:0px">
 	<md-toolbar class="toolbar-white">
 		<div class="md-toolbar-tools">
 			<h2 flex md-truncate class="pull-left" ><strong><?php echo lang2('custom_fields') ?></strong></h2>
 			<?php if (check_privilege('settings', 'create')) { ?> 
 				<md-button ng-click="CreateCustomField()" class="md-icon-button md-primary" aria-label="New">
 					<md-tooltip md-direction="top"><?php echo lang2('new_custom_field') ?></md-tooltip>
 					<md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
 				</md-button>
 			<?php } ?>
 		</div>
 	</md-toolbar>
 	<md-content ng-show="!customfieldLoader" class="bg-white" ng-cloak> 
 		<md-table-container ng-show="custom_fields.length > 0">
 			<table md-table  md-progress="promise">
 				<thead md-head md-order="customfield_list.order">
 					<tr md-row>
 						<th md-column><span><?php echo lang2('field_name'); ?></span></th>
 						<th md-column md-order-by="relation"><span><?php echo lang2('belongs_to'); ?></span></th>
 						<th md-column md-order-by="type"><span><?php echo lang2('type'); ?></span></th>
 						<th md-column md-order-by="active"><span><?php echo lang2('status'); ?></span></th>
 						<th md-column md-order-by="updated_on"><span><?php echo lang2('updated_at'); ?></span></th>
 						<th md-column><span><?php echo lang2('actions'); ?></span></th>
 					</tr>
 				</thead>
 				<tbody md-body>
 					<tr class="select_row" md-row ng-repeat="field in custom_fields | orderBy: customfield_list.order | limitTo: customfield_list.limit : (customfield_list.page -1) * customfield_list.limit"  class="cursor">
 						<td md-cell>
 							<strong>
 								<span ng-bind="field.name"></span>
 							</strong>
 						</td>
 						<td md-cell>
 							<strong><span class="text-uppercase" ng-bind="field.relation"></span></strong>
 						</td>
 						<td md-cell>
 							<strong><span class="text-uppercase" ng-bind="field.type"></span></strong>
 						</td>
 						<td md-cell>
 							<md-switch style="margin:unset!important" ng-change="UpdateCustomFieldStatus(field.id,field.active)" ng-model="field.active"><label><?php echo lang2('active');?></label></md-switch>
 						</td>
 						<td md-cell>
 							<strong ng-bind="field.updated_on"></strong>
 						</td>
 						<td md-cell>
 							<?php if (check_privilege('settings', 'edit')) { ?> 
 								<md-button class="md-icon-button" aria-label="Edit" ng-click="GetFieldDetail(field.id); FieldDetail()" ng-cloak>
 									<md-tooltip md-direction="bottom"><?php echo lang2('edit_field') ?></md-tooltip>
 									<md-icon><i class="ion-compose text-muted"></i></md-icon>
 								</md-button>
 							<?php } if (check_privilege('settings', 'delete')) { ?> 
 								<md-button class="md-icon-button" aria-label="Delete" ng-click="RemoveCustomField(field.id);" ng-cloak>
 									<md-tooltip md-direction="bottom"><?php echo lang2('remove_field') ?></md-tooltip>
 									<md-icon><i class="ion-android-delete text-muted"></i></md-icon>
 								</md-button>
 							<?php } ?>
 						</td>
 					</tr>
 				</tbody>
 			</table>
 		</md-table-container>
 		<md-table-pagination ng-show="custom_fields.length > 0" md-limit="customfield_list.limit" md-limit-options="limitOptions" md-page="customfield_list.page" md-total="{{custom_fields.length}}" ></md-table-pagination>
 		<md-content ng-show="!custom_fields.length" class="md-padding no-item-data"><?php echo lang2('notdata') ?></md-content>	
 	</md-content>
 </md-content>