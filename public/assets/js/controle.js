function Controle_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
    "use strict";
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

    var data = new Date();
    for (var i = 0; i <= 5; i++) {
        $scope.anosList.push(data.getFullYear() - i);
    }
    $scope.filtros.ano = data.getFullYear();
    $scope.filtros.mes = new Date().getMonth() + 1;

    $http.get(BASE_URL + 'companies/get_companies').then(function (Data) {
        $scope.companies = Data.data;
    });

    $scope.getResultados = function () {
        var dataObj = $.param({
            ano: $scope.filtros.ano,
            mes: $scope.filtros.mes,
            id_company: $scope.filtros.id_company,
        });

        $http.post(BASE_URL + 'controle/atualizaConta', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.faturas = data.data.faturas;

                $scope.totalGasto = 0;
                $.each($scope.faturas, (index, fatura) => {
                    $scope.totalGasto += parseFloat(fatura.valor);
                });

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
}
CiuisCRM.controller('Controle_Controller', Controle_Controller);