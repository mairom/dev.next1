<?php $appconfig = get_appconfig(); ?>
<?php $number = get_number('deposits',$deposit['id'],'deposit','deposit');?>
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
		}/*Custom CSS write/paste here*/
	</style> 
</head>
<?php
//Check internal or customer deposit
if ($deposit[ 'deposit_status' ] == '2') {
	$customer = lang2('internal'). ' ' .lang2('deposit');
} else {
	// Customer type i.e individual or company
	if ( $deposit[ 'customer' ] == NULL ) { 
		$customer = $deposit[ 'individual' ];
	} else {
		$customer = $deposit[ 'customer' ];	
	}
}

// Deposit logo
$logo =  file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo']);
if(file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'])) {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'];
} else {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['logo']; // Use app logo, if deposit logo is not found
}
?>
<body>
	<div class="container">
		<div class="row">
			<div class="page-header row">
				<div class="col-md-3 col-sm-3 col-xs-3 logo">
					<img height="90px" src="<?php echo $logo ?>" alt=""><br>
					<small>
						<!-- Company Name from app settings -->
						<strong><?php echo ($settings['company']) ? $settings['company'] : ""	; ?></strong>
					</small><br> 
					<small style="font-size: 11px;">
						<!-- Company Address -->
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
						<strong><span class="text-uppercase"><?php echo lang2('deposit') ?></span><br /> <!--  Deposit Title in your language -->
							# <?php echo ''.$number.'' ?> <!-- Deposit number -->
							<br>
							<?php echo '' . lang( 'deposit' ) .' '.lang( 'date' ). ': ' . date(get_dateFormat(), strtotime($deposit['created'])) . '' ?>
							<!-- Deposit created date -->
						</strong>
					</small>
				</div>
			</div>
			<div class="col-md-12 nav panel" style="padding-bottom: 20px;box-shadow: unset;padding-right: 0;">
				<?php if ($deposit[ 'deposit_status' ] == '2') { ?> 
					<div style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
						<strong><?php echo lang2('internal'). ' '.lang2('deposit') ?></strong>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
				<?php } else { ?>
				<div style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
					<!-- Customer Details -->
					<strong><?php echo lang2('customer').' '.lang2('deposit') ?></strong>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<br>
					<small>
						<strong><?php echo $customer; ?></strong>
					</small><br>
					<small>
						<?php echo ($customerstate ? $customerstate.'/' : '') ; ?><?php echo ($customercountry ? $customercountry:''); ?><?php echo ($deposit[ 'zipcode' ] ? '- '.$deposit[ 'zipcode' ] : ''); ?> 
					</small><br>
					<small>
						<?php echo $deposit[ 'customer_phone' ]; ?>
					</small><br>
					<small>
						<!-- Customer vatoffice & vat number -->
						<strong><?php echo  ($deposit[ 'taxoffice' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').': ' : '') ?></strong><?php echo $deposit[ 'taxoffice' ]; ?>
					</small><br>
					<small>
						<strong><?php echo (($deposit[ 'taxnumber' ] == 0 || !$deposit[ 'taxnumber' ]) ? '': ($appconfig['tax_label'].' '.lang2('vatnumber').': '.$deposit[ 'taxnumber' ]))  ?></strong>
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
							example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240  -->
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
								<!-- Account Type -->
								<th class="col-md-12"><?php echo  $deposit['account'] ?></th>
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
								<?php echo '' . number_format( $deposit[ 'sub_total' ], 2, '.', ',' ) . ' ' . currency . '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo $appconfig['tax_label']; ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . number_format( $deposit[ 'total_tax' ], 2, '.', ',' ) . ' ' . currency . '' ?>
							</span>
						</li>
						<li class="list-group-item active">
							<strong>
								<?php echo lang( 'total' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . number_format( $deposit[ 'amount' ], 2, '.', ',' ) . ' ' . currency . ''; ?>
							</span>
						</li>
					</div>
				</div>
			</div>
		</div>
		</div>
	</div>
</body>
</html>