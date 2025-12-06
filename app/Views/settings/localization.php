 <md-content class="md-padding bg-white">
 	<div class="col-md-6">
 		<md-input-container class="md-block">
 			<label><?php echo lang2('language'); ?></label>
 			<md-select placeholder="<?php echo lang2('language'); ?>"
 				ng-model="settings_detail.languageid" style="min-width: 200px;">
 				<md-option ng-value="language.foldername" ng-repeat="language in languages">
 				{{language.name}}</md-option>
 			</md-select>
 			<br>
 		</md-input-container>
 		<md-input-container class="md-block">
 			<label><?php echo lang2('defaulttimezone')?></label>
 			<md-select ng-model="settings_detail.default_timezone">
 				<md-optgroup ng-repeat="timezone in timezones" label="{{timezone.group}}">
 					<md-option ng-value="zone.value" ng-repeat="zone in timezone.zones">
 					{{zone.value}}</md-option>
 				</md-optgroup>
 			</md-select>
 		</md-input-container>
 	</div>
 	<div class="col-md-6">
 		<md-input-container class="md-block" flex-gt-xs>
 			<label><?php echo lang2('dateformat'); ?></label>
 			<md-select
 			ng-init="dateformats = [{value: 'yy.mm.dd',name: 'Y.M.D'}, {value: 'dd.mm.yy',name: 'D.M.Y'}, {value: 'yy-mm-dd',name: 'Y-M-D'}, {value: 'dd-mm-yy',name: 'D-M-Y'}, {value: 'yy/mm/dd',name: 'Y/M/D'}, {value: 'dd/mm/yy',name: 'D/M/Y'}];"
 			required placeholder="<?php echo lang2('dateformat'); ?>"
 			ng-model="settings_detail.dateformat" name="dateformat">
 			<md-option ng-value="dateformat.value" ng-repeat="dateformat in dateformats"><span
 				class="text-uppercase">{{dateformat.name}}</span></md-option>
 			</md-select>
 			<br>
 		</md-input-container>
 	</div>
 </md-content>