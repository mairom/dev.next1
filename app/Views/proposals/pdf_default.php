<?php $appconfig = get_appconfig(); ?>
<?php $number = get_number('proposals',$proposals['id'],'proposal','proposal');?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> <!-- Encoding utf8 chartset for the pdf -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/bootstrap/dist/css/bootstrap.css'); ?>" type="text/css"/> <!-- Bootstrap CSS file link -->
	<style>
		.list-group-item.active,
		.list-group-item.active:focus,
		.list-group-item.active:hover {
			z-index: 2;
			color: #fff;
			background-color: #555;
			border-color: #555;
		}
		tr, td, th, small, span, strong {
			font-family: DejaVu Sans; sans-serif;
		}
		/*Custom CSS write/paste here*/
	</style>
</head>
<?php if($proposals['relation_type'] == 'customer'){if($proposals['customercompany']===NULL) /*Customer type i.e individual or company */ { $customer = $proposals['namesurname'];} else $customer = $proposals['customercompany'];} ?>
<?php if($proposals['relation_type'] == 'lead'){$customer = $proposals['leadname'];} 

// Proposal logo
$logo =  file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo']);
if(file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'])) {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'];
} else {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['logo']; // Use app logo, if Proposal logo is not found
}
?>
<body>
	<div class="container">
		<div class="row">
			<div class="page-header">
				<img height="75px" src="<?php echo $logo ?>" alt="">
				<small class="pull-right" style="position:relative;top:20px;right:20px;"><strong><span class="text-uppercase"><?php echo lang2('proposal') ?></span> <br> <!--  Proposal Title in your language -->
					#<?php echo '' . $number. '' ?> <!-- Proposal number -->
					<br> <?php echo lang( 'date' ).': '. //Date label 
					date(get_dateFormat(), strtotime($proposals['date']))?> - <?php echo lang( 'opentill' )?> : <?php echo date(get_dateFormat(), strtotime($proposals['opentill']))?>
					<!-- Proposal issuance and ending date -->
					</strong></small>
			</div>
			<div class="col-md-12 nav panel" style="padding-bottom: 20px">
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<strong><?php echo lang2('from') ?></strong><br>
					<hr>
					<small>
						<strong><?php echo ($settings['company']) ? $settings['company'] : ""	; ?></strong>  <!-- Company Name from app settings -->
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
					<strong><?php echo lang2('to') ?></strong><br>
					<hr>
					<small>
						<strong><?php echo $customer; ?></strong>
					</small>
					<br>
					<small>
						<?php echo ($proposals[ 'toaddress' ] ? $proposals[ 'toaddress' ].'/' : ''); ?><?php echo ($proposals[ 'city' ] ? $proposals[ 'city' ].'/' : ''); ?><?php echo ($custstate ? $custstate.'/' : '') ; ?><?php echo ($custcountry ? $custcountry :''); ?><?php echo ($proposals[ 'zip' ] ? '- '.$proposals[ 'zip' ] : ''); ?> 
					</small><br>
					<small>
						<?php echo $proposals[ 'toemail' ]; ?>
					</small><br>
				</div>
			</div>
			<div class="col-md-12">
			<?php echo $proposals[ 'content' ] ?>
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
							<?php echo '' . amount_format($item[ 'total' ]) . '' ;?>
							<!-- You can change Number format as per your requirement
							i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
							example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="col-md-12 col-xs-12 col-sm-12 panel" style="padding:0px">
				<div class="col-md-6 col-xs-6 col-sm-6 panel pull-left" style="padding: 0px;">
					
				</div>
				<div class="col-md-5 col-xs-5 col-sm-5 pull-right" style="padding: 0">
					<div class="list-group">
						<li class="list-group-item">
							<strong>
								<?php echo lang( 'subtotal' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($proposals[ 'sub_total' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo lang( 'linediscount' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($proposals[ 'total_discount' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item">
							<strong>
								<?php echo $appconfig['tax_label']; ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($proposals[ 'total_tax' ], true). '' ?>
							</span>
						</li>
						<li class="list-group-item active">
							<strong>
								<?php echo lang( 'total' ); ?>
							</strong>
							<span class="pull-right">
								<?php echo '' . amount_format($proposals[ 'total' ], true). '' ?>
							</span>
						</li>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>