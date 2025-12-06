<?php $appconfig = get_appconfig(); ?>
<?php $number = get_number('purchases',$purchase['id'],'purchase','purchase');?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> <!-- Encoding utf8 chartset for the pdf -->
	<link rel='stylesheet prefetch' href='<?php echo base_url('assets/lib/bootstrap/dist/css/bootstrap.min.css'); ?>'>  <!-- Bootstrap CSS file link -->
	<style>
		.list-group-item.active,
		.list-group-item.active:focus,
		.list-group-item.active:hover {
			z-index: 2;
			color: #fff;
			background-color: #555;
			border-color: #555;
		}
		td, th, small, span, strong {
			font-family: DejaVu Sans; sans-serif;
		}
		/*Custom CSS write/paste here*/
	</style>
</head>
<?php
// Purchase logo
$logo =  file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo']);
if(file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'])) {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'];
} else {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['logo']; // Use app logo, if purchase logo is not found
}
?>
<body>
	<div class="container">
		<div class="row">
			<div class="page-header">
				<img height="75px" src="<?php echo $logo ?>" alt="">
				<small class="pull-right" style="position:relative;top:20px;right:20px;"><strong><span class="text-uppercase"><?php echo lang2('purchase').' '.lang2('invoiceorder') ?></span> <br> <!--  Purchase Title in your language -->
					#<?php echo '' . $number . '' ?> <!-- Purchase number -->
				</strong></small>
			</div>
			<div class="col-md-12 nav panel" style="padding-bottom: 20px">
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<strong><?php echo lang2('from') ?></strong><br>
					<hr>
					<small>
						<strong><?php echo ($settings['company']) ? $settings['company'] : ""	; ?></strong>
						<!-- Company Name from app settings -->
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
					<!-- Vendor Details -->
					<strong><?php echo lang2('to') ?></strong><br>
					<hr>
					<small>
						<strong><?php echo $purchase['vendorcompany']; ?></strong>
					</small>
					<br>
					<small>
						<?php echo ($purchase[ 'vendoraddress' ] ? $purchase[ 'vendoraddress' ].'/' : ''); ?><?php echo ($purchase[ 'vendortown' ] ? $purchase[ 'vendortown' ].'/' : ''); ?><?php echo ($purchase[ 'vendorcity' ] ? $purchase[ 'vendorcity' ].'/' : ''); ?><?php echo ($vendor_state ? $vendor_state.'/' : '') ; ?><?php echo ($vendor_country ? $vendor_country :''); ?><?php echo ($purchase[ 'vendorzip' ] ? '- '.$purchase[ 'vendorzip' ] : ''); ?> 
					</small><br>
					<small>
						<?php echo $purchase[ 'email' ]; ?>
					</small><br>
					<small>
						<!-- Vendor vatoffice & vat number -->
						<strong><?php echo  ($purchase[ 'taxoffice' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').': ' : '') ?></strong><?php echo $purchase[ 'taxoffice' ]; ?>
					</small><br>
					<small>
						<strong><?php echo (($purchase[ 'taxnumber' ] == 0 || !$purchase[ 'taxnumber' ]) ? '': ($appconfig['tax_label'].' '.lang2('vatnumber').': '.$purchase[ 'taxnumber' ]))  ?></strong>
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
							<?php echo '' . $item[ 'name' ] . '</b><br><small style="font-size:10px;line-height:10px">' . nl2br($item[ 'description' ]) . '</small>'; ?>
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
							<!-- You can change Number format as per your requirement
							i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
							example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
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
								<?php echo '' . amount_format($purchase[ 'sub_total' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo lang( 'linediscount' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($purchase[ 'total_discount' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo $appconfig['tax_label']; ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($purchase[ 'total_tax' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item active">
							<strong>
								<?php echo lang( 'total' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($purchase[ 'total' ], true). ''; ?>
							</span>
						</li>
					</div>
				</div>
			</div>
			<div class="row" style="page-break-inside: avoid;padding-top: 17%;">
				<div class="col-md-12 col-xs-12 col-sm-12 panel" style="padding:0px;border: unset;box-shadow: unset;margin-bottom:unset;">
					<div class="col-md-6 col-xs-6 col-sm-6 panel pull-left" style="padding: 0px;border: unset;box-shadow: unset;width: 40%;">
						<!-- Start Due note if you've given in your purchase -->
						<?php if ($purchase[ 'duenote' ]){echo '<div class="panel panel-warning" style="page-break-inside: avoid;"><div class="panel-heading text-uppercase"><strong>'. lang2('duenote').' </strong></div><table style="width:100%;border:none;"><tr style="width:100%;"><td style="width:50%;"><li class="list-group-item"><small>'.$purchase[ 'duenote' ].'</small></li></td></tr></table></div>';} ?>
						<!-- End Due note if you've given in your purchase -->

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
				</div>
			</div>
		</div>
</body>
</html>