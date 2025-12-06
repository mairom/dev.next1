<?php include_once(ROOTPATH. 'Views/inc/ciuis_data_table_header.php'); ?>

<div class="ciuis-body-content">



    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <h2 flex md-truncate class="text-bold">Avaliação de desempenho</h2>
            </div>
        </md-toolbar>


        <md-content class="bg-white" ng-cloak>
            <md-content style = "background-image:none;height: auto;" class="md-padding no-item-data">Em breve</md-content>
            <div style="background-image:url('<?= base_url('assets/img/em_breve2.jpeg') ?>');
width: 100%;
height: 69vh;
background-position: center;
background-size: 25%;
background-repeat: no-repeat;
"></div>
        </md-content>

    </div>
</div>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>
<script src="<?php echo base_url('assets/lib/chartjs/dist/Chart.min.js'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/leads.js'); ?>"></script>