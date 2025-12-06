function DashGoals_Controller($scope, $http) {
    "use strict";
    $scope.filtros = [];
    $scope.anosList = [];
    $scope.mesList = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto',
        'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
    $scope.filtros.ano = CONST_dt_ano != null ? CONST_dt_ano : new Date().getFullYear();
    $scope.filtros.mes = new Date().getMonth() + 1;

    var data = new Date();
    for (var i = 0; i <= 5; i++) {
        $scope.anosList.push(data.getFullYear() - i);
    }

    $http.get(BASE_URL + 'goals/get_metas').then(function (data) {
        if (data.data.length > 0) {
            $scope.goals = data.data;
            $scope.filtros.id_goal = $scope.goals[0].id_goal;
            $scope.getResultados();
        }

    });
    $http.get(BASE_URL + 'goals/get_equipes').then(function (data) {
        $scope.equipes = data.data;
    });
    $http.get(BASE_URL + 'products/get_products').then(function (data) {
        $scope.products = data.data;
    });
    $http.get(BASE_URL + 'api/staff/1').then(function (Staff) {
        $scope.staff = Staff.data;
    });

    $scope.getResultados = function () {
        $scope.report = [];
        $scope.report.totalMeta = '0';
        $scope.report.totalAtingido = '0';
        $scope.report.porcetAtingido = '0';
        $scope.report.totalFalta = '0';
        $scope.report.porcentFalta = '0';



        var dataObj = $.param({
            ano: $scope.filtros.ano,
            mes: $scope.filtros.mes,
            id_goal: $scope.filtros.id_goal,
            id_equipe: $scope.filtros.id_equipe,
            funcionario: !isNaN(parseInt($('#filtro_func').val())) ? $('#filtro_func').val() : null,
            produto: $scope.filtros.produto,
            id_company: CONST_idCompany != null ? CONST_idCompany : ''
        })

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };

        $http.post(BASE_URL + 'dashGoals/getReports_metaVsResultado', dataObj, config).then(function (data) {
            graph04('#graphMetaVsResultado', data.data, ['#ababab', '#2196f3']);

            if (data.data != null && data.data.datasets[0] != null) {
                var dat = data.data.datasets[0].data.length;
                var totalMeta = data.data.datasets[0].data[dat - 1];
                var totalAtingido = data.data.datasets[1].data[dat - 1];

                var porcetAtingido = ((totalAtingido * 100) / totalMeta).toFixed(0);
                var totalFalta = (totalMeta - totalAtingido) > 0 ? (totalMeta - totalAtingido) : 0;
                var porcentFalta = (totalMeta - totalAtingido) > 0 ? (100 - ((totalAtingido * 100) / totalMeta)).toFixed(0) : '0';


                $scope.report.totalMeta = totalMeta;
                $scope.report.totalAtingido = totalAtingido;

                $scope.report.porcetAtingido = !isNaN(porcetAtingido) ? porcetAtingido : 0;
                $scope.report.totalFalta = !isNaN(totalFalta) ? totalFalta.toFixed(2) : 0;
                $scope.report.porcentFalta = !isNaN(porcentFalta) ? porcentFalta : 0;
            }
        });
        $http.post(BASE_URL + 'dashGoals/getReports_PorcetAlcancados', dataObj, config).then(function (data) {
            graph04('#graphPorcetAlcancados', data.data, ['#1a33b9', '#bf2a0f'], "#'%'")
        });

        $http.post(BASE_URL + 'dashGoals/getReports_graphControleBonifica', dataObj, config).then(function (data) {
            graph05('#graphControleBonifica', data.data, true);
        });

    }


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
            printWindow.document.write('<html><head><title>Dashboard Metas ' + new Date().toISOString() + '</title>');
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

CiuisCRM.controller('DashGoals_Controller', DashGoals_Controller);
