<script type="text/javascript">
	(function umd(root, name, factory)
	{
		'use strict';
		if ('function' === typeof define && define.amd) {
			define(name, ['jquery'], factory);
		} else {
			root[name] = factory();
		}
	}
	(this, 'CiuisInvoiceStats', function UMDFactory()
	{
		'use strict';
		var ReportOverview = ReportOverviewConstructor;
		reportCircleGraph();
		return ReportOverview;
		function ReportOverviewConstructor(options) {
			var factory = {
				init: init
			},
			_elements = {
				$element: options.element
			};
			init();
			return factory;
			function init() {
				_elements.$element.append($(getTemplateString()));

				$('.invoice-percent').percentCircle({
					width: 130,
					trackColor: '#ececec',
					barColor: '#22c39e',
					barWeight: 3,
					endPercent: 0.<?php echo $ofx ?>,
					fps: 60
				});
				$('.invoice-percent-2').percentCircle({
					width: 130,
					trackColor: '#ececec',
					barColor: '#ee7a6b',
					barWeight: 3,
					endPercent: 0.<?php echo $ofy ?>,
					fps: 60
				});

				$('.invoice-percent-3').percentCircle({
					width: 130,
					trackColor: '#ececec',
					barColor: '#808281',
					barWeight: 3,
					endPercent: 0.<?php echo $vgy ?>,
					fps: 60
				});
			}
			function getTemplateString()
			{
				return [
				'<div>',
				'<div class="row">',
				'<div class="col-md-12">',
				'<div style="border-top-left-radius: 10px;" class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">',
				'<div class="box-header text-uppercase text-bold"><?php echo lang2('totalinvoice'); ?></div>',
				'<div class="box-content">',
				'<div class="sentTotal">{{totalinvoicesayisi}}</div>'.replace(/{{totalinvoicesayisi}}/, options.data.totalinvoicesayisi),
				'</div>',
				'<div class="box-foot">',
				'<div class="sendTime box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo currency;?> <?php switch($settings['unitseparator']){case ',': echo number_format($fam, 2, ',', '.');break;case '.': echo number_format($fam, 2, '.', ',');break;}?></strong></span></div>'.replace(/{{date}}/, options.data.date),
				'</div>',
				'</div>',
				'<div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">',
				'<div class="box-header text-uppercase text-bold"><?php echo lang2('paid'); ?></div>',
				'<div class="box-content invoice-percent">',
				'<div class="percentage">%<?php echo $ofx ?></div>',
				'</div>',
				'<div class="box-foot">',
				'<span class="arrow arrow-up"></span>',
				'<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo currency;?> <?php switch($settings['unitseparator']){case ',': echo number_format($ofv, 2, ',', '.');break;case '.': echo number_format($ofv, 2, '.', ',');break;}?></strong></span></div>',
				'<span class="arrow arrow-down"></span>',
				'<div class="box-foot-right"><br><span class="box-foot-stats""><strong><?php echo $otf ?></strong> (%<?php echo $ofx ?>)</span></div>',
				'</div>',
				'</div>',
				'<div class="ciuis-right-border-b1 ciuis-invoice-summaries-b1">',
				'<div class="box-header text-uppercase text-bold"><?php echo lang2('unpaidinvoice'); ?></div>',
				'<div class="box-content invoice-percent-2">',
				'<div class="percentage">%<?php echo $ofy ?></div>',
				'</div>',
				'<div class="box-foot">',
				'<span class="arrow arrow-up"></span>',
				'<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo currency;?> <?php switch($settings['unitseparator']){case ',': echo number_format($oft, 2, ',', '.');break;case '.': echo number_format($oft, 2, '.', ',');break;}?></strong></span></div>'.replace(/{{OdenmeyenInvoicesAmount}}/, options.data.OdenmeyenInvoicesAmount),
				'<span class="arrow arrow-down"></span>',
				'<div class="box-foot-right"><br><span class="box-foot-stats"><strong><?php echo $tef ?></strong> (%<?php echo $ofy ?>)</span></div>',
				'</div>',
				'</div>',
				'<div style="border-top-right-radius: 10px;" class="ciuis-invoice-summaries-b1">',
				'<div class="box-header text-uppercase text-bold"><?php echo lang2('overdue'); ?></div>',
				'<div class="box-content invoice-percent-3">',
				'<div class="percentage">%<?php echo $vgy ?></div>',
				'</div>',
				'<div class="box-foot">',
				'<span class="arrow arrow-up"></span>',
				'<div class="box-foot-left"><?php echo lang2('invoiceamount'); ?><br><span class="box-foot-stats"><strong><?php echo currency;?> <?php switch($settings['unitseparator']){case ',': echo number_format($vgf, 2, ',', '.');break;case '.': echo number_format($vgf, 2, '.', ',');break;}?></strong></span></div>'.replace(/{{VadesiDolanInvoices}}/, options.data.VadesiDolanInvoices),
				'<div class="box-foot-right"><br><span class="box-foot-stats"><strong><?php echo $vdf ?></strong> (%<?php echo $vgy ?>)</span></div>',
				'</div>',
				'</div>'
				].join('');
			}
		}
		function reportCircleGraph() {
			$.fn.percentCircle = function pie(options) {
				var settings = $.extend({
					width: 130,
					trackColor: '#fff',
					barColor: '#fff',
					barWeight: 3,
					startPercent: 0,
					endPercent: 1,
					fps: 60
				}, options);
				this.css({
					width: settings.width,
					height: settings.width
				});
				var _this = this,
				canvasWidth = settings.width,
				canvasHeight = canvasWidth,
				id = $('canvas').length,
				canvasElement = $('<canvas id="' + id + '" width="' + canvasWidth + '" height="' + canvasHeight + '"></canvas>'),
				canvas = canvasElement.get(0).getContext('2d'),
				centerX = canvasWidth / 2,
				centerY = canvasHeight / 2,
				radius = settings.width / 2 - settings.barWeight / 2,
				counterClockwise = false,
				fps = 500 / settings.fps,
				update = 0.01;
				this.angle = settings.startPercent;
				this.drawInnerArc = function (startAngle, percentFilled, color) {
					var drawingArc = true;
					canvas.beginPath();
					canvas.arc(centerX, centerY, radius, (Math.PI / 180) * (startAngle * 360 - 90), (Math.PI / 180) * (percentFilled * 360 - 90), counterClockwise);
					canvas.strokeStyle = color;
					canvas.lineWidth = settings.barWeight - 2;
					canvas.stroke();
					drawingArc = false;
				};
				this.drawOuterArc = function (startAngle, percentFilled, color) {
					var drawingArc = true;
					canvas.beginPath();
					canvas.arc(centerX, centerY, radius, (Math.PI / 180) * (startAngle * 360 - 90), (Math.PI / 180) * (percentFilled * 360 - 90), counterClockwise);
					canvas.strokeStyle = color;
					canvas.lineWidth = settings.barWeight;
					canvas.lineCap = 'round';
					canvas.stroke();
					drawingArc = false;
				};
				this.fillChart = function (stop) {
					var loop = setInterval(function () {
						canvas.clearRect(0, 0, canvasWidth, canvasHeight);
						_this.drawInnerArc(0, 360, settings.trackColor);
						_this.drawOuterArc(settings.startPercent, _this.angle, settings.barColor);
						_this.angle += update;
						if (_this.angle > stop) {
							clearInterval(loop);
						}
					}, fps);
				};
				this.fillChart(settings.endPercent);
				this.append(canvasElement);
				return this;
			};
		}
		function getMockData() {
			return {
				totalinvoicesayisi: <?php echo $tfa ?>,
			};
		}
	}));
(function activateCiuisInvoiceStats($) {
	'use strict';
	var $el = $('.ciuis-invoice-summary');
	return new CiuisInvoiceStats({
		element: $el,
		data: {
			totalinvoicesayisi: <?php echo $tfa ?>,
		}
	});
}(jQuery));
</script>