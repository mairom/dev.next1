<?php $appconfig = get_appconfig(); ?>
<?php $number = get_number('invoices',$invoice['id'],'invoice','inv');?>
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
		/*Custom CSS write/paste here*/
		tr, td, th, small, span, strong {
			font-family: DejaVu Sans; sans-serif;
		}
	</style>
</head>
<?php
if ( $invoice[ 'customercompany' ] === NULL ) { // Customer type i.e individual or company
	$customer = $invoice[ 'namesurname' ];
} else {
	$customer = $invoice[ 'customercompany' ];
}

// Invoice logo
$logo =  file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo']);
if(file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'])) {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'];
} else {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['logo']; // Use app logo, if invoice logo is not found
}
?>
<body>
	<div class="container">
		<div class="row">
			<div class="page-header">
				<img height="90px" src="<?php echo $logo ?>" alt="">
				<small class="pull-right" style="position:relative;top:20px;right:20px;">
					<strong>
						<span class="text-uppercase"><?php echo lang2('invoice') ?></span> <br><!--  Invoice Title in your language -->
						#<?php echo '' . $number. '' ?><br> <!-- Invoice number -->
						<?php echo lang2('date').': '. //Date label
						 date(get_dateFormat(), strtotime($invoice['created'])) ?>  <!-- Invoice created date -->
					</strong>
				</small>
			</div> 
			<div class="col-md-12 nav panel" style="padding-bottom: 20px">
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<strong><?php echo lang2('from') ?></strong><br>
					<hr>
					<small>
						<strong><?php echo ($settings['company']) ? $settings['company'] : ""	; ?></strong> <!-- Company Name from app settings -->
					</small>
					<br>
					<small>
						<!-- Company Address -->
						<?php echo '' .($settings[ 'town' ] ? $settings[ 'town' ].'/' : '').($settings[ 'city' ] ? $settings[ 'city' ].'/':'').($state ? $state.'/' : '').($country ? $country.'-':'').($settings[ 'zipcode' ] ? $settings[ 'zipcode' ] : '')  ?>
					</small><br>
					<small>
						<!-- Company Phone -->
						<?php echo $settings[ 'phone' ]; ?>
					</small><br>
					<small>
						<!-- Company Taxoffice & vatnumber -->
						<!-- You can remove these if don't want to display these in pdf -->
						<strong><?php echo  ($settings[ 'taxoffice' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').':' : '') ?></strong><?php echo $settings[ 'taxoffice' ]; ?>
					</small><br>
					<small>
						<?php echo '<strong>'.($settings[ 'vatnumber' ] ? $appconfig['tax_label'].' '.lang( 'vatnumber' ).':' : '') .'</strong>' . $settings[ 'vatnumber' ] . ''; ?>
					</small>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<!-- Customer Details -->
					<strong><?php echo lang2('billed_to') ?></strong><br>
					<hr>
					<small>
						<strong><?php echo $customer; ?></strong>
					</small>
					<br>
					<small>
						<?php echo ($invoice[ 'billing_street' ] ? $invoice[ 'billing_street' ].'/' : ''); ?><?php echo ($invoice[ 'billing_city' ] ? $invoice[ 'billing_city' ].'/' : ''); ?><?php echo ($billing_state ? $billing_state.'/' : '') ; ?><?php echo ($billing_country ? $billing_country:''); ?><?php echo ($invoice[ 'billing_zip' ] ? '- '.$invoice[ 'billing_zip' ] : ''); ?> 
					</small><br>
					<small>
						<?php echo $invoice[ 'phone' ]; ?>
					</small><br>
					<small>
						<!-- Customer vatoffice & vat number -->
						<strong><?php echo  ($invoice[ 'taxoffice' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').': ' : '') ?></strong><?php echo $invoice[ 'taxoffice' ]; ?>
					</small><br>
					<small>
						<strong><?php echo (($invoice[ 'taxnumber' ] == 0 || !$invoice[ 'taxnumber' ]) ? '': ($appconfig['tax_label'].' '.lang2('vatnumber').': '.$invoice[ 'taxnumber' ]))  ?></strong>
					</small>
				</div>
			</div>
			<table class="table panel">
				<thead>
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
							<?php echo  lang( 'discount' ) ?>
						</th>
						<th class="col-md-1">
							<?php echo  $appconfig['tax_label'] ?> <!-- Tax label from app settings -->
						</th>
						<th class="col-md-2">
							<?php echo  lang( 'total' ) ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($items as $item){ ?>
					<tr>
						<td class="text-left">
							<?php echo '' . $item[ 'name' ] . '</b><br><small style="font-size:10px;line-height:10px">' . nl2br($item[ 'description' ])  . '</small>'; ?>
						</td>
						<td class="text-left">
							<?php echo '' . amount_format($item[ 'quantity' ]). '' ?>
						</td>
						<td class="text-left">
							<?php echo '' . amount_format($item[ 'price' ]) . ''; ?>
						</td>
						<td class="text-left">
							<?php echo '' . amount_format($item[ 'discount' ]) . '%';?>
						</td>
						<td class="text-left">
							<?php echo '' . amount_format($item[ 'tax' ]) . '%';?>
						</td>
						<td class="text-left">
							<?php echo '' . amount_format($item[ 'total' ]) . '';?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="col-md-12 col-xs-12 col-sm-12 panel" style="padding:0px;box-shadow: unset;page-break-inside: avoid;">
				<div class="col-md-6 col-xs-6 col-sm-6 panel pull-left" style="padding: 0px;border: unset;box-shadow: unset;">
				</div>
				<div class="col-md-5 col-xs-5 col-sm-5 pull-right" style="padding: 0;">
					<div class="list-group">
						<li class="list-group-item">
							<strong>
								<?php echo lang( 'subtotal' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($invoice[ 'sub_total' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo lang( 'linediscount' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($invoice[ 'total_discount' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo $appconfig['tax_label']; ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($invoice[ 'total_tax' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item active">
							<strong>
								<?php echo lang( 'total' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($invoice[ 'total' ], true). ''; ?>
							</span>
						</li>
					</div>
				</div>
			</div>
			<div class="row" style="page-break-inside: avoid;padding-top: 17%;">
				<div class="col-md-12 col-xs-12 col-sm-12 panel" style="padding:0px;border: unset;box-shadow: unset;margin-bottom:unset;">
					<div class="col-md-6 col-xs-6 col-sm-6 panel pull-left" style="padding: 0px;border: unset;box-shadow: unset;width: 40%;">

						<!-- Start Due note if you've given in your invoice -->
						<?php if ($invoice[ 'duenote' ]){echo '<div class="panel panel-warning" style="page-break-inside: avoid;"><div class="panel-heading text-uppercase"><strong>'. lang2('duenote').' </strong></div><table style="width:100%;border:none;"><tr style="width:100%;"><td style="width:50%;"><li class="list-group-item"><small>'.$invoice[ 'duenote' ].'</small></li></td></tr></table></div>';} ?>
						<!-- End Due note if you've given in your invoice -->

						<!-- Start Terms & Conditions from app settings -->
						<div class="panel panel-default" style="page-break-inside: avoid;">
							<div class="panel-heading text-uppercase">
								<strong>
									<?php echo $settings[ 'termtitle' ] ?>
								</strong>
							</div>
							<table style="width:100%;border:none;">
								<tr style="width:100%;">
									<td style="width:50%;">
										<li class="list-group-item">
											<small>
												<?php echo $settings['termdescription'] ?>
											</small>
										</li>
									</td>
								</tr>
							</table>
						</div>
						<!-- End Terms & Conditions from app settings -->
					</div> 
					<div class="col-md-6 col-xs-6 col-sm-6" style="padding: 0;padding: 0;width: 55%;margin-left: 43%;">
						<!-- Start Default payment method -->
						<?php if ($default_payment) { ?>
						<div style="padding: 0;padding: 0;border: 1px solid #90909045;border-radius: 4px;">
							<div class="panel-heading text-uppercase" style="border-bottom: 1px solid #90909045;    background: whitesmoke;">
								<p>
									<strong>
										<?php echo lang( 'payment_method' ); ?></strong>: <?php echo $default_payment; ?>
								</p>
							</div>
						</div><br>
						<!-- End Default payment method -->
					<?php } 
					if (count($payments) > 0) {
					?>
					<!-- Start Payments -->
					<div style="padding: 0;padding: 0;border: 1px solid #90909045;border-radius: 4px;">
						<div class="panel-heading text-uppercase" style="border-bottom: 1px solid #90909045;    background: whitesmoke;">
							<strong>
								<?php echo lang( 'payments' ); ?>
							</strong>
						</div>
						<table class="table" style="page-break-inside: avoid;">
							<thead style="">
								<tr>
									<th class="col-md-2"><?php echo  lang( 'date' ) ?></th>
									<th class="col-md-2"><?php echo  lang( 'account' ) ?></th>
									<th style="text-align: center;" class="col-md-2"><?php echo  lang( 'amount' ) ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($payments as $payment){ ?>
								<tr>
									<td class="">
										<?php echo date("d-m-Y", strtotime($payment['date'])); ?>
									</td>
									<td class="">
										<?php echo $payment['name']; ?>
									</td>
									<td style="text-align: center;" class=""><?php echo '' . amount_format($payment[ 'amount' ], true) . '' ?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
					<!-- End Payments -->
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
