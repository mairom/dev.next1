function Deposits_Controller($scope, $compile, $http, $mdSidenav, $mdDialog, $q, $timeout, $filter) {

	"use strict";



	$scope.get_staff();

	$scope.depositsLoader = true;

	$scope.toggleFilter = buildToggler('ContentFilter');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.depositsLoader = true;

	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

		$scope.depositsLoader = false;

	});



	$scope.GetProduct = (function (search) {

		var deferred = $q.defer();

		$timeout(function () {

			deferred.resolve($scope.products);

		}, Math.random() * 500, false);

		return deferred.promise;

	});



	$scope.newdeposit = {

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

		$scope.newdeposit.items.push({

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

		$scope.newdeposit.items.splice(index, 1);

	};



	$scope.subtotal = function () {

		var subtotal = 0;

		angular.forEach($scope.newdeposit.items, function (item) {

			subtotal += item.quantity * item.price;

		});

		return subtotal.toFixed(2);

	};



	$scope.linediscount = function () {

		var linediscount = 0;

		angular.forEach($scope.newdeposit.items, function (item) {

			linediscount += ((item.discount) / 100 * item.quantity * item.price);

		});

		return linediscount.toFixed(2);

	};



	$scope.totaltax = function () {

		var totaltax = 0;

		angular.forEach($scope.newdeposit.items, function (item) {

			totaltax += ((item.tax) / 100 * item.quantity * item.price);

		});

		return totaltax.toFixed(2);

	};



	$scope.grandtotal = function () {

		var grandtotal = 0;

		angular.forEach($scope.newdeposit.items, function (item) {

			grandtotal += item.quantity * item.price + ((item.tax) / 100 * item.quantity * item.price) - ((item.discount) / 100 * item.quantity * item.price);

		});

		return grandtotal.toFixed(2);

	};



	$scope.today = new Date();



	$scope.close = function () {

		$mdSidenav('addNewDepositToggle').close();

		$mdSidenav('ContentFilter').close();

		$mdDialog.hide();

	};



	var deferred = $q.defer();

	$scope.deposit_list = {

		order: '',

		limit: 5,

		page: 1

	};

	$http.get(BASE_URL + 'deposits/get_deposits').then(function (Deposits) {

		$scope.deposits = Deposits.data;

		deferred.resolve();



		$scope.limitOptions = [5, 10, 15, 20];

		if($scope.deposits.length > 20 ) {

			$scope.limitOptions = [5, 10, 15, 20, $scope.deposits.length];

		}

		$scope.depositsLoader = false;

		

		$scope.search = {

			name: '',

		};

		// Filtered Datas

		$scope.filter = {};

		$scope.getOptionsFor = function (propName) {

			return ($scope.deposits || []).map(function (item) {

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

	});



	$scope.AddDeposit = function () {

		$scope.savingDeposit = true;

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



		var deposit_recurring;

		if ($scope.deposit_recurring == true) {

			deposit_recurring = '1';

		} else {

			deposit_recurring = '0';

		}

		var EndRecurring;

		if ($scope.EndRecurring) {

			EndRecurring = moment($scope.EndRecurring).format("YYYY-MM-DD 00:00:00");

		} else {

			EndRecurring = 'Invalid date';

		}

		if ($scope.newdeposit.date) {

			$scope.newdeposit.date = moment($scope.newdeposit.date).format("YYYY-MM-DD");

		}



		var internal = false;

		if ($scope.newdeposit.internal == true) {

			internal = true;

		}



		if (!$scope.newdeposit) {

			var dataObj = $.param({

				title: '',

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

				title: $scope.newdeposit.title,

				amount: $scope.newdeposit.amount,

				date: $scope.newdeposit.date,

				category: $scope.newdeposit.category,

				account: $scope.newdeposit.account,

				description: $scope.newdeposit.description,

				customer: $scope.newdeposit.customer,

				number: $scope.newdeposit.number,

				internal : internal,

				sub_total: $scope.subtotal,

				total_tax: $scope.totaltax,

				total: $scope.grandtotal,

				staff: $scope.newdeposit.staff,

				// START Recurring

				recurring: deposit_recurring,

				end_recurring: EndRecurring,

				recurring_type: $scope.recurring_type,

				recurring_period: $scope.recurring_period,

				// END Recurring

				items: $scope.newdeposit.items,

				totalItems: $scope.newdeposit.items.length

			});

		}

		var posturl = BASE_URL + 'deposits/create';

		$http.post(posturl, dataObj, config)

		.then(

			function (response) {

				$scope.savingDeposit = false;

				if (response.data.success == true) {

					window.location.href = BASE_URL + 'deposits/deposit/' + response.data.id;

				} else {

					$scope.savingDeposit = false;

					globals.mdToast('error', response.data.message);

				}

			},

			function (response) {

				$scope.savingDeposit = false;

				console.log(response);

			}

			);

	};	



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});

	

	$scope.depostisCatLoader = true;

	$http.get(BASE_URL + 'api/depositscategories').then(function (Categories) {

		$scope.categories = Categories.data;

		$scope.depostisCatLoader = false;



		$scope.NewCategory = function () {

			globals.createDialog($scope.lang.newcategory, $scope.lang.type_categoryname, $scope.lang.categoryname, '', $scope.lang.add, $scope.lang.cancel, 'deposits/add_category',  function(response) {

				if (response.success == true) {

					globals.mdToast('success', response.message);

					$http.get(BASE_URL + 'api/depositscategories').then(function (Categories) {

						$scope.categories = Categories.data;

					});

				} else {

					glocals.mdToast('error', response.message);

				}

				$http.get(BASE_URL + 'products/get_product_categories').then(function (Categories) {

					$scope.category = Categories.data;

				});

			});

		};



		$scope.UpdateCategory = function (index) {

			var Category = $scope.categories[index];

			globals.editDialog($scope.lang.update+' '+$scope.lang.category, $scope.lang.type_categoryname, $scope.lang.category+' '+$scope.lang.name, Category.name, Category.id, 'Save', 'Cancel', 'deposits/update_category/' + Category.id, function(response) {

				if (response.success == true) {

					globals.mdToast('success', response.message);

					$http.get(BASE_URL + 'api/depositscategories').then(function (Categories) {

						$scope.categories = Categories.data;

					});

				} else {

					globals.mdToast('error', response.message);

				}

			});

		};



		$scope.Remove = function (index) {

			var Category = $scope.categories[index];

			globals.deleteDialog($scope.lang.attention, $scope.lang.delete_category, Category.id, $scope.lang.doIt, $scope.lang.cancel, 'deposits/remove_category/' + Category.id, function(response) {

				if (response.success == true) {

					globals.mdToast('success', response.message);

					$http.get(BASE_URL + 'api/depositscategories').then(function (Categories) {

						$scope.categories = Categories.data;

					});

				} else {

					globals.mdToast('error', response.message);

				}

			});

		};		

	});

}



function Deposit_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout) {

	"use strict";



	$scope.Update = buildToggler('Update');

	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}

	$scope.close = function () {

		$mdSidenav('Update').close();

		$mdDialog.hide();

	};



	$http.get(BASE_URL + 'deposits/get_deposit/' + DEPOSITID).then(function (Deposit) {

		$scope.deposit = Deposit.data;

		if($scope.deposit.internal == false) {

			var cust = $scope.deposit.customername;

			var searchCustomer = $scope.deposit.customername?cust.split(' ')[0]:'';

			$scope.search_customers(searchCustomer);

		}

		$scope.savingDeposit = false;

		$scope.subtotal = function () {

			var subtotal = 0;

			angular.forEach($scope.deposit.items, function (item) {

				subtotal += item.quantity * item.price;

			});

			return subtotal.toFixed(2);

		};

		$scope.linediscount = function () {

			var linediscount = 0;

			angular.forEach($scope.deposit.items, function (item) {

				linediscount += ((item.discount) / 100 * item.quantity * item.price);

			});

			return linediscount.toFixed(2);

		};

		$scope.totaltax = function () {

			var totaltax = 0;

			angular.forEach($scope.deposit.items, function (item) {

				totaltax += ((item.tax) / 100 * item.quantity * item.price);

			});

			return totaltax.toFixed(2);

		};

		$scope.grandtotal = function () {

			var grandtotal = 0;

			angular.forEach($scope.deposit.items, function (item) {

				grandtotal += item.quantity * item.price + ((item.tax) / 100 * item.quantity * item.price) - ((item.discount) / 100 * item.quantity * item.price);

			});

			return grandtotal.toFixed(2);

		};



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

			$scope.deposit.items.push({

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

			var item = $scope.deposit.items[index];

			$http.post(BASE_URL + 'invoices/remove_item/' + item.id)

				.then(

					function (response) {

						console.log(response);

						$scope.deposit.items.splice(index, 1);

						$scope.deposit.balance = $scope.deposit.balance - item.total;

						$scope.amount = $scope.deposit.balance;

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.UpdateDeposit = function () {

			$scope.savingDeposit = true;

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

				var deposit_recurring;

				if ($scope.deposit_recurring == true) { 

					deposit_recurring = '1';

				} else {

					deposit_recurring = '0';

				}



				var EndRecurring;

				if ($scope.deposit.EndRecurring) {

					EndRecurring = moment($scope.deposit.EndRecurring).format("YYYY-MM-DD 00:00:00");

				} else {

					EndRecurring = 'Invalid date';

				}



				var internal = false;

				if ($scope.deposit.internal == true) {

					internal = true;

				}



				if ($scope.deposit.customer == '0') {

					$scope.deposit.customer = '';

				}



				if (!$scope.deposit) {

					var dataObj = $.param({

						title: '',

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

						title: $scope.deposit.title,

						amount: $scope.deposit.amount,

						date: moment($scope.deposit.date_edit).format("YYYY-MM-DD"),

						category: $scope.deposit.category,

						account: $scope.deposit.account,

						description: $scope.deposit.description,

						customer: $scope.deposit.customer,

						internal: internal,

						sub_total: $scope.subtotal,

						total_tax: $scope.totaltax,

						total: $scope.grandtotal,

						staff: $scope.deposit.staff_id,

						// START Recurring

						recurring_status: deposit_recurring,

						recurring: $scope.deposit.recurring_status,

						end_recurring: EndRecurring,

						recurring_type: $scope.deposit.recurring_type,

						recurring_period: $scope.deposit.recurring_period,

						recurring_id: $scope.deposit.recurring_id,

						// END Recurring

						items: $scope.deposit.items,

						totalItems: $scope.deposit.items.length

					});

				}

			var posturl = BASE_URL + 'deposits/update/' + DEPOSITID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						console.log(response);

						$scope.savingDeposit = false;

						if (response.data.success == true) {

							// $mdSidenav('Update').close();

							globals.mdToast('success', response.data.message);

							window.location.href = BASE_URL + 'deposits/deposit/' + response.data.id;

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						$scope.savingDeposit = false;

						console.log(response);

					}

				);

		};



		$http.get(BASE_URL + 'api/depositscategories').then(function (Categories) {

			$scope.categories = Categories.data;

		});



		$scope.Delete = function (index) {

			globals.deleteDialog(lang.attention, lang.delete_deposit, DEPOSITID, lang.doIt, lang.cancel, 'deposits/remove/' + DEPOSITID, function(response) {

				if (response.success == true) {

					window.location.href = BASE_URL + 'deposits';

				} else {

					globals.mdToast('error',response.message);

				}

			});

		};



		$scope.sendEmail = function() {

			$scope.sendingEmail = true;

			$http.post(BASE_URL + 'deposits/send_deposit_email/' + DEPOSITID)

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



		$scope.MarkAsReceived = function () {

			$http.post(BASE_URL + 'deposits/mark_as_received/' + DEPOSITID)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							window.location.href = BASE_URL + 'deposits';

						} else {

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.GeneratePDF = function(ev) {

			$mdDialog.show({

				templateUrl: 'generate-deposit.html',

				scope: $scope,

				preserveScope: true,

				targetEvent: ev

			});

		};



		$scope.CreatePDF = function() {

			$scope.PDFCreating = true;

			$http.post(BASE_URL + 'deposits/create_pdf/' + DEPOSITID)

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



		$scope.CloseModal = function () {

			$mdDialog.hide();

		};

	});



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});



	$http.get(BASE_URL + 'api/depositscategories').then(function (Epxensescategories) {

		$scope.depositscategories = Epxensescategories.data;

	});

}



CiuisCRM.controller('Deposits_Controller', Deposits_Controller);

CiuisCRM.controller('Deposit_Controller', Deposit_Controller);

