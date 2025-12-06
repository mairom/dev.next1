function Orders_Controller($scope, $http, $mdSidenav, $q, $timeout, $filter) {

	"use strict";



	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'order').then(function (custom_fields) {

		$scope.all_custom_fields = custom_fields.data;

		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {

			active: 'true',

		});

	});



	$http.get(BASE_URL + 'api/staff').then(function (Staff) {

		$scope.staff = Staff.data;

	});



	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

	});



	$http.get(BASE_URL + 'api/leads').then(function (Leads) {

		$scope.leads = Leads.data;

	});





	$scope.GetProduct = (function (search) {

		console.log(search);

		var deferred = $q.defer();

		$timeout(function () {

			deferred.resolve($scope.products);

		}, Math.random() * 500, false);

		return deferred.promise;

	});



	$scope.order = {

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

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

	$scope.add = function () {

		$scope.order.items.push({

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

		$scope.order.items.splice(index, 1);

	};



	$scope.subtotal = function () {

		var subtotal = 0;
		angular.forEach($scope.order.items, function (item) {
			subtotal += item.quantity * moneyEua(item.price);
		});

		return subtotal.toFixed(2);
	};



	$scope.linediscount = function () {
		var linediscount = 0;
		angular.forEach($scope.order.items, function (item) {
			linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));
		});

		return linediscount.toFixed(2);
	};



	$scope.totaltax = function () {

		var totaltax = 0;

		angular.forEach($scope.order.items, function (item) {

			totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

		});

		return totaltax.toFixed(2);

	};



	$scope.grandtotal = function () {

		var grandtotal = 0;

		angular.forEach($scope.order.items, function (item) {

			grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return grandtotal.toFixed(2);

	};

	var created = '', opentill = '';

	if ($scope.created) {

		created = moment($scope.created).format("YYYY-MM-DD");

	}

	if ($scope.opentill) {

		opentill = moment($scope.opentill).format("YYYY-MM-DD");

	}

	$scope.saveAll = function () {

		var dataObj = $.param({

			customer: $scope.customer,

			lead: $scope.lead,

			comment: $scope.comment,

			subject: $scope.subject,

			content: $scope.content,

			date: moment($scope.created).format("YYYY-MM-DD"),

			created: moment($scope.created).format("YYYY-MM-DD"),

			opentill: moment($scope.opentill).format("YYYY-MM-DD"),

			order_type: $scope.order_type,

			status: $scope.status,

			assigned: $scope.assigned,

			sub_total: $scope.subtotal,

			total_discount: $scope.linediscount,

			total_tax: $scope.totaltax,

			total: $scope.grandtotal,

			items: $scope.order.items,

			total_items: $scope.order.items.length,

		});

		var posturl = BASE_URL + 'orders/create';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					if (response.data.success == true) {

						window.location.href = BASE_URL + 'orders/order/' + response.data.id;

					} else {

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					console.log(response);

				}

			);

	};



	var deferred = $q.defer();

	$scope.order_list = {

		order: '',

		limit: 5,

		page: 1

	};

	$scope.promise = deferred.promise;

	$http.get(BASE_URL + 'orders/get_orders').then(function (Orders) {

		$scope.orders = Orders.data;

		deferred.resolve();



		$scope.limitOptions = [5, 10, 15, 20];

		if ($scope.orders.length > 20) {

			$scope.limitOptions = [5, 10, 15, 20, $scope.orders.length];

		}

		$scope.search = {

			subject: '',

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

			return ($scope.orders || []).map(function (item) {

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

}



function Order_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout) {

	"use strict";



	$http.get(BASE_URL + 'api/staff').then(function (Staff) {

		$scope.staff = Staff.data;

	});



	$scope.GeneratePDF = function (ev) {

		$mdDialog.show({

			templateUrl: 'generate-order.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	$scope.sendingEmail = false;

	$scope.sendEmail = function () {

		$scope.sendingEmail = true;

		$http.post(BASE_URL + 'orders/send_order_email/' + ORDERID)

			.then(

				function (response) {

					showToast(NTFTITLE, response.data.message, 'success');

					$scope.sendingEmail = false;

				},

				function (response) {

					$scope.sendingEmail = false;

				}

			);

	};



	$scope.CreatePDF = function () {

		$scope.PDFCreating = true;

		$http.post(BASE_URL + 'orders/create_pdf/' + ORDERID)

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



	$http.get(BASE_URL + 'orders/get_order/' + ORDERID).then(function (OrderDetails) {

		$scope.order = OrderDetails.data;

		var cust = $scope.order.customername;

		var searchCustomer = $scope.order.customername ? cust.split(' ')[0] : '';

		$scope.search_customers(searchCustomer);



		$scope.Convert = function (index) {

			globals.deleteDialog(lang.convert_title, lang.convert_text, ORDERID, lang.convert, lang.cancel, 'orders/convert_invoice/' + ORDERID, function (response) {

				if (response.success == true) {

					window.location.href = BASE_URL + 'invoices/invoice/' + response.id;

				} else {

					globals.mdToast('error', response.message);

				}

			});

		};



		$scope.Update = function () {

			window.location.href = BASE_URL + 'orders/update/' + ORDERID;

		};



		$scope.ViewOrder = function () {

			window.location.href = BASE_URL + 'share/order/' + $scope.order.token;

		};



		$scope.Delete = function (index) {

			globals.deleteDialog(lang.attention, lang.delete_order, ORDERID, lang.doIt, lang.cancel, 'orders/remove/' + ORDERID, function (response) {

				if (response.success == true) {

					globals.mdToast('success', response.message);

					window.location.href = BASE_URL + 'orders';

				} else {

					globals.mdToast('error', response.message);

				}

			});

		};



		$scope.MarkAs = function (id, name) {

			var dataObj = $.param({

				status_id: id,

				order_id: ORDERID,

				name: name,

			});

			var posturl = BASE_URL + 'orders/markas';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						console.log(response);

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

			angular.forEach($scope.order.items, function (item) {

				subtotal += item.quantity * moneyEua(moneyEua(item.price));

			});

			return subtotal.toFixed(2);

		};

		$scope.linediscount = function () {

			var linediscount = 0;

			angular.forEach($scope.order.items, function (item) {

				linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return linediscount.toFixed(2);

		};

		$scope.totaltax = function () {

			var totaltax = 0;

			angular.forEach($scope.order.items, function (item) {

				totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

			});

			return totaltax.toFixed(2);

		};

		$scope.grandtotal = function () {

			var grandtotal = 0;

			angular.forEach($scope.order.items, function (item) {

				grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return grandtotal.toFixed(2);

		};



		$http.get(BASE_URL + 'api/products').then(function (Products) {

			$scope.products = Products.data;

		});



		$http.get(BASE_URL + 'api/leads').then(function (Leads) {

			$scope.leads = Leads.data;

		});



		$scope.GetProduct = (function (search) {

			console.log(search);

			var deferred = $q.defer();

			$timeout(function () {

				deferred.resolve($scope.products);

			}, Math.random() * 500, false);

			return deferred.promise;

		});



		$scope.add = function () {

			$scope.order.items.push({

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

			var item = $scope.order.items[index];

			$http.post(BASE_URL + 'orders/remove_item/' + item.id)

				.then(

					function (response) {

						console.log(response);

						$scope.order.items.splice(index, 1);

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.ProType = $scope.order.order_type;



		$scope.saveAll = function () {

			var dataObj = $.param({

				customer: $scope.order.customer,

				lead: $scope.order.lead,

				comment: $scope.order.comment,

				subject: $scope.order.subject,

				content: $scope.order.content,

				date: moment($scope.order.created_edit).format("YYYY-MM-DD"),

				created: moment($scope.order.created_edit).format("YYYY-MM-DD"),

				opentill: moment($scope.order.opentill_edit).format("YYYY-MM-DD"),

				order_type: $scope.ProType,

				status: $scope.order.status,

				assigned: $scope.order.assigned,

				sub_total: $scope.subtotal,

				total_discount: $scope.linediscount,

				total_tax: $scope.totaltax,

				total: $scope.grandtotal,

				items: $scope.order.items,

			});

			var posturl = BASE_URL + 'orders/update/' + ORDERID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						console.log(response);

						window.location.href = BASE_URL + 'orders/order/' + response.data;

					},

					function (response) {

						console.log(response);

					}

				);

		};

	});



	$scope.ReminderForm = buildToggler('ReminderForm');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();



		};

	}

	$scope.close = function () {

		$mdSidenav('ReminderForm').close();

	};



	$scope.CloseModal = function () {

		$mdDialog.hide();

	};



	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

	});



	$http.get(BASE_URL + 'api/reminders_by_type/order/' + ORDERID).then(function (Reminders) {

		$scope.in_reminders = Reminders.data;

		$scope.AddReminder = function () {

			var dataObj = $.param({

				description: $scope.reminder_description,

				date: moment($scope.reminder_date).format("YYYY-MM-DD HH:mm:ss"),

				staff: $scope.reminder_staff,

				relation_type: 'order',

				relation: ORDERID,

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

		$scope.moneyEua = function (v) {
			var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
			return a;
		};

		$scope.formatReal = function (int) {
			return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
		}

		$scope.DeleteReminder = function (index) {

			var reminder = $scope.in_reminders[index];

			var dataObj = $.param({

				reminder: reminder.id

			});

			var posturl = BASE_URL + 'trivia/removereminder';

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



	$http.get(BASE_URL + 'api/notes/order/' + ORDERID).then(function (Notes) {

		$scope.notes = Notes.data;

		$scope.AddNote = function () {

			var dataObj = $.param({

				description: $scope.note,

				relation_type: 'order',

				relation: ORDERID,

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

							$http.get(BASE_URL + 'api/notes/order/' + ORDERID).then(function (Notes) {

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



CiuisCRM.controller('Orders_Controller', Orders_Controller);

CiuisCRM.controller('Order_Controller', Order_Controller);