<md-content class="md-padding bg-white">

	<div class="col-md-6">

		<md-input-container class="md-block">

			<label><?php echo lang2('company') ?></label>

			<input required name="company" ng-model="settings_detail.company">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('email') ?></label>

			<input required name="company" ng-model="settings_detail.email">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('country') ?></label>

			<md-select placeholder="<?php echo lang2('country'); ?>" ng-model="settings_detail.country_id" ng-change="getStates(settings_detail.country_id)" style="min-width: 200px;">

				<md-option ng-value="country.id" ng-repeat="country in countries">

					{{country.shortname}}</md-option>

			</md-select>

			<br>

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('state') ?></label>

			<md-select placeholder="<?php echo lang2('state'); ?>" ng-model="settings_detail.state_id" name="state_id" style="min-width: 200px;">

				<md-option ng-value="state.id" ng-repeat="state in states">{{state.state_name}}

				</md-option>

			</md-select>

			<br>

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('city') ?></label>

			<input required ng-model="settings_detail.city">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('address') ?></label>

			<textarea name="address" class="form-control" ng-model="settings_detail.address"></textarea>

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('town') ?></label>

			<input required ng-model="settings_detail.town">

		</md-input-container>

		<md-input-container class="md-block">
			<label>Ramo de atividade</label>
			<input required ng-model="settings_detail.ramo_atividade">
		</md-input-container>

	

	</div>

	<div class="col-md-6">

		<md-input-container class="md-block">

			<label><?php echo lang2('crmname') ?></label>

			<input required ng-model="settings_detail.crm_name">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('zipcode') ?></label>

			<input required ng-model="settings_detail.zipcode">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('phone') ?></label>

			<input required ng-model="settings_detail.phone">

		</md-input-container>

		<md-input-container class="md-block">

			<label><?php echo lang2('fax') ?></label>

			<input required ng-model="settings_detail.fax">

		</md-input-container>

		<md-input-container class="md-block">
			<label>Descrição da empresa e seus diferenciais</label>
			<textarea rows="3" required ng-model="settings_detail.descricao_empresa"></textarea>
		</md-input-container>

		<md-input-container class="md-block">
			<label>Sobre a empresa</label>
			<textarea rows="3" required ng-model="settings_detail.resumo"></textarea>
		</md-input-container>

	</div>



</md-content>