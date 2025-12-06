<?php include_once(APPPATH . 'Views/inc/ciuis_data_table_header.php'); ?>

<?php $appconfig = get_appconfig(); ?>

<div class="ciuis-body-content" ng-controller="Company_Controller">
    <div class="main-content container-fluid col-xs-12 col-md-12 col-lg-9">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <a class="md-icon-button" href="<?= base_url('companies') ?>" style="margin-right: 1em;">
                    <i class="fas fa-arrow-left" style="color: #5c5c5c;font-size: 22px;"></i>
                </a>

                <md-button class="md-icon-button" aria-label="Settings" ng-disabled="true" style="background: #5c5c5c;">
                    <md-icon ng-if="company.tp_pessoa == 2"><i class="far fa-building" style="color: #ffffff;font-size: 22px;"></i></md-icon>
                    <md-icon ng-if="company.tp_pessoa == 1"><i class="fas fa-user-tie" style="color: #ffffff;font-size: 22px;"></i></md-icon>
                </md-button>

                <!-- <h2 ng-bind="company.lead_number" class="lead_name"></h2>&nbsp; -->

                <h2 flex md-truncate ng-if="company.tp_pessoa == 2" ng-bind="company.nm_company" class="lead_name"></h2>
                <h2 flex md-truncate ng-if="company.tp_pessoa == 1" ng-bind="company.name" class="lead_name"></h2>

            </div>
        </md-toolbar>

        <div ng-show="companiesLoader" layout-align="center center" class="text-center" id="circular_loader">
            <!-- <md-progress-circular md-mode="indeterminate" md-diameter="40"></md-progress-circular> -->
            <img src="<?= base_url('uploads/images/load.gif') ?>" class="loadGif">
            <p style="font-size: 15px;margin-bottom: 5%;">
                <span>
                    <?php echo lang2('please_wait') ?> <br>
                    <small><strong><?php echo lang2('loading') . ' empressa...' ?></strong></small>
                </span>
            </p>
        </div>

        <md-content ng-show="!companiesLoader" class="bg-white" ng-cloak>
            <md-tabs md-dynamic-height md-border-bottom>
                <md-tab label="Empresa">
                    <md-content class="bg-white infosLeads" flex="100" style="border-right:1px solid #e0e0e0;">
                        <md-list-item ng-if="company.tp_pessoa == 2">
                            <md-icon class="mdi mdi-local-store"></md-icon>
                            <strong flex md-truncate><?php echo lang2('company') ?></strong>
                            <p class="text-right" flex md-truncate ng-bind="company.nm_company"></p>
                        </md-list-item>

                        <md-list-item ng-if="company.tp_pessoa == 1">
                            <md-icon class="mdi mdi-local-store"></md-icon>
                            <strong flex md-truncate><?php echo lang2('name') ?></strong>
                            <p class="text-right" flex md-truncate ng-bind="company.name"></p>
                        </md-list-item>
                    </md-content>

                    <div layout="row" class="infosLeads">
                        <md-content class="bg-white" flex="50" style="border-right:1px solid #e0e0e0;">
                            <md-list flex class="md-p-0 sm-p-0 lg-p-0">

                                <md-divider></md-divider>

                                <div ng-if="company.tp_pessoa == 2">
                                    <md-list-item>
                                        <md-icon class="mdi"><i class="far fa-building"></i></md-icon>
                                        <strong flex md-truncate>Cnpj</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.cnpj"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>

                                <div ng-if="company.tp_pessoa == 1">
                                    <md-list-item>
                                        <md-icon class="mdi"><i class="fas fa-archive"></i></md-icon>
                                        <strong flex md-truncate>Cpf</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.cpf"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>


                                <md-list-item>
                                    <md-icon class="mdi mdi-local-phone"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('phone') ?></strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.phone"></p>
                                </md-list-item>

                                <md-divider></md-divider>

                                <md-list-item>
                                    <md-icon class="ion-android-mail"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('email') ?></strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.email"></p>
                                </md-list-item>


                                <md-divider></md-divider>

                                <md-list-item>
                                    <md-icon class="mdi mdi-pin-drop"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('address') ?></strong>
                                    <p class="text-right" flex ng-bind="company.address"></p>
                                </md-list-item>

                                <md-divider></md-divider>

                                <md-list-item>
                                    <md-icon class="mdi mdi-city"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('city') ?></strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.city"></p>
                                </md-list-item>

                                <md-divider></md-divider>

                                <md-list-item>
                                    <md-icon><i class="fas fa-flag-usa"></i></md-icon>
                                    <strong flex md-truncate>Estado</strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.state"></p>
                                </md-list-item>
                                <md-divider></md-divider>

                                <md-list-item>
                                    <md-icon class="ion-earth"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('country') ?></strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.country"></p>
                                </md-list-item>
                                <md-divider></md-divider>


                            </md-list>

                        </md-content>

                        <md-content class="bg-white" flex="50">

                            <md-list flex class="md-p-0 sm-p-0 lg-p-0">
                                <md-divider></md-divider>

                                <div ng-if="company.tp_pessoa == 2">
                                    <md-list-item>
                                        <md-icon class="mdi mdi-nature-people"></md-icon>
                                        <strong flex md-truncate>Website</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.web_site"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>

                                <div ng-if="company.tp_pessoa == 1">
                                    <md-list-item>
                                        <md-icon class="mdi"><i class="fas fa-calendar-week"></i></md-icon>
                                        <strong flex md-truncate>Data de nascimento </strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.dt_nascimento"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>


                                <div ng-if="company.tp_pessoa == 2">
                                    <md-list-item>
                                        <md-icon class="mdi mdi-markunread-mailbox"></md-icon>
                                        <strong flex md-truncate>Setor de atividade</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.setor_atividade"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>

                                <div ng-if="company.tp_pessoa == 2">
                                    <md-list-item>
                                        <md-icon class="mdi"> <i class="fas fa-search-dollar"></i></md-icon>
                                        <strong flex md-truncate>Porte</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.porte"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>


                                <md-list-item>
                                    <md-icon class="mdi mdi-book-image"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('source') ?></strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.source"></p>
                                </md-list-item>



                                <md-divider></md-divider>

                                <md-list-item>
                                    <md-icon class="ion-android-calendar"></md-icon>
                                    <strong flex md-truncate><?php echo lang2('date_contacted') ?></strong>
                                    <p class="text-right" flex md-truncate ng-bind="company.date_contacted"></p>
                                </md-list-item>

                                <md-divider></md-divider>

                                <div ng-if="company.tp_pessoa == 1">
                                    <md-list-item>
                                        <md-icon><i class="fab fa-instagram"></i></md-icon>
                                        <strong flex md-truncate>Instagram</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.instagram"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>


                                <div ng-if="company.tp_pessoa == 1">
                                    <md-list-item>
                                        <md-icon><i class="fab fa-facebook"></i></md-icon>
                                        <strong flex md-truncate>Facebook</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.facebook"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>

                                <div ng-if="company.tp_pessoa == 1">
                                    <md-list-item>
                                        <md-icon><i class="fab fa-linkedin-in"></i></md-icon>
                                        <strong flex md-truncate>Linkedin</strong>
                                        <p class="text-right" flex md-truncate ng-bind="company.linkedin"></p>
                                    </md-list-item>
                                    <md-divider></md-divider>
                                </div>



                            </md-list>
                        </md-content>
                    </div>


                    <md-divider></md-divider>
                    <md-content class="bg-white infosLeads" layout-padding>
                        <md-list-item>
                            <md-icon class="mdi mdi-sort-desc"></md-icon>
                            <p class="text-left" flex ng-bind="company.description"></p>
                        </md-list-item>
                    </md-content>


                    <div class="divContatos">

                        <div class="titlePessoaAdd">
                            <h4 style="margin: 0;"><i style="font-size: 25px;" class="far fa-user"></i> Usuários
                                <button type="button" class="btn" style="float: right;background: #d1b338;color: #fff;" ng-click="Create()">
                                    <span style="vertical-align: middle;">Adicionar</span>
                                    <md-icon><i style="color:white" class="ion-android-add-circle"></i></md-icon>
                                </button>
                            </h4>
                        </div>


                        <div class="contatosLead" ng-repeat="usuario in usuarios">
                            <div class="capaContato" style="background-image:url('<?= base_url('assets/img/wallpaperLinkedin.jpg') ?>')">
                                <i style="float: right;margin-right: 1em;margin-top: 1em;color: #fff;" class="fas fa-pen" ng-click="Create(usuario)"></i>
                            </div>
                            <div class="fotoContato" style="background-image:url('{{appurl + 'uploads/images/' + usuario.staffavatar}}')"></div>

                            <div class="dadosContato">
                                <h4 style="margin: 0;font-weight: 600;margin-top: 0.5em;">{{usuario.staffname}}</h4>
                                <p style="margin: 0;">{{usuario.phone }}</p>
                                <p style="margin-top: 1em;margin-bottom: 0;"><i style="color: #0d3d9f;" class="fas fa-envelope"></i> {{usuario.email}}</p>
                                <p style="margin-bottom: 0;"><i style="color: #0d3d9f;" class="fas fa-phone-alt"></i> {{usuario.phone}} </p>
                            </div>
                        </div>
                    </div>
                </md-tab>

                <!--
                <md-tab label="<?php echo lang2('emailsettings'); ?>">
                    <?php //include_once(APPPATH . 'Views/settings/email_settings.php'); 
                    ?>
                </md-tab>

                <md-tab label="RECEBER EMAIL">
                    <md-content class="md-padding bg-white">
                        <div class="col-md-6">
                            <md-input-container class="md-block" flex-gt-xs>
                                <label><?php echo lang2('email') . ' ' . lang2('type') ?></label>
                                <md-select required placeholder="<?php echo lang2('email') . ' ' . lang2('type') ?>" ng-model="settings_detail.imap_email_type" style="min-width: 200px;">
                                    <md-option value="1" ng-selected="true"><span><?php echo 'IMAP' ?></span></md-option>
                                </md-select><br>
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Usuário</label>
                                <input required ng-model="settings_detail.imapUsername">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Servidor</label>
                                <input required ng-model="settings_detail.imapHost">
                            </md-input-container>
                        </div>

                        <div class="col-md-6">
                            <md-input-container class="md-block password-input">
                                <label><?php echo lang2('password') ?></label>
                                <input type="text" required ng-model="settings_detail.imapPassoword">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Porta</label>
                                <input required ng-model="settings_detail.imapPort">
                            </md-input-container>

                            <md-input-container class="md-block">
                                <label>Remetente</label>
                                <input required ng-model="settings_detail.imap_sendermail">
                            </md-input-container>
                        </div>

                        <md-button style="background: #11b21b;color: #fff;" ng-click="SaveEmailImap()" class="md-raised md-success pull-right">Salvar</md-button>
                    </md-content>

                </md-tab>
            -->
            </md-tabs>

            <md-subheader ng-if="custom_fields.length > 0"><?php echo lang2('custom_fields'); ?></md-subheader>

            <md-list-item ng-if="custom_fields.length > 0" ng-repeat="field in custom_fields">

                <md-icon class="{{field.icon}} material-icons"></md-icon>

                <strong flex md-truncate>{{field.name}}</strong>

                <p ng-if="field.type === 'input'" class="text-right" flex md-truncate ng-bind="field.data"></p>

                <p ng-if="field.type === 'textarea'" class="text-right" flex md-truncate ng-bind="field.data"></p>

                <p ng-if="field.type === 'date'" class="text-right" flex md-truncate ng-bind="field.data | date:'dd, MMMM yyyy EEEE'"></p>

                <p ng-if="field.type === 'select'" class="text-right" flex md-truncate ng-bind="custom_fields[$index].selected_opt.name"></p>

                <md-divider ng-if="custom_fields"></md-divider>

            </md-list-item>

        </md-content>

    </div>

    <ciuis-sidebar>

    </ciuis-sidebar>


    <md-sidenav class="md-sidenav-right md-whiteframe-4dp" md-component-id="Create" ng-cloak style="width: 450px;">
        <md-toolbar class="toolbar-white">
            <div class="md-toolbar-tools">
                <md-button ng-click="close()" class="md-icon-button" aria-label="Close"> <i class="ion-android-arrow-forward"></i> </md-button>
                <h2 flex md-truncate><?php echo lang2('create') ?></h2>
                <!--  <md-switch ng-model="staff.active" aria-label="Type"><strong class="text-muted"><?php echo lang2('active') ?></strong></md-switch> -->
            </div>
        </md-toolbar>
        <md-content>
            <md-content layout-padding>
                <md-input-container class="md-block">
                    <label><?php echo lang2('name') ?></label>
                    <input required type="text" ng-model="staff.staffname" class="form-control" id="title">
                </md-input-container>

                

                <md-input-container class="md-block">
                    <label><?php echo lang2('email') ?></label>
                    <input required type="text" ng-model="staff.email" class="form-control" id="title" minlength="10" maxlength="100" ng-pattern="/^.+@.+\..+$/">
                </md-input-container>

                <md-input-container class="md-block password-input">
                    <label><?php echo lang2('password') ?></label>
                    <input type="text" ng-model="staff.password" rel="gp" data-size="9" id="nc">
                    <md-icon ng-click="getNewPass()" class="ion-refresh" style="display:inline-block;"></md-icon>
                </md-input-container>

                <md-input-container class="md-block">
                    <label><?php echo lang2('phone') ?></label>
                    <input type="text" ng-model="staff.phone" class="form-control" id="title">
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('staffdepartment'); ?></label>
                    <md-select required ng-model="staff.department_id" name="assigned" style="min-width: 200px;">
                        <md-option ng-value="department.id" ng-repeat="department in departments">{{department.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('language'); ?></label>
                    <md-select required ng-model="staff.language" name="assigned" style="min-width: 200px;">
                        <md-option ng-selected="language.foldername == 'portuguese_br'" ng-value="language.foldername" ng-repeat="language in languages">{{language.name}}</md-option>
                    </md-select>
                </md-input-container>

                <md-input-container class="md-block" flex-gt-xs>
                    <label><?php echo lang2('roles'); ?></label>
                    <md-select required ng-model="staff.role_id" name="assigned_role" style="min-width: 200px;">
                        <md-option ng-value="role.role_id" ng-repeat="role in roles">{{role.role_name}} <span class="badge">{{role.role_type}}</span></md-option>
                    </md-select>
                </md-input-container>

                <!--
                <md-input-container class="md-block">
                    <label><?php echo lang2('staff') . ' ' . lang2('timezone') ?></label>
                    <md-select ng-model="staff_timezone">
                        <md-optgroup ng-repeat="timezone in timezones" label="{{timezone.group}}">
                            <md-option ng-value="zone.value" ng-repeat="zone in timezone.zones">{{zone.value}}</md-option>
                        </md-optgroup>
                    </md-select>

                </md-input-container>
-->
                <md-input-container class="md-block">
                    <label><?php echo lang2('address') ?></label>
                    <textarea rows="2" ng-model="staff.address" class="form-control"></textarea>
                </md-input-container>

                <md-input-container class="md-block">
                    <label style="margin-bottom: 17px;">Foto</label>
                    <input type="file" ng-model="staff.fotoStaff" id="fotoStaff" required name="profile_photo" file-model="profile_photo" accept="image/*">
                </md-input-container>

            </md-content>

            <custom-fields-vertical></custom-fields-vertical>
            <md-content>
                <section layout="row" layout-sm="column" layout-align="center center" layout-wrap>
                    <md-button ng-click="AddStaff()" class="md-raised md-primary btn-report block-button" ng-disabled="saving == true">
                        <span ng-hide="saving == true" ng-show="staff.id == null"><?php echo lang2('add'); ?></span>
                        <span ng-hide="saving == true" ng-show="staff.id != null"><?php echo lang2('update'); ?></span>
                        <md-progress-circular class="white" ng-show="saving == true" md-mode="indeterminate" md-diameter="20"></md-progress-circular>
                    </md-button>
                    <br /><br /><br /><br />
                </section>

            </md-content>

        </md-content>

    </md-sidenav>


</div>

<script>
    var COMPANYID = "<?php echo $company['id_company']; ?>";
    var ACCOUNTID = <?= session()->usr_id ?>;
    var USERLOGADO = (<?= json_encode(session()) ?>).userdata;
    var BASE_URL = '<?= base_url(''); ?>';
    var lang = {};
    lang.doIt = "<?php echo lang2('doIt') ?>";
    lang.cancel = "<?php echo lang2('cancel') ?>";
    lang.attention = "<?php echo lang2('attention') ?>";
    lang.convert_title = "<?php echo lang2('convert') . ' empresa ' . lang2('to') . ' ' . lang2('customer') ?>";
    lang.convert_text = "<?php echo lang2('convertmsg') . ' empresa ' . lang2('to') . ' ' . lang2('customer') ?>";
    lang.convert = "<?php echo lang2('convert') ?>";
</script>

<?php include_once(APPPATH . 'Views/inc/footer.php'); ?>

<script src="<?php echo base_url('assets/js/companies.js?v=1.3'); ?>"></script>