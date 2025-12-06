$(document).ready(function () {
    $(document).on({
        mouseenter: function () {
            if ($(window).width() < 630) {
                $('.labelGraph01').hide();
                var i = $(this).attr('data-index');
                $('.labelGraph01P' + i).show();
            }
        },
        mouseleave: function () {
            if ($(window).width() < 630) {
                $('.labelGraph01').hide();
            }
        }
    }, ".rectGraph01");
});

var cores = ['#7badf1', '#578dd7', '#4176bf', '#305e9f', '#264f89', '#1b3e70', '#36598b', '#5375a7', '#5985c7', '#416eab'];


function graph01(id, array, color = null) {
    var data = [];
    var labels = [];

    $(id + " svg").html('')

    array.datasets[0].data.map(a => {
        data.push(parseFloat(a))
    });

    array.labels.map((a, i) => {
        labels.push({
            label: a,
            data: array.datasets[0].data[i]
        })
    });

    var w = $(id).width() - 30;
    var h = $(id).height();
    var barPadding = 2;
    var mAx = d3.max(data)
    var yScale = d3.scale.linear().domain([0, mAx]).range([0, h]);
    var tamIndv = (w / data.length);
    var svg = d3.select(id + ' svg')
        .attr("width", w)
        .attr("height", h);

    svg.selectAll("rect")
        .data(data).enter()
        .append("rect")
        .attr("x", function (d, i) { return i * (w / data.length) })
        .attr("y", function (d) { return (h - yScale(d) + 35) > h ? (h - 35) : (h - yScale(d)) })
        .attr("width", w / data.length - barPadding)
        .attr("height", function (d) { return yScale(d) > 5 ? yScale(d) : 5 })
        .attr("fill", color != null ? color : "rgb(136, 196, " + (Math.floor(Math.random() * 10 + 1) * 100) + ")")
        .attr("class", "rectGraph01")
        .attr("data-index", (a, i) => { return i; })
        ;

    svg.selectAll("text")
        .data(data)
        .enter()
        .append("text")
        .text(function (d) { return d })
        .attr("x", function (d, i) { return (i * tamIndv) + (tamIndv / 2) - 4 - (d.toString().length * 2) })
        .attr("y", function (d) { return (h - yScale(d) + 15) > (h - 30) ? (h - 70) : (h - yScale(d) + 15) })
        .attr("font-family", "sans-serif")
        .attr("fill", function (d) { return (h - yScale(d) + 15) > h || labels.length > 10 ? "#7c7c7c" : "white" });

    svg.selectAll(".label")
        .data(labels)
        .enter()
        .append("text")
        .text(function (d) { return d.label })
        .attr("x", function (d, i) { return (i * tamIndv) + (tamIndv / 2) - 8 - (d.label.length * 2) })
        .attr("y", function (d, i) {
            if (i % 2 == 0) {
                return (h - 51)
            } else {
                return (h - 35)
            }
        })
        .attr("font-family", "sans-serif")
        .attr("class", (a, i) => { return "label labelGraph01 labelGraph01P" + i; })
    //.attr("fill", function (d) { return (h - yScale(d.data) + 15) > h || labels.length > 10 ? "#7c7c7c" : "white" })

    return svg;
}


function graph02(data) {
    var dataTemp = [];
    var labels = [];
    var maior = 0;

    data.datasets[0].data.map((a, i) => {
        if (parseFloat(a) > maior) {
            maior = parseFloat(a);
        }
        dataTemp.push({ label: data.labels[i], data: a });
    });

    dataTemp.map((a, i) => {
        var prct = (parseFloat(a.data) * 100) / maior;
        dataTemp[i].porcent = prct;
    });

    if (maior > 0) {
        for (var i = 0; i <= maior;) {
            var prct = (parseFloat(i) * 100) / maior;
            labels.push({ label: (i.toFixed(0)), porcent: prct });
            i = i + (maior / 10);
        }

        if (labels.label != maior) {
            labels.splice(labels.length - 1, 1);
            labels.push({ label: maior, porcent: 100 });
        }
    }

    return { data: dataTemp, labels };
}

function graph03(id, data) {
    var usr_color = 255; //Change value to change color scheme

    var canvas = document.querySelector(id);
    var ctx = canvas.getContext("2d");
    var maior = 0;

    canvas.width = 600;
    canvas.height = 415;

    var arcs = [];
    var dataTemp = [];
    data.datasets[0].data.map((a, i) => {
        if (parseFloat(a) > maior) {
            maior = parseFloat(a);
        }
        dataTemp.push({ label: data.labels[i], data: a });
    });

    function init() {
        dataTemp.map((a, i) => {
            var r = 195 - (18 * (i));
            var d = new arc(a.data, (r - (a.data.length * 3)));
            d.r = r;
            arcs.push(d);
        })

    }

    function arc(data, r) {
        this.r = 100;
        this.rot = 1;
        this.draw = function () {
            ctx.beginPath();
            ctx.arc(300, 210, this.r, (Math.PI / (2 / 3)), this.rot, false);
            ctx.lineWidth = 15;
            ctx.strokeStyle = "#c4c4c4";
            ctx.stroke();

            ctx.save();
            ctx.fillStyle = "#333";
            ctx.translate(300, 210);
            ctx.rotate(this.rot);
            ctx.font = "14px Arial Rounded MT Bold";

            ctx.fillText(data, r, 10);
            ctx.restore();
        }
    }



    function draw() {
        ctx.fillStyle = "rgba(51,51,51,0.5)";
        ctx.font = "12px Arial"
        var tam = {
            3: 274,
            4: 270,
            5: 264,
            6: 255,
            7: 250,
            8: 243,
            9: 233,
            10: 230
        }

        dataTemp.map((b, i) => {
            ctx.fillText(b.label, tam[b.label.length], (18 * (i + 1)));

            var data = (b.data * 2) / (maior + (maior * 0.05));
            var a = arcs[i];
            a.rot = data * (Math.PI * 2) - (Math.PI / 2);
            a.draw();
        });

    }

    function animloop() {
        draw();
    }


    init();
    animloop();
}

function graph04(id, data, cor, formatPam = '') {
    google.load("visualization", "1", { packages: ["corechart"] });
    google.setOnLoadCallback(drawCharts);

    function drawCharts() {
        var array = [];
        var maxValue = 0;
        if (data.datasets.length > 0 && data.datasets != null && data.datasets[0] != null) {
            $(id).show();

            if (data.datasets[1] != null) {
                array.push(['Dados', data.datasets[0].label, data.datasets[1].label]);
            } else {
                array.push(['Dados', data.datasets[0].label]);
            }

            if (data.datasets[0] != null) {
                $.each(data.datasets[0].data, function (i, a) {
                    if (maxValue < parseFloat(a)) {
                        maxValue = parseFloat(a);
                    }

                    if (data.datasets[1] != null) {
                        if (maxValue < parseFloat(data.datasets[1].data[i])) {
                            maxValue = parseFloat(data.datasets[1].data[i]);
                        }
                        array.push([data.labels[i], parseFloat(a), data.datasets[1].data[i]])
                    } else {
                        array.push([data.labels[i], parseFloat(a)])
                    }

                })
            }

            var barData = google.visualization.arrayToDataTable(array);
            var barOptions = {
                focusTarget: 'category',
                backgroundColor: 'transparent',
                colors: cor,
                fontName: 'arial',

                chartArea: {
                    left: 50,
                    top: 10,
                    width: '100%',
                    height: '70%',
                },
                bar: {
                    groupWidth: '80%',
                },
                hAxis: {
                    textStyle: {
                        fontSize: 11
                    },
                },
                vAxis: {
                    minValue: 0,
                    maxValue: maxValue,
                    baselineColor: '#DDD',
                    gridlines: {
                        color: '#DDD',
                        count: 4
                    },
                    textStyle: {
                        fontSize: 11
                    },
                    format: formatPam
                },
                legend: {
                    position: 'bottom',
                    textStyle: {
                        fontSize: 12
                    },

                },
                animation: {
                    duration: 1200,
                    easing: 'out',
                    startup: true
                }
            };

            if (formatPam == "#'%'") {
                var formatter = new google.visualization.NumberFormat({
                    fractionDigits: 0,
                    suffix: '%'
                });
                formatter.format(barData, 1);
            }

            if (formatPam == "currency") {
                var formatter = new google.visualization.NumberFormat({
                    fractionDigits: 2,
                    suffix: '',
                    groupingSymbol: ','
                });
                formatter.format(barData, 1);
            }

            var barChart = new google.visualization.ColumnChart(document.querySelector(id));
            barChart.draw(barData, barOptions);



        } else {
            $(id).hide();
        }
    }
}
var dataGraph05 = [];
var totalGraph05 = 0;
function graph05(idChart, data, moeada = false, porct = false) {
    dataGraph05 = [];
    totalGraph05 = 0;
    var dataTemp = [];
    if (data.datasets[0] != null) {
        $.each(data.datasets[0].data, function (i, a) {
            var ano = data.labels[i].toString().substr(-4);
            var mes = data.labels[i].toString().replace(ano, '');
            dataGraph05.push(mes + ano);
            dataTemp.push([parseInt(mes), (parseInt(a) * 100)])
        });
    }

    var graphData = [{
        data: dataTemp,
        color: '#71c73e'
    }];

    $.plot($(idChart + ' #graph-lines'), graphData, {
        series: {
            points: {
                show: true,
                radius: 5
            },
            lines: {
                show: true
            },
            shadowSize: 0
        },
        grid: {
            color: '#646464',
            borderColor: 'transparent',
            borderWidth: 20,
            hoverable: true
        },
        xaxis: {
            tickColor: 'transparent',
            tickDecimals: 2
        },
        yaxis: {
            tickSize: 1000
        }
    });

    $.plot($(idChart + ' #graph-bars'), graphData, {
        series: {
            bars: {
                show: true,
                barWidth: .9,
                align: 'center'
            },
            shadowSize: 0
        },
        grid: {
            color: '#646464',
            borderColor: 'transparent',
            borderWidth: 20,
            hoverable: true
        },
        xaxis: {
            tickColor: 'transparent',
            tickDecimals: 2
        },
        yaxis: {
            tickSize: 1000
        }
    });

    $(idChart + ' #graph-bars').hide();
    $(idChart + ' #lines').on('click', function (e) {
        $(idChart + ' #bars').removeClass('active');
        $(idChart + ' #graph-bars').fadeOut();
        $(this).addClass('active');
        $(idChart + ' #graph-lines').fadeIn();
        e.preventDefault();
    });

    $(idChart + ' #bars').on('click', function (e) {
        $(idChart + ' #lines').removeClass('active');
        $(idChart + ' #graph-lines').fadeOut();
        $(this).addClass('active');
        $(idChart + ' #graph-bars').fadeIn().removeClass('hidden');
        e.preventDefault();
    });

    function showTooltip(x, y, contents) {
        $('<div class="tooltipChart">' + contents + '</div>').css({
            top: y - 16,
            left: x + 20
        }).appendTo('body').fadeIn();
    }

    var previousPoint = null;
    $(idChart + ' #graph-lines, #graph-bars').bind('plothover', function (event, pos, item) {
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;
                $('.tooltipChart').remove();
                var x = item.datapoint[0],
                    y = item.datapoint[1];

                var v = (y / 100);

                if (moeada) {
                    v = 'R$ ' + ((v < 0) ? '-' : '') + formatarMoeda(v.toFixed(2).toString());
                }
                if (porct) {
                    v = v + '%';
                }

                showTooltip(item.pageX, item.pageY, v + ' em ' + dataGraph05[item.dataIndex]);
            }
        } else {
            $('.tooltipChart').remove();
            previousPoint = null;
        }
    });
}
function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min;
}

function graph06(idChart, data) {
    var graph = [];

    data.datasets[0].data.map((a, i) => {
        cor = cores[getRandomInt(0, cores.length)];
        graph.push({ title: data.labels[i], value: parseFloat(a), color: cor });
    });
    $(idChart).html('');
    $(idChart).drawDoughnutChart(graph);
}

$.fn.drawDoughnutChart = function (data, options) {
    var $this = this,
        W = $this.width(),
        H = $this.height(),
        centerX = W / 2,
        centerY = H / 2,
        cos = Math.cos,
        sin = Math.sin,
        PI = Math.PI,
        settings = $.extend({
            segmentShowStroke: true,
            segmentStrokeColor: "#1b4681",
            segmentStrokeWidth: 1,
            baseColor: "rgba(255,255,255,0.5)",
            baseOffset: 4,
            edgeOffset: 10,//offset from edge of $this
            percentageInnerCutout: 75,
            animation: true,
            animationSteps: 90,
            animationEasing: "easeInOutExpo",
            animateRotate: true,
            tipOffsetX: -8,
            tipOffsetY: -45,
            tipClass: "doughnutTip",
            summaryClass: "doughnutSummary",
            summaryTitle: "TOTAL:",
            summaryTitleClass: "doughnutSummaryTitle",
            summaryNumberClass: "doughnutSummaryNumber",
            beforeDraw: function () { },
            afterDrawed: function () { },
            onPathEnter: function (e, data) { },
            onPathLeave: function (e, data) { }
        }, options),
        animationOptions = {
            linear: function (t) {
                return t;
            },
            easeInOutExpo: function (t) {
                var v = t < .5 ? 8 * t * t * t * t : 1 - 8 * (--t) * t * t * t;
                return (v > 1) ? 1 : v;
            }
        },
        requestAnimFrame = function () {
            return window.requestAnimationFrame ||
                window.webkitRequestAnimationFrame ||
                window.mozRequestAnimationFrame ||
                window.oRequestAnimationFrame ||
                window.msRequestAnimationFrame ||
                function (callback) {
                    window.setTimeout(callback, 1000 / 60);
                };
        }();

    settings.beforeDraw.call($this);

    var $svg = $('<svg width="' + W + '" height="' + H + '" viewBox="0 0 ' + W + ' ' + H + '" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"></svg>').appendTo($this),
        $paths = [],
        easingFunction = animationOptions[settings.animationEasing],
        doughnutRadius = Min([H / 2, W / 2]) - settings.edgeOffset,
        cutoutRadius = doughnutRadius * (settings.percentageInnerCutout / 100),
        segmentTotal = 0;

    //Draw base doughnut
    var baseDoughnutRadius = doughnutRadius + settings.baseOffset,
        baseCutoutRadius = cutoutRadius - settings.baseOffset;
    $(document.createElementNS('http://www.w3.org/2000/svg', 'path'))
        .attr({
            "d": getHollowCirclePath(baseDoughnutRadius, baseCutoutRadius),
            "fill": settings.baseColor
        })
        .appendTo($svg);

    //Set up pie segments wrapper
    var $pathGroup = $(document.createElementNS('http://www.w3.org/2000/svg', 'g'));
    $pathGroup.attr({ opacity: 0 }).appendTo($svg);

    //Set up tooltip
    var $tip = $('<div class="' + settings.tipClass + '" />').appendTo('body').hide(),
        tipW = $tip.width(),
        tipH = $tip.height();

    //Set up center text area
    var summarySize = ((cutoutRadius - (doughnutRadius - cutoutRadius)) * 2) + 72,
        $summary = $('<div class="' + settings.summaryClass + '" />')
            .appendTo($this)
            .css({
                width: summarySize + "px",
                "margin-left": -(summarySize / 2) + "px",
            });
    var $summaryTitle = $('<p class="' + settings.summaryTitleClass + '">' + settings.summaryTitle + '</p>').appendTo($summary);
    var $summaryNumber = $('<p class="' + settings.summaryNumberClass + '"></p>').appendTo($summary).css({ opacity: 0 });

    for (var i = 0, len = data.length; i < len; i++) {
        segmentTotal += data[i].value;
        $paths[i] = $(document.createElementNS('http://www.w3.org/2000/svg', 'path'))
            .attr({
                "stroke-width": settings.segmentStrokeWidth,
                "stroke": settings.segmentStrokeColor,
                "fill": data[i].color,
                "data-order": i
            })
            .appendTo($pathGroup)
            .on("mouseenter", pathMouseEnter)
            .on("mouseleave", pathMouseLeave)
            .on("mousemove", pathMouseMove);
    }

    //Animation start
    animationLoop(drawPieSegments);

    //Functions
    function getHollowCirclePath(doughnutRadius, cutoutRadius) {
        //Calculate values for the path.
        //We needn't calculate startRadius, segmentAngle and endRadius, because base doughnut doesn't animate.
        var startRadius = -1.570,// -Math.PI/2
            segmentAngle = 6.2831,// 1 * ((99.9999/100) * (PI*2)),
            endRadius = 4.7131,// startRadius + segmentAngle
            startX = centerX + cos(startRadius) * doughnutRadius,
            startY = centerY + sin(startRadius) * doughnutRadius,
            endX2 = centerX + cos(startRadius) * cutoutRadius,
            endY2 = centerY + sin(startRadius) * cutoutRadius,
            endX = centerX + cos(endRadius) * doughnutRadius,
            endY = centerY + sin(endRadius) * doughnutRadius,
            startX2 = centerX + cos(endRadius) * cutoutRadius,
            startY2 = centerY + sin(endRadius) * cutoutRadius;
        var cmd = [
            'M', startX, startY,
            'A', doughnutRadius, doughnutRadius, 0, 1, 1, endX, endY,//Draw outer circle
            'Z',//Close path
            'M', startX2, startY2,//Move pointer
            'A', cutoutRadius, cutoutRadius, 0, 1, 0, endX2, endY2,//Draw inner circle
            'Z'
        ];
        cmd = cmd.join(' ');
        return cmd;
    };
    function pathMouseEnter(e) {
        var order = $(this).data().order;
        $tip.text(data[order].title + ": R$ " + formatarMoeda(parseFloat(data[order].value).toFixed(2)))
            .fadeIn(200);
        settings.onPathEnter.apply($(this), [e, data]);
    }
    function pathMouseLeave(e) {
        $tip.hide();
        settings.onPathLeave.apply($(this), [e, data]);
    }
    function pathMouseMove(e) {
        $tip.css({
            top: e.pageY + settings.tipOffsetY,
            left: e.pageX - $tip.width() / 2 + settings.tipOffsetX
        });
    }
    function drawPieSegments(animationDecimal) {
        var startRadius = -PI / 2,//-90 degree
            rotateAnimation = 1;
        if (settings.animation && settings.animateRotate) rotateAnimation = animationDecimal;//count up between0~1

        drawDoughnutText(animationDecimal, segmentTotal);

        $pathGroup.attr("opacity", animationDecimal);

        //If data have only one value, we draw hollow circle(#1).
        if (data.length === 1 && (4.7122 < (rotateAnimation * ((data[0].value / segmentTotal) * (PI * 2)) + startRadius))) {
            $paths[0].attr("d", getHollowCirclePath(doughnutRadius, cutoutRadius));
            return;
        }
        for (var i = 0, len = data.length; i < len; i++) {
            var segmentAngle = rotateAnimation * ((data[i].value / segmentTotal) * (PI * 2)),
                endRadius = startRadius + segmentAngle,
                largeArc = ((endRadius - startRadius) % (PI * 2)) > PI ? 1 : 0,
                startX = centerX + cos(startRadius) * doughnutRadius,
                startY = centerY + sin(startRadius) * doughnutRadius,
                endX2 = centerX + cos(startRadius) * cutoutRadius,
                endY2 = centerY + sin(startRadius) * cutoutRadius,
                endX = centerX + cos(endRadius) * doughnutRadius,
                endY = centerY + sin(endRadius) * doughnutRadius,
                startX2 = centerX + cos(endRadius) * cutoutRadius,
                startY2 = centerY + sin(endRadius) * cutoutRadius;
            var cmd = [
                'M', startX, startY,//Move pointer
                'A', doughnutRadius, doughnutRadius, 0, largeArc, 1, endX, endY,//Draw outer arc path
                'L', startX2, startY2,//Draw line path(this line connects outer and innner arc paths)
                'A', cutoutRadius, cutoutRadius, 0, largeArc, 0, endX2, endY2,//Draw inner arc path
                'Z'//Cloth path
            ];
            $paths[i].attr("d", cmd.join(' '));
            startRadius += segmentAngle;
        }
    }
    function drawDoughnutText(animationDecimal, segmentTotal) {
        $summaryNumber
            .css({ opacity: animationDecimal })
            .text('R$ ' + formatarMoeda(parseFloat((segmentTotal * animationDecimal).toFixed(2))));
    }
    function animateFrame(cnt, drawData) {
        var easeAdjustedAnimationPercent = (settings.animation) ? CapValue(easingFunction(cnt), null, 0) : 1;
        drawData(easeAdjustedAnimationPercent);
    }
    function animationLoop(drawData) {
        var animFrameAmount = (settings.animation) ? 1 / CapValue(settings.animationSteps, Number.MAX_VALUE, 1) : 1,
            cnt = (settings.animation) ? 0 : 1;
        requestAnimFrame(function () {
            cnt += animFrameAmount;
            animateFrame(cnt, drawData);
            if (cnt <= 1) {
                requestAnimFrame(arguments.callee);
            } else {
                settings.afterDrawed.call($this);
            }
        });
    }
    function Max(arr) {
        return Math.max.apply(null, arr);
    }
    function Min(arr) {
        return Math.min.apply(null, arr);
    }
    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }
    function CapValue(valueToCap, maxValue, minValue) {
        if (isNumber(maxValue) && valueToCap > maxValue) return maxValue;
        if (isNumber(minValue) && valueToCap < minValue) return minValue;
        return valueToCap;
    }
    return $this;
};


function formatarMoeda(valor) {
    if (isNaN(valor)) {
        return '0,00';
    }
    var valor = valor.toString();
    if (valor.indexOf('.') > -1) {
        if (valor.split('.')[1].length == 1) {
            valor = valor + '0';
        }
    } else {
        valor = valor + '00';
    }
    valor = parseInt(valor.replace(/[\D]+/g, ''));
    valor = valor.toString().replace(/([0-9]{2})$/g, ",$1");

    if (valor.length > 6) {
        valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    }

    return valor;
}