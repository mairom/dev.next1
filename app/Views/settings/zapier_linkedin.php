<md-content class="md-padding bg-white">
    <div class="card shadow-sm">

        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 40%;">Funil</th>
                        <th style="width: 40%;">Etapa padrão para novos leads</th>
                        <th style="width: 20%;" class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="funil in leadslist">
                        <td>
                            <strong>{{funil.nm_list}}</strong>
                        </td>
                        <td>
                            <span ng-if="settings_funis.funis[funil.id_list]">
                                {{ settings_funis.funis[funil.id_list].name }}
                            </span>
                            <span class="text-muted" ng-if="!settings_funis.funis[funil.id_list]">
                                Não definido
                            </span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-primary" ng-click="openStatusModal(funil)">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button ng-if="settings_funis.funis[funil.id_list]" class="btn btn-sm btn-success" ng-click="copyLink(funil)">
                                <i class="fas fa-link"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</md-content>