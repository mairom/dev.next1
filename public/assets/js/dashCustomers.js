function DashCustomers_Controller($scope, $http) {
    "use strict";
    $scope.filtros = [];

    var graphFaturamentoTop10 = null;
    var graphNovosClientes = null;
    var graphClientesAtivos = null;
    var graphEstado = null;
    var graphRamoDeAtividade = null;
    var QuantidadeDeReunioes = null;

    $scope.anosList = [];
    $scope.filtros.ano = CONST_dt_ano != null ? CONST_dt_ano : new Date().getFullYear();

    var data = new Date();
    for (var i = 0; i <= 5; i++) {
        $scope.anosList.push(data.getFullYear() - i);
    }

    $http.get(BASE_URL + 'products/get_products').then(function (data) {
        $scope.produtosList = data.data;
    });
    $http.get(BASE_URL + 'api/staff').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $scope.getResultados = function () {
        $scope.report = [];
        $scope.report.totalClientesAtivos = '0';
        $scope.report.totalLTV = '0';
        $scope.report.totalLTMes = '0';
        $scope.report.totalLTDias = '0';
        $scope.report.totalChurn = '0%';

        var dataObj = $.param({
            ano: $scope.filtros.ano,
            produto: $scope.filtros.produto,
            vendedor: $scope.filtros.vendedor,
            id_company: CONST_idCompany != null ? CONST_idCompany : ''
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

        $http.post(BASE_URL + 'customersDash/getReports_totaisCustomers', dataObj, config).then(function (data) {
            if (data.data != null) {
                var inativos = data.data.totalClientesInativos;
                var totalClientes = data.data.totalClientes;
               // var total = data.data.totalClientes;

                $scope.report.totalClientesAtivos = data.data.totalClientesAtivos;
                $scope.report.totalLTV = data.data.totalLTV;
                $scope.report.totalLTMes = data.data.totalLTMes;
                $scope.report.totalLTDias = data.data.totalLTDias;
                $scope.report.totalChurn = parseFloat((inativos * 100) / (inativos + (totalClientes - inativos))).toFixed(2) + '%';
            }
        });

        $http.post(BASE_URL + 'customersDash/getReports_estado', dataObj, config).then(function (data) {
            if (graphEstado == null) {
                graphEstado = new Chart($('#graphEstado'), {
                    type: 'bar',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                graphEstado.data = data.data;
                graphEstado.update();
            }
        });

        $http.post(BASE_URL + 'customersDash/getReports_ramoDeAtividade', dataObj, config).then(function (data) {
            if (graphRamoDeAtividade == null) {
                graphRamoDeAtividade = new Chart($('#graphRamoDeAtividade'), {
                    type: 'bar',
                    data: data.data,
                    options: MainChartOptions
                });
            } else {
                graphRamoDeAtividade.data = data.data;
                graphRamoDeAtividade.update();
            }

        });

        $http.post(BASE_URL + 'customersDash/getReports_graphFaturamentoTop10', dataObj, config).then(function (data) {
            graph01('#graphFaturamentoTop10', data.data, null, "#3f51b5");
            return;
            if (data.data != null) {
                if (graphFaturamentoTop10 == null) {
                    graphFaturamentoTop10 = new Chart($('#graphFaturamentoTop10'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    graphFaturamentoTop10.data = data.data;
                    graphFaturamentoTop10.update();
                }
            }
        });

        $http.post(BASE_URL + 'customersDash/getReports_graphNovosClientes', dataObj, config).then(function (data) {
            graphNovosClientes = graph01('#graphNovosClientes', data.data, null, "#3f51b5");
            return;

            if (data.data != null) {
                if (graphNovosClientes == null) {
                    graphNovosClientes = new Chart($('#graphNovosClientes'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    graphNovosClientes.data = data.data;
                    graphNovosClientes.update();
                }
            }
        });

        $http.post(BASE_URL + 'customersDash/getReports_graphClientesAtivos', dataObj, config).then(function (data) {
            graph05('#graphClientesAtivos', data.data);
            return;
            if (data.data != null) {
                if (graphClientesAtivos == null) {
                    graphClientesAtivos = new Chart($('#graphClientesAtivos'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    graphClientesAtivos.data = data.data;
                    graphClientesAtivos.update();
                }
            }
        });

        $http.post(BASE_URL + 'customersDash/getQuantidadeDeReunioes', dataObj, config).then(function (data2) {
			if (data2.data != null) {
				if (QuantidadeDeReunioes == null) {
					QuantidadeDeReunioes = new Chart($('#QuantidadeDeReunioes'), {
						type: 'bar',
						data: data2.data,
						options: MainChartOptions
					});
				} else {
					QuantidadeDeReunioes.data = data2.data;
					QuantidadeDeReunioes.update();
				}
			}

		});
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
            // printWindow.document.write(`<style type="text/css" media="print">@page { size: landscape; }</style>`);
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

CiuisCRM.controller('DashCustomers_Controller', DashCustomers_Controller);
