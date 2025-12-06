function Purchases_Controller($scope, $http, $mdSidenav, $q, $timeout) {

	"use strict";

	var data = new Date();
	var data2 = new Date(data.getFullYear(), data.getMonth() + 1, 0);

	$scope.filtros = [];
	$scope.filtros.data_de = new Date(data.getFullYear() + '-' + (data.getMonth() + 1) + '-01' + ' 00:00');
	$scope.filtros.data_ate = new Date(data2.getFullYear() + '-' + (data2.getMonth() + 1) + '-' + (data2.getDate() < 10 ? '0' + data2.getDate() : data2.getDate()) + ' 00:00');
	var gerandoPdf = false;
	$scope.created = new Date();

	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

	});

	$scope.geraPdf = function () {
		if (gerandoPdf) {
			return;
		}
		gerandoPdf = true;
		$('.filtrosLeads').hide();
		$('.filtrosLeads3').hide();
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
			$('.filtrosLeads3').show();
		}).catch(function (error) {
			gerandoPdf = false;
			$('.filtrosLeads').show();
			$('.filtrosLeads3').show();
			console.log('oops, something went wrong!', error);
		});
	}

	$http.get(BASE_URL + 'api/vendors').then(function (Vendors) {
		$scope.all_vendors = Vendors.data;
	});



	$scope.GetProduct = (function (search) {
		var deferred = $q.defer();
		$timeout(function () {
			deferred.resolve($scope.products);
		}, Math.random() * 500, false);
		return deferred.promise;
	});

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

	$scope.purchase = {

		items: [{

			name: 'New',

			product_id: 0,

			code: '',

			description: '',

			quantity: 1,

			unit: 'Unit',

			price: 0,

			tax: 0,

			discount: 0,

		}]

	};



	$scope.add = function () {

		$scope.purchase.items.push({

			name: 'New',

			product_id: 0,

			code: '',

			description: '',

			quantity: 1,

			unit: 'Unit',

			price: 0,

			tax: 0,

			discount: 0,

		});

	};



	$scope.remove = function (index) {

		$scope.purchase.items.splice(index, 1);

	};



	$scope.subtotal = function () {

		var subtotal = 0;

		angular.forEach($scope.purchase.items, function (item) {

			subtotal += item.quantity * moneyEua(item.price);

		});

		return subtotal.toFixed(2);

	};



	$scope.linediscount = function () {

		var linediscount = 0;

		angular.forEach($scope.purchase.items, function (item) {

			linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return linediscount.toFixed(2);

	};



	$scope.totaltax = function () {

		var totaltax = 0;

		angular.forEach($scope.purchase.items, function (item) {

			totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

		});

		return totaltax.toFixed(2);

	};



	$scope.grandtotal = function () {

		var grandtotal = 0;

		angular.forEach($scope.purchase.items, function (item) {

			grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return grandtotal.toFixed(2);

	};



	$scope.saveAll = function () {



		var purchase_recurring;

		if ($scope.purchase_recurring == true) {

			purchase_recurring = '1';

		} else {

			purchase_recurring = '0';

		}



		var EndRecurring;

		$scope.savingPurchase = true;

		if ($scope.EndRecurring) {

			EndRecurring = moment($scope.EndRecurring).format("YYYY-MM-DD 00:00:00");

		} else {

			EndRecurring = 'Invalid date';

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

			vendor: $scope.vendor,

			created: $scope.created,

			duedate: $scope.duedate,

			datepayment: $scope.datepayment,

			account: $scope.account,

			//	duenote: $scope.duenote,

			//	serie: $scope.serie,

			no: $scope.no,

			sub_total: $scope.subtotal,

			total_discount: $scope.linediscount,

			total_tax: $scope.totaltax,

			tax_code: $scope.tax_code,

			total: $scope.grandtotal,

			status: $scope.purchase_status,

			// START Recurring

			recurring: purchase_recurring,
			end_recurring: EndRecurring,
			recurring_type: $scope.recurring_type,
			recurring_period: $scope.recurring_period,

			// END Recurring

			items: $scope.purchase.items,

			totalItems: $scope.purchase.items.length,
		});

		var posturl = BASE_URL + 'Purchases/create';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					if (response.data.success == true) {

						window.location.href = BASE_URL + 'purchases/purchase/' + response.data.id;

					} else {

						$scope.savingPurchase = false;

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.savingPurchase = false;

				}

			);

	};



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});


	$scope.purchase_list = {

		order: '',

		limit: 50,

		page: 1

	};


	$scope.filtrarCompras = function () {
		var dataObj = $.param({
			data_de: moment($scope.filtros.data_de).format("YYYY-MM-DD"),
			data_ate: moment($scope.filtros.data_ate).format("YYYY-MM-DD"),
			fornecedor: $scope.filtros.fornecedor
		});

		$http.post(BASE_URL + 'purchases/get_purchases', dataObj, config).then(function (Purchases) {
			$scope.purchases = Purchases.data.purchases;
			$scope.dataGraph = Purchases.data.dataGraph;
			$scope.limitOptions = [50, 100, 150, 200];
			if ($scope.purchases != null && $scope.purchases.length > 200) {
				$scope.limitOptions = [50, 100, 150, 200, $scope.purchases.length];
			}
			$scope.purchasesLoader = false;
			initGraph($scope.dataGraph);
		});




	};
	$scope.filtrarCompras();


	$scope.purchasesLoader = true;
	$scope.toggleFilter = buildToggler('ContentFilter');
	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};
	}


}



function Purchase_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout) {

	"use strict";
	$scope.excluirCompras = 0;


	$scope.GeneratePDF = function (ev) {

		$mdDialog.show({

			templateUrl: 'generate-purchase.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};

	$scope.get_vendors();



	$scope.sendEmail = function () {

		$scope.sendingEmail = true;

		$http.post(BASE_URL + 'purchases/send_purchase_email/' + PURCHASEID)

			.then(

				function (response) {

					console.log(response)

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



	$scope.CreatePDF = function () {

		$scope.PDFCreating = true;

		$http.post(BASE_URL + 'purchases/create_pdf/' + PURCHASEID)

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



	$scope.Delete = function (index) {

		console.log("sdfs");

		globals.deleteDialog(lang.attention, lang.delete_meesage, PURCHASEID, lang.doIt, lang.cancel, 'purchases/remove/' + PURCHASEID, function (response) {

			if (response.success == true) {

				globals.mdToast('success', response.message);

				window.location.href = BASE_URL + 'purchases';

			} else {

				globals.mdToast('error', response.message);

			}

		});

	};



	$scope.purchaseLoader = true;

	$http.get(BASE_URL + 'purchases/get_purchase/' + PURCHASEID).then(function (PurchaseDetails) {

		$scope.purchase = PurchaseDetails.data;
		$scope.purchaseOriginal = JSON.parse(JSON.stringify(PurchaseDetails.data));
		$scope.purchaseLoader = false;
		$scope.purchase.duedate_edit = new Date(PurchaseDetails.data.duedate_edit + ' 00:00');
		$scope.purchase.created_edit = new Date(PurchaseDetails.data.created_edit + ' 00:00');
		$scope.purchase.purchase_status = $scope.purchase.status_id == 2 ? true : false;

		setTimeout(function () { InitMask() }, 2000);

		$scope.MarkAsDraft = function () {

			$http.post(BASE_URL + 'purchases/mark_as_draft/' + PURCHASEID)

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

			$http.post(BASE_URL + 'purchases/mark_as_cancelled/' + PURCHASEID)

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

			angular.forEach($scope.purchase.items, function (item) {

				subtotal += item.quantity * moneyEua(item.price);

			});

			return subtotal.toFixed(2);

		};

		$scope.linediscount = function () {

			var linediscount = 0;

			angular.forEach($scope.purchase.items, function (item) {

				linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return linediscount.toFixed(2);

		};

		$scope.totaltax = function () {

			var totaltax = 0;

			angular.forEach($scope.purchase.items, function (item) {

				totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

			});

			return totaltax.toFixed(2);

		};

		$scope.grandtotal = function () {

			var grandtotal = 0;

			angular.forEach($scope.purchase.items, function (item) {

				grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return grandtotal.toFixed(2);

		};



		$scope.totalpaid = function () {

			return $scope.purchase.payments.reduce(function (total, payment) {

				return total + (payment.amount * 1 || 0);

			}, 0);

		};



		$scope.amount = $scope.purchase.balance;



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

			$scope.purchase.items.push({

				name: 'New',

				product_id: 0,

				code: '',

				description: '',

				quantity: 1,

				unit: 'Unit',

				price: 0,

				tax: 0,

				discount: 0,

			});

		};



		$scope.remove = function (index) {

			var item = $scope.purchase.items[index];

			$http.post(BASE_URL + 'purchases/remove_item/' + item.id)

				.then(

					function (response) {

						console.log(response);

						$scope.purchase.items.splice(index, 1);

						$scope.purchase.balance = $scope.purchase.balance - item.total;

						$scope.amount = $scope.purchase.balance;

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.saveAll = function () {
			console.log($scope.purchaseOriginal);
			if (($scope.purchaseOriginal.recurring_status != null ? $scope.purchaseOriginal.recurring_status : 0) != $scope.purchase.recurring_status) {
				if (!$scope.purchase.recurring_status) {
					var confirm = $mdDialog.confirm()
						.title('Você deseja excluir as compras futuras?')
						.textContent('Caso exista compras futuras as mesma serão excluidas!')
						.ariaLabel('1')
						.ok($scope.lang.delete)
						.cancel($scope.lang.cancel);

					$mdDialog.show(confirm).then(function () {
						$scope.excluirCompras = 1;
						$scope.saveAll();
					}, function () {
						//
					});
					return;
				}
			}

			if ($scope.purchase.recurring_status) {
				if ($scope.purchase.recurring_type != $scope.purchaseOriginal.recurring_type
					|| $scope.purchase.recurring_endDate != $scope.purchaseOriginal.recurring_endDate) {
					var confirm = $mdDialog.confirm()
						.title('Você deseja editar as compras futuras?')
						.textContent('Com isso irá editar todas as compras futuras para o novo parametro')
						.ariaLabel('1')
						.ok($scope.lang.update)
						.cancel($scope.lang.cancel);

					$mdDialog.show(confirm).then(function () {
						$scope.excluirCompras = 1;
						$scope.saveAll();
					}, function () {
						//
					});
					return;
				}
			}

			var EndRecurring;

			if ($scope.purchase.recurring_endDate) {

				EndRecurring = moment($scope.purchase.recurring_endDate).format("YYYY-MM-DD 00:00:00");

			} else {

				EndRecurring = 'Invalid date';

			}

			if ($scope.purchase.created_edit) {
				$scope.purchase.created = moment($scope.purchase.created_edit).format("YYYY-MM-DD");
			}

			if ($scope.purchase.duedate_edit) {
				$scope.purchase.duedate_edit = moment($scope.purchase.duedate_edit).format("YYYY-MM-DD");
			}

			var dataObj = $.param({
				//vendor: $scope.purchase.vendor,
				nm_vendor: $scope.purchase.nm_vendor,
				created: $scope.purchase.created,
				duedate: $scope.purchase.duedate_edit,
				//	duenote: $scope.purchase.duenote,
				//	serie: $scope.purchase.serie,
				no: $scope.purchase.no,
				sub_total: $scope.subtotal,
				total_discount: $scope.linediscount,
				total_tax: $scope.totaltax,
				total: $scope.grandtotal,
				tax_code: $scope.tax_code,
				// START Recurring
				recurring_status: $scope.purchase.recurring_status,
				recurring: $scope.purchase.recurring_status,
				end_recurring: EndRecurring,
				recurring_type: $scope.purchase.recurring_type,
				recurring_period: $scope.purchase.recurring_period,
				recurring_id: $scope.purchase.recurring_id,
				// END Recurring
				items: $scope.purchase.items,
				staff_isActive: $scope.isActive,
				excluirCompras: $scope.excluirCompras,
				status: $scope.purchase.purchase_status,
			});


			var posturl = BASE_URL + 'purchases/update/' + PURCHASEID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							showToast(NTFTITLE, response.data.message, ' success');

							window.location.href = BASE_URL + 'purchases/purchase/' + response.data.id;

						} else {

							showToast(NTFTITLE, response.data.message, ' danger');

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};

	});

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

	$scope.UpdateInvoice = function (id) {

		window.location.href = BASE_URL + 'purchases/update/' + id;

	};







	function buildToggler(navID) {
		return function () {
			$mdSidenav(navID).toggle();
		};
	}
	$scope.RecordPayment = buildToggler('RecordPayment');


	$scope.close = function () {

		$mdSidenav('RecordPayment').close();

	};



	$scope.CloseModal = function () {

		$mdDialog.hide();

	};



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});



	$scope.AddPayment = function () {

		var dataObj = $.param({

			date: moment($scope.date).format("YYYY-MM-DD HH:mm:ss"),

			balance: $scope.purchase.balance - $scope.amount,

			total: $scope.amount,

			not: $scope.not,

			account: $scope.account,

			purchasetotal: $scope.grandtotal,

			vendor: PURCHASVENDOR,

			purchase: PURCHASEID,

		});

		var posturl = BASE_URL + 'purchases/record_payment';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					if (response.data.success == true) {

						$mdSidenav('RecordPayment').close();

						globals.mdToast('success', response.data.message);

						$scope.purchase.balance = $scope.purchase.balance - $scope.amount;

						$http.get(BASE_URL + 'purchases/get_purchase/' + PURCHASEID).then(function (PurchaseDetails) {

							$scope.purchase = PurchaseDetails.data;
							$scope.purchaseOriginal = JSON.parse(JSON.stringify(PurchaseDetails.data));

							$scope.purchase.duedate_edit = new Date(PurchaseDetails.data.duedate_edit + ' 00:00');
							$scope.purchase.created_edit = new Date(PurchaseDetails.data.created_edit + ' 00:00');

							setTimeout(function () { InitMask() }, 2000);
						});

					} else {

						globals.mdToast('error', response.data.message);

					}



				},

				function (response) {

					console.log(response);

				}

			);

	};

}



CiuisCRM.controller('Purchases_Controller', Purchases_Controller);

CiuisCRM.controller('Purchase_Controller', Purchase_Controller);