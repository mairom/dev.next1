<?php if (check_privilege('settings', 'edit')) { ?>

	<md-content ng-show="viewRole == true" class="md-padding bg-white">

		<md-button ng-click="addRole()" class="md-raised md-primary pull-right successButton" ng-disabled="getRoles == true">

			<span ng-hide="getRoles == true"><?php echo lang2('create') . ' ' . lang2('role'); ?></span>

			<md-progress-circular class="white" ng-show="getRoles == true" md-mode="indeterminate" md-diameter="20">

			</md-progress-circular>

		</md-button>

	</md-content>

<?php } ?>

<md-content ng-show="viewRole == true" class="md-padding bg-white">

	<md-table-container>

		<table md-table md-progress="promise">

			<thead md-head>

				<tr md-row>

					<th md-column><?php echo lang2('role') . ' ' . lang2('name'); ?></th>

					<th md-column><?php echo lang2('role') . ' ' . lang2('type'); ?></th>

					<th md-column><?php echo lang2('updated_at'); ?></th>

					<th md-column><?php echo lang2('action'); ?></th>

				</tr>

			</thead>

			<tbody md-body>

				<tr class="select_row" md-row ng-repeat="role in roles">

					<td md-cell>

						<span ng-bind="role.role_name"></span>

					</td>

					<td md-cell>

						<span ng-bind="role.role_type"></span>

					</td>

					<td md-cell>

						<span ng-bind="role.updated_at"></span>

					</td>

					<td md-cell>

						<span ng-if = "roles_export.length > 0" ng-click="modalPermissaoExport(role)">
							
							<md-icon style="font-size: 21px;"><i class="fas fa-tasks"></i></md-icon>
						</span>



						<?php if (check_privilege('settings', 'edit')) { ?>

							<span ng-click="get_role(role.role_id)">

								<md-progress-circular ng-show="editLoader == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>

								<md-icon ng-hide="editLoader == true" md-menu-align-target class="md-raised md-primary mdi mdi-edit" style="margin: auto 3px auto 0;">

								</md-icon>

							</span>

						<?php }
						if (check_privilege('settings', 'delete')) { ?>

							<span ng-click="delete_role(role.role_id)">

								<md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">

								</md-icon>

							</span>

						<?php } ?>

					</td>

				</tr>

			</tbody>

		</table>

	</md-table-container>

</md-content>

<br />

<md-content ng-show="createRole == true" class="bg-white">

	<md-button ng-click="create_role()" class="md-raised md-primary btn-report pull-right" ng-disabled="creatingRole == true">

		<span ng-hide="creatingRole == true"><?php echo lang2('create'); ?></span>

		<md-progress-circular class="white" ng-show="creatingRole == true" md-mode="indeterminate" md-diameter="20">

		</md-progress-circular>

	</md-button>

	<md-button ng-click="cancel_role()" class="md-raised md-primary btn-report pull-right">

		<?php echo lang2('cancel'); ?>

	</md-button>

</md-content>

<md-content ng-show="createRole == true" class="bg-white">

	<div layout-gt-xs="row" layout-padding>

		<md-input-container class="md-block" flex-gt-xs>

			<label><?php echo lang2('role') . ' ' . lang2('name'); ?></label>

			<input required ng-model="newrole.new_role_name">

		</md-input-container>

		<md-input-container class="md-block" flex-gt-xs>

			<label><?php echo lang2('role') . ' ' . lang2('type') ?></label>

			<md-select ng-model="newrole.usertype" ng-change="get_permissions_by_type(newrole.usertype)" name="usertype">

				<md-option value="admin" selected><?php echo lang2('admin') ?></md-option>

				<md-option value="staff"><?php echo lang2('staff') ?></md-option>

				<md-option value="other"><?php echo lang2('other') ?></md-option>

			</md-select>

		</md-input-container>

	</div>

	<md-table-container>

		<table md-table md-progress="promise">
			<thead md-head>
				<tr md-row>
					<th md-column>
						<?php echo lang2('staffdetailsdescription'); ?> (
						<md-checkbox ng-change="get_permissions_by_type(newrole.usertype)" md-no-ink aria-label="view_own" ng-model="permission_all" class="md-primary">
						</md-checkbox> <?php echo lang2('select') . ' ' . lang2('all') ?>)
					</th>
					<th md-column><?php echo lang2('view') . ' ' . lang2('own'); ?></th>
					<th md-column><?php echo lang2('view') . ' ' . lang2('all'); ?></th>
					<th md-column><?php echo lang2('create'); ?></th>
					<th md-column><?php echo lang2('edit'); ?></th>
					<th md-column><?php echo lang2('delete'); ?></th>
				</tr>
			</thead>

			<tbody md-body>
				<tr ng-show="permission.key != 'quotes'" class="select_row" md-row ng-repeat="permission in all_permissions">
					<td md-cell>
						<span ng-bind="permission.permission_key"></span>
					</td>

					<td md-cell>
						<span>
							<md-checkbox ng-show="permission.key != 'staff' && permission.key != 'accounts' && permission.key != 'report' && permission.key != 'emails' && newrole.usertype != 'other' && permission.key != 'settings'" md-no-ink aria-label="view_own" ng-model="permission.permission_view_own" class="md-primary">
							</md-checkbox>
						</span>
					</td>

					<td md-cell>
						<span>
							<md-checkbox md-no-ink aria-label="view_all" ng-model="permission.permission_view_all" class="md-primary">
							</md-checkbox>
						</span>
					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-if="newrole.usertype == 'admin' || newrole.usertype == 'other'" ng-show="permission.key != 'report' && permission.key != 'emails' && newrole.usertype != 'other'" md-no-ink aria-label="create" ng-model="permission.permission_create" class="md-primary" ng-change="permission.permission_create?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

							<md-checkbox ng-if="newrole.usertype == 'staff'" ng-show="permission.key != 'report' && permission.key != 'emails' && newrole.usertype != 'other' && permission.key != 'staff'" md-no-ink aria-label="create" ng-model="permission.permission_create" class="md-primary" ng-change="permission.permission_create?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

						</span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-if="newrole.usertype == 'admin' || newrole.usertype == 'other'" ng-show="permission.key != 'report' && newrole.usertype != 'other'" md-no-ink aria-label="edit" ng-model="permission.permission_edit" class="md-primary" ng-change="permission.permission_edit?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

							<md-checkbox ng-if="newrole.usertype == 'staff'" ng-show="permission.key != 'report' && newrole.usertype != 'other' && permission.key != 'staff'" md-no-ink aria-label="edit" ng-model="permission.permission_edit" class="md-primary" ng-change="permission.permission_edit?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

						</span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-if="newrole.usertype == 'admin' || newrole.usertype == 'other'" ng-show="permission.key != 'report' && permission.key != 'emails' && newrole.usertype != 'other'" md-no-ink aria-label="delete" ng-model="permission.permission_delete" class="md-primary" ng-change="permission.permission_delete?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

							<md-checkbox ng-if="newrole.usertype == 'staff'" ng-show="permission.key != 'report' && permission.key != 'emails' && newrole.usertype != 'other' && permission.key != 'staff'" md-no-ink aria-label="delete" ng-model="permission.permission_delete" class="md-primary" ng-change="permission.permission_delete?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

						</span>

					</td>

				</tr>

			</tbody>

		</table>

	</md-table-container>

	<br><br><br>

</md-content>

<md-content ng-show="editRole == true" class="bg-white">

	<md-button ng-click="update_role(role_id)" class="md-raised md-primary btn-report pull-right" ng-disabled="updatingRole == true">

		<span ng-hide="updatingRole == true"><?php echo lang2('update'); ?></span>

		<md-progress-circular class="white" ng-show="updatingRole == true" md-mode="indeterminate" md-diameter="20">

		</md-progress-circular>

	</md-button>

	<md-button ng-click="cancel_role()" class="md-raised md-primary btn-report pull-right">

		<?php echo lang2('cancel'); ?>

	</md-button>

</md-content>

<md-content ng-show="editRole == true" class="bg-white">

	<div layout-gt-xs="row">

		<md-input-container class="md-block" flex-gt-xs>

			<label><?php echo lang2('role') . ' ' . lang2('name'); ?></label>

			<input required ng-model="role_name">

		</md-input-container>

		<md-input-container class="md-block" flex-gt-xs>

			<label><?php echo lang2('role') . ' ' . lang2('type') ?></label>

			<md-select ng-model="role_type" name="role_type" disabled>

				<md-option value="admin"><?php echo lang2('admin') ?></md-option>

				<md-option value="staff"><?php echo lang2('staff') ?></md-option>

				<md-option value="other"><?php echo lang2('other') ?></md-option>

			</md-select>

		</md-input-container>

	</div>

	<md-table-container>

		<table md-table md-progress="promise">

			<thead md-head>

				<tr md-row>

					<th md-column><?php echo lang2('staffdetailsdescription'); ?></th>

					<th md-column><?php echo lang2('view') . ' ' . lang2('own'); ?></th>

					<th md-column><?php echo lang2('view') . ' ' . lang2('all'); ?></th>

					<th md-column><?php echo lang2('create'); ?></th>

					<th md-column><?php echo lang2('edit'); ?></th>

					<th md-column><?php echo lang2('delete'); ?></th>

				</tr>

			</thead>

			<tbody md-body>

				<tr ng-show="permission.key != 'quotes'" class="select_row" md-row ng-repeat="permission in permissions">

					<td md-cell>

						<span ng-bind="permission.permission_key"></span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-show="permission.key != 'staff' && permission.key != 'accounts' && permission.key != 'report' && permission.key != 'emails' && permission.key != 'settings' && role_type != 'other'" md-no-ink aria-label="view_own" ng-model="permission.permission_view_own" class="md-primary">

							</md-checkbox>

						</span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox md-no-ink aria-label="view_all" ng-model="permission.permission_view_all" class="md-primary">

							</md-checkbox>

						</span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-if="role_type == 'admin' || role_type == 'other'" ng-show="permission.key != 'report' && permission.key != 'emails' && role_type != 'other'" md-no-ink aria-label="create" ng-model="permission.permission_create" class="md-primary" ng-change="permission.permission_create?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

							<md-checkbox ng-if="role_type == 'staff'" ng-show="permission.key != 'report' && permission.key != 'emails' && role_type != 'other' && permission.key != 'staff'" md-no-ink aria-label="create" ng-model="permission.permission_create" class="md-primary" ng-change="permission.permission_create?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

						</span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-if="role_type == 'admin' || role_type == 'other'" ng-show="permission.key != 'report' && role_type != 'other'" md-no-ink aria-label="edit" ng-model="permission.permission_edit" class="md-primary" ng-change="permission.permission_edit?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

							<md-checkbox ng-if="role_type == 'staff'" ng-show="permission.key != 'report' && role_type != 'other' && permission.key != 'staff'" md-no-ink aria-label="edit" ng-model="permission.permission_edit" class="md-primary" ng-change="permission.permission_edit?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

						</span>

					</td>

					<td md-cell>

						<span>

							<md-checkbox ng-if="role_type == 'admin' || role_type == 'other'" ng-show="permission.key != 'report' && permission.key != 'emails' && role_type != 'other'" md-no-ink aria-label="delete" ng-model="permission.permission_delete" class="md-primary" ng-change="permission.permission_delete?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

							<md-checkbox ng-if="role_type == 'staff'" ng-show="permission.key != 'report' && permission.key != 'emails' && role_type != 'other' && permission.key != 'staff'" md-no-ink aria-label="delete" ng-model="permission.permission_delete" class="md-primary" ng-change="permission.permission_delete?((!permission.permission_view_own && !permission.permission_view_all)? (permission.permission_view_own = true):''):''">

							</md-checkbox>

						</span>

					</td>

				</tr>

			</tbody>

		</table>

	</md-table-container>

	<br><br><br>

</md-content>