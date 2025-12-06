<?php if (check_privilege('settings', 'edit')) { ?>
    <md-content class="md-padding bg-white">
        <md-button ng-click="addPlanos()" class="md-raised md-primary pull-right successButton">
            <span><?= lang2('create') . ' plano' ?></span>
        </md-button>
    </md-content>
<?php } ?>

<md-content class="md-padding bg-white">
    <md-table-container>
        <table md-table md-progress="promise">
            <thead md-head>
                <tr md-row>
                    <th md-column><?= lang2('name'); ?></th>
                    <th md-column><?= lang2('price'); ?></th>
                    <th md-column width = "150" style = "text-align: center;"><?php echo lang2('action'); ?></th>
                </tr>
            </thead>
            <tbody md-body>
                <tr class="select_row" md-row ng-repeat="plano in planos">
                    <td md-cell>
                        <span ng-bind="plano.nm_plan"></span>
                    </td>
                    <td md-cell>
                        <span ng-bind-html="plano.valor | currencyFormat:cur_code:null:true:cur_lct"></span>
                    </td>

                    <td md-cell width = "150">
                        <md-button style="width: 30px;height: 30px;" ng-click="Permissions(plano.id_plan)" class="md-icon-button md-primary" aria-label="Actions" ng-cloak>
                            <md-icon style="font-size: 21px;"><i class="fas fa-tasks"></i></md-icon>
                        </md-button>

                        <span ng-click="addPlanos(plano)">
                            <md-icon ng-hide="editLoader == true" md-menu-align-target class="md-raised md-primary mdi mdi-edit" style="margin: auto 3px auto 0;">
                            </md-icon>
                        </span>

                        <span ng-click="dell_planos(plano.id_plan)">
                            <md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">
                            </md-icon>
                        </span>

                    </td>
                </tr>
            </tbody>
        </table>
    </md-table-container>

</md-content>

<script>
    var base_url = '<?= base_url() ?>';
</script>