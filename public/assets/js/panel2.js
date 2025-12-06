function Panel2_Controller($scope, $http, $mdSidenav, $mdDialog, $mdConstant, $filter, $q) {
	"use strict";
	var data = new Date();
	var loadMeta = false;
	var loadCliente = false;
	var loadResultados = false;
	var loadFinanceiro = false;
	$scope.filtros = [];
	$scope.overviewLoader = true;
	$scope.departments = null;
	$scope.languages = null;
	$scope.roles = null;
	$scope.timezones = null;
	$scope.new_staff = [];

	$scope.anosList = [];
	$scope.mesList = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto',
		'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

	var data = new Date();
	for (var i = 0; i <= 5; i++) {
		$scope.anosList.push(data.getFullYear() - i);
	}

	$scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
	$scope.filtros.dt_ate = CONSTdt_ate != null ? CONSTdt_ate : new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');

	$scope.filtros.flt_funil = '-1';
	$scope.filtros.flt_funcionario = '-1';
	$scope.filtros.flt_origem = '-1';
	$scope.filtros.id_company = '-1';

	$scope.filtros.ano = data.getFullYear();
	$scope.filtros.mes = (data.getMonth() + 1);

	$scope.customPanel = {};


	$http.get(BASE_URL + 'settings/get_roles').then(function (Roles) {
		$scope.roles = Roles.data;
		$scope.new_staff.assigned_role = Roles.data[0].role_id;
	});


	$scope.Create = function () {
		if ($scope.departments == null) {
			$http.get(BASE_URL + 'api/departments').then(function (Departments) {
				$scope.departments = Departments.data;
				$scope.new_staff.department_id = 1;
			});

			$http.get(BASE_URL + 'api/languages').then(function (Languages) {
				$scope.languages = Languages.data;
				$scope.new_staff.language = 'portuguese_pt';
			});

			$http.get(BASE_URL + 'api/timezones').then(function (Timezones) {
				$scope.timezones = Timezones.data;
			});


		}
		$mdSidenav('Create').toggle();
	}

	$scope.AddStaff = function () {
		$scope.saving = true;
		$scope.tempArr = [];
		angular.forEach($scope.custom_fields, function (value) {
			if (value.type === 'input') {
				$scope.field_data = value.data;
			}
			if (value.type === 'textarea') {
				$scope.field_data = value.data;
			}
			if (value.type === 'date') {
				$scope.field_data = moment(value.data).format("YYYY-MM-DD");
			}
			if (value.type === 'select') {
				$scope.field_data = JSON.stringify(value.selected_opt);
			}
			$scope.tempArr.push({
				id: value.id,
				name: value.name,
				type: value.type,
				order: value.order,
				data: $scope.field_data,
				relation: value.relation,
				permission: value.permission,
			});
		});
		var file = document.querySelector('#fotoStaff').files[0];
		getBase64(file).then((staffavatar) => {
			var dataObj = $.param({
				name: $scope.new_staff.name,
				email: $scope.new_staff.email,
				phone: $scope.new_staff.phone,
				department: $scope.new_staff.department_id,
				language: $scope.new_staff.language,
				address: $scope.new_staff.address,
				password: $scope.passwordNew,
				timezone: $scope.staff_timezone,
				custom_fields: $scope.tempArr,
				role: $scope.new_staff.assigned_role,
				id_company: $scope.new_staff.id_company,
				staffavatar: staffavatar,
			});
			var posturl = BASE_URL + 'staff/createStaff';
			$http.post(posturl, dataObj, config).then(
				function (response) {
					$scope.saving = false;
					if (response.data.success == true) {
						globals.mdToast('success', response.data.message);
						$mdSidenav('Create').close();
						$http.get(BASE_URL + 'staff/get_staff/').then(function (Staff) {
							$scope.staff = Staff.data;
							$scope.tabIndex--;
						});
					} else {
						globals.mdToast('error', response.data.message, 7000);
					}
				},
				function (response) {
					$scope.saving = false;
				}
			);
		});
	};

	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};
	}

	if (localStorage.customPanel != null) {
		$scope.customPanel = JSON.parse(localStorage.customPanel);

		$.each($scope.customPanel, (i, a) => {

			if (i.includes('metas') && a && acessoMeta != '1') {
				$scope.customPanel[i] = false;
				if (i.includes('graph')) {
					$scope.totaisBoxEGraphs.splice($scope.totaisBoxEGraphs.indexOf('G'), 1);
				} else {
					$scope.totaisBoxEGraphs.splice($scope.totaisBoxEGraphs.indexOf('B'), 1);
				}
			} else if (i.includes('clientes') && a && acessoCliente != '1') {
				$scope.customPanel[i] = false;

			} else if (i.includes('financeiro') && a && acessoFinanceiro != '1') {
				$scope.customPanel[i] = false;

			}
		})


	} else {
		$scope.customPanel = {
			"box_panel1": true, "box_panel3": true, "box_clientes1": true,
			"graph_panel2": true, "graph_panel3": true, "graph_clientes2": true
		};
		localStorage.customPanel = JSON.stringify($scope.customPanel);
	}


	$scope.customPanelOriginal = $scope.customPanel;
	$.each($scope.customPanel, (i, atual) => {
		if (i.includes('metas') && atual && !loadMeta) {
			setTimeout(a => {
				$scope.getResultadosMetas();
			}, 2000)
		} else if (i.includes('clientes') && atual && !loadCliente) {
			setTimeout(a => {
				$scope.getResultadosClientes();
			}, 2000)
		} else if (i.includes('panel') && atual && !loadResultados) {
			setTimeout(a => {
				$scope.getResultados();
			}, 2000)
		} else if (i.includes('financeiro') && atual && !loadFinanceiro) {
			setTimeout(a => {
				$scope.getResultadosFinanceiro();
			}, 2000)
		}
	});




	var graphNvOportMes = null;
	var graphNvOport = null;
	var graphNvOportPorClientes = null;
	var graphSetorDeAtividades = null;

	var graphFaturamentoTop10 = null;
	var graphNovosClientes = null;
	var graphClientesAtivos = null;
	var graphEstado = null;
	var graphRamoDeAtividade = null;

	var graphEstados = null;
	var graphOrigem = null;
	var atv_graph = null;
	var leadslist = [];
	var staff = [];
	var leadssources = [];
	$scope.report = [];

	$scope.report.totalMeta = '0';
	$scope.report.totalAtingido = '0';
	$scope.report.porcetAtingido = '0';
	$scope.report.totalFalta = '0';
	$scope.report.porcentFalta = '0';

	$scope.report.totalClientesAtivos = '0';
	$scope.report.totalLTV = '0';
	$scope.report.totalLTMes = '0';
	$scope.report.totalLTDias = '0';
	$scope.report.totalChurn = '0%';



	$http.get(BASE_URL + 'api/staff/1').then(function (Staff) {
		$scope.staff = Staff.data;
		staff = Staff.data;
		$scope.overviewLoader = false;

		$('#main-content').show();
		if (is_admin == "1") {
			$http.get(BASE_URL + 'companies/get_companies').then(function (Data) {

				$scope.companies = Data.data;
			});
		}

		$http.get(BASE_URL + 'leads/leadslist/1').then(function (data) {
			leadslist = data.data;
			$scope.leadslist = data.data;
			$('#main-content').show();
		});

	});



	$http.get(BASE_URL + 'leads/leadsources/1').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;
		leadssources = LeadSources.data;
	});

	$http.get(BASE_URL + 'products/get_products').then(function (data) {
		$scope.products = data.data;
	});

	$http.get(BASE_URL + 'goals/get_metas').then(function (data) {
		if (data.data.length > 0) {
			$scope.goals = data.data;
			$scope.filtros.id_goal = $scope.goals[0].id_goal;
		}

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

	}

	$scope.formataMilhar = function (vl) {
		vl = parseFloat(vl);
		if (vl < 1000.00) {
			return parseFloat(vl).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
		} else {
			var vlSt = vl.toString();
			if (vl > 1000.00 && vl < 10000.00) {
				return vlSt.substr(0, 1) + "." + vlSt.substr(1, 1) + "K";
			} else if (vl >= 10000.00 && vl < 99999.99) {
				return vlSt.substr(0, 2) + "." + vlSt.substr(2, 1) + "K";
			} else if (vl >= 100000.00 && vl < 999999.99) {
				return vlSt.substr(0, 3) + "." + vlSt.substr(3, 1) + "K";
			} else if (vl >= 1000000.00 && vl < 9999999.99) {
				return vlSt.substr(0, 4) + "." + vlSt.substr(4, 1) + "M";
			} else if (vl >= 10000000.00 && vl < 99999999.99) {
				return vlSt.substr(0, 5) + "." + vlSt.substr(5, 1) + "M";
			} else if (vl >= 100000000.00 && vl < 999999999.99) {
				return vlSt.substr(0, 6) + "." + vlSt.substr(6, 1) + "M";
			} else if (vl >= 1000000000.00 && vl < 9999999999.99) {
				return vlSt.substr(0, 7) + "." + vlSt.substr(7, 1) + "B";
			}
		}
	}

	$scope.getResultadosFinanceiro = function () {

		if (loadFinanceiro) {
			return;
		}

		loadFinanceiro = true;
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



		$http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamento', dataObj, config).then(function (data) {
			$scope.graphFaturamento = graph02(data.data);
		});

		$http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamentoehDespesa', dataObj, config).then(function (data) {

			graph04('#FaturamentoehDespesa', data.data, ['#1a33b9', '#bf2a0f'])

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


			$http.post(BASE_URL + 'DashInvoices/getReports_faturamentoTotais', dataObj, config).then(function (data) {
				if (data.data != null) {
					$scope.report.fatBruto = $scope.formataMilhar(data.data.fatBruto);
					$scope.report.totalDepsECompras = $scope.formataMilhar(data.data.totalDepsECompras);

					$scope.report.RestLiquid = $scope.formataMilhar(parseFloat(data.data.fatBruto) - parseFloat(data.data.totalDepsECompras));
					var MargemRestLiq = ((parseFloat($scope.report.RestLiquid) * 100) / parseFloat(data.data.fatBruto));
					$scope.report.MargemRestLiq = (!isNaN(MargemRestLiq) && MargemRestLiq > 0 ? MargemRestLiq.toFixed(2) : '0') + '%';
				}
			});

			$http.post(BASE_URL + 'DashInvoices/getReports_graphDespesasECompras', dataObj, config).then(function (data) {
				graph04('#despesasECompras', data.data, ['#f00', '#f17d1a'])
			});

			$http.post(BASE_URL + 'DashInvoices/getReports_graphreceitasEDespesas', dataObj, config).then(function (data) {
				graph04('#receitasXDespesas', data.data, ['#1a33b9', '#bf2a0f'])
			});

		});


	}

	$scope.getResultadosClientes = function () {
		if (loadCliente) {
			return;
		}
		loadCliente = true;
		var dataObj = $.param({
			ano: $scope.filtros.ano,
			produto: $scope.filtros.produto,
			vendedor: $scope.filtros.flt_funcionario,
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

		$http.post(BASE_URL + 'customersDash/getReports_totaisCustomers', dataObj, config).then(function (data) {
			if (data.data != null) {
				var inativos = data.data.totalClientesInativos;
				var ativos = data.data.totalClientesAtivos;

				$scope.report.totalClientesAtivos = data.data.totalClientesAtivos;
				$scope.report.totalLTV = $scope.formataMilhar(data.data.totalLTV);
				$scope.report.totalLTMes = data.data.totalLTMes;
				$scope.report.totalLTDias = data.data.totalLTDias;
				$scope.report.totalChurn = parseFloat((inativos * 100) / (inativos + ativos)).toFixed(2) + '%';
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

			$http.post(BASE_URL + 'customersDash/getReports_graphFaturamentoTop10', dataObj, config).then(function (data) {
				graph01('#graphFaturamentoTop10', data.data, null, "#3f51b5");
			});

			$http.post(BASE_URL + 'customersDash/getReports_graphNovosClientes', dataObj, config).then(function (data) {
				graphNovosClientes = graph01('#graphNovosClientes', data.data, null, "#3f51b5");
			});

			$http.post(BASE_URL + 'customersDash/getReports_graphClientesAtivos', dataObj, config).then(function (data) {
				graph05('#graphClientesAtivos', data.data);

			});

		});


	}


	$scope.getResultadosMetas = function () {
		if (loadMeta) {
			return;
		}
		loadMeta = true;

		var dataObj = $.param({
			ano: $scope.filtros.ano,
			mes: $scope.filtros.mes,

			id_goal: $scope.filtros.id_goal,
			id_equipe: $scope.filtros.id_equipe,
			funcionario: $scope.filtros.flt_funcionario,
			produto: $scope.filtros.produto,
			id_company: CONST_idCompany != null ? CONST_idCompany : $scope.filtros.id_company
		})

		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};

		$http.post(BASE_URL + 'dashGoals/getReports_metaVsResultado', dataObj, config).then(function (data) {
			graph04('#graphMetaVsResultado', data.data, ['#ababab', '#2196f3']);

			if (data.data != null && data.data.datasets.length > 0) {
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
	$scope.verificaCustomPanel = function (tp, val, campo) {

		var total = 0;
		var tpArray = {
			'G': 'graph',
			'B': 'box'
		}
		if (tp == 'B') {
			$.each($scope.customPanel, (i, a) => {
				if (a === true) {
					if (i.includes(tpArray[tp])) {
						total++;
					}
				}
			});
		}

		if (val) {
			if (total >= 4) {
				showToast(NTFTITLE, "Para sua Home você pode selecionar algumas apenas 3 boxes / gráficos", ' error');
				$scope.customPanel[campo] = false;
			}
		}

	}

	$scope.changeCustomPanel = function (tipo = null) {
		localStorage.customPanel = JSON.stringify($scope.customPanel);

		$.each($scope.customPanel, (i, atual) => {
			if (($scope.customPanelOriginal[i] == null || $scope.customPanelOriginal[i] == false) && atual == true) {
				$scope.customPanelOriginal[i] = true;
				if (tipo == "leads") {
					$scope.getResultados();
				} else if (tipo == "metas") {
					$scope.getResultadosMetas();
				} else if (tipo == "clientes") {
					$scope.getResultadosClientes();
				} else if (tipo == "financeiro") {
					$scope.getResultadosFinanceiro();
				}
			}
		});

	}


	$scope.getResultados = function () {
		if (loadResultados) {
			return;
		}
		loadResultados = true;

		$scope.report.totalLeadsPipeline = '';
		$scope.report.totalAtividades = '';
		$scope.report.totalLeadsAtivos = '';
		$scope.report.totalLeadsGerados = '';
		$scope.report.totalLeadsAtrasados = '';
		$scope.report.totalLeadsEmDia = '';


		var dataObj = $.param({
			//dt_de: $scope.filtros.ano + '-' + $scope.filtros.mes + '-01', //$scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
			//dt_ate: $scope.filtros.ano + '-' + $scope.filtros.mes + '-31', //$scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
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








		$http.post(BASE_URL + 'panel/getReports_nvOportunidade_mes', dataObj, config).then(function (data) {
			if (data.data != null) {
				$scope.graphNvOportMes = graph02(data.data);
			}

			$http.post(BASE_URL + 'panel/getReports_graphOrigem', dataObj, config).then(function (data) {
				$scope.graphOrigem = graph02(data.data);

				$http.post(BASE_URL + 'panel/getReports_atv_graph', dataObj, config).then(function (data) {
					graph03('#atv_graph', data.data);

					$http.post(BASE_URL + 'panel/getReports_nvOportunidade', dataObj, config).then(function (data) {
						graphNvOport = graph01('#graphNvOport', data.data);
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

					$http.post(BASE_URL + 'panel/getReports_totalAtividades', dataObj, config).then(function (data) {
						if (data.data != null) {
							$scope.report.totalAtividades = data.data;
						}

						$http.post(BASE_URL + 'panel/getReports_totalLeadsAtrasados', dataObj, config).then(function (data) {

							if (data.data != null) {
								$scope.report.totalLeadsAtrasados = data.data;
							}
						});


					});
					$http.post(BASE_URL + 'panel/getReports_totalLeadsAtivos', dataObj, config).then(function (data) {
						if (data.data != null) {
							$scope.report.totalLeadsAtivos = data.data;
						}
					});
					$http.post(BASE_URL + 'panel/getReports_totalLeadsGerados', dataObj, config).then(function (data) {
						if (data.data != null) {
							$scope.report.totalLeadsGerados = data.data;
						}
					});
				});

				$http.post(BASE_URL + 'panel/getReports_nvOportunidadePorCliente', dataObj, config).then(function (data) {
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
				});
			});
		});





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
		document.getElementById('totalLeadsAtrasados').addEventListener('load', function () {
			this.style.display = 'block'
		});
		document.getElementById('totalAtividades').addEventListener('load', function () {
			this.style.display = 'block'
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

	$scope.openModalCustom = function () {
		$('#modalCustom').modal('show');
	}

	function getBase64(file) {
		if (file == null) {
			return new Promise(resolve => {
				resolve(null);
			})
		} else {
			const reader = new FileReader()
			return new Promise(resolve => {
				reader.onload = ev => {
					resolve(ev.target.result)
				}
				reader.readAsDataURL(file)
			})
		}
	}
}

CiuisCRM.controller('Panel2_Controller', Panel2_Controller);
