@extends('layouts.app')
    @if(Auth::check())
        @section('content')
        <div class="navbar-company">
            <nav class="navbar" role="navigation">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                        <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                            <div class="navbar-form navbar-right" role="export">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </div>
                                        <input type="text" class="form-control input-sm calendar-input" id="start" value="" placeholder="yyyy-mm-dd" size="10">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-minus"></span>
                                        </div>
                                        <input type="text" class="form-control input-sm calendar-input" id="end" value="" placeholder="yyyy-mm-dd" size="10">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button id="seach_button" class="btn btn-success"><span class="glyphicon glyphicon-send"></span></button>
                                    <div class="input-group">
                                        <a href="#" class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Exportar a CSV" id="csv" download><i class="fa fa-file-o" aria-hidden="true"></i></a>
                                        <a href="#" class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Exportar a Excel" id="xls" download><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                                        <a href="#" class="input-group-addon" data-toggle="tooltip" data-placement="bottom" title="Exportar a PDF" id="pdf" download><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <table id="main-table" class="table  table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Vehiculo</th>
                                <th>Documentos</th>
                                <th>Reparto</th>
                                <th>Aceptados</th>
                                <th>Redespacho</th>
                                <th>Rechazado</th>
                                <th>Rechazado <br>parcialmente</th>
                                <th>No entregado</th>
                            </tr>
                        </thead>
                        <tbody id="table-tbody">
                        </tbody>
                    </table>
                    <span id="error"></span>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.0/js/bootstrap-datepicker.min.js"></script>
        <script>
            var get_performance=function(date_ini, date_end){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    contentType:  false,
                    data:{date_ini:date_ini,date_end:date_end},
                    url: '/reports/get/performance-plate'
                }); 
            }
            
            $(document).on('click','#seach_button',function(){
                var date_start=$('#start').val(),
                    date_end=$('#end').val();
                var plate_performance=get_performance(date_start,date_end);
                plate_performance.done(function(response){
                    $('#table-tbody').html('');
                    $('#error').html('');
                    var html='';
                    if(Object.keys(response).length<1){
                        html+='<tr><td><h2 class="error-report"><i class="ion-close-circled"></i> No se encontraron registros.</h2></td></tr>';
                        $('#error').append(html);
                    }else{
                        $('#csv').attr('href','/reports/get/performance-plate?date_ini='+date_start+'&date_end='+date_end+'&export=csv');
                        $('#xls').attr('href','/reports/get/performance-plate?date_ini='+date_start+'&date_end='+date_end+'&export=xls');
                        $('#pdf').attr('href','/reports/get/performance-plate?date_ini='+date_start+'&date_end='+date_end+'&export=pdf');
                        for(var i in response){
                            html+='<tr>';
                            html+="<td><span class='badge'> "+(parseInt(i)+1)+" </span></td>";
                            html+="<td><span> "+response[i]['plate']+" </span></td>";
                            html+="<td><span> "+response[i]['documents']+" </span></td>";
                            html+="<td><span> "+response[i]['reparto']+" </span></td>";
                            html+="<td><span> "+response[i]['f_aceptada']+" </span></td>";
                            html+="<td><span> "+response[i]['redespacho']+" </span></td>";
                            html+="<td><span> "+response[i]['f_rechazada']+" </span></td>";
                            html+="<td><span> "+response[i]['r_parcial']+" </span></td>";
                            html+="<td><span> "+response[i]['no_entregado']+" </span></td>";
                            html+='</tr>';
                        }
                        $('#table-tbody').append(html);
                    }
                });
            });
            
            $(document).ready(function(){
                var today = new Date(),
                    date_end = date_start= today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                $("#start").val(date_start);
                $("#end").val(date_end);
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
                
                var plate_performance=get_performance(date_start,date_end);
                plate_performance.done(function(response){
                    var html='';
                    if(Object.keys(response).length<1){
                        html+='<tr><td><h2 class="error-report"><i class="ion-close-circled"></i> No se encontraron registros.</h2></td></tr>';
                        $('#error').append(html);  
                    }else{
                        $('#csv').attr('href','/reports/get/performance-driver?date_ini='+date_start+'&date_end='+date_end+'&export=csv');
                        $('#xls').attr('href','/reports/get/performance-driver?date_ini='+date_start+'&date_end='+date_end+'&export=xls');
                        $('#pdf').attr('href','/reports/get/performance-driver?date_ini='+date_start+'&date_end='+date_end+'&export=pdf');
                        for(var i in response){
                            html+='<tr>';
                            html+="<td><span class='badge'> "+(parseInt(i)+1)+" </span></td>";
                            html+="<td><span> "+response[i]['plate']+" </span></td>";
                            html+="<td><span> "+response[i]['documents']+" </span></td>";
                            html+="<td><span> "+response[i]['reparto']+" </span></td>";
                            html+="<td><span> "+response[i]['f_aceptada']+" </span></td>";
                            html+="<td><span> "+response[i]['redespacho']+" </span></td>";
                            html+="<td><span> "+response[i]['f_rechazada']+" </span></td>";
                            html+="<td><span> "+response[i]['r_parcial']+" </span></td>";
                            html+="<td><span> "+response[i]['no_entregado']+" </span></td>";
                            html+='</tr>';
                        }
                        $('#table-tbody').append(html);
                    }
                });
            });
        </script>
    @endsection
@endif