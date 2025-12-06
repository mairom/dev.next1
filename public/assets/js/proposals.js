function Proposals_Controller($scope, $http, $mdSidenav, $q, $timeout, $filter, $element) {

	"use strict";



	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'proposal').then(function (custom_fields) {

		$scope.all_custom_fields = custom_fields.data;

		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {

			active: 'true',

		});

	});



	$scope.proposalsLoader = true;

	$scope.get_staff();



	$http.get(BASE_URL + 'api/products').then(function (Products) {

		$scope.products = Products.data;

		$scope.proposalsLoader = false;

	});



	$http.get(BASE_URL + 'api/leads').then(function (Leads) {

		$scope.leads = Leads.data;

		$scope.proposalsLoader = false;

	});





	$scope.GetProduct = (function (search) {

		console.log(search);

		var deferred = $q.defer();

		$timeout(function () {

			deferred.resolve($scope.products);

		}, Math.random() * 500, false);

		return deferred.promise;

	});



	$scope.proposal = {

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

		$scope.proposal.items.push({

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

		$scope.proposal.items.splice(index, 1);

	};



	$scope.subtotal = function () {

		var subtotal = 0;

		angular.forEach($scope.proposal.items, function (item) {

			subtotal += item.quantity * moneyEua(item.price);

		});

		return subtotal.toFixed(2);

	};



	$scope.linediscount = function () {

		var linediscount = 0;

		angular.forEach($scope.proposal.items, function (item) {

			linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return linediscount.toFixed(2);

	};



	$scope.totaltax = function () {

		var totaltax = 0;

		angular.forEach($scope.proposal.items, function (item) {

			totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

		});

		return totaltax.toFixed(2);

	};

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}


	$scope.grandtotal = function () {

		var grandtotal = 0;

		angular.forEach($scope.proposal.items, function (item) {

			grandtotal += item.quantity * moneyEua(item.price) + ((item.tax) / 100 * item.quantity * moneyEua(item.price)) - ((item.discount) / 100 * item.quantity * moneyEua(item.price));

		});

		return grandtotal.toFixed(2);

	};



	$scope.savingProposal = false;

	$scope.saveAll = function () {

		$scope.savingProposal = true;

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

		var created = '', date = '', duedate = '';

		if ($scope.created) {

			created = moment($scope.created).format("YYYY-MM-DD");

		}

		if ($scope.opentill) {

			duedate = moment($scope.opentill).format("YYYY-MM-DD");

		}

		var dataObj = $.param({

			customer: $scope.customer,

			lead: $scope.lead,

			comment: $scope.comment,

			subject: $scope.subject,

			content: $scope.content,

			date: created,

			opentill: duedate,

			proposal_type: $scope.proposal_type,

			status: $scope.status,

			assigned: $scope.assigned,

			sub_total: $scope.subtotal,

			total_discount: $scope.linediscount,

			total_tax: $scope.totaltax,

			total: $scope.grandtotal,

			items: $scope.proposal.items,

			total_items: $scope.proposal.items.length,

			custom_fields: $scope.tempArr,

		});

		var posturl = BASE_URL + 'proposals/create';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.savingProposal = false;

					if (response.data.success == true) {

						window.location.href = BASE_URL + 'proposals/proposal/' + response.data.proposal_id;

					} else {

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.savingProposal = false;

				}

			);

	};



	$scope.proposalsLoader = true;

	var deferred = $q.defer();

	$scope.proposal_list = {
		order: '',
		limit: 5,
		page: 1
	};

	$scope.promise = deferred.promise;

	$http.get(BASE_URL + 'api/proposals').then(function (Proposals) {

		$scope.proposals = Proposals.data;

		deferred.resolve();

		$scope.limitOptions = [5, 10, 15, 20];
		if($scope.proposals.length > 20 ) {
			$scope.limitOptions = [5, 10, 15, 20, $scope.proposals.length];
		}

		$scope.proposalsLoader = false;

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

			return ($scope.proposals || []).map(function (item) {

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



function Proposal_Controller($scope, $http, $mdSidenav, $mdDialog, $q, $timeout) {

	"use strict";



	$http.get(BASE_URL + 'api/staff').then(function (Staff) {

		$scope.staff = Staff.data;

	});



	$scope.GeneratePDF = function (ev) {

		$mdDialog.show({

			templateUrl: 'generate-proposal.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	$scope.sendEmail = function() {

		$scope.sendingEmail = true;

		$http.post(BASE_URL + 'proposals/send_proposal_email/' + PROPOSALID)

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



	$scope.CreatePDF = function () {

		$scope.PDFCreating = true;

		$http.post(BASE_URL + 'proposals/create_pdf/' + PROPOSALID)

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



	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'proposal/' + PROPOSALID).then(function (custom_fields) {

		$scope.custom_fields = custom_fields.data;

	});



	$http.get(BASE_URL + 'proposals/get_proposal/' + PROPOSALID).then(function (ProposalDetails) {

		$scope.proposal = ProposalDetails.data;

		var cust = $scope.proposal.customername;

		var searchCustomer = $scope.proposal.customername?cust.split(' ')[0]:'';

		$scope.search_customers(searchCustomer);



		$scope.Convert = function (index) {

			globals.deleteDialog(lang.convert_title, lang.convert_text, PROPOSALID, lang.convert, lang.cancel, 'proposals/convert_invoice/' + PROPOSALID, function(response) {

				if (response.success == true) {

					window.location.href = BASE_URL + 'invoices/invoice/' + response.id;

				} else {

					globals.mdToast('error',response.message);

				}

			});

		};



		$scope.Update = function () {

			window.location.href = BASE_URL + 'proposals/update/' + PROPOSALID;

		};



		$scope.ViewProposal = function () {

			window.location.href = BASE_URL + 'share/proposal/' + $scope.proposal.token;

		};



		$scope.Delete = function () {

			// Appending dialog to document.body to cover sidenav in docs app

			var confirm = $mdDialog.confirm()

				.title($scope.lang.delete_proposal)

				.textContent($scope.lang.proposal_remove_msg)

				.ariaLabel('Delete Proposal')

				.targetEvent(PROPOSALID)

				.ok($scope.lang.delete)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function () {

				$http.post(BASE_URL + 'proposals/remove/' + PROPOSALID, config)

					.then(

						function (response) {

							if(response.data.success == true) {

								globals.mdToast('success', response.data.message);

								window.location.href = BASE_URL + 'proposals';

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



		$scope.MarkAs = function (id, name) {

			var dataObj = $.param({

				status_id: id,

				proposal_id: PROPOSALID,

				name: name,

			});

			var posturl = BASE_URL + 'proposals/markas';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true) {

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

			angular.forEach($scope.proposal.items, function (item) {

				subtotal += item.quantity * moneyEua(item.price);

			});

			return subtotal.toFixed(2);

		};

		$scope.linediscount = function () {

			var linediscount = 0;

			angular.forEach($scope.proposal.items, function (item) {

				linediscount += ((item.discount) / 100 * item.quantity * moneyEua(item.price));

			});

			return linediscount.toFixed(2);

		};

		$scope.totaltax = function () {

			var totaltax = 0;

			angular.forEach($scope.proposal.items, function (item) {

				totaltax += ((item.tax) / 100 * item.quantity * moneyEua(item.price));

			});

			return totaltax.toFixed(2);

		};

		$scope.grandtotal = function () {

			var grandtotal = 0;

			angular.forEach($scope.proposal.items, function (item) {

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

			$scope.proposal.items.push({

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

			var item = $scope.proposal.items[index];

			$http.post(BASE_URL + 'proposals/remove_item/' + item.id)

				.then(

					function (response) {

						console.log(response);

						$scope.proposal.items.splice(index, 1);

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.ProType = $scope.proposal.proposal_type;

		$scope.savingProposal = false;

		$scope.saveAll = function () {

			$scope.savingProposal = true;

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

			var created = '', date = '', duedate = '';

			if ($scope.proposal.date_edit) {

				created = moment($scope.proposal.date_edit).format("YYYY-MM-DD");

			}

			if ($scope.proposal.opentill_edit) {

				duedate = moment($scope.proposal.opentill_edit).format("YYYY-MM-DD");

			}

			var dataObj = $.param({

				customer: $scope.proposal.customer,

				lead: $scope.proposal.lead,

				comment: $scope.proposal.comment,

				subject: $scope.proposal.subject,

				content: $scope.proposal.content,

				date: created,

				opentill: duedate,

				proposal_type: $scope.proposal.proposal_type,

				status: $scope.proposal.status,

				assigned: $scope.proposal.assigned,

				sub_total: $scope.subtotal,

				total_discount: $scope.linediscount,

				total_tax: $scope.totaltax,

				total: $scope.grandtotal,

				items: $scope.proposal.items,

				custom_fields: $scope.tempArr,

				total_items: $scope.proposal.items.length,

			});

			var posturl = BASE_URL + 'proposals/update/' + PROPOSALID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.savingProposal = false;

						if (response.data.success == true) {

							window.location.href = BASE_URL + 'proposals/proposal/' + response.data.id;

						} else {

							showToast(NTFTITLE, response.data.message, ' danger');

						}

					},

					function (response) {

						$scope.savingProposal = false;

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



	$http.get(BASE_URL + 'api/reminders_by_type/proposal/' + PROPOSALID).then(function (Reminders) {

		$scope.in_reminders = Reminders.data;

		$scope.AddReminder = function () {

			var dataObj = $.param({

				description: $scope.reminder_description,

				date: moment($scope.reminder_date).format("YYYY-MM-DD HH:mm:ss"),

				staff: $scope.reminder_staff,

				relation_type: 'proposal',

				relation: PROPOSALID,

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



	$http.get(BASE_URL + 'api/notes/proposal/' + PROPOSALID).then(function (Notes) {

		$scope.notes = Notes.data;

		$scope.AddNote = function () {

			var dataObj = $.param({

				description: $scope.note,

				relation_type: 'proposal',

				relation: PROPOSALID,

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

							$http.get(BASE_URL + 'api/notes/proposal/' + PROPOSALID).then(function (Notes) {

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

		$scope.moneyEua = function (v) {
			var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
			return a;
		};

		$scope.formatReal = function (int) {
			return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
		}

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



CiuisCRM.controller('Proposals_Controller', Proposals_Controller);

CiuisCRM.controller('Proposal_Controller', Proposal_Controller);