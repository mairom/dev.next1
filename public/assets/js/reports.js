function Reports_Controller($scope, $http) {
	"use strict";
	$scope.filtros = [];
	var graphNvOport = null;
	var graphOrigem = null;
	var atv_graph = null;
	var gerandoPdf = false;

	var graphNvOportMes = null;
	var graphNvOport = null;
	var graphNvOportPorClientes = null;

	$scope.report = [];

	$scope.baixarPdf = function () {
		//$('#mdTab' + $scope.ctrl.selectedIndex).css('width', '820')
		if (gerandoPdf) {
			return;
		}
		gerandoPdf = true;

		var node = document.getElementById("mdTab" + $scope.ctrl.selectedIndex);
		domtoimage.toPng(node).then(function (dataUrl) {
			var printWindow = window.open('', '', 'height=400,width=800');
			printWindow.document.write('<html><head><title>GraficosReport' + new Date().toISOString() + '</title>');
			printWindow.document.write('</head><body >');
			printWindow.document.write("<img onload = 'window.print()' src = '" + dataUrl + "'>");
			printWindow.document.write(`<style type="text/css" media="print">@page { size: landscape; }</style>`);
			printWindow.document.write('</body></html>');
			printWindow.document.close();
			gerandoPdf = false;

		}).catch(function (error) {
			console.error('oops, something went wrong!', error);
		});

		return;
		html2canvas(document.querySelector("#mdTab" + $scope.ctrl.selectedIndex), { allowTaint: true }).then(canvas => {
			const imgData = canvas.toDataURL('image/png', 1.0);
			var printWindow = window.open('', '', 'height=400,width=800');
			printWindow.document.write('<html><head><title>GraficosReport' + new Date().toISOString() + '</title>');
			printWindow.document.write('</head><body >');
			printWindow.document.write("<img onload = 'window.print()' src = '" + imgData + "'>");

			printWindow.document.write(`<style type="text/css" media="print">@page { size: landscape; }</style>`);

			printWindow.document.write('</body></html>');
			printWindow.document.close();
			//	printWindow.print();

			/*

			const pdf = new jsPDF({
				orientation: 'p',
				unit: 'in',
				format: [100, 100]
			});
			pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
			pdf.save('GraficosReport' + new Date().toISOString() + '.pdf');


			let imgData = canvas.toDataURL('image/png');
			let imgWidth = 210; // Largura em mm de um a4
			let pageHeight = 297; // Altura em mm de um a4

			let imgHeight = canvas.height * imgWidth / canvas.width;
			let heightLeft = imgHeight;
			let position = 0;
			let pdf = new jsPDF('p', 'mm');
			let fix_imgWidth = 15; // Vai subindo e descendo esses valores ate ficar como queres
			let fix_imgHeight = 15; // Vai subindo e descendo esses valores ate ficar como queres

			pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
			heightLeft -= pageHeight;

			while (heightLeft >= 0) {
				position = heightLeft - imgHeight;
				pdf.addPage();
				pdf.addImage(imgData, 'PNG', 0, position, imgWidth + fix_imgWidth, imgHeight + fix_imgHeight);
				heightLeft -= pageHeight;
			}

			pdf.save(filename);
			*/

			gerandoPdf = false;
		});
	}

	$http.get(BASE_URL + 'leads/leadslist/1').then(function (data) {
		$scope.leadslist = data.data;
	});
	$http.get(BASE_URL + 'api/staff').then(function (Staff) {
		$scope.staff = Staff.data;
	});
	$http.get(BASE_URL + 'leads/leadsources').then(function (LeadSources) {
		$scope.leadssources = LeadSources.data;
	});
	var retrievePath = localStorage.getItem('findPath');
	if (retrievePath) {
		retrievePath = JSON.parse(retrievePath);
	}
	$scope.overview = {};
	$scope.overview.loader = true;
	$http.get(BASE_URL + 'api/stats').then(function (Stats) {
		$scope.stats = Stats.data;
		$scope.overview.loader = false;
		if (retrievePath) {
			if (retrievePath.type == 'report' && retrievePath.view == 'timesheet') {
				$scope.ctrl = {};
				$scope.ctrl.selectedIndex = 4;
				$('#timesheetTab').click();
				$scope.getTimesheet();
				localStorage.clear();
			}
		}
		new Chart($('#invoice_chart_by_status'), {
			type: 'horizontalBar',
			data: $scope.stats.invoice_chart_by_status,
			options: {
				legend: {
					display: false,
				},
				responsive: true
			}
		});
		new Chart($('#leads_to_win_by_leadsource'), {
			type: 'horizontalBar',
			data: $scope.stats.leads_to_win_by_leadsource,
			options: {
				legend: {
					display: false,
				}
			}
		});
		new Chart($('#leads_by_leadsource'), {
			type: 'horizontalBar',
			data: $scope.stats.leads_by_leadsource,
			options: {
				legend: {
					display: false,
				}
			}
		});
		new Chart($('#expensesbycategories'), {
			type: 'bar',
			data: $scope.stats.expenses_by_categories,
			options: {
				legend: {
					display: false,
				}
			}
		});
		new Chart($('#top_selling_staff_chart'), {
			type: 'line',
			data: $scope.stats.top_selling_staff_chart,
			options: {
				legend: {
					display: false,
				}
			}
		});
		var CustomerGraph;
		$.get(BASE_URL + 'report/customer_monthly_increase_chart/' + $scope.CustomerReportMonth, function (response) {
			var ctx = $('#customergraph_ciuis-xe').get(0).getContext('2d');
			CustomerGraph = new Chart(ctx, {
				'type': 'bar',
				data: response,
				options: {
					responsive: true
				},
			});
		}, 'json');
		$scope.CustomerMonthChanged = function () {
			lead_graph.destroy();
			$.get(BASE_URL + 'report/customer_monthly_increase_chart/' + $scope.CustomerReportMonth, function (response) {
				var ctx = $('#customergraph_ciuis-xe').get(0).getContext('2d');
				CustomerGraph = new Chart(ctx, {
					'type': 'bar',
					data: response,
					options: {
						responsive: true
					},
				});
			}, 'json');
		};
		var lead_graph;
		$.get(BASE_URL + 'report/lead_graph/' + $scope.LeadReportMonth, function (response) {
			var ctx = $('#lead_graph').get(0).getContext('2d');
			lead_graph = new Chart(ctx, {
				'type': 'bar',
				data: response,
				options: {
					responsive: true
				},
			});
		}, 'json');
		$scope.LeadMonthChanged = function () {
			lead_graph.destroy();
			$.get(BASE_URL + 'report/lead_graph/' + $scope.LeadReportMonth, function (response) {
				var ctx = $('#lead_graph').get(0).getContext('2d');
				lead_graph = new Chart(ctx, {
					'type': 'bar',
					data: response,
					options: {
						responsive: true
					},
				});
			}, 'json');
		};
		var expenses_payments_graph;
		$.get(BASE_URL + 'report/expenses_payments_graph/' + $scope.paymentsExpensesByYear, function (response) {
			var ctx = $('#incomingsvsoutgoins').get(0).getContext('2d');
			expenses_payments_graph = new Chart(ctx, {
				'type': 'line',
				data: response,
				options: {
					responsive: true
				},
			});
		}, 'json');
		$scope.getPaymentsExpensesByYear = function () {
			expenses_payments_graph.destroy();
			$.get(BASE_URL + 'report/expenses_payments_graph/' + $scope.paymentsExpensesByYear, function (response) {
				var ctx = $('#incomingsvsoutgoins').get(0).getContext('2d');
				expenses_payments_graph = new Chart(ctx, {
					'type': 'line',
					data: response,
					options: {
						responsive: true
					},
				});
			}, 'json');
		};
	});
	$scope.getResultados = function () {
		$scope.report2 = [];
		$scope.report2.totalLeadsPipeline = '';
		$scope.report2.totalAtividades = '';
		$scope.report2.totalLeadsAtivos = '';
		$scope.report2.totalLeadsAtrasados = '';
		$scope.report2.totalLeadsEmDia = '';

		var dataObj = $.param({
			dt_de: $scope.filtros.dt_de != null ? moment($scope.filtros.dt_de).format("YYYY-MM-DD") : null,
			dt_ate: $scope.filtros.dt_ate != null ? moment($scope.filtros.dt_ate).format("YYYY-MM-DD") : null,
			flt_funil: $scope.filtros.flt_funil,
			flt_funcionario: $scope.filtros.flt_funcionario,
			flt_origem: $scope.filtros.flt_origem,
			id_company: $scope.filtros.id_company
		})

		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		var MainChartOptions = {
			responsive: true,
			maintainAspectRatio: false
		};


		$http.post(BASE_URL + 'panel/getReports_nvOportunidadePorCliente', dataObj, config).then(function (data) {
			if (data.data != null && $('#divgraphNvOportPorClientes').length > 0) {
				if (graphNvOportPorClientes == null) {
					graphNvOportPorClientes = new Chart($('#graphNvOportPorClientes'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNvOportPorClientes.data = data.data;
					graphNvOportPorClientes.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_nvOportunidade', dataObj, config).then(function (data) {
			graphNvOport = graph01('#graphNvOport', data.data);
			return;
			if (data.data != null) {
				if (graphNvOport == null) {
					graphNvOport = new Chart($('#graphNvOport'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNvOport.data = data.data;
					graphNvOport.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_nvOportunidade_mes', dataObj, config).then(function (data) {
			if (data.data != null) {

				$scope.graphNvOportMes = graph02(data.data);
				return;
				if (graphNvOportMes == null) {
					graphNvOportMes = new Chart($('#graphNvOportMes'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNvOportMes.data = data.data;
					graphNvOportMes.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_graphOrigem', dataObj, config).then(function (data) {

			$scope.graphOrigem = graph02(data.data);
			return;
			if (data.data != null) {
				if (graphOrigem == null) {
					graphOrigem = new Chart($('#graphOrigem'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphOrigem.data = data.data;
					graphOrigem.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_atv_graph', dataObj, config).then(function (data) {

			graph03('#atv_graph', data.data)
			return;
			if (data.data != null) {
				if (atv_graph == null) {
					atv_graph = new Chart($('#atv_graph').get(0).getContext('2d'), {
						type: 'horizontalBar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					atv_graph.data = data.data;
					atv_graph.update();
				}
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalAtividades', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report2.totalAtividades = data.data;
			}
		});
		$http.post(BASE_URL + 'panel/getReports_totalLeadsAtivos', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report2.totalLeadsAtivos = data.data;
				console.log($scope.report2.totalLeadsAtivos);
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalLeadsAtrasados', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report2.totalLeadsAtrasados = data.data;
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalLeadsEmDia', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report2.totalLeadsEmDia = data.data;
			}
		});

		$http.post(BASE_URL + 'panel/getReports_totalLeadsPipeline', dataObj, config).then(function (data) {

			if (data.data != null) {
				$scope.report2.totalLeadsPipeline = data.data;
			}
		});
	}
	$scope.getResultados();


	$http.get(BASE_URL + 'report/get_reports_data').then(function (response) {
		$scope.report = response.data;
		Highcharts.chart('incomingsvsoutgoins_weekly', {
			chart: {
				type: 'column'
			},
			title: {
				text: ''
			},
			height: 380,
			colors: ['#5ba768', '#e26862'],
			xAxis: {
				categories: $scope.report.weekdays,
				title: {
					text: null
				}
			},
			pointRange: 86400000,
			yAxis: {
				min: 0,
				title: {
					text: '',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {
				valueSuffix: ''
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true
					}
				}
			},
			legend: {
				layout: 'horixontal',
				align: 'right',
				verticalAlign: 'top',
				floating: true,
				borderWidth: 1,
				backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
				shadow: true
			},
			credits: {
				enabled: false
			},
			options: {
				responsive: true
			},
			series: [
				{ name: lang.payments, data: $scope.report.payments },
				{ name: lang.expenses, data: $scope.report.expenses },
			]
		});
	});

	$scope.getTimesheet = function () {
		$scope.timesheet = {};
		$scope.timesheet.loader = true;
		$http.get(BASE_URL + 'report/get_timesheet_data').then(function (response) {
			$scope.timesheets = response.data.timesheet;
			$scope.total_time = response.data.total;
			$scope.timesheet.loader = false;
			$scope.itemsPerPage = 5;
			$scope.currentPage = 0;
			$scope.range = function () {
				var rangeSize = 5;
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
				return Math.ceil($scope.timesheets.length / $scope.itemsPerPage) - 1;
			};
		});
	}
	$scope.viewReport = function () {
		$scope.loadingReport = true;
		var dataObj = $.param({
			period: $scope.period,
			exporttype: $scope.exportType,
			from: $scope.from ? moment($scope.from).format("YYYY-MM-DD") : "",
			to: $scope.to ? moment($scope.to).format("YYYY-MM-DD") : "",
		});
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		$scope.invoiceReport = 'true';
		$scope.customersReport = 'true';
		$scope.expensesReport = 'true';
		$scope.proposalsReport = 'true';
		$scope.depositsReport = 'true';
		$scope.ordersReport = 'true';
		$scope.vendorsReport = 'true';
		$scope.purchaseReport = 'true';
		$scope.contactsReport = 'true';
		$scope.ticketsReport = 'true';
		$scope.taskReport = 'true';
		$scope.leadsReport = 'true';
		$scope.productsReport = 'true';
		$scope.staffReport = 'true';
		$scope.projectsReport = 'true';
		var posturl = BASE_URL + 'report/viewReports';
		$http.post(posturl, dataObj, config).then(function (response) {
			if (response.data.success == true) {
				if (response.data.exporttype == 'invoices') {
					$scope.invoice_list = {
						order: '',
						limit: 5,
						page: 1
					};
					$scope.invoiceReport = 'false';
					$scope.invoices = response.data.result.data_invoice;
					$scope.subtotal = response.data.result.total;
					$scope.inv_limitOptions = [5, 10, 15, 20];
					if ($scope.invoices.length > 20) {
						$scope.inv_limitOptions = [5, 10, 15, 20, $scope.invoices.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == 'customers') {
					$scope.customer_list = {
						order: '',
						limit: 5,
						page: 1
					};
					$scope.customersReport = 'false';
					$scope.customers = response.data.result;
					$scope.cust_limitOptions = [5, 10, 15, 20];
					if ($scope.customers.length > 20) {
						$scope.cust_limitOptions = [5, 10, 15, 20, $scope.customers.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == 'expenses') {
					$scope.expense_list = {
						order: '',
						limit: 5,
						page: 1
					};
					$scope.expensesReport = 'false';
					$scope.expenses = response.data.result.data_expense;
					$scope.subtotal = response.data.result.total;
					$scope.exp_limitOptions = [5, 10, 15, 20];
					if ($scope.expenses.length > 20) {
						$scope.exp_limitOptions = [5, 10, 15, 20, $scope.expenses.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == 'proposals') {
					$scope.proposals_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.proposalsReport = 'false';
					$scope.proposals = response.data.result.data_proposal;
					$scope.subtotal = response.data.result.total;
					$scope.pro_limitOptions = [5, 10, 15, 20];
					if ($scope.proposals.length > 20) {
						$scope.proposal_limitOptions = [5, 10, 15, 20, $scope.proposals.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "deposits") {
					$scope.deposit_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.depositsReport = 'false';
					$scope.deposits = response.data.result;
					$scope.dep_limitOptions = [5, 10, 15, 20];
					if ($scope.deposits.length > 20) {
						$scope.dep_limitOptions = [5, 10, 15, 20, $scope.deposits.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "orders") {
					$scope.order_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.ordersReport = 'false';
					$scope.orders = response.data.result;
					$scope.odr_limitOptions = [5, 10, 15, 20];
					if ($scope.orders.length > 20) {
						$scope.odr_limitOptions = [5, 10, 15, 20, $scope.orders.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "vendors") {
					$scope.vendor_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.vendorsReport = 'false';
					$scope.vendors = response.data.result;
					$scope.ven_limitOptions = [5, 10, 15, 20];
					if ($scope.vendors.length > 20) {
						$scope.ven_limitOptions = [5, 10, 15, 20, $scope.vendors.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "purchases") {
					$scope.purchase_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.purchaseReport = 'false';
					$scope.purchases = response.data.result.data_purchase;
					$scope.subtotal = response.data.result.total;
					$scope.po_limitOptions = [5, 10, 15, 20];
					if ($scope.purchases.length > 20) {
						$scope.po_limitOptions = [5, 10, 15, 20, $scope.purchases.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "contacts") {
					$scope.contact_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.contactsReport = 'false';
					$scope.contacts = response.data.result;
					$scope.contact_limitOptions = [5, 10, 15, 20];
					if ($scope.contacts.length > 20) {
						$scope.contact_limitOptions = [5, 10, 15, 20, $scope.contacts.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "tickets") {
					$scope.ticket_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.ticketsReport = 'false';
					$scope.tickets = response.data.result;
					$scope.tkt_limitOptions = [5, 10, 15, 20];
					if ($scope.tickets.length > 20) {
						$scope.tkt_limitOptions = [5, 10, 15, 20, $scope.tickets.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "tasks") {
					$scope.task_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.taskReport = 'false';
					$scope.tasks = response.data.result;
					$scope.task_limitOptions = [5, 10, 15, 20];
					if ($scope.tasks.length > 20) {
						$scope.task_limitOptions = [5, 10, 15, 20, $scope.tasks.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "leads") {
					$scope.lead_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.leadsReport = 'false';
					$scope.leads = response.data.result;
					$scope.lead_limitOptions = [5, 10, 15, 20];
					if ($scope.leads.length > 20) {
						$scope.lead_limitOptions = [5, 10, 15, 20, $scope.leads.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "products") {
					$scope.product_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.productsReport = 'false';
					$scope.products = response.data.result;
					$scope.product_limitOptions = [5, 10, 15, 20];
					if ($scope.products.length > 20) {
						$scope.product_limitOptions = [5, 10, 15, 20, $scope.products.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "staff") {
					$scope.staff_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.staffReport = 'false';
					$scope.staff = response.data.result;
					$scope.staff_limitOptions = [5, 10, 15, 20];
					if ($scope.staff.length > 20) {
						$scope.staff_limitOptions = [5, 10, 15, 20, $scope.staff.length];
					}
					$scope.loadingReport = false;
				} else if (response.data.exporttype == "projects") {
					$scope.project_list = {
						order: '',
						limit: 5,
						page: 1
					}
					$scope.projectsReport = 'false';
					$scope.projects = response.data.result;
					$scope.pro_limitOptions = [5, 10, 15, 20];
					if ($scope.projects.length > 20) {
						$scope.pro_limitOptions = [5, 10, 15, 20, $scope.projects.length];
					}
					$scope.loadingReport = false;
				}
			} else {
				$scope.loadingReport = false;
				globals.mdToast('error', response.data.message);
			}
		});
	}
	$scope.exportData = function (type, period) {
		$scope.csvLoader = true;
		if (period == '7') {
			var dataObj = $.param({
				period: period,
				from: moment($scope.from).format("YYYY-MM-DD"),
				to: moment($scope.to).format("YYYY-MM-DD"),
				type: type,
			});
		} else {
			var dataObj = $.param({
				period: period,
				type: type,
			});
		}
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		var posturl = BASE_URL + 'report/exportData';
		$http.post(posturl, dataObj, config).then(
			function (response) {
				var file = new Blob([response.data], {
					type: 'application/csv'
				});
				var fileURL = URL.createObjectURL(file);
				var a = document.createElement('a');
				a.href = fileURL;
				a.target = '_blank';
				a.download = type + '.csv';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
				$scope.csvLoader = false;
			});
	}
	$scope.generatePdf = function (type, period) {
		$scope.generated_url = '#';
		$scope.pdfLoader = true;
		var dataObj = $.param({
			period: period,
			type: type,
			from: moment($scope.from).format("YYYY-MM-DD"),
			to: moment($scope.to).format("YYYY-MM-DD"),
		});
		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		var posturl = BASE_URL + 'report/generatePdf';
		$http.post(posturl, dataObj, config).then(
			function (response) {
				$scope.pdfLoader = false;
				if (response.data.success == true) {
					$scope.generated_url = BASE_URL + 'uploads/files/reports/' + response.data.file_name;
					window.open(BASE_URL + 'uploads/files/reports/' + response.data.file_name, '_blank');
				} else {
					$scope.pdfLoader = false;
					globals.mdToast('error', 'error generating pdf');
				}
			},
			function (response) {
				$scope.pdfLoader = false;
				globals.mdToast('error', 'error generating pdf');
			}
		);
	}
	$scope.filtros2 = [];
	var graphFaturamento = null;
	var FaturamentoehDespesa = null;
	var despesasECompras = null;
	var receitasXDespesas = null;
	$scope.anosList = [];
	var data = new Date();
	for (var i = 0; i <= 5; i++) {
		$scope.anosList.push(data.getFullYear() - i);
	}
	$scope.filtros2.ano = $scope.anosList[0];
	$http.get(BASE_URL + 'products/get_products').then(function (data) {
		$scope.produtosList = data.data;
	});
	$scope.getResultados2 = function () {
        $scope.report.fatBruto = '0';
        $scope.report.totalDepsECompras = '0';
        $scope.report.RestLiquid = '0';
        $scope.report.MargemRestLiq = '0';

        var dataObj = $.param({
            ano: $scope.filtros2.ano,
            produto: $scope.filtros2.produto,
            vendedor: $scope.filtros2.vendedor,
            situacao: $scope.filtros2.situacao,
            mes: $scope.filtros2.mes,
        })

        var config = {
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
            }
        };
        var MainChartOptions = {
            responsive: true,
        };

        $http.post(BASE_URL + 'DashInvoices/getReports_faturamentoTotais', dataObj, config).then(function (data) {
            if (data.data != null) {
                $scope.report.fatBruto = data.data.fatBruto;
                $scope.report.totalDepsECompras = data.data.totalDepsECompras;

                $scope.report.RestLiquid = parseFloat(data.data.fatBruto) - parseFloat(data.data.totalDepsECompras);
                var MargemRestLiq = ((parseFloat($scope.report.RestLiquid) * 100) / parseFloat(data.data.fatBruto));
                $scope.report.MargemRestLiq = (!isNaN(MargemRestLiq) && MargemRestLiq > 0 ? MargemRestLiq.toFixed(2) : '0') + '%';
            }
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamento', dataObj, config).then(function (data) {
            $scope.graphFaturamento = graph02(data.data);
            return;
            if (data.data != null) {
                if (graphFaturamento == null) {
                    graphFaturamento = new Chart($('#graphFaturamento'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    graphFaturamento.data = data.data;
                    graphFaturamento.update();
                }
            }
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphFaturamentoehDespesa', dataObj, config).then(function (data) {

            graph04('#FaturamentoehDespesa', data.data, ['#bf2a0f', '#1a33b9'])
            return;
            if (data.data != null) {
                if (FaturamentoehDespesa == null) {
                    FaturamentoehDespesa = new Chart($('#FaturamentoehDespesa'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    FaturamentoehDespesa.data = data.data;
                    FaturamentoehDespesa.update();
                }
            }
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphDespesasECompras', dataObj, config).then(function (data) {

            graph04('#despesasECompras', data.data, ['#f00', '#f17d1a'])
            return;
            if (data.data != null) {
                if (despesasECompras == null) {
                    despesasECompras = new Chart($('#despesasECompras'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    despesasECompras.data = data.data;
                    despesasECompras.update();
                }
            }
        });

        $http.post(BASE_URL + 'DashInvoices/getReports_graphreceitasEDespesas', dataObj, config).then(function (data) {


            graph04('#receitasXDespesas', data.data, ['#bf2a0f', '#1a33b9'])
            return;

            if (data.data != null) {
                if (receitasXDespesas == null) {
                    receitasXDespesas = new Chart($('#receitasXDespesas'), {
                        type: 'bar',
                        data: data.data,
                        options: MainChartOptions
                    });
                } else {
                    receitasXDespesas.data = data.data;
                    receitasXDespesas.update();
                }
            }
        });


	}
	$scope.getResultados2();

	var graphFaturamentoTop10 = null;
	var graphNovosClientes = null;
	var graphClientesAtivos = null;
	$scope.getResultados3 = function () {
		$scope.report.totalClientes = '0';
		$scope.report.totalLTV = '0';
		$scope.report.totalLTMes = '0';
		$scope.report.totalLTDias = '0';

		var dataObj = $.param({
			ano: $scope.filtros.ano,
			produto: $scope.filtros.produto,
			vendedor: $scope.filtros.vendedor,
		})

		var config = {
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8;'
			}
		};
		var MainChartOptions = {
			responsive: true,
			maintainAspectRatio: false
		};

		$http.post(BASE_URL + 'customersDash/getReports_totaisCustomers', dataObj, config).then(function (data) {
			if (data.data != null) {

				$scope.report.totalClientes = data.data.totalClientes;
				$scope.report.totalLTV = data.data.totalLTV;
				$scope.report.totalLTMes = data.data.totalLTMes;
				$scope.report.totalLTDias = data.data.totalLTDias;
			}
		});

		$http.post(BASE_URL + 'customersDash/getReports_graphFaturamentoTop10', dataObj, config).then(function (data) {


			graph01('#graphFaturamentoTop10', data.data, null, "#3f51b5");

			return;
			if (data.data != null) {
				if (graphFaturamentoTop10 == null) {
					graphFaturamentoTop10 = new Chart($('#graphFaturamentoTop10'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphFaturamentoTop10.data = data.data;
					graphFaturamentoTop10.update();
				}
			}
		});

		$http.post(BASE_URL + 'customersDash/getReports_graphNovosClientes', dataObj, config).then(function (data) {
			graphNovosClientes = graph01('#graphNovosClientes', data.data, null, "#3f51b5");
			return;

			if (data.data != null) {
				if (graphNovosClientes == null) {
					graphNovosClientes = new Chart($('#graphNovosClientes'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphNovosClientes.data = data.data;
					graphNovosClientes.update();
				}
			}
		});

		$http.post(BASE_URL + 'customersDash/getReports_graphClientesAtivos', dataObj, config).then(function (data) {
			graph05('#graphClientesAtivos', data.data);
			return;
			if (data.data != null) {
				if (graphClientesAtivos == null) {
					graphClientesAtivos = new Chart($('#graphClientesAtivos'), {
						type: 'bar',
						data: data.data,
						options: MainChartOptions
					});
				} else {
					graphClientesAtivos.data = data.data;
					graphClientesAtivos.update();
				}
			}
		});



	}

	$scope.getResultados3();
}
CiuisCRM.controller('Reports_Controller', Reports_Controller);
