<?php $appconfig = get_appconfig(); ?>
<?php $number = get_number('expenses',$expense['id'],'expense','expense');?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> <!-- Encoding utf8 chartset for the pdf -->
	<link rel='stylesheet prefetch' href='<?php echo base_url('assets/lib/bootstrap/dist/css/bootstrap.min.css'); ?>'> <!-- Bootstrap CSS file link -->
	<style>
		.list-group-item.active,
		.list-group-item.active:focus,
		.list-group-item.active:hover {
			z-index: 2;
			color: #fff;
			background-color: #555;
			border-color: #555;
		}
		.page-header.row {
			margin: 30px 0 10px 0 !important;
		}
		.page-header .logo {
			padding-right: 0 !important;
			padding-left: 0 !important;
		}
		.panel {
			box-shadow: 0 1px 1px rgb(255, 255, 255) !important;
		}
		/*Custom CSS write/paste here*/
	</style> 
</head>
<?php
// Internal expense or not
if ($expense[ 'internal' ] == '1') {
	//Check Internal Expense or not
	$customer = lang2('internal'). ' ' .lang2('expense');
} else {
	if ( $expense[ 'customer' ] == NULL ) { 
		// Customer type i.e individual or company
		$customer = $expense[ 'individual' ];
	} else {
		$customer = $expense[ 'customer' ];
	}
}
// Expense logo
$logo =  file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo']);
if(file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'])) {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'];
} else {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['logo']; // Use app logo, if expense logo is not found
}
?>
<body>
	<div class="container">
		<div class="row">
			<div class="page-header row">
				<div class="col-md-3 col-sm-3 col-xs-3 logo">
					<img height="75px" src="<?php echo $logo ?>" alt=""><br>
					<small>
						<strong><?php echo ($settings['company']) ? $settings['company'] : ""	; ?></strong> <!-- Company Name from app settings -->
					</small><br>
					<!-- Company Address -->
					<small style="font-size: 11px;">
						<?php echo '' .($settings[ 'town' ] ? $settings[ 'town' ].'/' : '').($settings[ 'city' ] ? $settings[ 'city' ].'/':'').($state ? $state.'/' : '').($country ? $country.'-':'').($settings[ 'zipcode' ] ? $settings[ 'zipcode' ] : '')  ?>
					</small><br>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4" style="padding-top: 2%;">
					<small>
						<!-- Company Taxoffice & vatnumber -->
						<!-- You can remove these if don't want to display these in pdf -->
						<strong style="font-size: 11px;"><?php echo  ($settings[ 'taxoffice' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').':' : '') ?></strong><?php echo $settings[ 'taxoffice' ]; ?>
					</small><br>
					<small>
						<?php echo '<strong style="font-size: 11px;">'.($settings[ 'vatnumber' ] ? $appconfig['tax_label'].' '.lang( 'vatnumber' ).':' : '') .'</strong>' . $settings[ 'vatnumber' ] . ''; ?>
					</small>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<small class="" style="position:relative;top:20px;right:20px;padding-right: 40px;text-align: left;float: right;">
						<strong><span class="text-uppercase"><?php echo lang2('expense') ?></span> <br><!--  Expense Title in your language -->
							#<?php echo ''.$number.'' ?>  <!-- Expense number -->
							<?php echo ''. ($expense['number'] ? "<br>". lang( 'reference' ).':' : '') . $expense['number'] . '' ?>
							<br> <!-- Expense reference -->
							<?php echo '' . lang( 'expense' ) .' '.lang( 'date' ). ': ' . date(get_dateFormat(), strtotime($expense['created'])) . '' ?> <!-- Expense created date -->
						</strong>
					</small>
				</div>
			</div>
			<div class="col-md-12 nav panel" style="padding-bottom: 20px;box-shadow: unset;padding-right: 0;">
				<!-- Internal Expense -->
				<?php if ($expense[ 'internal' ] == '1') { ?> 
					<div style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
						<strong><?php echo lang2('internal'). ' '.lang2('expense') ?></strong>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
				<?php } else { ?>
				<div style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
					<strong><?php echo lang2('customer').' '.lang2('expense') ?></strong>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<br>
					<small>
						<strong><?php echo $customer; ?></strong>
					</small><br>
					<small>
						<!-- Customer Details -->
						<?php echo ($expense[ 'billing_street' ] ? $expense[ 'billing_street' ].'/' : ''); ?><?php echo ($expense[ 'billing_city' ] ? $expense[ 'billing_city' ].'/' : ''); ?><?php echo ($billing_state ? $billing_state.'/' : '') ; ?><?php echo ($billing_country ? $billing_country:''); ?><?php echo ($expense[ 'billing_zip' ] ? '- '.$expense[ 'billing_zip' ] : ''); ?> 
					</small><br>
					<small>
						<?php echo $expense[ 'customer_phone' ]; ?>
					</small><br>
					<small>
						<!-- Customer vatoffice & vat number -->
						<strong><?php echo  ($expense[ 'customer_tax' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').': ' : '') ?></strong><?php echo $expense[ 'customer_tax' ]; ?>
					</small><br>
					<small>
						<strong><?php echo (($expense[ 'customer_taxnum' ] == 0 || !$expense[ 'customer_taxnum' ]) ? '': ($appconfig['tax_label'].' '.lang2('vatnumber').': '.$expense[ 'customer_taxnum' ]))  ?></strong>
					</small>
				</div>
			<?php } ?><br>
			</div>
			<table class="table panel" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 3%;">
				<thead style="border-top: 2px solid #e4e4e4;">
					<tr>
						<th class="col-md-6">
							<?php echo  lang( 'invoiceitemdescription' ) ?>
						</th>
						<th class="col-md-1">
							<?php echo  lang( 'quantity' ) ?>
						</th>
						<th class="col-md-1">
							<?php echo  lang( 'price' ) ?>
						</th>
						<th class="col-md-1">
							<?php echo  $appconfig['tax_label'] ?> <!-- Tax label from app settings -->
						</th>
						<th class="col-md-2" style="text-align: center;">
							<?php echo  lang( 'total' ) ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($items as $item){ ?>
					<tr style="border-bottom: 1px solid #eaeaea;">
						<td class="text-left">
							<?php echo '<b>' . $item[ 'name' ] . '</b><br><small style="font-size:10px;line-height:10px">' . $item[ 'description' ] . '</small>'; ?>
						</td>
						<td class="text-left">
							<?php echo '' . number_format( $item[ 'quantity' ], 2, '.', ',' ) . '' ?>
						</td>
						<td class="text-left">
							<?php echo '' . number_format( $item[ 'price' ], 2, '.', ',' ) . ''; ?>
						</td>
						<td class="text-left">
							<?php echo '' . number_format( $item[ 'tax' ], 2, '.', '.' ) . '%';?>
						</td>
						<td class="text-left" style="text-align: center;">
							<?php echo '' . number_format( $item[ 'total' ], 2, '.', ',' ) . ' ' . currency . '';?>
								<!-- You can change Number format as per your requirement
								i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
								example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="col-md-12 col-xs-12 col-sm-12 panel" style="padding:0px;margin-top: 4%;page-break-inside: avoid;">
				<div class="col-md-6 col-xs-6 col-sm-6 panel pull-left" style="padding: 0;padding: 0;border: 1px solid #90909045;border-radius: 4px;width: 55%;page-break-inside: avoid;">
					<div class="panel-heading text-uppercase" style="border-bottom: 1px solid #90909045;    background: whitesmoke;">
						<strong>
							<?php echo lang( 'paidvia' ); ?>
						</strong>
					</div>
					<table class="table" style="page-break-inside: avoid;">
						<thead style="">
							<tr>
								<th class="col-md-12"><?php echo  $expense['account'] ?></th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="col-md-5 col-xs-5 col-sm-5 pull-right" style="padding: 0">
					<div class="list-group">
						<li class="list-group-item">
							<strong>
								<?php echo lang( 'subtotal' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . number_format( $expense[ 'sub_total' ], 2, '.', ',' ) . ' ' . currency . '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo $appconfig['tax_label']; ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . number_format( $expense[ 'total_tax' ], 2, '.', ',' ) . ' ' . currency . '' ?>
							</span>
						</li>
						<li class="list-group-item active">
							<strong>
								<?php echo lang( 'total' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . number_format( $expense[ 'amount' ], 2, '.', ',' ) . ' ' . currency . ''; ?>
							</span>
						</li>
					</div>
				</div>
			</div>
		</div>
			<?php if (count($otherFiles) > 0) { ?>
			<div style="padding-bottom: 10px;font-size: 17px;margin-top: 10px;border-top: 1px solid #efefef;border-bottom: 1px solid #dddddd;">
				<strong><?php echo lang2('attached'). ' ' .lang2('files') ?></strong>
			</div>
			<table class="table panel" style="box-shadow: 0 1px 1px rgb(255, 255, 255);border-bottom: 1px solid #dddddd;">
				<tbody>
					<?php foreach($otherFiles as $file){ ?>
					<tr>
						<td class="text-left">
							<?php echo '' . $file[ 'file_name' ] . ''; ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
			<?php if (count($images) > 0) { ?>
				<div style="border-bottom: 1px solid #eee;padding-bottom: 10px;margin-bottom: 5%;font-size: 17px;margin-top: 40px;">
					<strong><?php echo lang2('attached'). ' ' .lang2('receipts') ?></strong>
				</div>
				<?php foreach($images as $file) { ?>
					<img src="<?php echo $file['path'] ?>" style="max-width: 100%;"> <br><br>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</body>
</html>