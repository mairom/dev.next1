<?php $appconfig = get_appconfig(); ?>
<?php $project_number = get_number('projects',$project['id'],'project','project');?>

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
		.new-section {
			box-shadow: unset;
			padding-right: 0;
			border: 1px solid #e4e4e4;
			border-collapse: separate;
			border-radius: unset !important;
			margin-bottom: 3%;
			page-break-inside: avoid;
		}
		.section-title {
			color: <?php echo $color?$color:'black' ?> !important;
			/*Heading Color of Project*/
		}
		tr, td, th, small, span, strong {
			font-family: DejaVu Sans; sans-serif;
		}
		/*Custom CSS write/paste here*/
	</style> 
</head>

<?php 

// Project logo
$logo =  file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo']);
if(file_exists(FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'])) {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['app_logo'];
} else {
	$logo = FCPATH.'uploads/ciuis_settings/'.$settings['logo']; // Use app logo, if project logo is not found
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
					<small style="font-size: 11px;">
						<!-- Company Address -->
						<?php echo '' .($settings[ 'town' ] ? $settings[ 'town' ].'/' : '').($settings[ 'city' ] ? $settings[ 'city' ].'/':'').($state ? $state.'/' : '').($country ? $country.'-':'').($settings[ 'zipcode' ] ? $settings[ 'zipcode' ] : '')  ?>
					</small><br>
				</div>
				<div class="col-md-4 col-sm-4 col-xs-4" style="padding-top: 2%;text-align: center;">
					<strong class="section-title"><span style="font-size: 25px;" class="text-uppercase"><?php echo lang2('project') ?></span> <br>
					<!--  Project Title in your language -->
					</strong>
					<span class="section-title">
						<!-- Project number -->
						<?php echo $project_number ?>
					</span>
				</div>
				<div class="col-md-5 col-sm-5 col-xs-5">
					<small class="" style="position:relative;top:20px;right:20px;padding-right: 40px;text-align: left;float: right;">
						<strong><span class="text-uppercase"><?php echo lang2('project') ?></span> <br>
							<?php echo $project_number ?>
							<br> 
							<!-- Porject Start Date -->
							<?php echo lang( 'start' ). ' '.lang( 'date' ).': ' . date(get_dateFormat(), strtotime($project['start_date'])) ?>
							<br>
							<!-- Project Dead Line -->
							<?php echo lang( 'deadline' ).': ' . date(get_dateFormat(), strtotime($project['deadline'])) ?>
						</strong>
					</small>
				</div>
			</div>
			<div class="col-md-12 nav panel" style="padding-bottom: 20px;box-shadow: unset;padding-right: 0;">
				<div style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
					<strong><?php echo $project['name'] ?></strong>
				</div>
				<?php if ($customer == true) { ?>
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<div class="section-title" style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
						<!-- Customer Details -->
						<strong><?php echo lang2('customer').' '.lang2('details') ?></strong>
					</div>
					<br>
					<small>
						<strong><?php echo $project['customercompany']?$project['customercompany']:$project['individual']; ?></strong>
					</small><br>
					<small>
						<?php echo ($project[ 'billing_street' ] ? $project[ 'billing_street' ].'/' : ''); ?><?php echo ($project[ 'billing_city' ] ? $project[ 'billing_city' ].'/' : ''); ?><?php echo ($billing_state ? $billing_state.'/' : '') ; ?><?php echo ($billing_country ? $billing_country:''); ?><?php echo ($project[ 'billing_zip' ] ? '- '.$project[ 'billing_zip' ] : ''); ?> 
					</small>
					<small>
						<?php echo $project[ 'customeremail' ]; ?>
					</small><br>
					<small>
						<!-- Customer vatoffice & vat number -->
						<strong><?php echo  ($project[ 'taxoffice' ] ? $appconfig['tax_label'].' '.lang2('taxoffice').': ' : '') ?></strong><?php echo $project[ 'taxoffice' ]; ?>
					</small><br>
					<small>
						<strong><?php echo (($project[ 'taxnumber' ] == 0 || !$project[ 'taxnumber' ]) ? '': ($appconfig['tax_label'].' '.lang2('vatnumber').': '.$project[ 'taxnumber' ]))  ?></strong>
					</small>
				</div>
			<?php } ?>
				<div class="col-md-6 col-sm-6 col-xs-6" style="padding: 0">
					<div class="section-title" style="border-bottom: 1px solid #eee;padding-bottom: 10px;">
						<!-- Project Details -->
						<strong><?php echo lang2('project').' '.lang2('details') ?></strong>
					</div>
					<br>
					<small> 
						<?php $log_time = (int)0; 
						$log_amt = (int)0; 
						foreach ($logs as $log) { 
							$log_time = $log_time+(int)$log['minutes'];
							$log_amt = $log_amt+(int)$log['total_amount'];
						} ?>
						<strong><?php echo lang2('project').' '.lang2('status').': '.$status ?><br>
						<?php echo lang2('project').' '.lang2('value').': '.$project['projectvalue'] ?><br>
						<?php echo lang2('clocked').' '.lang2('time').': '. floor($log_time / 60).'h : '.($log_time % 60).'m' ?><br>
						<?php echo lang2('clocked').' '.lang2('amount').': '.amount_format($log_amt, true) .''; ?><br></strong>
					</small><br>
				</div>
				<br>
			</div>
			<?php if ($is_summary == true) { ?>
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('summary') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<tbody>
						<tr style="border-bottom: 1px solid #eaeaea;">
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'expenses' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['expenses'] . ''; ?>
							</td>
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'proposals' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['proposals'] . ''; ?>
							</td>
						</tr>
						<tr style="border-bottom: 1px solid #eaeaea;">
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'tickets' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['tickets'] . ''; ?>
							</td>
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'services' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['services'] . ''; ?>
							</td>
						</tr>
						<tr style="border-bottom: 1px solid #eaeaea;">
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'milestones' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['milestones'] . ''; ?>
							</td>
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'tasks' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['tasks'] . ''; ?>
							</td>
						</tr>
						<tr style="border-bottom: 1px solid #eaeaea;">
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'members' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['members'] . ''; ?>
							</td>
							<td class="text-left col-md-3">
								<strong><?php echo lang( 'files' ); ?></strong>
							</td>
							<td class="text-left col-md-3">
								<?php echo '' . $summary['files'] . ''; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php } if ($members != '') { ?>
				<!-- Project Members -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('members') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-6">
								<?php echo  lang( 'name' ) ?>
							</th>
							<th class="col-md-6">
								<?php echo  lang( 'email' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($members as $member){ ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '' . $member[ 'member' ] . ''; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $member[ 'memberemail' ] . ''; ?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($services != '') { ?>
				<!-- Project Services -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('services') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-4">
								<?php echo  lang( 'productname' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'category' ) ?>
							</th>
							<th class="col-md-1">
								<?php echo  lang( 'quantity' ) ?>
							</th>
							<th class="col-md-1">
								<?php echo  lang( 'unit' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  $appconfig['tax_label'] ?> <!-- Tax label from app settings -->
							</th>
							<th class="col-md-2" style="text-align: center;">
								<?php echo  lang( 'amount' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($services) > 0) {
							foreach($services as $service){ ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '<b>' . $service[ 'servicename' ] . '</b><br><small style="font-size:10px;line-height:10px">' . $service[ 'servicedescription' ] . '</small>'; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $service[ 'categoryname' ] ?>
								</td>
								<td class="text-left">
									<?php echo '' . $service[ 'quantity' ]; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $service[ 'unit' ];?>
								</td>
								<td class="text-left">
									<?php echo '' . amount_format($service[ 'servicetax' ]) . '%';?>
								</td>
								<td class="text-left" style="text-align: center;">
									<?php echo '' . amount_format($service[ 'serviceprice' ], true). '' ?>
									<!-- You can change Number format as per your requirement
									i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
									example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('services').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($milestones != '') { ?>
				<!-- Project Milestones -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('milestones') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-1">
								<?php echo  lang( 'id' ) ?>
							</th>
							<th class="col-md-6">
								<?php echo  lang( 'name' ).' / '.lang( 'description' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'created' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'duedate' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php $m = 1; if(count($milestones) > 0) { foreach($milestones as $milestone) { ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo $milestone[ 'id' ]; ?>
								</td>
								<td class="text-left">
									<?php echo '<b>' . $milestone[ 'name' ] . '</b><br><small style="font-size:10px;line-height:10px">' . $milestone[ 'description' ] . '</small>'; ?>
								</td>
								<td class="text-left">
									<?php echo '' . date(get_dateFormat(),strtotime($milestone[ 'created' ])); ?>
								</td>
								<td class="text-left">
									<?php echo '' . date(get_dateFormat(), strtotime($milestone[ 'duedate' ])); ?>
								</td>
							</tr>
						<?php $m++; } } else {
							echo lang2('no').' '.lang2('milestones').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($tasks != '') { ?>
				<!-- Project Tasks -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('tasks') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-6">
								<?php echo  lang( 'name' ).' / '.lang( 'description' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'startdate' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'duedate' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'milestone' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'status' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($tasks) > 0) {
							foreach($tasks as $task){ 
						switch ( $task[ 'status_id' ] ) {
							case '1':
								$status = lang( 'open' );
								break;
							case '2':
								$status = lang( 'inprogress' );
								break;
							case '3':
								$status = lang( 'waiting' );
								break;
							case '4':
								$status = lang( 'complete' );
								break;
							case '5':
								$status = lang( 'cancelled' );
								break;
						}; ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '<b>' . $task[ 'name' ] . '</b><br><small style="font-size:10px;line-height:10px">' . $task[ 'description' ] . '</small>'; ?>
								</td>
								<td class="text-left">
									<?php echo '' .  date(get_dateFormat(), strtotime($task[ 'startdate' ])) ?>
								</td>
								<td class="text-left">
									<?php echo '' .  date(get_dateFormat(), strtotime($task[ 'duedate' ])); ?>
								</td>
								<td class="text-left">
									<?php echo '' . $task[ 'milestone' ]; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $status;?>
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('tasks').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($expenses != '') { ?>
				<!-- Project Expenses -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('expenses') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-6">
								<?php echo  lang( 'title' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'category' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'date' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'amount' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($expenses) > 0) { foreach($expenses as $expense){ ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '<b>' .get_number('expenses', $expense[ 'id' ], 'expense','expense') . '</b><br><small style="font-size:10px;line-height:10px">' . $expense[ 'title' ] . '</small>'; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $expense[ 'category' ] ?>
								</td>
								<td class="text-left">
									<?php echo '' .  date(get_dateFormat(), strtotime($expense[ 'date' ])); ?>
								</td>
								<td class="text-left">
									<?php echo '' . amount_format($expense[ 'amount' ], true). '' ?>
									<!-- You can change Number format as per your requirement
									i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
									example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('expenses').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($proposals != '') { ?>
				<!-- Project Proposals  -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('proposals') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-4">
								<?php echo  lang( 'subject' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'date' ) ?>
							</th>
							<th class="col-md-3">
								<?php echo  lang( 'opentill' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'amount' ) ?>
							</th>
							<th class="col-md-4">
								<?php echo  lang( 'status' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($proposals) > 0) { foreach($proposals as $proposal) { 
						switch ( $proposal[ 'status_id' ] ) {
							case '0':
								$status = lang( 'quote' ).' '.lang( 'request' );
								break;
							case '1':
								$status = lang( 'draft' );
								break;
							case '2':
								$status = lang( 'sent' );
								break;
							case '3':
								$status = lang( 'open' );
								break;
							case '4':
								$status = lang( 'revised' );
								break;
							case '5':
								$status = lang( 'declined' );
								break;
							case '6':
								$status = lang( 'accepted' );
								break;
						}; ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '' . $proposal[ 'subject' ] . ''; ?>
								</td>
								<td class="text-left">
									<?php echo '' .  date(get_dateFormat(), strtotime($proposal[ 'date' ])) ?>
								</td>
								<td class="text-left">
									<?php echo '' .  date(get_dateFormat(), strtotime($proposal[ 'opentill' ])); ?>
								</td>
								<td class="text-left">
									<?php echo '' . amount_format($proposal[ 'total' ], true). '' ?>
									<!-- You can change Number format as per your requirement
									i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
									example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
								</td>
								<td class="text-left">
									<?php echo '' . $status;?>
								</td>
							</tr>
						<?php } } else { 
							?> <tr style="border-bottom: 1px solid #eaeaea;"><?php
							echo lang2('no').' '.lang2('proposals').' '.lang2('found'); ?>
						</tr> <?php } ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($tickets != '') { ?>
				<!-- Project Tickets  -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('tickets') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-4">
								<?php echo  lang( 'subject' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'contact' ) ?>
							</th>
							<th class="col-md-3">
								<?php echo  lang( 'priority' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($tickets) > 0) { foreach($tickets as $ticket){ ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '' . $ticket[ 'subject' ] . ''; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $ticket[ 'contactname' ] ?>
								</td>
								<td class="text-left">
									<?php 
									switch ($ticket[ 'priority' ]) {
										case '1':
										echo lang2('low');
										break;
										case '2':
										echo lang2('medium');
										break;
										case '3':
										echo lang2('high');
										break;
									}
									?>
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('tickets').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($notes != '') { ?>
				<!-- Project Notes  -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('notes') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-6">
								<?php echo  lang( 'description' ) ?>
							</th>
							<th class="col-md-4">
								<?php echo  lang( 'added_by' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'date' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($notes) > 0) { foreach($notes as $note){ ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '' . $note[ 'description' ] . ''; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $note[ 'notestaff' ] ?>
								</td>
								<td class="text-left">
									<?php echo '' . date(get_dateFormat(), strtotime($note['created'])); ?>
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('notes').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($time_logs != '') { ?>
				<!-- Project Time logs -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('task').' '. lang2('time_logs') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<thead style="border-top: 2px solid #e4e4e4;">
						<tr>
							<th class="col-md-6">
								<?php echo  lang( 'note' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'staff' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'total' ) ?>
							</th>
							<th class="col-md-2">
								<?php echo  lang( 'amount' ) ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($time_logs) > 0) {
						 foreach($time_logs as $time_log){ 
							$end_time = $time_log['end'];
							$date = new DateTime();
							if ($end_time == NULL) {
								$endTime = NULL;
								$end_time = $date->format('Y-m-d H:i:s');
							} else {
								$endTime = $time_log['end'];
								$end_time = $time_log['end'];
							}
							$date1 = new DateTime($time_log['start']);
							$diffs = $date1->diff(new DateTime($end_time));
							$h = $diffs->days * 24;
							$h += $diffs->h;
							$minutes = $diffs->i;
							$seconds = $diffs->s;
							if ($minutes < 10) {
								$minutes = '0'.$minutes;
							}
							if ($seconds < 10) {
								$seconds = '0'.$seconds;
							}
							if ($h < 10) {
								$h = '0'.$h;
							}
							$total = $h.':'.$minutes.':'.$seconds;
							$amount = ($h+($minutes / 60)) * $time_log['rate'];
							?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '' . $time_log[ 'note' ] . ''; ?>
								</td>
								<td class="text-left">
									<?php echo '' . $time_log[ 'staff'] ?>
								</td>
								<td class="text-left">
									<?php echo $time_log['total_logged']; ?>
								</td>
								<td class="text-left">
									<?php echo '' . amount_format($time_log[ 'total_amount' ], true). '' ?>
									<!-- You can change Number format as per your requirement
									i.e. number_format(number or amount, decimal_upto, decimal_separator, number_seperator)
									example: 1. number_format(12345.24, 3, '.', ',')  ===> 12,345.240 -->
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('time_logs').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
			<?php  } if ($files != '') { ?>
				<!-- Project  Files   -->
			<div class="col-md-12 nav new-section">
				<div class="section-title" style="padding: 10px;color: blue;">
					<strong><?php echo lang2('project').' '. lang2('files') ?></strong>
				</div>
				<table class="table" style="box-shadow: 0 1px 1px rgb(255, 255, 255);margin-top: 1%;">
					<tbody>
						<?php if (count($files) > 0) {
							foreach($files as $file){ ?>
							<tr style="border-bottom: 1px solid #eaeaea;">
								<td class="text-left">
									<?php echo '' . $file[ 'file_name' ] . ''; ?>
								</td>
							</tr>
						<?php } } else {
							echo lang2('no').' '.lang2('files').' '.lang2('found');
						} ?>
					</tbody>
				</table>
			</div>
		<?php  } ?>
		</div>
	</div>
</body>
</html>