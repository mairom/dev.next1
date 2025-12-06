<?php include_once(APPPATH . 'Views/inc/header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<div class="ciuis-body-content" ng-controller="Deposit_Controller">
	<div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
	    <div ng-show="depositsLoader" layout-align="center center" class="text-center" id="circular_loader">
	      <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
	      <p style="font-size: 15px;margin-bottom: 5%;">
	        <span><?php echo lang2('please_wait') ?> <br>
	        <small><strong><?php echo lang2('loading'). ' '. lang2('deposit').'...' ?></strong></small></span>
	      </p>
	    </div>
	    <md-toolbar ng-show="!depositsLoader" class="toolbar-white" ng-cloak>
	      <div class="md-toolbar-tools">
	        <h2 class="md-pl-10" flex md-truncate>
	          <span ng-bind="deposit.longid"></span>  
	          <span class="badge"><strong ng-bind="deposit.category_name"></strong></span>
	        </h2>
        	<md-button ng-click="sendEmail()" class="md-icon-button" aria-label="Email" ng-cloak>
        		<md-progress-circular ng-show="sendingEmail == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
        		<md-tooltip ng-hide="sendingEmail == true" md-direction="bottom" ng-bind="lang.send"></md-tooltip>
        		<md-icon ng-hide="sendingEmail == true"><i class="mdi mdi-email text-muted"></i></md-icon>
        	</md-button>
        	<md-button ng-show="deposit.pdf_status == '0'" ng-click="GeneratePDF()" class="md-icon-button" aria-label="Pdf" ng-cloak>
        		<md-tooltip md-direction="bottom"><?php echo lang2('deposit'). ' '.lang2('summary') ?></md-tooltip>
        		<md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>
        	</md-button>
        	<md-button ng-show="deposit.pdf_status == '1'" ng-href="<?php echo base_url('deposits/download_pdf/').$id ?>" class="md-icon-button" aria-label="Pdf" ng-cloak>
        		<md-tooltip md-direction="bottom"><?php echo lang2('deposit'). ' '.lang2('summary') ?></md-tooltip>
        		<md-icon><i class="mdi mdi-collection-pdf text-muted"></i> </md-icon>
        	</md-button>	
	        <?php if (check_privilege('deposits', 'edit') || check_privilege('deposits', 'delete')) { ?>      
	        	<md-menu md-position-mode="target-right target" ng-cloak>
	        		<md-button aria-label="Open demo menu" class="md-icon-button" ng-click="$mdMenu.open($event)">
	        			<md-icon><i class="ion-android-more-vertical text-muted"></i></md-icon>
	        		</md-button>
	        		<md-menu-content width="4">
	        			<?php if (check_privilege('deposits', 'edit')) { ?>
	        				<md-menu-item ng-show="deposit.status=='0'">
	        					<md-button  ng-click="MarkAsReceived()" aria-label="Update">
	        						<div layout="row" flex>
	        							<p flex ng-bind="lang.markasreceived"></p>
	        							<md-icon md-menu-align-target class="ion-checkmark-circled" style="margin: auto 3px auto 0;"></md-icon>
	        						</div>
	        					</md-button>
	        				</md-menu-item>
	        				<md-menu-item ng-show="deposit.status=='0'">
	        					<md-button ng-href="<?php echo base_url('deposits/update/')?>{{deposit.id}}" aria-label="Update">
	        						<div layout="row" flex>
	        							<p flex ng-bind="lang.update"></p>
	        							<md-icon md-menu-align-target class="mdi mdi-edit" style="margin: auto 3px auto 0;"></md-icon>
	        						</div>
	        					</md-button>
	        				</md-menu-item>
	        			<?php } if (check_privilege('deposits', 'delete')) { ?>
	        				<md-menu-item>
	        					<md-button ng-click="Delete()">
	        						<div layout="row" flex>
	        							<p flex ng-bind="lang.delete"></p>
	        							<md-icon md-menu-align-target class="ion-trash-b" style="margin: auto 3px auto 0;"></md-icon>
	        						</div>
	        					</md-button>
	        				</md-menu-item>
	        			<?php } ?>
	        		</md-menu-content>
	        	</md-menu>
	        <?php } ?>
	      </div>
	    </md-toolbar>
	    <md-content ng-show="!depositsLoader" class="bg-white invoice" layout-padding ng-cloak>
		    <div class="invoice-header col-md-12">
		        <div class="col-md-6 col-xs-6"> 
		          <div class="ciuis-expenses-receipt-xs-colum" style="border: unset;"> <i class="mdi mdi-balance-wallet" aria-hidden="true"></i>
		            <p> 
		              <span><?php echo lang2('amount')?>:</span><br>
		              <span style="font-size: 26px;font-weight: 900;" ng-bind-html="deposit.amount | currencyFormat:cur_code:null:true:cur_lct"></span><br>
		              <small><?php echo lang2('paidvia')?> <strong ng-bind="deposit.account_name"></strong></small> 
		            </p>
		          </div>
		        </div>
		        <div class="col-md-6 col-xs-6">  
		          <div class="ciuis-expenses-receipt-xs-colum">
		            <p><?php echo lang2('title') ?>:<br>
		              <span><strong ng-bind="deposit.title"></strong></span>
		            </p>
		          </div>
		        </div>
		    </div>
	      	<div class="invoice-header col-md-12">
		    	<div class="col-md-6 col-xs-6">  
		          <div class="ciuis-expenses-receipt-xs-colum">
		            <p><?php echo lang2('deposit'). ' '.lang2('date')?>:<br>
		              <span><strong ng-bind="deposit.date | date:'dd, MMMM yyyy EEEE'"></strong></span>
		            </p>
		          </div>
		        </div>
		        <div class="col-md-6 col-xs-6">  
			        <div class="ciuis-expenses-receipt-xs-colum">
			            <p><?php echo lang2('created')?>:<br>
			              <span><strong ng-bind="deposit.created"></strong></span>
			            </p>
			        </div>
		        </div>
	      	</div>
	      <div class="invoice-header col-md-12">
	        <div class="col-md-6 col-xs-6"> 
	        	<p ng-show="deposit.status=='2'">
	        		<span class="label label-success"><strong><?php echo lang2('internal'). ' '.lang2('deposit')?></strong></span>
	      		</p>
		        <div ng-show="deposit.status!='2'"class="invoice-from">
		            <small class="text-uppercase" ng-bind="lang.customer"></small>
		            <address class="m-t-5 m-b-5">
		              <strong ng-bind="deposit.customername"></strong><br>
		              <span ng-bind="deposit.customeremail"></span><br>
		              <span ng-bind="deposit.customer_phone"></span>
		            </address>
	         	</div>
	        </div>
	        <div class="col-md-6 col-xs-6">
	          <div class="">
	            <p><?php echo lang2('staff')?>:<br>
	              <a ng-href="<?php echo base_url('staff/staffmember/')?>{{deposit.staff_id}}">
	                <span><strong ng-bind="deposit.staff_name"></strong></span>
	              </a>
	            </p>
	          </div>
	        </div>
	      </div>
	      <div class="invoice-content col-md-12 md-p-0 xs-p-0 sm-p-0 lg-p-0">
        <div class="table-responsive">
          <table class="table table-invoice">
            <thead>
              <tr>
                <th ng-bind="lang.product"></th>
                <th ng-bind="lang.quantity"></th>
                <th ng-bind="lang.price"></th>
                <th><?php echo $appconfig['tax_label'] ?></th>
                <th ng-bind="lang.total"></th>
              </tr>
            </thead>
            <tbody>
            	<tr ng-repeat="item in deposit.items">
            		<td>
            			<span ng-bind="item.name"></span><br>
            			<pre class="pre_view" ng-cloak>{{item.description}}</pre>
            		</td>
            		<td ng-bind="item.quantity"></td>
            		<td ng-bind-html="item.price | currencyFormat:cur_code:null:true:cur_lct"></td>
            		<td ng-bind="item.tax + '%'"></td>
            		<td ng-bind-html="item.total | currencyFormat:cur_code:null:true:cur_lct"></td>
            	</tr>
            </tbody>
          </table>
        </div>
        <div class="invoice-price">
          <div class="invoice-price-left">
            <div class="invoice-price-row">
              <div class="sub-price"> <small ng-bind="lang.subtotal"></small> <span ng-bind-html="deposit.sub_total | currencyFormat:cur_code:null:true:cur_lct"></span> </div>
              <div class="sub-price"> <i class="ion-plus-round"></i> </div>
              <div class="sub-price"> <small><?php echo $appconfig['tax_label'] ?></small> <span ng-bind-html="deposit.total_tax | currencyFormat:cur_code:null:true:cur_lct"></span> </div>
            </div>
          </div>
          <div class="invoice-price-right"> <small ng-bind="lang.total"></small> <span ng-bind-html="deposit.total | currencyFormat:cur_code:null:true:cur_lct"></span> </div>
        </div>
      </div>
	  	</md-content>
	</div>
</div>
<ciuis-sidebar></ciuis-sidebar>
<script>
	var DEPOSITID = "<?php echo $id ?>";
	var lang = {};
	lang.doIt = "<?php echo lang2('doIt')?>";
	lang.cancel = "<?php echo lang2('cancel')?>";
	lang.attention = "<?php echo lang2('attention')?>";
	lang.delete_deposit = "<?php echo lang2('depositsatentiondetail')?>";
</script>

<script type="text/ng-template" id="generate-deposit.html">
  <md-dialog aria-label="options dialog">
	<md-dialog-content layout-padding class="text-center">
		<md-content class="bg-white" layout-padding>
			<h2 class="md-title" ng-hide="PDFCreating == true"><?php echo lang2('generate').' '.lang2('deposit').' '.lang2('pdf') ?></h2>
			<h2 class="md-title" ng-if="PDFCreating == true"><?php echo lang2('report_generating') ?></h2>
			<span ng-hide="PDFCreating == false"><?php echo lang2('generate_pdf_deposit_msg') ?></span><br><br>
			<span ng-if="PDFCreating == false"><?php echo lang2('generate_pdf_last_msg') ?></span><br><br>
			<img ng-if="PDFCreating == true" ng-src="<?php echo base_url('assets/img/loading_time.gif') ?>" alt="">
			<a ng-if="PDFCreating == false" href="<?php echo base_url('deposits/download_pdf/'.$id.'')?>"><img  width="30%"ng-src="<?php echo base_url('assets/img/download_pdf.png') ?>" alt=""></a>
		</md-content>
	</md-dialog-content>
	<md-dialog-actions>
	  <span flex></span>
	  <md-button ng-click="CloseModal()"><?php echo lang2('cancel') ?>!</md-button>
	  <md-button ng-click="CreatePDF()"><?php echo lang2('create') ?>!</md-button>
	</md-dialog-actions>
  </md-dialog>
</script> 
<?php include_once( APPPATH . 'Views/inc/footer.php' );?>
<script type="text/javascript" src="<?php echo base_url('assets/js/deposits.js') ?>"></script>
