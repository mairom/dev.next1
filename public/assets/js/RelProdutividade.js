function RelProdutividade($scope, $http) {
    "use strict";
    var data = new Date();
    $scope.filtros = [];
    $scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
    $scope.filtros.dt_ate = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');
    $scope.loader = true;

    $http.get(BASE_URL + 'api/staff').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    var config = {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;' }
    };

    $scope.getResultados = function () {
        $scope.report = [];
        $scope.report.horasTrabalhadas = '00:00:00';
        $scope.report.HorasOciosas = '00:00:00';
        $scope.report.totalAcoes = '0';
        $scope.report.LeadsAcionados = '0';
        $scope.report.ReunioesRealizadas = '0';
        $scope.report.totalAcoes = '0';
        $scope.leadsGerados = [];
        $scope.ClientesSemLead = [];

        var dataObj = $.param({
            dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
            dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
            funcionario: $scope.filtros.funcionario
        })

       

        $http.post(BASE_URL + 'RelProdutividade/getReports_all', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.report = data.data;
            }

            $http.post(BASE_URL + 'RelProdutividade/getReports_TempoLogadoPorFuncionario', dataObj, config).then(function (data) {
                if (data.data != null) {
                    $scope.report.horasTrabalhadas = data.data.horasTrabalhadas;
                    $scope.report.HorasOciosas = data.data.HorasOciosas;
                    $scope.report.totalAcoes = data.data.totalAcoes;
                }
            });

        });



        $http.post(BASE_URL + 'RelProdutividade/getLeadsGerados', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.leadsGerados = data.data;
            }
        });

        $http.post(BASE_URL + 'RelProdutividade/getClientesSemLead', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.ClientesSemLead = data.data;
            }
        });
    }

    if (super_admin == 1) {
        $http.post(BASE_URL + 'RelProdutividade/leadsPorEmpresas', {}, config).then(function (data) {
            if (data.data != null) {
                $scope.empresas = data.data;
            }
        });
    }

}

CiuisCRM.controller('RelProdutividade', RelProdutividade);
