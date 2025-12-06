function Dash_reports($scope, $http) {
    "use strict";

    var data = new Date();
    $scope.filtros = [];
    $scope.lista = [];

    $scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
    $scope.filtros.dt_ate = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');
    $scope.filtros.closer = '-1';
    $scope.filtros.sdr = '-1';
    $scope.filtros.tipo = 'reunioes';
    $scope.filtros.tipo_reuniao = '-1';

    $http.get(BASE_URL + 'leads/leadslist/0').then(function (data) {
        $scope.leadslist = data.data;
    });

    $http.get(BASE_URL + 'api/staff/0').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $http.get(BASE_URL + 'leads/leadsources/0').then(function (LeadSources) {
        $scope.leadssources = LeadSources.data;
    });

    $scope.getResultados = function () {
        var dataObj = $.param({
            dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
            dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
            closer: $scope.filtros.closer,
            sdr: $scope.filtros.sdr,
            tipo: $scope.filtros.tipo,
            tipo_reuniao: $scope.filtros.tipo_reuniao,
        })

        $http.post(BASE_URL + 'reports/getLista', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.lista = data.data;
            }
        });
    };

    $scope.getResultados();
}

CiuisCRM.controller('Dash_reports', Dash_reports);
