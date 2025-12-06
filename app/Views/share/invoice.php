<!DOCTYPE html>
<html lang="<?php echo lang2('lang_code'); ?>">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/logo-fav.png">
	<title><?php echo $title; ?></title>
	<script src="<?php echo base_url('assets/lib/angular/angular.min.js'); ?>"></script>
   	<script src="<?php echo base_url('assets/lib/angular/angular-animate.min.js'); ?>"></script>
   	<script src="<?php echo base_url('assets/lib/angular/angular-aria.min.js'); ?>"></script>
   	<script src="<?php echo base_url('assets/lib/angular/i18n/angular-locale_en-us.js'); ?>"></script>
	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/ciuis.css" type="text/css"/>
	<style type="text/css">
		tr, td, th, small, span, strong {
			font-family: DejaVu Sans; sans-serif;
		}
	</style>
</head>
<body ng-controller>
	<md-toolbar class="toolbar-white">
	  <div class="md-toolbar-tools">
		<md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">
		  <md-icon><i class="ico-ciuis-invoices text-muted"></i></md-icon>
		</md-button>
		<h2 flex md-truncate><?php echo get_number('invoices',$invoice['id'],'invoice','inv')?></h2>
	  </div>
	</md-toolbar>
	<md-content class="bg-white invoice" style="height: 100%;">
		<div class="invoice-header col-md-12">
			<div class="invoice-from col-md-4 col-xs-12">
				<small><?php echo  lang2('from'); ?></small>
				<address class="m-t-5 m-b-5">
					<strong><?php echo $settings['company'] ?></strong><br>
					<?php echo $settings['address'] ?><br>
					<?php echo lang2('phone')?>:</b> <?php echo $settings['phone'] ?><br>
				</address>
			</div>
			<div class="invoice-to col-md-4 col-xs-12">
				<small><?php echo  lang2('to'); ?></small>
				<address class="m-t-5 m-b-5">
					<strong><?php if($invoice['customercompany']===NULL){echo $invoice['namesurname'];} else echo $invoice['customercompany']; ?></strong><br>
					<?php echo $invoice['customeraddress']; ?><br>
					<?php echo lang2('phone') ?>: <?php echo $invoice['phone'] ?><br>
				</address>
			</div>
			<div class="invoice-date col-md-4 col-xs-12">
				<div class="date m-t-5"><?php echo _adate($invoice['created']) ?></div>
				<div class="invoice-detail">
					<?php echo $invoice['serie'] ?>#<?php echo $invoice['no'] ?><br>
				</div>
			</div>
		</div>
		<div class="invoice-content col-md-12 md-p-0 xs-p-0 sm-p-0 lg-p-0">
			<div class="table-responsive">
				<table class="table table-invoice">
					<thead>
						<tr>
							<th><?php echo lang2('product') ?></th>
							<th><?php echo lang2('quantity') ?></th>
							<th><?php echo lang2('price') ?></th>
							<th><?php echo lang2('tax') ?></th>
							<th><?php echo lang2('discount') ?></th>
							<th><?php echo lang2('total') ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($items as $item){?>
						<tr ng-repeat="item in invoice.items">
							<td><span><?php echo $item['name'] ?></span><br><small><?php echo nl2br($item['description']) ?></small></td>
							<td><?php echo $item['quantity'] ?></td>
							<td><?php echo '' . amount_format($item[ 'price' ]) . ''; ?></td>
							<td><?php echo '' . amount_format($item[ 'tax' ]) . '%';?></td>
							<td><?php echo '' . amount_format($item[ 'discount' ]) . '%';?></td>
							<td><?php echo '' . amount_format($item[ 'total' ]) . '';?></td>
						</tr>
					<?php }?>
					</tbody>
				</table>
			</div>
			<div class="invoice-price" style="bottom: 0px; position: fixed;">
				<div class="invoice-price-left">
					<div class="invoice-price-row">
						<div class="sub-price">
							<small><?php echo lang2('subtotal') ?></small>
							<span><?php echo '' . amount_format($invoice[ 'sub_total' ], true). '' ?></span>
						</div>
						<div class="sub-price">
							<i class="ion-plus-round"></i>
						</div>
						<div class="sub-price">
							<small><?php echo lang2('tax') ?></small>
							<span><?php echo '' . amount_format($invoice[ 'total_tax' ], true). '' ?></span>
						</div>
						<div class="sub-price">
							<i class="ion-minus-round"></i>
						</div>
						<div class="sub-price">
							<small><?php echo lang2('discount') ?></small>
							<span><?php echo '' . amount_format($invoice[ 'total_discount' ], true). '' ?></span>
						</div>
					</div>
				</div>
				<div class="invoice-price-right">
					<small><?php echo lang2('total') ?></small>
					<span><?php echo '' . amount_format($invoice[ 'total' ], true). ''; ?></span>
				</div>
			</div>
		</div>
	</md-content>
</body>
</html>