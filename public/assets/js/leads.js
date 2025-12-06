function Leads_Controller($scope, $http, $mdSidenav, $mdDialog, $mdConstant, $filter, $q) {
	"use strict";
	$scope.lead = [];
	$scope.lead.tags = [];
	$scope.filtros = {};
	$scope.filtros.flt_funil = 0;
	$scope.leads = [];
	$scope.selectedLeads = [];
	$scope.leadsLoader = true;
	var param = get_parametro_url();

	$scope.funilSelect_index = localStorage.funil != null ? localStorage.funil : 0;
	$scope.funilSelect_id = 0;
	var statusCarregado = [];

	$scope.ContatosCarregado = [];
	$scope.lostFunilCarregado = [];
	$scope.FunilCarregado = [];
	$scope.ContatosLead = [];
	$scope.carregandoFunil = false;
	$scope.filtroPendente = false;
	$scope.anotacoesAtv = false;
	$scope.emailsAut = [];
	$scope.selectsFunil = [];
	$scope.selectsFunc = [];
	$scope.selectsInativar = [];



	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});

	$scope.sortableOptions = {
		'stop': () => {
			$.each($scope.leadslist, (index, list) => {
				var order = [];
				list.leadstatuses.map((status, index2) => {
					order[index2] = status.id;
				});

				var dataObj = $.param({ order, id: list.id });

				$http.post(BASE_URL + 'leads/reorderList', dataObj, config).then(
					function (response) {
					},
					function () {
					}
				);
			})

		}
	};

	$scope.dimiss = function (ev) {
		$(".modal").modal('hide');
	};

	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'lead').then(function (custom_fields) {
		$scope.all_custom_fields = custom_fields.data;
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	});

	$scope.openSearch = function () {
		$('.search-wrapper').toggleClass('active');
		if ($scope.ContatosCarregado[$scope.filtros.flt_funil] == null) {
			$scope.ContatosCarregado[$scope.filtros.flt_funil] = true;
			$http.get(BASE_URL + 'leads/getContatosLeadAll/' + $scope.funilSelect_id).then(function (data) {
				$scope.ContatosLead = data.data;
			});
		}
	}


	$scope.updateColumns = function (column, value) {

		var dataObj = $.param({
			column: column,
			value: +value,
		});

		var posturl = BASE_URL + 'api/update_columns/leads';

		$http.post(posturl, dataObj, config).then(
			function (response) {
			}, function (error) { }
		);
	};

	$scope.SelectSalesFlow = function () {
		$("#SelectSalesFlow").modal("show");
		$scope.fluxos_manual = [];
		$http.get(BASE_URL + 'sales_flow/get_fluxos_manual').then(function (result) {
			$scope.fluxos_manual.fluxos = result.data;
		});
	}

	$scope.AlterarSalesFlow = function () {
		if ($scope.fluxos_manual.sales_flow.length == 0) {
			showToast('Atenção!', 'É preciso informar o sales flow', 'danger');
			return;
		}

		$("#SelectSalesFlow").modal("hide");

		var dataObj = $.param({
			selectedLeads: $scope.selectedLeads,
			sales_flow: $scope.fluxos_manual.sales_flow
		});

		$http.post(BASE_URL + 'leads/AlterarSalesFlow', dataObj, config).then(() => {
			showToast(NTFTITLE, "Salvo com sucesso!", ' success');
		}, () => {
			showToast(NTFTITLE, "Erro, tente novamente!", ' error');
		});
	}


	$scope.KanbanBoard = true;

	$http.get(BASE_URL + 'api/table_columns/leads').then(function (Data) {
		$scope.table_columns = Data.data;
		if ($scope.table_columns.list_view == true) {
			$scope.KanbanBoard = false;
		} else {
			$scope.KanbanBoard = true;
		}
	});



	$scope.ShowKanban = function () {
		$scope.KanbanBoard = true;
	};



	$scope.HideKanban = function () {
		$scope.KanbanBoard = false;
	};

	$scope.toggleFilter = buildToggler('ContentFilter');
	$scope.LeadSettings = buildToggler('LeadsSettings');

	$scope.Create = function () {
		//$scope.lead.funil_list = ($scope.filtros.flt_funil + 1);
		//$scope.lead.status_id = 0;
		$mdSidenav('Create').toggle();
	};

	$scope.Import = buildToggler('Import');


	$scope.Export = function () {

		var dataObj = $.param({
			flt_vencidos: $scope.filtros.flt_vencidos,
			flt_periodo: $scope.filtros.flt_periodo,
			flt_funcionario: $scope.filtros.flt_funcionario,
			flt_origem: $scope.filtros.flt_origem,
			flt_funil: $scope.funilSelect_id,
			lost: $scope.filtros.lost,
			temperatura: $scope.filtros.temperatura,
		});


		var confirm = $mdDialog.confirm().title("Aviso!").textContent('Você deseja exportar os leads com todos os dados enriquecidos?')
			.ok('Exportar normal')
			.cancel('Exportar com enriquecimento');

		$mdDialog.show(confirm).then(function () {
			window.open(BASE_URL + 'leads/exportdata?' + dataObj, '_blank')
		}, function () {
			window.open(BASE_URL + 'leads/exportdataFull?' + dataObj, '_blank')
		});

	}


	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	globals.get_countries();

	$scope.getStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.states = States.data;
		});
	};



	$scope.close = function () {
		$mdSidenav('ContentFilter').close();
		$mdSidenav('LeadsSettings').close();
		$mdSidenav('Create').close();
		$mdSidenav('Import').close();
		$mdSidenav('SelectsEmailAut').close();

		$mdSidenav('SelectsFunil').close();
		$mdSidenav('SelectsFunc').close();
		$mdSidenav('SelectsInativar').close();




		$mdDialog.hide();
	};



	$scope.ConvertedStatus = function (ev) {
		$mdDialog.show({
			templateUrl: 'converted-status-template.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};



	$scope.keys = [$mdConstant.KEY_CODE.ENTER, $mdConstant.KEY_CODE.COMMA];
	var semicolon = 186;

	$scope.customKeys = [$mdConstant.KEY_CODE.ENTER, $mdConstant.KEY_CODE.COMMA, semicolon];



	$http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {
		$scope.settings_detail = Settings.data;
		$scope.ConvertedLeadStatus = $scope.settings_detail.converted_lead_status_id;

		$scope.MakeConvertedLedStatus = function () {
			$http.post(BASE_URL + 'leads/make_converted_status/' + $scope.ConvertedLeadStatus, config)
				.then(
					function (response) {
						$mdDialog.hide();
						$scope.get_leads_by_funil();
					},
					function (response) {
						console.log(response);
					}

				);

		};



		$scope.RemoveConverted = function () {
			// Appending dialog to document.body to cover sidenav in docs app
			var confirm = $mdDialog.confirm().title(MSG_TITLE).textContent(MSG_REMOVE)
				.ariaLabel('Delete Converted Leads')
				.targetEvent($scope.ConvertedLeadStatus)
				.ok(MSG_OK)
				.cancel(MSG_CANCEL);

			$mdDialog.show(confirm).then(function () {
				$http.post(BASE_URL + 'leads/remove_converted/' + $scope.ConvertedLeadStatus, config)
					.then(
						function (response) {
							$scope.get_leads_by_funil();
						},
						function (response) {
							console.log(response);
						}
					);
			}, function () {
				//
			});
		};

	});


	if ($('#leads_by_leadsource').length > 0) {
		$http.get(BASE_URL + 'api/leads_by_leadsource_leadpage').then(function (LeadsBySource) {

			new Chart($('#leads_by_leadsource'), {
				type: 'horizontalBar',
				data: LeadsBySource.data,
				options: {
					legend: {
						display: false,
					}
				}
			});

		});
	}
	$scope.changeFunil = function (id) {
		var index = 0;
		localStorage.funil = id;

		$.each($scope.leadslist, (i, f) => {
			if (index == $scope.filtros.flt_funil) {
				$scope.funilSelect_id = f.id_list;
			}
			index++;
		});

	}

	$scope.get_leads_by_funil = function (refresh = false) {
		var index = 0;
		//return;
		$.each($scope.leadslist, (i, f) => {
			if (index == $scope.filtros.flt_funil) {
				$scope.funilSelect_id = f.id_list;
				if ((
					$scope.filtros.lost == "0" && $scope.FunilCarregado[$scope.filtros.flt_funil] == null
				) ||
					(
						$scope.filtros.lost != null
						&& ($scope.filtros.lost == "1" || $scope.filtros.lost == "-1")
						&& $scope.lostFunilCarregado[$scope.filtros.flt_funil] == null
					)
					|| refresh
				) {
					if (!$scope.carregandoFunil) {
						$scope.get_leads_by_funil_Request(($scope.filtros.lost == "1" ? null : 20), f.id_list);
					} else {
						$scope.filtroPendente = true;
					}

				}
			}
			index++;
		});

	}

	$scope.get_leads_by_funil_Request = function (limit = null, flt_funil) {
		let lost = $scope.filtros.lost;
		if ($scope.filtros.lost == "-1") {
			if (($scope.FunilCarregado[$scope.filtros.flt_funil] != null && $scope.FunilCarregado[$scope.filtros.flt_funil]) &&
				($scope.lostFunilCarregado[$scope.filtros.flt_funil] == null || !$scope.lostFunilCarregado[$scope.filtros.flt_funil])
			) {
				lost = '1';
			} else if (
				($scope.FunilCarregado[$scope.filtros.flt_funil] == null || !$scope.FunilCarregado[$scope.filtros.flt_funil]) &&
				($scope.lostFunilCarregado[$scope.filtros.flt_funil] != null || $scope.lostFunilCarregado[$scope.filtros.flt_funil])) {
				lost = '0';
			} else if (
				($scope.FunilCarregado[$scope.filtros.flt_funil] == null || !$scope.FunilCarregado[$scope.filtros.flt_funil]) &&
				($scope.lostFunilCarregado[$scope.filtros.flt_funil] == null || !$scope.lostFunilCarregado[$scope.filtros.flt_funil])) {
				lost = '-1';
			} else {
				$scope.filtrarLeadsVerifica();

				if ($scope.filtroPendente) {
					$scope.filtroPendente = false;
					$scope.filtrarLeads();
				}
				return;
			}
		} else if ($scope.filtros.lost == "1" &&
			($scope.lostFunilCarregado[$scope.filtros.flt_funil] != null || $scope.lostFunilCarregado[$scope.filtros.flt_funil])
		) {
			$scope.filtrarLeadsVerifica();
			if ($scope.filtroPendente) {
				$scope.filtroPendente = false;
				$scope.filtrarLeads();
			}
			return;
		}

		var dataObj = $.param({
			flt_funil,
			limit,
			lost: lost,
		});

		$scope.carregandoFunil = true;
		if ($scope.filtros.lost != "1") {
			$scope.FunilCarregado[$scope.filtros.flt_funil] = true;
		}

		$http.post(BASE_URL + 'leads/get_leads_by_funil', dataObj, config).then(function (Leads) {

			$scope.leads = $scope.leads.concat(Leads.data);
			$scope.filtrarLeadsVerifica();
			$scope.carregandoFunil = false;

			if ($scope.filtroPendente) {
				$scope.filtroPendente = false;
				$scope.filtrarLeads();
			}


			//$scope.get_fluxo_funil();
		});
	}



	$scope.filtrarLeads = function (leads = []) {
		if ($scope.FunilCarregado[$scope.filtros.flt_funil] == null && $scope.filtros.lost != "1") {
			$scope.get_leads_by_funil();
			return;
		}

		if ($scope.filtros.lost != null
			&& ($scope.filtros.lost == "1" || $scope.filtros.lost == "-1")
			&& $scope.lostFunilCarregado[$scope.filtros.flt_funil] == null
		) {
			$scope.get_leads_by_funil();
			$scope.lostFunilCarregado[$scope.filtros.flt_funil] = true;
			$scope.leadsLoader = false;
			return;
		}

		if (leads.length > 0) {
			leads.map((row, index) => {
				var leadF = $scope.leads.filter(a => a.id == row.id);
				if (leadF.length == 0) {
					$scope.leads.push(row);
				}
			});
		}
		$scope.filtrarLeadsVerifica();
	}
	$scope.filtrarLeadsVerifica = function () {
		$scope.leadsLoader = true;
		$scope.leads.map((row, index) => {

			$scope.leads[index].view = '1';

			if ($scope.filtros.flt_vencidos != null) {
				if (row.prazo != $scope.filtros.flt_vencidos && $scope.filtros.flt_vencidos != "-1") {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.flt_periodo != null) {
				var qtdDias = getDiferencasDeDatas(row.createddate);
				if (qtdDias > $scope.filtros.flt_periodo && $scope.filtros.flt_periodo != "-1") {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.flt_funcionario != null) {
				if (row.assigned_id != $scope.filtros.flt_funcionario && $scope.filtros.flt_funcionario != "-1") {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.flt_origem != null) {
				if (row.source != $scope.filtros.flt_origem && $scope.filtros.flt_origem != "-1") {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.lost != null) {
				if ((row.lost == null ? 0 : row.lost) != $scope.filtros.lost && $scope.filtros.lost != "-1") {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.temperatura != null) {
				if (row.temperatura != $scope.filtros.temperatura && $scope.filtros.temperatura != "-1") {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.canais != null && $scope.filtros.canais != '-1') {
				let temEmail = false;
				let temTelefone = false;
				let temLinkedin = false;

				// Verifica email no lead principal
				if (row.email && row.email.trim() !== '') {
					temEmail = true;
				}

				// Verifica email nos contatos
				if (!temEmail && row.contatos_emails && row.contatos_emails.trim() !== '') {
					temEmail = true;
				}

				// Verifica email nos dados de enriquecimento
				if (!temEmail && row.has_enrichment_emails == '1') {
					temEmail = true;
				}

				// Verifica telefone no lead principal
				if (row.phone && row.phone.trim() !== '') {
					temTelefone = true;
				}

				// Verifica telefone nos contatos
				if (!temTelefone && row.contatos_telefones && row.contatos_telefones.trim() !== '') {
					temTelefone = true;
				}

				// Verifica telefone nos dados de enriquecimento
				if (!temTelefone && row.has_enrichment_phones == '1') {
					temTelefone = true;
				}

				// Verifica LinkedIn
				if (row.linkedin && row.linkedin.trim() !== '') {
					temLinkedin = true;
				}

				// Aplica o filtro baseado no canal selecionado
				if ($scope.filtros.canais == '1') { // Email
					if (!temEmail) {
						$scope.leads[index].view = '0';
					}
				} else if ($scope.filtros.canais == '2') { // Telefone
					if (!temTelefone) {
						$scope.leads[index].view = '0';
					}
				} else if ($scope.filtros.canais == '3') { // Email + Telefone
					if (!temEmail || !temTelefone) {
						$scope.leads[index].view = '0';
					}
				} else if ($scope.filtros.canais == '4') { // LinkedIn
					if (!temLinkedin) {
						$scope.leads[index].view = '0';
					}
				}
			}

			if ($scope.filtros.tags != null && $scope.filtros.tags.length > 0) {
				var s = $scope.filtros.tags.toLowerCase();
				if (row.tags != null) {
					if (!row.tags.toLowerCase().includes(s)) {
						$scope.leads[index].view = '0';
					}
				} else {
					$scope.leads[index].view = '0';
				}
			}

			if ($scope.filtros.lead_search) {
				const s = $scope.filtros.lead_search.toLowerCase();
				const campos = [
					row.company,
					row.email,
					row.name,
					row.name_lead
				];

				// Verifica se há correspondência direta nos campos principais
				const temMatchDireto = campos.some(campo =>
					campo?.toLowerCase().includes(s)
				);

				let temMatchContato = false;

				// Se não houver match direto, verifica os contatos
				if (!temMatchDireto && $scope.ContatosLead?.length > 0) {
					const contatos = $scope.ContatosLead.filter(c => c.id_lead === row.id);
					temMatchContato = contatos.some(c =>
						c.nm_contato?.toLowerCase().includes(s)
					);
				}

				// Se nenhum dos dois tiver correspondência, esconde o lead
				if (!temMatchDireto && !temMatchContato) {
					$scope.leads[index].view = '0';
				}
			}
		});

		$scope.leads.sort(function (a, b) {
			return (a.name_lead.toLowerCase() > b.name_lead.toLowerCase()) ? 1 : ((b.name_lead.toLowerCase() > a.name_lead.toLowerCase()) ? -1 : 0);
		});

		$scope.leadsLoader = false;
	};


	var deferred = $q.defer();
	$scope.lead_list = {
		order: '',
		limit: 5,
		page: 1
	};

	$scope.promise = deferred.promise;
	getLeadList();

	function getDiferencasDeDatas(d1) {
		var d2 = new Date();
		d1 = new Date(d1);
		var diff = moment(d2, "DD/MM/YYYY HH:mm:ss").diff(moment(d1, "DD/MM/YYYY HH:mm:ss"));
		var dias = moment.duration(diff).asDays();
		return dias;
	}


	$scope.dropSuccessHandler = function ($event, index, array) {
		$scope.selected_lead = $scope.leads[index];
	};


	$scope.onDrop = function ($event, $data, array, index) {
		if ($scope.dropProcessing) return;
		$scope.dropProcessing = true;

		$scope.moved_lead = $data;
		var dataObj = $.param({
			lead_id: $scope.moved_lead.id,
			status_id: array,
		});

		var leadIndex = 0;
		$scope.leads.filter((a, i) => {
			if (a.id == $scope.moved_lead.id) {
				leadIndex = i;
			}
		})

		$scope.leads[leadIndex].status = array;

		$http.post(BASE_URL + 'leads/move_lead', dataObj, config).then(
			function (response) {
				$scope.dropProcessing = false;
				//	getLeadList();
				showToast(NTFTITLE, "Salvo com sucesso!", ' success');
			},
			function () {
				showToast(NTFTITLE, "Erro, tente novamente!", ' error');
			}
		);

	};



	$scope.saving = false;

	$scope.AddLead = function () {

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
				active: value.active,
			});

		});

		if (!$scope.lead) {
			var dataObj = $.param({
				title: '',
				date_contacted: '',
				name: '',
				company: '',
				assigned: '',
				status: '',
				source: '',
				phone: '',
				email: '',
				website: '',
				country_id: '',
				state: '',
				state_id: '',
				city: '',
				zip: '',
				address: '',
				description: '',
				public: '',
				type: '',
				tags: '',
				custom_fields: $scope.tempArr,

			});

		} else {
			if ($scope.lead.public === true) {
				$scope.isPublic = 1;
			} else {
				$scope.isPublic = 0;
			}

			if ($scope.lead.type === true) {
				$scope.isIndividual = 1;
			} else {
				$scope.isIndividual = 0;
			}

			if ($scope.lead.date_contacted) {
				$scope.lead.date_contacted = moment($scope.lead.date_contacted).format("YYYY-MM-DD HH:mm:ss");
			}

			if ($scope.lead.dt_nascimento) {
				$scope.lead.dt_nascimento = moment($scope.lead.dt_nascimento).format("YYYY-MM-DD");
			}


			if ($scope.lead.funil_list == null || $scope.lead.funil_list.length == 0) {
				globals.mdToast('error', "Informe um funil!");

				return;
			}

			if ($scope.lead.status_id == null || $scope.lead.status_id.length == 0) {
				globals.mdToast('error', "Informe um status!");
				return;
			}


			if ($scope.lead.tp_pessoa == "2") {
				if ($scope.lead.company.length == 0) {
					globals.mdToast('error', "Informe o nome da empresa!");
					return;
				}
			} else if ($scope.lead.tp_pessoa == "1") {
				if ($scope.lead.name == null || $scope.lead.name.length == 0) {
					globals.mdToast('error', "Informe o nome!");
					return;
				}
			}


			var dataObj = $.param({
				title: $scope.lead.title,
				tp_pessoa: $scope.lead.tp_pessoa,
				date_contacted: $scope.lead.date_contacted,
				name: $scope.lead.name,
				company: $scope.lead.company,
				assigned: $scope.lead.assigned_id,
				status: $scope.lead.status_id,
				source: $scope.lead.source_id,
				phone: $scope.lead.phone,
				email: $scope.lead.email,
				website: $scope.lead.website,
				country_id: $scope.lead.country_id,
				state_id: $scope.lead.state_id,
				city: $scope.lead.city,
				closer: $scope.lead.closer,
				zip: $scope.lead.zip,
				address: $scope.lead.address,
				description: $scope.lead.description,
				public: $scope.isPublic,
				type: $scope.isIndividual,
				tags: JSON.stringify($scope.lead.tags),
				custom_fields: $scope.tempArr,

				temperatura: $scope.lead.temperatura,
				cnpj: $scope.lead.cnpj,
				web_site: $scope.lead.web_site,
				cpf: $scope.lead.cpf,
				setor_atividade: $scope.lead.setor_atividade,
				dt_nascimento: $scope.lead.dt_nascimento,
				porte: $scope.lead.porte,
				instagram: $scope.lead.instagram,
				facebook: $scope.lead.facebook,
				linkedin: $scope.lead.linkedin,
				is_whatsApp: $scope.lead.is_whatsApp == true ? '1' : '0',
			});

		}

		$scope.saving = true;
		$http.post(BASE_URL + 'leads/create', dataObj, config).then(
			function (response) {
				$scope.saving = false;
				if (response.data.success == true) {
					$mdSidenav('Create').close();
					globals.mdToast('success', response.data.message);
					$scope.leads = $scope.leads.concat(response.data.lead);

					//$scope.get_leads_by_funil(true);
					$scope.filtrarLeadsVerifica();
					$scope.lead = [];
				} else {
					globals.mdToast('error', response.data.message);
				}
			},

			function (response) {
				$scope.saving = false;
			}
		);
	};



	$scope.filter = {};

	$scope.getOptionsFor = function (propName) {
		return ($scope.leads || []).map(function (item) {
			return item[propName];
		}).filter(function (item, idx, arr) {
			return arr.indexOf(item) === idx;
		}).sort();
	};

	$scope.FilteredData = function (item) {

		// Use this snippet for matching with AND

		var matchesAND = true;

		for (var prop in $scope.filter) {

			if (noSubFilter($scope.filter[prop])) {

				continue;

			}

			if (!$scope.filter[prop][item[prop]]) {

				matchesAND = false;

				break;

			}

		}
		return matchesAND;
	};



	function noSubFilter(subFilterObj) {
		for (var key in subFilterObj) {
			if (subFilterObj[key]) {
				return false;
			}
		}
		return true;
	}

	// Filtered Datas

	$scope.search = {

		name: '',

		statusname: ''

	};



	function getLeadList() {
		$http.get(BASE_URL + 'leads/leadslist').then(function (data) {
			setTimeout(a => {
				$scope.leadslist = data.data;
				$scope.leadsLoader = false;

				if (localStorage.funil != null) {
					if ($scope.leadslist[localStorage.funil] != null) {
						$scope.filtros.flt_funil = localStorage.funil;
					} else {
						$scope.filtros.flt_funil = 0;
					}

				} else {
					$scope.filtros.flt_funil = 0;
				}

				$scope.get_leads_by_funil();
			}, 2000);

		});
	}

	$scope.NewStatus = function (id_list) {
		globals.createDialog($scope.lang.new_status, $scope.lang.type_status_name, $scope.lang.status_name, event, $scope.lang.add,
			$scope.lang.cancel, 'leads/add_status', function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					getLeadList();
				} else {
					globals.mdToast('error', response.message);
				}
			}, { "id_list": id_list });
	};

	$scope.NewFunil = function () {
		globals.createDialog("Novo Funil", "Por favor, digite o nome do funil", "Nome do funil", event, $scope.lang.add,
			$scope.lang.cancel, 'leads/add_funil', function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					getLeadList();
				} else {
					globals.mdToast('error', response.message);
				}
			});
	};



	$scope.EditStatus = function (status_id, lead_status, event, index) {
		globals.editDialog($scope.lang.edit + ' ' + $scope.lang.lead + ' ' + $scope.lang.status, $scope.lang.lead_title + ' ' +
			$scope.lang.lead + ' ' + $scope.lang.status + ' ' + $scope.lang.name, $scope.lang.status + ' ' + $scope.lang.name, lead_status,
			event, $scope.lang.save, $scope.lang.cancel, 'leads/update_status/' + status_id, function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					getLeadList();
				} else {
					globals.mdToast('error', response.message);
				}

			});
	};


	$scope.EditFunil = function (id_list, nm_list, event, index) {
		globals.editDialog($scope.lang.edit + ' Funil', ' Você pode alterar o nome do funil', $scope.lang.status + ' ' + $scope.lang.name, nm_list,
			event, $scope.lang.save, $scope.lang.cancel, 'leads/update_funil/' + id_list, function (response) {

				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					getLeadList();
				} else {
					globals.mdToast('error', response.message);
				}

			});
	};

	$scope.DeleteLeadFunil = function (funil) {
		globals.deleteDialog($scope.lang.delete + ' Funil', 'Você confirma a exclusão deste Funil ?',
			funil, $scope.lang.delete, $scope.lang.cancel, 'leads/remove_funil/' + funil, function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					getLeadList();
				} else {
					globals.mdToast('error', response.message);
				}
			});
	};

	$scope.DeleteLeadStatus = function (status) {
		globals.deleteDialog($scope.lang.delete + ' ' + $scope.lang.status, $scope.lang.delete_meesage + ' ' + $scope.lang.status + '?',
			status, $scope.lang.delete, $scope.lang.cancel, 'leads/remove_status/' + status, function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					getLeadList();
				} else {
					globals.mdToast('error', response.message);
				}
			});
	};

	$scope.getLeadByStatus = function (lead_status) {
		return $scope.leads.filter(a => a.status == lead_status && a.view == '1' && ($scope.filtros.lost == '-1' || $scope.filtros.lost == a.lost));
	};


	if (param['flt_vencidos'] != null) {
		$scope.filtros.flt_vencidos = param['flt_vencidos'];
	} else {
		$scope.filtros.flt_vencidos = "-1";
	}

	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;

		$scope.NewSource = function () {
			globals.createDialog($scope.lang.new_source, $scope.lang.type_source_name, $scope.lang.source_name, event, $scope.lang.add, $scope.lang.cancel, 'leads/add_source', function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
						$scope.leadssources = LeadSources.data;
					});

				} else {

					globals.mdToast('error', response.message);

				}
			});
		};



		$scope.EditSource = function (source_id, lead_source, event) {
			globals.editDialog($scope.lang.edit + ' ' + $scope.lang.lead + ' ' + $scope.lang.source, $scope.lang.lead_title + ' ' +
				$scope.lang.lead + ' ' + $scope.lang.source + ' ' + $scope.lang.name, $scope.lang.source + ' ' + $scope.lang.name,
				lead_source, event, $scope.lang.save, $scope.lang.cancel, 'leads/update_source/' + source_id, function (response) {
					if (response.success == true) {
						showToast(NTFTITLE, response.message, ' success');
						$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
							$scope.leadssources = LeadSources.data;
						});
					} else {
						globals.mdToast('error', response.message);
					}

				});

		};



		$scope.DeleteLeadSource = function (index) {
			var source = $scope.leadssources[index];
			globals.deleteDialog($scope.lang.delete + ' ' + $scope.lang.source, $scope.lang.delete_meesage + ' ' + $scope.lang.source + '?',
				source.id, $scope.lang.delete, $scope.lang.cancel, 'leads/remove_source/' + source.id, function (response) {
					if (response.success == true) {
						$scope.leadssources.splice($scope.leadssources.indexOf(source), 1);
						showToast(NTFTITLE, response.message, ' success');
					} else {
						globals.mdToast('error', response.message);
					}
				});

		};

	});

	function get_parametro_url() {

		var query = location.href.split('?');
		var data = {};
		if (query.length > 1) {
			var partes = query[1].split('&');
			partes.forEach(function (parte) {
				var chaveValor = parte.split('=');
				var chave = chaveValor[0];
				var valor = chaveValor[1];
				data[chave] = valor;
			});

		}
		return data;
	}


	$(document).on('change', '.selectStatus', function () {
		let id = $(this).val();
		$('.inptSel' + id).prop('checked', $(this).prop('checked')).change();

	})


	$scope.updateSelection = function (id) {
		var idx = $scope.selectedLeads.indexOf(id);
		if (idx > -1) {
			$scope.selectedLeads.splice(idx, 1);
		} else {
			$scope.selectedLeads.push(id);
		}
	};

	$scope.SelectsEmailAut = function () {
		$scope.adicionadoTask = [];

		$mdSidenav('SelectsEmailAut').toggle();

		if ($scope.emailsAut.length == 0) {
			$http.get(BASE_URL + 'EmailsAut/getTaskAutByUser/0').then(function (data) {
				$scope.emailsAut = data.data;
			});
		}
	}




	$scope.SelectsFunil = function () {
		$mdSidenav('SelectsFunil').toggle();
	}

	$scope.SelectsFunc = function () {
		$mdSidenav('SelectsFunc').toggle();
	}

	$scope.SelectsInativar = function () {
		$mdSidenav('SelectsInativar').toggle();
	}

	$scope.SelectsWhatsApp = function () {
		$scope.SelectsWhatsApp.mensagem = "";
		$('#SelectsWhatsApp').modal('show');
	}

	$scope.addVar = function (text) {
		$scope.SelectsWhatsApp.mensagem += text;

	}


	$scope.saveSelectsWhatsApp = function () {
		if ((!$scope.SelectsWhatsApp.mensagem || $scope.SelectsWhatsApp.mensagem.trim() === '') &&
			!$scope.SelectsWhatsApp.file) {
			showToast('Atenção!', 'A mensagem está vazia e não há arquivo', 'error');
			return;
		}

		$scope.loadEnvioWhatsapp = true;

		const dataObj = {
			selectedLeads: $scope.selectedLeads,
			mensagem: $scope.SelectsWhatsApp.mensagem || null,
		};

		// Se tiver arquivo, adiciona
		if ($scope.SelectsWhatsApp.file) {
			const file = $scope.SelectsWhatsApp.file;
			const reader = new FileReader();
			reader.onload = function (e) {
				const base64Data = e.target.result.split(',')[1];
				dataObj.media = {
					data: base64Data,
					filename: file.name,
					mimetype: file.type
				};

				sendWhatsApp(dataObj);
			};
			reader.readAsDataURL(file);
		} else {
			sendWhatsApp(dataObj);
		}
	};

	function sendWhatsApp(dataObj) {
		$http.post(BASE_URL + 'whatsapp/envia_mensagem_whatsapp_selects', $.param(dataObj), config).then(function (response) {
			$scope.loadEnvioWhatsapp = false;
			if (response.data.success) {
				$('#SelectsWhatsApp').modal('hide');
				globals.mdToast('success', response.data.message);
				$scope.SelectsWhatsApp.mensagem = '';
				$scope.SelectsWhatsApp.file = null;
			} else {
				globals.mdToast('error', response.data.message);
			}
		}, () => {
			$scope.loadEnvioWhatsapp = false;
		});
	}



	$scope.SelectsExcluir = function () {
		var confirm = $mdDialog.confirm().title("Aviso!").textContent('Tem certeza que deseja excluir esses leads?')
			.ok('Sim')
			.cancel('Cancelar');

		$mdDialog.show(confirm).then(function () {
			var dataObj = $.param({
				selectedLeads: $scope.selectedLeads,
			});

			$http.post(BASE_URL + 'EmailsAut/SelectsExcluir', dataObj, config).then(function (response) {
				if (response.data.success == true) {
					window.location.reload();
				}
			})

		}, function () {
		});
	};


	$scope.saveSelectsInativar = function () {
		var dataObj = $.param({
			selectedLeads: $scope.selectedLeads,
			motivo: $scope.selectsInativar.motivo,
		});

		$http.post(BASE_URL + 'EmailsAut/UpdateInativar', dataObj, config).then(function (response) {
			if (response.data.success == true) {
				globals.mdToast('success', response.data.message);

				$scope.selectedLeads.map(id_lead => {
					$scope.leads.map((a, i) => {
						if (a.id == id_lead) {
							$scope.leads.splice(i, 1);
						}

					});

				});
				$scope.close();
			} else {
				globals.mdToast('error', response.data.message);
			}

		}, function (response) {
		});
	}



	$scope.saveSelectsFunc = function () {
		var dataObj = $.param({
			selectedLeads: $scope.selectedLeads,
			assigned_id: $scope.selectsFunc.assigned_id,
		});

		$http.post(BASE_URL + 'EmailsAut/UpdateFunc', dataObj, config).then(function (response) {
			if (response.data.success == true) {
				globals.mdToast('success', response.data.message);
				$scope.close();
			} else {
				globals.mdToast('error', response.data.message);
			}

		}, function (response) {
		});
	}

	$scope.saveSelectsFunil = function () {
		var dataObj = $.param({
			selectedLeads: $scope.selectedLeads,
			funil_list: $scope.selectsFunil.funil_list,
			status_id: $scope.selectsFunil.status_id,
		});

		$http.post(BASE_URL + 'EmailsAut/UpdateFunil', dataObj, config).then(function (response) {
			if (response.data.success == true) {
				globals.mdToast('success', response.data.message);
				window.location.reload();
			} else {
				globals.mdToast('error', response.data.message);
			}

		}, function (response) {
		});
	}

	$scope.UpdateAdicionadoTask = function (id_task = null) {


		let e = 0;
		$scope.selectedLeads.map((id_lead, i) => {
			if ($scope.leads.find(lead => lead.id == id_lead).email.length == 0) {
				$scope.selectedLeads.splice(i, 1);
				e++;
			}
		})

		if (e > 0) {
			globals.mdToast('error', e + " leads sem email salvo.");
		}


		var dataObj = $.param({
			id_task,
			selectedLeads: $scope.selectedLeads,
			action: $scope.adicionadoTask[id_task]
		});

		$http.post(BASE_URL + 'EmailsAut/UpdateAdicionadoTask2', dataObj, config).then(function (response) {
			if (response.data.success == true) {
				globals.mdToast('success', response.data.message);
			} else {
				globals.mdToast('error', response.data.message);
			}

		}, function (response) {
		});
	}



}



function Lead_Controller($scope, $http, $mdSidenav, $mdDialog, $mdConstant, $filter, $sce, $timeout) {
	"use strict";
	$scope.lead = [];
	$scope.lead.leads_data = {};
	$scope.template = [];
	$scope.modal_pausa = [];
	$scope.lead.tags = [];
	$scope.ReminderForm = buildToggler('ReminderForm');
	$scope.Message = "My name is <span style = 'color:red'><b>Mudassar Khan</b></span>";
	$scope.isPlaying = true;
	$scope.selectedIndex = selectedIndex;
	$scope.atividade = [];
	$scope.base_url = BASE_URL;
	$scope.MarkLeadAsVal = 0;
	$scope.exibeIcon = true;
	$scope.modalEtapa = null;
	$scope.user_logado = USERLOGADO;
	$scope.atividadeLancada = false;
	$scope.lead_next = [];
	$scope.reuniaoModal = [];
	$scope.load_fluxo = false;
	$scope.creditos_enriquecimento = 0;
	var ignoraLeads = [];

	$scope.BusinessSize = {
		0: 'Micro empresa',
		1: 'Pequeno porte',
		2: 'Médio porte',
		3: 'Grande Porte'
	};

	$scope.TaxModel = {
		0: 'Lucro Presumido',
		1: 'Lucro Real',
		2: 'Simples Nacional'
	};

	$scope.displayTime = '00:00:00';
	$scope.timerRunning = false;
	var timer;
	var startTime;
	var elapsedTime = 0;


	function formatTime(milliseconds) {
		var totalSeconds = Math.floor(milliseconds / 1000);
		var hours = Math.floor(totalSeconds / 3600);
		var minutes = Math.floor((totalSeconds % 3600) / 60);
		var seconds = totalSeconds % 60;
		return padTime(hours) + ':' + padTime(minutes) + ':' + padTime(seconds);
	}

	function padTime(time) {
		return time < 10 ? '0' + time : time.toString();
	}

	$http.get(BASE_URL + 'settings/get_motivos').then(function (Settings) {
		$scope.motivos = Settings.data.motivos_pausa;
	});

	if (localStorage.ignoraLeads != null) {
		if (localStorage.ignoraLeadsDia != null) {
			var dia = new Date(localStorage.ignoraLeadsDia);
			var diaAtual = new Date();

			if (dia < diaAtual) {
				localStorage.ignoraLeads = '[]';
			} else {
				ignoraLeads = JSON.parse(localStorage.ignoraLeads);
			}
		} else {
			ignoraLeads = JSON.parse(localStorage.ignoraLeads);
		}

	}

	$scope.Update = function (lead) {
		$mdSidenav('Update').toggle();
	};

	$scope.motivos = [{
		id: 1,
		text: 'Pausa'
	},
	{
		id: 2,
		text: 'Almoço'
	},
	{
		id: 3,
		text: 'Reunião'
	},]

	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};

	}

	$scope.get_fluxo = function () {
		$http.get(BASE_URL + 'leads/get_fluxo/' + LEADID + '/' + $scope.lead.id_list).then(function (data) {
			$scope.load_fluxo = true;
			$scope.fluxos = data.data.fluxos;
			$scope.fluxo_lead = $scope.getFluxoLead();

			// Verificar se o flow atual está ativo
			if ($scope.fluxo_lead && $scope.fluxo_lead.id_fluxo) {
				$http.get(BASE_URL + 'leads/check_flow_active/' + LEADID + '/' + $scope.fluxo_lead.id_fluxo).then(function (response) {
					$scope.salesFlowAtivo = response.data.ativo;
				}).catch(function (error) {
					$scope.salesFlowAtivo = false;
				});
			}
		});
	}


	$scope.UpdateAtivo = function (leads_data) {

		var dataObj = $.param({
			id_lead: LEADID,
			leads_data
		});

		$http.post(BASE_URL + 'leads/UpdateAtivo', dataObj, config).then(function (response) {
			showToast('Sucesso!', 'Alterado com sucesso', 'success');


		});

	}
	$scope.nextLead = function () {


		if (!$scope.atividadeLancada) {
			globals.mdToast('error', 'É preciso lançar uma atividade para avançar em leads!');
			return;
		}

		ignoraLeads.push(LEADID);
		localStorage.ignoraLeads = JSON.stringify(ignoraLeads);
		localStorage.ignoraLeadsDia = new Date();


		window.location.href = BASE_URL + 'leads/lead/' + $scope.lead_next.id + '?page=1';
	};

	$scope.stopLead = function () {
		var confirm = $mdDialog.confirm().title("Aviso!").textContent('Tem certeza que deseja parar?')
			.ok('Sim')
			.cancel('Cancelar');

		$mdDialog.show(confirm).then(function () {
			localStorage.ignoraLeads = '[]';

			window.location.href = BASE_URL + 'leadsToDo';
		}, function () {

		});
	};


	$scope.togglePlayPause = function () {
		if (!$scope.isPlaying) {
			clearInterval(timer);
			$scope.displayTime = '00:00:00';
			$scope.timerRunning = false;
			elapsedTime = 0;

			$scope.isPlaying = true;

			var dataObj = $.param({
				id_lead: LEADID,
				pausa: 0
			});

			$http.post(BASE_URL + 'leadsToDo/pausaLead', dataObj, config).then(function (response) {
				//globals.mdToast('success', 'Retomado!');
				$('#modalEmPausa').modal('hide');
			});
		} else {
			$('#modalPausa').modal('show');
		}
	};

	$scope.salva_pausa = function () {
		if ($scope.modal_pausa.motivo == null) {
			globals.mdToast('error', 'Informe um motivo');
			return;
		}
		$('#modalPausa').modal('hide');
		setTimeout(a => {
			$scope.isPlaying = false;
		}, 200);

		var dataObj = $.param({
			id_lead: LEADID,
			pausa: 1,
			motivo: $scope.modal_pausa.motivo
		});

		$http.post(BASE_URL + 'leadsToDo/pausaLead', dataObj, config).then(function (response) {
			//globals.mdToast('success', 'Pausa registrada!');

			startTime = Date.now() - elapsedTime;
			timer = setInterval(function () {
				elapsedTime = Date.now() - startTime;
				$scope.displayTime = formatTime(elapsedTime);
			}, 1000);
			$scope.timerRunning = !$scope.timerRunning;

			$('#modalEmPausa').modal('show');
		});


	};

	$http.get(BASE_URL + 'leads/get_credito').then(function (data) {
		$scope.creditos_enriquecimento = data.data.creditos_enriquecimento;
	});

	$http.get(BASE_URL + 'leads/getListSelectLeads').then(function (data) {
		$scope.leadAtvSelect = data.data;
	});


	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});

	$http.get(BASE_URL + 'leads/getContatosLead/' + LEADID).then(function (Staff) {
		$scope.contatos = Staff.data;
	});

	globals.get_countries();
	$scope.getStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.states = States.data;
		});
	};


	$scope.copyDescricao = function () {
		var copyText = document.getElementById("campoCopiar");
		copyText.select();
		copyText.setSelectionRange(0, 99999);
		navigator.clipboard.writeText(copyText.value);
		globals.mdToast('success', 'Copiado para area de transferência.');
	}

	$scope.copyRespostaIa = function () {
		var copyText = document.getElementById("body_resposta");
		copyText.select();
		copyText.setSelectionRange(0, 99999);
		navigator.clipboard.writeText(copyText.value);
		globals.mdToast('success', 'Copiado para area de transferência.');
	}



	$scope.close = function () {
		$mdSidenav('ReminderForm').close();
		$mdSidenav('Update').close();
	};



	$http.get(BASE_URL + 'api/custom_fields_data_by_type/lead/' + LEADID).then(function (custom_fields) {
		$scope.custom_fields = custom_fields.data;
	});



	$scope.leadsLoader = true;

	$http.get(BASE_URL + 'leads/get_lead/' + LEADID).then(function (Lead) {
		$scope.lead = Lead.data;
		$scope.lead.date_contacted = new Date($scope.lead.date_contacted);
		$scope.lead.dt_nascimento = new Date($scope.lead.dt_nascimento);

		if (Lead.data.tags != null && Lead.data.tags.length > 0) {
			$scope.lead.tags = JSON.parse(Lead.data.tags);
		} else {
			$scope.lead.tags = [];
		}

		$scope.lead.hora_taskAut = new Date('2021-01-01 ' + $scope.lead.hora_taskAut)
		$scope.lead.is_whatsApp = $scope.lead.is_whatsApp == '1' ? true : false;
		
		// Inicializar Sales Flow Ativo - será carregado quando o fluxo for carregado
		$scope.salesFlowAtivo = false;
		
		$scope.leadsLoader = false;

		if (selectedIndex == 1) {
			$scope.get_fluxo();
			var dataObj = $.param({
				leadAtual: LEADID,
				ignoraLeads
			});
			$http.post(BASE_URL + 'leadsToDo/get_leads_next', dataObj, config).then(function (Leads) {
				$scope.lead_next = Leads.data.lead_next;
			});
		}


		$scope.getStates($scope.lead.country_id);

		$scope.OpenMarkLeadAs = function (status) {
			$scope.MarkLeadAsVal = status;
			$("#modalMarcaPerdido").modal("show");
		}

		$http.get(BASE_URL + 'leads/leadslist').then(function (data) {
			$scope.leadslist = data.data;

			$.each($scope.leadslist, (index, funil) => {
				funil.leadstatuses.map((status) => {
					if (status.id == $scope.lead.status_id) {
						$scope.lead.funil_list = funil.id_list;
					}
				});
			});

		});

		$scope.MarkLeadAs = function (status, perdeOportu = null) {

			if (perdeOportu != null && perdeOportu.quando != null) {
				perdeOportu.quando = moment(perdeOportu.quando).format("YYYY-MM-DD");
			}

			if (status === 1) {
				$scope.lead.lost = 1;
				$scope.valuOn = 1;
				$scope.TypeOn = 'lost';
			}

			if (status === 2) {
				$scope.lead.lost = 0;
				$scope.valuOn = 2;
				$scope.TypeOn = 'lost';
			}

			if (status === 3) {
				$scope.lead.junk = 1;
				$scope.valuOn = 3;
				$scope.TypeOn = 'junk';
			}

			if (status === 4) {
				$scope.lead.junk = 0;
				$scope.valuOn = 4;
				$scope.TypeOn = 'junk';
			}

			var dataObj = $.param({
				value: $scope.valuOn,
				perdeOportu
			});

			$http.post(BASE_URL + 'leads/mark_as_lead/' + LEADID, dataObj, config)
				.then(
					function (response) {
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);

							if (perdeOportu != null) {
								$("#modalMarcaPerdido").modal("hide");
							}
						} else {
							globals.mdToast('error', response.data.message);
						}
					},

					function (response) {
						console.log(response);
					}

				);

		};


		$scope.SearcLead = function (lead) {
			if (parseFloat($scope.creditos_enriquecimento) <= 0) {
				showToast('Atenção!', 'Você está sem créditos de enriquecimento', 'danger');
				return;
			}
			var doc = "";
			if (lead.tp_pessoa == "1" && (lead.cpf == null || lead.cpf.length == 0)) {
				showToast('Atenção!', 'É preciso informar o cpf do lead', 'danger');
				return;
			} else if (lead.tp_pessoa == "2" && (lead.cnpj == null || lead.cnpj.length == 0)) {
				showToast('Atenção!', 'É preciso informar o cnpj do lead', 'danger');
				return;
			}

			if (lead.ultimaBusca != null) {
				showToast('Atenção!', 'Já realizado uma busca neste lead.', 'warning');
				return;
			}


			if (lead.type == "1") {
				doc = lead.cpf;
			} else {
				doc = lead.cnpj;
			}

			var dataObj = $.param({
				document: doc,
				id_lead: LEADID
			});


			$http.post(BASE_URL + 'leads/buscaDadosPh3a', dataObj, config).then(function (response) {
				if (response.data.success == true) {
					$scope.lead.leads_data = response.data.data.Data;
					showToast(NTFTITLE, response.data.message, ' success');
					lead.ultimaBusca = new Date();

				} else {
					globals.mdToast('error', response.data.message);
				}

			}, function (response) {

			});

		}


		$scope.saveFluxo = function () {
			var dataObj = $.param({
				id_fluxo: $scope.lead.id_fluxo,
				id_lead: $scope.lead.id
			});

			$http.post(BASE_URL + 'leads/updateFluxo', dataObj, config).then(function (response) {
				if (response.data.success == true) {
					showToast(NTFTITLE, response.data.message, 'success');
				} else {
					globals.mdToast('error', response.data.message);
				}
			}, function (response) {

			});
		}

		$scope.salva_reuniao = function (id_reuniao, reuniao) {

			$('#modalDetalhesReuniao').modal('hide');

			var dataObj = $.param({
				id_reuniao,
				realizada: $scope.reuniaoModal.realizada,
				problema: $scope.reuniaoModal.problema,
				motivo: $scope.reuniaoModal.motivo
			});

			$http.post(BASE_URL + 'reuniao/salva_reuniao', dataObj, config).then(function (response) {
				reuniao.confirmado = 1;
				globals.mdToast('success', 'Alterado com sucesso!');
			});
		};

		$scope.alterarTemperatura = function (temp) {
			$scope.lead.temperatura = temp;
			var dataObj = $.param({
				temperatura: temp
			});
			var posturl = BASE_URL + 'leads/updateTemp/' + LEADID;
			$http.post(posturl, dataObj, config).then(function (response) {
				if (response.data.success == true) {
					showToast(NTFTITLE, response.data.message, ' success');
				} else {
					globals.mdToast('error', response.data.message);
				}

			}, function (response) {
			});
		}

		$scope.alterarQualificacao = function (qualificacao) {
			$scope.lead.qualificacao = qualificacao;
			var dataObj = $.param({
				qualificacao
			});
			var posturl = BASE_URL + 'leads/updateQual/' + LEADID;
			$http.post(posturl, dataObj, config).then(function (response) {
				if (response.data.success == true) {
					showToast(NTFTITLE, response.data.message, ' success');
				} else {
					globals.mdToast('error', response.data.message);
				}

			}, function (response) {
			});
		}

		$scope.saving = false;
		$scope.UpdateLead = function () {
			$scope.saving = true;
			if ($scope.lead.public === true) {
				$scope.isPublic = 1;
			} else {
				$scope.isPublic = 0;
			}
			if ($scope.lead.type === true) {
				$scope.isIndividual = 1;
			} else {
				$scope.isIndividual = 0;
			}

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

			if ($scope.lead.dt_nascimento) {
				$scope.lead.dt_nascimento = moment($scope.lead.dt_nascimento).format("YYYY-MM-DD");
			}

			var dataObj = $.param({

				title: $scope.lead.title,
				tp_pessoa: $scope.lead.tp_pessoa,
				date_contacted: moment($scope.lead.date_contacted).format("YYYY-MM-DD HH:mm"),
				name: $scope.lead.name,
				company: $scope.lead.company,
				assigned_id: $scope.lead.assigned_id,
				status: $scope.lead.status_id,
				source: $scope.lead.source_id,
				phone: $scope.lead.phone,
				email: $scope.lead.email,
				website: $scope.lead.website,
				country_id: $scope.lead.country_id,
				state_id: $scope.lead.state_id,
				city: $scope.lead.city,
				closer: $scope.lead.closer,
				zip: $scope.lead.zip,
				address: $scope.lead.address,
				description: $scope.lead.description,
				public: $scope.isPublic,
				type: $scope.isIndividual,
				tags: JSON.stringify($scope.lead.tags),
				custom_fields: $scope.tempArr,

				temperatura: $scope.lead.temperatura,
				cnpj: $scope.lead.cnpj,
				web_site: $scope.lead.web_site,
				cpf: $scope.lead.cpf,
				setor_atividade: $scope.lead.setor_atividade,
				dt_nascimento: $scope.lead.dt_nascimento,
				porte: $scope.lead.porte,
				instagram: $scope.lead.instagram,
				facebook: $scope.lead.facebook,

				linkedin: $scope.lead.linkedin,
				is_whatsApp: $scope.lead.is_whatsApp == true ? '1' : '0',
			});

			var posturl = BASE_URL + 'leads/update/' + LEADID;
			$http.post(posturl, dataObj, config).then(function (response) {
				$scope.saving = false;
				if (response.data.success == true) {
					showToast(NTFTITLE, response.data.message, ' success');
					$mdSidenav('Update').close();
					$('.modal').modal('hide')
				} else {
					globals.mdToast('error', response.data.message);
				}

			},

				function (response) {
					$scope.saving = false;
				}

			);

		};

	});



	$http.get(BASE_URL + 'leads/leadstatuses').then(function (LeadStatuses) {
		$scope.statuses = LeadStatuses.data;
	});



	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
		$scope.sources = LeadSources.data;
	});



	$scope.Delete = function (index) {
		globals.deleteDialog(lang.attention, lang.delete_lead, LEADID, lang.doIt, lang.cancel, 'leads/remove/' + LEADID, function (response) {
			if (response.success == true) {
				window.location.href = BASE_URL + 'leads';
			} else {
				globals.mdToast('error', response.message);
			}
		});

	};



	$scope.Convert = function (index) {
		globals.deleteDialog(lang.convert_title, lang.convert_text, LEADID, lang.convert, lang.cancel, 'leads/convert/' + LEADID, function (response) {
			if (response.success == true) {
				window.location.href = BASE_URL + 'customers/customer/' + response.id;
			} else {
				globals.mdToast('error', response.message);
			}
		});
	};



	$http.get(BASE_URL + 'api/reminders_by_type/lead/' + LEADID).then(function (Reminders) {
		$scope.in_reminders = Reminders.data;
		$scope.AddReminder = function () {
			var dataObj = $.param({
				description: $scope.reminder_description,
				date: moment($scope.reminder_date).format("YYYY-MM-DD HH:mm:ss"),
				staff: $scope.reminder_staff,
				relation_type: 'lead',
				relation: LEADID,
			});

			var posturl = BASE_URL + 'trivia/addreminder';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						$scope.in_reminders.push({
							'description': $scope.reminder_description,
							'creator': LOGGEDINSTAFFNAME,
							'avatar': UPIMGURL + LOGGEDINSTAFFAVATAR,
							'staff': LOGGEDINSTAFFNAME,
							'date': $scope.reminder_date,
						});

						$mdSidenav('ReminderForm').close();

					},

					function (response) {
						console.log(response);
					}

				);

		};

		$scope.DeleteReminder = function (index) {
			var reminder = $scope.in_reminders[index];
			var dataObj = $.param({
				reminder: reminder.id
			});

			var posturl = BASE_URL + 'trivia/remove_reminder';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						$scope.in_reminders.splice($scope.in_reminders.indexOf(reminder), 1);
					},
					function (response) {
						console.log(response);
					}

				);

		};

	});



	$http.get(BASE_URL + 'api/notes/lead/' + LEADID).then(function (Notes) {
		$scope.notes = Notes.data;
		$scope.AddNote = function () {
			var dataObj = $.param({
				description: $scope.note,
				relation_type: 'lead',
				relation: LEADID,
			});

			var posturl = BASE_URL + 'trivia/addnote';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						if (response.data.success == true) {
							$.gritter.add({
								title: '<b>' + NTFTITLE + '</b>',
								text: response.data.message,
								class_name: 'color success'
							});

							$('.note-description').val('');
							$scope.note = '';
							$http.get(BASE_URL + 'api/notes/lead/' + LEADID).then(function (Notes) {
								$scope.notes = Notes.data;
							});

						} else {
							$.gritter.add({
								title: '<b>' + NTFTITLE + '</b>',
								text: response.data.message,
								class_name: 'color danger'
							});

						}

					},
					function (response) {
						console.log(response);
					}

				);

		};

		$scope.DeleteNote = function (index) {
			var note = $scope.notes[index];
			var dataObj = $.param({
				notes: note.id
			});

			var posturl = BASE_URL + 'trivia/removenote';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						$scope.notes.splice($scope.notes.indexOf(note), 1);
					},

					function (response) {
						console.log(response);
					}

				);

		};

	});



	$http.get(BASE_URL + 'api/proposals').then(function (Proposals) {
		$scope.all_proposals = Proposals.data;
		$scope.proposals = $filter('filter')($scope.all_proposals, {
			relation_type: "lead",
			relation: LEADID
		});
	});


	$scope.changeEmail = function () {

		$scope.atividade.anotacoes = $scope.replaceVariaveis($scope.templates.find(a => a.id == $scope.atividade.email).message);
	}

	$scope.showDialogReuniao = function (reuniao, id_atividade = null) {
		$scope.reuniaoModal = reuniao;

		$scope.reuniaoModal.realizada = reuniao.reuniao.realizada;
		$scope.reuniaoModal.problema = reuniao.reuniao.problema;
		$scope.reuniaoModal.motivo = reuniao.reuniao.motivo;
		$scope.reuniaoModal.id_atividade = id_atividade;

		$("#modalDetalhesReuniao").modal('show');
	};


	$scope.getParentEtapa = function (id_etapa_pai) {
	}

	$scope.showDialogAddAtividade = function (atv = null, etapa = null) {
		$scope.atividade = [];
		$scope.modalEtapa = null;
		//$scope.exibeIcon = false;
		if (atv != null) {

			if (atv.is_reuniao == "1") {
				$scope.showDialogReuniao(atv, atv.id_atividade);
				return;
			}
			$scope.anotacoesAtv = false;
			var atvTemp = JSON.parse(JSON.stringify(atv));
			atvTemp.data = new Date(atvTemp.data + ' 00:00');
			atvTemp.retorno = new Date(atvTemp.retorno + ' 00:00');
			atvTemp.horario = new Date(atvTemp.data + ' 00:00').setHours(atv.horario.split(':')[0], atv.horario.split(':')[1]);
			atvTemp.horario = new Date(atvTemp.horario);
			$scope.atividade = atvTemp;

			setTimeout(function () {
				$("#horarioAtv").val(atv.horario);
			}, 100);
		} else if (etapa != null) {
			$scope.modalEtapa = etapa;
			$scope.atividade.atividade = etapa.atividade.id_atv;
			$scope.atividade.email = etapa.email;
			$scope.atividade.retorno = new Date(new Date().setDate(new Date().getDate() + etapa.dias));
			$scope.atividade.data = new Date();
			$scope.atividade.horario = new Date();
			$scope.atividade.campoCopiar = etapa.campoCopiar;
			$scope.atividade.descricao = etapa.descricao;
			//$scope.atividade.reuniao_call = '0';

			if (etapa.email != null && etapa.email != "0") {
				$scope.atividade.anotacoes = $scope.replaceVariaveis($scope.templates.find(a => a.id == etapa.email).message);
				tinyMCE.get("anotacoesAtv").setContent($scope.replaceVariaveis($scope.atividade.anotacoes), { format: 'raw' });
				$scope.anotacoesAtv = true;
			} else {
				$scope.atividade.anotacoes = $scope.replaceVariaveis(etapa.campoCopiar);
				$scope.anotacoesAtv = false;
			}

			//tinyMCE.get("anotacoesAtv").setContent($scope.replaceVariaveis($scope.atividade.anotacoes), { format: 'raw' });

			var d = new Date(new Date().setDate(new Date().getDate() + etapa.dias));
			setTimeout(function () {
				$("#horarioAtv").val(d.getHours() + ':' + d.getMinutes());
			}, 100);
		} else {
			$scope.anotacoesAtv = false;
			$scope.atividade.data = new Date();
			//$scope.atividade.reuniao_call = '0';
			setTimeout(function () {
				$("#horarioAtv").val(new Date().getHours() + ':' + new Date().getMinutes());
			}, 100);
		}

		$("#modalAddAtividade").modal("show");
	}

	$scope.showReuniao = function (reuniao = null, id_atividade = null) {
		$scope.newReuniao = [];

		if (reuniao == null) {
			$scope.newReuniao.data = new Date();
			$scope.newReuniao.tipo = 'Presencial';
		} else {
			$scope.newReuniao = reuniao;


			$scope.newReuniao.hora = new Date(reuniao.data + ' ' + reuniao.hora);
			$scope.newReuniao.data = new Date(reuniao.data + ' 00:00:00');
			$scope.newReuniao.id_funcionario = reuniao.id_funcionario.split(',');
			$scope.newReuniao.id_atividade = id_atividade;
		}
		$("#modalDetalhesReuniao").modal("hide");
		$("#modalAddReuniao").modal("show");
	}

	$scope.showDialogAddContato = function (contato = null) {
		$scope.newContato = [];

		if (contato != null) {
			if (contato.ctt_principal == "1") {
				contato.ctt_principal = true;
			} else {
				contato.ctt_principal = false;
			}

			if (contato.is_whatsApp == "1") {
				contato.is_whatsApp = true;
			} else {
				contato.is_whatsApp = false;
			}
			$scope.newContato = contato;
		}

		$("#modalAddContato").modal("show");
	}

	$(document).bind('click', function (event) {
		$(".md-select-menu-container").hide();
	});



	$scope.salvarAtividade = function (sem_aviso = false) {
		if (($scope.lead.ultimoRetorno == "" || $scope.lead.ultimoRetorno == null || $scope.lead.ultimoRetorno == '0000-00-00' ||
			($scope.lead.ultimoRetorno != "0000-00-00" && $scope.lead.ultimoRetorno != "" && $scope.lead.ultimoRetorno != null && new Date($scope.lead.ultimoRetorno + ' 23:59:59') < new Date()))
			&& $scope.atividade.retorno == null && !sem_aviso
		) {
			$scope.dimiss();
			Swal.fire({
				title: "Atenção",
				text: "Este lead ainda não tem um agendamento de retorno. Para inserir basta colocar a data na atividade!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Prosseguir mesmo assim",
				cancelButtonText: "Voltar"
			}).then((result) => {
				$("#modalAddAtividade").modal("show");
				if (result.isConfirmed) {
					$scope.salvarAtividade(true);
				}
			});
			return;
		}

		var anotacoes = $scope.atividade.anotacoes;
		if ($scope.anotacoesAtv) {
			anotacoes = tinyMCE.get("anotacoesAtv").getContent({ format: 'raw' }).replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' ');
		}
		//var anotacoes = tinyMCE.get("anotacoesAtv").getContent({ format: 'raw' }).replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' ');


		if ($scope.atividade.atividade == null || $scope.atividade.atividade.length == 0) {
			globals.mdToast('warning', "Informe uma atividade!");
			return;
		} else if ($scope.atividade.data == null || $scope.atividade.data.length == 0) {
			globals.mdToast('warning', "Informe uma data!");
			return;
		}
		// else if ($scope.atividade.horario == null || $scope.atividade.horario.length == 0) {
		//	globals.mdToast('warning', "Informe uma horário!");
		//	return;
		//} 
		else if (anotacoes == null || anotacoes.length == 0) {
			globals.mdToast('warning', "Informe uma anotação!");
			return;
		}
		//else if ($scope.atividade.retorno == null || $scope.atividade.retorno.length == 0) {
		//	globals.mdToast('warning', "Informe uma retorno!");
		//	return;
		//}

		$scope.atividadeLancada = true;

		$scope.atividade.data = moment($scope.atividade.data).format("YYYY-MM-DD");
		if ($scope.atividade.retorno != null) {
			$scope.atividade.retorno = moment($scope.atividade.retorno).format("YYYY-MM-DD");
		}

		$scope.atividade.horario = moment($scope.atividade.horario).format("HH:mm:ss");

		var dataObj = $.param({
			id_lead: LEADID,
			id_atividade: $scope.atividade.id_atividade,
			atividade: $scope.atividade.atividade,
			data: $scope.atividade.data,
			horario: $scope.atividade.horario,
			duracao: $scope.atividade.duracao,
			anotacoes: anotacoes,
			retorno: $scope.atividade.retorno,
			//reuniao_call: $scope.atividade.reuniao_call,
			email: $scope.atividade.email,
			id_etapa_flow: $scope.modalEtapa != null ? $scope.modalEtapa.id_etapa : null
		});

		var posturl = BASE_URL + 'leads/addNewAtividade';
		$http.post(posturl, dataObj, config).then((data) => {
			if (data.data.result == true) {
				$scope.lead.ultimoRetorno = $scope.atividade.retorno;

				$scope.dimiss();
				$scope.atividade = [];
				globals.mdToast('success', "Salvo com sucesso!");
				$http.get(BASE_URL + 'leads/get_list_atividades/' + LEADID).then(function (result) {
					$scope.lead.list_atividades = result.data;
				});

				if ($scope.modalEtapa != null) {
					$scope.modalEtapa.feito = 1;
					$scope.modalEtapa = null;
				}

			}
		}, (err) => {
			console.log(response);
		});
	};



	$scope.apagaReuniao = function (reuniao) {
		$('.modal').modal('hide');
		var confirm = $mdDialog.confirm()
			.title("Atenção!")
			.textContent('Tem certeza que deseja remover essa reunião?')
			.ok("Sim")
			.cancel("Cancelar");

		$mdDialog.show(confirm).then(function () {
			var dataObj = $.param({
				id_reuniao: reuniao.id_reuniao,
				id_atividade: reuniao.id_atividade,
			});

			$http.post(BASE_URL + 'reuniao/remove_reuniao', dataObj, config).then((data) => {
				if (data.data.result == true) {
					globals.mdToast('success', "Apagado com sucesso!");

					$http.get(BASE_URL + 'leads/get_list_atividades/' + LEADID).then(function (result) {
						$scope.lead.list_atividades = result.data;
					});
				}
			}, (err) => {
				console.log(response);
			});
		}, function () {
			//
		});
	}

	$scope.salvarReuniao = function (ev) {

		if ($scope.newReuniao.data == null || $scope.newReuniao.data.length == 0) {
			globals.mdToast('warning', "Informe uma data!");
			return;
		} else if ($scope.newReuniao.hora == null || $scope.newReuniao.hora.length == 0) {
			globals.mdToast('warning', "Informe uma hora!");
			return;
		}
		else if ($scope.newReuniao.id_funcionario == null || $scope.newReuniao.id_funcionario.length == 0) {
			globals.mdToast('warning', "Informe um funcionário!");
			return;
		} else if ($scope.newReuniao.tipo == null || $scope.newReuniao.tipo.length == 0) {
			globals.mdToast('warning', "Informe um tipo de renião!");
			return;
		}

		$scope.newReuniao.data = moment($scope.newReuniao.data).format("YYYY-MM-DD");
		$scope.newReuniao.hora = moment($scope.newReuniao.hora).format("HH:mm");

		var dataObj = $.param({
			id_lead: LEADID,
			id_reuniao: $scope.newReuniao.id_reuniao,

			data: $scope.newReuniao.data,
			hora: $scope.newReuniao.hora,

			id_funcionario: $scope.newReuniao.id_funcionario,
			tipo: $scope.newReuniao.tipo,
			realizada: $scope.newReuniao.realizada,
			problema: $scope.newReuniao.problema,
			motivo: $scope.newReuniao.motivo,
			observacao: $scope.newReuniao.observacao,

		});

		var posturl = BASE_URL + 'leads/addNewReuniao';
		$http.post(posturl, dataObj, config).then((data) => {
			if (data.data.result == true) {
				$scope.dimiss();
				$scope.newReuniao = [];
				globals.mdToast('success', "Salvo com sucesso!");
				$http.get(BASE_URL + 'leads/get_list_atividades/' + LEADID).then(function (result) {
					$scope.lead.list_atividades = result.data;
				});


				if ($scope.modalEtapa != null) {
					$scope.modalEtapa.feito = 1;
					$scope.modalEtapa = null;
				}

			}
		}, (err) => {
			console.log(response);
		}
		);
	};

	$scope.findAtividadeByid = function (id_atv) {
		if ($scope.leadAtvSelect != null) {
			return $scope.leadAtvSelect.find(a => a.id_atv == id_atv);
		} else {
			return [];
		}

	}

	$scope.getHeight = function (index) {
		var height = 0;
		if (index > 0) {
			height = $('.elementF' + (index - 1)).height() - 40;
		}

		return height;
	}

	$scope.salvarContato = function (ev) {

		if ($scope.newContato.nm_contato == null || $scope.newContato.nm_contato.length == 0) {
			globals.mdToast('warning', "Informe um nome!");
			return;
		} /* else if ($scope.newContato.email == null || $scope.newContato.email.length == 0) {
			globals.mdToast('warning', "Informe um email!");
			return;
		} else if ($scope.newContato.telefone == null || $scope.newContato.telefone.length == 0) {
			globals.mdToast('warning', "Informe um telefone!");
			return;
		}
		*/

		if ($scope.newContato.ctt_principal != null && $scope.newContato.ctt_principal) {
			$scope.newContato.ctt_principal = "1";
		} else {
			$scope.newContato.ctt_principal = "0";
		}

		if ($scope.newContato.is_whatsApp != null && $scope.newContato.is_whatsApp) {
			$scope.newContato.is_whatsApp = "1";
		} else {
			$scope.newContato.is_whatsApp = "0";
		}


		$scope.newContato.dt_aniversario = moment($scope.newContato.dt_aniversario).format("YYYY-MM-DD");

		var dataObj = $.param({
			id_lead: LEADID,
			id_contato: $scope.newContato.id_lead_contato,
			nm_contato: $scope.newContato.nm_contato,
			cargo: $scope.newContato.cargo,
			email: $scope.newContato.email,
			telefone: $scope.newContato.telefone,
			linkedin: $scope.newContato.linkedin,
			observacao: $scope.newContato.observacao,
			dt_aniversario: $scope.newContato.dt_aniversario,
			ctt_principal: $scope.newContato.ctt_principal,
			is_whatsApp: $scope.newContato.is_whatsApp,
		});

		var posturl = BASE_URL + 'leads/addNewContato';
		$http.post(posturl, dataObj, config)
			.then((data) => {
				if (data.data.result == true) {
					$scope.dimiss();
					$scope.atividade = [];
					globals.mdToast('success', "Salvo com sucesso!");
					$http.get(BASE_URL + 'leads/getContatosLead/' + LEADID).then(function (result) {
						$scope.contatos = result.data;
					});
				}
			}, (err) => {
				console.log(response);
			}
			);
	};




	$scope.alterarStatusLead = function (status) {
		var dataObj = $.param({
			id: LEADID,
			status: status,
		});

		var posturl = BASE_URL + 'leads/updateStatus';
		$http.post(posturl, dataObj, config)
			.then((data) => {
				if (data.data.result == true) {
					globals.mdToast('success', "fase atualizada!");
					$scope.lead.status_id = status;

				}
			}, (err) => {
				console.log(response);
			});
	};

	$scope.dimiss = function (ev) {
		$(".modal").modal('hide');
	};


	$scope.converteData = function (data) {
		var split = data.split(' ');
		var formmated = split[0].split('-');
		return formmated[2] + '.' + formmated[1] + '.' + formmated[0] + (split[1] != null ? ' ' + split[1] : '');
	}

	$scope.UpdateTaskAut = function () {
		$scope.saving = true;
		var hora_taskAut = new Date($scope.lead.hora_taskAut).getHours() + ':' + new Date($scope.lead.hora_taskAut).getMinutes();

		var dataObj = $.param({
			name_taskAut: $scope.lead.name_taskAut,
			periodo_taskAut: $scope.lead.periodo_taskAut,
			hora_taskAut: hora_taskAut,
			id_task: $scope.lead.id_task,
			copia_para_taskAut: $scope.lead.copia_para_taskAut,
			copia_para_oculto_taskAut: $scope.lead.copia_para_oculto_taskAut,
			template: $scope.lead.template,
			id_lead: LEADID
		});

		$http.post(BASE_URL + 'EmailsAut/create', dataObj, config)
			.then(function (response) {
				if (response.data.success == true) {
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
				}

				$scope.saving = false;
			}, function (response) {
				$scope.saving = false;
			});
	}

	$http.get(BASE_URL + 'emails/get_email_templates').then(function (Templates) {
		$scope.templates = Templates.data;
	});

	$http.get(BASE_URL + 'EmailsAut/getTaskAutByUser/' + LEADID).then(function (data) {
		$scope.emailsAut = data.data;
		$scope.emailsAutLoader = false;
	});

	$http.get(BASE_URL + 'EmailsAut/getAdicionados/' + LEADID).then(function (data) {
		$scope.adicionadoTask = [];
		data.data.map((a) => {
			$scope.adicionadoTask[a.id_task] = true;
		});
	});


	$scope.getStaff = function (id_staff) {
		var fl = [];
		$.each($scope.staff, (index, a) => { if (a.id == id_staff) { fl = a } });
		return fl
	}


	$scope.getFluxoLead = function () {
		var fl = null;
		$.each($scope.fluxos, (index, a) => {
			if (a.funil != null && a.funil.split(',').includes($scope.lead.id_list)) {
				fl = a;
			}
		});
		return fl
	}
	$scope.checkEtapa = function (etapa) {
		if (etapa.feito == null || etapa.feito == '0') {
			etapa.feito = '1';
		} else {
			etapa.feito = '0';
		}
	}

	$scope.UpdateAdicionadoTask = function (id_task = null) {

		if ($scope.lead.email.length == 0) {
			globals.mdToast('error', "Lead sem email salvo");
			$scope.adicionadoTask[id_task] = false;
			return;
		}

		var dataObj = $.param({
			adicionadoTask: $scope.adicionadoTask,
			id_lead: LEADID
		});

		$http.post(BASE_URL + 'EmailsAut/UpdateAdicionadoTask', dataObj, config)
			.then(function (response) {
				if (response.data.success == true) {
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
				}

				$scope.saving = false;
			}, function (response) {
				$scope.saving = false;
			});
	}

	// Sales Flow Automático - Switch único
	$scope.salesFlowAtivo = false;

	$scope.UpdateSalesFlowAtivo = function (novoValor) {
		console.log('UpdateSalesFlowAtivo CHAMADO - Parâmetro recebido:', novoValor);
		console.log('Tipo do novoValor:', typeof novoValor);
		console.log('Email do lead:', $scope.lead.email);
		
		if (!$scope.lead.email || $scope.lead.email.length == 0) {
			globals.mdToast('error', "Lead sem email salvo");
			$scope.salesFlowAtivo = false;
			return;
		}

		// Verificar se existe fluxo_lead
		if (!$scope.fluxo_lead || !$scope.fluxo_lead.id_fluxo) {
			globals.mdToast('error', "Nenhum Sales Flow definido para este lead");
			$scope.salesFlowAtivo = false;
			return;
		}

		var ativoValue = novoValor ? '1' : '0';
		console.log('UpdateSalesFlowAtivo - Enviando:', {
			id_lead: LEADID,
			id_flow: $scope.fluxo_lead.id_fluxo,
			ativo: ativoValue,
			novoValor: novoValor,
			salesFlowAtivo: $scope.salesFlowAtivo,
			tipoNovoValor: typeof novoValor
		});

		var dataObj = $.param({
			id_lead: LEADID,
			id_flow: $scope.fluxo_lead.id_fluxo,
			ativo: ativoValue
		});

		$http.post(BASE_URL + 'leads/UpdateSalesFlowAtivo', dataObj, config)
			.then(function (response) {
				console.log('UpdateSalesFlowAtivo - Resposta:', response.data);
				if (response.data.success == true) {
					globals.mdToast('success', response.data.message);
				} else {
					globals.mdToast('error', response.data.message);
					$scope.salesFlowAtivo = !$scope.salesFlowAtivo; // Reverter
				}
			}, function (response) {
				console.error('UpdateSalesFlowAtivo - Erro:', response);
				globals.mdToast('error', 'Erro ao atualizar Sales Flow');
				$scope.salesFlowAtivo = !$scope.salesFlowAtivo; // Reverter
			});
	}

	$http.get(BASE_URL + 'settings/get_settingsIa2').then(function (response) {
		$scope.apiwhats_connected = response.data.connected == '1' ? true : false;
	});


	$scope.geraRespostaIa = function () {
		var dataObj = $.param({
			mensagem: $scope.template.message,
		});

		$http.post(BASE_URL + 'leads/gerarResposta', dataObj, config).then(function (response) {
			if (response.data.status == "ok") {
				tinyMCE.get("email_body").setContent(response.data.resposta, { format: 'raw' });
			} else {
				globals.mdToast('error', response.data.message);
			}
		}, function (response) {

		});
	}



	$scope.geraRespostaIaAtividade = function (atividade) {
		$scope.leadRespostaIa = true;
		$scope.respostaIa = [];
		$("#modalRespostaIa").modal("show");

		var dataObj = $.param({
			mensagem: atividade.anotacoes,
		});

		$http.post(BASE_URL + 'leads/gerarResposta', dataObj, config).then(function (response) {
			$scope.leadRespostaIa = false;
			if (response.data.status == "ok") {
				$scope.respostaIa.message = response.data.resposta;
			} else {
				globals.mdToast('error', response.data.message);
			}
		}, function (response) {
			$scope.leadRespostaIa = false;
		});
	}





	$scope.reply = function (atividade) {
		$scope.template = [];
		$scope.template.message = atividade.anotacoes;
		tinyMCE.get("email_body").setContent($scope.replaceVariaveis(atividade.anotacoes), { format: 'raw' });
		$scope.template.email = atividade.email_enviado;
		$("#modalEnviaEmail").modal("show");
	}

	$scope.ShowContato = '';
	$scope.showDialogContatoSocial = function (ShowContato, aut = false) {

		$('.modal').modal('hide')
		$scope.enviaAutomatico = aut;
		var ctt_principal = $scope.contatos.find(a => a.ctt_principal == "1");

		if ($scope.lead.tp_pessoa == '1' || ShowContato == 'email') {
			if (ShowContato == 'email') {
				if ($scope.atividade.email != null && $scope.atividade.email != "0") {
					$scope.template = $scope.templates.find(a => a.id == $scope.atividade.email);
					tinyMCE.get("email_body").setContent($scope.replaceVariaveis($scope.template.message), { format: 'raw' });
					$scope.template.message = $scope.template.message;
				} else {
					$scope.template = [];
					tinyMCE.get("email_body").setContent($scope.replaceVariaveis($scope.atividade.anotacoes), { format: 'raw' });
					$scope.template.message = $scope.template.anotacoes;
				}
				var email = "";

				if (ctt_principal != null && ctt_principal.email != null) {
					email = ctt_principal.email;
				} else if ($scope.lead.email != null && $scope.lead.email.length > 0) {
					email = $scope.lead.email;
				} else {
					var ctt = $scope.contatos.find(a => a.ctt_principal == "1");
					if (ctt.email != null && ctt.length > 0) {
						email = ctt.email;
					}
				}
				$scope.template.email = email;
				$("#modalEnviaEmail").modal("show");
			}
			if (ShowContato == 'linkedin') {
				if ($scope.lead.linkedin == null || $scope.lead.linkedin.length == 0) {
					if (ctt_principal.linkedin != null && ctt_principal.linkedin.length > 0) {
						window.open(ctt_principal.linkedin.startsWith('http') ? ctt_principal.linkedin : 'https://' + ctt_principal.linkedin);
						return;
					} else {
						showToast('Atenção', 'Lead sem linkedin cadastrado', 'warning');
						return;
					}

				}

				window.open($scope.lead.linkedin.startsWith('http') ? $scope.lead.linkedin : 'https://' + $scope.lead.linkedin);
			}
			if (ShowContato == 'whatsapp') {
				var telefone = "";
				var name = "";
				if (ctt_principal != null && ctt_principal.telefone != null) {
					telefone = ctt_principal.telefone;
					name = ctt_principal.nm_contato;
				} else if ($scope.lead.phone != null && $scope.lead.phone.length > 0) {
					telefone = $scope.lead.phone;
					name = lead.name != null ? lead.name : lead.company;
				}

				if ($scope.apiwhats_connected) {
					window.open(BASE_URL + 'whatsapp?number=' + telefone + '&name=' + name);
				} else {
					window.open('https://wa.me/+55' + telefone);
				}

			}
		} else {
			$scope.ShowContato = ShowContato;
			$("#modalContatoSocial").modal("show");
		}
	}

	$scope.enviaEmail = function () {
		var data = tinyMCE.get("email_body").getContent({ format: 'raw' });
		var ctt_principal = $scope.contatos.find(a => a.ctt_principal == "1");
		var company_name = $scope.lead.name != '' ? $scope.lead.name : $scope.lead.company;

		var dataObj = $.param({
			subject: $scope.template.subject,
			from_name: $scope.template.from_name,
			relation: $scope.template.relation,
			company_name: company_name,
			customer: ctt_principal != null && ctt_principal.nm_contato != null ? ctt_principal.nm_contato : company_name,
			message: data.replace(/&nbsp;/g, ' ').replace(/;/g, '').replace(/&nbsp/g, ' '),
			status: $scope.template.status,
			attachment: $scope.template.attachment,
			cc: $scope.template.cc,
			cco: $scope.template.cco,
			id_lead: LEADID,
			email: $scope.template.email
		});


		$http.post(BASE_URL + 'emails/sendEmail', dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					showToast(NTFTITLE, response.data.message, 'success');
					$('#modalEnviaEmail').modal('hide');
					$('#modalAddAtividade').modal('show');

					$http.get(BASE_URL + 'leads/get_list_atividades/' + LEADID).then(function (result) {
						$scope.lead.list_atividades = result.data;
					});

					$scope.salvarAtividade();

				} else {
					showToast(NTFTITLE, response.data.message, 'warning');
				}
				$scope.saving = false;
			}, function () {
				$scope.saving = false;
			}
		);
	}
	$scope.replaceNumero = function (text = '') {
		text = text.replaceAll('-', '');
		text = text.replaceAll(' ', '');
		text = text.replaceAll('(', '');
		text = text.replaceAll(')', '');
		return text;
	}

	$scope.replaceVariaveis = function (text = '') {
		var ctt_principal = $scope.contatos.find(a => a.ctt_principal == "1");

		var nm_principal = "";
		if (ctt_principal != null && Array.isArray(ctt_principal) && ctt_principal[0].length > 0) {
			nm_principal = ctt_principal[0].nm_contato;
		} else if (ctt_principal != null && ctt_principal.nm_contato != null) {
			nm_principal = ctt_principal.nm_contato;

		}

		text = text.replaceAll('{nome}', $scope.lead.name != null && $scope.lead.name.length > 0 ? $scope.lead.name : nm_principal);
		text = text.replaceAll('{empresa}', $scope.lead.company);
		text = text.replaceAll('{customer}', nm_principal);
		return text;
	}

	$scope.openInfo = function (text) {
		$('#modalInfo').modal('show');
		$('#modalInfoContent').html(text);
	}

	$scope.addVar = function (text) {
		tinyMCE.get("email_body").insertContent(text);

	}






}



CiuisCRM.controller('Leads_Controller', Leads_Controller);
CiuisCRM.controller('Lead_Controller', Lead_Controller);

CiuisCRM.directive("myDirective", function () {
	return {

		link: function (scope, element, attrs) {

			scope.etapaPai = scope.etapa;
		},

		template: `
		<div class="element-filho" ng-repeat="(key2, etapa) in  etapa.etapas | filter: { id_etapa_pai: etapa.id_etapa}">
			<div class="icon-element">
			<button type="button" class="btn_edit6" style = "background: transparent;">
				<i ng-if = "etapa.feito != '1'" style = "color: #979797;font-size: 20px;" class="far fa-check-circle"></i>
				<i ng-if = "etapa.feito == '1'" style = "font-size: 20px;" class="fas fa-check-circle"></i>
				<img ng-if="(etapaPai.feito == '1') && etapa.feito != '1'" style="width: 40px;position: absolute;right: 22px;top: 66px;" src="{{base_url + 'assets/img/click-sales-flow2.gif'}}">
			</button>
			
			<p class="inf-num" style = "float: unset;margin: 0 auto;width: max-content;padding: 2px 4px 0px 4px;min-width: 25px;">
				<i class="fa fa-info-circle btn_info" title = "Ver instruções" ng-click="openInfo(etapa.descricao)"></i> {{etapa.numero}}
			</p>
			<div style="background-image: url('{{base_url + etapa.atividade.atv_ft}}');" ng-click="showDialogAddAtividade(null, etapa)"></div>
			</div>
			<div class="element-line"></div>
			
			<div style="display: flex;" ng-if = "etapa.etapas.length > 0">
				<div style = "display: flex;" class = 'element_vert' etapa="etapa" my-directive></div>
			</div>
			
		</div>`
	};
});