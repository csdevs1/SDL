@extends('layouts.app')
@if(Auth::check())
    @section('content')
        <div class="navbar-company">
            <div class="navbar-company">
            <nav class="navbar" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <span class="navbar-brand">Licenciamiento de aplicativo m&oacute;vil de reparto</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <form class="navbar-form navbar-right" role="search">
                        <div class="form-group">
                            <div class="input-group">
                                <button type="button" class="btn btn-success btn-downlod" disabled="disabled"><span class="glyphicon glyphicon-download-alt"></span></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                <input type="text" class="form-control input-sm calendar-input" value="<?php echo date("Y-m");?>" placeholder="yyyy-mm" size="7"/>
                            </div>
                        </div>
                    </form>
                    <p class="navbar-text navbar-right">Total del mes <span class="label label-info label-total-month">...</span></p>
                </div>
            </nav>
        </div>
        </div>

        <div class="dashboard">
            <div class="jumbotron text-center">
                <span class="glyphicon glyphicon-refresh glyphicon-spin-infinite"></span> cargando ...
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-12">
                        <div id="chart_documents"></div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.0/js/bootstrap-datepicker.min.js"></script>
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            var dashboard = function(date){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    contentType:  false,
                    data:{date:date},
                    url: '/statistic/licence'
                });
            }

            function daysInMonth(month,year) {
                return new Date(year, month, 0).getDate();
            }

            var drawChart=function(date=""){
                $('#chart_documents').html("");
                if(date==""){
                    var d = new Date(),
                        m = ("0" + (d.getMonth() + 1)).slice(-2),
                        y = d.getFullYear(),
                        date=y+''+m;
                }

                var get_info=dashboard(date);
                get_info.done(function(r){
                    $('.jumbotron').fadeOut(500);
                    $('#chart_documents').fadeIn(1000);
                    var response=r['result'],
                        data=[],
                        Header= ["Dia","Dispositivos usados","Licencia Maxima"],
                        month={},total={};
                    data.push(Header)
                    for(var val in response){
                        var imei=response[val].imei,
                            dia=response[val].dia;
                        if(dia in month == false){
                            month[dia]={};
                        }
                        month[dia][response[val].imei]=true;
                        total[imei] = true;
                    }
                    var last_day=daysInMonth(6,2017);
                    for (var i=1;i<=last_day;i++) {
                        var day = i < 10 ? "0"+i : i,
                            temp=[];
                        var month2=month.hasOwnProperty(i) ? Object.keys(month[i]).length : 0;
                        temp.push(day,month2,50);
                        data.push(temp);
                    }
                    var data_documents = new google.visualization.arrayToDataTable(data);
                    var chart = new google.visualization.ComboChart(document.getElementById("chart_documents"));
                    chart.draw(data_documents, {
                        legend : "none",
                        backgroundColor: "transparent",
                        vAxis: {title: "Licencias"},
                        hAxis: {title: "dias del mes"},
                        seriesType: 'bars',
                        series: {1: {type: 'line'}},
                        colors: ["lightblue","purple"]
                    });
                    $(".label-total-month").html(Object.keys(total).length);
                   /*
                   This is for the Documents chart
                   data.push(Header);
                    for (var i in response) {
                        var temp=[],
                            temp2=[];
                        if(response[i].status!=''){
                            for(var x in response[i].status){
                                temp2.push(response[i]['status'][x]);
                            }
                         }
                        temp.push(response[i].office_name);
                        temp.push(temp2);
                        data.push(temp);
                    }
                    var data_documents = new google.visualization.arrayToDataTable(data);
                    var chart = new google.visualization.ComboChart(document.getElementById("chart_documents"));

                    chart.draw(data_documents, {
                        backgroundColor: "transparent",
                        title : "Avance de reparto de documentos",
                        vAxis: {title: "Documentos"},
                        hAxis: {title: "Oficinas"},
                        seriesType: "bars",
                        isStacked: true,
                        colors:["red","green","blue","yellow","orange"]
                    });*/

                });
            }

            $(document).on("change",".calendar-input",function() {
                var date=$(this).val().replace('-', ''),
                    selectedM=$(this).val().split('-'),
                    d = new Date(),
                    m = d.getMonth() + 1;
                if(parseInt(selectedM[1]) < m)
                    $('.btn-downlod').removeAttr('disabled');
                else
                    $('.btn-downlod').attr('disabled','disabled');
                drawChart(date);
            });

            $(document).ready(function(){
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(drawChart);
                var d = new Date(),
                    m = ("0" + (d.getMonth() + 1)).slice(-2),
                    y = d.getFullYear();
                    d.setMonth(d.getMonth()-3);
                    var mStart=("0" + (d.getMonth() + 1)).slice(-2),
                    startDate=y+'-'+mStart,
                    endDate=y+'-'+m;
                $(".calendar-input").datepicker({
                    language: "es",
                    format: "yyyy-mm",
                    viewMode: "months",
                    minViewMode: "months",
                    autoclose: true,
                    startDate: startDate,
                    endDate: endDate,
                    orientation: "auto right"
                });
            });
        </script>
    @endsection
@endif
