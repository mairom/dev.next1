function Products_Controller($scope, $http, $mdSidenav, $filter, $mdDialog, $compile, fileUpload) {

	"use strict";

	$http.get(BASE_URL + 'api/custom_fields_by_type/' + 'product').then(function (custom_fields) {
		$scope.all_custom_fields = custom_fields.data;
		$scope.custom_fields = $filter('filter')($scope.all_custom_fields, {
			active: 'true',
		});
	});

	$http.get(BASE_URL + 'vendors/get_vendors').then(function (Vendors) {
		$scope.vendors = Vendors.data;
	});


	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '.')).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

	$scope.Create = buildToggler('Create');

	$scope.toggleFilter = buildToggler('ContentFilter');

	$scope.CreateCategory = buildToggler('CreateCategory');

	$scope.ImportProductsNav = buildToggler('ImportProductsNav');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.importing = false;

	$scope.importerror = false;

	$scope.importProduct = function () {

		$scope.importing = true;

		var file = $scope.product_file;

		var uploadUrl = BASE_URL + 'products/productsimport';

		fileUpload.uploadFileToUrl(file, uploadUrl, function (response) {

			if ((response.success == true) && (!response.errors)) {

				globals.mdToast('success', response.message);

				$mdSidenav('ImportProductsNav').close();

			} else if ((response.success == false) && (response.errors)) {

				$scope.importerror = true;

				$scope.errors = response.errors;

				console.log(response.errors);

				globals.mdToast('error', response.message);

			} else {

				$scope.importerror = true;

				$scope.errors = response.errors;

				globals.mdToast('error', response.message);

				console.log(response.errors);

			}

			$http.get(BASE_URL + 'products/get_products').then(function (Products) {

				$scope.products = Products.data;

			});

			$scope.productFiles = true;

			$scope.importing = false;



		});

	};



	$scope.GoToProduct = function (id) {

		$scope.loadingData = true;

		globals.PRODUCTID = id;

		$http.get(BASE_URL + 'products/get_content/product/' + id).then(function (Data) {

			$scope.content = Data.data;

			var div = angular.element(document.getElementById("pageContent"));

			div.html($scope.content);

			$compile(div.contents())($scope);

			$scope.loadingData = false;

		});

	};



	$scope.close = function () {

		$mdSidenav('Create').close();

		$mdSidenav('ContentFilter').close();

		$mdSidenav('CreateCategory').close();

		$mdSidenav('ImportProductsNav').close();

	};



	var cdata;

	$http.get(BASE_URL + 'products/categories/').then(function (Data) {

		cdata = Data.data;

		var data = [];

		for (var i = 0; i < cdata.length; i++) {

			data.push([cdata[i].name, parseInt(cdata[i].y)]);

		}



		Highcharts.chart('container', {

			chart: {

				polar: true,

				plotBackgroundColor: '#f3f3f3',

				plotBorderWidth: 0,

				plotShadow: false

			},

			title: {

				text: lang.product + '<br>' + lang.categories,

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

					// startAngle: -90,

					// endAngle: 90,

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

				}],

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

			var w = $('#container').closest(".wrapper").width()

			chart.setSize(

				w, w * (3 / 4), false

			);

		}



		$(window).resize(redrawchart);

		redrawchart();

	});



	$scope.product_list = {

		order: '',

		limit: 10,

		page: 1

	};

	$http.get(BASE_URL + 'products/get_products').then(function (Products) {

		$scope.products = Products.data;

		$scope.limitOptions = [10, 15, 20];

		if ($scope.products.length > 20) {

			$scope.limitOptions = [10, 15, 20, $scope.products.length];

		}



		$http.get(BASE_URL + 'products/get_product_categories').then(function (Categories) {

			$scope.category = Categories.data;



			$scope.NewCategory = function () {

				globals.createDialog(lang.addProductCategory, lang.type_categoryname, lang.categoryname, '', lang.add, lang.cancel, 'products/add_category', function (response) {

					if (response.success == true) {

						globals.mdToast('success', response.message);

					} else {

						globals.mdToast('error', response.message);

					}

					$http.get(BASE_URL + 'products/get_product_categories').then(function (Categories) {

						$scope.category = Categories.data;

					});

				});

			};



			$scope.EditCategory = function (id, name, event) {

				globals.editDialog(lang.edit + lang.categoryname, lang.type_categoryname, lang.categoryname, name, event, lang.save, lang.cancel, 'products/update_category/' + id, function (response) {

					if (response.success == true) {

						globals.mdToast('success', response.message);

						$http.get(BASE_URL + 'products/get_product_categories').then(function (Categories) {

							$scope.category = Categories.data;

						});

					} else {

						globals.mdToast('error', response.message);

					}

				});

			};



			$scope.DeleteProductCategory = function (index) {

				var name = $scope.category[index];

				globals.deleteDialog(lang.attention, lang.confirm_product_category_delete, name.id, lang.do_it, lang.cancel, 'products/remove_category/' + name.id, function (response) {

					if (response.success == true) {

						globals.mdToast('success', response.message);

						$http.get(BASE_URL + 'products/get_product_categories').then(function (Categories) {

							$scope.category = Categories.data;

						});

					} else {

						globals.mdToast('error', response.message);

					}

				});

			};



			$scope.deleteProduct = function (PRODUCTID) {

				// Appending dialog to document.body to cover sidenav in docs app

				var confirm = $mdDialog.confirm()

					.title(lang.attention)

					.textContent(lang.productattentiondetail)

					.ariaLabel(lang.delete + ' ' + lang.product)

					.targetEvent(PRODUCTID)

					.ok(lang.doIt)

					.cancel(lang.cancel);



				$mdDialog.show(confirm).then(function () {

					$http.post(BASE_URL + 'products/remove/' + PRODUCTID, config)

						.then(

							function (response) {

								if (response.data.success == true) {

									$.gritter.add({

										title: '<b>' + NTFTITLE + '</b>',

										text: response.data.message,

										class_name: 'color success'

									});

								}

								$http.get(BASE_URL + 'products/get_products').then(function (Products) {

									$scope.products = Products.data;

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



		$scope.AddProduct = function () {

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



			if (!$scope.product) {

				var dataObj = $.param({

					name: '',

					category: '',

					purchaseprice: '',

					saleprice: '',

					code: '',

					tax: '',

					stock: '',

					description: '',

					custom_fields: ''

				});

			} else {

				var dataObj = $.param({
					name: $scope.product.productname,
					categoryid: $scope.product.categoryid,
					purchaseprice: moneyEua($scope.product.purchase_price),
					saleprice:  moneyEua($scope.product.sale_price),
					code: $scope.product.code,
					//tax: $scope.product.vat,
					stock: $scope.product.stock,
					description: $scope.product.description,
					custom_fields: $scope.tempArr,
					vendor_id: $scope.product.vendor_id,
				});

			}

			var posturl = BASE_URL + 'products/create';

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							$.gritter.add({

								title: '<b>' + NTFTITLE + '</b>',

								text: response.data.message,

								class_name: 'color success'

							});

							$mdSidenav('Create').close();

							$http.get(BASE_URL + 'products/get_products').then(function (Products) {

								$scope.products = Products.data;

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



function Product_Controller($scope, $http, $mdSidenav, $mdDialog) {

	"use strict";


	$http.get(BASE_URL + 'vendors/get_vendors').then(function (Vendors) {
		$scope.vendors = Vendors.data;
	});

	var PRODUCTID = globals.PRODUCTID;



	$scope.Update = buildToggler('Update');

	$scope.toggleFilter = buildToggler('ContentFilter');



	function buildToggler(navID) {

		return function () {

			$mdSidenav(navID).toggle();

		};

	}



	$scope.close = function () {

		$mdSidenav('Update').close();

	};



	$http.get(BASE_URL + 'api/custom_fields_data_by_type/' + 'product/' + PRODUCTID).then(function (custom_fields) {

		$scope.custom_fields = custom_fields.data;

	});



	$http.get(BASE_URL + 'products/get_product/' + PRODUCTID).then(function (Product) {

		$scope.product = Product.data;



		$http.get(BASE_URL + 'products/get_product_categories').then(function (Categories) {

			$scope.category = Categories.data;

		});



		$scope.UpdateProduct = function () {

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



			if (!$scope.product) {

				var dataObj = $.param({

					name: '',

					category: '',

					purchaseprice: '',

					saleprice: '',

					code: '',

					tax: '',

					stock: '',

					description: '',

					custom_fields: ''

				});

			} else {
				var dataObj = $.param({
					name: $scope.product.productname,
					categoryid: $scope.product.categoryid,
					purchaseprice:  moneyEua($scope.product.purchase_price),
					saleprice:  moneyEua($scope.product.sale_price),
					code: $scope.product.code,
					tax: $scope.product.vat,
					stock: $scope.product.stock,
					description: $scope.product.description,
					custom_fields: $scope.tempArr,
					vendor_id: $scope.product.vendor_id,
				});

			}



			var posturl = BASE_URL + 'products/update/' + PRODUCTID;

			$http.post(posturl, dataObj, config)

				.then(

					function (response) {

						if (response.data.success == true) {

							globals.mdToast('success', response.data.message);

							$mdSidenav('Update').close();

							$http.get(BASE_URL + 'products/get_product/' + PRODUCTID).then(function (Product) {

								$scope.product = Product.data;

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



		$scope.Delete = function () {

			// Appending dialog to document.body to cover sidenav in docs app

			var confirm = $mdDialog.confirm()

				.title(lang.attention)

				.textContent(lang.productattentiondetail)

				.ariaLabel(lang.delete + ' ' + lang.product)

				.targetEvent(PRODUCTID)

				.ok(lang.doIt)

				.cancel(lang.cancel);



			$mdDialog.show(confirm).then(function () {

				$http.post(BASE_URL + 'products/remove/' + PRODUCTID, config)

					.then(

						function (response) {

							console.log(response);

							window.location.href = BASE_URL + 'products';

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

	$scope.moneyEua = function (v) {
		var a = v ? parseFloat(parseFloat(v.toString().replaceAll('.', '').replace(',', '').replace(/([0-9]{2})$/g, ".$1")).toFixed(2)) : 0;
		return a;
	};

	$scope.formatReal = function (int) {
		return new Intl.NumberFormat('pt-BR', { currency: 'BRL' }).format(int);
	}

}



CiuisCRM.controller('Products_Controller', Products_Controller);

CiuisCRM.controller('Product_Controller', Product_Controller);
