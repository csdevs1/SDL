@extends('layouts.app')
@if(Auth::check())
    @section('content')

        <div class="navbar-company">
            <nav class="navbar" role="navigation">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                        <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                            <form class="navbar-form navbar-right" role="export" method="post" id="form-date"action="/reports/summary">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </div>
                                        <input type="text" class="form-control input-sm  calendar-input" id="start" name="start" value="<?php echo date('Y-m-d'); ?>" placeholder="yyyy-mm-dd" size="10">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-minus"></span>
                                        </div>
                                        <input type="text" class="form-control  input-sm calendar-input" id="end" name="end" value="<?php echo date('Y-m-d'); ?>" placeholder="yyyy-mm-dd" size="10">
                                        <span class="input-group-btn">
                                            <button id="seach_button" class="btn btn-success input-sm" type="button"><span class="glyphicon glyphicon-send"></span></button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <h4 class="comfortaa">On Time</h4>
                    <div style="max-width: 250px; max-height: 250px; margin: 0 auto">
                        <div id="container-speed" style="width: 300px; height: 200px; float: left"></div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <h4 class="comfortaa">Estado de Documentos</h4>
                    <div style="max-width: 250px; max-height: 250px;margin:auto">
                        <canvas id="documents"></canvas>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <h4 class="comfortaa">Resumen de Rechazos Totales</h4>
                    <div style="max-width: 250px; max-height: 250px;margin:auto">
                        <canvas id="rejects"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <h4 class="comfortaa">On Time Historicos</h4>
                    <div style="width:400px; height:275px;margin-top:35px;">
                        <canvas id="on_time" width="400" height="275"></canvas>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <h4 class="comfortaa">Entregas por Canal de Distribucion</h4>
                    <div style="width:550px; height:250px;margin-left:-79px;margin-top:35px;">
                        <canvas id="delivery_channel" width="550" height="275"></canvas>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <h4 class="comfortaa">Resumen de Rechazos Parciales</h4>
                    <div style="max-width: 250px; max-height: 250px;margin:auto">
                        <canvas id="partial-rejects"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-chartjs/2.7.0/vue-chartjs.full.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.0/js/bootstrap-datepicker.min.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/highcharts-more.js"></script>
        <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
        <script>
            $(document).on('click','#seach_button',function(){
                var date_start=$('#start').val(),
                    date_end=$('#end').val();
                if($chart1!='')
                    $chart1.destroy();
                if($chart2!='')
                    $chart2.destroy();
                if($chart3!='')
                    $chart3.destroy();
                if($chart4!='')
                    $chart4.destroy();
                if($chart5!='')
                    $chart5.destroy();

                clearInterval($interval);
                clearInterval($interval2);
                clearInterval($interval3);
                clearInterval($interval4);
                clearInterval($interval5);
                clearInterval($interval6);

                documents(date_start,date_end);
                total_rejects(date_start,date_end);
                partial_rejects(date_start,date_end);
                distro_channel(date_start,date_end);
                on_time_history(date_start,date_end);
                gauge(date_start,date_end);
            });
            $chart1='';
            $chart2='';
            $chart3='';
            $chart4='';
            $chart5='';
            $interval='';
            $interval2='';
            $interval3='';
            $interval4='';
            $interval5='';
            $interval6='';
            
            // DOCUMENTOS ACEPTADOS
                var dashboard = function(date_start, date_end){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{office_group:41,date_ini:date_start,date_end:date_end},
                        url: '/dashboard/list/deliveries'
                    });
                }

                var get_document_info = function(id){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{id:id},
                        url: '/get/documents'
                    });
                }

                var documents=function(date_start,date_end){
                    var doc_summary=dashboard(date_start,date_end);
                    doc_summary.done(function(r){
                        var status={},
                            doc_id={};
                        try{
                            for(var i in r[0]['status']){
                                status[r[0]['status'][i]['f1']]=0;
                                doc_id[r[0]['status'][i]['f1']]=[];
                            }
                            for(var i in r[0]['status']){
                                status[r[0]['status'][i]['f1']]+=1;
                                doc_id[r[0]['status'][i]['f1']].push(r[0]['status'][i]['f2']);
                            }
                            status = $.map(status, function(el) { return el });
                            var canvas = document.getElementById("documents");
                            var ctx = canvas.getContext("2d");
                            $chart1 = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    datasets: [{
                                        // Aceptada, Rechazada(status: 4), Reparto
                                        data: [status[1],status[3],status[0]],
                                        backgroundColor: [
                                            "#F7464A",
                                            "#46BFBD",
                                            "#EEEE51"
                                        ],
                                        values:doc_id,
                                        keys:[1,4,0]
                                    }],
                                    labels: [
                                        "Aceptada",
                                        "Rechazo",
                                        "Reparto"
                                    ]
                                }
                            });
                            canvas.onclick = function (evt) {
                                var activePoints = $chart1.getElementsAtEvent(evt);
                                var chartData = activePoints[0]['_chart'].config.data;
                                var idx = activePoints[0]['_index'];
                                var label = 'Documentos '+chartData.labels[idx];
                                var key = chartData.datasets[0].keys[idx];
                                var value = chartData.datasets[0].values[key];
                                $('#modal').modal('toggle');
                                $('.modal-title').text(label);
                                var table='<table class="table table-hover"><thead><tr><th>Documento</th><th>Patente</th><th>Status</th></tr></thead><tbody>';
                                $('.modal-body').html('');
                                $('.modal-body').append(table);
                                for(var i in value){
                                    var doc=get_document_info(value[i]);
                                    doc.done(function(r){
                                        r=JSON.parse(r);
                                        var table_body='<tr><td>'+r.document+'</td><td>'+r.plate_number+'</td><td>'+r.status+'</td></tr>';
                                        $('.modal-body .table.table-hover tbody').append(table_body);
                                    });
                                }
                                var table_end='</tbody></table>';
                                $('.modal-body').append(table_end);
                            }

                            // Bring life to the graph
                            $interval=setInterval(function () {
                                var update_doc_graph=dashboard(date_start,date_end);
                                update_doc_graph.done(function(r){
                                    var status={};
                                    for(var i in r[0]['status'])
                                        status[r[0]['status'][i]['f1']]=0;
                                    for(var i in r[0]['status'])
                                        status[r[0]['status'][i]['f1']]+=1;
                                    $chart1.data.datasets[0].data=[status[1],status[3],status[0]];
                                    $chart1.update();
                                });
                            }, 1000);

                        } catch(e){
                            console.log(e);
                        }
                    });

                }
            // DOCUMENTOS ACEPTADOS

            // RESUMEN RECHAZADOS
                var get_total_rejects = function(date_start,date_end,id){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{date_ini:date_start,date_end:date_end,office_group_id:41,office_id:0},
                        url: '/documents/rejected/reason'
                    });
                }

                var total_rejects=function (date_start,date_end){
                    var get_rejects=get_total_rejects(date_start,date_end);
                    get_rejects.done(function(r){
                        var total=0,
                            reason_of_rejection=[],
                            percentage=[],
                            doc_info=[];
                        for(var i in r){
                            total+=parseInt(r[i].total);
                        }
                        for(var i in r){
                            reason_of_rejection.push(r[i].description);
                            percentage.push(r[i].total);
                            doc_info.push(JSON.parse(r[i].di_info));
                        }
                        var canvas = document.getElementById("rejects");
                        var ctx = canvas.getContext("2d");
                        $chart2 = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                datasets: [{
                                    // Aceptada, Rechazada(status: 4), Reparto
                                    data: percentage,
                                    backgroundColor: [
                                        "#F7464A",
                                        "#46BFBD",
                                        "#EEEE51",
                                        "#49FF00",
                                        "#551A8B",
                                        "##3B80DF",
                                        "##ACDF3B",
                                        "#5A5939",
                                        "#0004FF",
                                    ],
                                    doc_info:doc_info
                                }],
                                labels: reason_of_rejection
                            },options: {
                                legend: {
                                    display: false,
                                    position: 'bottom'
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            var allData = data.datasets[tooltipItem.datasetIndex].data;
                                            var tooltipLabel = data.labels[tooltipItem.index];
                                            var tooltipData = allData[tooltipItem.index];
                                            var tooltipPercentage = Math.floor((tooltipData / total) * 100);
                                            return tooltipLabel + ': '+tooltipData+' (' + tooltipPercentage + '%)';
                                        }
                                    }
                                }
                            }
                        });
                        canvas.onclick = function (evt) {
                            var activePoints = $chart2.getElementsAtEvent(evt);
                            var chartData = activePoints[0]['_chart'].config.data;
                            var idx = activePoints[0]['_index'];
                            var label = chartData.labels[idx];
                            var value = chartData.datasets[0].data[idx];
                            $('#modal').modal('toggle');
                            $('.modal-title').text(label);
                            var doc_info = chartData.datasets[0].doc_info[idx];
                            var table='<table class="table table-hover"><thead><tr><th>Documento</th><th>Descripcion</th><th>Codigo</th></tr></thead><tbody>';
                            $('.modal-body').html('');
                            $('.modal-body').append(table);
                            for(var i in doc_info){
                                var document=doc_info[i].f1,
                                    p_description=doc_info[i].f2.description,
                                    p_code=doc_info[i].f2.code,
                                    table_body='<tr><td>'+document+'</td><td>'+p_description+'</td><td>'+p_code+'</td></tr>';
                                $('.modal-body .table.table-hover tbody').append(table_body);
                            }
                            var table_end='</tbody></table>';
                            $('.modal-body').append(table_end);
                        }

                        // Bring life to the graph
                        $interval2=setInterval(function () {
                            var update_rejects_graph=get_total_rejects(date_start,date_end);
                            update_rejects_graph.done(function(r){
                                var percent=[];
                                for(var i in r)
                                    percent.push(r[i].total);
                                $chart2.data.datasets[0].data=percent;
                                $chart2.update();
                            });
                        }, 1000);
                    });
                };
            // RESUMMEN RECHAZADOS

            // RESUMEN RECHAZOS PARCIALES
                var summary_partial_rejects = function(date_start,date_end,id){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{date_ini:date_start,date_end:date_end,office_group_id:41,office_id:0},
                        url: '/documents/rejected/partial'
                    });
                }

                var partial_rejects=function (date_start,date_end) {
                    var summary_partial=summary_partial_rejects(date_start,date_end);
                    summary_partial.done(function(r){
                        var total=0,
                            reason_of_rejection=[],
                            percentage=[],
                            doc_info=[];
                        for(var i in r){
                            total+=parseInt(r[i].total);
                        }
                        for(var i in r){
                            reason_of_rejection.push(r[i].description);
                            percentage.push(r[i].total);
                            doc_info.push(JSON.parse(r[i].di_info));
                        }
                        var canvas = document.getElementById("partial-rejects");
                        var ctx = canvas.getContext("2d");
                        $chart3 = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                datasets: [{
                                    // Aceptada, Rechazada(status: 4), Reparto
                                    data: percentage,
                                    backgroundColor: [
                                        "#F7464A",
                                        "#46BFBD",
                                        "#EEEE51",
                                        "#551A8B",
                                        "#49FF00",
                                        "#3B80DF",
                                        "#ACDF3B"
                                    ],
                                    doc_info:doc_info
                                }],
                                labels: reason_of_rejection
                            },options: {
                                legend: {
                                    display: false,
                                    position: 'bottom'
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            var allData = data.datasets[tooltipItem.datasetIndex].data;
                                            var tooltipLabel = data.labels[tooltipItem.index];
                                            var tooltipData = allData[tooltipItem.index];
                                            var tooltipPercentage = Math.floor((tooltipData / total) * 100);
                                            return tooltipLabel + ': '+tooltipData+' (' + tooltipPercentage + '%)';
                                        }
                                    }
                                }
                            }
                        });
                        canvas.onclick = function (evt) {
                            var activePoints = $chart3.getElementsAtEvent(evt);
                            var chartData = activePoints[0]['_chart'].config.data;
                            var idx = activePoints[0]['_index'];
                            var label = chartData.labels[idx];
                            var value = chartData.datasets[0].data[idx];
                            $('#modal').modal('toggle');
                            $('.modal-title').text(label);
                            var doc_info = chartData.datasets[0].doc_info[idx];
                            var table='<table class="table table-hover"><thead><tr><th>Documento</th><th>Descripcion</th><th>Codigo</th></tr></thead><tbody>';
                            $('.modal-body').html('');
                            $('.modal-body').append(table);
                            for(var i in doc_info){
                                var document=doc_info[i].f1,
                                    p_description=doc_info[i].f2.description,
                                    p_code=doc_info[i].f2.code,
                                    table_body='<tr><td>'+document+'</td><td>'+p_description+'</td><td>'+p_code+'</td></tr>';
                                $('.modal-body .table.table-hover tbody').append(table_body);
                            }
                            var table_end='</tbody></table>';
                            $('.modal-body').append(table_end);
                        }

                        // Bring life to the graph
                        $interval3=setInterval(function () {
                            var update_summary=summary_partial_rejects(date_start,date_end);
                            update_summary.done(function(r){
                                var percent=[];
                                for(var i in r)
                                    percent.push(r[i].total);
                                $chart3.data.datasets[0].data=percent;
                                $chart3.update();
                            });
                        }, 1000);
                    });
                }
            // RESUMMEN RECHAZOS PARCIALES

            // ENTREGAS POR CANAL DE DISTRIBUCION
                var delivery_distro_channel = function(date_start,date_end,id){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{date_ini:date_start,date_end:date_end,office_group_id:41,office_id:0},
                        url: '/deliveries/channel'
                    });
                }

                var distro_channel=function (date_start,date_end) {
                    var summary_partial=delivery_distro_channel(date_start,date_end);
                    summary_partial.done(function(r){
                        var total=0,
                            channel=[],
                            percentage=[],
                            status=[];
                        for(var i in r){
                            total+=parseInt(r[i].total);
                        }
                        for(var i in r){
                            channel.push(r[i].channel);
                            percentage.push(r[i].total);
                            status.push(r[i].status);
                        }
                        var canvas = document.getElementById("delivery_channel");
                        var ctx = canvas.getContext("2d");
                        $chart4 = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                datasets: [{
                                    // Aceptada, Rechazada(status: 4), Reparto
                                    data: percentage,
                                    backgroundColor: [
                                        "#F7464A",
                                        "#46BFBD",
                                        "#EEEE51",
                                        "#551A8B",
                                        "#49FF00",
                                        "#3B80DF",
                                        "#ACDF3B"
                                    ]
                                }],
                                labels: channel
                            },options: {
                                legend: {
                                    display: false,
                                    position: 'bottom'
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            var allData = data.datasets[tooltipItem.datasetIndex].data;
                                            var tooltipLabel = data.labels[tooltipItem.index];
                                            var tooltipData = allData[tooltipItem.index];
                                            var tooltipPercentage = Math.floor((tooltipData / total) * 100);
                                            return tooltipLabel + ': '+tooltipData+' (' + tooltipPercentage + '%)';
                                        }
                                    }
                                }
                            }
                        });
                    /*    canvas.onclick = function (evt) {
                            var activePoints = $chart4.getElementsAtEvent(evt);
                            var chartData = activePoints[0]['_chart'].config.data;
                            var idx = activePoints[0]['_index'];
                            var label = chartData.labels[idx];
                            var value = chartData.datasets[0].data[idx];
                            $('#modal').modal('toggle');
                            $('.modal-title').text(label);
                            var table='<table class="table table-hover"><thead><tr><th>Documento</th><th>Descripcion</th><th>Codigo</th></tr></thead><tbody>';
                            $('.modal-body').html('');
                            $('.modal-body').append(table);
                            for(var i in doc_info){
                                var document=doc_info[i].f1,
                                    p_description=doc_info[i].f2.description,
                                    p_code=doc_info[i].f2.code,
                                    table_body='<tr><td>'+document+'</td><td>'+p_description+'</td><td>'+p_code+'</td></tr>';
                                $('.modal-body .table.table-hover tbody').append(table_body);
                            }
                            var table_end='</tbody></table>';
                            $('.modal-body').append(table_end);
                        }*/
                        // Bring life to the graph
                        $interval4=setInterval(function () {
                            var update_channel_graph=delivery_distro_channel(date_start,date_end);
                            update_channel_graph.done(function(r){
                                var percent=[];
                                for(var i in r)
                                    percent.push(r[i].total);
                                $chart4.data.datasets[0].data=percent;
                                $chart4.update();
                            });
                        }, 1000);
                    });
                  }
            // ENTREGAS POR CANAL DE DISTRIBUCION

            // ON TIME HISTORICO
                var get_history = function(date_start,date_end,id){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{date_ini:date_start,date_end:date_end,office_group_id:41,office_id:0},
                        url: '/report/historic_tendency'
                    });
                }

                var on_time_history=function (date_start,date_end) {
                    var on_time=get_history(date_start,date_end);
                    on_time.done(function(r){
                        var total_docs=[],
                            kpi_docs=[],
                            created_at=[];
                        for(var i in r){
                            total_docs.push(r[i].total_docs);
                            kpi_docs.push(r[i].kpi_docs);
                            created_at.push(r[i].created);
                        }
                        var canvas = document.getElementById("on_time");
                        var ctx = canvas.getContext("2d");
                        $chart5 = new Chart(ctx, {
                            type: 'line',
                            data: {
                                datasets: [{
                                    data: kpi_docs,
                                    backgroundColor: [
                                        "#F7464A",
                                        "#46BFBD",
                                        "#EEEE51",
                                        "#551A8B",
                                        "#49FF00",
                                        "#3B80DF",
                                        "#ACDF3B"
                                    ]
                                }],
                                labels: created_at
                            },options: {
                                legend: {
                                    display: false,
                                    position: 'bottom'
                                },
                                tooltips: {
                                    callbacks: {
                                        label: function(tooltipItem, data) {
                                            var allData = data.datasets[tooltipItem.datasetIndex].data;
                                            var tooltipData = allData[tooltipItem.index];
                                            return 'Entregas: '+tooltipData;
                                        }
                                    }
                                }
                            }
                        });
                    /*    canvas.onclick = function (evt) {
                            var activePoints = $chart5.getElementsAtEvent(evt);
                            var chartData = activePoints[0]['_chart'].config.data;
                            var idx = activePoints[0]['_index'];
                            var label = chartData.labels[idx];
                            var value = chartData.datasets[0].data[idx];
                            $('#modal').modal('toggle');
                            $('.modal-title').text(label);
                            var table='<table class="table table-hover"><thead><tr><th>Documento</th><th>Descripcion</th><th>Codigo</th></tr></thead><tbody>';
                            $('.modal-body').html('');
                            $('.modal-body').append(table);
                            for(var i in doc_info){
                                var document=doc_info[i].f1,
                                    p_description=doc_info[i].f2.description,
                                    p_code=doc_info[i].f2.code,
                                    table_body='<tr><td>'+document+'</td><td>'+p_description+'</td><td>'+p_code+'</td></tr>';
                                $('.modal-body .table.table-hover tbody').append(table_body);
                            }
                            var table_end='</tbody></table>';
                            $('.modal-body').append(table_end);
                        }*/
                        // Bring life to the graph
                        $interval5=setInterval(function () {
                            var update_history_graph=get_history(date_start,date_end);
                            update_history_graph.done(function(r){
                                var kpi_docs=[],
                                    created_at=[];
                                for(var i in r){
                                    kpi_docs.push(r[i].kpi_docs);
                                    created_at.push(r[i].created);
                                }
                                $chart5.data.datasets[0].data=kpi_docs;
                                $chart5.data.labels=created_at;
                                $chart5.update();
                            });
                        }, 1000);
                    });
                  }
            // ON TIME HISTORICO


            //Gauge Chart - On Time
                var kpi_indicator = function(date_start,date_end,id){
                    return $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        //processData: false,
                        //contentType:  false,
                        data:{date_ini:date_start,date_end:date_end,office_group_id:41,office_id:0},
                        url: '/report/kpi_indicator'
                    });
                }
                var gauge=function(date_start,date_end){
                    var gauge_ontime=kpi_indicator(date_start,date_end);
                    gauge_ontime.done(function(r){
                        var gaugeOptions = {
                            chart: {
                                type: 'solidgauge'
                            },
                            title: null,
                            pane: {
                                center: ['50%', '85%'],
                                size: '140%',
                                startAngle: -90,
                                endAngle: 90,
                                background: {
                                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                                    innerRadius: '60%',
                                    outerRadius: '100%',
                                    shape: 'arc'
                                }
                            },
                            tooltip: {
                                enabled: false
                            },
                            // the value axis
                            yAxis: {
                                stops: [
                                    [0.1, '#55BF3B'], // green
                                    [0.5, '#DDDF0D'], // yellow
                                    [0.9, '#DF5353'] // red
                                ],
                                lineWidth: 0,
                                minorTickInterval: null,
                                tickAmount: 2,
                                title: {
                                    y: -70
                                },
                                labels: {
                                    y: 16
                                }
                            },
                            plotOptions: {
                                solidgauge: {
                                    dataLabels: {
                                        y: 5,
                                        borderWidth: 0,
                                        useHTML: true
                                    }
                                }
                            }
                        };
                        // The speed gauge
                        var chartSpeed = Highcharts.chart('container-speed', Highcharts.merge(gaugeOptions, {
                            yAxis: {
                                min: 0,
                                max: 100,
                                title: {
                                    text: ''
                                }
                            },

                            credits: {
                                enabled: false
                            },
                            series: [{
                                name: 'Speed',
                                data: [r.kpi_docs],
                                dataLabels: {
                                    format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                                        ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                                           '<span style="font-size:12px;color:silver">KPI</span></div>'
                                },
                                tooltip: {
                                    valueSuffix: ' KPI'
                                }
                            }]

                        }));
                        // Bring life to the dials
                        $interval6=setInterval(function () {
                            var newKpiVal=kpi_indicator(date_start,date_end);
                            newKpiVal.done(function(r){
                                var point,
                                    newVal;
                                if (chartSpeed) {
                                    point = chartSpeed.series[0].points[0];
                                    newVal = r.kpi_docs;
                                    point.update(newVal);
                                }
                            });
                        }, 1000);
                    });
                }
            //Gauge Chart - On Time -
            $(document).ready(function () {
                gauge();
                var today = new Date(),
                    date_end = date_start= today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                documents(date_start,date_end);
                total_rejects(date_start,date_end);
                partial_rejects(date_start,date_end);
                distro_channel(date_start,date_end);
                on_time_history(date_start,date_end);

                $(".calendar-input").datepicker({
                    language: "es",
                    format: "yyyy-mm-dd",
                    weekStart: 1,
                    autoclose: true,
                    //startDate: "2017-05-02",
                    endDate: date_end,
                    todayHighlight: true,
                    orientation: "auto right"
                });
            });
        </script>
    @endsection
@endif
