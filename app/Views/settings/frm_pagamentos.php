<?php if (check_privilege('settings', 'edit')) { ?>
    <md-content class="md-padding bg-white">
        <md-button ng-click="addPagamento()" class="md-raised md-primary pull-right successButton">
            <span><?= lang2('create') . ' Forma' ?></span>
        </md-button>
    </md-content>
<?php } ?>

<md-content class="md-padding bg-white">
    <md-table-container>
        <table md-table md-progress="promise">
            <thead md-head>
                <tr md-row>
                    <th md-column width="10%">#</th>
                    <th md-column><?= lang2('name'); ?></th>
                    <th md-column><?php echo lang2('action'); ?></th>
                </tr>
            </thead>
            <tbody md-body>
                <tr class="select_row" md-row ng-repeat="pagamento in frm_pagamentos">
                    <td md-cell>
                        <span ng-bind="pagamento.id_forma"></span>
                    </td>
                    <td md-cell>
                        <span ng-bind="pagamento.nm_forma"></span>
                    </td>
                    <td md-cell>
                        <?php if (check_privilege('settings', 'edit')) { ?>
                            <span ng-click="addPagamento(pagamento)">
                                <md-icon ng-hide="editLoader == true" md-menu-align-target class="md-raised md-primary mdi mdi-edit" style="margin: auto 3px auto 0;">
                                </md-icon>
                            </span>
                        <?php }
                        if (check_privilege('settings', 'delete')) { ?>
                            <span ng-click="dellPagamentos(pagamento.id_forma)">
                                <md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">
                                </md-icon>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </md-table-container>
</md-content>
<br />


<script>
    var base_url = '<?= base_url() ?>';
</script>