var CiuisCRM = angular.module('Ciuis', ['Ciuis.datepicker', 'ngMaterial', 'ngMaterialDatePicker', 'currencyFormat', 'md.data.table', 'ui.sortable', 'ngSanitize'])

	.config(function ($mdGestureProvider) {

		"use strict";

		$mdGestureProvider.skipClickHijack();

	}).config(function ($mdAriaProvider) {

		"use strict";

		$mdAriaProvider.disableWarnings();

	}).config(function ($mdDateLocaleProvider) {
		$mdDateLocaleProvider.formatDate = function (date) {
			var m = moment(date);
			return m.isValid() ? m.format('DD.MM.YYYY') : '';
		}
	});



var globals = {};



var config = {

	headers: {

		'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

	}

};

globals.config = {

	headers: {

		'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'

	}

};



function Ciuis_Controller($scope, $http, $mdSidenav, fileUpload, $filter, $interval, $mdDialog, $element, $mdToast, $log) {

	"use strict";



	$scope.lang;





	$scope.date = new Date();

	$scope.tick = function () {

		$scope.clock = Date.now();

	};

	$scope.tick();

	$interval($scope.tick, 1000);

	var curDate = new Date();

	var y = curDate.getFullYear();

	var m = curDate.getMonth() + 1;

	if (m < 10) {

		m = '0' + m;

	}

	var d = curDate.getDate();

	if (d < 10) {

		d = '0' + d;

	}

	$scope.curDate = y + '-' + m + '-' + d;

	$scope.appurl = BASE_URL;

	$scope.UPIMGURL = UPIMGURL;

	//$scope.IMAGESURL = BASE_URL + 'assets/img/';

	//$scope.SETFILEURL = BASE_URL + 'uploads/ciuis_settings/';

	$scope.ONLYADMIN = SHOW_ONLY_ADMIN;

	$scope.USERNAMEIN = LOGGEDINSTAFFNAME;

	$scope.USERAVATAR = LOGGEDINSTAFFAVATAR;

	$scope.activestaff = ACTIVESTAFF;

	$scope.cur_symbol = CURRENCY;

	$scope.cur_code = CURRENCY;

	$scope.cur_lct = LOCATE_SELECTED;


	$http.get(BASE_URL + 'api/settings').then(function (Settings) {



		$scope.settings = Settings.data;
		var setapp = $scope.settings;
		$scope.applogo = (setapp.logo);
		$scope.staff_timezone = $scope.settings.default_timezone;

		if (Settings.data.newnotification) {
			let ultimaNaoVisualizada = Settings.data.notifications.find(a => a.markread == 0);

			if (ultimaNaoVisualizada != null) {
				if (localStorage.idUltimoAlerta == null || parseFloat(localStorage.idUltimoAlerta) < ultimaNaoVisualizada.id_notifications) {
					localStorage.idUltimoAlerta = ultimaNaoVisualizada.id_notifications;

					if (ultimaNaoVisualizada.alerta != null) {
						var audio = new Audio(BASE_URL + '/uploads/anexos/' + ultimaNaoVisualizada.alerta);
						audio.addEventListener('canplaythrough', function () {
							audio.play();
						});
					}
					showToast('Novo alerta', ultimaNaoVisualizada.detail, 'success');
				}
				setTimeout(a => {
					$('.triangle_aviso').hide();
				}, 5000)

			} else {
				$('.triangle_aviso').hide();
			}
		}


		if (Settings.data.settings_ia != null
			&& Settings.data.settings_ia.connected == "1"
			&& window.location.href.split('/')[3] != "whatsapp"
		) {

			$scope.urlApi = 'apiwhats.ageup.pro:3000';
			let ws = new WebSocket('wss://' + $scope.urlApi);
			ws.onopen = () => {
				console.log('Conectado ao WebSocket');

				ws.send(JSON.stringify({ type: 'get-nao-lidas', number: Settings.data.settings_ia.number }));
			};

			ws.onclose = function () {
				console.log('WebSocket fechado');
			}

			ws.onmessage = (event) => {
				const data = JSON.parse(event.data);

				if (data.total != null) {
					$scope.settings.settings_ia.newnotificationWhatsapp = data.total;
					localStorage.newnotificationWhatsapp = data.total;
				} else if (new_message) {
					let total = $scope.settings.settings_ia.newnotificationWhatsapp != null ? $scope.settings.settings_ia.newnotificationWhatsapp : 0;

					$scope.settings.settings_ia.newnotificationWhatsapp = total++;
					localStorage.newnotificationWhatsapp = total++;
				}

			}
			/*
			if (window.location.href.split('/')[3] == "panel") {
				var dataObj = {
					number: Settings.data.settings_ia.number
				};
				$http.post("https://apiwhats.ageup.pro:3000/get-nao-lidas", dataObj).then((response) => {
					$scope.settings.settings_ia.newnotificationWhatsapp = response.data.total;
					localStorage.newnotificationWhatsapp = response.data.total;
				}, (response) => { });
			} else if (window.location.href.split('/')[3] == "whatsapp") {
				$scope.settings.settings_ia.newnotificationWhatsapp = 0;
				localStorage.newnotificationWhatsapp = 0;
			} else {
				$scope.settings.settings_ia.newnotificationWhatsapp = localStorage.newnotificationWhatsapp;
			}
			*/

		}

	});



	$http.get(BASE_URL + 'api/user').then(function (Userman) {

		$scope.user = Userman.data;



		if ($scope.user.appointment_availability === '1') {

			$scope.appointment_availability = true;

		} else {

			$scope.appointment_availability = false;

		}



		$http.get(BASE_URL + 'api/lang/' + $scope.user.language).then(function (Language) {

			$scope.lang = Language.data;

		});



		$scope.ChangeAppointmentAvailability = function (id, value) {

			$http.post(BASE_URL + 'staff/appointment_availability/' + id + '/' + value)

				.then(

					function (response) {

						console.log(response);

					},

					function (response) {

						console.log(response);

					}

				);

		};

	});



	$scope.goToLink = function (url, blank = false) {


		if (blank) {
			window.open(
				BASE_URL + url,
				'_blank'
			);
		} else {
			window.location.href = BASE_URL + url;
		}

	};



	$scope.ChangeLanguage = function (lang) {

		$http.get(BASE_URL + 'api/lang/' + lang).then(function (Language) {

			$scope.lang = Language.data;

		});

	};



	$scope.get_appointments = function () {

		$http.get(BASE_URL + 'api/appointments').then(function (appointments) {

			$scope.dashboard_appointments = appointments.data;

		});

	};



	$scope.get_customers = function () {

		$http.get(BASE_URL + 'api/customers').then(function (Customers) {

			$scope.all_customers = Customers.data;

		});

	};



	$scope.get_vendors = function () {

		$http.get(BASE_URL + 'api/vendors').then(function (Vendors) {

			$scope.vendors = Vendors.data;

		});

	};



	$scope.clearSearchTerm = function () {

		$scope.searchTerm = '';

	};



	$element.find('input').on('keydown', function (ev) {

		ev.stopPropagation();

	});



	$scope.search_customers = function (q) {

		if (q.length > 0) {

			$http.get(BASE_URL + 'api/search_customers/' + q).then(function (Customers) {

				$scope.all_customers = Customers.data;

			});

		} else {

			$scope.all_customers = [];

		}

	};



	$scope.get_staff = function () {

		$http.get(BASE_URL + 'api/staff').then(function (Staff) {

			$scope.staff = Staff.data;

		});

	};



	$scope.get_meetings = function () {

		$http.get(BASE_URL + 'api/meetings').then(function (Meetings) {

			$scope.meetings = Meetings.data;

		});

	};



	// $http.get(BASE_URL + 'api/projects').then(function (Projects) {

	// 	$scope.projects = Projects.data;

	// });



	// $http.get(BASE_URL + 'api/staff').then(function (Staff) {

	// 	$scope.staff = Staff.data;

	// });



	// $http.get(BASE_URL + 'api/appointments').then(function (appointments) {

	// 	$scope.dashboard_appointments = appointments.data;

	// });



	// $http.get(BASE_URL + 'api/meetings').then(function (Meetings) {

	// 	$scope.meetings = Meetings.data;

	// });



	// $http.get(BASE_URL + 'api/customers').then(function (Customers) {

	// 	$scope.all_customers = Customers.data;

	// });



	// $http.get(BASE_URL + 'api/contacts').then(function (Contacts) {

	// 	$scope.all_contacts = Contacts.data;

	// });



	// $http.get(BASE_URL + 'api/departments').then(function (Departments) {

	// 	$scope.departments = Departments.data;

	// });



	// $http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {

	// 	$scope.settings_detail = Settings.data;

	// });



	globals.get_stats = function () {

		$http.get(BASE_URL + 'api/stats').then(function (Stats) {

			$scope.stats = Stats.data;

		});

	};



	globals.get_countries = function () {

		$http.get(BASE_URL + 'api/countries').then(function (Countries) {

			$scope.countries = Countries.data;

		});

	};



	globals.get_departments = function () {

		$http.get(BASE_URL + 'api/departments').then(function (Departments) {

			$scope.departments = Departments.data;

		});

	};



	// Global dialog for create, you can use this for any delete dialog popup.

	// eg: globals.deleteDialog(title, content, id, ok_text, cancel_text, api, function(reponse){}

	globals.deleteDialog = function (title, content, id, ok_text, cancel_text, api, callback) {

		var confirm = $mdDialog.confirm()

			.title(title)

			.textContent(content)

			.ariaLabel(title)

			.targetEvent(id)

			.ok(ok_text)

			.cancel(cancel_text);

		$mdDialog.show(confirm).then(function () {

			$http.post(BASE_URL + api, config)

				.then(

					function (response) {

						callback(response.data);

					},

					function (response) {

						callback(response.data);

					}

				);

		}, function () {

		});

	};



	// Global dialog for edit, it'll be useful only when your db column name is 'name'

	// eg: globals.editDialog(title, content, placeholder, value, event, ok_text, cancel_text, api, function(reponse){}

	globals.editDialog = function (title, content, placeholder, value, event, ok_text, cancel_text, api, callback) {

		var confirm = $mdDialog.prompt()

			.title(title)
			.textContent(content)
			.placeholder(placeholder)
			.ariaLabel(placeholder)
			.initialValue(value)
			.targetEvent(event)
			.required(true)
			.ok(ok_text)
			.cancel(cancel_text);

		$mdDialog.show(confirm).then(function (result) {
			var dataObj = $.param({
				name: result,
			});

			$http.post(BASE_URL + api, dataObj, config).then(
				function (response) {
					response.data.dialog = result;
					callback(response.data);
				},
				function (response) {
					response.data.dialog = result;
					callback(response.data);
				}
			);

		}, function () {

		});

	};



	// Global dialog for create, it'll be useful only when your db column name is 'name'

	// eg: globals.createDialog(title, content, placeholder, event, ok_text, cancel_text, api, function(reponse){}

	globals.createDialog = function (title, content, placeholder, event, ok_text, cancel_text, api, callback, dataPost = [{}]) {

		var confirm = $mdDialog.prompt()
			.title(title)
			.textContent(content)
			.placeholder(placeholder)
			.ariaLabel(placeholder)
			.targetEvent(event)
			.required(true)
			.ok(ok_text)
			.cancel(cancel_text);

		$mdDialog.show(confirm).then(function (result) {

			var arr = {};
			arr['name'] = result;
			$.each(dataPost, function (i, row) {
				arr[i] = row;
			})
			var dataObj = $.param(arr);
			$http.post(BASE_URL + api, dataObj, config)
				.then(
					function (response) {
						callback(response.data);
					},
					function (response) {
						callback(response.data);
					}
				);

		}, function () {

		});

	};



	function getEventTypes() {

		$http.get(BASE_URL + 'api/eventtypes').then(function (Data) {

			$scope.eventtypes = Data.data;

		});

	}



	$scope.OpenMenu = function () {

		$('#mobile-menu').show();

	};



	// type = success or error

	// message = your message, that you want to display in toast

	// eg: globals.mdToast('success', 'Timer has been deleted successfully');

	globals.mdToast = function (type, message, time = 4000) {

		$mdToast.show({

			template: '<md-toast class="md-toast ' + type + '">' + message + '</md-toast>',

			hideDelay: time,

			position: 'top center'

		});

	};



	$scope.neweventtype = false;



	$scope.EventForm = buildToggler('EventForm');

	$scope.SetOnsiteVisit = buildToggler('SetOnsiteVisit');

	$scope.Notifications = buildToggler('Notifications');

	$scope.open_messages = buildToggler('Mensagens_chat');

	$scope.Todo = buildToggler('Todo');

	$scope.Profile = buildToggler('Profile');

	$scope.PickUpTo = buildToggler('PickUpTo');

	$scope.CreateChat = buildToggler('CreateChat');
	$scope.CHAT_ID = null;
	$scope.reply_chat = [];
	var ultimaVerificacao = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");

	$http.get(BASE_URL + 'api/chats').then(function (Chats) {
		$scope.chats = Chats.data;
	});

	setInterval(a => {
		$http.post(BASE_URL + 'chats/chats_getNews', $.param({
			id: $scope.CHAT_ID,
			ultimaVerificacao
		}), config).then(function (response) {
			$scope.chats = response.data.chats;

			if (response.data.chatAtual.result == 1) {
				response.data.chatAtual.replies.map(a => {
					$scope.chatAtual.replies.push(a);
				});
				$('#chat_form').animate({
					scrollTop: $('#chat_form').prop("scrollHeight")
				}, 500);

				$('.notify .heartbit').css('border', '5px solid #ff00009e');
				$('.notify .point').css('background-color', '#ff00009e');
			}
			console.log($scope.chatAtual)
			ultimaVerificacao = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");
		});
	}, 100000);

	$http.get(BASE_URL + 'api/chats').then(function (Chats) {
		$scope.chats = Chats.data;
	});

	$scope.setChat = function (chat) {
		$scope.close();
		if (chat == 0) {
			$scope.CHAT_ID = 0;
			$scope.chatAtual = [];
			$scope.chatAtual.replies = [];
			$scope.chatAtual.details = [];
			$scope.chatAtual.details.id = 0;

		} else {
			if ($scope.CHAT_ID != chat.id) {
				$scope.CHAT_ID = chat.id;
				chat.msg_nova = 0;
				$scope.get_chat(chat.id);
			}

		}

		setTimeout(a => {
			toggleFab();
		}, 200);


	}

	$scope.get_chat = function (id) {
		$http.get(BASE_URL + 'chats/get_chat/' + id).then(function (chats) {
			$scope.chatAtual = chats.data;
		});
	};



	$scope.checkEnter = function (event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			$scope.replyToChat();
		}
	}


	$scope.replyToChat = function () {
		if ($scope.reply_chat.message.length == 0) {
			return;
		}

		let msg = $scope.reply_chat.message;
		$scope.reply_chat.message = '';
		$scope.processando_chat = true;

		if ($scope.CHAT_ID == 0) {
			var dataObj = $.param({
				message: msg,
			});
			$scope.chatAtual.replies.push({ message: msg, date: '', send_staff: ACTIVESTAFF });

			$http.post(BASE_URL + 'chats/reply_suporte', dataObj, config).then(function (data) {
				$scope.chatAtual.replies.push({ message: data.data.response, date: '', send_staff: 0 });
				$("#chat_form").animate({ scrollTop: $(document).height() }, 3000);
				$scope.processando_chat = false;
			});

		} else {
			var dataObj = {
				message: msg,
				file: $scope.reply_chat.attachment
			};
			var uploadUrl = BASE_URL + 'chats/reply/' + $scope.CHAT_ID;
			fileUpload.uploadFileWithData(dataObj, uploadUrl, function (response) {
				if (response.success == true) {
					$('#chooseFile').val('');

					$scope.reply_chat.file = null;
					$http.get(BASE_URL + 'chats/get_chat/' + $scope.CHAT_ID).then(function (ChatDetails) {
						$scope.chatAtual = ChatDetails.data;
					});
				}
			});
		}


	};

	$scope.departLoad = false;
	$scope.atualiza_departament = function () {
		if (!$scope.departLoad) {
			globals.get_departments();
			$scope.departLoad = true;

			$http.get(BASE_URL + 'api/staff/0').then(function (Staff) {
				$scope.users = Staff.data;
			});

		}
	};

	$scope.createChat = function () {
		$scope.uploading = true;
		if (!$scope.chat) {
			var dataObj = {
				subject: '',
				user: '',
				department: '',
				priority: '',
				message: '',
				file: ''
			};
		} else {
			var dataObj = {
				subject: $scope.chat.subject,
				user: $scope.chat.user,
				department: $scope.chat.department,
				priority: $scope.chat.priority,
				message: $scope.chat.message,
				file: $scope.chat.chat_attachment
			};
		}

		var uploadUrl = BASE_URL + 'chats/create/';
		fileUpload.uploadFileWithData(dataObj, uploadUrl, function (response) {
			if (response.success == true) {
				$scope.close();
				globals.mdToast('success', response.message);
				$scope.chat = [];
				$http.get(BASE_URL + 'api/chats').then(function (Chats) {
					$scope.chats = Chats.data;
					$scope.chatsLoader = false;
				});
			} else {
				globals.mdToast('error', response.message);
			}
			$scope.uploading = false;
		});
	};

	$scope.get_notifications = function () {

		$scope.loadingnotification = true;

		$http.get(BASE_URL + 'api/notifications').then(function (Notifications) {

			$scope.notifications = Notifications.data;

			$scope.loadingnotification = false;

		});

	};



	$scope.get_todo = function () {

		$scope.loadingtodo = true;

		$http.get(BASE_URL + 'api/donetodos').then(function (Donetodos) {

			$scope.tododone = Donetodos.data;

		});

		$http.get(BASE_URL + 'api/todos').then(function (Todos) {

			$scope.todos = Todos.data;

			$scope.loadingtodo = false;

		});

	};



	function buildToggler(navID) {

		return function () {
			$("html, body").scrollTop(0);
			$mdSidenav(navID).toggle();

		};

	}



	function openEventModal() {

		$mdSidenav('EventForm').toggle();

	}



	$scope.ManageEventTypes = function (ev) {

		getEventTypes();

		$mdSidenav('EventForm').close();

		$mdSidenav('ManageEventTypes').toggle();

	};



	$scope.closeEventTypes = function (ev) {

		$mdSidenav('ManageEventTypes').close();

		$mdSidenav('EventForm').toggle();

	};



	$scope.EventForm = function (ev) {

		getEventTypes();

		$mdSidenav('EventForm').toggle();

	};



	$scope.CloseEventForm = function (ev) {

		$mdSidenav('EventForm').close();

	};



	$scope.changeEventType = function (ev) {

		if ($scope.event_ntf_type == 'email') {

			if ($scope.eventType) {

				if ($scope.eventType.public == '1') {

					$scope.send_to_all = true;

				} else {

					$scope.send_to_all = false;

				}

			} else {

				$scope.send_to_all = false;

			}

		} else {

			$scope.send_to_all = false;

		}

	};



	$scope.changeEventTypes = function (ev) {

		if ($scope.event_ntf_type == 'email') {

			if ($scope.event_reminder) {

				if ($scope.eventType) {

					if ($scope.eventType.public == '1') {

						$scope.send_to_all = true;

					} else {

						$scope.send_to_all = false;

					}

				} else {

					$scope.send_to_all = false;

				}

			} else {

				$scope.send_to_all = false;

			}

		} else {

			$scope.send_to_all = false;

		}

	};



	$scope.searchNav = function () {

		$mdSidenav('searchNav').toggle();

		$scope.searchInputMsg = 1;

	};

	$scope.openAviso = function (notification) {
		$scope.notificationModal = notification;
		$('#notificationModal').modal("show");
		$scope.notificationModal.markread = 1;
		$http.get(BASE_URL + 'api/marcaLido/' + notification.id_notifications).then(function (data) {
		});
	}


	$scope.closeModal = function () {

		$('#notificationModal').modal("hide");
	}

	$scope.close = function () {

		$mdSidenav('SetOnsiteVisit').close();

		$mdSidenav('Notifications').close();

		$mdSidenav('Mensagens_chat').close();

		$mdSidenav('CreateChat').close();

		$mdSidenav('Todo').close();

		$mdSidenav('Profile').close();

		$mdSidenav('PickUpTo').close();

		$mdSidenav('searchNav').close();

		$mdSidenav('taskTimer').close();


		$mdDialog.hide();

		$('#mobile-menu').hide();

	};



	$scope.markAllAsRead = function () {

		var dataObj = $.param({});

		var posturl = BASE_URL + 'api/mark_read_ntf';

		$http.post(posturl, dataObj, config).then(function (response) {

			$http.get(BASE_URL + 'api/notifications').then(function (Notifications) {

				$scope.notifications = Notifications.data;

			});

			$scope.stats.tbs = '0';

		}, function (error) { }

		);

	};



	$scope.addingEventType = false;

	$scope.AddEventType = function (action) {

		$scope.addingEventType = true;

		if (!$scope.event_type) {

			var dataObj = $.param({

				name: '',

				color: '',

				public: '',

			});

		} else {

			var dataObj = $.param({

				name: $scope.event_type.name,

				color: $scope.event_type.color,

				public: +($scope.event_type.public ? $scope.event_type.public : false),

			});

		}

		var posturl = BASE_URL + 'api/add_eventtype';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.addingEventType = false;

					if (response.data.success == true) {

						showToast(NTFTITLE, response.data.message, 'success');

						getEventTypes();

						$scope.event_type.name = '';

						$scope.neweventtype = false;

					} else {

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.addingEventType = false;

				}

			);

	};



	$scope.DeleteEventType = function (id) {

		var confirm = $mdDialog.confirm()

			.title($scope.lang.delete + ' ' + $scope.lang.event + ' ' + $scope.lang.type)

			.textContent($scope.lang.event_type_delete_meesage)

			.ariaLabel('Delete Converted Leads')

			.targetEvent(id)

			.ok($scope.lang.delete)

			.cancel($scope.lang.cancel);

		$mdDialog.show(confirm).then(function () {

			$http.post(BASE_URL + 'api/remove_eventtype/' + id, config)

				.then(

					function (response) {

						getEventTypes();

						showToast(NTFTITLE, response.data.message, ' success');

					},

					function (response) {

						console.log(response);

					}

				);

		}, function () {

		});

	};



	function resetSearch() {

		$scope.searchProposals = [];

		$scope.searchLeads = [];

		$scope.searchInvoices = [];

		$scope.searchStaff = [];

		$scope.searchCustomers = [];

		$scope.searchLeads = [];

		$scope.searchExpenses = [];

		$scope.searchProjects = [];

		$scope.searchProducts = [];

		$scope.searchTickets = [];

		$scope.searchTasks = [];

		$scope.searchOrders = [];

		$scope.searchProposals.length = 0;

		$scope.searchInvoices.length = 0;

		$scope.searchLoader = 0;

		$scope.searchStaff.length = 0;

		$scope.searchCustomers.length = 0;

		$scope.searchLeads.length = 0;

		$scope.searchExpenses.length = 0;

		$scope.searchProjects.length = 0;

		$scope.searchProducts.length = 0;

		$scope.searchTickets.length = 0;

		$scope.searchTasks.length = 0;

		$scope.searchOrders.length = 0;

		$scope.searchVendors = [];

		$scope.searchPurchases = [];

		$scope.searchDeposits = [];

		$scope.searchVendors.length = 0;

		$scope.searchPurchases.length = 0;

		$scope.searchDeposits.length = 0;

	}

	$scope.searchInput = function (input) {

		if (input.length > 1) {

			$scope.searchInputMsg = 0;

			var dataObj = $.param({

				input: input

			});

			var posturl = BASE_URL + 'api/search';

			$scope.searchResult = 0;

			$scope.searchLoader = 1;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						$scope.searchLoader = 0;

						$scope.searchResult = 0;

						if (response.data.length > 0) {

							$scope.searchResult = 0;

							for (var i = 0; i < response.data.length; i++) {

								if (response.data[i].type == 'proposals') {

									$scope.searchProposals = response.data[i].result;

								}

								if (response.data[i].type == 'invoices') {

									$scope.searchInvoices = response.data[i].result;

								}

								if (response.data[i].type == 'staff') {

									$scope.searchStaff = response.data[i].result;

								}

								if (response.data[i].type == 'customers') {

									$scope.searchCustomers = response.data[i].result;

								}

								if (response.data[i].type == 'leads') {

									$scope.searchLeads = response.data[i].result;

								}

								if (response.data[i].type == 'expenses') {

									$scope.searchExpenses = response.data[i].result;

								}

								if (response.data[i].type == 'projects') {

									$scope.searchProjects = response.data[i].result;

								}

								if (response.data[i].type == 'products') {

									$scope.searchProducts = response.data[i].result;

								}

								if (response.data[i].type == 'tickets') {

									$scope.searchTickets = response.data[i].result;

								}

								if (response.data[i].type == 'tasks') {

									$scope.searchTasks = response.data[i].result;

								}

								if (response.data[i].type == 'orders') {

									$scope.searchOrders = response.data[i].result;

								}

								if (response.data[i].type == 'vendors') {

									$scope.searchVendors = response.data[i].result;

								}

								if (response.data[i].type == 'purchases') {

									$scope.searchPurchases = response.data[i].result;

								}

								if (response.data[i].type == 'deposits') {

									$scope.searchDeposits = response.data[i].result;

								}

							}

						} else {

							$scope.searchResult = 1;

							resetSearch();

						}

					}, function () {

						$scope.searchResult = 0;

					});

		} else if (input == '') {

			$scope.searchInputMsg = 1;

			$scope.searchResult = 0;

			resetSearch();

		} else {

			$scope.searchResult = 1;

			$scope.searchInputMsg = 0;

			resetSearch();

		}

	};



	$scope.getTimersList = function () {

		$scope.getTimer();

		$mdSidenav('taskTimer').toggle();

	};



	function getMenuTimer() {

		$scope.getTimer();

	}



	function countdown(element, seconds) {

		var el = $('#' + element);

		var interval = setInterval(function () {

			var h = Math.floor(seconds / 3600);

			var m = Math.floor(seconds % 3600 / 60);

			var s = Math.floor(seconds % 3600 % 60);

			h = (h < 10) ? ('0' + h) : h;

			m = (m < 10) ? ('0' + m) : m;

			s = (s < 10) ? ('0' + s) : s;

			let counter = h + ':' + m + ':' + s;

			$('#' + element).text(counter);

			seconds++;

		}, 1000);

	}







	$scope.getTimer = function () {

		$scope.timer = {};

		$scope.timer.loading = true;

		$scope.timer.start = false;

		$scope.timer.stop = false;

		$scope.timer.found = false;

		var dataObj = $.param({});

		var posturl = BASE_URL + 'api/get_timer';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.timers = response.data;

					var interval = setInterval(function () {

						clearInterval(interval);

					});

					for (let i = 0; i <= $scope.timers.length - 1; i++) {

						let timer = $scope.timers[i];

						countdown('totalTime' + timer.id, timer.seconds);

					}

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.startTimer = function (action, id = null) {



		if (action == 'stop') {

			$scope.stopTimerWithTask('stop', id);

		} else {

			var dataObj = $.param({

				status: action,

			});

			var posturl = BASE_URL + 'api/timer/';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {

								$scope.settings_detail = Settings.data;

							});

							getMenuTimer();

						}

					},

					function (response) {

						console.log(response);

					}

				);

		}

	};



	$scope.stopTimer = function (id) {

		$mdDialog.show({

			templateUrl: 'stopTimer.html',

			parent: angular.element(document.body),

			clickOutsideToClose: false,

			fullscreen: false,

			escapeToClose: false,

			controller: NewTeamDialogController,

		});

	};



	$scope.DeleteMenuTimer = function (id) {

		var confirm = $mdDialog.confirm()

			.title($scope.lang.delete + ' ' + $scope.lang.timer)

			.textContent($scope.lang.timer_delete_meesage)

			.ariaLabel('Delete Converted Leads')

			.targetEvent(id)

			.ok($scope.lang.delete)

			.cancel($scope.lang.cancel);

		$mdDialog.show(confirm).then(function () {

			$http.post(BASE_URL + 'api/delete_timer/' + id, config)

				.then(

					function (response) {

						getMenuTimer();

						globals.mdToast('success', response.data.message);

						$http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {

							$scope.settings_detail = Settings.data;

						});

					},

					function (response) {

						console.log(response);

					}

				);

		}, function () {

		});

	};



	$scope.stopTimerWithTask = function (action, id) {

		$mdDialog.show({

			templateUrl: 'timerTasks.html',

			parent: angular.element(document.body),

			clickOutsideToClose: false,

			fullscreen: false,

			escapeToClose: false,

			controller: NewTeamDialogController,

		});



		function NewTeamDialogController($scope, $mdDialog) {

			$http.post(BASE_URL + 'api/get_timer_data/' + id, config)

				.then(

					function (response) {

						$scope.timerData = response.data;

					});



			$scope.taskTimer = {};

			$scope.taskTimer.loader = true;

			if (action == 'assign') {

				$scope.taskTimer.assign = true;

				$scope.taskTimer.stop = false;

			}

			if (action == 'stop') {

				$scope.taskTimer.assign = false;

				$scope.taskTimer.stop = true;

			}

			$http.get(BASE_URL + 'api/get_open_tasks').then(function (Tasks) {

				$scope.timerTasks = Tasks.data;

				$scope.taskTimer.loader = false;

			});



			$scope.searchTerm;

			$scope.clearSearchTerm = function () {

				$scope.searchTerm = '';

			};

			$element.find('input').on('keydown', function (ev) {

				ev.stopPropagation();

			});

			//

			$scope.stopTimer = {};

			$scope.stopTimer.task = globals.task_id;

			$scope.stopTimer.note = globals.timer_note;

			$scope.close = function () {

				$mdDialog.hide();

			};



			$scope.savingTimer = false;

			$scope.timerStopConfirm = function (id) {

				$scope.savingTimer = true;

				if (!$scope.timerData) {

					var dataObj = $.param({

						status: 'stop',

						//id: id,

						task: '',

						note: '',

						action: action

					});

				} else {

					var dataObj = $.param({

						status: 'stop',

						//id: id,

						task: $scope.timerData.task_id,

						note: $scope.timerData.note,

						action: action

					});

				}

				var posturl = BASE_URL + 'api/timer/' + id;

				$http.post(posturl, dataObj, config)

					.then(

						function (response) {

							$scope.savingTimer = false;

							if (response.data.success == true) {

								globals.mdToast('success', response.data.message);

								$http.get(BASE_URL + 'api/settings_detail').then(function (Settings) {

									$scope.settings_detail = Settings.data;

								});

								$mdDialog.hide();

								if (action == 'stop') {

									$('#timerStarted').removeClass('text-success');

									$('#timerStarted').addClass('text-muted');

									$('.timer-section').css('display', 'none');

									$('.task-timer').css('display', 'flex');

									$('.task-timer').removeClass('ng-hide');

								}

							} else {

								globals.mdToast('error', response.data.message);

							}

							getMenuTimer();

						},

						function (response) {

							$scope.savingTimer = false;

						}

					);

			};

		}

	};



	if ($scope.ONLYADMIN) {

		setTimeout(function () {

			checkForUpdate();

		}, 20000);

	}



	function checkForUpdate() {

		var posturl = BASE_URL + 'settings/check_for_update';

		$http.post(posturl, config).then(function (res) { }, function (res) { });

	}



	$scope.viewTimesheet = function () {

		localStorage.clear();

		var findPath = {};

		findPath.type = 'report';

		findPath.view = 'timesheet';

		localStorage.setItem('findPath', JSON.stringify(findPath));

		window.location.href = BASE_URL + 'report';

	};



	$scope.addingEvent = false;

	$scope.AddEvent = function () {

		$scope.addingEvent = true;

		var start = '', end = '';

		if ($scope.event_start) {

			start = moment($scope.event_start).format("YYYY-MM-DD HH:mm:ss")

		}

		if ($scope.event_end) {

			end = moment($scope.event_end).format("YYYY-MM-DD HH:mm:ss")

		}

		var dataObj = $.param({

			title: $scope.event_title,

			detail: $scope.event_detail,

			eventstart: start,

			eventType: $scope.eventType ? $scope.eventType.id : '',

			eventend: end,

			staff_id: $scope.event_staff,

			notification: +($scope.event_reminder ? $scope.event_reminder : false),

			email_to_all: +($scope.email_to_all ? $scope.email_to_all : false),

			notification_type: $scope.event_ntf_type,

			notification_duration: $scope.event_ntf_duration,

			notification_time: $scope.event_ntf_time,

		});

		var posturl = BASE_URL + 'calendar/addevent';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.addingEvent = false;

					if (response.data.success == true) {

						showToast(NTFTITLE, response.data.message, ' success');

						$mdSidenav('EventForm').close();

					} else {

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.addingEvent = false;

				}

			);

	};



	$scope.addingOnsite = false;

	$scope.AddOnsiteVisit = function () {

		$scope.addingOnsite = true;

		if (!$scope.onsite_visit) {

			var dataObj = $.param({

				title: '',

				customer_id: '',

				staff_id: '',

				description: '',

				date: '',

				start: '',

				end: '',

			});

		} else {

			var date1 = '', date2 = '';

			if ($scope.onsite_visit.start) {

				date1 = moment($scope.onsite_visit.start).format("YYYY-MM-DD HH:mm:ss");

			}

			if ($scope.onsite_visit.end) {

				date2 = moment($scope.onsite_visit.end).format("YYYY-MM-DD HH:mm:ss");

			}

			var dataObj = $.param({

				title: $scope.onsite_visit.title,

				customer_id: $scope.onsite_visit.customer_id,

				staff_id: $scope.onsite_visit.staff_id,

				description: $scope.onsite_visit.description,

				date: date1,

				start: date1,

				end: date2,

			});

		}

		var posturl = BASE_URL + 'trivia/set_onsite_visit';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.addingOnsite = false;

					if (response.data.success == true) {

						$mdSidenav('SetOnsiteVisit').close();

						showToast(NTFTITLE, response.data.message, ' success');

					} else {

						showToast(NTFTITLE, response.data.message, ' danger');

					}

				},

				function (response) {

					$scope.addingOnsite = false;

				}

			);

	};



	$scope.AddTodo = function () {

		$scope.addingTodo = true;

		var dataObj = $.param({

			tododetail: $scope.tododetail,

		});

		var posturl = BASE_URL + 'trivia/addtodo';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.addingTodo = false;

					if (response.data.success) {

						$scope.todos.push({

							'description': $scope.tododetail,

							'date': response.data.messageDate,

						});

						$.gritter.add({

							title: '<b>' + NTFTITLE + '</b>',

							text: $scope.lang.todoadded,

							position: 'bottom',

							class_name: 'color success',

						});

						$scope.tododetail = '';

					}

				},

				function (response) {

					$scope.addingTodo = false;

					console.log(response);

				}

			);

	};



	$scope.DeleteTodo = function (index) {

		var todo = $scope.todos[index];

		var dataObj = $.param({

			todo: todo.id

		});

		var posturl = BASE_URL + 'trivia/removetodo';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.todos.splice($scope.todos.indexOf(todo), 1);

					console.log(response);

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.TodoAsUnDone = function (index) {

		var todo = $scope.tododone[index];

		var dataObj = $.param({

			todo: todo.id

		});

		$http.post(BASE_URL + 'trivia/undonetodo', dataObj, config)

			.then(

				function (response) {

					var todo = $scope.tododone[index];

					$scope.tododone.splice($scope.tododone.indexOf(todo), 1);

					$scope.todos.unshift(todo);

					console.log(response);

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.DeleteTodoDone = function (index) {

		var todo = $scope.tododone[index];

		var dataObj = $.param({

			todo: todo.id

		});

		var posturl = BASE_URL + 'trivia/removetodo';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.tododone.splice($scope.tododone.indexOf(todo), 1);

					console.log(response);

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.TodoAsDone = function (index) {

		var todo = $scope.todos[index];

		var dataObj = $.param({

			todo: todo.id

		});

		$http.post(BASE_URL + 'trivia/donetodo', dataObj, config)

			.then(

				function (response) {

					var todo = $scope.todos[index];

					$scope.todos.splice($scope.todos.indexOf(todo), 1);

					$scope.tododone.unshift(todo);

					$.gritter.add({

						title: '<b>' + NTFTITLE + '</b>',

						text: $scope.lang.tododone,

						position: 'bottom',

						class_name: 'color success',

					});

					console.log(response);

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.ReminderRead = function (id) {

		var dataObj = $.param({

			reminder_id: id

		});

		var posturl = BASE_URL + 'trivia/markreadreminder';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					showToast(NTFTITLE, $scope.lang.remindermarkasread, ' success');

					$http.get(BASE_URL + 'api/reminders').then(function (Reminders) {

						$scope.reminders = Reminders.data;

					});

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.InvoiceCancel = function (e) {

		var id = $(e.target).data('invoice');

		var dataObj = $.param({

			status_id: 4,

			invoice_id: id,

		});

		var posturl = BASE_URL + 'invoices/cancelled';

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$.gritter.add({

						title: '<b>' + NTFTITLE + '</b>',

						text: INVMARKCACELLED,

						class_name: 'color danger'

					});

					$('.toggle-due').hide();

					$('.toggle-cash').hide();

					$('.cancelledinvoicealert').show();

					console.log(response);

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.NotificationRead = function (index) {

		var notification = $scope.notifications[index];

		var posturl = BASE_URL + 'trivia/mark_read_notification/' + notification.id;

		$http.post(posturl, config)

			.then(

				function (response) {

					console.log(response);

					window.location.href = notification.target;

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.ChangeTicketStatus = function () {

		var dataObj = $.param({

			statusid: $scope.item.code,

			ticketid: $(".tickid").val(),

		});

		$http.post(BASE_URL + 'tickets/chancestatus', dataObj, config)

			.then(

				function (response) {

					$.gritter.add({

						title: '<b>' + NTFTITLE + '</b>',

						text: TICKSTATUSCHANGE,

						class_name: 'color success'

					});

					$(".label-status").text($scope.item.name);

					console.log(response);

				},

				function (response) {

					console.log(response);

				}

			);

	};



	$scope.ciuisTooltip = {

		showTooltip: false,

		tipDirection: 'bottom'

	};



	$scope.ciuisTooltip.delayTooltip = undefined;



	$scope.$watch('demo.delayTooltip', function (val) {

		$scope.ciuisTooltip.delayTooltip = parseInt(val, 10) || 0;

	});



	$scope.passwordLength = 12;

	$scope.addUpper = true;

	$scope.addNumbers = true;

	$scope.addSymbols = true;



	function getRandomInt(min, max) {

		return Math.floor(Math.random() * (max - min + 1)) + min;

	}



	function shuffleArray(array) {

		for (var i = array.length - 1; i > 0; i--) {

			var j = Math.floor(Math.random() * (i + 1));

			var temp = array[i];

			array[i] = array[j];

			array[j] = temp;

		}

		return array;

	}



	$scope.createPassword = function () {

		var lowerCharacters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

		var upperCharacters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

		var numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

		var symbols = ['!', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|', '}', '~'];

		var noOfLowerCharacters = 0,

			noOfUpperCharacters = 0,

			noOfNumbers = 0,

			noOfSymbols = 0;

		var noOfneededTypes = $scope.addUpper + $scope.addNumbers + $scope.addSymbols;

		var noOfLowerCharacters = getRandomInt(1, $scope.passwordLength - noOfneededTypes);

		var usedTypeCounter = 1;

		if ($scope.addUpper) {

			noOfUpperCharacters = getRandomInt(1, $scope.passwordLength - noOfneededTypes + usedTypeCounter - noOfLowerCharacters);

			usedTypeCounter++;

		}

		if ($scope.addNumbers) {

			noOfNumbers = getRandomInt(1, $scope.passwordLength - noOfneededTypes + usedTypeCounter - noOfLowerCharacters - noOfUpperCharacters);

			usedTypeCounter++;

		}

		if ($scope.addSymbols) {

			noOfSymbols = $scope.passwordLength - noOfLowerCharacters - noOfUpperCharacters - noOfNumbers;

		}

		var passwordArray = [];

		for (var i = 0; i < noOfLowerCharacters; i++) {

			passwordArray.push(lowerCharacters[getRandomInt(1, lowerCharacters.length - 1)]);

		}

		for (var i = 0; i < noOfUpperCharacters; i++) {

			passwordArray.push(upperCharacters[getRandomInt(1, upperCharacters.length - 1)]);

		}

		for (var i = 0; i < noOfNumbers; i++) {

			passwordArray.push(numbers[getRandomInt(1, numbers.length - 1)]);

		}

		for (var i = 0; i < noOfSymbols; i++) {

			passwordArray.push(symbols[getRandomInt(1, symbols.length - 1)]);

		}

		passwordArray = shuffleArray(passwordArray);

		return passwordArray.join("");

	};



	$scope.passwordNew = $scope.createPassword();



	$scope.getNewPass = function () {

		$scope.passwordNew = $scope.createPassword();

	};

}



function Chart_Controller($scope, $http) {

	"use strict";



	$http.get(BASE_URL + 'api/stats').then(function (Stats) {

		$scope.stats = Stats.data;



		Highcharts.setOptions({

			colors: ['#ffbc00', 'rgb(239, 89, 80)',]

		});



		Highcharts.chart('monthlyexpenses', {

			title: {

				text: '',

			},

			credits: {

				enabled: false

			},

			chart: {

				backgroundColor: 'transparent',

				marginBottom: 0,

				marginLeft: -10,

				marginRight: -10,

				marginTop: 0,

				type: 'area',

			},

			exporting: {

				enabled: false

			},

			plotOptions: {

				series: {

					fillOpacity: 0.1

				},

				area: {

					lineWidth: 1,

					marker: {

						lineWidth: 2,

						symbol: 'circle',

						radius: 3,

					},

					legend: {

						radius: 2,

					}

				}

			},

			xAxis: {

				categories: $scope.stats.months,

				visible: true,

				endOnTick: true,

			},

			yAxis: {

				title: {

					enabled: false

				},

				visible: false

			},

			legend: {

				align: 'right',

				enabled: false,

				floating: true,

				verticalAlign: 'top',

				layout: 'vertical',

				x: -15,

				y: 100,

				itemMarginBottom: 20,

				useHTML: true,

				labelFormatter: function () {

					return '<span style="color:' + this.color + '">' + this.name + '</span>'

				},

				symbolPadding: 0,

				symbolWidth: 0,

				symbolRadius: 0

			},

			series: [{

				name: lang.expense,

				data: $scope.stats.monthly_expenses,

				color: '#f52f24',			//red

			}, {

				name: lang.sale,

				data: $scope.stats.monthly_sales,

				backgroundColor: 'rgba(255, 255, 255, 0.1)',

				color: '#26c281'			//green

			}]

		});

	});



	$http.get(BASE_URL + 'api/weekly_dashboard_chart').then(function (Charts) {

		$scope.chart = Charts.data;



		var MainChartOptions = {

			responsive: true,

			maintainAspectRatio: false,

			scales: {

				xAxes: [{

					categoryPercentage: .2,

					barPercentage: 1,

					position: 'top',

					gridLines: {

						color: '#C7CBD5',

						zeroLineColor: '#C7CGH7',

						drawTicks: true,

						borderDash: [8, 5],

						offsetGridLines: false,

						tickMarkLength: 10,

						callback: function (value) {

						}

					},

					ticks: {

						callback: function (value) {

							return value.charAt(0) + value.charAt(1) + value.charAt(2);

						}

					}

				}],

				yAxes: [{

					display: false,

					gridLines: {

						drawBorder: false,

						drawOnChartArea: false,

						borderDash: [8, 5],

						offsetGridLines: false

					},

					ticks: {

						beginAtZero: true,

						maxTicksLimit: 5,

					}

				}]

			},

			legend: {

				display: true,

				labels: {

					filter: function (item, chart) {

						return !item.text.includes('Line');

					}

				}

			}

		};



		var ctx = $('#main-chart');

		var mainChart = new Chart(ctx, {

			type: 'bar',

			data: $scope.chart.weekly_expenses,

			borderRadius: 10,

			options: MainChartOptions

		});

	});

}



function Search_Controller($scope, $http, $mdSidenav, $mdDialog, $filter) {

	"use strict";

	function buildToggler(navID) {

		return function () {
			$("html, body").scrollTop(0);
			$mdSidenav(navID).toggle();

		};

	}



	$scope.close = function () {

		$mdSidenav('searchNav').close();

	};



	$scope.searchNav = function () {

		buildToggler('searchNav');

	};

}



function Consultant_Controller($scope, $http, $mdSidenav, $mdDialog, $filter) {

	"use strict";

	$http.get(BASE_URL + 'api/get_consultant_data').then(function (Data) {

		$scope.consultant = Data.data;



		Highcharts.chart('consultantchart', {

			chart: {

				type: 'area'

			},

			credits: {

				enabled: false

			},

			title: {

				text: $scope.consultant.lang.sales_vs_expenses,

				color: '#777',

				//font-weight: 'bold'

			},

			subtitle: {

				text: ''

			},

			xAxis: {

				//startOnTick: true,

				endOnTick: true,

				min: 0.5,

				tickColor: '#fff',

				categories: $scope.consultant.months_short

			},

			yAxis: {

				title: {

					text: $scope.consultant.lang.amount

				}

			},

			plotOptions: {

				line: {

					dataLabels: {

						enabled: true

					},

					enableMouseTracking: false

				}

			},

			series: [{

				name: $scope.consultant.lang.expenses,

				data: $scope.consultant.monthly_expenses,

				color: '#f52f24'

			}, {

				name: $scope.consultant.lang.sales,

				data: $scope.consultant.monthly_sales,

				backgroundColor: 'rgba(255, 255, 255, 0.1)',

				color: '#26c281'

			}],

		});



		Highcharts.setOptions({

			colors: ['#ffbc00', 'rgb(239, 89, 80)']

		});



		Highcharts.chart('monthlyexpenses', {

			title: {

				text: '',

			},

			credits: {

				enabled: false

			},

			chart: {

				backgroundColor: 'transparent',

				marginBottom: 0,

				marginLeft: -10,

				marginRight: -10,

				marginTop: 0,

				type: 'area',

			},

			exporting: {

				enabled: false

			},

			plotOptions: {

				series: {

					fillOpacity: 0.1

				},

				area: {

					lineWidth: 1,

					marker: {

						lineWidth: 2,

						symbol: 'circle',

						fillColor: 'black',

						radius: 3,

					},

					legend: {

						radius: 2,

					}

				}

			},

			xAxis: {

				categories: $scope.consultant.months,

				visible: true,

			},

			yAxis: {

				title: {

					enabled: false

				},

				visible: false

			},

			tooltip: {

				shadow: false,

				useHTML: true,

				padding: 0,

				formatter: function () {

					return '<div class="bis-tooltip" style="background-color: ' + this.color + '">' + this.x + ' <span>' + this.y + ' ' + $scope.cur_symbol + '</span></div>'

				}

			},

			legend: {

				align: 'right',

				enabled: false,

				verticalAlign: 'top',

				layout: 'vertical',

				x: -15,

				y: 100,

				itemMarginBottom: 20,

				useHTML: true,

				labelFormatter: function () {

					return '<span style="color:' + this.color + '">' + this.name + '</span>'

				},

				symbolPadding: 0,

				symbolWidth: 0,

				symbolRadius: 0

			},

			series: [{

				"data": $scope.consultant.monthly_expenses,

			}]

		}, function (chart) {

			var series = chart.series;

			series.forEach(function (serie) {

				if (serie.legendSymbol) {

					serie.legendSymbol.destroy();

				}

				if (serie.legendLine) {

					serie.legendLine.destroy();

				}

			});

		});

	});

}



function Sidebar_Controller($scope, $http, $mdSidenav, $filter, $interval, $mdDialog, $element, $mdToast, $log) {



	$http.get(BASE_URL + 'api/overdueinvoices').then(function (Overdueinvoices) {

		$scope.overdueinvoices = Overdueinvoices.data;

	});



	$http.get(BASE_URL + 'api/dueinvoices').then(function (Dueinvoices) {

		$scope.dueinvoices = Array.isArray(Dueinvoices.data) ? Dueinvoices.data : [];

	}).catch(function (error) {
		console.error('Erro ao carregar dueinvoices:', error);
		$scope.dueinvoices = [];
	});



	$http.get(BASE_URL + 'api/transactions').then(function (Transactions) {

		$scope.transactions = Transactions.data;

	});



	$http.get(BASE_URL + 'api/newtickets').then(function (Newtickets) {

		$scope.newtickets = Array.isArray(Newtickets.data) ? Newtickets.data : [];

	}).catch(function (error) {
		console.error('Erro ao carregar newtickets:', error);
		$scope.newtickets = [];
	});



	$http.get(BASE_URL + 'api/events').then(function (Events) {

		$scope.events = Events.data;

	});



	$http.get(BASE_URL + 'api/reminders').then(function (Reminders) {

		$scope.reminders = Reminders.data;

	});



	$http.get(BASE_URL + 'api/logs').then(function (Logs) {

		$scope.logs = Logs.data;

	});



}



function Panel_controller($scope, $http, $mdSidenav, $filter, $interval, $mdDialog, $element, $mdToast, $log) {



	$http.get(BASE_URL + 'api/stats').then(function (Stats) {

		$scope.stats = Stats.data;



		Highcharts.setOptions({

			colors: ['#ffbc00', 'rgb(239, 89, 80)',]

		});



		Highcharts.chart('monthlyexpenses', {

			title: {

				text: '',

			},

			credits: {

				enabled: false

			},

			chart: {

				backgroundColor: 'transparent',

				marginBottom: 0,

				marginLeft: -10,

				marginRight: -10,

				marginTop: 0,

				type: 'area',

			},

			exporting: {

				enabled: false

			},

			plotOptions: {

				series: {

					fillOpacity: 0.1

				},

				area: {

					lineWidth: 1,

					marker: {

						lineWidth: 2,

						symbol: 'circle',

						radius: 3,

					},

					legend: {

						radius: 2,

					}

				}

			},

			xAxis: {

				categories: $scope.stats.months,

				visible: true,

				endOnTick: true,

			},

			yAxis: {

				title: {

					enabled: false

				},

				visible: false

			},

			legend: {

				align: 'right',

				enabled: false,

				floating: true,

				verticalAlign: 'top',

				layout: 'vertical',

				x: -15,

				y: 100,

				itemMarginBottom: 20,

				useHTML: true,

				labelFormatter: function () {

					return '<span style="color:' + this.color + '">' + this.name + '</span>'

				},

				symbolPadding: 0,

				symbolWidth: 0,

				symbolRadius: 0

			},

			series: [{

				name: lang.expense,

				data: $scope.stats.monthly_expenses,

				color: '#f52f24',			//red

			}, {

				name: lang.sale,

				data: $scope.stats.monthly_sales,

				backgroundColor: 'rgba(255, 255, 255, 0.1)',

				color: '#26c281'			//green

			}]

		});

	});



	$http.get(BASE_URL + 'api/weekly_dashboard_chart').then(function (Charts) {

		$scope.chart = Charts.data;



		var MainChartOptions = {

			responsive: true,

			maintainAspectRatio: false,

			scales: {

				xAxes: [{

					categoryPercentage: .2,

					barPercentage: 1,

					position: 'top',

					gridLines: {

						color: '#C7CBD5',

						zeroLineColor: '#C7CGH7',

						drawTicks: true,

						borderDash: [8, 5],

						offsetGridLines: false,

						tickMarkLength: 10,

						callback: function (value) {

						}

					},

					ticks: {

						callback: function (value) {

							return value.charAt(0) + value.charAt(1) + value.charAt(2);

						}

					}

				}],

				yAxes: [{

					display: false,

					gridLines: {

						drawBorder: false,

						drawOnChartArea: false,

						borderDash: [8, 5],

						offsetGridLines: false

					},

					ticks: {

						beginAtZero: true,

						maxTicksLimit: 5,

					}

				}]

			},

			legend: {

				display: true,

				labels: {

					filter: function (item, chart) {

						return !item.text.includes('Line');

					}

				}

			}

		};



		var ctx = $('#main-chart');

		var mainChart = new Chart(ctx, {

			type: 'bar',

			data: $scope.chart.weekly_expenses,

			borderRadius: 10,

			options: MainChartOptions

		});

	});

}



CiuisCRM.controller('Ciuis_Controller', Ciuis_Controller);

CiuisCRM.controller('Chart_Controller', Chart_Controller);

CiuisCRM.controller('Search_Controller', Search_Controller);

CiuisCRM.controller('Consultant_Controller', Consultant_Controller);

CiuisCRM.controller('Sidebar_Controller', Sidebar_Controller);

CiuisCRM.controller('Panel_controller', Panel_controller);



// ALL FILTERS



CiuisCRM.filter('trustAsHtml', ['$sce', function ($sce) {

	"use strict";



	return function (text) {

		return $sce.trustAsHtml(text);

	};

}]);



CiuisCRM.filter('pagination', function () {

	"use strict";



	return function (input, start) {

		if (!input || !input.length) {

			return;

		}

		start = +start; //parse to int

		return input.slice(start);

	};

});



CiuisCRM.filter('time', function () {

	"use strict";



	var conversions = {

		'ss': angular.identity,

		'mm': function (value) {

			return value * 60;

		},

		'hh': function (value) {

			return value * 3600;

		}

	};



	var padding = function (value, length) {

		var zeroes = length - ('' + (value)).length,

			pad = '';

		while (zeroes-- > 0) pad += '0';

		return pad + value;

	};



	return function (value, unit, format, isPadded) {

		var totalSeconds = conversions[unit || 'ss'](value),

			hh = Math.floor(totalSeconds / 3600),

			mm = Math.floor((totalSeconds % 3600) / 60),

			ss = totalSeconds % 60;



		format = format || 'hh:mm:ss';

		isPadded = angular.isDefined(isPadded) ? isPadded : true;

		hh = isPadded ? padding(hh, 2) : hh;

		mm = isPadded ? padding(mm, 2) : mm;

		ss = isPadded ? padding(ss, 2) : ss;



		return format.replace(/hh/, hh).replace(/mm/, mm).replace(/ss/, ss);

	};

});



// ALL DIRECTIVES



CiuisCRM.directive('loadMore', function () {

	"use strict";



	return {

		template: "<a ng-click='loadMore()' id='loadButton' class='activity_tumu'><i style='font-size:22px;' class='icon ion-android-arrow-down'></i></a>",

		link: function (scope) {

			scope.LogLimit = 2;

			scope.loadMore = function () {

				scope.LogLimit += 5;

				if (scope.logs.length < scope.LogLimit) {

					CiuisCRM.element(loadButton).fadeOut();

				}

			};

		}

	};

});



CiuisCRM.directive("bindExpression", function ($parse) {

	"use strict";

	var directive = {};

	directive.restrict = 'E';

	directive.require = 'ngModel';

	directive.link = function (scope, element, attrs, ngModel) {

		scope.$watch(attrs.expression, function (newValue) {

			ngModel.$setViewValue(newValue);

		});

		ngModel.$render = function () {

			$parse(attrs.expression).assign(ngModel.viewValue);

		};

	};

	return directive;

});



CiuisCRM.directive('onErrorSrc', function () {

	"use strict";



	return {

		link: function (scope, element, attrs) {

			element.bind('error', function () {

				if (attrs.src !== attrs.onErrorSrc) {

					attrs.$set('src', attrs.onErrorSrc);

				}

			});

		}

	};

});



CiuisCRM.directive('ciuisReady', function () {

	"use strict";

	return {

		link: function ($scope) {

			setTimeout(function () {

				$scope.$apply(function () {

					var milestone_projectExpandablemilestonetitle = $('.milestone_project-action.is-expandable .milestonetitle');

					$(milestone_projectExpandablemilestonetitle).attr('tabindex', '0');

					// Give milestone_projects ID's

					$('.milestone_project').each(function (i, $milestone_project) {

						var $milestone_projectActions = $($milestone_project).find('.milestone_project-action.is-expandable');

						$($milestone_projectActions).each(function (j, $milestone_projectAction) {

							var $milestoneContent = $($milestone_projectAction).find('.content');

							$($milestoneContent).attr('id', 'milestone_project-' + i + '-milestone-content-' + j).attr('role', 'region');

							$($milestoneContent).attr('aria-expanded', $($milestone_projectAction).hasClass('expanded'));

							$($milestone_projectAction).find('.milestonetitle').attr('aria-controls', 'milestone_project-' + i + '-milestone-content-' + j);

						});

					});

					$(milestone_projectExpandablemilestonetitle).click(function () {

						$(this).parent().toggleClass('is-expanded');

						$(this).siblings('.content').attr('aria-expanded', $(this).parent().hasClass('is-expanded'));

					});

					// Expand or navigate back and forth between sections

					$(milestone_projectExpandablemilestonetitle).keyup(function (e) {

						if (e.which === 13) { //Enter key pressed

							$(this).click();

						} else if (e.which === 37 || e.which === 38) { // Left or Up

							$(this).closest('.milestone_project-milestone').prev('.milestone_project-milestone').find('.milestone_project-action .milestonetitle').focus();

						} else if (e.which === 39 || e.which === 40) { // Right or Down

							$(this).closest('.milestone_project-milestone').next('.milestone_project-milestone').find('.milestone_project-action .milestonetitle').focus();

						}

					});

				});

			}, 5000);

			angular.element(document).ready(function () {

				$('.transform_logo').addClass('animated rotateIn'); // Logo Transform

				$('#chooseFile').bind('change', function () {

					var filename = $("#chooseFile").val();

					if (/^\s*$/.test(filename)) {

						$(".file-upload").removeClass('active');

						$("#noFile").text("None Chosen");

					} else {

						$(".file-upload").addClass('active');

						$("#noFile").text(filename.replace("C:\\fakepath\\", ""));

					}

				});

				var $btns = $('.pbtn').click(function () {

					if (this.id == 'all') {

						$('#ciuisprojectcard > div').fadeIn(450);

					} else {

						var $el = $('.' + this.id).fadeIn(450);

						$('#ciuisprojectcard > div').not($el).hide();

					}

					$btns.removeClass('active');

					$(this).addClass('active');

				});



				$('.add-file-cover').hide();



				$(document).on('click', function (e) {

					if ($(e.target).closest('.add-file').length) {

						$(".add-file-cover").show();

					} else if (!$(e.target).closest('.add-file-cover').length) {

						$('.add-file-cover').hide();

					}

				});

				$('.form-field-file').each(function () {

					var label = $('label', this);

					var labelValue = $(label).html();

					var fileInput = $('input[type="file"]', this);

					$(fileInput).on('change', function () {

						var fileName = $(this).val().split('\\').pop();

						if (fileName) {

							$(label).html(fileName);

						} else {

							$(label).html(labelValue);

						}

					});

				});

				$(document).ready(function () {
					$.event.addProp('dataTransfer');

					$('input[name=type]').change(function () {

						if (!$(this).is(':checked')) {

							return;

						}

						if ($(this).val() === '0') {

							$('.bank').hide();

						} else if ($(this).val() === '1') {

							$('.bank').show();

						}

					});

				});

				$('#ciuisloader').hide();

			});

		}

	};

});

CiuisCRM.directive("strToTime", function () {

	"use strict";

	return {

		require: 'ngModel',

		link: function (scope, element, attrs, ngModelController) {

			ngModelController.$parsers.push(function (data) {

				if (!data) {

					return "";

				}

				return ("0" + data.getHours().toString()).slice(-2) + ":" + ("0" + data.getMinutes().toString()).slice(-2);

			});

			ngModelController.$formatters.push(function (data) {

				if (!data) {

					return null;

				}

				var d = new Date(1970, 1, 1);

				var splitted = data.split(":");

				d.setHours(splitted[0]);

				d.setMinutes(splitted[1]);

				return d;

			});

		}

	};

});

CiuisCRM.directive('ciuisSidebar', function () {

	"use strict";

	return {

		templateUrl: "ciuis-sidebar.html"

	};

});

CiuisCRM.directive('customFieldsVertical', function () {

	"use strict";

	return {

		templateUrl: "custom-fields.html"

	};

});

CiuisCRM.directive("uiDraggable", [

	'$parse',

	'$rootScope',

	function ($parse, $rootScope) {

		"use strict";

		return function (scope, element, attrs) {

			if ($.jQuery && !$.jQuery.event.props.dataTransfer) {

				$.jQuery.event.props.push('dataTransfer');

			}

			element.attr("draggable", false);

			attrs.$observe("uiDraggable", function (newValue) {

				element.attr("draggable", newValue);

			});

			var dragData = "";

			scope.$watch(attrs.drag, function (newValue) {
				dragData = newValue;
			});

			element.bind("dragstart", function (e) {
				var sendData = angular.toJson(dragData);
				var sendChannel = attrs.dragChannel || "defaultchannel";
				e.dataTransfer.setData("Text", sendData);
				$rootScope.$broadcast("ANGULAR_DRAG_START", sendChannel);
			});



			element.bind("dragend", function (e) {

				var sendChannel = attrs.dragChannel || "defaultchannel";

				$rootScope.$broadcast("ANGULAR_DRAG_END", sendChannel);

				if (e.dataTransfer && e.dataTransfer.dropEffect !== "none") {

					if (attrs.onDropSuccess) {

						var fn = $parse(attrs.onDropSuccess);

						scope.$apply(function () {

							fn(scope, {

								$event: e

							});

						});

					}

				}

			});





		};

	}

]);

CiuisCRM.directive("uiOnDrop", [
	'$parse',
	'$rootScope',

	function ($parse, $rootScope) {

		"use strict";

		return function (scope, element, attr) {
			var dropChannel = "defaultchannel";
			var dragChannel = "";
			var dragEnterClass = attr.dragEnterClass || "on-drag-enter";
			var dragHoverClass = attr.dragHoverClass || "on-drag-hover";

			function onDragOver(e) {
				if (e.preventDefault) {
					e.preventDefault(); // Necessary. Allows us to drop.
				}

				if (e.stopPropagation) {
					e.stopPropagation();
				}
				e.dataTransfer.dropEffect = 'move';
				return false;
			}



			function onDragEnter(e) {

				$rootScope.$broadcast("ANGULAR_HOVER", dropChannel);

				element.addClass(dragHoverClass);

			}



			function onDrop(e) {

				if (e.preventDefault) {

					e.preventDefault(); // Necessary. Allows us to drop.

				}

				if (e.stopPropagation) {

					e.stopPropagation(); // Necessary. Allows us to drop.

				}

				console.log(e.dataTransfer.getData("Text").toString() == 'undefined');

				var data = e.dataTransfer.getData("Text").toString() != 'undefined' ? e.dataTransfer.getData("Text") : [];

				data = angular.fromJson(data);


				var fn = $parse(attr.uiOnDrop);

				scope.$apply(function () {

					fn(scope, {

						$data: data,

						$event: e

					});

				});

				element.removeClass(dragEnterClass);

			}





			$rootScope.$on("ANGULAR_DRAG_START", function (event, channel) {

				dragChannel = channel;

				if (dropChannel === channel) {



					element.bind("dragover", onDragOver);

					element.bind("dragenter", onDragEnter);



					element.bind("drop", onDrop);

					element.addClass(dragEnterClass);

				}



			});



			$rootScope.$on("ANGULAR_DRAG_END", function (e, channel) {

				dragChannel = "";

				if (dropChannel === channel) {



					element.unbind("dragover", onDragOver);

					element.unbind("dragenter", onDragEnter);



					element.unbind("drop", onDrop);

					element.removeClass(dragHoverClass);

					element.removeClass(dragEnterClass);

				}

			});



			$rootScope.$on("ANGULAR_HOVER", function (e, channel) {

				if (dropChannel === channel) {

					element.removeClass(dragHoverClass);

				}

			});



			attr.$observe('dropChannel', function (value) {

				if (value) {

					dropChannel = value;

				}

			});





		};

	}

]);



// New model type for file upload i.e. file-model instead of ng-model

CiuisCRM.directive('fileModel', ['$parse', function ($parse) {

	return {

		restrict: 'A',

		link: function (scope, element, attrs) {

			var model = $parse(attrs.fileModel);

			var modelSetter = model.assign;

			element.bind('change', function () {

				scope.$apply(function () {

					modelSetter(scope, element[0].files[0]);

				});

			});

		}

	};

}]);



// ------------------------------------------------

// File upload service

// ------------------------------------------------

// Code to use file upload function in angular:

// ================================================

// var file = $scope.project_file;

// var uploadUrl = BASE_URL+'projects/add_file/'+PROJECTID;

// fileUpload.uploadFileToUrl(file, uploadUrl, function(response) {

// });

// ================================================



CiuisCRM.service('fileUpload', ['$http', function ($http) {

	this.uploadFileToUrl = function (file, uploadUrl, callback) {

		var fd = new FormData();

		fd.append('file', file);

		$http.post(uploadUrl, fd, {

			transformRequest: angular.identity,

			headers: { 'Content-Type': undefined }

		}).then(function (response) {

			callback(response.data);

		}, function (response) {

			callback(response.data);

		});

	};



	this.uploadFileWithData = function (data, uploadUrl, callback) {

		var formData = new FormData();

		angular.forEach(data, function (value, key) {

			formData.append(key, (value ? value : ''));

		});

		$http.post(uploadUrl, formData, {

			transformRequest: angular.identity,

			headers: { 'Content-Type': undefined }

		}).then(function (response) {

			callback(response.data);

		}, function (response) {

			callback(response.data);

		});

	};

}]);





// Global Toaster function
function showToast(title, message, type) {
	$.gritter.removeAll();
	setTimeout(a => {
		$.gritter.add({
			title: '<b>' + title + '</b>',
			text: message,
			class_name: 'color ' + type,
		});
	}, 1000)


}