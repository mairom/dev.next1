<?php if (check_privilege('settings', 'edit')) { ?>
    <md-content class="md-padding bg-white">
        <md-button ng-click="addAtividade()" class="md-raised md-primary pull-right successButton">
            <span><?= lang2('create') . ' Atividade' ?></span>
        </md-button>
    </md-content>
<?php } ?>


<md-content class="md-padding bg-white">
    <md-tabs>
        <md-tab ng-click="tp_atividade = 'leads'" label="Leads">
            <md-table-container>
                <table md-table md-progress="promise">
                    <thead md-head>
                        <tr md-row>
                            <th md-column width="10%">Icon</th>
                            <th md-column><?= lang2('name'); ?></th>
                            <th md-column><?php echo lang2('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody md-body>
                        <tr class="select_row" md-row ng-repeat="atividade in atividades">
                            <td md-cell>
                                <img style="width: 5em;margin-top: 7px;" src="{{base_url + atividade.atv_ft}}" class="atv_img">
                            </td>
                            <td md-cell>
                                <span ng-bind="atividade.nm_atividade_select"></span>
                            </td>
                            <td md-cell>
                                <?php if (check_privilege('settings', 'edit')) { ?>
                                    <span ng-click="addAtividade(atividade)">
                                        <md-icon ng-hide="editLoader == true" md-menu-align-target class="md-raised md-primary mdi mdi-edit" style="margin: auto 3px auto 0;">
                                        </md-icon>
                                    </span>
                                <?php }
                                if (check_privilege('settings', 'delete')) { ?>
                                    <span ng-click="dell_atividades(atividade.id_atv)">
                                        <md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">
                                        </md-icon>
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>
        </md-tab>
        <md-tab ng-click="tp_atividade = 'clientes'" label="Clientes">

            <md-table-container>
                <table md-table md-progress="promise">
                    <thead md-head>
                        <tr md-row>
                            <th md-column width="10%">Icon</th>
                            <th md-column><?= lang2('name'); ?></th>
                            <th md-column><?php echo lang2('action'); ?></th>
                        </tr>
                    </thead>
                    <tbody md-body>
                        <tr class="select_row" md-row ng-repeat="atividade in atividadesCustomer">
                            <td md-cell>
                                <img style="width: 5em;margin-top: 7px;" src="{{base_url + atividade.atv_ft}}" class="atv_img">
                            </td>
                            <td md-cell>
                                <span ng-bind="atividade.nm_atividade_select"></span>
                            </td>
                            <td md-cell>
                                <?php if (check_privilege('settings', 'edit')) { ?>
                                    <span ng-click="addAtividade(atividade)">
                                        <md-icon ng-hide="editLoader == true" md-menu-align-target class="md-raised md-primary mdi mdi-edit" style="margin: auto 3px auto 0;">
                                        </md-icon>
                                    </span>
                                <?php }
                                if (check_privilege('settings', 'delete')) { ?>
                                    <span ng-click="dell_atividades(atividade.id_atv)">
                                        <md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">
                                        </md-icon>
                                    </span>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </md-table-container>

        </md-tab>
    </md-tabs>
</md-content>
<br />


<script>
    var base_url = '<?= base_url() ?>';
</script>