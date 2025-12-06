<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php');
$user_data = get_user();
?>

<?php $appconfig = get_appconfig(); ?>

<style>
    md-input-container {
        min-height: 34px;
    }
</style>
<link href="<?php echo base_url('assets/css/cssCartao.css?v=1.1.3'); ?>" rel="stylesheet" />
<div class="ciuis-body-content" ng-controller="Settings_Controller">

    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-12">

        <md-toolbar class="toolbar-white">

            <div class="md-toolbar-tools">

                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true">

                    <md-icon><i class="ion-ios-gear text-muted"></i></md-icon>

                </md-button>

                <h2 flex md-truncate><?php echo lang2('crmsettings') ?></h2>

                <?php if (check_privilege('settings', 'edit')) { ?>

                    <md-button ng-click="VersionCheck()" class="md-icon-button" aria-label="Update" ng-cloak>

                        <md-tooltip md-direction="bottom"><?php echo lang2('version_check') ?></md-tooltip>



                    </md-button>

                    <md-button style="width: 140px;" ng-click="UpdateSettings()" class="md-icon-button" aria-label="Save" ng-cloak>

                        <md-progress-circular ng-show="savingSettings == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                        <md-tooltip ng-hide="savingSettings == true" md-direction="bottom"><?php echo lang2('update') ?></md-tooltip>


                        <md-icon ng-hide="savingSettings == true">
                            <div style="color: #000;font-size: 15px; font-weight: 600;margin-top: 5px;float: left;margin-right: 5px;">Salvar</div> <i class="ion-checkmark-circled text-muted"></i>
                        </md-icon>
                    </md-button>

                <?php } ?>

            </div>

        </md-toolbar>

        <md-content class="bg-white">

            <div ng-show="settings.loader" layout-align="center center" class="text-center" id="circular_loader">

                <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
                <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">


                <p style="font-size: 15px;margin-bottom: 5%;">

                    <span>

                        <?php echo lang2('please_wait') ?> <br>

                        <small><strong><?php echo lang2('loading') . ' ' . lang2('settings') . '...' ?></strong></small>

                    </span>

                </p>

            </div>

            <md-tabs ng-show="!settings.loader" md-dynamic-height md-border-bottom md-selected="indexTab">

                <!-- EMPRESA -->
                <md-tab label="EMPRESA">
                    <?php include_once(APPPATH . 'Views/settings/company_settings.php'); ?>
                </md-tab>

                <!-- TIPOS DE USUÁRIO -->
                <md-tab ng-click="get_roles()" label="TIPOS DE USUÁRIO">
                    <?php include_once(APPPATH . 'Views/settings/roles.php'); ?>
                </md-tab>

                <!-- EMAIL -->
                <md-tab label="EMAIL">
                    <?php include_once(APPPATH . 'Views/settings/email_combined.php'); ?>
                </md-tab>

                <!-- WHATSAPP -->
                <md-tab ng-click="get_settingsIa()" label="WHATSAPP">
                    <?php include_once(APPPATH . 'Views/settings/whatsapp.php'); ?>
                </md-tab>

                <!-- ZAPIER LINKEDIN -->
                <md-tab ng-click="get_zapier_linkedin()" label="ZAPIER LINKEDIN">
                    <?php include_once(APPPATH . 'Views/settings/zapier_linkedin.php'); ?>
                </md-tab>

                <!-- IA SALES -->
                <md-tab ng-click="load_documents()" label="IA SALES">
                    <?php include_once(APPPATH . 'Views/settings/sobre.php'); ?>
                </md-tab>

                <!-- ATIVIDADES -->
                <md-tab ng-click="get_atividades()" label="ATIVIDADES">
                    <?php include_once(APPPATH . 'Views/settings/atividades.php'); ?>
                </md-tab>

                <!-- CAMPOS PERSONALIZADOS -->
                <md-tab ng-click="get_custom_fields()" label="CAMPOS PERSONALIZADOS">
                    <?php include_once(APPPATH . 'Views/settings/custom_fields.php'); ?>
                </md-tab>

                <!-- LEADS TO DO -->
                <md-tab ng-click="get_fila_prioridades()" label="LEADS TO DO">
                    <?php include_once(APPPATH . 'Views/settings/fila_leads.php'); ?>
                </md-tab>

                <!-- NOTIFICAÇÕES -->
                <?php
                if ($user_data['admin'] == "1" || $user_data['super_admin'] == "1") {
                ?>
                    <md-tab ng-click="get_avisos()" label="NOTIFICAÇÕES">
                        <?php include_once(APPPATH . 'Views/settings/avisos.php'); ?>
                    </md-tab>
                <?php
                }
                ?>

                <!-- MINHA ASSINATURA -->
                <?php
                if ($user_data['admin'] == "1" && $user_data['super_admin'] != "1") {
                ?>
                    <md-tab ng-click="get_assinatura()" label="MINHA ASSINATURA">
                        <?php include_once(APPPATH . 'Views/settings/assinatura.php'); ?>
                    </md-tab>
                <?php
                }
                ?>

                <!-- ABAS SUPER ADMIN -->
                <?php
                if ($user_data['super_admin'] == "1") {
                ?>
                    <md-tab label="<?php echo lang2('series'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/series.php'); ?>
                    </md-tab>

                    <md-tab label="<?php echo lang2('localization'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/localization.php'); ?>
                    </md-tab>

                    <md-tab label="<?php echo lang2('customization'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/customization.php'); ?>
                    </md-tab>

                    <md-tab label="<?php echo lang2('security'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/security.php'); ?>
                    </md-tab>

                    <md-tab ng-click="get_payment_methods()" label="<?php echo lang2('paymentgateway'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/payment_gateway.php'); ?>
                    </md-tab>

                    <md-tab label="<?php echo lang2('cron_job'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/cron_job.php'); ?>
                    </md-tab>

                    <md-tab ng-click="get_database_backup()" label="<?php echo lang2('backup'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/backup.php'); ?>
                    </md-tab>

                    <md-tab label="<?php echo lang2('modules'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/modules.php'); ?>
                    </md-tab>

                    <md-tab label="<?php echo lang2('system'); ?>">
                        <?php include_once(APPPATH . 'Views/settings/system.php'); ?>
                    </md-tab>

                    <md-tab ng-click="get_planos()" label="Ger. Planos">
                        <?php include_once(APPPATH . 'Views/settings/admin_planos.php'); ?>
                    </md-tab>
                <?php
                }
                ?>

            </md-tabs>

        </md-content>

        <?php include_once(APPPATH . 'Views/settings/sidenaves.php'); ?>

    </div>

    <?php include_once(APPPATH . 'Views/settings/dialogs.php'); ?>


    <div class="modal fade" id="modalStatus" tabindex="-1" role="dialog" aria-labelledby="modalStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">

                <div class="modal-header bg-light">
                    <h5 class="modal-title">
                        Definir etapa do funil: <strong>{{selectedFunil.nm_list}}</strong>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <label class="font-weight-bold">Selecione a etapa:</label>
                    <select class="form-control" ng-model="tempStatus" ng-options="status.id as status.name for status in selectedFunil.leadstatuses">
                        <option value="">-- Selecione --</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" ng-click="saveStatus()">Salvar</button>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAtividades">
        <div class="modal-dialog mdAngular" style="margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b> Adicionar nova atividade</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <div layout="row">
                        <md-input-container flex="100" style='margin-top: 8px;'>
                            <label style="margin-bottom: 0;">Atividade</label>
                            <input ng-model="atividadeModal.nm_atividade_select" type="text">
                        </md-input-container>
                    </div>
                    <div layout="row" style="display: block;">
                        <div class="icons_atv">
                            <md-button style="width: 70px;height: 70px;border-radius: 0;padding: 0;margin: 5px;" ng-repeat="icon in icons_atv" ng-click="selectIcon(icon)" class="md-icon-button" aria-label="Update" ng-cloak>
                                <md-tooltip md-direction="bottom">{{icon}}</md-tooltip>
                                <img ng-class="{'ln_icons_atvSelect': atividadeModal.icon_atvSelect == icon}" class="ln_icons_atv" src="<?= base_url(); ?>/assets/img/atividades/icons/{{icon}}">
                            </md-button>
                        </div>
                        <p class="p_atv">Ou</p>
                        <md-input-container flex="60" style='margin-top: 8px;' md-no-float="true">
                            <input ng-model="atividadeModal.atividade_photo" style="color: #000;" type="file" required="" id="atividade_photo" name="atividade_photo" file-model="atividade_photo" accept="image/*">
                        </md-input-container>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarAtividadeList()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPlano">
        <div class="modal-dialog mdAngular" style="margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b> Adicionar novo plano</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <div layout="row">
                        <md-input-container flex="100" style='margin-top: 8px;'>
                            <label style="margin-bottom: 0;">Plano</label>
                            <input ng-model="planoModal.nm_plan" type="text">
                        </md-input-container>

                        <md-input-container flex="100" style='margin-top: 8px;'>
                            <label style="margin-bottom: 0;">Valor mensal</label>
                            <input class="money" ng-model="planoModal.valor" type="text">
                        </md-input-container>

                        <md-input-container flex="100" style='margin-top: 8px;'>
                            <label style="margin-bottom: 0;">Valor Anual</label>
                            <input class="money" ng-model="planoModal.valor_anual" type="text">
                        </md-input-container>

                        <md-input-container flex="100" style='margin-top: 8px;'>
                            <label style="margin-bottom: 0;">Minimo de usuarios</label>
                            <input ng-model="planoModal.min_user" type="number">
                        </md-input-container>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarPlanoList(planoModal.id_plan)">Salvar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalPagamento">
        <div class="modal-dialog mdAngular" style="margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b> Adicionar nova forma</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <div layout="row">
                        <md-input-container flex="100" style='margin-top: 8px;'>
                            <label style="margin-bottom: 0;">Forma de Pagamento</label>
                            <input ng-model="pagamentoModal.nm_forma" type="text">
                        </md-input-container>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarPagamentoList(pagamentoModal.id_forma)">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAviso">
        <div class="modal-dialog mdAngular" style="margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b> Adicionar novo aviso</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <md-input-container class="md-block" style='margin-top: 8px;'>
                                <label style="margin-bottom: 0;">Nome do aviso</label>
                                <input ng-model="avisoModal.nm_aviso" type="text">
                            </md-input-container>
                        </div>

                        <div class="col-md-4">
                            <md-input-container class="md-block" style='margin-top: 2.5px;'>
                                <label>Tipo</label>
                                <md-select placeholder="" ng-model="avisoModal.tipo">
                                    <md-option value="1">Ociosidade</md-option>
                                    <md-option value="2">Conversão lead para cliente</md-option>
                                    <md-option value="3">Novos leads adicionados</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-4" ng-show="avisoModal.tipo == 1">
                            <md-input-container class="md-block" style='margin-top: 20.5px;'>
                                <label style="margin-bottom: 0;">Tempo em minutos</label>
                                <input ng-model="avisoModal.tempo" type="number">
                            </md-input-container>
                        </div>

                        <div class="col-md-4">
                            <md-input-container class="md-block">
                                <label>Opção de alerta</label>
                                <md-select placeholder="" ng-model="avisoModal.opcaoAlerta">
                                    <md-option value="1">Sistema</md-option>
                                    <md-option value="2">Email</md-option>
                                    <md-option value="3">Ambos</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-4">
                            <md-input-container class="md-block" ng-show="avisoModal.tipo == 1">
                                <label>Frequência</label>
                                <md-select placeholder="" ng-model="avisoModal.frequencia">
                                    <md-option value="1">Diário</md-option>
                                    <md-option value="2">Semanal</md-option>
                                    <md-option value="3">Mensal</md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4" ng-if="avisoModal.frequencia == 2">
                            <md-input-container class="md-block">
                                <label>Dia da semana</label>
                                <md-select placeholder="" ng-model="avisoModal.diaSemana">
                                    <md-option value="1">Segunda</md-option>
                                    <md-option value="2">Terça</md-option>
                                    <md-option value="3">Quarta</md-option>
                                    <md-option value="4">Quinta</md-option>
                                    <md-option value="5">Sexta</md-option>
                                    <md-option value="6">Sábado</md-option>
                                    <md-option value="7">Domingo</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-4" ng-if="avisoModal.frequencia == 3">
                            <md-input-container class="md-block">
                                <label>Dia do mes</label>
                                <input ng-model="avisoModal.diaMes" min="1" max="28" type="number">
                            </md-input-container>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" style="margin-top: 30px; padding: 0 10px;">
                            <md-input-container class="md-block">
                                <label>Funcionários para receber o aviso</label>
                                <md-select multiple placeholder="<?php echo lang2('choosestaff'); ?>" id="flt_funcionario" ng-model="avisoModal.funcionarios" style="min-width: 200px;">
                                    <md-option ng-value="staff.id" ng-repeat="staff in staff">{{staff.name}}</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-12" style="margin-top: 30px; padding: 0 10px;">
                            <md-input-container class="md-block" id="inpt_email" style="margin-top: 20px;">
                                <label>Emails</label>
                                <select multiple placeholder="<?php echo lang2('choosestaff'); ?>" id="select_emails" ng-model="avisoModal.emails" style="min-width: 200px;">
                                    <option ng-value="email" ng-repeat="email in avisoModal.emails_adicionados">{{email}}</option>
                                </select>
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <md-input-container class="md-block" ng-if="avisoModal.anexo != null">
                                <p style="font-size: 11px;margin-bottom: 0;">Anexo - <a href="javaScript:void(0);" ng-click="removeAnexo()">Remover</a> </p>
                                <a target="_blank" style="color: #007eff;" href="<?= base_url('') ?>/uploads/anexos/{{avisoModal.anexo}}">{{avisoModal.anexo}}</a>
                            </md-input-container>

                            <md-input-container class="md-block md-input-has-value" md-no-float="true">
                                <label style="transform: translate3d(0,6px,0) scale(.75) !important;"><span ng-if="avisoModal.anexo != null">Editar</span> Anexo</label>
                                <input style = "display: block;" type="file" value="" ng-model="avisoModal.anexo" id="anexo" name="anexo" file-model="anexo">
                            </md-input-container>
                        </div>

                        <div class="col-md-6">
                            <md-input-container class="md-block" ng-if="avisoModal.alerta != null" >
                                <p style="font-size: 11px;margin-bottom: 0;">Audio de alerta - <a href="javaScript:void(0);" ng-click="removeAlerta()">Remover</a> </p>
                                <a target="_blank" style="color: #007eff;" href="<?= base_url('') ?>/uploads/anexos/{{avisoModal.alerta}}">{{avisoModal.alerta}}</a>
                            </md-input-container>

                            <md-input-container class="md-block md-input-has-value" md-no-float="true">
                                <label style="transform: translate3d(0,6px,0) scale(.75) !important;"><span ng-if="avisoModal.alerta != null">Editar</span> Audio de alerta</label>
                                <input style = "display: block;" type="file" value="" ng-model="avisoModal.alerta" id="alerta" name="alerta" file-model="alerta">
                            </md-input-container>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12" style="padding-top: 15px;">
                            <textarea class="tinymce" ng-model="avisoModal.menssagem"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarAviso(avisoModal.id_aviso)">Salvar</button>
                </div>
            </div>
        </div>
    </div>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Permissions" style="width: 450px;" ng-cloak>
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <h2 flex md-truncate>Permissões</h2>
            </div>
        </md-toolbar>
        <md-content layout-padding>
            <div ng-show="permissionModalLoader" layout-align="center center" class="text-center" id="circular_loader" ng-cloak>
                <md-progress-circular md-mode="indeterminate" md-diameter="30"></md-progress-circular>
                <p style="font-size: 15px;margin-bottom: 5%;">
                    <span><?php echo lang2('please_wait') ?>
                </p>
            </div>

            <table md-table md-progress="promise">
                <thead md-head>
                    <tr md-row>
                        <th md-column>
                            <md-checkbox md-no-ink aria-label="edit" ng-model="permissionModal.permission_all" ng-change="selectAll()" class="md-primary"></md-checkbox>
                        </th>
                        <th md-column>Permissão</th>
                    </tr>
                </thead>
                <tbody md-body>
                    <tr class="select_row" md-row ng-repeat="permission in permissions_all">
                        <td md-cell>
                            <md-checkbox ng-checked="permission.permitido == '1'" md-no-ink aria-label="edit" ng-model="permissionModal.permission[permission.id]" class="md-primary"></md-checkbox>
                        </td>
                        <td md-cell>
                            <span ng-bind="permission.permission_key"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </md-content>

        <md-content layout-padding>
            <section layout="row" layout-sm="column" layout-align="center" layout-wrap>
                <md-button ng-click="AddPermission()" class="md-raised md-primary btn-report block-button">
                    <span><?php echo lang2('save'); ?></span>
                    <md-progress-circular class="white" md-mode="indeterminate" md-diameter="20">
                    </md-progress-circular>
                </md-button>
            </section>
        </md-content>
    </md-sidenav>

    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pagar
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </h5>
                </div>
                <div class="modal-body">
                    <div class='checkout'>
                        <h2 style="margin-bottom: 0;">Realizar pagamento</h2>
                        <img class='payment2' style="max-width: 215px;float: right;margin-top: 20px;" src="<?= base_url(); ?>/assets/img/compra-segura.png">
                        <form id="form-checkout" onsubmit="return false">
                            <div style="display: none;">
                                <input type="hidden" type="text" name="transactionAmount" id="transactionAmount" value="0" />
                                <input type="hidden" type="text" name="paymentMethodId" id="paymentMethodId" />
                                <input type="hidden" type="text" name="description" id="description" value="Venda de plano" />
                                <input type="hidden" type="text" name="cardNumber" id="cardNumber" />

                                <select type="hidden" name="installments" id="installments" value="1">
                                </select>
                                <select type="hidden" name="docType" id="docType" value="CPF"></select>
                                <select name="issuer" id="issuer" value="1">
                                </select>

                            </div>

                            <input type="hidden" name="email" id="email" value="pagamentos@lead1crm.com" />
                            <input type="hidden" id="cardExpirationMonth" name="cardExpirationMonth" value="" />
                            <input type="hidden" id="cardExpirationYear" name="cardExpirationYear" value="" />
                            <input type='hidden' id='cardcpf2' />

                            <div id='payment' class='payment'>
                                <div class='card'>
                                    <div class='card-content'>
                                        <svg id='logo-visa' enable-background="new 0 0 50 70" height="70px" version="1.1" viewBox="0 0 50 50" width="70px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <g>
                                                <g>
                                                    <polygon clip-rule="evenodd" fill="#f4f5f9" fill-rule="evenodd" points="17.197,32.598 19.711,17.592 23.733,17.592     21.214,32.598   " />
                                                    <path clip-rule="evenodd" d="M35.768,17.967c-0.797-0.287-2.053-0.621-3.596-0.621    c-3.977,0-6.752,2.029-6.776,4.945c-0.023,2.154,1.987,3.358,3.507,4.08c1.568,0.738,2.096,1.201,2.076,1.861    c0,1.018-1.238,1.471-2.395,1.471c-1.604,0-2.455-0.232-3.773-0.787l-0.53-0.248l-0.547,3.348    c0.929,0.441,2.659,0.789,4.462,0.811c4.217,0,6.943-2.012,6.979-5.135c0.025-1.692-1.053-2.999-3.369-4.071    c-1.393-0.685-2.246-1.134-2.246-1.844c0-0.645,0.723-1.306,2.295-1.306c1.314-0.024,2.268,0.271,3.002,0.58l0.365,0.167    L35.768,17.967z" fill="#f4f5f9" fill-rule="evenodd" />
                                                    <path clip-rule="evenodd" d="M46.055,17.616h-3.102c-0.955,0-1.688,0.272-2.117,1.24    l-5.941,13.767h4.201c0,0,0.688-1.869,0.852-2.262c0.469,0,4.547,0,5.133,0c0.123,0.518,0.49,2.262,0.49,2.262h3.711    L46.055,17.616 M41.1,27.277c0.328-0.842,1.609-4.175,1.609-4.175c-0.041,0.043,0.328-0.871,0.529-1.43l0.256,1.281    c0,0,0.773,3.582,0.938,4.324H41.1z" fill="#f4f5f9" fill-rule="evenodd" />
                                                    <path clip-rule="evenodd" d="M13.843,17.616L9.905,27.842l-0.404-2.076    c-0.948-2.467-2.836-4.634-5.53-6.163l3.564,12.995h4.243l6.312-14.982H13.843z" fill="#f4f5f9" fill-rule="evenodd" />
                                                    <path clip-rule="evenodd" d="M7.232,17.174H0.755l-0.037,0.333    c5.014,1.242,8.358,4.237,9.742,7.841l-1.42-6.884C8.798,17.507,8.105,17.223,7.232,17.174L7.232,17.174z" fill="#f4f5f9" fill-rule="evenodd" />
                                                </g>
                                            </g>
                                        </svg>
                                        <h5>Numero do cartão</h5>
                                        <h6 id='label-cardnumber'>0000 0000 0000 0000</h6>
                                        <h5>Expiração<span>CVC</span></h5>
                                        <h6><span style="margin-left: 0;" id='label-cardexpiration'>00 / 0000</span><span id="label-cvv">000</span></h6>
                                    </div>
                                    <div class='wave'></div>
                                </div>
                                <div class='card-form'>
                                    <p class='field' style="width: 100%;">
                                        <svg id='i-cardfront' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="M471.5,88h-432C17.72,88,0,105.72,0,127.5v256C0,405.28,17.72,423,39.5,423h432c21.78,0,39.5-17.72,39.5-39.5v-256   C511,105.72,493.28,88,471.5,88z M496,383.5c0,13.509-10.991,24.5-24.5,24.5h-432C25.991,408,15,397.009,15,383.5v-256   c0-13.509,10.991-24.5,24.5-24.5h432c13.509,0,24.5,10.991,24.5,24.5V383.5z" fill="#dddfe6" />
                                                <path d="M239.5,352h-176c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h176c4.142,0,7.5-3.358,7.5-7.5S243.642,352,239.5,352z" fill="#dddfe6" />
                                                <path d="M343.5,352h-72c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h72c4.142,0,7.5-3.358,7.5-7.5S347.642,352,343.5,352z" fill="#dddfe6" />
                                                <path d="M79.5,239h48c12.958,0,23.5-10.542,23.5-23.5v-32c0-12.958-10.542-23.5-23.5-23.5h-48C66.542,160,56,170.542,56,183.5v32   C56,228.458,66.542,239,79.5,239z M136,183.5v8.5h-8.5c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h8.5v8.5   c0,4.687-3.813,8.5-8.5,8.5H111v-49h16.5C132.187,175,136,178.813,136,183.5z M79.5,175H96v49H79.5c-4.687,0-8.5-3.813-8.5-8.5V207   h8.5c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5H71v-8.5C71,178.813,74.813,175,79.5,175z" fill="#dddfe6" />
                                                <path d="M63.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16C56,315.642,59.358,319,63.5,319   z" fill="#dddfe6" />
                                                <path d="M80,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S80,291.358,80,295.5z" fill="#dddfe6" />
                                                <path d="M104,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S104,291.358,104,295.5z" fill="#dddfe6" />
                                                <path d="M128,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S128,291.358,128,295.5z" fill="#dddfe6" />
                                                <path d="M167.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C160,315.642,163.358,319,167.5,319z" fill="#dddfe6" />
                                                <path d="M191.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C184,315.642,187.358,319,191.5,319z" fill="#dddfe6" />
                                                <path d="M215.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C208,315.642,211.358,319,215.5,319z" fill="#dddfe6" />
                                                <path d="M239.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C247,291.358,243.642,288,239.5,288z" fill="#dddfe6" />
                                                <path d="M271.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C264,315.642,267.358,319,271.5,319z" fill="#dddfe6" />
                                                <path d="M295.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C288,315.642,291.358,319,295.5,319z" fill="#dddfe6" />
                                                <path d="M319.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C312,315.642,315.358,319,319.5,319z" fill="#dddfe6" />
                                                <path d="M343.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C351,291.358,347.642,288,343.5,288z" fill="#dddfe6" />
                                                <path d="M375.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C383,291.358,379.642,288,375.5,288z" fill="#dddfe6" />
                                                <path d="M399.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C407,291.358,403.642,288,399.5,288z" fill="#dddfe6" />
                                                <path d="M423.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C431,291.358,427.642,288,423.5,288z" fill="#dddfe6" />
                                                <path d="M447.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C455,291.358,451.642,288,447.5,288z" fill="#dddfe6" />
                                                <path d="M415.5,160h-48c-21.78,0-39.5,17.72-39.5,39.5s17.72,39.5,39.5,39.5h48c21.78,0,39.5-17.72,39.5-39.5S437.28,160,415.5,160   z M415.5,224h-48c-13.509,0-24.5-10.991-24.5-24.5s10.991-24.5,24.5-24.5h48c13.509,0,24.5,10.991,24.5,24.5S429.009,224,415.5,224   z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardname' name='cardname' data-checkout="cardholderName" placeholder='Ex: joão da silva' title='Nome' />
                                    </p>

                                    <p class='field' style="width: 65%;">
                                        <svg id='i-cardfront' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="M471.5,88h-432C17.72,88,0,105.72,0,127.5v256C0,405.28,17.72,423,39.5,423h432c21.78,0,39.5-17.72,39.5-39.5v-256   C511,105.72,493.28,88,471.5,88z M496,383.5c0,13.509-10.991,24.5-24.5,24.5h-432C25.991,408,15,397.009,15,383.5v-256   c0-13.509,10.991-24.5,24.5-24.5h432c13.509,0,24.5,10.991,24.5,24.5V383.5z" fill="#dddfe6" />
                                                <path d="M239.5,352h-176c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h176c4.142,0,7.5-3.358,7.5-7.5S243.642,352,239.5,352z" fill="#dddfe6" />
                                                <path d="M343.5,352h-72c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h72c4.142,0,7.5-3.358,7.5-7.5S347.642,352,343.5,352z" fill="#dddfe6" />
                                                <path d="M79.5,239h48c12.958,0,23.5-10.542,23.5-23.5v-32c0-12.958-10.542-23.5-23.5-23.5h-48C66.542,160,56,170.542,56,183.5v32   C56,228.458,66.542,239,79.5,239z M136,183.5v8.5h-8.5c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h8.5v8.5   c0,4.687-3.813,8.5-8.5,8.5H111v-49h16.5C132.187,175,136,178.813,136,183.5z M79.5,175H96v49H79.5c-4.687,0-8.5-3.813-8.5-8.5V207   h8.5c4.142,0,7.5-3.358,7.5-7.5s-3.358-7.5-7.5-7.5H71v-8.5C71,178.813,74.813,175,79.5,175z" fill="#dddfe6" />
                                                <path d="M63.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16C56,315.642,59.358,319,63.5,319   z" fill="#dddfe6" />
                                                <path d="M80,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S80,291.358,80,295.5z" fill="#dddfe6" />
                                                <path d="M104,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S104,291.358,104,295.5z" fill="#dddfe6" />
                                                <path d="M128,295.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5S128,291.358,128,295.5z" fill="#dddfe6" />
                                                <path d="M167.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C160,315.642,163.358,319,167.5,319z" fill="#dddfe6" />
                                                <path d="M191.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C184,315.642,187.358,319,191.5,319z" fill="#dddfe6" />
                                                <path d="M215.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C208,315.642,211.358,319,215.5,319z" fill="#dddfe6" />
                                                <path d="M239.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C247,291.358,243.642,288,239.5,288z" fill="#dddfe6" />
                                                <path d="M271.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C264,315.642,267.358,319,271.5,319z" fill="#dddfe6" />
                                                <path d="M295.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C288,315.642,291.358,319,295.5,319z" fill="#dddfe6" />
                                                <path d="M319.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C312,315.642,315.358,319,319.5,319z" fill="#dddfe6" />
                                                <path d="M343.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C351,291.358,347.642,288,343.5,288z" fill="#dddfe6" />
                                                <path d="M375.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C383,291.358,379.642,288,375.5,288z" fill="#dddfe6" />
                                                <path d="M399.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C407,291.358,403.642,288,399.5,288z" fill="#dddfe6" />
                                                <path d="M423.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C431,291.358,427.642,288,423.5,288z" fill="#dddfe6" />
                                                <path d="M447.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C455,291.358,451.642,288,447.5,288z" fill="#dddfe6" />
                                                <path d="M415.5,160h-48c-21.78,0-39.5,17.72-39.5,39.5s17.72,39.5,39.5,39.5h48c21.78,0,39.5-17.72,39.5-39.5S437.28,160,415.5,160   z M415.5,224h-48c-13.509,0-24.5-10.991-24.5-24.5s10.991-24.5,24.5-24.5h48c13.509,0,24.5,10.991,24.5,24.5S429.009,224,415.5,224   z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardNumber2' name='cardNumber2' placeholder='1234 5678 9123 4567' title='Numero do cartão' />
                                    </p>
                                    <p class='field space' style="width: calc(35% - 5px);margin-left: 5px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="i-calendar" x="0px" y="0px" viewBox="0 0 191.259 191.259" style="enable-background:new 0 0 191.259 191.259;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <circle cx="59.768" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="83.676" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="107.583" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="35.861" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="59.768" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="83.676" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="107.583" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="155.398" cy="107.541" r="3.984" fill="#dddfe6" />
                                                <circle cx="131.49" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="155.398" cy="83.634" r="3.985" fill="#dddfe6" />
                                                <circle cx="35.861" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="59.768" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="83.676" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="107.583" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="131.49" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="155.398" cy="131.449" r="3.985" fill="#dddfe6" />
                                                <circle cx="35.861" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <circle cx="59.768" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <circle cx="83.676" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <circle cx="107.583" cy="155.356" r="3.985" fill="#dddfe6" />
                                                <path d="M131.49,119.495c6.603,0,11.954-5.351,11.954-11.954s-5.351-11.954-11.954-11.954   c-6.603,0-11.954,5.351-11.954,11.954S124.887,119.495,131.49,119.495z M131.49,103.557c2.199,0,3.985,1.786,3.985,3.984   s-1.786,3.984-3.985,3.984s-3.984-1.786-3.984-3.984S129.292,103.557,131.49,103.557z" fill="#dddfe6" />
                                                <path d="M175.321,15.98h-7.969v-3.985c0-6.601-5.354-11.954-11.954-11.954   c-6.603,0-11.954,5.352-11.954,11.954v3.985h-95.63v-3.985c0-6.601-5.354-11.954-11.954-11.954   c-6.603,0-11.954,5.352-11.954,11.954v3.985h-7.969C7.136,15.98,0,23.116,0,31.918v15.854v7.969v119.537   c0,8.802,7.136,15.938,15.938,15.938h159.382c8.802,0,15.938-7.136,15.938-15.938V55.742v-7.969V31.918   C191.259,23.116,184.123,15.98,175.321,15.98z M151.413,23.949V15.98v-3.985c0-2.201,1.782-3.985,3.985-3.985   c2.198,0,3.984,1.784,3.984,3.985v3.985v7.969v3.984c0,2.2-1.786,3.985-3.984,3.985c-2.202,0-3.985-1.784-3.985-3.985V23.949z    M31.876,23.949V15.98v-3.985c0-2.201,1.782-3.985,3.985-3.985c2.199,0,3.985,1.784,3.985,3.985v3.985v7.969v3.984   c0,2.2-1.786,3.985-3.985,3.985c-2.202,0-3.985-1.784-3.985-3.985V23.949z M183.29,175.279c0,4.399-3.564,7.969-7.969,7.969H15.938   c-4.405,0-7.969-3.57-7.969-7.969V55.742H183.29V175.279z M183.29,47.773H7.969V31.918c0-4.403,3.564-7.969,7.969-7.969h7.969   v3.984c0,6.601,5.35,11.954,11.954,11.954c6.6,0,11.954-5.352,11.954-11.954v-3.984h95.63v3.984c0,6.601,5.35,11.954,11.954,11.954   c6.599,0,11.954-5.352,11.954-11.954v-3.984h7.969c4.405,0,7.969,3.566,7.969,7.969V47.773z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardexpiration' name='cardexpiration' placeholder="MM / YYYY" title='Expiração' />
                                    </p>
                                    <p class='field' style="width: 35%;">
                                        <svg id='i-cardback' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="M63.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16C71,291.358,67.642,288,63.5,288   z" fill="#dddfe6" />
                                                <path d="M87.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16C95,291.358,91.642,288,87.5,288   z" fill="#dddfe6" />
                                                <path d="M111.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C119,291.358,115.642,288,111.5,288z" fill="#dddfe6" />
                                                <path d="M135.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C143,291.358,139.642,288,135.5,288z" fill="#dddfe6" />
                                                <path d="M167.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C160,315.642,163.358,319,167.5,319z" fill="#dddfe6" />
                                                <path d="M199,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S199,315.642,199,311.5z" fill="#dddfe6" />
                                                <path d="M223,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S223,315.642,223,311.5z" fill="#dddfe6" />
                                                <path d="M239.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C247,291.358,243.642,288,239.5,288z" fill="#dddfe6" />
                                                <path d="M271.5,319c4.142,0,7.5-3.358,7.5-7.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16   C264,315.642,267.358,319,271.5,319z" fill="#dddfe6" />
                                                <path d="M303,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S303,315.642,303,311.5z" fill="#dddfe6" />
                                                <path d="M327,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S327,315.642,327,311.5z" fill="#dddfe6" />
                                                <path d="M351,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S351,315.642,351,311.5z" fill="#dddfe6" />
                                                <path d="M383,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S383,315.642,383,311.5z" fill="#dddfe6" />
                                                <path d="M407,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S407,315.642,407,311.5z" fill="#dddfe6" />
                                                <path d="M431,311.5v-16c0-4.142-3.358-7.5-7.5-7.5s-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5S431,315.642,431,311.5z" fill="#dddfe6" />
                                                <path d="M447.5,288c-4.142,0-7.5,3.358-7.5,7.5v16c0,4.142,3.358,7.5,7.5,7.5s7.5-3.358,7.5-7.5v-16   C455,291.358,451.642,288,447.5,288z" fill="#dddfe6" />
                                                <path d="M447.5,216h-384C50.542,216,40,226.542,40,239.5v8c0,12.958,10.542,23.5,23.5,23.5h384c12.958,0,23.5-10.542,23.5-23.5v-8   C471,226.542,460.458,216,447.5,216z M456,247.5c0,4.687-3.813,8.5-8.5,8.5h-384c-4.687,0-8.5-3.813-8.5-8.5v-8   c0-4.687,3.813-8.5,8.5-8.5h384c4.687,0,8.5,3.813,8.5,8.5V247.5z" fill="#dddfe6" />
                                                <path d="M447.5,352h-176c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h176c4.142,0,7.5-3.358,7.5-7.5S451.642,352,447.5,352z" fill="#dddfe6" />
                                                <path d="M239.5,352h-72c-4.142,0-7.5,3.358-7.5,7.5s3.358,7.5,7.5,7.5h72c4.142,0,7.5-3.358,7.5-7.5S243.642,352,239.5,352z" fill="#dddfe6" />
                                                <path d="M511,159.498V127.5c0-21.78-17.72-39.5-39.5-39.5h-432C17.72,88,0,105.72,0,127.5v47.998c0,0.001,0,0.003,0,0.005V383.5   C0,405.28,17.72,423,39.5,423h432c21.78,0,39.5-17.72,39.5-39.5V159.502C511,159.501,511,159.499,511,159.498z M496,184h-6.394   l6.394-6.394V184z M449.606,184l41-41H496v13.394L468.394,184H449.606z M409.606,184l41-41h18.787l-41,41H409.606z M369.606,184   l41-41h18.787l-41,41H369.606z M329.606,184l41-41h18.787l-41,41H329.606z M289.606,184l41-41h18.787l-41,41H289.606z M249.606,184   l41-41h18.787l-41,41H249.606z M209.606,184l41-41h18.787l-41,41H209.606z M169.606,184l41-41h18.787l-41,41H169.606z M129.606,184   l41-41h18.787l-41,41H129.606z M89.606,184l41-41h18.787l-41,41H89.606z M49.606,184l41-41h18.787l-41,41H49.606z M15,184v-5.394   L50.606,143h18.787l-41,41H15z M15,143h14.394L15,157.394V143z M39.5,103h432c13.509,0,24.5,10.991,24.5,24.5v0.5h-8.497   c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.003,0-0.005,0   h-39.995c-0.002,0-0.003,0-0.005,0h-39.995c-0.002,0-0.004,0-0.005,0h-39.995c-0.001,0-0.003,0-0.005,0h-39.995   c-0.001,0-0.003,0-0.005,0h-39.995c-0.001,0-0.003,0-0.005,0h-39.995c-0.001,0-0.003,0-0.005,0H87.502c-0.001,0-0.003,0-0.005,0   H47.502c-0.001,0-0.003,0-0.005,0H15v-0.5C15,113.991,25.991,103,39.5,103z M471.5,408h-432C25.991,408,15,397.009,15,383.5V199   h481v184.5C496,397.009,485.009,408,471.5,408z" fill="#dddfe6" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardcvc' name='securityCode' data-checkout="securityCode" placeholder="123" title='CVC' />
                                    </p>

                                    <p class='field' style="width: calc(65% - 5px);margin-left: 5px;">
                                        <svg id='i-cardback' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 28 28;fill:#d9d7d7" xml:space="preserve" width="28px" height="28px">
                                            <g>
                                                <path d="m376.85 357.4 16.828 5.0938c-1.1289 4.7148-2.9062 8.6523-5.3398 11.82-2.4297 3.168-5.4531 5.5547-9.0586 7.1602-3.6016 1.6211-8.1875 2.418-13.746 2.418-6.7578 0-12.285-0.98438-16.551-2.9375-4.2812-1.9688-7.9883-5.4258-11.098-10.375-3.0977-4.9492-4.6602-11.27-4.6602-18.984 0-10.289 2.7344-18.203 8.2031-23.715 5.4688-5.5273 13.211-8.2891 23.207-8.2891 7.8281 0 13.977 1.5781 18.461 4.7461 4.4844 3.168 7.8125 8.0312 9.9844 14.586l-16.957 3.7773c-0.59375-1.8945-1.2148-3.2852-1.8672-4.168-1.0703-1.4609-2.3867-2.5898-3.9336-3.3867-1.5625-0.79688-3.3008-1.1875-5.2227-1.1875-4.3398 0-7.6836 1.75-9.9961 5.2539-1.75 2.6055-2.6328 6.6836-2.6328 12.254 0 6.9023 1.0547 11.617 3.1406 14.18 2.0977 2.5625 5.0352 3.8359 8.8125 3.8359 3.6758 0 6.4531-1.0273 8.332-3.0977 1.8789-2.0547 3.2422-5.0508 4.0938-8.9844zm26.07-36.75h31.918c6.9453 0 12.152 1.6484 15.609 4.9609 3.457 3.2969 5.1953 8.0156 5.1953 14.105 0 6.2773-1.8789 11.184-5.6562 14.715s-9.5352 5.2969-17.273 5.2969h-10.504v23.062h-19.285v-62.141zm19.285 26.492h4.7031c3.7031 0 6.293-0.63672 7.7969-1.9258 1.4922-1.2891 2.2422-2.9375 2.2422-4.9336 0-1.9531-0.65234-3.6172-1.9531-4.9609-1.2891-1.3594-3.7344-2.0391-7.3203-2.0391h-5.4688zm43.562-26.492h47.484v13.355h-28.199v10.852h24.074v12.543h-24.074v25.391h-19.285z" />
                                                <path d="m323.08 287.73h196.21c12.098 0 23.086 4.9414 31.051 12.906 7.9648 7.9648 12.906 18.953 12.906 31.051v40.113c0 12.098-4.9414 23.086-12.906 31.051s-18.953 12.906-31.051 12.906h-196.21c-12.098 0-23.086-4.9414-31.051-12.906s-12.906-18.953-12.906-31.051v-40.113c0-12.098 4.9414-23.086 12.906-31.051 7.9648-7.9648 18.953-12.906 31.051-12.906zm196.21 16.129h-196.21c-7.6445 0-14.602 3.1328-19.648 8.1797s-8.1797 12.004-8.1797 19.648v40.113c0 7.6445 3.1328 14.602 8.1797 19.648 5.0469 5.0469 12.004 8.1797 19.648 8.1797h196.21c7.6445 0 14.602-3.1328 19.648-8.1797 5.0469-5.0469 8.1797-12.004 8.1797-19.648v-40.113c0-7.6445-3.1328-14.602-8.1797-19.648s-12.004-8.1797-19.648-8.1797z" />
                                                <path d="m483.73 407.69v19.055c0 11.152-4.5547 21.281-11.895 28.621-7.3398 7.3398-17.473 11.895-28.621 11.895h-190.27c-11.152 0-21.281-4.5547-28.621-11.895-7.3398-7.3398-11.895-17.473-11.895-28.621v-293.49c0-11.152 4.5547-21.281 11.895-28.621 7.3398-7.3398 17.473-11.895 28.621-11.895h190.27c11.152 0 21.281 4.5547 28.621 11.895 7.3398 7.3398 11.895 17.473 11.895 28.621v162.54h-16.129v-162.54c0-6.6992-2.7461-12.797-7.1719-17.219-4.4258-4.4258-10.52-7.1719-17.219-7.1719h-190.27c-6.6992 0-12.797 2.7461-17.219 7.1719-4.4258 4.4258-7.1719 10.52-7.1719 17.219v293.49c0 6.6992 2.7461 12.797 7.1719 17.219 4.4258 4.4258 10.52 7.1719 17.219 7.1719h190.27c6.6992 0 12.797-2.7461 17.219-7.1719 4.4258-4.4258 7.1719-10.52 7.1719-17.219v-19.055z" />
                                                <path d="m483.73 407.69v19.055c0 11.152-4.5547 21.281-11.895 28.621-7.3398 7.3398-17.473 11.895-28.621 11.895h-190.27c-11.152 0-21.281-4.5547-28.621-11.895-7.3398-7.3398-11.895-17.473-11.895-28.621v-293.49c0-11.152 4.5547-21.281 11.895-28.621 7.3398-7.3398 17.473-11.895 28.621-11.895h190.27c11.152 0 21.281 4.5547 28.621 11.895 7.3398 7.3398 11.895 17.473 11.895 28.621v162.54h-16.129v-162.54c0-6.6992-2.7461-12.797-7.1719-17.219-4.4258-4.4258-10.52-7.1719-17.219-7.1719h-190.27c-6.6992 0-12.797 2.7461-17.219 7.1719-4.4258 4.4258-7.1719 10.52-7.1719 17.219v293.49c0 6.6992 2.7461 12.797 7.1719 17.219 4.4258 4.4258 10.52 7.1719 17.219 7.1719h190.27c6.6992 0 12.797-2.7461 17.219-7.1719 4.4258-4.4258 7.1719-10.52 7.1719-17.219v-19.055z" />
                                                <path d="m273.26 143.81c-4.4531 0-8.0625 3.6094-8.0625 8.0625s3.6094 8.0625 8.0625 8.0625h149.63c4.4531 0 8.0625-3.6094 8.0625-8.0625s-3.6094-8.0625-8.0625-8.0625z" />
                                                <path d="m448.3 462.94c-24.797 41.25-53.711 29.523-90.336 14.672-8.8086-3.5742-18.117-7.3477-27.523-10.359l4.9141-15.309c10.371 3.3203 19.766 7.1328 28.66 10.738 29.512 11.969 52.809 21.418 70.488-7.9922l13.797 8.2539zm-227.19-16.805-2.043-0.39453c-10.961-2.125-20.047-8.5234-25.855-17.121-5.8047-8.5938-8.3477-19.398-6.2266-30.332l25.477-131.47 15.812 3.0234-25.477 131.47c-1.2773 6.5859 0.26172 13.105 3.7695 18.301 3.5039 5.1875 8.9609 9.0469 15.523 10.316l2.043 0.39453zm27.328-350.02c26.629-41.625 52.969-30.27 89.391-14.566 8.7266 3.7617 18.105 7.8047 28.188 11.273l-5.2305 15.246c-10.633-3.6562-20.312-7.8281-29.32-11.711-29.16-12.57-50.254-21.664-69.48 8.3945l-13.543-8.6328zm228.63 18.152c10.961 2.125 20.047 8.5234 25.855 17.121 5.8047 8.5938 8.3477 19.395 6.2266 30.332l-25.586 132.05-15.812-3.0234 25.586-132.05c1.2773-6.5859-0.26172-13.105-3.7695-18.301-3.5039-5.1875-8.9609-9.0469-15.523-10.316z" />
                                                <path d="m273.26 189.54c-4.4531 0-8.0625 3.6094-8.0625 8.0625s3.6094 8.0625 8.0625 8.0625h149.63c4.4531 0 8.0625-3.6094 8.0625-8.0625s-3.6094-8.0625-8.0625-8.0625z" />
                                                <path d="m273.26 235.26c-4.4531 0-8.0625 3.6094-8.0625 8.0625 0 4.4531 3.6094 8.0625 8.0625 8.0625h149.63c4.4531 0 8.0625-3.6094 8.0625-8.0625 0-4.4531-3.6094-8.0625-8.0625-8.0625z" />
                                            </g>
                                        </svg>
                                        <input type='text' id='cardcpf' name="docNumber" data-checkout="docNumber" placeholder="CPF" title='CPF' />
                                    </p>


                                    <button class='button-cta' title='Confirme o pagamento'>Pagar R$ <span id="spanValor"></span></button>
                                </div>
                            </div>
                        </form>
                        <div id='paid' class='paid'>
                            <svg id='icon-paid' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 310.277 310.277" style="enable-background:new 0 0 310.277 310.277;" xml:space="preserve" width="180px" height="180px">
                                <g>
                                    <path d="M155.139,0C69.598,0,0,69.598,0,155.139c0,85.547,69.598,155.139,155.139,155.139   c85.547,0,155.139-69.592,155.139-155.139C310.277,69.598,240.686,0,155.139,0z M144.177,196.567L90.571,142.96l8.437-8.437   l45.169,45.169l81.34-81.34l8.437,8.437L144.177,196.567z" fill="#3ac569" />
                                </g>
                            </svg>
                            <h2>Pagamento completo!.</h2>
                            <h2>Obrigado!</h2>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPermissaoExport">
        <div class="modal-dialog mdAngular" style="margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b> Adicionar permissões de exportação</b>
                </div>
                <div id="modalBody" class="modal-body">

                    <table md-table md-progress="promise">
                        <thead md-head>
                            <tr md-row>
                                <th style="padding-left: 28px;">
                                    #
                                </th>
                                <th>Permissão para exportar</th>
                            </tr>
                        </thead>
                        <tbody md-body>

                            <tr class="select_row" md-row ng-repeat="permission in roles_export_modal">
                                <td md-cell>
                                    <md-checkbox ng-checked="permission.permitido == '1'" md-no-ink aria-label="edit" ng-model="permissions_export.permission[permission.id]" class="md-primary"></md-checkbox>
                                </td>
                                <td md-cell>
                                    <span ng-bind="permission.permission_key"></span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarRole(roleModal.role_id)">Salvar</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalPrioridade">
        <div class="modal-dialog mdAngular" style="margin: 0 auto;">
            <div class="modal-content">
                <div class="modal-header" style="border-bottom: 1px solid #d8d8d8;">
                    <button type="button" class="close" ng-click="dimiss()">
                        <span aria-hidden="true">×</span> <span class="sr-only"><?php echo lang2('close') ?></span>
                    </button>
                    <b>Alterar prioridade</b>
                </div>
                <div id="modalBody" class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <md-input-container class="md-block ">
                                <label>Funil</label>
                                <md-select multiple placeholder="Funil" ng-model="prioridade_modal.funil" ng-change="selecionaFunil()" style="min-width: 200px;">
                                    <md-option ng-value="-1">Todos</md-option>
                                    <md-option ng-value="list.id_list" ng-repeat="list in leadslist">{{list.nm_list}}</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-6">
                            <md-input-container class="md-block">
                                <label><?php echo lang2('source'); ?></label>
                                <md-select placeholder="<?php echo lang2('source'); ?>" ng-model="prioridade_modal.source_id" style="min-width: 200px;">
                                    <md-option ng-value="-1">Todos</md-option>
                                    <md-option ng-value="source.id" ng-repeat="source in leadssources">{{source.name}}</md-option>
                                </md-select>
                            </md-input-container>
                        </div>

                        <div class="col-md-6">
                            <md-input-container class="md-block">
                                <label>Regras de Pré-seleção</label>
                                <md-select placeholder="Regras de Pré-seleção" ng-model="prioridade_modal.regras" style="min-width: 200px;">
                                    <md-option ng-value="1">Sempre dos vencidos, depois vence hoje</md-option>
                                    <md-option ng-value="2">Sempre da temperatura mais alta para a mais baixa</md-option>
                                    <md-option ng-value="3">Sempre da qualificação mais alta para a mais baixa</md-option>
                                </md-select>
                            </md-input-container>
                        </div>
                    </div>

                    <div class="row" style="margin: 0;">
                        <h3>Prioridades
                            <md-button ng-click="addFase()" class="md-icon-button md-primary add-title" aria-label="New">
                                <md-tooltip md-direction="top">Adcionar nova fase</md-tooltip>
                                <md-icon><i class="ion-android-add-circle text-success"></i></md-icon>
                            </md-button>
                        </h3>
                        <div class="row" ng-repeat="(key, fase_array) in fases_funil">
                            <div class="col-md-6">
                                <md-input-container class="md-block">
                                    <label>Prioridade {{key + 1}}</label>
                                    <md-select multiple ng-model="fase_array.id_status" style="min-width: 200px;">
                                        <md-option ng-value="-1">Demais leads</md-option>
                                        <md-option ng-value="fase.id" ng-repeat="fase in funil_fases">{{fase.name}}</md-option>
                                    </md-select>
                                </md-input-container>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" ng-click="dimiss()">Fechar</button>
                    <button type="button" class="btn btn-success" ng-click="salvarPrioridade()">Salvar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include_once(APPPATH . 'Views/inc/other_footer.php'); ?>


<script type="text/javascript">
    var Issuers = 0;
    var paymentMethodId = '';
    var installments = 0;
    var token = '';
    var transactionAmount = "0.00";
    var plan_tipo = 30;
    var id_plan = 0;
    var cardForm = null;
    var lang = {};
    var indexTab = 0;
    lang.doIt = '<?php echo lang2('doIt') ?>';
    lang.cancel = '<?php echo lang2('cancel') ?>';
    lang.attention = '<?php echo lang2('attention') ?>';
    lang.delete_role_meesage = "<?php echo lang2('delete_meesage') . '' . lang2('role') . '.' ?>";
    lang.invoice = "<?php echo lang2('invoice') ?>";
    lang.proposal = "<?php echo lang2('proposal') ?>";
    lang.customer = "<?php echo lang2('customer') ?>";
    lang.task = "<?php echo lang2('task') ?>";
    lang.project = "<?php echo lang2('project') ?>";
    lang.ticket = "<?php echo lang2('ticket') ?>";
    lang.expense = "<?php echo lang2('expense') ?>";
    lang.product = "<?php echo lang2('product') ?>";
    lang.lead = "<?php echo lang2('lead') ?>";
    lang.input = "<?php echo lang2('input') ?>";
    lang.datepicker = "<?php echo lang2('datepicker') ?>";
    lang.textarea = "<?php echo lang2('textarea') ?>";
    lang.select = "<?php echo lang2('select') ?>";

    <?php if (request()->getGet('p') && request()->getGet('p') == "minha_assinatura") {
    ?>
        indexTab = 9;
    <?php
    }
    ?>
</script>
<script type="text/javascript" src="<?php echo base_url('assets/js/settings.js?v=1.2.109') ?>"></script>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<script src="<?php echo base_url('assets/lib/tinymce/tinymce.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

<script>
    tinymce.init({
        selector: '.tinymce',
        theme: 'modern',
        //height: 200,
        content_style: "body p, h1, h2, h3, h4, h5, h6{margin: 2px 5px;}",
        editor_selector: "mceEditor",
        theme: 'modern',
        valid_elements: '*',
        valid_styles: '*',
        plugins: 'print preview searchreplace autoresize autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking importcss anchor code insertdatetime advlist lists textcolor wordcount imagetools  contextmenu colorpicker textpattern',
        //body_class: 'my_class',
        valid_children: "+body[style]",
        valid_elements: "@[id|class|title|style]," +
            "a[name|href|target|title|alt]," +
            "#p,-ol,,div,h1,h2,h3,h4,h5,h6,strong,-ul,-li,br,img[src|unselectable],-sub,-sup,-b,-i,-u," +
            "-span[data-mce-type],hr",

        valid_child_elements: "body[p,ol,ul,div,h1,h2,h3,h4,h5,h6,strong,b]" + ",p[a|span|b|i|u|sup|sub|img|hr|#text]" +
            ",span[a|b|i|u|sup|sub|img|#text]" + ",a[span|b|i|u|sup|sub|img|#text]" + ",b[span|a|i|u|sup|sub|img|#text]" +
            ",i[span|a|b|u|sup|sub|img|#text]" + ",sup[span|a|i|b|u|sub|img|#text]" + ",sub[span|a|i|b|u|sup|img|#text]" +
            ",li[span|a|b|i|u|sup|sub|img|ol|ul|#text]" + ",ol[li]" + ",ul[li]",

        content_css: [
            //fonts.googleapis.com/css?family=Lato:300,300i,400,400i’,
            //www.tinymce.com/css/codepen.min.css’
        ],
        toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat'
    });

    $(document).ready(function() {

        $('.select2Input').on('select2:selecting', function(e) {
            var id = $(this).attr('id');
            var val = $(this).val();
            var html = `<md-option value="${val}">${val}</md-option>`;
            $('#' + id).append(html);


            $('#' + id).find('[value="' + val + '"]').trigger('click');
        });


        $('#select_emails').select2({
            dropdownParent: $("#inpt_email"),
            tags: true
        });


        $('.select2Func').select2({
            ajax: {
                url: '<?= base_url('api/get_staff'); ?>',
                dataType: 'json'
            }
        });



        $('#cardNumber2').mask('0000 0000 0000 0000');
        $('#cardexpiration').mask('00/00');
        $('#cardcvc').mask('000');
        $('#cardcpf').mask('000.000.000-00');

        $('#cardNumber2').keyup(function(event) {
            if ($(this).val().length == 0) {
                $('#label-cardnumber').html('0000 0000 0000 0000');
            } else {
                $('#label-cardnumber').html($(this).val());
                $('#cardNumber').val($(this).val().toString().replace(/[^0-9]/g, ''));
            }
        });

        $('#cardcpf').keyup(function(event) {
            var cpf = $('#cardcpf').val().toString().replace(/[^0-9]/g, '');
            $('#cardcpf2').val(cpf);
        });



        $('#cardexpiration').keyup(function(event) {
            if ($(this).val().length == 0) {
                $('#label-cardexpiration').html('00 / 0000');
            } else {
                if ($(this).val().length > 2) {
                    $('#cardExpirationMonth').val($(this).val().substring(0, 2));
                }
                if ($(this).val().length >= 5) {
                    $('#cardExpirationYear').val($(this).val().substring(3, 5));
                }
                $('#label-cardexpiration').html($(this).val());
            }
        });

        $('#cardcvc').keyup(function(event) {
            if ($(this).val().length == 0) {
                $('#label-cvv').html('000');
            } else {
                $('#label-cvv').html($(this).val());
            }
        });


        $('.button-cta').on('click', function() {
            var proceed = true;
            $(".field input").each(function() {
                $(this).parent().find('path').each(function() {
                    $(this).attr('fill', '#dddfe6');
                });

                if (!$.trim($(this).val())) {
                    $(this).parent().find('path').each(function() {
                        $(this).attr('fill', '#f1404b');
                        proceed = false;
                    });

                    if (!proceed) {
                        $(this).parent().find('svg').animate({
                            opacity: '0.1'
                        }, "slow");
                        $(this).parent().find('svg').animate({
                            opacity: '1'
                        }, "slow");
                        $(this).parent().find('svg').animate({
                            opacity: '0.1'
                        }, "slow");
                        $(this).parent().find('svg').animate({
                            opacity: '1'
                        }, "slow");
                    }
                }
            });

            if (proceed) {
                $('.field').find('path').each(function() {
                    $(this).attr('fill', '#3ac569');
                });
                $('#form-checkout').submit()
            }
        });


    });
</script>