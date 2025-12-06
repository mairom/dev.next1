</md-content>
<script src="<?php echo base_url('assets/lib/jquery/jquery.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/Ciuis.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/moment.js/min/moment.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/bootstrap/dist/js/bootstrap.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/jquery.gritter/js/jquery.gritter.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/angular-datepicker/src/js/angular-datepicker.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js')?>"></script>
<script src="<?php echo base_url('assets/lib/material/angular-material.min.js')?>"></script>
<script src="<?php echo base_url('assets/lib/currency-format/currency-format.min.js')?>"></script>
<script src="<?php echo base_url('assets/lib/angular-datetimepicker/angular-material-datetimepicker.min.js')?>"></script>
<script src="<?php echo base_url('assets/lib/data-table/md-data-table.min.js'); ?>"></script> 
<script type="text/ng-template" id="calendar.html">
	<div class="header">
		<i class="fa fa-angle-left ion-chevron-left" ng-click="previous()"></i>
		<span>{{month.format("MMMM, YYYY")}}</span>
		<i class="fa fa-angle-right ion-chevron-right" ng-click="next()"></i>
	</div>
	<div class="week names">
		<span class="day"><?php echo mb_substr(lang2('sunday'), 0, 3, 'UTF-8'); ?></span>
		<span class="day"><?php echo mb_substr(lang2('monday'), 0, 3, 'UTF-8'); ?></span>
		<span class="day"><?php echo mb_substr(lang2('tuesday'), 0, 3, 'UTF-8'); ?></span>
		<span class="day"><?php echo mb_substr(lang2('wednesday'), 0, 3, 'UTF-8'); ?></span>
		<span class="day"><?php echo mb_substr(lang2('thursday'), 0, 3, 'UTF-8'); ?></span>
		<span class="day"><?php echo mb_substr(lang2('friday'), 0, 3, 'UTF-8'); ?></span>
		<span class="day"><?php echo mb_substr(lang2('saturday'), 0, 3, 'UTF-8'); ?></span>
	</div>
	<div class="week" ng-repeat="week in weeks">
		<span class="day" ng-class="{ today: day.isToday, 'different-month': !day.isCurrentMonth, selected: day.date.isSame(selected) }" ng-click="select(day)" ng-repeat="day in week.days">{{day.number}}</span>
	</div>
</script>
<script src="<?php echo base_url('assets/js/AreaAngular.js'); ?>"></script>
<script>
	var CURRENCY = "<?php echo currency ?>";
	var UPIMGURL = "<?php echo base_url('uploads/images/'); ?>";
	var IMAGESURL = "<?php echo base_url('assets/img/'); ?>";
	var SETFILEURL = "<?php echo base_url('uploads/ciuis_settings/') ?>";
	var NTFTITLE = "<?php echo lang2('notification')?>";
	var TODAYDATE = "<?php echo date('Y.m.d ')?>";
	var LOCATE_SELECTED = "en_us";
</script>
</body>
</html>