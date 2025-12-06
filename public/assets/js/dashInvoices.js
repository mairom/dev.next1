function DashInvoices_Controller($scope, $http) {
    "use strict";
    var graphFaturamento = null;
    $scope.filtros = [];
    $scope.anosList = [];
    $scope.mesList = {
        0: { name: 'Janeiro', numero: 1 },
        1: { name: 'Fevereiro', numero: 2 },
        2: { name: 'Março', numero: 3 },
        3: { name: 'Abril', numero: 4 },
        4: { name: 'Maio', numero: 5 },
        5: { name: 'Junho', numero: 6 },
        6: { name: 'Julho', numero: 7 },
        7: { name: 'Agosto', numero: 8 },
        8: { name: 'Setembro', numero: 9 },
        9: { name: 'Outubro', numero: 10 },
        10: { name: 'Novembro', numero: 11 },
        11: { name: 'Dezembro', numero: 12 },
    };
    var MainChartOptions = {
        responsive: true,
        maintainAspectRatio: false
    };

    var data = new Date();
    for (var i = 0; i <= 5; i++) {
        $scope.anosList.push(data.getFullYear() - i);
    }
    $scope.filtros.ano = CONST_dt_ano != null ? CONST_dt_ano : $scope.anosList[0];
    $scope.filtros.mes = CONST_dt_mes != null ? CONST_dt_mes : null;

    $http.get(BASE_URL + 'products/get_products').then(function (data) {
        $scope.produtosList = data.data;
    });
    $http.get(BASE_URL + 'api/staff').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $scope.formatReal = function (int) {
        return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
    }

    $scope.getResultados = function () {
        $scope.report = [];
        $scope.report.fatBruto = '0';
        $scope.report.totalDepsECompras = '0';
        $scope.report.RestLiquid = '0';
        $scope.report.MargemRestLiq = '0';

        var dataObj = $.param({
            ano: $scope.filtros.ano,
            produto: $scope.filtros.produto,
            vendedor: $scope.filtros.vendedor,
            situacao: $scope.filtros.situacao,
            mes: $scope.filtros.mes,
            id_company: CONST_idCompany != null ? CONST_idCompany : ''
        })

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post(BASE_URL + 'DashInvoices/getReports_graphAnaliseDeDespesas', dataObj, config).then(function (data) {
            $scope.AnaliseDeDespesas = graph06('#AnaliseDeDespesas', data.data);
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_faturamentoTotais', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.report.fatBruto = data.data.fatBruto;
                $scope.report.totalDepsECompras = data.data.totalDepsECompras;

                $scope.report.RestLiquid = parseFloat(data.data.fatBruto) - parseFloat(data.data.totalDepsECompras);
                var MargemRestLiq = ((parseFloat($scope.report.RestLiquid) * 100) / parseFloat(data.data.fatBruto));
                $scope.report.MargemRestLiq = (!isNaN(MargemRestLiq) && MargemRestLiq > 0 ? MargemRestLiq.toFixed(2) : '0') + '%';
            }
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamento', dataObj, config).then(function (data) {
            //$scope.graphFaturamento = graph02(data.data);
            if (data.data != null) {
				if (graphFaturamento == null) {
					graphFaturamento = new Chart($('#graphFaturamento'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphFaturamento.data = data.data;
					graphFaturamento.update();
				}
			}
        });

        
        $http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamentoehDespesa', dataObj, config).then(function (data) {

            graph04('#FaturamentoehDespesa', data.data, ['#1a33b9', '#bf2a0f'], 'currency')

            var graphAnaliseDeDespesas = [];
            graphAnaliseDeDespesas.datasets = [];
            graphAnaliseDeDespesas.datasets[0] = [];
            graphAnaliseDeDespesas.datasets[0].data = [];
            graphAnaliseDeDespesas.labels = [];

            data.data.datasets[0].data.map((row, i) => {
                graphAnaliseDeDespesas.datasets[0].data.push(row - data.data.datasets[1].data[i]);
                graphAnaliseDeDespesas.labels.push(data.data.labels[i]);
            });

            $scope.graphLucroLiquido = graph05('#graphLucroLiquido', graphAnaliseDeDespesas, true);

        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamentoOrigem', dataObj, config).then(function (data) {
            graph04('#faturamentoPorOrigem', data.data, ['#1a33b9', '#bf2a0f'], 'currency')
        });
        

        $http.post(BASE_URL + 'DashInvoices/getReports_graphDespesasECompras', dataObj, config).then(function (data) {
            graph04('#despesasECompras', data.data, ['#f00', '#f17d1a'], 'currency')
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphreceitasEDespesas', dataObj, config).then(function (data) {
            graph04('#receitasXDespesas', data.data, ['#1a33b9', '#bf2a0f'], 'currency')
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
            printWindow.document.write('<html><head><title>Dashboard financeiro ' + new Date().toISOString() + '</title>');
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

}

CiuisCRM.controller('DashInvoices_Controller', DashInvoices_Controller);
