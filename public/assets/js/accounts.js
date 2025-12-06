function Accounts_Controller($scope, $http, $mdSidenav) {

	"use strict";



	$scope.Create = buildToggler('Create');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();



		};

	}

	$scope.close = function () {

		$mdSidenav('Create').close();

	};



	$http.get(BASE_URL + 'api/accounts_total').then(function (Total) {

		$scope.total = Total.data;

	});

	$http.get(BASE_URL + 'accounts/get_accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

		$scope.AddAccount = function () {

			if ($scope.isBankType === true) {

				$scope.isBank = 1;

			}

			if ($scope.isBankType === false) {

				$scope.isBank = 0;

			}

			var dataObj = $.param({

				name: $scope.account.name,

				bankname: $scope.account.bankname,

				branchbank: $scope.account.branchbank,

				account: $scope.account.account,

				iban: $scope.account.iban,

				type: $scope.isBank,

			});

			var posturl = BASE_URL + 'accounts/create';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							console.log(response);

							globals.mdToast('success', response.data.message);

							$mdSidenav('Create').close();

							$scope.accounts.push({

								'id': response.data.id,

								'name': response.data.name,

								'amount': response.data.amount,

								'icon': response.data.icon,

								'status': response.data.status,

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

	});

}



function Account_Controller($scope, $http, $mdSidenav, $mdDialog) {

	"use strict";



	$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

		$scope.accounts = Accounts.data;

	});



	$scope.QuickTransfer_ = false;

	$scope.transferring = false;



	$scope.QuickTransfer = function () {

		$scope.QuickTransfer_ = true;

	};



	$scope.CancelTransfer = function () {

		$scope.QuickTransfer_ = false;

	};



	$scope.Update = buildToggler('Update');



	$scope.Detail = function (id) {

		$mdDialog.show({

			contentElement: '#payment-' + id,

			parent: angular.element(document.body),

			targetEvent: id,

			clickOutsideToClose: true

		});

	};



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();



		};

	}



	$scope.close = function () {

		$mdSidenav('Update').close();

		$mdDialog.hide();

	};



	$http.get(BASE_URL + 'accounts/get_account/' + ACCOUNTID).then(function (Account) {

		$scope.account = Account.data;



		if ($scope.account.payments.length > 0 ) {

			$scope.model = {

				isDisabled: true

			};

		}



		$scope.MakeTransfer = function () {

			$scope.transferring = true;

			var dataObj = $.param({

				from_account_id: $scope.account.id,

				to_account_id: $scope.To_Account_ID,

				amount: $scope.TransferAmount,

			});

			var posturl = BASE_URL + 'accounts/make_transfer/' + ACCOUNTID ;

			$http.post(posturl, dataObj, config)

			.then(

				function (response) {

					if (response.data.success == true) {

						globals.mdToast('success', response.data.message);

						$http.get(BASE_URL + 'accounts/get_account/' + ACCOUNTID).then(function (Account) {

							$scope.account = Account.data;

						});

						$http.get(BASE_URL + 'api/accounts').then(function (Accounts) {

							$scope.accounts = Accounts.data;

						});

						//Stop progress spinner

						$scope.transferring = false;

						// Hide section after successful transfer

						$scope.QuickTransfer_ = false;

						$mdSidenav('EventForm').close();

						// Clear field data

						$scope.To_Account_ID = undefined;

						$scope.TransferAmount = '';

					} else {

						$scope.transferring = false;

						globals.mdToast('error', response.data.message);

					}

				},

				function (response) {

					console.error(response);

				}

			);



		};



		$scope.current_balance = $scope.account.account_total;



		if ($scope.account.status === true) {

			$scope.isActive = 0;

		} else {

			$scope.isActive = 1;

		}

		$scope.UpdateAccount = function () {

			var dataObj = $.param({

				name: $scope.account.name,

				bankname: $scope.account.bankname,

				branchbank: $scope.account.branchbank,

				account: $scope.account.account,

				iban: $scope.account.iban,

				status: $scope.isActive,

			});

			var posturl = BASE_URL + 'accounts/update/' + ACCOUNTID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if(response.data.success == true) {

							$mdSidenav('Update').close();

							globals.mdToast('success', response.data.message);

						} else {

							$mdSidenav('Update').close();

							globals.mdToast('error', response.data.message);

						}

					},

					function (response) {

						console.log(response);

					}

				);

		};



		$scope.Delete = function (index) {

			globals.deleteDialog(lang.attention, lang.delete_account, ACCOUNTID, lang.doIt, lang.cancel, 'accounts/remove/' + ACCOUNTID, function(response) {

				if (response.success == true) {

					globals.mdToast('success', response.message);

					window.location.href = BASE_URL + 'accounts';

				} else {

					globals.mdToast('error',response.message);

				}

			});

		};

	});

}



CiuisCRM.controller('Accounts_Controller', Accounts_Controller);

CiuisCRM.controller('Account_Controller', Account_Controller);