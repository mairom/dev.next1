/* vendor section */

function Vendors_Controller($scope, $http, $mdSidenav) {

	"use strict";

	$scope.toggleFilter = buildToggler('ContentFilter');

	$scope.Create = buildToggler('Create');

	$scope.CreateGroup = buildToggler('CreateGroup');



	globals.get_countries();



	$scope.vendor_list = {

		order: '',

		limit: 5,

		page: 1

	};



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}

	$scope.vendorsLoader = true;

	$scope.getStates = function (country) {

		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {

			$scope.states = States.data;

		});

	};

	$scope.close = function () {

		$mdSidenav('ContentFilter').close();

		$mdSidenav('Create').close();

		$mdSidenav('CreateGroup').close();

	};

	var gdata;

	$http.get(BASE_URL + 'vendors/groups').then(function (Data) {

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

				text: lang.vendor + '<br>' + lang.group,

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

	$http.get(BASE_URL + 'vendors/get_vendors').then(function (Vendors) {

		$scope.vendors = Vendors.data;



		$scope.limitOptions = [5, 10, 15, 20];

		if ($scope.vendors.length > 20) {

			$scope.limitOptions = [5, 10, 15, 20, $scope.vendors.length];

		}



		$scope.vendorsLoader = false;



		$http.get(BASE_URL + 'vendors/get_vendor_groups').then(function (Groups) {

			$scope.group = Groups.data;



			$scope.NewGroup = function () {

				globals.createDialog(lang.new + ' ' + lang.vendor + ' ' + lang.group, lang.type_group, lang.group + ' ' + lang.name, '', lang.add, lang.cancel, 'vendors/add_group', function (response) {

					if (response.success == true) {

						globals.mdToast('success', response.message);

					} else {

						globals.mdToast('error', response.message);

					}

					$http.get(BASE_URL + 'vendors/get_vendor_groups').then(function (Groups) {

						$scope.group = Groups.data;

					});

				});

			};



			$scope.EditGroup = function (id, name, event) {

				globals.editDialog(lang.edit + ' ' + lang.group + ' ' + lang.name, lang.vendor_title, lang.group + ' ' + lang.name, name, event, lang.save, lang.cancel, 'vendors/update_group/' + id, function (response) {

					if (response.success == true) {

						globals.mdToast('success', response.message);

						$http.get(BASE_URL + 'vendors/get_vendor_groups').then(function (Groups) {

							$scope.group = Groups.data;

						});

					} else {

						globals.mdToast('error', response.message);

					}

				});

			};



			$scope.DeleteVendorGroup = function (index) {

				var name = $scope.group[index];

				globals.deleteDialog(lang.attention, lang.delete_group, name.id, lang.doIt, lang.cancel, 'vendors/remove_group/' + name.id, function (response) {

					if (response.success == true) {

						globals.mdToast('success', response.message);

						$http.get(BASE_URL + 'vendors/get_vendor_groups').then(function (Groups) {

							$scope.group = Groups.data;

						});

					} else {

						globals.mdToast('error', response.message);

					}

				});

			};

		});

		$scope.AddVendor = function () {

			$scope.saving = true;

			if (!$scope.vendor) {

				var dataObj = $.param({

					name: '',

					groupid: '',

					taxoffice: '',

					taxnumber: '',

					ssn: '',

					executive: '',

					address: '',

					zipcode: '',

					country_id: '',

					state: '',

					city: '',

					town: '',

					phone: '',

					fax: '',

					email: '',

					web: '',



				});

			} else {

				var dataObj = $.param({

					name: $scope.vendor.name,

					groupid: $scope.vendor.group_id,

					//taxoffice: $scope.vendor.taxoffice,

					//taxnumber: $scope.vendor.taxnumber,

					ssn: $scope.vendor.ssn,

					//	executive: $scope.vendor.executive,

					address: $scope.vendor.address,

					zipcode: $scope.vendor.zipcode,

					country_id: $scope.vendor.country_id,

					state: $scope.vendor.state_id,

					city: $scope.vendor.city,

					town: $scope.vendor.town,

					phone: $scope.vendor.phone,

					fax: $scope.vendor.fax,

					email: $scope.vendor.email,

					web: $scope.vendor.web,

					risk: 0,

					type: $scope.isIndividual,

				});

			}

			var posturl = BASE_URL + 'vendors/create';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							if (response.data.id) {

								window.location.href = BASE_URL + 'vendors/vendor/' + response.data.id;

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

						console.log(response);

					}

				);

		};

		$scope.filter = {};

		$scope.getOptionsFor = function (propName) {

			return ($scope.vendors || []).map(function (item) {

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



function Vendor_Controller($scope, $http, $filter, $mdSidenav, $mdDialog) {

	"use strict";

	$scope.ReminderForm = buildToggler('ReminderForm');

	$scope.NewContact = buildToggler('NewContact');

	$scope.Update = buildToggler('Update');

	$('.update-view').hide();



	$scope.vendorsLoader = true;

	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();



		};

	}

	globals.get_countries();

	$scope.getStates = function (country) {

		$http.get(BASE_URL + 'api/get_states/' + country).then(function (States) {

			$scope.states = States.data;

		});

	};

	$scope.close = function () {

		$mdSidenav('ReminderForm').close();

		$mdSidenav('NewContact').close();

		$mdSidenav('Update').close();

	};

	$http.get(BASE_URL + 'vendors/get_vendor_groups').then(function (Groups) {

		$scope.groups = Groups.data;

	});

	$http.get(BASE_URL + 'vendors/get_vendor/' + VENDORRID).then(function (Vendors) {

		$scope.vendor = Vendors.data;
		$scope.purchases = Vendors.data.purchases;
		$scope.isActive = $scope.vendor.vendor_isActive;

		$scope.getStates($scope.vendor.country_id);

		$scope.vendorsLoader = false;

	});



	$scope.savingCustomer = false;

	$scope.UpdateVendor = function () {

		$scope.savingCustomer = true;

		var dataObj = $.param({

			name: $scope.vendor.name,

			groupid: $scope.vendor.group_id,

			//taxoffice: $scope.vendor.taxoffice,

			//	taxnumber: $scope.vendor.taxnumber,

			ssn: $scope.vendor.ssn,

			//	executive: $scope.vendor.executive,

			address: $scope.vendor.address,

			zipcode: $scope.vendor.zipcode,

			country_id: $scope.vendor.country_id,

			state: $scope.vendor.state,

			city: $scope.vendor.city,

			town: $scope.vendor.town,

			phone: $scope.vendor.phone,

			fax: $scope.vendor.fax,

			email: $scope.vendor.email,

			web: $scope.vendor.web,

			risk: 0,

			status_id: +$scope.vendor.vendor_status_id,

		});

		var posturl = BASE_URL + 'vendors/vendor/' + VENDORRID;

		$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					$scope.savingCustomer = false;

					if (response.data.success == true) {

						$mdSidenav('Update').close();

						globals.mdToast('success', response.data.message);

						$http.get(BASE_URL + 'vendors/get_vendor/' + VENDORRID).then(function (Vendors) {

							$scope.vendor = Vendors.data;
							$scope.purchases = Vendors.data.purchases;
						});

					} else {

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					$scope.savingCustomer = false;

				}

			);



	};

	$scope.Delete = function (index) {

		console.log("sdfs");

		globals.deleteDialog(lang.attention, lang.delete_vendor, VENDORRID, lang.doIt, lang.cancel, 'vendors/remove/' + VENDORRID, function (response) {

			if (response.success == true) {

				window.location.href = BASE_URL + 'vendors';

			} else {

				globals.mdToast('error', response.message);

			}

		});

	};

	$scope.CloseModal = function () {

		$mdDialog.cancel();

	};

}



CiuisCRM.controller('Vendors_Controller', Vendors_Controller);

CiuisCRM.controller('Vendor_Controller', Vendor_Controller);