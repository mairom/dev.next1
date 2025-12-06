function WebLeads_Controller($scope, $http, $mdSidenav, $mdDialog, $filter, $q) {

	"use strict";



	$scope.close = function () {

		$mdSidenav('Create').close();

	};



	$scope.createForm = function() {

		$mdSidenav('Create').toggle();

	};



	$scope.get_staff();

	$scope.webleadsLoader = true;

	var deferred = $q.defer();

	$scope.weblead_list = {

		order: '',

		limit: 5,

		page: 1

	};

	$scope.promise = deferred.promise;

	$http.get(BASE_URL + 'leads/webleads').then(function (WebLeads) {

		$scope.webleads = WebLeads.data;

		deferred.resolve();

		$scope.limitOptions = [5, 10, 15, 20];

		if($scope.webleads.length > 20 ) {

			$scope.limitOptions = [5, 10, 15, 20, $scope.webleads.length];

		}

		$scope.webleadsLoader = false;



	});



	$http.get(BASE_URL + 'leads/leadstatuses').then(function (LeadStatuses) {

		$scope.leadstatuses = LeadStatuses.data;

		$scope.NewStatus = function () {

			var confirm = $mdDialog.prompt()

				.title($scope.lang.new_status)

				.textContent($scope.lang.type_status_name)

				.placeholder($scope.lang.status_name)

				.ariaLabel($scope.lang.status_name)

				.initialValue('')

				.required(true)

				.ok($scope.lang.add)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function (result) {

				var dataObj = $.param({

					name: result,

				});

				$http.post(BASE_URL + 'leads/add_status', dataObj, config)

					.then(

						function (response) {

							console.log(response);

							$scope.leadstatuses.push({

								'id': response.data,

								'name': result,

							});

						},

						function (response) {

							console.log(response);

						}

					);

			}, function () {



			});

		};

	});



	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {

		$scope.leadssources = LeadSources.data;

		$scope.NewSource = function () {

			var confirm = $mdDialog.prompt()

				.title($scope.lang.new_source)

				.textContent($scope.lang.type_source_name)

				.placeholder($scope.lang.source_name)

				.ariaLabel($scope.lang.source_name)

				.initialValue('')

				.required(true)

				.ok($scope.lang.add)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function (result) {

				var dataObj = $.param({

					name: result,

				});

				$http.post(BASE_URL + 'leads/add_source', dataObj, config)

					.then(

						function (response) {

							console.log(response);

							$scope.leadssources.push({

								'id': response.data,

								'name': result,

							});

						},

						function (response) {

							console.log(response);

						}

					);

			}, function () {



			});

		};

	});



	$scope.AddWebLeadForm = function() {

		$scope.addingLead = true;

		if (!$scope.weblead) {

			var dataObj = $.param({

				name: '',

				assigned_id: '',

				status_id: '',

				source_id: '',

				submit_text: '',

				success_message: '',

				duplicate: '',

				notification: '',

				status: '',

			});

		} else {

			var dataObj = $.param({

				name: $scope.weblead.name,

				assigned_id: $scope.weblead.assigned_id,

				status_id: $scope.weblead.status_id,

				source_id: $scope.weblead.source_id,

				submit_text: $scope.weblead.submit_text,

				success_message: $scope.weblead.success_message,

				duplicate: $scope.weblead.duplicate,

				notification: $scope.weblead.notification,

				status: $scope.weblead.status,

			}); 

		}

		$http.post(BASE_URL + 'leads/add_weblead_form', dataObj, config).then(function (response) {

			$scope.addingLead = false;

			if (response.data.success == true) {

				globals.mdToast('success', response.data.message);

				$mdSidenav('Create').close();

				window.location.href = BASE_URL+'leads/form/'+response.data.id;

			} else {

				globals.mdToast('error', response.data.message);

			}

		}, function(response) {

			$scope.addingLead = false;

		});

	};

}



function WebLead_Controller($scope, $http, $mdSidenav, $mdDialog, $filter) {

	"use strict";



	$scope.get_staff();

	$scope.close = function () {

		$mdSidenav('Update').close();

		$mdDialog.hide();

	};



	$scope.UpdateForm = function() {

		$mdSidenav('Update').toggle();

	};



	$scope.viewIntegration = function (ev) {

		$mdDialog.show({

			templateUrl: 'viewIntegration.html',

			scope: $scope,

			preserveScope: true,

			targetEvent: ev

		});

	};



	var customField;

	var dumpdata = [];



	$http.get(BASE_URL + 'api/custom_fields_by_type/lead').then(function (custom_fields) {

		$scope.customField = custom_fields.data;

		for (var i = 0; i < $scope.customField.length; i++) {

			var inputype = 'textfield';

			if ($scope.customField[i].type == 'input' || $scope.customField[i].type == 'date' || $scope.customField[i].type == 'textarea') {

				if ($scope.customField[i].type == 'input') {

					inputype = 'textfield';

				}

				if ($scope.customField[i].type == 'date') {

					inputype = 'datetime';

				}

				if ($scope.customField[i].type == 'textarea') {

					inputype = 'textarea';

				}

				var define = {

					title: $scope.customField[i].name,

					key: 'custom_'+$scope.customField[i].id,

					placeholder: $scope.customField[i].name,

					schema: {

						label: $scope.customField[i].name,

						title: $scope.customField[i].name,

						api: 'custom_'+$scope.customField[i].id,

						property: 'custom_'+$scope.customField[i].id,

						type: inputype,

						key: 'custom_'+$scope.customField[i].id,

						input: true

					},

					title: $scope.customField[i].name,

				};

				dumpdata.push(define);

			}

		}

	});



	$http.get(BASE_URL + 'leads/leadstatuses').then(function (LeadStatuses) {

		$scope.leadstatuses = LeadStatuses.data;

		$scope.NewStatus = function () {

			var confirm = $mdDialog.prompt()

				.title($scope.lang.new_status)

				.textContent($scope.lang.type_status_name)

				.placeholder($scope.lang.status_name)

				.ariaLabel($scope.lang.status_name)

				.initialValue('')

				.required(true)

				.ok($scope.lang.add)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function (result) {

				var dataObj = $.param({

					name: result,

				});

				$http.post(BASE_URL + 'leads/add_status', dataObj, config)

					.then(

						function (response) {

							console.log(response);

							$scope.leadstatuses.push({

								'id': response.data,

								'name': result,

							});

						},

						function (response) {

							console.log(response);

						}

					);

			}, function () {



			});

		};

	});



	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {

		$scope.leadssources = LeadSources.data;

		$scope.NewSource = function () {

			var confirm = $mdDialog.prompt()

				.title($scope.lang.new_source)

				.textContent($scope.lang.type_source_name)

				.placeholder($scope.lang.source_name)

				.ariaLabel($scope.lang.source_name)

				.initialValue('')

				.required(true)

				.ok($scope.lang.add)

				.cancel($scope.lang.cancel);



			$mdDialog.show(confirm).then(function (result) {

				var dataObj = $.param({

					name: result,

				});

				$http.post(BASE_URL + 'leads/add_source', dataObj, config)

					.then(

						function (response) {

							console.log(response);

							$scope.leadssources.push({

								'id': response.data,

								'name': result,

							});

						},

						function (response) {

							console.log(response);

						}

					);

			}, function () {



			});

		};

	});



	$scope.webleadsLoader = true;

	$http.get(BASE_URL + 'leads/get_weblead/'+FORMID).then(function (WebLeads) {

		$scope.weblead = WebLeads.data;

		$scope.webleadsLoader = false;

		var data = '<iframe src="'+BASE_URL + 'forms/wlf/'+$scope.weblead.token+'" width="600" height="800" allowfullscreen></iframe>';

		$('.editorField').text(data);

		$scope.iframeText = data;

		var formData;

		$scope.form = {

          components: JSON.parse($scope.weblead.data),

          display: 'form'

        };

		Formio.builder(document.getElementById('builder'), $scope.form, {

			builder: {

				basic: false,

				advanced: false,

				data: false,

				layout: true,

				customBasic: false,

				custom: {

					title: lang.formbuilder,

					default: true,

					weight: 10,

					components: {

						title: {

							title: lang.title,

							key: 'l_title',

							icon: 'ion-bookmark',

							default: 'Mr',

							placeholder: lang.title,

							schema: {

								label: lang.title,

								type: 'textfield',

								key: 'l_title',

								input: true

							}

						},

						name: {

							title: lang.name,

							key: 'l_name',

							icon: 'ion-android-contact',

							placeholder: lang.name,

							schema: {

								label: lang.name,

								type: 'textfield',

								key: 'l_name',

								input: true

							}

						},

						company: {

							title: lang.company,

							key: 'l_company',

							icon: 'ion-ios-briefcase',

							placeholder: lang.company,

							schema: {

								label: lang.company,

								type: 'textfield',

								key: 'l_company',

								input: true

							}

						},

						phone: {

							title: lang.phone,

							key: 'l_phone',

							icon: 'ion-ios-telephone',

							placeholder: lang.phone,

							schema: {

								label: lang.phone,

								title: lang.phone,

								type: 'number',

								key: 'l_phone',

								input: true

							}

						},

						email: {

							title: lang.email,

							key: 'l_email',

							icon: 'ion-at',

							placeholder: lang.email,

							schema: {

								label: lang.email,

								type: 'email',

								key: 'l_email',

								input: true

							}

						},

						website: {

							title: lang.website,

							key: 'l_webSite',

							icon: 'ion-earth',

							placeholder: lang.website,

							schema: {

								label: lang.website,

								type: 'textfield',

								key: 'l_webSite',

								input: true

							}

						},

						country: {

							title: lang.country,

							key: 'l_country',

							icon: 'ion-ios-flag',

							placeholder: lang.country,

							// data: {

							// 	json: [

							// 	{"value":"a","label":"A"},

							// 	{"value":"b","label":"B"},

							// 	{"value":"c","label":"C"},

							// 	{"value":"d","label":"D"}

							// 	]

							// },

							schema: {

								label: lang.country,

								type: 'textfield',

								key: 'l_country',

								input: true,

							}

						},

						state: {

							title: lang.state,

							key: 'l_state',

							icon: 'ion-map',

							placeholder: lang.state,

							schema: {

								label: lang.state,

								type: 'textfield',

								key: 'l_state',

								input: true

							}

						},

						city: {

							title: lang.city,

							key: 'l_city',

							icon: 'ion-map',

							placeholder: lang.city,

							schema: {

								label: lang.city,

								type: 'textfield',

								key: 'l_city',

								input: true

							}

						},

						zip: {

							title: lang.zip,

							key: 'l_zip',

							icon: 'ion-ios-paperplane-outline',

							placeholder: lang.zip,

							schema: {

								label: lang.zip,

								type: 'number',

								key: 'l_zip',

								input: true

							}

						},

						address: {

							title: lang.address,

							key: 'l_address',

							icon: 'ion-ios-location-outline',

							placeholder: lang.address,

							schema: {

								label: lang.address,

								type: 'textfield',

								key: 'l_address',

								input: true

							}

						},

						description: {

							title: lang.description,

							key: 'l_description',

							icon: 'ion-ios-list-outline',

							placeholder: lang.description,

							schema: {

								label: lang.description,

								type: 'textarea',

								key: 'l_description',

								input: true,

							}

						},

						date: {

							title: lang.date,

							key: 'l_date',

							icon: 'ion-ios-calendar-outline',

							placeholder: lang.date,

							schema: {

								label: lang.date,

								type: 'datetime',

								key: 'l_date',

								input: true

							}

						},

						submit: {

							title: lang.submit,

							key: 'l_submit',

							icon: '',

							placeholder: lang.submit,

							schema: {

								label: lang.submit,

								type: 'button',

								action: 'submit',

								key: 'l_submit',

								input: true

							}

						}

					}

				},

				customFields: {

					title: lang.custom_fields,

					//default: true,

					weight: 10,

					components: dumpdata, 

				},

				layout: {

					default: true,

				  	components: {

					    fieldset: false,

					    well: false,

					    tabs: false,

					    panel: false,

					    table: false

				  	}

				}

				},

				editForm: {

					textfield: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

					email: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

					textarea: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

					number: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

					button: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

					datetime: [{key: 'api',ignore: true},{key: 'time',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},

						{key: 'logic',ignore: true}],

					select: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

					columns: [{key: 'api',ignore: true},{key: 'data',ignore: true},{key: 'conditional',ignore: true},{key: 'logic',ignore: true}],

				}

			}).then(function(builder) {

			checkBuilderComponents(builder.schema.components);

			builder.on('change', function() {

				checkBuilderComponents(builder.schema.components);

				//console.log(builder.schema);

			});

			$('#saveFormContent').on('click', function() {

				var hasError = false;

				if (builder.schema.components.length > 0) {

					for (var i = 0; i < builder.schema.components.length; i++) {

						var component = builder.schema.components[i];

						hasError = true;

						if (component.key == "eMail" || component.key == "lEmail") {

							hasError = false;

							break;

						}

					}

				}

				if(hasError) {

					showToast(NTFTITLE, lang.emailError, ' danger');

				}

				if (!hasError) {

					$scope.savingLead = true;

					var dataObj = $.param({

						components: JSON.stringify(builder.schema.components)

					});

					$http.post(BASE_URL + 'leads/save_weblead_components/'+FORMID, dataObj, config)

						.then(function (response) {

							$scope.savingLead = false;

							if (response.data.success == true) {

								globals.mdToast('success', response.data.message);

								$mdSidenav('Update').close();

							} else {

								globals.mdToast('error', response.data.message);

							}

					}, function(response) {

						$scope.savingLead = false;

					});

				}

			});

		});

	});



	function checkBuilderComponents(data) {

		$('.formcomponent.drag-copy').css('display', 'block');



		function removeDuplicateFields(key, type = null) {

			var hasCustom = true;

			if (key == 'title' || key == 'lTitle') {

				hasCustom = false;

				$('#builder-l_title').css('display', 'none');

			}

			if (key == "name" || key == "lName") {

				hasCustom = false;

				$('#builder-l_name').css('display', 'none');

			}

			if (key == "companyName" || key == "lCompany") {

				hasCustom = false;

				$('#builder-l_company').css('display', 'none');

			}

			if (key == "phone" || key == "lPhone") {

				hasCustom = false;

				$('#builder-l_phone').css('display', 'none');

			}

			if (key == "eMail" || key == "lEmail") {

				hasCustom = false;

				$('#builder-l_email').css('display', 'none');

			}

			if (key == "webSite" || key == "lWebsite") {

				hasCustom = false;

				$('#builder-l_website').css('display', 'none');

			}

			if (key == "country" || key == "lCountry") {

				hasCustom = false;

				$('#builder-l_country').css('display', 'none');

			}

			if (key == "state" || key == "lState") {

				hasCustom = false;

				$('#builder-l_state').css('display', 'none');

			}

			if (key == "city" || key == "lCity") {

				hasCustom = false;

				$('#builder-l_city').css('display', 'none');

			}

			if (key == "zipCode" || key == "lZip") {

				hasCustom = false;

				$('#builder-l_zip').css('display', 'none');

			}

			if (key == "address" || key == "lAddress") {

				hasCustom = false;

				$('#builder-l_address').css('display', 'none');

			}

			if (key == "description" || key == "lDescription") {

				hasCustom = false;

				$('#builder-l_description').css('display', 'none');

			}

			if (key == "date" || key == "lDate") {

				hasCustom = false;

				$('#builder-l_date').css('display', 'none');

			}

			if (key == "submit" || key == "lSubmit") {

				hasCustom = false;

				$('#builder-l_submit').css('display', 'none');

			}

			if (key) {

				var datac = key;

				var searchText = "custom";

				var re = new RegExp("\\b"+searchText+"\\b",'i');

				if (datac.includes(searchText)) {

					var find = 'custom';

					var re = new RegExp(find, 'g');

					var keyss = datac.replace(re, '');

					$('#builder-custom_'+keyss).css('display', 'none');

				}

			}

		}

		for (var i = 0; i < data.length; i++) {

			if (data[i].type == "columns") {

				$('#builder-columns').css('display', 'none');

				for (var j = 0; j < data[i].columns.length; j++) {

					if (data[i].columns[j].components) {

						for (var k = 0; k < data[i].columns[j].components.length; k++) {

							removeDuplicateFields(data[i].columns[j].components[k].key)

							data[i].columns[j].components[k]

						}

					}

				}

			} else {

				removeDuplicateFields(data[i].key, data[i].key)

			}

		}

	}



	$scope.SaveWebLeadForm = function() {

		$scope.savingLead = true;

		if (!$scope.weblead) {

			var dataObj = $.param({

				name: '',

				assigned_id: '',

				status_id: '',

				source_id: '',

				submit_text: '',

				success_message: '',

				duplicate: '',

				notification: '',

				status: '',

			});

		} else {

			var dataObj = $.param({

				name: $scope.weblead.name,

				assigned_id: $scope.weblead.assigned_id,

				status_id: $scope.weblead.status_id,

				source_id: $scope.weblead.source_id,

				submit_text: $scope.weblead.submit_text,

				success_message: $scope.weblead.success_message,

				duplicate: $scope.weblead.duplicate,

				notification: $scope.weblead.notification,

				status: $scope.weblead.status,

			}); 

		}

		$http.post(BASE_URL + 'leads/save_weblead_form/'+FORMID, dataObj, config).then(function (response) {

			$scope.savingLead = false;

			if (response.data.success == true) {

				globals.mdToast('success', response.data.message);

			} else {

				globals.mdToast('error', response.data.message);

			}

		}, function(response) {

			$scope.savingLead = false;

		});

	};



	$scope.changeStatus = function(status) {

		var dataObj = $.param({

			status: $scope.weblead.status,

		});

		$http.post(BASE_URL + 'leads/change_weblead_status/'+FORMID, dataObj, config).then(function (response) {

			if (response.data.success == true) {

				globals.mdToast('success', response.data.message);

			} else {

				globals.mdToast('error', response.data.message);

			}

		}, function(response) {

		});

	};



	$scope.deleteForm = function() {

		var confirm = $mdDialog.confirm()

			.title($scope.lang.delete)

			.textContent($scope.lang.delete_web_form)

			.ariaLabel($scope.lang.delete)

			.targetEvent(FORMID)

			.ok($scope.lang.delete)

			.cancel($scope.lang.cancel);



		$mdDialog.show(confirm).then(function () {

			$http.post(BASE_URL + 'leads/delete_web_form/' + FORMID, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							window.location.href = BASE_URL+'leads/forms';

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



CiuisCRM.controller('WebLeads_Controller', WebLeads_Controller);

CiuisCRM.controller('WebLead_Controller', WebLead_Controller);