function Customers_sucess_Controller($scope, $http, $mdSidenav, $mdDialog, $mdConstant, $filter, $q) {
	"use strict";
	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'customer').then(function (custom_fields) {
		$scope.all_custom_fields = custom_fields.data;
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	});
	$scope.customer = [];
	$scope.filtros = [];

	$scope.customer.created = moment($scope.customer.created).format("YYYY-MM-DD HH:mm:ss");

	$scope.customersLoader = true;
	$scope.customers = [];
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

	$http.get(BASE_URL + 'api/staff/0').then(function (Staff) {
        $scope.staff = Staff.data;
    });

	$scope.sortableOptions = {
		'stop': () => {
			console.log('reord')
			$.each($scope.funilsCustomers, (index, list) => {
				var order = [];
				list.etapas.map((status, index2) => {
					order[index2] = status.id;
				});

				var dataObj = $.param({ order, id: list.id });

				$http.post(BASE_URL + 'customer_sucess/reorderList', dataObj, config).then(
					function (response) {
					},
					function () {
					}
				);
			})

		}
	};

	$scope.changeFunil = function (id) {
		localStorage.funilCutomer = id;
		$scope.funilSelect_index = id;


	}

	$scope.filtrarCustomers = function () {
	
		$scope.customers.map((row, index) => {
			$scope.customers[index].view = '1';

			if ($scope.filtros.flt_funcionario != null) {
				if (row.customer_sucess != $scope.filtros.flt_funcionario && $scope.filtros.flt_funcionario != "-1") {
					$scope.customers[index].view = '0';
				}
			}
		})
	}


	$scope.dropSuccessHandler = function ($event, index, array) {
		$scope.selected_customer = $scope.customers[index];
	};

	$scope.onDrop = function ($event, $data, array, index) {
		$scope.moved_customer = $data;
		var dataObj = $.param({
			customer_id: $scope.moved_customer.id,
			status_id: array,
		});

		var customerIndex = 0;
		$scope.customers.filter((a, i) => {
			if (a.id == $scope.moved_customer.id) {
				customerIndex = i;
			}
		})

		$scope.customers[customerIndex].etapa = array;
		$http.post(BASE_URL + 'customer_sucess/move_customer', dataObj, config).then(
			function (response) {
				showToast(NTFTITLE, "Salvo com sucesso!", ' success');
			},
			function () {
				showToast(NTFTITLE, "Erro, tente novamente!", ' error');
			}
		);

	};

	$scope.getCustomerByStatus = function (id_group) {
		return $scope.customers.filter(a => a.etapa == id_group);
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
			$http.get(BASE_URL + 'api/customers?customer_status_id=1').then(function (Customers) {
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


	});
	$scope.get_funils = function () {
		$http.get(BASE_URL + 'customer_sucess/get_funils').then(function (returns) {
			$scope.funilsCustomers = returns.data;

			if (localStorage.funilCutomer != null) {
				if ($scope.funilsCustomers[localStorage.funilCutomer] != null) {
					$scope.funilSelect_index = localStorage.funilCutomer;
				} else {
					$scope.funilSelect_index = 0;
				}

			} else {
				$scope.funilSelect_index = 0;
			}


		});
	};
	$scope.get_funils();

	$scope.NewFunil = function () {
		globals.createDialog("Novo Funil", "Por favor, digite o nome do funil", "Nome do funil", event, $scope.lang.add,
			$scope.lang.cancel, 'customer_sucess/add_funil', function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$scope.get_funils();
				} else {
					globals.mdToast('error', response.message);
				}
			});
	};

	$scope.EditFunil = function (id_list, nm_list, event, index) {
		globals.editDialog($scope.lang.edit + ' Funil', ' Você pode alterar o nome do funil', $scope.lang.status + ' ' + $scope.lang.name, nm_list,
			event, $scope.lang.save, $scope.lang.cancel, 'customer_sucess/update_funil/' + id_list, function (response) {

				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$scope.get_funils();
				} else {
					globals.mdToast('error', response.message);
				}

			});
	};

	$scope.NewStatus = function (id_funil) {
		globals.createDialog($scope.lang.new_status, $scope.lang.type_status_name, $scope.lang.status_name, event, $scope.lang.add,
			$scope.lang.cancel, 'customer_sucess/add_status', function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$scope.get_funils();
				} else {
					globals.mdToast('error', response.message);
				}
			}, { "id_funil": id_funil });
	};

	$scope.DeleteLeadFunil = function (funil) {
		globals.deleteDialog($scope.lang.delete + ' Funil', 'Você confirma a exclusão deste Funil ?',
			funil, $scope.lang.delete, $scope.lang.cancel, 'customer_sucess/remove_funil/' + funil, function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$scope.get_funils();
				} else {
					globals.mdToast('error', response.message);
				}
			});
	};

	$scope.EditStatus = function (status_id, lead_status, event, index) {
		globals.editDialog($scope.lang.edit + ' ' + $scope.lang.lead + ' ' + $scope.lang.status, $scope.lang.lead_title + ' ' +
			$scope.lang.lead + ' ' + $scope.lang.status + ' ' + $scope.lang.name, $scope.lang.status + ' ' + $scope.lang.name, lead_status,
			event, $scope.lang.save, $scope.lang.cancel, 'customer_sucess/update_status/' + status_id, function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$scope.get_funils();
				} else {
					globals.mdToast('error', response.message);
				}

			});
	};

	$scope.DeleteLeadStatus = function (status) {
		globals.deleteDialog($scope.lang.delete + ' ' + $scope.lang.status, $scope.lang.delete_meesage + ' ' + $scope.lang.status + '?',
			status, $scope.lang.delete, $scope.lang.cancel, 'customer_sucess/remove_status/' + status, function (response) {
				if (response.success == true) {
					showToast(NTFTITLE, response.message, ' success');
					$scope.get_funils();
				} else {
					globals.mdToast('error', response.message);
				}
			});
	};




	var deferred = $q.defer();
	$scope.customer_list = {
		order: '',
		limit: 5,
		page: 1
	};

	$scope.promise = deferred.promise;
	$http.get(BASE_URL + 'api/customers?customer_status_id=1').then(function (Customers) {
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
								$http.get(BASE_URL + 'api/customers?customer_status_id=1&customer_sucess=1').then(function (Customers) {
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
						$http.get(BASE_URL + 'api/customers?customer_status_id=1').then(function (Customers) {
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

CiuisCRM.controller('Customers_sucess_Controller', Customers_sucess_Controller);