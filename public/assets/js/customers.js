function Customers_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, fileUpload, $q) {
	"use strict";
	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'customer').then(function (custom_fields) {
		$scope.all_custom_fields = custom_fields.data;
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	});

	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});
	$scope.customer = [];
	$scope.customer.created = moment($scope.customer.created).format("YYYY-MM-DD HH:mm:ss");

	$scope.customersLoader = true;
	$scope.isContact = true;
	$scope.toggleFilter = buildToggler('ContentFilter');
	$scope.Create = buildToggler('Create');
	$scope.ImportCustomersNav = buildToggler('ImportCustomersNav');
	$scope.CreateGroup = buildToggler('CreateGroup');
	function buildToggler(navID) {
		return function () {
			console.log(navID);
			$mdSidenav(navID).toggle();
		};
	}
	$scope.close = function () {
		$mdSidenav('ContentFilter').close();
		$mdSidenav('Create').close();
		$mdSidenav('ImportCustomersNav').close();
		$mdSidenav('CreateGroup').close();
	};
	$scope.importing = false;
	$scope.importerror = false;
	$scope.importCustomer = function () {
		$scope.importing = true;
		var file = $scope.customer_file;
		var uploadUrl = BASE_URL + 'customers/customersimport';
		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {
			if ((response.success == true) && (!response.errors)) {
				//showToast(NTFTITLE, response.message, ' success');
				globals.mdToast('success', response.message);
				$mdSidenav('ImportCustomersNav').close();
			} else if ((response.success == false) && (response.errors)) {
				$scope.importerror = true;
				$scope.errors = response.errors;
				console.log(response.errors);
				globals.mdToast('error', response.message);
			} else {
				$scope.importerror = true;
				$scope.errors = response.errors;
				globals.mdToast('error', response.message);
				console.log(response.errors);
			}
			$http.get(BASE_URL + 'api/customers').then(function (Customers) {
				$scope.customers = Customers.data;
			});
			$scope.customerFiles = true;
			$scope.importing = false;
		});
	};

	$scope.getAtivos = function () {
		if ($scope.customers != null) {
			return $scope.customers.filter(a => a.customer_status_id == 1).length;
		}
		return 0;
	};

	$scope.getDesativos = function () {
		if ($scope.customers != null) {
			return $scope.customers.filter(a => a.customer_status_id == 0).length;
		}
		return 0;
	};

	$http.get(BASE_URL + 'customer_sucess/get_funils').then(function (returns) {
		$scope.funilsCustomers = returns.data;
	});

	globals.get_countries();
	$scope.getStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.states = States.data;
		});
	};
	$scope.getBillingStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.billingStates = States.data;
		});
	};
	$scope.getShippingStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.shippingStates = States.data;
		});
	};
	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;
	});
	var gdata;
	$http.get(BASE_URL + 'customers/groups').then(function (Data) {
		gdata = Data.data;
		var data = [];
		for (var i = 0; i < gdata.length; i++) {
			data.push([gdata[i].name, parseInt(gdata[i].y)]);
		}
		Highcharts.chart('container', {
			chart: {
				polar: true,
				plotBackgroundColor: '#f3f3f3',
				plotBorderWidth: 0,
				plotShadow: false
			},
			title: {
				//text: 'Customer<br>Group',
				text: lang.customer + '<br>' + lang.group,
				align: 'center',
				verticalAlign: 'middle',
				y: -18
			},
			tooltip: {
				pointFormat: '<b>{point.y}</b>'
			},
			credits: {
				enabled: false
			},
			plotOptions: {
				pie: {
					dataLabels: {
						enabled: true,
						distance: -50,
						style: {
							fontWeight: 'bold',
							color: 'white'
						}
					},
					center: ['50%', '47%'],
					size: '100%'
				}
			},
			series: [
				{
					type: 'pie',
					name: '',
					innerSize: '42%',
					data: data
				}
			],
			exporting: {
				buttons: {
					contextButton: {
						menuItems: ['downloadPNG', 'downloadSVG', 'downloadPDF', 'downloadCSV', 'downloadXLS']
					}
				}
			}
		});
		function redrawchart() {
			var chart = $('#container').highcharts();
			var w = $('#container').closest(".wrapper").width();
			chart.setSize(
				w, w * (3 / 4), false
			);
		}
		$(window).resize(redrawchart);
		redrawchart();
	});
	var deferred = $q.defer();
	$scope.customer_list = {
		order: '',
		limit: 5,
		page: 1
	};
	$scope.promise = deferred.promise;
	$http.get(BASE_URL + 'api/customers').then(function (Customers) {
		$scope.customers = Customers.data;
		deferred.resolve();
		$scope.limitOptions = [5, 10, 15, 20];
		if ($scope.customers.length > 20) {
			$scope.limitOptions = [5, 10, 15, 20, $scope.customers.length];
		}
		$scope.customersLoader = false;
		$http.get(BASE_URL + 'customers/get_customer_groups').then(function (Groups) {
			$scope.group = Groups.data;
			$scope.NewGroup = function () {
				globals.createDialog(lang.new + ' ' + lang.customer + ' ' + lang.group, $scope.lang.type_group_name, lang.group + ' ' + lang.name, '', lang.add, lang.cancel, 'customers/add_group', function (response) {
					if (response.success == true) {
						globals.mdToast('success', response.message);
					} else {
						globals.mdToast('error', response.message);
					}
					$http.get(BASE_URL + 'customers/get_customer_groups').then(function (Groups) {
						$scope.group = Groups.data;
					});
				});
			};
			$scope.EditGroup = function (id, name, event) {
				globals.editDialog(lang.edit + ' ' + lang.group + ' ' + lang.name, $scope.lang.type_group_name, lang.group + ' ' + lang.name, name, event, lang.save, lang.cancel, 'customers/update_group/' + id, function (response) {
					if (response.success == true) {
						globals.mdToast('success', response.message);
						$http.get(BASE_URL + 'customers/get_customer_groups').then(function (Groups) {
							$scope.group = Groups.data;
						});
					} else {
						globals.mdToast('error', response.message);
					}
				});
			};
			$scope.DeleteCustomerGroup = function (index) {
				var name = $scope.group[index];
				globals.deleteDialog(lang.attention, $scope.lang.delete_meesage + ' ' + $scope.lang.group, name.id, lang.doIt, lang.cancel, 'customers/remove_group/' + name.id, function (response) {
					if (response.success == true) {
						globals.mdToast('success', response.message);
						$http.get(BASE_URL + 'customers/get_customer_groups').then(function (Groups) {
							$scope.group = Groups.data;
						});
					} else {
						globals.mdToast('error', response.message);
					}
				});
			};
		});
		$scope.GoCustomer = function (index) {
			var customer = $scope.customers[index];
			window.location.href = BASE_URL + 'customers/customer/' + customer.id;
		};
		$scope.isIndividual = false;
		$scope.saving = false;

		$scope.openSearch = function () {
			$('.search-wrapper').toggleClass('active');
		}


		$scope.AddCustomer = function () {

			if ($scope.customer.created) {
				$scope.customer.created = moment($scope.customer.created).format("YYYY-MM-DD HH:mm:ss");
			}

			if ($scope.customer.dt_inactive) {
				$scope.customer.dt_inactive = moment($scope.customer.dt_inactive).format("YYYY-MM-DD HH:mm:ss");
			}
			if ($scope.customer.dt_nascimento) {
				$scope.customer.dt_nascimento = moment($scope.customer.dt_nascimento).format("YYYY-MM-DD");
			}

			$scope.saving = true;
			$scope.tempArr = [];
			if ($scope.isContact == true) {
				$scope.isContact = 1;
			} else {
				$scope.isContact = 0;
			}
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

			var dataObj = $.param({
				company: $scope.customer.company,
				namesurname: $scope.customer.namesurname,
				groupid: $scope.customer.group_id,
				taxoffice: $scope.customer.taxoffice,
				taxnumber: $scope.customer.taxnumber,
				ssn: $scope.customer.ssn,
				executive: $scope.customer.executive,
				address: $scope.customer.address,
				zipcode: $scope.customer.zipcode,
				country_id: $scope.customer.country_id,
				state_id: $scope.customer.state_id,
				closer: $scope.customer.closer,
				customer_sucess: $scope.customer.customer_sucess,
				city: $scope.customer.city,
				town: $scope.customer.town,
				phone: $scope.customer.phone,
				fax: $scope.customer.fax,
				email: $scope.customer.email,
				web: $scope.customer.web,
				risk: $scope.customer.risk,
				billing_street: $scope.customer.billing_street,
				billing_city: $scope.customer.billing_city,
				billing_state_id: $scope.customer.billing_state_id,
				billing_zip: $scope.customer.billing_zip,
				billing_country: $scope.customer.billing_country,
				shipping_street: $scope.customer.shipping_street,
				shipping_city: $scope.customer.shipping_city,
				shipping_state_id: $scope.customer.shipping_state_id,
				shipping_zip: $scope.customer.shipping_zip,
				shipping_country: $scope.customer.shipping_country,
				type: $scope.isIndividual,
				custom_fields: $scope.tempArr,
				default_payment_method: $scope.customer.default_payment_method,
				contact: $scope.isContact,
				dt_inactive: $scope.customer.dt_inactive,
				created: $scope.customer.created,
				cpf: $scope.customer.cpf,

				is_whatsApp: $scope.customer.is_whatsApp ? '1' : '0',
				instagram: $scope.customer.instagram,
				porte: $scope.customer.porte,
				facebook: $scope.customer.facebook,
				linkedin: $scope.customer.linkedin,
				dt_nascimento: $scope.customer.dt_nascimento,
				setor_atividade: $scope.customer.setor_atividade,
				source_id: $scope.customer.source_id,
				assigned_id: $scope.customer.assigned_id,
				tp_pessoa: $scope.customer.tp_pessoa,
				description: $scope.customer.description,
				cnpj: $scope.customer.cnpj,
				etapa: $scope.customer.etapa,
			});

			var posturl = BASE_URL + 'customers/create';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						if (response.data.success == true) {
							if (response.data.id) {
								$http.get(BASE_URL + 'api/customers').then(function (Customers) {
									$scope.customers = Customers.data;
								});
								globals.mdToast('success', response.data.message);
								$mdSidenav('Create').close();
							} else {
								$scope.saving = false;
								globals.mdToast('error', response.data.message);
							}
						} else {
							$scope.saving = false;
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						$scope.saving = false;
						$http.get(BASE_URL + 'api/customers').then(function (Customers) {
							$scope.customers = Customers.data;
						});
					}
				);
		};
		$scope.SameAsCustomerAddress = function () {
			$scope.customer.billing_street = $scope.customer.address;
			$scope.customer.billing_city = $scope.customer.city;
			$scope.customer.billing_state = $scope.customer.state_ud;
			$scope.customer.billing_zip = $scope.customer.zipcode;
			$scope.customer.billing_country = $scope.customer.country_id;
		};
		$scope.SameAsBillingAddress = function () {
			$scope.customer.shipping_street = $scope.customer.billing_street;
			$scope.customer.shipping_city = $scope.customer.billing_city;
			$scope.customer.shipping_state = $scope.customer.billing_state_id;
			$scope.customer.shipping_zip = $scope.customer.billing_zip;
			$scope.customer.shipping_country = $scope.customer.billing_country;
		};
		$scope.filter = {};
		$scope.getOptionsFor = function (propName) {
			return ($scope.customers || []).map(function (item) {
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
		$scope.updateDropdown = function (_prop) {
			var _opt = this.filter_select,
				_optList = this.getOptionsFor(_prop),
				len = _optList.length;
			console.log(_optList);
			if (_opt == 'all') {
				for (var j = 0; j < len; j++) {
					$scope.filter[_prop][_optList[j]] = true;
				}
			} else {
				for (var j = 0; j < len; j++) {
					$scope.filter[_prop][_optList[j]] = false;
				}
				$scope.filter[_prop][_opt] = true;
			}
		};
		// Filtered Datas
		$scope.search = {
			name: '',
			phone: '',
			email: '',
		};
	});
}


function Customer_Controller($scope, $http, $filter, $mdSidenav, $mdDialog) {
	"use strict";
	$scope.isScreenSize = $(window).width();
	$scope.ReminderForm = buildToggler('ReminderForm');
	$scope.NewContact = buildToggler('NewContact');
	$scope.Update = buildToggler('Update');
	$('.update-view').hide();
	$scope.customersLoader = true;
	$scope.visibleFlag = true;
	$scope.filtroAno = '';
	$scope.AnosAnteriores = [];
	$scope.reuniaoModal = [];
	$scope.base_url = BASE_URL;
	var customer_annual_sales_chart = null;

	var statusCustumerOrig = '';
	var data = new Date();
	var anoAtual = data.getFullYear();
	for (var i = 0; i <= 4; i++) {
		$scope.AnosAnteriores.push(anoAtual - i);
	}
	$scope.filtroAno = anoAtual;

	$http.get(BASE_URL + 'customers/getListSelectCustomers').then(function (data) {
		$scope.leadAtvSelect = data.data;
	});

	$http.get(BASE_URL + 'customers/get_customer_groups').then(function (Groups) {
		$scope.group = Groups.data;
	});

	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});

	$scope.replaceNumero = function (text = '') {
		text = text.replaceAll('-', '');
		text = text.replaceAll(' ', '');
		text = text.replaceAll('(', '');
		text = text.replaceAll(')', '');
		return text;
	}

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

					$http.get(BASE_URL + 'customers/get_list_atividades/' + CUSTOMERID).then(function (result) {
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

	$scope.salva_reuniao = function (id_reuniao, reuniao) {

		$('#modalDetalhesReuniao').modal('hide');

		var dataObj = $.param({
			id_reuniao,
			realizada: $scope.reuniaoModal.realizada,
			problema: $scope.reuniaoModal.problema,
			motivo: $scope.reuniaoModal.motivo
		});

		$http.post(BASE_URL + 'reuniao/salva_reuniao', dataObj, config).then(function (response) {
			globals.mdToast('success', 'Alterado com sucesso!');
		});
	};


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
			id_customer: CUSTOMERID,
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

		var posturl = BASE_URL + 'customers/addNewReuniao';
		$http.post(posturl, dataObj, config).then((data) => {
			if (data.data.result == true) {
				$scope.dimiss();
				$scope.newReuniao = [];
				globals.mdToast('success', "Salvo com sucesso!");
				$http.get(BASE_URL + 'customers/get_list_atividades/' + CUSTOMERID).then(function (result) {
					$scope.customer.list_atividades = result.data;
				});

			}
		}, (err) => {
			console.log(response);
		}
		);
	};

	$scope.alterarStatusCustomer = function (status) {
		var dataObj = $.param({
			customer_id: CUSTOMERID,
			status_id: status,
		});

		var posturl = BASE_URL + 'Customer_sucess/move_customer';
		$http.post(posturl, dataObj, config)
			.then((data) => {
				if (data.data.sucess == 200) {
					globals.mdToast('success', "grupo atualizado!");
					$scope.customer.group_id = status;
				}
			}, (err) => {
				console.log(response);
			});
	};

	$scope.salvarAtividade = function (sem_aviso = false) {
		if (($scope.customer.ultimoRetorno == "" || $scope.customer.ultimoRetorno == null || $scope.customer.ultimoRetorno == '0000-00-00' ||
			($scope.customer.ultimoRetorno != "0000-00-00" && $scope.customer.ultimoRetorno != "" && $scope.customer.ultimoRetorno != null && new Date($scope.customer.ultimoRetorno + ' 23:59:59') < new Date()))
			&& $scope.atividade.retorno == null && !sem_aviso
		) {
			$scope.dimiss();
			Swal.fire({
				title: "Atenção",
				text: "Este cliente ainda não tem um agendamento de retorno. Para inserir basta colocar a data na atividade!",
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

		if ($scope.atividade.atividade == null || $scope.atividade.atividade.length == 0) {
			globals.mdToast('warning', "Informe uma atividade!");
			return;
		} else if ($scope.atividade.data == null || $scope.atividade.data.length == 0) {
			globals.mdToast('warning', "Informe uma data!");
			return;
		} else if ($scope.atividade.horario == null || $scope.atividade.horario.length == 0) {
			globals.mdToast('warning', "Informe uma horário!");
			return;
		} else if ($scope.atividade.anotacoes == null || $scope.atividade.anotacoes.length == 0) {
			globals.mdToast('warning', "Informe uma anotação!");
			return;
		}
		// else if ($scope.atividade.retorno == null || $scope.atividade.retorno.length == 0) {
		//	globals.mdToast('warning', "Informe uma retorno!");
		//	return;
		//}

		$scope.atividade.data = moment($scope.atividade.data).format("YYYY-MM-DD");
		$scope.atividade.retorno = moment($scope.atividade.retorno).format("YYYY-MM-DD");
		$scope.atividade.horario = moment($scope.atividade.horario).format("HH:mm:ss");

		var dataObj = $.param({
			id_customer: CUSTOMERID,
			id_atividade: $scope.atividade.id_atividade,
			atividade: $scope.atividade.atividade,
			data: $scope.atividade.data,
			horario: $scope.atividade.horario,
			duracao: $scope.atividade.duracao,
			anotacoes: $scope.atividade.anotacoes,
			retorno: $scope.atividade.retorno
		});

		$http.post(BASE_URL + 'customers/addNewAtividade', dataObj, config).then((data) => {
			$scope.customer.ultimoRetorno = $scope.atividade.retorno;

			if (data.data.result == true) {
				$scope.dimiss();
				$scope.atividade = [];
				globals.mdToast('success', "Salvo com sucesso!");
				$http.get(BASE_URL + 'customers/get_list_atividades/' + CUSTOMERID).then(function (result) {
					$scope.customer.list_atividades = result.data;
				});
			}
		}, (err) => {
			console.log(response);
		}
		);
	};

	$scope.showDialogReuniao = function (reuniao, id_atividade = null) {
		$scope.reuniaoModal = reuniao;

		$scope.reuniaoModal.realizada = reuniao.reuniao.realizada;
		$scope.reuniaoModal.problema = reuniao.reuniao.problema;
		$scope.reuniaoModal.motivo = reuniao.reuniao.motivo;
		$scope.reuniaoModal.id_atividade = id_atividade;

		$("#modalDetalhesReuniao").modal('show');
	};

	$scope.showDialogAddAtividade = function (atv = null) {
		$scope.atividade = [];
		if (atv != null) {

			if (atv.is_reuniao == "1") {
				$scope.showDialogReuniao(atv, atv.id_atividade);
				return;
			}

			var atvTemp = JSON.parse(JSON.stringify(atv));
			atvTemp.data = new Date(atvTemp.data + ' 00:00');
			atvTemp.retorno = new Date(atvTemp.retorno + ' 00:00');
			atvTemp.horario = new Date(atvTemp.data + ' 00:00').setHours(atv.horario.split(':')[0], atv.horario.split(':')[1]);
			atvTemp.horario = new Date(atvTemp.horario);
			atvTemp.atividade = atvTemp.atividade_customer;
			$scope.atividade = atvTemp;


			setTimeout(function () {
				$("#horarioAtv").val(atv.horario);
			}, 100);
		} else {
			$scope.atividade.data = new Date();
			$scope.atividade.horario = new Date();

			setTimeout(function () {
				$("#horarioAtv").val((new Date().getHours() + 1) + ':' + new Date().getMinutes());
			}, 100);
		}

		$("#modalAddAtividade").modal("show");
	}

	$scope.ShowContato = '';
	$scope.showDialogContatoSocial = function (ShowContato) {
		$('.modal').modal('hide')

		if ($scope.customer.tp_pessoa == '1') {
			if (ShowContato == 'email') {
				window.open(BASE_URL + 'emails/send_email?email=' + $scope.customer.email + '&customer=' + $scope.customer.name +
					'&company_name=' + $scope.customer.name + '&customer=' + $scope.customer.id);
			}
			if (ShowContato == 'linkedin') {
				window.open($scope.customer.linkedin.startsWith('http') ? $scope.customer.linkedin : 'https://' + $scope.customer.linkedin);
			}
			if (ShowContato == 'whatsapp') {
				window.open('https://wa.me/+55' + $scope.customer.phone);
			}
			if (ShowContato == 'instagram') {
				window.open($scope.customer.instagram);
			}
		} else {
			$scope.ShowContato = ShowContato;
			$("#modalContatoSocial").modal("show");
		}
	}

	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};
	}
	$scope.close = function () {
		$mdSidenav('ReminderForm').close();
		$mdSidenav('NewContact').close();
		$mdSidenav('Update').close();
	};
	globals.get_countries();
	$scope.getStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.states = States.data;
		});
	};
	$scope.getBillingStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.billingStates = States.data;
		});
	};
	$scope.getShippingStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.shippingStates = States.data;
		});
	};
	$scope.SameAsCustomerAddress = function () {
		$scope.customer.billing_street = $scope.customer.address;
		$scope.customer.billing_city = $scope.customer.city;
		$scope.customer.billing_state = $scope.customer.state_id;
		$scope.customer.billing_zip = $scope.customer.zipcode;
		$scope.customer.billing_country = $scope.customer.country_id;
	};
	$scope.SameAsBillingAddress = function () {
		$scope.customer.shipping_street = $scope.customer.billing_street;
		$scope.customer.shipping_city = $scope.customer.billing_city;
		$scope.customer.shipping_state = $scope.customer.billing_state_id;
		$scope.customer.shipping_zip = $scope.customer.billing_zip;
		$scope.customer.shipping_country = $scope.customer.billing_country;
	};
	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'customer/' + CUSTOMERID).then(function (custom_fields) {
		$scope.custom_fields = custom_fields.data;
	});

	$scope.getDadosResume = function () {
		$http.get(BASE_URL + 'customers/get_customer/' + CUSTOMERID + '/' + $scope.filtroAno).then(function (Customer) {
			statusCustumerOrig = Customer.data.customer_status_id ? '1' : '0';
			$scope.customer = Customer.data;

			$http.get(BASE_URL + 'customer_sucess/get_funils').then(function (returns) {
				$scope.funilsCustomers = returns.data;

				$.each($scope.funilsCustomers, (index, funil) => {
					funil.etapas.map((status) => {
						if (status.id == $scope.customer.etapa) {
							$scope.customer.funil = index;
						}
					});
				});
			});

			$http.get(BASE_URL + 'api/contact/' + CUSTOMERID).then(function (contact) {
				$scope.contacts = contact.data;
			});
			$scope.isActive = $scope.customer.customer_isActive;
			$scope.getStates($scope.customer.country_id);
			$scope.getBillingStates($scope.customer.billing_country);
			$scope.getShippingStates($scope.customer.shipping_country);
			$scope.customersLoader = false;
			$http.get(BASE_URL + 'customers/get_customer_groups').then(function (Groups) {
				$scope.groups = Groups.data;
			});



			if (customer_annual_sales_chart == null) {
				customer_annual_sales_chart = new Chart($('#customer_annual_sales_chart'), {
					type: 'bar',
					data: Customer.data.chart_data,
					options: {
						legend: {
							display: false
						},
						responsive: true,
						maintainAspectRatio: false
					}
				});
			} else {
				customer_annual_sales_chart.data = Customer.data.chart_data;
				customer_annual_sales_chart.update();
			}


		});
	}
	$scope.getDadosResume();

	$scope.dimiss = function (ev) {
		$(".modal").modal('hide');
	};

	$scope.converteData = function (data) {
		var split = data.split(' ');
		var formmated = split[0].split('-');
		return formmated[2] + '/' + formmated[1] + '/' + formmated[0] + (split[1] != null ? ' ' + split[1] : '');
	}

	$http.get(BASE_URL + 'customers/get_invoices/' + CUSTOMERID).then(function (Invoices) {
		$scope.all_invoices = Invoices.data;
		$scope.invoices = $filter('filter')($scope.all_invoices, {
			customer_id: CUSTOMERID,
		});
	});
	$scope.GoInvoice = function (index) {
		var invoice = $scope.invoices[index];
		window.location.href = BASE_URL + 'invoices/invoice/' + invoice.id;
	};

	$http.get(BASE_URL + 'api/proposals').then(function (Proposals) {
		$scope.all_proposals = Proposals.data;
		$scope.proposals = $filter('filter')($scope.all_proposals, {
			relation_type: 'customer',
			relation: CUSTOMERID,
		});
	});
	$scope.GoProposal = function (index) {
		var proposal = $scope.proposals[index];
		window.location.href = BASE_URL + 'proposals/proposal/' + proposal.id;
	};
	$http.get(BASE_URL + 'api/projects').then(function (Projects) {
		$scope.all_projects = Projects.data;
		$scope.projects = $filter('filter')($scope.all_projects, {
			customer_id: CUSTOMERID,
		});
	});
	$scope.GoProject = function (index) {
		var project = $scope.projects[index];
		window.location.href = BASE_URL + 'projects/project/' + project.id;
	};
	$http.get(BASE_URL + 'api/tickets').then(function (Tickets) {
		$scope.all_tickets = Tickets.data;
		$scope.tickets = $filter('filter')($scope.all_tickets, {
			customer_id: CUSTOMERID,
		});
	});
	$scope.GoTicket = function (index) {
		var ticket = $scope.tickets[index];
		window.location.href = BASE_URL + 'tickets/ticket/' + ticket.id;
	};
	$scope.savingCustomer = false;


	$scope.UpdateCustomer = function () {
		$scope.savingCustomer = true;
		$scope.tempArr = [];
		var customer_status;
		if ($scope.customer.customer_status_id == true) {
			customer_status = '1';
		} else {
			customer_status = '0';
		}

		if (statusCustumerOrig != customer_status && customer_status == '0') {
			var confirm = $mdDialog.confirm()
				.title('Atenção!')
				.textContent('Você deseja excluir as faturas recorrentes deste cliente?')
				.ok('Sim, excluir!')
				.cancel('Não, manter!');

			$mdDialog.show(confirm).then(function () {
				$scope.customer.apgFat = '1';
				statusCustumerOrig = '0';
				$scope.UpdateCustomer();
			}, function () {
				$scope.customer.apgFat = '0';
				statusCustumerOrig = '0';
				$scope.UpdateCustomer();
			});

			return;
		} else {
			statusCustumerOrig = customer_status;
			$scope.customer.apgFat = '0';
		}

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

		if ($scope.customer.created) {
			$scope.customer.created = moment($scope.customer.created).format("YYYY-MM-DD HH:mm:ss");
		}

		if ($scope.customer.dt_inactive) {
			$scope.customer.dt_inactive = moment($scope.customer.dt_inactive).format("YYYY-MM-DD HH:mm:ss");
		}

		if ($scope.customer.dt_nascimento) {
			$scope.customer.dt_nascimento = moment($scope.customer.dt_nascimento).format("YYYY-MM-DD");
		}

		var dataObj = $.param({
			company: $scope.customer.company,
			group_id: $scope.customer.group_id,
			taxoffice: $scope.customer.taxoffice,
			taxnumber: $scope.customer.taxnumber,
			ssn: $scope.customer.ssn,
			executive: $scope.customer.executive,
			address: $scope.customer.address,
			zipcode: $scope.customer.zipcode,
			country_id: $scope.customer.country_id,
			state_id: $scope.customer.state_id,
			closer: $scope.customer.closer,
			customer_sucess: $scope.customer.customer_sucess,
			city: $scope.customer.city,
			town: $scope.customer.town,
			phone: $scope.customer.phone,
			fax: $scope.customer.fax,
			email: $scope.customer.email,
			web: $scope.customer.web,
			risk: $scope.customer.risk,
			billing_street: $scope.customer.billing_street,
			billing_city: $scope.customer.billing_city,
			billing_state_id: $scope.customer.billing_state_id,
			billing_zip: $scope.customer.billing_zip,
			billing_country: $scope.customer.billing_country,
			shipping_street: $scope.customer.shipping_street,
			shipping_city: $scope.customer.shipping_city,
			shipping_state_id: $scope.customer.shipping_state_id,
			shipping_zip: $scope.customer.shipping_zip,
			shipping_country: $scope.customer.shipping_country,
			custom_fields: $scope.tempArr,
			default_payment_method: $scope.customer.default_payment_method,
			type: $scope.customer.isIndividual,
			status_id: customer_status,
			dt_inactive: $scope.customer.dt_inactive,
			created: $scope.customer.created,
			apgFat: $scope.customer.apgFat,
			cpf: $scope.customer.cpf,

			is_whatsApp: $scope.customer.is_whatsApp ? '1' : '0',
			instagram: $scope.customer.instagram,
			porte: $scope.customer.porte,
			facebook: $scope.customer.facebook,
			linkedin: $scope.customer.linkedin,
			dt_nascimento: $scope.customer.dt_nascimento,
			setor_atividade: $scope.customer.setor_atividade,
			source_id: $scope.customer.source_id,
			assigned_id: $scope.customer.assigned_id,
			tp_pessoa: $scope.customer.tp_pessoa,
			description: $scope.customer.description,
			cnpj: $scope.customer.cnpj,
			etapa: $scope.customer.etapa,
		});
		var posturl = BASE_URL + 'customers/customer/' + CUSTOMERID;
		$http.post(posturl, dataObj, config)
			.then(
				function (response) {
					$scope.savingCustomer = false;
					if (response.data.success == true) {
						$mdSidenav('Update').close();
						$http.get(BASE_URL + 'customers/get_customer/' + CUSTOMERID).then(function (Customer) {
							$scope.customer = Customer.data;

							$http.get(BASE_URL + 'customer_sucess/get_funils').then(function (returns) {
								$scope.funilsCustomers = returns.data;

								$.each($scope.funilsCustomers, (index, funil) => {
									funil.etapas.map((status) => {
										if (status.id == $scope.customer.etapa) {
											$scope.customer.funil = index;
										}
									});
								});
							});

						});
						globals.mdToast('success', response.data.message);
					} else {
						globals.mdToast('error', response.data.message);
					}
				},
				function (response) {
					$scope.savingCustomer = false;
				}
			);
	};

	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;
	});
	$scope.Delete = function (index) {
		globals.deleteDialog(lang.attention, lang.delete_customer, CUSTOMERID, lang.doIt, lang.cancel, 'customers/remove/' + CUSTOMERID, function (response) {
			if (response.success == true) {
				window.location.href = BASE_URL + 'customers';
			} else {
				globals.mdToast('error', response.message);
			}
		});
	};
	// $http.get(BASE_URL + 'api/contacts').then(function (contacts) {
	// 	$scope.all_contacts = contactscon.data;
	// 	$scope.contacts = $filter('filter')($scope.all_contacts, {
	// 		customer_id: CUSTOMERID,
	// 	});
	// });
	$scope.isPrimary = true;
	$scope.isAdmin = false;
	$scope.ContactDetail = function (index) {
		console.log('1')
		var contact = $scope.contacts[index];
		$mdDialog.show({
			contentElement: '#ContactModal-' + contact.id,
			parent: angular.element(document.body),
			targetEvent: index,
			clickOutsideToClose: true
		});
		$scope.UpdateContactPrivilege = function (id, value, privilege_id) {
			$http.post(BASE_URL + 'customers/update_contact_privilege/' + id + '/' + value + '/' + privilege_id)
				.then(
					function (response) {
						if (response.data.success == false) {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						console.log(response);
					}
				);
		};
	};
	$scope.Contact = function () {
		if ($scope.newcontact.dt_nascimento) {
			$scope.newcontact.dt_nascimento = moment($scope.newcontact.dt_nascimento).format("YYYY-MM-DD");
		}
		$scope.saving = true;
		if (!$scope.newcontact) {
			var dataObj = $.param({
				name: '',
				surname: '',
				phone: '',
				extension: '',
				mobile: '',
				email: '',
				address: '',
				skype: '',
				linkedin: '',
				position: '',
				customer: CUSTOMERID,
				isPrimary: $scope.isPrimary,
				isAdmin: $scope.isAdmin,
				facebook: '',
				dt_nascimento: '',
			});
		} else {
			var dataObj = $.param({
				name: $scope.newcontact.name,
				surname: $scope.newcontact.surname,
				phone: $scope.newcontact.phone,
				extension: $scope.newcontact.extension,
				mobile: $scope.newcontact.mobile,
				email: $scope.newcontact.email,
				address: $scope.newcontact.address,
				skype: $scope.newcontact.skype,
				linkedin: $scope.newcontact.linkedin,
				position: $scope.newcontact.position,
				instagram: $scope.newcontact.instagram,
				customer: CUSTOMERID,
				isPrimary: $scope.newcontact.isPrimary,
				isAdmin: $scope.newcontact.isAdmin,
				facebook: $scope.newcontact.facebook,
				dt_nascimento: $scope.newcontact.dt_nascimento,
			});
		}
		var posturl = BASE_URL + 'customers/contact';
		$http.post(posturl, dataObj, config)
			.then(
				function (response) {
					$scope.tabIndex = 0;
					$scope.saving = false;
					if (response.data.success == true) {
						$scope.tabIndex++;
					} else {
						globals.mdToast('error', response.data.message, 6000);
					}
				},
				function (response) {
					$scope.saving = false;
				}
			);
	};

	$scope.saving = false;
	$scope.AddContact = function () {
		$scope.saving = true;
		if ($scope.newcontact.dt_nascimento) {
			$scope.newcontact.dt_nascimento = moment($scope.newcontact.dt_nascimento).format("YYYY-MM-DD");
		}

		if (!$scope.newcontact) {
			var dataObj = $.param({
				name: '',
				surname: '',
				phone: '',
				extension: '',
				mobile: '',
				email: '',
				address: '',
				skype: '',
				linkedin: '',
				position: '',
				customer: CUSTOMERID,
				isPrimary: $scope.isPrimary,
				isAdmin: $scope.isAdmin,
				facebook: '',
				dt_nascimento: '',
			});
		} else {
			var dataObj = $.param({
				name: $scope.newcontact.name,
				surname: $scope.newcontact.surname,
				phone: $scope.newcontact.phone,
				extension: $scope.newcontact.extension,
				mobile: $scope.newcontact.mobile,
				email: $scope.newcontact.email,
				address: $scope.newcontact.address,
				skype: $scope.newcontact.skype,
				linkedin: $scope.newcontact.linkedin,
				position: $scope.newcontact.position,
				instagram: $scope.newcontact.instagram,

				customer: CUSTOMERID,
				isPrimary: $scope.newcontact.isPrimary,
				isAdmin: $scope.newcontact.isAdmin,
				facebook: $scope.newcontact.facebook,
				dt_nascimento: $scope.newcontact.dt_nascimento,
			});
		}
		$http.post(BASE_URL + 'customers/create_contact', dataObj, config)
			.then(
				function (response) {
					if (response.data.success == true) {
						globals.mdToast('success', response.data.message);
						$mdSidenav('NewContact').close();
						$scope.newcontact = [];
						$http.get(BASE_URL + 'api/contact/' + CUSTOMERID).then(function (contact) {
							$scope.all_contacts = contact.data;
							$scope.contacts = $filter('filter')($scope.all_contacts, {
								customer_id: CUSTOMERID,
							});
						});
					} else {
						globals.mdToast('error', response.data.message);
					}
					$scope.saving = false;
				},
				function (response) {
					$scope.saving = false;
				}
			);
	};
	$scope.updatingContact = false;
	$scope.UpdateContact = function (index) {

		$scope.updatingContact = true;
		var contact = $scope.contacts[index];
		var contact_id = contact.id;
		$scope.contact = contact;

		if ($scope.contact.dt_nascimento) {
			$scope.contact.dt_nascimento = moment($scope.contact.dt_nascimento).format("YYYY-MM-DD HH:mm");
		}
		var dataObj = $.param({
			name: $scope.contact.name,
			surname: $scope.contact.surname,
			phone: $scope.contact.phone,
			extension: $scope.contact.extension,
			mobile: $scope.contact.mobile,
			email: $scope.contact.email,
			address: $scope.contact.address,
			skype: $scope.contact.skype,
			linkedin: $scope.contact.linkedin,
			instagram: $scope.contact.instagram,
			position: $scope.contact.position,
			facebook: $scope.contact.facebook,
			dt_nascimento: $scope.contact.dt_nascimento,
		});
		var posturl = BASE_URL + 'customers/update_contact/' + contact_id;
		$http.post(posturl, dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					globals.mdToast('success', response.data.message);
					//$mdDialog.cancel();
					$(".modal").modal('hide');
					$http.get(BASE_URL + 'api/contact/' + CUSTOMERID).then(function (contact) {
						$scope.all_contacts = contact.data;
						$scope.contacts = $filter('filter')($scope.all_contacts, {
							customer_id: CUSTOMERID,
						});
					});
					$('#updatecontact' + contact_id + '').modal('hide');
				} else {
					globals.mdToast('error', response.data.message);
				}
				$scope.updatingContact = false;
			},
			function (response) {
				$scope.updatingContact = false;
			}
		);
	};
	$scope.ChangePassword = function (contact) {
		// Appending dialog to document.body to cover sidenav in docs app
		var confirm = $mdDialog.prompt()
			.title('Change Password')
			.textContent('Are sure change contact password?')
			.placeholder('Password')
			.ariaLabel('Password')
			.initialValue('')
			.targetEvent(contact)
			.required(true)
			.ok('Okay!')
			.cancel('Cancel');
		$mdDialog.show(confirm).then(function (result) {
			var dataObj = $.param({
				password: result,
			});
			$http.post(BASE_URL + 'customers/change_password_contact/' + contact, dataObj, config)
				.then(
					function (response) {
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);
						} else {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
					}
				);
		}, function () {
		});
	};
	$scope.RemoveContact = function (id) {
		globals.deleteDialog(lang.attention, lang.delete_contact, id, lang.doIt, lang.cancel, 'customers/remove_contact/' + id, function (response) {
			if (response.success == true) {
				globals.mdToast('success', response.message);
				$http.get(BASE_URL + 'api/contact/' + CUSTOMERID).then(function (contact) {
					$scope.contacts = contact.data;
				});
			} else {
				globals.mdToast('error', response.message);
			}
		});
	};




	$scope.CloseModal = function () {
		$mdDialog.cancel();
	};

	$http.get(BASE_URL + 'EmailsAut/getTaskAutByUserCustomer/' + CUSTOMERID).then(function (data) {
		$scope.emailsAut = data.data;
		$scope.emailsAutLoader = false;
	});

	$http.get(BASE_URL + 'EmailsAut/getAdicionadosCustomer/' + CUSTOMERID).then(function (data) {
		$scope.adicionadoTask = [];
		data.data.map((a) => {
			$scope.adicionadoTask[a.id_task] = true;
		});
	});

	$scope.UpdateAdicionadoTask = function () {

		var dataObj = $.param({
			adicionadoTask: $scope.adicionadoTask,
			id_customer: CUSTOMERID
		});

		$http.post(BASE_URL + 'EmailsAut/UpdateAdicionadoTaskCustomer', dataObj, config)
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

	$http.get(BASE_URL + 'api/reminders_by_type/customer/' + CUSTOMERID).then(function (Reminders) {
		$scope.in_reminders = Reminders.data;
		$scope.AddReminder = function () {
			var dataObj = $.param({
				description: $scope.reminder_description,
				date: moment($scope.reminder_date).format("YYYY-MM-DD HH:mm:ss"),
				staff: $scope.reminder_staff,
				relation_type: 'customer',
				relation: CUSTOMERID,
			});
			var posturl = BASE_URL + 'trivia/addreminder';
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						console.log(response);
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
						console.log(response);
					},
					function (response) {
						console.log(response);
					}
				);
		};
	});
	$http.get(BASE_URL + 'api/notes/customer/' + CUSTOMERID).then(function (Notes) {
		$scope.notes = Notes.data;
		$scope.AddNote = function () {
			var dataObj = $.param({
				description: $scope.note,
				relation_type: 'customer',
				relation: CUSTOMERID,
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
							$http.get(BASE_URL + 'api/notes/customer/' + CUSTOMERID).then(function (Notes) {
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
						console.log(response);
					},
					function (response) {
						console.log(response);
					}
				);
		};
	});
}
CiuisCRM.controller('Customers_Controller', Customers_Controller);
CiuisCRM.controller('Customer_Controller', Customer_Controller);