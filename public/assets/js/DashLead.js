function DashLead_Controller($scope, $http, $mdSidenav, $mdDialog, $mdConstant, $filter, $q) {
	"use strict";
	var data = new Date();
	var loadingResultado = false;
	$scope.filtros = [];
	$scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
	$scope.filtros.dt_ate = CONSTdt_ate != null ? CONSTdt_ate : new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');

	$scope.filtros.flt_funil = '-1';
	$scope.filtros.flt_funcionario = '-1';
	$scope.filtros.flt_origem = '-1';
	$scope.filtros.id_company = '-1';


	var graphNvOportMes = null;
	var graphNvOport = null;
	var graphNvOportPorClientes = null;
	var graphSetorDeAtividades = null;
	var graphQualificacoes = null;
	var graphLeadsPorFunil = null;

	var graphEstados = null;
	var graphOrigem = null;
	var atv_graph = null;
	var leadslist = [];
	var staff = [];
	var leadssources = [];

	$http.get(BASE_URL + 'leads/leadslist/1').then(function (data) {
		leadslist = data.data;
		$scope.leadslist = data.data;
		$('#main-content').show();
	});
	$http.get(BASE_URL + 'api/staff/1').then(function (Staff) {
		$scope.staff = Staff.data;
		staff = Staff.data;
	});
	if (is_admin == "1") {
		$http.get(BASE_URL + 'companies/get_companies').then(function (Data) {
			$scope.companies = Data.data;
		});
	}
	$http.get(BASE_URL + 'leads/leadsources/1').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;
		leadssources = LeadSources.data;
	});

	$scope.changeEmpresa = function () {
		var listTemp = [];
		$.each(leadslist, (index, a) => {
			if (a.id_company == $scope.filtros.id_company) {
				listTemp.push(a);
			}
		});
		$scope.leadslist = listTemp;

		var staffTemp = [];
		$.each(staff, (index, a) => {
			if (a.id_company == $scope.filtros.id_company) {
				staffTemp.push(a);
			}
		});
		$scope.staff = staffTemp;

		var leadssourcesTemp = [];
		$.each(leadssources, (index, a) => {
			if (a.id_company == $scope.filtros.id_company) {
				leadssourcesTemp.push(a);
			}
		});
		$scope.leadssources = leadssourcesTemp;


	

		$scope.getResultados();
	}

	$scope.getResultados = function () {
		if (loadingResultado) {
			return;
		}
		loadingResultado = true;

		$scope.report = [];
		$scope.report.totalLeadsPipeline = '';
		$scope.report.totalAtividades = '';
		$scope.report.totalLeadsAtivos = '';
		$scope.report.totalLeadsGerados = '';
		$scope.report.totalLeadsAtrasados = '';
		$scope.report.totalLeadsEmDia = '';

		$scope.report.reunioes_agendadas = '0';
		$scope.report.reunioes_realizadas = '0';
		$scope.report.engajamento = '0';
		$scope.report.pontualidade = '0 %';
		$scope.report.closeTime = '0';


		var dataObj = $.param({
			dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
			dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
			flt_funil: $scope.filtros.flt_funil,
			flt_funcionario: $scope.filtros.flt_funcionario,
			flt_origem: $scope.filtros.flt_origem,
			id_company: CONST_idCompany != null ? CONST_idCompany : $scope.filtros.id_company
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



		$http.post(BASE_URL + 'panel/getReports_nvOportunidadePorCliente', dataObj, config).then(function (data) {
			loadingResultado = false;
			if (data.data != null && $('#divgraphNvOportPorClientes').length > 0) {
				if (graphNvOportPorClientes == null) {
					graphNvOportPorClientes = new Chart($('#graphNvOportPorClientes'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNvOportPorClientes.data = data.data;
					graphNvOportPorClientes.update();
				}
			}
		});

		$http.post(BASE_URL + 'dashLead/getReports_qualificacoes', dataObj, config).then(function (data) {
			loadingResultado = false;
			if (data.data != null && $('#graphQualificacoes').length > 0) {
				if (graphQualificacoes == null) {
					graphQualificacoes = new Chart($('#graphQualificacoes'), {
						type: 'doughnut',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphQualificacoes.data = data.data;
					graphQualificacoes.update();
				}
			}
		});

		$http.post(BASE_URL + 'dashLead/getReports_leadsPorFunil', dataObj, config).then(function (data) {
			loadingResultado = false;
			if (data.data != null && $('#graphLeadsPorFunil').length > 0) {
				if (graphLeadsPorFunil == null) {
					graphLeadsPorFunil = new Chart($('#graphLeadsPorFunil'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphLeadsPorFunil.data = data.data;
					graphLeadsPorFunil.update();
				}
			}
		});

		

		$http.post(BASE_URL + 'panel/getReports_graphEstados', dataObj, config).then(function (data) {
			if (graphEstados == null) {
				graphEstados = new Chart($('#graphEstados'), {
					type: 'bar',
					data: data.data,
					options: MainChartOptions
				});
			} else {
				graphEstados.data = data.data;
				graphEstados.update();
			}
		});
		$http.post(BASE_URL + 'panel/getReports_graphSetorDeAtividades', dataObj, config).then(function (data) {
			if (graphSetorDeAtividades == null) {
				graphSetorDeAtividades = new Chart($('#graphSetorDeAtividades'), {
					type: 'bar',
					data: data.data,
					options: MainChartOptions
				});
			} else {
				graphSetorDeAtividades.data = data.data;
				graphSetorDeAtividades.update();
			}
		});

		$http.post(BASE_URL + 'panel/getReports_nvOportunidade', dataObj, config).then(function (data) {
			graphNvOport = graph01('#graphNvOport', data.data);
			return;
			if (data.data != null) {
				if (graphNvOport == null) {
					graphNvOport = new Chart($('#graphNvOport'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNvOport.data = data.data;
					graphNvOport.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_nvOportunidade_mes', dataObj, config).then(function (data) {
			if (data.data != null) {
				if (graphNvOportMes == null) {
					graphNvOportMes = new Chart($('#graphNvOportMes'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNvOportMes.data = data.data;
					graphNvOportMes.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_graphOrigem', dataObj, config).then(function (data) {
			if (data.data != null) {
				if (graphOrigem == null) {
					graphOrigem = new Chart($('#graphOrigem'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphOrigem.data = data.data;
					graphOrigem.update();
				}
			}
		});
/*
		$http.post(BASE_URL + 'panel/getReports_atv_graph', dataObj, config).then(function (data) {

			graph03('#atv_graph', data.data)
			return;
			if (data.data != null) {
				if (atv_graph == null) {
					atv_graph = new Chart($('#atv_graph').get(0).getContext('2d'), {
						type: 'horizontalBar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					atv_graph.data = data.data;
					atv_graph.update();
				}
			}
		});
		*/

		document.getElementById('totalLeadsAtivos').addEventListener('load', function () {
			this.style.display = 'block'
		});
		document.getElementById('totalLeadsGerados').addEventListener('load', function () {
			this.style.display = 'block'
		});
		document.getElementById('totalLeadsPipeline').addEventListener('load', function () {
			this.style.display = 'block'
		});
		document.getElementById('totalLeadsEmDia').addEventListener('load', function () {
			this.style.display = 'block'
		});
		/*
		document.getElementById('totalLeadsAtrasados').addEventListener('load', function () {
			this.style.display = 'block'
		});
		document.getElementById('totalAtividades').addEventListener('load', function () {
			this.style.display = 'block'
		});
		

		$http.post(BASE_URL + 'panel/getReports_totalAtividades', dataObj, config).then(function (data) {
			if (data.data != null) {
				$scope.report.totalAtividades = data.data;
			}
		});
		*/
		$http.post(BASE_URL + 'panel/getReports_totalLeadsAtivos', dataObj, config).then(function (data) {
			if (data.data != null) {
				$scope.report.totalLeadsAtivos = data.data;
			}
		});
		$http.post(BASE_URL + 'panel/getReports_totalLeadsGerados', dataObj, config).then(function (data) {
			if (data.data != null) {
				console.log(data.data)
				$scope.report.totalLeadsGerados = data.data;
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalLeadsAtrasados', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report.totalLeadsAtrasados = data.data;
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalLeadsEmDia', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report.totalLeadsEmDia = data.data;
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalLeadsPipeline', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report.totalLeadsPipeline = data.data;
			}
		});

		$http.post(BASE_URL + 'dashLead/getReports_closeTime', dataObj, config).then(function (data) {
			if (data.data != null) {
				$scope.report.closeTime = data.data.total;
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

CiuisCRM.controller('DashLead_Controller', DashLead_Controller);
