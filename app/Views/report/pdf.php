<!DOCTYPE html>

<html>

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/lib/bootstrap/dist/css/bootstrap.css'); ?>" type="text/css" />

</head>

<body>
	<div class="container">
		<div class="row">
			<?php if ($type == 'invoices') { ?>
				<h2 class="text-center"><?php echo lang2('invoices') ?></h2>
				<table class="table panel">

					<thead>

						<tr md-row>
							<th class='col-md-1'><span><?php echo lang2('invoice'); ?></span></th>
							<th md-column><span>Cpf/Cnpj</span></th>
							<th><span><?php echo lang2('customer'); ?></span></th>
							<th><span><?php echo lang2('billeddate'); ?></span></th>
							<th><span><?php echo lang2('invoiceduedate'); ?></span></th>
							<th><span><?php echo lang2('addedby'); ?></span></th>

							<th>Produto</th>
							<th>Valor</th>
							<th>Quantidade</th>

							<th><?php echo lang2('amount'); ?></th>
							<th><span>Status</span></th>

						</tr>
					</thead>

					<tbody>
						<?php
						foreach ($results as $data) {
							echo "<tr>";
							echo "<td class='text-left'>" . (get_number('invoices', $data['id'], 'invoice', 'inv')) . "<br>" . ($data['serie'] ? $data['serie'] : '') . "</td>";
							echo "<td class='text-left'>" . ($data['cpf'] ? $data['cpf'] : '') . "</td>";
							echo "<td class='text-left'>" . ($data['customer'] ? $data['customer'] : '') . "</td>";
							echo "<td class='text-left'>" . ($data['created'] ? implode('/', array_reverse(explode('-', $data['duedate']))) : '') . "</td>";
							echo "<td class='text-left'>" . ($data['duedate'] ? implode('/', array_reverse(explode('-', $data['duedate']))) : '') . "</td>";
							echo "<td class='text-left'>" . ($data['staffmembername']) . "</td>";

							echo "<td class='text-left'>#</td>";
							echo "<td class='text-left'>#</td>";
							echo "<td class='text-left'>#</td>";

							echo "<td class='text-left'>R$ " . ($data['total']) . "</td>";
							echo "<td class='text-left'>" . ($data['statusname']) . "</td>";
							echo "</tr>";

							foreach ($data['items'] as $item) {
								echo "<tr>";
								echo "<td class='text-left'></td>";
								echo "<td class='text-left'></td>";
								echo "<td class='text-left'></td>";
								echo "<td class='text-left'></td>";
								echo "<td class='text-left'></td>";

								echo "<td class='text-left'>" . ($item['name']) . "</td>";
								echo "<td class='text-left'>R$ " . ($item['price']) . "</td>";
								echo "<td class='text-left'>" . ($item['quantity']) . "</td>";

								echo "<td class='text-left'></td>";
								echo "<td class='text-left'></td>";
								echo "<td class='text-left'></td>";
								echo "</tr>";
							}
						}
						?>
					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'customers') { ?>

				<h2 class="text-center"><?php echo lang2('customers') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('customer') . "</th>";

						echo "<th>" . lang2('companyname') . "</th>";

						echo "<th>" . lang2('phone') . "</th>";

						echo "<th>" . lang2('email') . "</th>";

						echo "<th>" . lang2('group') . "</th>";

						echo "<th>" . lang2('amount') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							$total_unpaid_invoice_amount = $this->db->table('invoices')
								->selectSum('total')
								->where('status_id', 3)
								->where('customer_id', $data['id'])
								->get()
								->getRow()
								->total;

							$total_paid_invoice_amount = $this->db->table('invoices')
								->selectSum('total')
								->where('status_id', 2)
								->where('customer_id', $data['id'])
								->get()
								->getRow()
								->total;

							$total_paid_amount = $this->db->table('payments')
								->selectSum('amount')
								->where('transactiontype', 0)
								->where('customer_id', $data['id'])
								->get()
								->getRow()
								->amount;



							echo "<tr style='max-width:100%; overflow:auto;'>";

							echo "<td class='text-left'>" . (get_number('customers', $data['id'], 'customer', 'customer')) . "</td>";

							echo "<td class='text-left'>" . ($data['company'] ? $data['company'] : $data['namesurname']) . "</td>";

							echo "<td class='text-left'>" . ($data['phone'] ? $data['phone'] : '') . "</td>";

							echo "<td class='text-left'>" . $data['email'] . "</td>";

							echo "<td class='text-left'>" . $data['name'] . "</td>";

							echo "<td class='text-left'>" . ($total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount) . ' ' . currency . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'expenses') { ?>

				<h2 class="text-center"><?php echo lang2('expenses') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('expenses') . "</th>";

						echo "<th>" . lang2('category') . "</th>";

						echo "<th>" . lang2('type') . "</th>";

						echo "<th>" . lang2('customer') . "</th>";

						echo "<th>" . lang2('date') . "</th>";

						echo "<th>" . lang2('amount') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('payment_account') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							if ($data['invoice_id'] == NULL) {

								$billstatus = lang2('notbilled');
							} else {

								$billstatus = lang2('billed');
							}

							if ($data['internal'] == '1') {

								$customer = get_number('staff', $data['staffid'], 'staff', 'staff');

								$customername = $data['staff'];

								$billstatus = lang2('internal');
							} else {

								$customer = get_number('customers', $data['customerid'], 'customer', 'customer');

								$customername = $data['company'] ? $data['company'] : $data['namesurname'];
							}

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('expenses', $data['id'], 'expense', 'expense')) . "<br>" . ($data['title'] ? $data['title'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['category'] ? $data['category'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['internal'] == '1' ? lang2('internal') : '') . "</td>";

							echo "<td class='text-left'>" . $customername . "<br>" . $customer . "</td>";

							echo "<td class='text-left'>" . $data['date'] . "</td>";

							echo "<td class='text-left'>" . $data['amount'] . ' ' . currency . "</td>";

							echo "<td class='text-left'>" . $billstatus . "</td>";

							echo "<td class='text-left'>" . ($data['payment_account'] ? $data['payment_account'] : '') . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'proposals') { ?>

				<h2 class="text-center"><?php echo lang2('proposals') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('proposals') . "</th>";

						echo "<th>" . lang2('assignedstaff') . "</th>";

						echo "<th>" . lang2('created') . "</th>";

						echo "<th>" . lang2('opentill') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('total') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							switch ($data['status_id']) {

								case '0':

									$status = lang2('quote') . ' ' . lang2('request');

									break;

								case '1':

									$status = lang2('draft');

									break;

								case '2':

									$status = lang2('sent');

									break;

								case '3':

									$status = lang2('open');

									break;

								case '4':

									$status = lang2('revised');

									break;

								case '5':

									$status = lang2('declined');

									break;

								case '6':

									$status = lang2('accepted');

									break;

								default:

									$status = lang2('open');

									break;
							};

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('proposals', $data['id'], 'proposal', 'proposal')) . "<br>" . $data['subject'] . "</td>";

							echo "<td class='text-left'>" . $data['staffmembername'] . "<br>" . (get_number('staff', $data['staffid'], 'staff', 'staff')) . "</td>";

							echo "<td class='text-left'>" . ($data['date'] ? $data['date'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['opentill'] ? $data['opentill'] : '') . "</td>";

							echo "<td class='text-left'>" . $status . "</td>";

							echo "<td class='text-left'>" . ($data['total']) . ' ' . currency . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'deposits') { ?>

				<h2 class="text-center"><?php echo lang2('deposits') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('deposit') . "</th>";

						echo "<th>" . lang2('category') . "</th>";

						echo "<th>" . lang2('customer') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('date') . "</th>";

						echo "<th>" . lang2('amount') . "</th>";

						echo "<th>" . lang2('payment_account') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							if ($data['status'] == '1') {

								$billstatus = lang2('paid') and $color = 'success';
							} else if ($data['status'] == '0') {

								$billstatus = lang2('unpaid') and $color = 'danger';
							} else {

								$billstatus = lang2('internal') and $color = 'success';
							}

							if ($data['status'] == '2') {

								$customer = get_number('staff', $data['staffid'], 'staff', 'staff');

								$customername = $data['staffname'];
							} else {

								$customer = get_number('customers', $data['customerid'], 'customer', 'customer');

								$customername = $data['company'] ? $data['company'] : $data['namesurname'];
							}

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('deposits', $data['id'], 'deposit', 'deposit')) . "<br>" . $data['title'] . "</td>";

							echo "<td class='text-left'>" . ($data['category'] ? $data['category'] : '') . "</td>";

							echo "<td class='text-left'>" . $customername . "<br>" . $customer . "</td>";

							echo "<td class='text-left'>" . $billstatus . "</td>";

							echo "<td class='text-left'>" . $data['date'] . "</td>";

							echo "<td class='text-left'>" . $data['amount'] . ' ' . currency . "</td>";

							echo "<td class='text-left'>" . $data['payment_account'] . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'orders') { ?>

				<h2 class="text-center"><?php echo lang2('orders') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('order') . "</th>";

						echo "<th>" . lang2('customer') . "</th>";

						echo "<th>" . lang2('assigned') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('issuance_date') . "</th>";

						echo "<th>" . lang2('opentill') . "</th>";

						echo "<th>" . lang2('total') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							if ($data['relation_type'] == 'customer') {

								$customer_number = get_number('customers', $data['customerid'], 'customer', 'customer');

								$customer = $data['company'] ? $data['company'] : $data['namesurname'];
							} else {

								$customer_number = get_number('leads', $data['leadid'], 'lead', 'lead');

								$customer = $data['leadname'];
							}

							switch ($data['status_id']) {

								case '1':

									$status = lang2('draft');

									break;

								case '2':

									$status = lang2('sent');

									break;

								case '3':

									$status = lang2('open');

									break;

								case '4':

									$status = lang2('revised');

									break;

								case '5':

									$status = lang2('declined');

									break;

								case '6':

									$status = lang2('accepted');

									break;

								default:

									$status = lang2('open');

									break;
							};

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('orders', $data['id'], 'order', 'order')) . "<br>" . $data['subject'] . "</td>";

							echo "<td class='text-left'>" . $customer . "<br>" . $customer_number . "</td>";

							echo "<td class='text-left'>" . $data['staffmembername'] . "<br>" . (get_number('staff', $data['staffid'], 'staff', 'staff')) . "</td>";

							echo "<td class='text-left'>" . $status . "</td>";

							echo "<td class='text-left'>" . $data['date'] . "</td>";

							echo "<td class='text-left'>" . $data['opentill'] . "</td>";

							echo "<td class='text-left'>" . $data['total'] . ' ' . currency . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'vendors') { ?>

				<h2 class="text-center"><?php echo lang2('vendors') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('vendor') . "</th>";

						echo "<th>" . lang2('name') . "</th>";

						echo "<th>" . lang2('groupname') . "</th>";

						echo "<th>" . lang2('email') . "</th>";

						echo "<th>" . lang2('address') . "</th>";

						echo "<th>" . lang2('balance') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							// Total unpaid invoice amount
							$total_unpaid_invoice_amount = $this->db->table('purchases')
								->selectSum('total')
								->where('status_id', 3)
								->where('vendor_id', $data['id'])
								->get()
								->getRow()
								->total;

							// Total paid invoice amount
							$total_paid_invoice_amount = $this->db->table('purchases')
								->selectSum('total')
								->where('status_id', 2)
								->where('vendor_id', $data['id'])
								->get()
								->getRow()
								->total;

							// Total paid amount
							$total_paid_amount = $this->db->table('payments')
								->selectSum('amount')
								->where('transactiontype', 0)
								->where('vendor_id', $data['id'])
								->get()
								->getRow()
								->amount;


							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('vendors', $data['id'], 'vendor', 'vendor')) . "</td>";

							echo "<td class='text-left'>" . $data['company'] . "</td>";

							echo "<td class='text-left'>" . $data['name'] . "</td>";

							echo "<td class='text-left'>" . $data['email'] . "</td>";

							echo "<td class='text-left'>" . ($data['address'] ? $data['address'] : '') . "<br>" . ($data['phone'] ? $data['phone'] : '') . "</td>";

							echo "<td class='text-left'>" . ($total_unpaid_invoice_amount - $total_paid_amount + $total_paid_invoice_amount) . ' ' . currency . "</td>";





							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'purchases') { ?>

				<h2 class="text-center"><?php echo lang2('purchases') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('purchase') . "</th>";

						echo "<th>" . lang2('vendor') . "</th>";

						echo "<th>" . lang2('issuance_date') . "</th>";

						echo "<th>" . lang2('duedate') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('amount') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							$totalx = $data['total'];

							// Total payments for the given purchase_id
							$paytotal = $this->db->table('payments')
								->selectSum('amount')
								->where('purchase_id', $data['id'])
								->get()
								->getRow()
								->amount;

							// Calculate balance
							$balance = $totalx - $paytotal;

							// Determine purchase status and color
							if ($balance > 0) {
								$purchasesstatus = '';
							} else {
								$purchasesstatus = lang2('paidinv');
							}

							$color = 'success';

							if ($paytotal < $data['total'] && $paytotal > 0 && $data['status_id'] == 3) {
								$purchasesstatus = lang2('partial');
							} else {
								if ($paytotal < $data['total'] && $paytotal > 0) {
									$purchasesstatus = lang2('partial');
								}
								if ($data['status_id'] == 3) {
									$purchasesstatus = lang2('unpaid');
								}
							}


							if ($data['status_id'] == 1) {

								$purchasesstatus = lang2('draft');
							}

							if ($data['status_id'] == 4) {

								$purchasesstatus = lang2('cancelled');
							}

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number("purchases", $data['id'], 'purchase', 'purchase')) . "<br>" . ($data['serie'] ? $data['serie'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['vendorcompany']) . "<br>(" . (get_number("vendors", $data['vendor_id'], 'vendor', 'vendor')) . ")</td>";

							echo "<td class='text-left'>" . ($data['created'] ? $data['created'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['duedate'] ? $data['duedate'] : '') . "</td>";

							echo "<td class='text-left'>" . ($purchasesstatus) . "</td>";

							echo "<td class='text-left'>" . ($data['total']) . ' ' . currency . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'contacts') { ?>

				<h2 class="text-center"><?php echo lang2('customers') . ' ' . lang2('customercontacts') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('name') . "</th>";

						echo "<th>" . lang2('email') . "</th>";

						echo "<th>" . lang2('contactmobile') . ' ' . lang2('phone') . "</th>";

						echo "<th>" . lang2('customer') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							$customer = $data['company'] ? $data['company'] : $data['namesurname'];

							echo "<tr>";

							echo "<td class='text-left'>" . ($data['name'] . ' ' . $data['surname']) . "</td>";

							echo "<td class='text-left'>" . $data['email'] . "</td>";

							echo "<td class='text-left'>" . ($data['mobile'] ? $data['mobile'] : $data['phone']) . "</td>";

							echo "<td class='text-left'>" . ($customer) . "<br>(" . (get_number("customers", $data['customerid'], 'customer', 'customer')) . ")</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'tickets') { ?>

				<h2 class="text-center"><?php echo lang2('tickets') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('ticket') . "</th>";

						echo "<th>" . lang2('customer') . "</th>";

						echo "<th>" . lang2('department') . "</th>";

						echo "<th>" . lang2('priority') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('assigned') . "</th>";

						echo "<th>" . lang2('lastreply') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							switch ($data['priority']) {

								case '1':

									$priority = lang2('low');

									break;

								case '2':

									$priority = lang2('medium');

									break;

								case '3':

									$priority = lang2('high');

									break;

								default:

									$priority = lang2('medium');

									break;
							};

							switch ($data['status_id']) {

								case '1':

									$status = lang2('open');

									break;

								case '2':

									$status = lang2('inprogress');

									break;

								case '3':

									$status = lang2('answered');

									break;

								case '4':

									$status = lang2('closed');

									break;

								default:

									$status = lang2('open');

									break;
							};

							$customer = ($data['type'] == 0  ?  $data['company'] : $data['namesurname']);

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('tickets', $data['id'], 'ticket', 'ticket')) . "<br><small>" . $data['subject'] . "</small></td>";

							echo "<td class='text-left'>" . $customer . "<br>" . (get_number('customers', $data['customer_id'], 'customer', 'customer')) . "</td>";

							echo "<td class='text-left'>" . ($data['department'] ? $data['department'] : '') . "</td>";

							echo "<td class='text-left'>" . $priority . "</td>";

							echo "<td class='text-left'>" . $status . "</td>";

							echo "<td class='text-left'>" . ($data['staffmembername']) . "</td>";

							echo "<td class='text-left'>" . ($data['lastreply'] ? $data['lastreply'] : '') . "</td>";





							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'leads') { ?>

				<h2 class="text-center"><?php echo lang2('leads') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('lead') . "</th>";

						echo "<th>" . lang2('companyname') . "</th>";

						echo "<th>" . lang2('email') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('source') . "</th>";

						echo "<th>" . lang2('assigned') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('leads', $data['id'], 'lead', 'lead')) . "<br>" . $data['leadname'] . "</td>";

							echo "<td class='text-left'>" . ($data['company'] ? $data['company'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['email'] ? $data['email'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['statusname'] ? $data['statusname'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['sourcename'] ? $data['sourcename'] : '') . "</td>";

							echo "<td class='text-left'>" . $data['leadassigned'] . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'tasks') { ?>

				<h2 class="text-center"><?php echo lang2('tasks') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('task') . "</th>";

						echo "<th>" . lang2('project') . "</th>";

						echo "<th>" . lang2('startdate') . "</th>";

						echo "<th>" . lang2('duedate') . "</th>";

						echo "<th>" . lang2('priority') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('assigned') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							switch ($data['status_id']) {

								case '1':

									$status = lang2('open');

									break;

								case '2':

									$status = lang2('inprogress');

									break;

								case '3':

									$status = lang2('waiting');

									break;

								case '4':

									$status = lang2('complete');

									break;

								case '5':

									$status = lang2('cancelled');

									break;

								default:

									$status = lang2('open');

									break;
							};

							switch ($data['priority']) {

								case '1':

									$priority = lang2('low');

									break;

								case '2':

									$priority = lang2('medium');

									break;

								case '3':

									$priority = lang2('high');

									break;

								default:

									$priority = lang2('medium');

									break;
							};

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('tasks', $data['id'], 'task', 'task')) . "<br>" . $data['taskname'] . "</td>";

							echo "<td class='text-left'>" . $data['projectname'] . "<br>" . (get_number('projects', $data['projectid'], 'project', 'project')) . "</td>";

							echo "<td class='text-left'>" . ($data['startdate'] ? $data['startdate'] : '') . "</td>";

							echo "<td class='text-left'>" . ($data['duedate'] ? $data['duedate'] : '') . "</td>";

							echo "<td class='text-left'>" . $priority . "</td>";

							echo "<td class='text-left'>" . $status . "</td>";

							echo "<td class='text-left'>" . ($data['staffname']) . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'products') { ?>

				<h2 class="text-center"><?php echo lang2('products') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						$appconfig = get_appconfig();

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('product') . "</th>";

						echo "<th>" . lang2('name') . "</th>";

						echo "<th>" . lang2('purchaseprice') . "</th>";

						echo "<th>" . lang2('salesprice') . "</th>";

						echo "<th>" . $appconfig['tax_label'] . "</th>";

						echo "<th class='col-md-1'>" . lang2('instock') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('products', $data['id'], 'product', 'product')) . "<br>" . $data['name'] . "</td>";

							echo "<td class='text-left'>" . $data['productname'] . "<br>" . ($data['description'] ? $data['description'] : '') . "</td>";

							echo "<td class='text-left'>" . $data['purchase_price'] . ' ' . currency . "</td>";

							echo "<td class='text-left'>" . $data['sale_price'] . ' ' . currency . "</td>";

							echo "<td class='text-left'>" . $data['vat'] . "</td>";

							echo "<td class='text-left'>" . $data['stock'] . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'staff') { ?>

				<h2 class="text-center"><?php echo lang2('staff') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('staff') . "</th>";

						echo "<th>" . lang2('name') . "</th>";

						echo "<th>" . lang2('department') . "</th>";

						echo "<th>" . lang2('email') . "</th>";

						echo "<th>" . lang2('phone') . "</th>";

						echo "<th class='col-md-1'>" . lang2('type') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							if ($data['admin'] == '1') {

								$type = lang2('admin');
							} else if ($data['staffmember'] == '1' && $data['other'] == null) {

								$type = lang2('staff');
							} else {

								$type = lang2('other');
							}

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('staff', $data['id'], 'staff', 'staff')) . "</td>";

							echo "<td class='text-left'>" . $data['staffname'] . "</td>";

							echo "<td class='text-left'>" . $data['department'] . "</td>";

							echo "<td class='text-left'>" . $data['email'] . "</td>";

							echo "<td class='text-left'>" . ($data['phone'] ? $data['phone'] : '') . "</td>";

							echo "<td class='text-left'>" . $type . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

			<?php if ($type == 'projects') { ?>

				<h2 class="text-center"><?php echo lang2('projects') ?></h2>

				<table class="table panel">

					<thead>

						<?php

						echo "<tr>";

						echo "<th class='col-md-1'>" . lang2('project') . "</th>";

						echo "<th>" . lang2('customer') . "</th>";

						echo "<th>" . lang2('startdate') . "</th>";

						echo "<th>" . lang2('end') . ' ' . lang2('date') . "</th>";

						echo "<th>" . lang2('project_value') . "</th>";

						echo "<th>" . lang2('status') . "</th>";

						echo "<th>" . lang2('project') . ' ' . lang2('members') . "</th>";

						echo "</tr>";

						?>

					</thead>

					<tbody>

						<?php

						foreach ($results as $data) {

							switch ($data['status']) {

								case '1':

									$status = lang2('notstarted');

									break;

								case '2':

									$status = lang2('started');

									break;

								case '3':

									$status = lang2('percentage');

									break;

								case '4':

									$status = lang2('cancelled');

									break;

								case '5':

									$status = lang2('completed');

									break;

								default:

									$status = lang2('started');

									break;
							}

							if ($data['template'] == '1') {

								$customer = lang2('template');
							} else {

								$customer = ($data['customercompany']) ? $data['customercompany'] : $data['namesurname'];
							}

							$members = $this->Projects_Model->get_members_index($data['id']);

							$staff = array();

							foreach ($members as $member) {

								$staff[] = $member['staffname'];
							}

							echo "<tr>";

							echo "<td class='text-left'>" . (get_number('projects', $data['id'], 'project', 'project')) . "<br><small>" . $data['name'] . "</small></td>";

							echo "<td class='text-left'>" . $customer . "<br><small>" . (get_number('customers', $data['customerid'], 'customer', 'customer')) . "</small></td>";

							echo "<td class='text-left'>" . $data['start_date'] . "</td>";

							echo "<td class='text-left'>" . $data['deadline'] . "</td>";

							echo "<td class='text-left'>" . $data['projectvalue'] . ' ' . currency . "</td>";

							echo "<td class='text-left'>" . $status . "</td>";

							echo "<td class='text-left'>" . implode(',', $staff) . "</td>";

							echo "</tr>";
						}

						?>

					</tbody>

				</table>

			<?php } ?>

		</div>

	</div>

</body>

</html>