<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>
<?php $appconfig = get_appconfig(); ?>
<link href="<?php echo base_url('assets/lib/select2/select2.min.css'); ?>" rel="stylesheet" />

<div class="ciuis-body-content" ng-controller="Emails_Aut_Controller">
  <style type="text/css">
    rect.highcharts-background {
      fill: #f3f3f3;
    }

    .select2-container {
      width: 100% !important;
    }

    md-input-container {
      height: 50px;
      float: left;
      width: 100%;
    }
    md-input-container .md-placeholder, md-input-container label:not(.md-no-float):not(.md-container-ignore){
      text-transform: none;
    }
  </style>

  <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">
    <md-toolbar class="toolbar-white">
      <div class="md-toolbar-tools">
        <md-button class="md-icon-button" aria-label="File">
          <img class="img-icon-toolbar" src="{{appurl + 'assets/img/menu/E-mails%20Automatizados.svg'}}">
        </md-button>
        <h2 flex md-truncate>Roadmap<small>(<span ng-bind="emailsAut.length"></span>)</small>
        </h2>

        <?php if (check_privilege('emailsAut', 'create')) { ?>
          <md-button ng-click="Create()" class="md-icon-button" aria-label="New" ng-cloak>
            <md-tooltip md-direction="bottom"><?php echo lang2('create') ?></md-tooltip>
            <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
          </md-button>
        <?php } ?>
      </div>

    </md-toolbar>

    <div ng-show="emailsAutLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
      <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
      <p style="font-size: 15px;margin-bottom: 5%;">
        <span><?php echo lang2('please_wait') ?> <br>
          <small><strong><?php echo lang2('loading') . ' ' . lang2('x_menu_emails_aut') . '...' ?></strong></small></span>
      </p>
    </div>

    <md-content ng-show="!emailsAutLoader" class="bg-white" ng-cloak>
      <md-table-container ng-show="emailsAut.length > 0">
        <table md-table md-progress="promise">
          <thead md-head>
            <tr md-row>
              <th md-column><span><?php echo lang2('name'); ?></span></th>

              <th md-column><span>Criado em</span></th>
              <th md-column><span>Ações</span></th>
            </tr>
          </thead>
          <tbody md-body>
            <tr class="select_row" md-row ng-repeat="taskAut in emailsAut " class="cursor">
              <td md-cell>
                <strong><span ng-bind="taskAut.name_taskAut"></span></strong>
              </td>
              <td md-cell>
                <span class="blur" ng-bind="taskAut.dt_criado"></span>
              </td>
              <td md-cell>

                <?php if (check_privilege('emailsAut', 'edit')) { ?>
                  <md-button style="width: 30px;height: 30px;" ng-click="Update(taskAut)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                    <md-icon style="font-size: 21px;" class="mdi mdi-edit"></md-icon>
                  </md-button>
                <?php } ?>

                <?php if (check_privilege('emailsAut', 'delete')) { ?>
                  <md-button style="width: 30px;height: 30px;" ng-click="Remove(taskAut.id_task)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                    <md-icon style="font-size: 21px;" class="mdi mdi-close"></md-icon>
                  </md-button>
                <?php } ?>
              </td>
            </tr>
          </tbody>
        </table>
      </md-table-container>
      <md-content ng-show="!emailsAut.length && !emailsAutLoader" class="md-padding no-item-data">
        <?php echo lang2('notdata') ?></md-content>
    </md-content>

  </div>


  <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" style="width: 450px;" ng-cloak>
    <md-content>
      <md-toolbar class="toolbar-white">
        <div class="md-toolbar-tools">
          <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
          <h2 flex md-truncate><?php echo lang2('create') ?></h2>
        </div>
      </md-toolbar>

      <md-content layout-padding>
        <md-input-container class="md-block" flex-gt-xs>
          <label>Nome da task</label>
          <input name="name_taskAut" ng-model="TEMPtaskAut.name_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label>Cópia para</label>
          <input name="copia_para_taskAut" ng-model="TEMPtaskAut.copia_para_taskAut">
        </md-input-container>

        <md-input-container class="md-block" style="margin-top: 1em;">
          <label>Responsavel email</label>
          <md-select ng-model="TEMPtaskAut.respon_email" style="min-width: 200px;">
            <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
          </md-select>
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs ng-class="{disabled: TEMPtaskAut.tipo != null}">
          <label>Tipo</label>
          <md-select ng-disabled="TEMPtaskAut.tipo != null ? true : false" ng-model="TEMPtaskAut.tipo">
            <md-option value="L">Leads</md-option>
            <md-option value="C">Clientes</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label>Enviar para</label>
          <md-select  ng-model="TEMPtaskAut.enviar_para">
            <md-option value="1">Contato principal</md-option>
            <md-option value="2">Todos</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs>
          <label>Cópia para (oculto)</label>
          <input name="copia_para_oculto_taskAut" ng-model="TEMPtaskAut.copia_para_oculto_taskAut">
        </md-input-container>
        <!--
        <md-input-container class="md-block" flex-gt-xs>
          <label>Hora</label>
          <input type="time" ng-model="TEMPtaskAut.hora_taskAut">
        </md-input-container>
        -->


        <md-input-container class="md-block" flex-gt-xs flex="65">
          <label>Modelo de email (Envio 1)</label>
          <md-select ng-model="TEMPtaskAut.template1">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="35">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo1_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 2">
          <label>Modelo de email (Envio 2)</label>
          <md-select ng-model="TEMPtaskAut.template2">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 2">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo2_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 3">
          <label>Modelo de email (Envio 3)</label>
          <md-select ng-model="TEMPtaskAut.template3">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 3">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo3_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 4">
          <label>Modelo de email (Envio 4)</label>
          <md-select ng-model="TEMPtaskAut.template4">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 4">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo4_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 5">
          <label>Modelo de email (Envio 5)</label>
          <md-select ng-model="TEMPtaskAut.template5">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 5">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo5_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 6">
          <label>Modelo de email (Envio 6)</label>
          <md-select ng-model="TEMPtaskAut.template6">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 6">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo6_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 7">
          <label>Modelo de email (Envio 7)</label>
          <md-select ng-model="TEMPtaskAut.template7">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 7">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo7_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 8">
          <label>Modelo de email (Envio 8)</label>
          <md-select ng-model="TEMPtaskAut.template8">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 8">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo8_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 9">
          <label>Modelo de email (Envio 9)</label>
          <md-select ng-model="TEMPtaskAut.template9">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 9">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo9_taskAut">
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 10">
          <label>Modelo de email (Envio 10)</label>
          <md-select ng-model="TEMPtaskAut.template10">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 10">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo10_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 11">
          <label>Modelo de email (Envio 11)</label>
          <md-select ng-model="TEMPtaskAut.template11">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 11">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo11_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 12">
          <label>Modelo de email (Envio 12)</label>
          <md-select ng-model="TEMPtaskAut.template12">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 12">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo12_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 13">
          <label>Modelo de email (Envio 13)</label>
          <md-select ng-model="TEMPtaskAut.template13">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 13">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo13_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 14">
          <label>Modelo de email (Envio 14)</label>
          <md-select ng-model="TEMPtaskAut.template14">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 14">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo14_taskAut">
        </md-input-container>


        <md-input-container class="md-block" flex-gt-xs flex="70" ng-show="totalPeriodos >= 15">
          <label>Modelo de email (Envio 15)</label>
          <md-select ng-model="TEMPtaskAut.template15">
            <md-option ng-value="template.id" ng-repeat="template in templates">{{template.name != null ? template.name : template.subject}}</md-option>
          </md-select>
        </md-input-container>

        <md-input-container class="md-block" flex-gt-xs flex="30" ng-show="totalPeriodos >= 15">
          <label>Intervalo (dias)</label>
          <input type="number" max="180" min="1" ng-model="TEMPtaskAut.periodo15_taskAut">
        </md-input-container>





        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="AddMPeriodo()" class="md-raised md-primary btn-report block-button" style="background: none;color: #4caf50;max-height: 60px !important;width: 80% !important; box-shadow: 0px 0px 4px #4caf50;font-size: 12px;">
            <span>Adicionar novo periodo</span> <i class="fas fa-plus" style="vertical-align: initial;"></i>
          </md-button>
        </section>


        <!--
        <md-input-container class="md-block" flex-gt-xs>
          <label>Leads</label>
          <select class="form-control select2" id="select_leads" multiple>
          </select>
        </md-input-container>
        -->

      </md-content>

      <md-content layout-padding>
        <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
          <md-button ng-click="AddTaskAut()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">
            <span ng-hide="saving == true"><?php echo lang2('create'); ?></span>
            <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20">
            </md-progress-circular>
          </md-button>
          <br /><br /><br /><br />
        </section>
      </md-content>
    </md-content>

  </md-sidenav>
</div>

<script type="text/javascript">
  var lang = {};
  lang.group = '<?php echo lang2('group') ?>';
  lang.new = '<?php echo lang2('new') ?>';
  lang.name = '<?php echo lang2('name') ?>';
  lang.add = '<?php echo lang2('add') ?>';
  lang.cancel = '<?php echo lang2('cancel') ?>';
  lang.save = '<?php echo lang2('save') ?>';
  lang.edit = '<?php echo lang2('edit') ?>';
  lang.doIt = '<?php echo lang2('doIt') ?>';
  lang.attention = '<?php echo lang2('attention') ?>';
</script>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>

<script type="text/javascript">
  $(".select2").select2({
    ajax: {
      url: BASE_URL + 'staff/get_staff_ajax/',
      delay: 100,
      dataType: 'json',
      data: function(params) {
        var query = {
          search: params.term,
          page: params.page || 1
        }
        return query;
      },
      cache: true
    },
    placeholder: 'Procurar Produtos...',
    minimumInputLength: 1
  });
</script>

<script src="<?php echo base_url('assets/lib/highcharts/highcharts.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/emailsAut.js?v=1.1.8') ?>"></script>