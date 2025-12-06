function DashRh_Controller($scope, $http) {
    "use strict";
    var data = new Date();
    $scope.filtros = [];
    $scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
    $scope.filtros.dt_ate = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');
    $scope.loader = true;

    $scope.TmpDiarioPorMes = null;
    $scope.LeadsPorFuncionarios = null;
    $scope.ClientesPorFuncionario = null;
    $scope.mediaTempoLogadoPorFuncionario = null;
    $scope.AtvDiariaFunc = null;
    $scope.MediaAtvSistem = null;
    $scope.top10Atvs = null;

    $http.get(BASE_URL + 'api/staff').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $scope.getResultados = function () {
        $scope.report = [];
        $scope.report.mediaDiaria = '';
        $scope.report.totalDeAtividade = '';
        $scope.report.totalLeads = '';
        $scope.report.totalClientes = '';

        var dataObj = $.param({
            dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
            dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
            funcionario: $scope.filtros.funcionario
        })

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        var MainChartOptions = {
            responsive: true,
            maintainAspectRatio: false
        };

        $http.post(BASE_URL + 'dash_rh/getReports_all', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.report = data.data;
            }
        });
        /*
                $http.post(BASE_URL + 'dash_rh/getReports_TmpDiarioPorMes', dataObj, config).then(function (data) {
                    if ($scope.TmpDiarioPorMes == null) {
                        $scope.TmpDiarioPorMes = new Chart($('#TmpDiarioPorMes'), {
                            type: 'line',
                            data: data.data,
                            options: MainChartOptions
                        });
                    } else {
                        $scope.TmpDiarioPorMes.data = data.data;
                        $scope.TmpDiarioPorMes.update();
                    }
                });
                */

        $http.post(BASE_URL + 'dash_rh/getReports_LeadsPorFuncionarios', dataObj, config).then(function (data) {
            if ($scope.LeadsPorFuncionarios == null) {
                $scope.LeadsPorFuncionarios = new Chart($('#LeadsPorFuncionarios'), {
                    type: 'bar',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                $scope.LeadsPorFuncionarios.data = data.data;
                $scope.LeadsPorFuncionarios.update();
            }
        });

        $http.post(BASE_URL + 'dash_rh/getReports_ClientesPorFuncionario', dataObj, config).then(function (data) {
            if ($scope.ClientesPorFuncionario == null) {
                $scope.ClientesPorFuncionario = new Chart($('#ClientesPorFuncionario'), {
                    type: 'bar',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                $scope.ClientesPorFuncionario.data = data.data;
                $scope.ClientesPorFuncionario.update();
            }
        });

        $http.post(BASE_URL + 'dash_rh/getReports_AtvDiariaFunc', dataObj, config).then(function (data) {
            if ($scope.AtvDiariaFunc == null) {
                $scope.AtvDiariaFunc = new Chart($('#AtvDiariaFunc'), {
                    type: 'line',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                $scope.AtvDiariaFunc.data = data.data;
                $scope.AtvDiariaFunc.update();
            }
        });

        $http.post(BASE_URL + 'dash_rh/getReports_mediaTempoLogadoPorFuncionario', dataObj, config).then(function (data) {
          
       
            // Criação do gráfico
          
            var configs = {
                type: 'bar',
                data: data.data,
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                  let label = context.raw;
                                    return label.substr(0, 2) + ':' + label.substr(2, 2) + ':' + label.substr(4, 2);
                                },
                                title: function(context) {
                                    let title = context[0].label;
                                    return title;
                                }
                            },
                        }
                    }
                }
            };

           

            if ($scope.mediaTempoLogadoPorFuncionario == null) {
                $scope.mediaTempoLogadoPorFuncionario = new Chart($('#mediaTempoLogadoPorFuncionario'), configs);
            } else {
                $scope.mediaTempoLogadoPorFuncionario.data = data.data;
                $scope.mediaTempoLogadoPorFuncionario.update();
            }
        });




        /*
        $http.post(BASE_URL + 'dash_rh/getReports_MediaAtvSistem', dataObj, config).then(function (data) {
            if ($scope.MediaAtvSistem == null) {
                $scope.MediaAtvSistem = new Chart($('#MediaAtvSistem'), {
                    type: 'bar',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                $scope.MediaAtvSistem.data = data.data;
                $scope.MediaAtvSistem.update();
            }
        
        });
        

        $http.post(BASE_URL + 'dash_rh/getReports_top10Atvs', dataObj, config).then(function (data) {
            if ($scope.top10Atvs == null) {
                $scope.top10Atvs = new Chart($('#top10Atvs'), {
                    type: 'bar',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                $scope.top10Atvs.data = data.data;
                $scope.top10Atvs.update();
            }
        });
*/


    }

    $scope.getResultados();


    var gerandoPdf = false;
    $scope.baixarPdf = function () {
        if (gerandoPdf) {
            return;
        }
        gerandoPdf = true;
        $('.exibeData').show();

        var node = document.getElementById("contentMain");
        domtoimage.toPng(node).then(function (dataUrl) {
            gerandoPdf = false;
            var printWindow = window.open('', '', 'height=400,width=800');
            printWindow.document.write('<html><head><title>Dashboard clientes ' + new Date().toISOString() + '</title>');
            printWindow.document.write('</head><body >');
            printWindow.document.write("<img style = 'max-width: 750px;max-height: 1030px;' onload = 'window.print()' src = '" + dataUrl + "'>");
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            $('.exibeData').hide();
        }).catch(function (error) {
            gerandoPdf = false;
            console.error('oops, something went wrong!', error);
        });
    }

    var idGraphModal = '';
    $scope.exibeBalao = function (id) {
        $('#modalBalao').modal('show');
        $('#textoBalao').html('');
        $('#descricaoGraph').val('');
        $('#tituloGraphBalao').html('Oque estou vendo?');
        $('#tituloGraph').val('');
        $('#exibirGraph').val('1');

        idGraphModal = id;
        $http.get(BASE_URL + 'api/getExplicacaoBalao/' + id).then(function (data) {
            if (data.data != null) {
                $('#textoBalao').html(data.data.explicacao);
                $('#descricaoGraph').val(data.data.explicacao);
                $('#exibirGraph').val(data.data.exibir);
                if (data.data.titulo != null && data.data.titulo.length > 0) {
                    $('#tituloGraphBalao').html(data.data.titulo);
                    $('#tituloGraph').val(data.data.titulo);
                }
            }
        });
    }

    $scope.salvarBalao = function () {
        var dataObj = $.param({
            idGraphModal,
            explicacao: $('#descricaoGraph').val(),
            titulo: $('#tituloGraph').val(),
            exibir: $('#exibirGraph').val(),
        });

        $http.post(BASE_URL + 'api/setExplicacaoBalao', dataObj, config).then(function (data) {
            $('#modalBalao').modal('hide');
        });
    }

    $http.get(BASE_URL + 'api/getAllExplicacaoBalao').then(function (data) {
        $scope.baloesGraphs = data.data;
    });

}

CiuisCRM.controller('DashRh_Controller', DashRh_Controller);
