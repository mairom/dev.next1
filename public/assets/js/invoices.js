function Invoices_Controller($scope, $compile, $http, $mdSidenav, $q, $timeout, $filter) {
	"use strict";

	var data = new Date();
	var data2 = new Date(data.getFullYear(), data.getMonth() + 1, 0);

	$scope.filtros = [];
	$scope.filtros.dt_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01 00:00');
	$scope.filtros.dt_ate = new Date(data2.getFullYear() + '-' + (data2.getMonth() + 1) + '-' + (data2.getDate() < 10 ? '0' + data2.getDate() : data2.getDate()) + ' 00:00');
	$scope.created = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()) + ' 00:00');
	var gerandoPdf = false;

	// Inicializar variáveis como arrays vazios
	$scope.formas_pagamento = [];
	$scope.pagamentoModal = {};
	$scope.all_custom_fields = [];
	$scope.custom_fields = [];
	$scope.products = [];
	$scope.invoices = [];
	$scope.dueinvoices = [];
	$scope.newtickets = [];

	globals.get_countries();
	$scope.InvoicesList = function () {
		$scope.loadingData = true;
		$http.get(BASE_URL + 'invoices/get_content/index').then(function (Data) {
			$scope.content = Data.data;
			var div = angular.element(document.getElementById("pageContent"));
			div.html($scope.content);
			$compile(div.contents())($scope);
			$scope.loadingData = false;
		});
	};


	// Carregar formas de pagamento
	$scope.get_pagamentos = function () {
		$http.get(`${BASE_URL}api/get_frm_pagamentos`).then(function (response) {
			$scope.formas_pagamento = Array.isArray(response.data) ? response.data : [];
		}).catch(function (error) {
			console.error('Erro ao carregar formas de pagamento:', error);
			$scope.formas_pagamento = [];
		});
	};
	$scope.get_pagamentos();

	// Abrir modal de configuração de formas de pagamento
	$scope.openPaymentMethodsConfig = function () {
		$scope.get_pagamentos();
		$("#modalPagamento").modal("show");
	};

	// Fechar modal de formas de pagamento
	$scope.closePaymentModal = function () {
		$("#modalPagamento").modal("hide");
	};

	console.log('sadasd')

	// Adicionar nova forma de pagamento
	$scope.addPagamento = function (forma = null) {
		console.log('addPagamento chamado', forma);
		$scope.pagamentoModal = forma || {};
		console.log('Abrindo modal modalAddPagamento');
		
		// Fechar o modal principal primeiro
		$("#modalPagamento").modal("hide");
		
		// Aguardar um pouco antes de abrir o segundo modal
		setTimeout(function() {
			$("#modalAddPagamento").modal("show");
		}, 300);
	};

	// Editar forma de pagamento
	$scope.editPagamento = function (forma) {
		$scope.pagamentoModal = angular.copy(forma);
		$("#modalAddPagamento").modal("show");
	};

	// Fechar modal de adicionar/editar
	$scope.closeAddPaymentModal = function () {
		$("#modalAddPagamento").modal("hide");
		// Reabrir o modal principal
		setTimeout(function() {
			$("#modalPagamento").modal("show");
		}, 300);
	};

	// Salvar forma de pagamento
	$scope.salvarPagamentoList = function (id_forma = null) {
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};

		var dataObj = $.param({
			id_forma: id_forma,
			nm_forma: $scope.pagamentoModal.nm_forma,
			is_ativo: '1'
		});

		var posturl = BASE_URL + "api/updt_frm_pagamentos/";

		$http.post(posturl, dataObj, config).then(function (response) {
			console.log('Resposta do servidor:', response);
			if (response.data.success == true) {
				showToast('SUCESSO!', 'Forma de pagamento salva com sucesso', 'success');
				$("#modalAddPagamento").modal("hide");
				$scope.get_pagamentos();
				// Reabrir o modal principal
				setTimeout(function() {
					$("#modalPagamento").modal("show");
				}, 300);
			} else {
				showToast('Erro!', response.data.message || 'Erro ao salvar', 'danger');
			}
		}, function (error) {
			console.error('Erro ao salvar:', error);
			showToast('Erro!', 'Erro ao salvar forma de pagamento', 'danger');
		});
	};

	// Deletar forma de pagamento
	$scope.dellPagamentos = function (id_forma) {
		if (!confirm('Deseja realmente excluir esta forma de pagamento?')) {
			return;
		}

		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};

		var dataObj = $.param({
			id_forma: id_forma,
			is_ativo: '0'
		});

		var posturl = BASE_URL + "api/updt_frm_pagamentos/";

		$http.post(posturl, dataObj, config).then(function (response) {
			showToast('SUCESSO!', 'Forma de pagamento excluída', 'success');
			$scope.get_pagamentos();
		}, function (error) {
			showToast('Erro!', 'Erro ao excluir forma de pagamento', 'danger');
		});
	};

	$scope.GoToInvoice = function () {

	};
	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (v) {
		v = v.toString().replace(/\D/g, "");
		v = new String(Number(v));
		var len = v.length;
		if (1 == len) {
			v = v.replace(/(\d)/, "0,0$1");
		} else if (2 == len) {
			v = v.replace(/(\d)/, "0,$1");
		} else if (len > 2) {
			v = v.replace(/(\d{2})$/, ',$1');
			if (len > 5) {
				var x = len - 5
					, er = new RegExp('(\\d{' + x + '})(\\d)');
				v = v.replace(er, '$1.$2');
			}
		}
		return v;
	}

	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'invoice').then(function (custom_fields) {
		$scope.all_custom_fields = Array.isArray(custom_fields.data) ? custom_fields.data : [];
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	}).catch(function (error) {
		console.error('Erro ao carregar custom fields:', error);
		$scope.all_custom_fields = [];
		$scope.custom_fields = [];
	});

	$http.get(BASE_URL + 'api/products').then(function (Products) {
		$scope.products = Array.isArray(Products.data) ? Products.data : [];
	}).catch(function (error) {
		console.error('Erro ao carregar produtos:', error);
		$scope.products = [];
	});

	$scope.GetProduct = (function (search) {
		var deferred = $q.defer();
		var tempProd = [];
		$scope.products.map(a => {
			if (a.name.toLowerCase().includes(search.toLowerCase())) {
				tempProd.push(a);
			}
		})

		deferred.resolve(tempProd);

		return deferred.promise;
	});

	$scope.selectedProduct = [];
	$scope.invoice = {
		items: [{
			code: '',
			name: null,
			product_id: 0,
			description: '',
			quantity: 1,
			unit: item_unit,
			price: 0,
			tax: 0,
			discount: 0,
		}]
	};

	$scope.add = function () {
		$scope.invoice.items.push({
			name: new_item,
			product_id: 0,
			code: '',
			description: '',
			quantity: 1,
			unit: item_unit,
			price: 0,
			tax: 0,
			discount: 0,
		});
	};

	$scope.remove = function (index) {
		$scope.invoice.items.splice(index, 1);
	};

	$scope.subtotal = function () {
		var subtotal = 0;
		angular.forEach($scope.invoice.items, function (item) {
			subtotal += item.quantity * moneyEua(item.price);
		});
		return subtotal.toFixed(2);
	};

	$scope.linediscount = function () {
		var linediscount = 0;
		angular.forEach($scope.invoice.items, function (item) {
			linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));
		});
		return linediscount.toFixed(2);
	};

	$scope.totaltax = function () {
		var totaltax = 0;
		angular.forEach($scope.invoice.items, function (item) {
			totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));
		});
		return totaltax.toFixed(2);
	};

	$scope.grandtotal = function () {
		var grandtotal = 0;
		angular.forEach($scope.invoice.items, function (item) {
			grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));
		});
		return grandtotal.toFixed(2);
	};

	$scope.today = new Date();

	$scope.saveAll = function () {
		$scope.savingInvoice = true;
		if ($scope.invoice.shipping_country) {
			$scope.shipping_country = $scope.invoice.billing_country_id;
		} else {
			$scope.shipping_country = null;
		}
		if ($scope.invoice.billing_country) {
			$scope.billing_country = $scope.invoice.billing_country_id;
		} else {
			$scope.billing_country = null;
		}
		if ($scope.invoice.shipping_state) {
			$scope.shipping_state = $scope.invoice.shipping_state_id;
		} else {
			$scope.shipping_state = null;
		}
		if ($scope.invoice.billing_state) {
			$scope.billing_state = $scope.invoice.billing_state_id;
		} else {
			$scope.billing_state = null;
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

		var invoice_recurring;
		if ($scope.invoice_recurring == true) {
			invoice_recurring = '1';
		} else {
			invoice_recurring = '0';
		}

		var EndRecurring;
		if ($scope.EndRecurring && $scope.EndRecurring != null && $scope.EndRecurring != '') {
			EndRecurring = moment($scope.EndRecurring).format("YYYY-MM-DD 00:00:00");
		}

		if ($scope.created) {
			$scope.created = moment($scope.created).format("YYYY-MM-DD");
		}
		if ($scope.duedate) {
			$scope.duedate = moment($scope.duedate).format("YYYY-MM-DD");
		}
		if ($scope.datepayment) {
			$scope.datepayment = moment($scope.datepayment).format("YYYY-MM-DD");
		}
		var dataObj = $.param({
			customer: $scope.customer.id,
			created: $scope.created,
			duedate: $scope.duedate,
			datepayment: $scope.datepayment,
			account: $scope.account,
			//duenote: $scope.duenote,
			serie: $scope.serie,
			no: $scope.no,
			sub_total: $scope.subtotal,
			total_discount: $scope.linediscount,
			total_tax: $scope.totaltax,
			total: $scope.grandtotal,
			status: $scope.invoice_status,
			id_forma_pagment: $scope.id_forma_pagment,
			// Billing Address
			billing_street: $scope.invoice.billing_street,
			billing_city: $scope.invoice.billing_city,
			billing_state_id: $scope.invoice.billing_state_id,
			billing_zip: $scope.invoice.billing_zip,
			billing_country: $scope.billing_country,
			// Shipping Address
			shipping_street: $scope.invoice.shipping_street,
			shipping_city: $scope.invoice.shipping_city,
			shipping_state_id: $scope.invoice.shipping_state_id,
			shipping_zip: $scope.invoice.shipping_zip,
			shipping_country: $scope.shipping_country,
			// START Recurring
			recurring: invoice_recurring,
			end_recurring: EndRecurring,
			recurring_type: $scope.recurring_type,
			recurring_period: $scope.recurring_period,
			// END Recurring
			items: $scope.invoice.items,
			totalItems: $scope.invoice.items.length,
			custom_fields: $scope.tempArr,
			default_payment_method: $scope.default_payment_method
		});
		var posturl = BASE_URL + 'invoices/create';
		$http.post(posturl, dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					window.location.href = BASE_URL + 'invoices/invoice/' + response.data.id;
				} else {
					$scope.savingInvoice = false;
					showToast(NTFTITLE, response.data.message, ' danger');
				}
			},
			function (response) {
				$scope.savingInvoice = false;
			}
		);
	};

	$scope.CopyBillingFromCustomer = function () {
		$scope.invoice.billing_street = $scope.customer.billing_street;
		$scope.invoice.billing_city = $scope.customer.billing_city;
		$scope.invoice.billing_state = $scope.customer.billing_state;
		$scope.invoice.billing_zip = $scope.customer.billing_zip;
		$scope.invoice.billing_country = $scope.customer.billing_country;
	};

	$scope.CopyShippingFromCustomer = function () {
		$scope.invoice.shipping_street = $scope.customer.shipping_street;
		$scope.invoice.shipping_city = $scope.customer.shipping_city;
		$scope.invoice.shipping_state = $scope.customer.shipping_state;
		$scope.invoice.shipping_zip = $scope.customer.shipping_zip;
		$scope.invoice.shipping_country = $scope.customer.shipping_country;
	};

	$scope.getBillingStates = function (country) {
		console.log(country);
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.billingStates = States.data;
		});
	};

	$scope.getShippingStates = function (country) {
		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {
			$scope.shippingStates = States.data;
		});
	};

	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {
		$scope.accounts = Accounts.data;
	});

	$scope.SelectedCustomer = $scope.customer;

	$scope.invoiceLoader = true;
	var deferred = $q.defer();
	$scope.invoice_list = {
		order: '',
		limit: 50,
		page: 1
	};

	$scope.promise = deferred.promise;
	$scope.getResultados = function () {
		var dataObj = $.param({
			dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
			dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
			flt_cliente: $scope.filtros.flt_cliente,
		});

		$http.post(BASE_URL + 'invoices/invoices', dataObj, config).then(function (Invoices) {
			$scope.invoices = Invoices.data.invoices;
			$scope.graph = Invoices.data.graph;

			initGraph($scope.graph);

			$('#graphsIvoices').show()
			deferred.resolve();
			$scope.limitOptions = [50, 100, 150, 200];
			if ($scope.invoices.length > 200) {
				$scope.limitOptions = [50, 100, 150, 200, $scope.invoices.length];
			}

			$scope.invoiceLoader = false;
			$scope.search = {
				customer: ''
			};
			// Filter Buttons //
			$scope.toggleFilter = buildToggler('ContentFilter');

			function buildToggler(navID) {
				return function () {
					$mdSidenav(navID).toggle();

				};
			}
			$scope.close = function () {
				$mdSidenav('ContentFilter').close();
			};
			// Filter Buttons //
			// Filtered Datas
			$scope.filter = {};
			$scope.getOptionsFor = function (propName) {
				return ($scope.invoices || []).map(function (item) {
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
		});

		/*
		$http.post(BASE_URL + 'invoices/get_graphs', dataObj, config).then(function (data) {
			$scope.graph = data.data;
		});
		*/
	}
	$scope.getResultados();


	$scope.geraPdf = function () {
		if (gerandoPdf) {
			return;
		}
		gerandoPdf = true;
		$('.filtros-invoices').hide();
		var node = document.getElementById("main-content-page");

		domtoimage.toPng(node).then(function (dataUrl) {
			var printWindow = window.open('', '', 'height=400,width=800');
			printWindow.document.write('<html><head><title>GraficosReport' + new Date().toISOString() + '</title>');
			printWindow.document.write('</head><body >');
			printWindow.document.write("<img onload = 'window.print()' src = '" + dataUrl + "'>");
			printWindow.document.write(`<style type="text/css" media="print">@page { size: landscape; }</style>`);
			printWindow.document.write('</body></html>');
			printWindow.document.close();
			gerandoPdf = false;
			$('.filtros-invoices').show();
		}).catch(function (error) {
			gerandoPdf = false;
			$('.filtros-invoices').show();
			console.log('oops, something went wrong!', error);
		});
	}


}

function Invoice_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout, fileUpload) {
	"use strict";

	var data = new Date();
	$scope.invoice = [];
	$scope.invoice.created_edit = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-' + (data.getDate() < 10 ? '0' + data.getDate() : data.getDate()));


	globals.get_countries();
	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'invoice/' + INVOICEID).then(function (custom_fields) {
		$scope.custom_fields = custom_fields.data;
	});

	$http.get(`${BASE_URL}api/get_frm_pagamentos`).then(function (data) {
		$scope.formas_pagamento = data.data;
	});

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

	$scope.sendEmail = function () {
		$scope.sendingEmail = true;
		$http.post(BASE_URL + 'invoices/send_invoice_email/' + INVOICEID)
			.then(
				function (response) {
					console.log(response);
					if (response.data.status === true) {
						showToast(NTFTITLE, response.data.message, 'success');
					}
					$scope.sendingEmail = false;
				},
				function (response) {
					console.log(response);
				}
			);
	};

	$scope.GeneratePDF = function (ev) {
		$mdDialog.show({
			templateUrl: 'generate-invoice.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};

	$scope.CreatePDF = function () {
		$scope.PDFCreating = true;
		$http.post(BASE_URL + 'invoices/create_pdf/' + INVOICEID)
			.then(
				function (response) {
					console.log(response);
					if (response.data.status === true) {
						$scope.PDFCreating = false;
						$scope.CreatedPDFName = response.data.file_name;
					}
				},
				function (response) {
					console.log(response);
				}
			);
	};

	$scope.Delete = function () {
		// Appending dialog to document.body to cover sidenav in docs app
		var confirm = $mdDialog.confirm()
			.title($scope.lang.deleteinvoice)
			.textContent($scope.lang.inv_remove_msg)
			.ariaLabel('Delete Invoice')
			.targetEvent(INVOICEID)
			.ok($scope.lang.delete)
			.cancel($scope.lang.cancel);

		$mdDialog.show(confirm).then(function () {
			$http.post(BASE_URL + 'invoices/remove/' + INVOICEID, config)
				.then(
					function (response) {
						if (response.data.success == true) {
							window.location.href = BASE_URL + 'invoices';
							globals.mdToast('success', response.data.message);
						} else {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						console.log(response);
					}
				);
		});
	};

	$scope.invoiceLoader = true;
	$http.get(BASE_URL + 'invoices/get_invoice/' + INVOICEID).then(function (InvoiceDetails) {
		$scope.invoice = InvoiceDetails.data;
		$scope.invoice.duedate_edit = new Date($scope.invoice.duedate_edit + ' 00:00');
		$scope.invoice.created_edit = new Date($scope.invoice.created_edit + ' 00:00');
		if ($scope.invoice.recurring_endDate != null && $scope.invoice.recurring_endDate != '1969-12-31T21:00:00-0300') {
			$scope.invoice.recurring_endDate = new Date($scope.invoice.recurring_endDate);
		} else {
			$scope.invoice.recurring_endDate = new Date();
		}
		$scope.excluirFuturas = 0;
		$scope.invoiceOriginal = JSON.parse(JSON.stringify(InvoiceDetails.data));

		setTimeout(function () { InitMask() }, 2000);

		var invoice = JSON.stringify($scope.invoice);
		var cust = $scope.invoice.properties.customer;
		var searchCustomer = $scope.invoice.properties.customer ? cust.split(' ')[0] : '';
		$scope.getBillingStates($scope.invoice.billing_country_id);
		$scope.getShippingStates($scope.invoice.shipping_country_id);
		$scope.search_customers(searchCustomer);
		$http.get(BASE_URL + 'api/customers/').then(function (Data) {
			$scope.customers = Data.data;
		});
		// $http.get(BASE_URL + 'api/contacts').then(function (Contacts) {
		// 	$scope.all_contacts = Contacts.data;
		// 	$scope.contacts = $filter('filter')($scope.all_contacts, {
		// 		customer_id: $scope.invoice.customer,
		// 	});
		// });
		$scope.invoiceLoader = false;
		$scope.MarkAsDraft = function () {
			$http.post(BASE_URL + 'invoices/mark_as_draft/' + INVOICEID)
				.then(
					function (response) {
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);
						} else {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						console.log(response);
					}
				);
		};

		$scope.MarkAsCancelled = function () {
			$http.post(BASE_URL + 'invoices/mark_as_cancelled/' + INVOICEID)
				.then(
					function (response) {
						if (response.data.success == true) {
							globals.mdToast('success', response.data.message);
						} else {
							globals.mdToast('error', response.data.message);
						}
					},
					function (response) {
						console.log(response);
					}
				);
		};

		$scope.subtotal = function () {
			var subtotal = 0;
			angular.forEach($scope.invoice.items, function (item) {
				subtotal += item.quantity * moneyEua(item.price);
			});
			return subtotal.toFixed(2);
		};
		$scope.linediscount = function () {
			var linediscount = 0;
			angular.forEach($scope.invoice.items, function (item) {
				linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));
			});
			return linediscount.toFixed(2);
		};
		$scope.totaltax = function () {
			var totaltax = 0;
			angular.forEach($scope.invoice.items, function (item) {
				totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));
			});
			return totaltax.toFixed(2);
		};
		$scope.grandtotal = function () {
			var grandtotal = 0;
			angular.forEach($scope.invoice.items, function (item) {
				grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));
			});
			return grandtotal.toFixed(2);
		};

		$scope.totalpaid = function () {
			return $scope.invoice.payments.reduce(function (total, payment) {
				return total + (payment.amount * 1 || 0);
			}, 0);
		};

		$scope.amount = $scope.invoice.balance;

		$http.get(BASE_URL + 'api/products').then(function (Products) {
			$scope.products = Products.data;
		});

		$scope.GetProduct = (function (search) {
			var deferred = $q.defer();
			$timeout(function () {
				deferred.resolve($scope.products);
			}, Math.random() * 500, false);
			return deferred.promise;
		});

		$scope.add = function () {
			$scope.invoice.items.push({
				name: new_item,
				product_id: 0,
				code: '',
				description: '',
				quantity: 1,
				unit: item_unit,
				price: 0,
				tax: 0,
				discount: 0,
			});
		};

		$scope.remove = function (index) {
			var item = $scope.invoice.items[index];
			$http.post(BASE_URL + 'invoices/remove_item/' + item.id)
				.then(
					function (response) {
						console.log(response);
						$scope.invoice.items.splice(index, 1);
						$scope.invoice.balance = $scope.invoice.balance - item.total;
						$scope.amount = $scope.invoice.balance;
					},
					function (response) {
						console.log(response);
					}
				);
		};

		$scope.changeBank = function (id) {
			var customer = '';
			for (var i = 0; i < $scope.customers.length; i++) {
				if ($scope.customers[i].id == id) {
					customer = $scope.customers[i];
					continue;
				}
			}
			$scope.invoice.default_payment_method = customer.default_payment_method;
		};

		$scope.saveAll = function (perguntado = false) {

			if ($scope.invoiceOriginal.recurring_status != $scope.invoice.recurring_status && !perguntado) {
				if (!$scope.invoice.recurring_status) {
					var confirm = $mdDialog.confirm()
						.title('Você deseja excluir as faturas futuras?')
						.textContent('Caso exista faturas futuras as mesma serão excluidas!')
						.ariaLabel('1')
						.ok($scope.lang.delete)
						.cancel($scope.lang.cancel);

					$mdDialog.show(confirm).then(function () {
						$scope.excluirFuturas = 1;
						$scope.saveAll(true);
					}, function () {
						//
					});
					return;
				}
			}

			if ($scope.invoice.recurring_status && !perguntado) {
				if ($scope.invoice.recurring_type != $scope.invoiceOriginal.recurring_type
					|| $scope.invoice.recurring_endDate != $scope.invoiceOriginal.recurring_endDate) {
					var confirm = $mdDialog.confirm()
						.title('Você deseja editar as faturas futuras?')
						.textContent('Com isso irá editar todas as faturas futuras para o novo parametro')
						.ariaLabel('1')
						.ok($scope.lang.update)
						.cancel($scope.lang.cancel);

					$mdDialog.show(confirm).then(function () {
						$scope.excluirFuturas = 1;
						$scope.saveAll(true);
					}, function () {
						//
					});
					return;
				}
			}

			$scope.savingInvoice = true;
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
			var EndRecurring;
			if ($scope.invoice.recurring_endDate && $scope.invoice.recurring_endDate != null && $scope.invoice.recurring_endDate != '') {
				EndRecurring = moment($scope.invoice.recurring_endDate).format("YYYY-MM-DD 00:00:00");
			}
			if ($scope.invoice.created_edit) {
				$scope.invoice.created = moment($scope.invoice.created_edit).format("YYYY-MM-DD");
			}
			if ($scope.invoice.duedate_edit) {
				$scope.invoice.duedate = moment($scope.invoice.duedate_edit).format("YYYY-MM-DD");
			}
			var dataObj = $.param({
				customer: $scope.invoice.customer,
				created: $scope.invoice.created,
				duedate: $scope.invoice.duedate,

				serie: $scope.invoice.serie,
				no: $scope.invoice.no,
				sub_total: $scope.subtotal,
				total_discount: $scope.linediscount,
				id_forma_pagment: $scope.id_forma_pagment,
				total_tax: $scope.totaltax,
				total: $scope.grandtotal,
				// Billing Address
				billing_street: $scope.invoice.billing_street,
				billing_city: $scope.invoice.billing_city,
				billing_state_id: $scope.invoice.billing_state_id,
				billing_zip: $scope.invoice.billing_zip,
				billing_country: $scope.invoice.billing_country_id,
				// Shipping Address
				shipping_street: $scope.invoice.shipping_street,
				shipping_city: $scope.invoice.shipping_city,
				shipping_state_id: $scope.invoice.shipping_state_id,
				shipping_zip: $scope.invoice.shipping_zip,
				shipping_country: $scope.invoice.shipping_country_id,
				// START Recurring
				recurring_status: $scope.invoice.recurring_status,
				recurring: $scope.invoice.recurring_status,
				end_recurring: EndRecurring,
				recurring_type: $scope.invoice.recurring_type,
				recurring_period: $scope.invoice.recurring_period,
				recurring_id: $scope.invoice.recurring_id,
				// END Recurring
				items: $scope.invoice.items,
				totalItems: $scope.invoice.items.length,
				custom_fields: $scope.tempArr,
				default_payment_method: $scope.invoice.default_payment_method,
				excluirFuturas: $scope.excluirFuturas
			});
			var posturl = BASE_URL + 'invoices/update/' + INVOICEID;
			$http.post(posturl, dataObj, config)
				.then(
					function (response) {
						if (response.data.success == true) {
							window.location.href = BASE_URL + 'invoices/invoice/' + response.data.id;
						} else {
							$scope.savingInvoice = false;
							showToast(NTFTITLE, response.data.message, ' danger');
						}
					},
					function (response) {
						$scope.savingInvoice = false;
						showToast(NTFTITLE, response.data.message, ' danger');
					}
				);
		};
	});

	$scope.UpdateInvoice = function (id) {
		window.location.href = BASE_URL + 'invoices/update/' + id;
	};

	$scope.RecordPayment = buildToggler('RecordPayment');
	$scope.Discussions = buildToggler('Discussions');
	$scope.NewDiscussion = buildToggler('NewDiscussion');

	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();

		};
	}

	$scope.close = function () {
		$mdSidenav('RecordPayment').close();
		$mdSidenav('Discussions').close();
		$mdSidenav('NewDiscussion').close();
		$mdDialog.hide();
	};
	$scope.CloseModal = function () {
		$mdDialog.hide();
	};

	$http.get(BASE_URL + 'api/discussions/invoice/' + INVOICEID).then(function (Discussions) {
		$scope.discussions = Discussions.data;
		$scope.Discussion_Detail = function (index) {
			var discussion = $scope.discussions[index];
			$scope.discussions_comments = discussion.comments;
			$scope.AddComment = function (index) {
				var discussion = $scope.discussions[index];
				var dataObj = $.param({
					discussion_id: discussion.id,
					content: discussion.newcontent,
					contact_id: discussion.contact_id,
					full_name: LOGGEDINSTAFFNAME,

				});
				var posturl = BASE_URL + 'trivia/add_discussion_comment';
				$http.post(posturl, dataObj, config)
					.then(
						function (response) {
							console.log(response);
							$scope.discussions_comments.push({
								'content': discussion.newcontent,
								'full_name': LOGGEDINSTAFFNAME,
								'created': new Date(),
							});
							$('.comment-description').val('');
						},
						function (response) {
							console.log(response);
						}
					);
			};
			$mdDialog.show({
				contentElement: '#Discussion_Detail-' + discussion.id,
				parent: angular.element(document.body),
				targetEvent: index,
				clickOutsideToClose: true
			});
		};
	});

	$scope.ShowCustomer = false;

	$scope.CreateDiscussion = function () {
		var dataObj = $.param({
			relation_type: 'invoice',
			relation: INVOICEID,
			subject: $scope.new_discussion.subject,
			description: $scope.new_discussion.description,
			contact_id: $scope.new_discussion.contact_id,
			show_to_customer: $scope.ShowCustomer,
			staff_id: ACTIVESTAFF,

		});
		var posturl = BASE_URL + 'trivia/create_discussion';
		$http.post(posturl, dataObj, config)
			.then(
				function (response) {
					console.log(response);
					$scope.discussions.push({
						'id': response.data,
						'subject': $scope.new_discussion.subject,
						'contact': $scope.new_discussion.contact_id,
					});
					$mdSidenav('NewDiscussion').close();
				},
				function (response) {
					console.log(response);
				}
			);
	};

	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {
		$scope.accounts = Accounts.data;
	});
	$scope.doing = false;
	$scope.AddPayment = function () {
		$scope.doing = true;
		var dataObj = $.param({
			date: moment($scope.date).format("YYYY-MM-DD"),
			balance: $scope.invoice.balance - $scope.amount,
			amount: $scope.amount,
			not: $scope.not,
			account: $scope.account,
			invoicetotal: $scope.grandtotal,
			staff: ACTIVESTAFF,
			customer: INVOICECUSTOMER,
			invoice: INVOICEID,
		});
		var posturl = BASE_URL + 'invoices/record_payment';
		$http.post(posturl, dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					$mdSidenav('RecordPayment').close();
					globals.mdToast('success', response.data.message);
					$scope.invoice.balance = $scope.invoice.balance - $scope.amount;
					$scope.doing = false;
					$http.get(BASE_URL + 'invoices/get_invoice/' + INVOICEID).then(function (InvoiceDetails) {

						$scope.invoice = InvoiceDetails.data;
						$scope.invoice.duedate_edit = new Date($scope.invoice.duedate_edit + ' 00:00');
						$scope.invoice.created_edit = new Date($scope.invoice.created_edit + ' 00:00');
						setTimeout(function () { InitMask() }, 2000);
					});
				} else {
					globals.mdToast('error', response.data.message);
					$scope.doing = false;
				}
			},
			function (response) {
				$scope.doing = false;
			}
		);
	};
	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (v) {
		v = v.toString().replace(/\D/g, "");
		v = new String(Number(v));
		var len = v.length;
		if (1 == len) {
			v = v.replace(/(\d)/, "0,0$1");
		} else if (2 == len) {
			v = v.replace(/(\d)/, "0,$1");
		} else if (len > 2) {
			v = v.replace(/(\d{2})$/, ',$1');
			if (len > 5) {
				var x = len - 5
					, er = new RegExp('(\\d{' + x + '})(\\d)');
				v = v.replace(er, '$1.$2');
			}
		}
		return v;
	}

	$http.get(BASE_URL + 'invoices/files/' + INVOICEID).then(function (Files) {
		$scope.files = Files.data;
		$scope.invoicesFiles = false;

		$scope.pageCount = function () {
			return Math.ceil($scope.files.length / $scope.itemsPerPage) - 1;
		};

		$scope.range = function () {
			var rangeSize = 6;
			var ps = [];
			var start;
			start = $scope.currentPage;

			if (start > $scope.pageCount() - rangeSize) {
				start = $scope.pageCount() - rangeSize + 1;
			}

			for (var i = start; i < start + rangeSize; i++) {
				if (i >= 0) {
					ps.push(i);
				}
			}
			return ps;
		};



		$scope.prevPage = function () {
			if ($scope.currentPage > 0) {
				$scope.currentPage--;
			}
		};

		$scope.nextPage = function () {
			if ($scope.currentPage < $scope.pageCount()) {
				$scope.currentPage++;
			}
		};

		$scope.setPage = function (n) {
			$scope.currentPage = n;
		};
		$scope.DisablePrevPage = function () {
			return $scope.currentPage === 0 ? "disabled" : "";
		};

		$scope.DisableNextPage = function () {
			return $scope.currentPage === $scope.pageCount() ? "disabled" : "";
		};

	});

	$scope.UploadFile = function (ev) {
		$mdDialog.show({
			templateUrl: 'addfile-template.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: ev
		});
	};




	$scope.uploading = false;
	$scope.uploadInvoiceFile = function () {
		$scope.uploading = true;
		var file = $scope.project_file;
		var uploadUrl = BASE_URL + 'invoices/add_file/' + INVOICEID;
		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {
			if (response.success == true) {
				globals.mdToast('success', response.message);
			} else {
				globals.mdToast('error', response.message);
			}

			$scope.invoicesFiles = true;
			$http.get(BASE_URL + 'invoices/files/' + INVOICEID).then(function (Files) {
				$scope.files = Files.data;
				$scope.invoicesFiles = false;
			});

			$scope.uploading = false;
			$mdDialog.hide();
		});
	};

	$scope.ViewFile = function (index, image) {
		$scope.file = $scope.files[index];
		$mdDialog.show({
			templateUrl: 'view_image.html',
			scope: $scope,
			preserveScope: true,
			targetEvent: $scope.file.id
		});
	};

	$scope.DeleteFile = function (id) {
		var confirm = $mdDialog.confirm()
			.title($scope.lang.delete_file_title)
			.textContent($scope.lang.delete_file_message)
			.ariaLabel($scope.lang.delete_file_title)
			.targetEvent(EXPENSEID)
			.ok($scope.lang.delete)
			.cancel($scope.lang.cancel);

		$mdDialog.show(confirm).then(function () {
			$http.post(BASE_URL + 'expenses/delete_file/' + id, config).then(
				function (response) {
					if (response.data.success == true) {
						showToast(NTFTITLE, response.data.message, ' success');
						$http.get(BASE_URL + 'expenses/files/' + EXPENSEID).then(function (Files) {
							$scope.files = Files.data;
						});
					} else {
						globals.mdToast('error', response.data.message);
					}
				},

				function (response) {
					console.log(response);
				}
			);
		}, function () {
			//
		});
	};

}
CiuisCRM.controller('Invoices_Controller', Invoices_Controller);
CiuisCRM.controller('Invoice_Controller', Invoice_Controller);