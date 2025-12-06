<style>
    md-dialog {
        padding: 0 10px;
    }

    .add-title {
        margin: 0 !important;
        margin-top: -6px !important;
        padding: 0 !important;
        width: 26px !important;
    }
</style>
<md-content class="md-padding bg-white">
    <div class="col-12 col-md-6">
        <h3>Configurações de prioridades
            <md-button ng-click="addPrioridade()" class="md-icon-button md-primary add-title" aria-label="New">
                <md-tooltip md-direction="top">Adcionar prioridade</md-tooltip>
                <md-icon><i class="ion-android-add-circle text-success "></i></md-icon>
            </md-button>
        </h3>

        <table md-table md-progress="promise">
            <thead md-head>
                <tr md-row>
                    <th md-column width="10%">#</th>
                    <th md-column>Funil</th>
                    <th md-column width = "150px">Ação</th>
                </tr>
            </thead>
            <tbody md-body>
                <tr class="select_row" md-row ng-repeat="(key, prioridade) in prioridades">
                    <td md-cell>
                        {{key + 1}}
                    </td>

                    <td md-cell>
                        {{prioridade.funil != '-1' ? prioridade.nm_list : 'Todos'}}
                    </td>

                    <td md-cell>
                        <md-button style = "min-width: auto;" ng-click="addPrioridade(prioridade)">
                            <md-icon md-menu-align-target class="md-raised md-primary mdi mdi-edit" style="margin: auto 3px auto 0;">
                            </md-icon>
                        </md-button>

                        <md-button style = "min-width: auto;" ng-click="removePrioridade(key)">
                            <md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">
                            </md-icon>
                        </md-button>
                    </td>
                </tr>
            </tbody>
        </table>




    </div>

    <div class="col-12 col-md-6">
        <h3>Motivos de pausa

            <md-button ng-click="addMotivo()" class="md-icon-button md-primary add-title" aria-label="New">
                <md-tooltip md-direction="top">Adcionar</md-tooltip>
                <md-icon><i class="ion-android-add-circle text-success "></i></md-icon>
            </md-button>

        </h3>

        <table md-table md-progress="promise">
            <thead md-head>
                <tr md-row>
                    <th md-column width="10%">#</th>
                    <th md-column>Motivo</th>
                    <th md-column>Ação</th>
                </tr>
            </thead>
            <tbody md-body>
                <tr class="select_row" md-row ng-repeat="(key, motivo) in motivos_pausa">
                    <td md-cell>
                        {{key + 1}}
                    </td>

                    <td md-cell>
                        {{motivo.name}}
                    </td>

                    <td md-cell>
                        <md-button ng-click="removeMotivo(key)">
                            <md-icon md-menu-align-target class="md-raised md-primary ion-trash-b" style="margin: auto 3px auto 0;">
                            </md-icon>
                        </md-button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


</md-content>