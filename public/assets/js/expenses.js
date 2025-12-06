function Expenses_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout, $filter) {
	"use strict";
	var data = new Date();
	var data2 = new Date(data.getFullYear(), data.getMonth() + 1, 0);

	$scope.filtros = [];
	$scope.filtros.data_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01' + ' 00:00');
	$scope.filtros.data_ate = new Date(data2.getFullYear() + '-' + (data2.getMonth() + 1) + '-' + (data2.getDate() < 10 ? '0' + data2.getDate() : data2.getDate()) + ' 00:00');
	$scope.indexExpense = 0;

	$scope.newCatg = [];

	$scope.dataGraph = dataGraph;


	var gerandoPdf = false;

	$scope.getCategory = function () {
		if ($scope.categories != null && $scope.newexpense.category != null) {
			return $scope.categories.find(a => a.id == $scope.newexpense.category);
		}
		return [];
	}


	$scope.geraPdf = function () {
		if (gerandoPdf) {
			return;
		}
		gerandoPdf = true;
		$('.filtrosLeads').hide();
		$('.btn_expenses').hide();

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
			$('.filtrosLeads').show();
			$('.btn_expenses').show();
		}).catch(function (error) {
			gerandoPdf = false;
			$('.filtrosLeads').show();
			$('.btn_expenses').show();
			console.log('oops, something went wrong!', error);
		});
	}


	$scope.filtrarDespesas = function () {
		var dataObj = $.param({
			data_de: moment($scope.filtros.data_de).format("YYYY-MM-DD"),
			data_ate: moment($scope.filtros.data_ate).format("YYYY-MM-DD"),
			listInvoice: listInvoice
		});

		$http.post(BASE_URL + 'expenses/get_expenses', dataObj, config).then(function (Expenses) {
			$scope.expenses = Expenses.data.expenses;
			$scope.limitOptions = [50, 100, 150, 200];
			$scope.dataGraph = Expenses.data.graph;
			initGraph($scope.dataGraph);

			if ($scope.expenses.length > 200) {
				$scope.limitOptions = [50, 100, 150, 200, $scope.expenses.length];
			}
		});

		$http.post(BASE_URL + 'api/expensescategories', dataObj, config).then(function (Epxensescategories) {
			$scope.expensescategories = Epxensescategories.data;
			$scope.categories = Epxensescategories.data;
		});
	};
	$scope.filtrarDespesas();



	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'expense').then(function (custom_fields) {
		$scope.all_custom_fields = custom_fields.data;
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	});

	$http.get(BASE_URL + 'api/get_frm_pagamentos').then(function (frm_pagamentos) {
		$scope.frm_pagamentos = frm_pagamentos.data;
	});

	$scope.get_staff();
	$scope.expensesLoader = true;

	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

		$scope.expensesLoader = false;

	});

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

	$scope.GetProduct = (function (search) {

		var deferred = $q.defer();

		$timeout(function () {

			deferred.resolve($scope.products);

		}, Math.random() * 500, false);

		return deferred.promise;

	});



	$scope.newexpense = {
		date: new Date(),
		items: [{

			name: new_item,

			product_id: 0,

			code: '',

			description: '',

			quantity: 1,

			unit: item_unit,

			price: 0,

			tax: 0,

			discount: 0,

		}]

	};



	$scope.add = function () {

		$scope.newexpense.items.push({

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

		$scope.newexpense.items.splice(index, 1);

	};



	$scope.subtotal = function () {

		var subtotal = 0;

		angular.forEach($scope.newexpense.items, function (item) {

			subtotal += item.quantity * moneyEua(item.price);

		});

		return subtotal.toFixed(2);

	};



	$scope.linediscount = function () {

		var linediscount = 0;

		angular.forEach($scope.newexpense.items, function (item) {

			linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return linediscount.toFixed(2);

	};



	$scope.totaltax = function () {

		var totaltax = 0;

		angular.forEach($scope.newexpense.items, function (item) {

			totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

		});

		return totaltax.toFixed(2);

	};



	$scope.grandtotal = function () {

		var grandtotal = 0;

		angular.forEach($scope.newexpense.items, function (item) {

			grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return grandtotal.toFixed(2);

	};



	$scope.today = new Date();



	$scope.NewExpense = buildToggler('NewExpense');
	$scope.toggleFilter = buildToggler('ContentFilter');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.close = function () {

		$('.md-select-menu-container.md-active.md-clickable').css('display', 'block');

		$mdSidenav('ContentFilter').close();

		$mdSidenav('CreateCustomer').close();

		$mdDialog.hide();

	};



	$scope.AddExpense = function () {
		$scope.savingExpense = true;
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

		var expense_recurring;
		if ($scope.expense_recurring == true) {
			expense_recurring = '1';
		} else {
			expense_recurring = '0';
		}

		var EndRecurring;
		if ($scope.EndRecurring) {
			EndRecurring = moment($scope.EndRecurring).format("YYYY-MM-DD 00:00:00");
		} else {
			EndRecurring = 'Invalid date';
		}
		var internal = false;

		//if ($scope.newexpense.internal == true) {
		//	internal = true;
		//}

		if (!$scope.newexpense) {

			var dataObj = $.param({
				//title: '',
				amount: '',
				date: '',
				category: '',
				account: '',
				description: '',
				customer: '',
				number: '',
				custom_fields: '',
			});

		} else {

			var dataObj = $.param({
				//title: $scope.newexpense.title,
				frm_pagamento: $scope.newexpense.id_forma_pgt,
				amount: $scope.newexpense.amount,
				date: moment($scope.newexpense.date).format("YYYY-MM-DD"),
				category: $scope.newexpense.category,
				account: $scope.newexpense.account,
				customer: $scope.newexpense.customer,
				custom_fields: $scope.tempArr,
				number: $scope.newexpense.number,
				internal: internal,
				sub_total: $scope.subtotal,
				total_discount: $scope.linediscount,
				total_tax: $scope.totaltax,
				total: $scope.grandtotal,
				staff: $scope.newexpense.staff,
				// START Recurring
				recurring: expense_recurring,
				end_recurring: EndRecurring,
				recurring_type: $scope.recurring_type,
				// END Recurring
				items: $scope.newexpense.items,
				totalItems: $scope.newexpense.items.length,
				duedate: moment($scope.newexpense.duedate).format("YYYY-MM-DD"),
				pago: $scope.newexpense.pago
			});

		}

		var posturl = BASE_URL + 'expenses/create';
		$http.post(posturl, dataObj, config).then(
			function (response) {
				var types = response.data.type;
				$scope.savingExpense = false;

				if (response.data.success == true) {
					//showToast(NTFTITLE, response.data.message, ' success');
					$mdSidenav('NewExpense').close();
					window.location.href = BASE_URL + 'expenses/receipt/' + response.data.id;

				} else {
					showToast(NTFTITLE, response.data.message, ' danger');
				}

			},

			function (response) {

				$scope.savingExpense = false;

				console.log(response);

			}

		);

	};



	var deferred = $q.defer();

	$scope.expense_list = {

		order: '',

		limit: 50,

		page: 1

	};





	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});

	$http.get(BASE_URL + 'api/vendors').then(function (Vendors) {
		$scope.all_vendors = Vendors.data;
	});

	$scope.salvaCategory = function () {
		var dataObj = $.param({
			name: $scope.newCatg.name,
			id_vendor: $scope.newCatg.id_vendor,
		});

		let url = BASE_URL + 'expenses/add_category';
		if ($scope.newCatg.id != null) {
			url = BASE_URL + 'expenses/update_category/' + $scope.newCatg.id;
		}

		$http.post(url, dataObj, config).then(
			function (response) {
				if (response.data.success == true) {
					globals.mdToast('success', response.message);
					$('#modalCategoria').modal('hide');
					$http.get(BASE_URL + 'api/expensescategories').then(function (Categories) {
						$scope.categories = Categories.data;
					});
				}
			}
		);

	}


	$scope.NewCategory = function () {
		$scope.newCatg = [];
		$scope.newCatg.com_fornecedor = 1;
		$('#modalCategoria').modal('show');
		/*
			globals.createDialog($scope.lang.newcategory, $scope.lang.type_categoryname, $scope.lang.categoryname, '', $scope.lang.add, $scope.lang.cancel, 'expenses/add_category', function (response) {
				if (response.success == true) {
					globals.mdToast('success', response.message);
				} else {
					globals.mdToast('error', response.message);
				}
				$http.get(BASE_URL + 'api/expensescategories').then(function (Categories) {
					$scope.categories = Categories.data;
				});
			});
		*/
	};



	$scope.UpdateCategory = function (index) {
		$('#modalCategoria').modal('show');
		$scope.newCatg = $scope.categories[index];
		$scope.newCatg.com_fornecedor = $scope.categories[index].id_vendor != null ? 1 : 0;
	};



	$scope.Remove = function (index) {

		var Category = $scope.categories[index];

		globals.deleteDialog($scope.lang.attention, $scope.lang.delete_category, Category.id, $scope.lang.doIt, $scope.lang.cancel, 'expenses/remove_category/' + Category.id, function (response) {

			if (response.success == true) {

				globals.mdToast('success', response.message);

				$http.get(BASE_URL + 'api/expensescategories').then(function (Categories) {

					$scope.categories = Categories.data;

				});

			} else {

				globals.mdToast('error', response.message);

			}

		});

	};


}



function Expense_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout, fileUpload) {

	"use strict";
	$scope.excluirDespesas = 0;

	$http.get(BASE_URL + 'api/get_frm_pagamentos').then(function (frm_pagamentos) {
		$scope.frm_pagamentos = frm_pagamentos.data;
	});


	$scope.Update = buildToggler('Update');

	$scope.get_staff();



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.close = function () {
		$mdDialog.hide();
	};



	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'expense/' + EXPENSEID).then(function (custom_fields) {

		$scope.custom_fields = custom_fields.data;

	});



	$scope.expensesLoader = true;

	$http.get(BASE_URL + 'expenses/get_expense/' + EXPENSEID).then(function (Expense) {

		$scope.expense = Expense.data;

		$scope.expenseOriginal = JSON.parse(JSON.stringify(Expense.data));

		$scope.expense.date_edit = new Date(Expense.data.date_edit + ' 00:00');
		$scope.expense.duedate = new Date(Expense.data.duedate + ' 00:00');

		setTimeout(function () { InitMask() }, 2000);

		var cust = $scope.expense.customername;

		var searchCustomer = $scope.expense.customername ? cust.split(' ')[0] : '';

		$scope.search_customers(searchCustomer);

		$scope.expensesLoader = false;



		$scope.subtotal = function () {

			var subtotal = 0;

			angular.forEach($scope.expense.items, function (item) {

				subtotal += item.quantity * moneyEua(item.price);

			});

			return subtotal.toFixed(2);

		};

		$scope.linediscount = function () {

			var linediscount = 0;

			angular.forEach($scope.expense.items, function (item) {

				linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return linediscount.toFixed(2);

		};

		$scope.totaltax = function () {

			var totaltax = 0;

			angular.forEach($scope.expense.items, function (item) {

				totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

			});

			return totaltax.toFixed(2);

		};

		$scope.grandtotal = function () {

			var grandtotal = 0;

			angular.forEach($scope.expense.items, function (item) {

				grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return grandtotal.toFixed(2);

		};



		$http.get(BASE_URL + 'api/products').then(function (Products) {

			$scope.products = Products.data;

		});



		$scope.GetProduct = (function (search) {

			console.log(search);

			var deferred = $q.defer();
			$timeout(function () {
				deferred.resolve($scope.products);
			}, Math.random() * 500, false);

			//deferred.promise.value

			console.log(deferred.promise.value)

			return deferred.promise;
		});



		$scope.add = function () {

			$scope.expense.items.push({

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

			var item = $scope.expense.items[index];

			$http.post(BASE_URL + 'invoices/remove_item/' + item.id)

				.then(

					function (response) {

						console.log(response);

						$scope.expense.items.splice(index, 1);

						$scope.expense.balance = $scope.expense.balance - item.total;

						$scope.amount = $scope.expense.balance;

					},

					function (response) {

						console.log(response);

					}

				);

		};

		$scope.UpdateExpense = function () {

			if ($scope.expenseOriginal.recurring_status != $scope.expense.recurring_status) {
				if (!$scope.expense.recurring_status) {
					var confirm = $mdDialog.confirm()
						.title('Você deseja excluir as despesas futuras?')
						.textContent('Caso exista despesas futuras as mesma serão excluidas!')
						.ariaLabel('1')
						.ok($scope.lang.delete)
						.cancel($scope.lang.cancel);

					$mdDialog.show(confirm).then(function () {
						$scope.excluirDespesas = 1;
						$scope.UpdateExpense();
					}, function () {
						//
					});
					return;
				}
			}

			if ($scope.expense.recurring_status) {
				if ($scope.expense.recurring_type != $scope.expenseOriginal.recurring_type
					|| $scope.EndRecurring != $scope.expenseOriginal.EndRecurring) {
					var confirm = $mdDialog.confirm()
						.title('Você deseja editar as despesas futuras?')
						.textContent('Com isso irá editar todas as despesas futuras para o novo parametro')
						.ariaLabel('1')
						.ok($scope.lang.update)
						.cancel($scope.lang.cancel);

					$mdDialog.show(confirm).then(function () {
						$scope.excluirDespesas = 1;
						$scope.UpdateExpense();
					}, function () {
						//
					});
					return;
				}
			}

			$scope.savingExpense = true;

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

			var expense_recurring;
			if ($scope.expense_recurring == true) {
				expense_recurring = '1';
			} else {
				expense_recurring = '0';
			}



			var EndRecurring;
			if ($scope.expense.EndRecurring) {
				EndRecurring = moment($scope.expense.EndRecurring).format("YYYY-MM-DD 00:00:00");
			} else {
				EndRecurring = 'Invalid date';
			}



			var internal = false;

			//if ($scope.expense.internal == true) {
			//	internal = true;
			//}

			if ($scope.expense.customer == '0') {
				$scope.expense.customer = '';
			}



			if (!$scope.expense) {

				var dataObj = $.param({
					//title: '',
					amount: '',
					date: '',
					category: '',
					account: '',
					description: '',
					customer: '',
					number: '',
					custom_fields: '',

				});

			} else {

				var dataObj = $.param({

					//title: $scope.expense.title,
					frm_pagamento: $scope.expense.id_forma_pgt,
					amount: $scope.expense.amount,
					date: moment($scope.expense.date_edit).format("YYYY-MM-DD"),
					category: $scope.expense.category,
					account: $scope.expense.account,
					customer: $scope.expense.customer,
					custom_fields: $scope.tempArr,
					number: $scope.expense.number,
					internal: internal,
					sub_total: $scope.subtotal,
					total_discount: $scope.linediscount,
					total_tax: $scope.totaltax,
					total: $scope.grandtotal,
					staff: $scope.expense.staff_id,

					// START Recurring

					recurring_status: expense_recurring,
					recurring: $scope.expense.recurring_status,
					end_recurring: EndRecurring,
					recurring_type: $scope.expense.recurring_type,
					recurring_id: $scope.expense.recurring_id,

					// END Recurring

					items: $scope.expense.items,
					totalItems: $scope.expense.items.length,
					duedate: moment($scope.expense.duedate).format("YYYY-MM-DD"),
					pago: $scope.expense.pago,


					excluirDespesas: $scope.excluirDespesas

				});

			}

			var posturl = BASE_URL + 'expenses/update/' + EXPENSEID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.savingExpense = false;

						if (response.data.success == true) {

							//showToast(NTFTITLE, response.data.message, ' success');

							window.location.href = BASE_URL + 'expenses/receipt/' + response.data.id;

						} else {

							showToast(NTFTITLE, response.data.message, ' danger');

						}

					},

					function (response) {

						$scope.savingExpense = false;

						console.log(response);

					}

				);

		};



		$scope.sendEmail = function () {

			$scope.sendingEmail = true;

			$http.post(BASE_URL + 'expenses/send_expense_email/' + EXPENSEID)

				.then(

					function (response) {

						showToast(NTFTITLE, lang.email_sent_success, 'success');

						$scope.sendingEmail = false;

					},

					function (response) {

						$scope.sendingEmail = false;

						showToast(NTFTITLE, response, 'success');

						console.log(response);

					}

				);

		};



		$scope.GeneratePDF = function (ev) {

			$mdDialog.show({

				templateUrl: 'generate-expense-summary.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: ev

			});

		};



		$scope.CreatePDF = function () {

			$scope.PDFCreating = true;

			$http.post(BASE_URL + 'expenses/create_pdf/' + EXPENSEID)

				.then(

					function (response) {

						console.log(response)

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



		$http.get(BASE_URL + 'api/expensescategories').then(function (Categories) {
			$scope.categories = Categories.data;
		});


		$scope.getCategory = function () {
			if ($scope.categories != null && $scope.expense.category != null) {
				return $scope.categories.find(a => a.id == $scope.expense.category);
			}
			return [];
		}



		$scope.Delete = function (index) {

			globals.deleteDialog(lang.attention, lang.delete_expense, EXPENSEID, lang.doIt, lang.cancel, 'expenses/remove/' + EXPENSEID, function (response) {

				if (response.success == true) {

					globals.mdToast('success', response.message);

					window.location.href = BASE_URL + 'expenses';

				} else {

					globals.mdToast('error', response.message);

				}

			});

		};



		$scope.Convert = function (index) {

			globals.deleteDialog(lang.convert_title, lang.convert_text, EXPENSEID, lang.convert, lang.cancel, 'expenses/convert/' + EXPENSEID, function (response) {

				if (response.success == true) {

					window.location.href = BASE_URL + 'invoices/invoice/' + response.id;

				} else {

					globals.mdToast('error', response.message);

				}

			});

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
	$scope.uploadExpenseFile = function () {
		$scope.uploading = true;
		var file = $scope.project_file;
		var uploadUrl = BASE_URL + 'expenses/add_file/' + EXPENSEID;
		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {
			if (response.success == true) {
				globals.mdToast('success', response.message);
			} else {
				globals.mdToast('error', response.message);
			}

			$scope.expensesFiles = true;
			$http.get(BASE_URL + 'expenses/files/' + EXPENSEID).then(function (Files) {
				$scope.files = Files.data;
				$scope.expensesFiles = false;
			});

			$scope.uploading = false;
			$mdDialog.hide();
		});
	};



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

	$scope.expensesFiles = true;

	$http.get(BASE_URL + 'expenses/files/' + EXPENSEID).then(function (Files) {

		$scope.files = Files.data;
		$scope.itemsPerPage = 6;
		$scope.currentPage = 0;
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

		$scope.DisablePrevPage = function () {
			return $scope.currentPage === 0 ? "disabled" : "";
		};

		$scope.nextPage = function () {
			if ($scope.currentPage < $scope.pageCount()) {
				$scope.currentPage++;
			}
		};

		$scope.DisableNextPage = function () {

			return $scope.currentPage === $scope.pageCount() ? "disabled" : "";

		};

		$scope.setPage = function (n) {
			$scope.currentPage = n;
		};

		$scope.pageCount = function () {
			return Math.ceil($scope.files.length / $scope.itemsPerPage) - 1;
		};

		$scope.expensesFiles = false;
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
				$http.post(BASE_URL + 'expenses/delete_file/' + id, config)
					.then(
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

	});





	$http.get(BASE_URL + 'api/expensescategories').then(function (Epxensescategories) {
		$scope.expensescategories = Epxensescategories.data;
	});

}

CiuisCRM.controller('Expenses_Controller', Expenses_Controller);

CiuisCRM.controller('Expense_Controller', Expense_Controller);
