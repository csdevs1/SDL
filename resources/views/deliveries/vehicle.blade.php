@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <input type="hidden" id="plate-val" value="<?php echo $plate; ?>">
    <input type="hidden" id="delivery-date" value="<?php echo $delivery_date; ?>">
    <div class="path" id="path">
        <!--<img src="https://s-media-cache-ak0.pinimg.com/originals/b2/73/e1/b273e1e588139eee13dfeb889d1a40e6.png" class="sun">-->
        <div class="street">
            <img src="https://maxcdn.icons8.com/Share/icon/Transport//truck1600.png" class="truck-icon">
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="1%">#</th>
                <th>Ruta</th>
                <th>Pedido</th>
                <th>Documento</th>
                <th width="5%">Estado</th>
                <th width="5%">Vol</th>
                <th width="5%">Bultos</th>
                <th width="5%">Items</th>
                <th width="5%">Orden</th>
                <th width="30%">Cliente</th>
                <th>Hora pactada</th>
                <th>Hora est. llegada</th>
                <th width="5%">Hora entrega</th>
                <th width="5%">Pago</th>
                <th>
                    <span class="label label-warning" title="Pallets A Retenidos">
                        <span class="glyphicon glyphicon-menu-hamburger" ></span> A
                    </span>
                </th>
                <th>
                    <span class="label label-warning" title="Pallets B Retenidos">
                        <span class="glyphicon glyphicon-menu-hamburger" ></span> B
                    </span>
                </th>
                <th width="5%"><span class="glyphicon glyphicon-paperclip" title="adjuntos"></span></th>
                <th width="5%"><span class="glyphicon glyphicon-pencil" title="Firma"></span></th>
                <th width="5%"><span class="glyphicon glyphicon-map-marker" title="ubicaci&oacute;n"></span></th>
                <th width="5%"><span class="glyphicon glyphicon-repeat" title="resetear estado"></span></th>
            </tr>
        </thead>
        <tbody>
            <?php $count=1;
                foreach($result as $key=>$val){
                    $dp = array(100,0,0);
                    $bp = array(100,0,0);
                    $redispatch = false;
                    $time = "--:--";
                    $buttons = false;
                    switch($val['status']['status']){
                        case 4 : $css = "label-orange"; break;
                        case 3 : $css = "label-warning"; break;
                        case 2 : $css = "label-primary"; break;
                        case 1 : $css = "label-success"; break;
                        case 0 : $css = "label-danger"; break;
                        default: $css = "default";
                    }
                    switch ($val["payment"]) {
                        case 4: $payment = "Efectivo"; break;
                        case 3: $payment = "Contra entrega"; break;
                        case 2: $payment = "Factura"; break;
                        case 1: $payment = "Contra entrega cheque"; break;
                        default: $payment = "Otro";break;
                    }

                    if ($val["status"]["status"]) {
                        $dp[0] = 0;
                        $dp[1] = intval(($val["dispatch_bulk"] - $val["rejected_bulk"])*100/$val["dispatch_bulk"]);
                        $dp[2] = 100 - $dp[1];
                        $bp[0] = 0;
                        $bp[1] = intval(($val["dispatch_bulk"] - $val["rejected_bulk"])*100/$val["dispatch_bulk"]);
                        $time = explode(" ",$val["modified"]);
                        $time = date("H:i",strtotime($time[1]));
                        $buttons = true;
                    }

                    if ($val["status"]["status"] == 2) {
                        $redispatch = true;
                        $dp = array(0,0,0);
                        $bp = array(0,0,0);
                    }

                    if ( $val["attachment"]!="" ) {
                        $val["cross_docking_label"] = "<button type='button' class='btn btn-primary btn-sm btn-block btn-attachment' value='{$val['attachment']}' onclick='show_attachment(".$val['document'].")' data-toggle='modal' data-target='#details'><span class='glyphicon glyphicon-paperclip' title='Fotos'></span></button>";
                    } else {
                        $val["cross_docking_label"] = '';
                    }
                    if ( $val["signature"]!="" ) {
                        $val["cross_docking_label_2"] = "<button type='button' class='btn btn-primary btn-sm btn-block btn-signature' value='{$val['signature']}' onclick='show_signature(".$val['document'].")' data-toggle='modal' data-target='#details'><span class='glyphicon glyphicon-pencil' title='Firma'></span></button>";
                    } else {
                        $val["cross_docking_label_2"] = '';
                    }

                    $containera_ret = is_null($val['containera_ret']) ? 0 : $val['containera_ret'];
                    $containerb_ret = is_null($val['containerb_ret']) ? 0 : $val['containerb_ret'];
            ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><?php echo $val["route_sheet"]; ?></td>
                <td>
                    <?php echo $val["order_number"]; ?>
                </td>
                <td>
                    <a onclick='show_list(<?php echo '"'.$val["document"].'","'.$val["delivery_date"].'"'; ?>)' data-toggle="modal" data-target="#details" class="btn btn-primary"><?php echo $val["document"]; ?></a>
                </td>
                <td><span class="label <?php echo $css; ?>"><?php echo $val['status']['label']; ?></span></td>
                <!--<td><span class="badge"><?php echo $val["dispatch_bulk"];?></span></td>-->
                <td><span class="badge"><?php echo $val["dispatch_volume"];?></span></td>
                <td>
                    <span class="badge"><?php echo $val["dispatch_bulk"];?></span>
                    <div class="progress progress-dashboard">
                        <?php if ($bp[0]) { ?><div class="progress-bar progress-bar-danger" style="width: <?php echo $bp[0]; ?>%" title="bultos en reparto <?php echo $bp[0]; ?>%"><?php echo $bp[0]; ?>%</div><?php } ?>
                        <?php if ($bp[1]) { ?><div class="progress-bar progress-bar-success" style="width: <?php echo $bp[1]; ?>%" title="bultos entregados: <?php echo $bp[1]; ?>%"><?php echo $bp[1]; ?>%</div><?php } ?>
                        <?php if ($bp[2]) { ?><div class="progress-bar progress-bar-orange" style="width: <?php echo $bp[2]; ?>%" title="bultos rechazados: <?php echo $bp[2]; ?>%"><?php echo $bp[2]; ?>%</div><?php } ?>
                        <?php if ($redispatch) { ?><div class="progress-bar" style="width: 100%" title="bultos redespacho: 100%">100%</div><?php } ?>
                    </div>
                </td>
                <td>
					<span class="badge"><?php echo $val["dispatch_quantity"];?></span>
					<div class="progress progress-dashboard">
						<?php if ($dp[0]) { ?><div class="progress-bar progress-bar-danger" style="width: <?php echo $dp[0]; ?>%" title="dp en reparto <?php echo $dp[0]; ?>%"><?php echo $dp[0]; ?>%</div><?php } ?>
						<?php if ($dp[1]) { ?><div class="progress-bar progress-bar-success" style="width: <?php echo $dp[1]; ?>%" title="dp entregados: <?php echo $dp[1]; ?>%"><?php echo $dp[1]; ?>%</div><?php } ?>
						<?php if ($dp[2]) { ?><div class="progress-bar progress-bar-orange" style="width: <?php echo $dp[2]; ?>%" title="dp rechazados: <?php echo $dp[2]; ?>%"><?php echo $dp[2]; ?>%</div><?php } ?>
						<?php if ($redispatch) { ?><div class="progress-bar" style="width: 100%" title="dp redespacho: 100%">100%</div><?php } ?>
					</div>
				</td>

                <td>
                <?php
                	if($dp[1]) $content="<i class='glyphicon glyphicon-ok-circle custom-style'></i>";
                	elseif($dp[0]) $content= '<span class="badge">'.$val["order"].'</span>';
                	else $content= "<i class='glyphicon glyphicon-ok-circle custom-style'></i>"; //BORRAR LUEGO, AJUSTAR CON LOS STATUS
                ?>
                <?php echo $content; ?>
                </td>
                <td><small><b><?php echo $val['client']['name']; ?></b><br><i><?php echo $val['client']['address']; ?></i></small></td>
                <td><span class="badge">N/D</span></td>
                <td><span class="badge"><?php echo $val['eta']; ?></span></td>
                <td><span class="badge"><?php echo $time; ?></span></td>
                <td><span class="label label-payment" title="<?php echo "codigo: {$val["payment"]}"; ?>"><?php echo $payment; ?></span></td>
                <td><?= $containera_ret ?></td>
				<td><?= $containerb_ret ?></td>
				<td><?php echo $val["cross_docking_label"]; ?></td>
				<td><?php echo $val["cross_docking_label_2"]; ?></td>
                <td><?php if ($buttons) { ?><button type="button" class="btn btn-primary btn-block btn-map" value="<?php echo "{$val["document"]}";?>" data-location="<?php echo $val["location"]; ?>"<?php if ($val["location"] == "-1,-1") echo 'disabled="disabled"'; ?> data-toggle='modal' data-target='#details'><span class="glyphicon glyphicon-phone" title="ubicacion"></span></button><?php } else {echo "&nbsp;";} ?></td>
                <td><?php if ($buttons) { ?><button type="button" class="btn btn-warning btn-block btn-reset" value="<?php echo $val["id"];?>"><span class="glyphicon glyphicon-repeat"></span></button><?php } else {echo "&nbsp;";} ?></td>
            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>

    <div class="modal fade" id="details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="document-description">
            </div>
        </div>
    </div>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=geometry,places&ext=.js&key=AIzaSyBzRvHQoE265Wfz6gRRrzfpfKxBuj6_dcg"></script>
    <script type="text/javascript">
        var get_list=function(id_document,delivery_date){
            return $.ajax({
                type: 'GET',
                dataType: 'json',
                //processData: false,
                //contentType:  false,
                data:{id_document:id_document,delivery_date:delivery_date},
                url: '/get/document/'
            });
        }

        var show_list=function(id_document,delivery_date){
            var list=get_list(id_document,delivery_date);
            $('#document-description').html('');
            list.done(function(data){
                console.log(data);
                if(Object.keys(data['item_detail']).length >0){
                   var data1=data['item_detail'],
                   html='<div class="modal-header"><h3 class="modal-title">'+id_document+'</h3><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body document-modal"><table class="table table-condensed table-hover table-striped table-cloned"><thead><tr><th width="1%">#</th><th>C&oacute;digo</th><th>Descripcion</th><th>BU</th><th>Peso</th><th>Volumen</th><th>Estado</th></tr></thead><tbody>',
                   n=1;
                   for(var i in data1){
                        var status={},
                            dispatch_quantity=[];

                        switch(data1[i]['status']['status']) {
                                case 4 :
                                    status["label"] = data1[i]["status"]["description"];
                                    status["class"] = data["document_status"] == 3 ? "warning":"orange";
                                    break;
                                case 3 : status["class"] = "warning";	break;
                                case 2 : status["class"] = "primary";	break;
                                case 1 : status["class"] = "success";	break;
                                case 0 : status["class"] = "danger";break;
                                default: status["class"] = "default";
                        }

                        if (data1[i]["status"]["status"]) {
                            dispatch_quantity[0] = 0;
                            dispatch_quantity[1] = parseInt((data1[i]['dispatch_quantity'] - data1[i]['rejected_quantity'])*100/data1[i]['dispatch_quantity']);
                            dispatch_quantity[2] = 100 - dispatch_quantity[1];
                            var calculo1=parseInt((data1[i]["dispatch_bulk"] - data1[i]["rejected_bulk"])*100/data1[i]["dispatch_bulk"]),
                                calculo2=100 - calculo1;
                            var dispatch_bulk=[0,calculo1, calculo2];
                        } else {
                            dispatch_quantity = [100,0,0];
                            var dispatch_bulk = [100,0,0];
                        }
                    html+='<tr><td>'+n+'</td><td>'+data1[i]['code']+'</td><td>'+data1[i]['description']+'</td><td><span class="badge">'+data1[i]['dispatch_quantity']+'</span><div class="progress progress-dashboard"><div class="progress-bar progress-bar-danger" style="width: '+dispatch_quantity[0]+'%" title="dp en reparto: '+dispatch_quantity[0]+'%">'+dispatch_quantity[0]+'%</div><div class="progress-bar progress-bar-success" style="width: '+dispatch_quantity[1]+'%" title="dp entregados: '+dispatch_quantity[1]+'%">'+dispatch_quantity[1]+'%</div><div class="progress-bar progress-bar-orange" style="width: '+dispatch_quantity[2]+'%" title="dp rechazados: '+dispatch_quantity[2]+'%">'+dispatch_quantity[2]+'%</div></div></td><td><span class="badge">'+data1[i]['dispatch_bulk']+'</span><div class="progress progress-dashboard"><div class="progress-bar progress-bar-danger" style="width: '+dispatch_bulk[0]+'%" title="peso en reparto: '+dispatch_bulk[0]+'%">'+dispatch_bulk[0]+'%</div><div class="progress-bar progress-bar-success" style="width: '+dispatch_bulk[1]+'%" title="peso entregados: '+dispatch_bulk[1]+'%">'+dispatch_bulk[1]+'%</div><div class="progress-bar progress-bar-orange" style="width: '+dispatch_bulk[2]+'%" title="peso rechazados: '+dispatch_bulk[2]+'%">'+dispatch_bulk[2]+'%</div></div></td><td><span class="badge">'+data1[i]['dispatch_volume']+'</span></td><td><span class="label label-success">'+data1[i]['status']['label']+'</span></td></tr>';
                    n++;
                  }
                    html+='</tbody></table></div>';

                    $('#document-description').append(html);
                }
            });
        }
        
        // For Attachments
            var get_attachment=function(field,id_document,type){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    //contentType:  false,
                    data:{field:'id_document',value:id_document},
                    url: '/document/'+type
                });
            }

            var show_attachment=function(id_document){
                var list=get_attachment('id_document',id_document,'attachment');
                $('#document-description').html('');
                list.done(function(data){
                    var attachments={};
                    if(data['name1'] && data['path1']) attachments[data['name1']]=data['path1'];
                    if(data['name2'] && data['path2']) attachments[data['name2']]=data['path2'];
                    if(data['name3'] && data['path3']) attachments[data['name3']]=data['path3'];
                    if(data['name4'] && data['path4']) attachments[data['name4']]=data['path4'];
                    if(Object.keys(attachments).length>=1){
                        var col_n=12 / Object.keys(attachments).length,
                            style=col_n==12?'margin:auto;height:450px':'',
                            html='<div class="modal-header"><h3 class="modal-title">Documento: '+id_document+'</h3><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body document-modal"><div class="row">';
                        for(var i in attachments){
                            html+="<div class='col-md-"+col_n+"'><img class='img-responsive' src='"+attachments[i]+"' style='"+style+"'></div>";
                        }
                        html+='</div></div>';
                        $('#document-description').append(html);
                    }
                });
            }
        // For Attachments

        // For Signatures
            var show_signature=function(id_document){
                $('#document-description').html('');
                var list=get_attachment('id_document',id_document,'signature');
                $('#document-description').html('');
                list.done(function(data){
                    var attachments={};
                    if(data['name5'] && data['path5']) attachments[data['name5']]=data['path5'];
                    console.log(data);
                    if(Object.keys(attachments).length>=1){
                        var col_n=12 / Object.keys(attachments).length,
                            html='<div class="modal-header"><h3 class="modal-title">Documento: '+id_document+'</h3><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><div class="modal-body document-modal"><div class="row">';
                        for(var i in attachments){
                            html+="<div class='col-md-"+col_n+"'><img class='img-responsive' src='"+attachments[i]+"'></div>";
                        }
                        html+='</div></div>';
                        $('#document-description').append(html);
                    }
                });
            }
        // For Attachments

        /* ANIMATION (DYNAMIC REMAINS TO BE DONE) */
        // 1: delivered
        // 2: on route
        // 3: denied
        // 4: remaining
        var get_vehicles_info=function(){
            var plate=$('#plate-val').val(),
                delivery_date=$('#delivery-date').val();
            return $.ajax({
                type: 'GET',
                dataType: 'json',
                url: '/deliveries/vehicle/list/vehicle/'+plate+'/'+delivery_date
            });
        }
        
        var drawAnimation=function(addresses,total_distance){
            var truck=0;
            for(var i in addresses){
                var status=addresses[i].status.status;
                if(status==1 || status==3){
                    var dist=parseInt(addresses[i].distance)/1000;
                    truck+=dist;
                }
            }
            for(var i in addresses){
                var distance=parseInt(addresses[i].distance)/1000,
                    status=addresses[i].status.status,
                    cssClass='',
                    img='',
                    percentage=(distance*100)/total_distance;
                $('.street').css('width',(total_distance*2)+'%');
                $('.truck-icon').css('margin-left',truck+'%');
                switch(status){
                    case 3:
                        cssClass='partial-reject';
                        img='http://www.clker.com/cliparts/i/O/S/S/t/5/yellow-house-md.png';
                        //img='https://cdn4.iconfinder.com/data/icons/home3/102/Untitled-25-512.png';
                        break;
                    case 1:
                        cssClass='house-delivered';
                        img='https://cdn4.iconfinder.com/data/icons/real-estate-2-3/512/check-512.png';
                        break;
                    case 4:
                        cssClass='not-delivered';
                        img='https://cdn4.iconfinder.com/data/icons/real-estate-2-3/512/exclamation-512.png';
                        break;
                    default:
                        cssClass='remaining-point';
                        img='http://micasaenpuebla.com.mx/content/970944/iconos/Logo_Casa.png';
                        break;
                }
                if((parseInt(i)+1)==Object.keys(addresses).length)percentage=0;
                var html='<img src="'+img+'" style="margin-right:'+percentage+'%;" class="'+cssClass+' icon" data-placement="top" data-toggle="popover" title="'+addresses[i].client.name+'" data-content="<b>Direccion:</b> '+addresses[i].client.address+'<br><b>Hora Estimada de Llegada:</b> '+addresses[i].eta+'">';
                $('.street').append(html);
            }
        }
        $( window).on('load', function() {
            $('.street').css('display','flex');
        });
        var refreshTruck=function(end,total_distance,init=0){
            var truck=init
            if(!isNaN(end) && end > 0){
                var interval=setInterval(function () {
                    $('.truck-icon').css('margin-left',truck+'%');
                    $('.truck-icon').css('bottom','-2px');
                    $('.truck-icon').delay(800).queue(function (next) {
                        $(this).css('bottom','-4px');
                        next();
                    });
                    truck+=0.1;
                    truck=Math.round(truck*100)/100;
                    if(truck>=end){
                        clearInterval(interval);
                        var vehicles_info=get_vehicles_info();
                        vehicles_info.done(function(r){
                            var end2=0;
                            total_distance=0;
                            for(var i in r){
                                total_distance+=parseInt(r[i].distance)/1000;
                                if(r[i]['status']['status']!=0)
                                    end2+=parseInt(r[i]['distance'])/1000;
                            }
                            end2=(end2*100)/total_distance;
                            if(init!=end2)
                                init=end;
                            refreshTruck(end2, total_distance,init);
                        });
                    }
                }, 100);
            }
        }

        $(document).ready(function(){
            var vehicles_info=get_vehicles_info();
            vehicles_info.done(function(r){
                var end=0;
                $(function () {
                    $('[data-toggle="popover"]').popover({ html : true, container: 'body'});
                });
                total_distance=0;
                for(var i in r){
                    total_distance+=parseInt(r[i].distance)/1000;
                    if(r[i]['status']['status']!=0)
                        end+=parseInt(r[i]['distance'])/1000;
                }
                end=(end*100)/total_distance;
                drawAnimation(r,total_distance);
                refreshTruck(end,total_distance);
            });
        });
        /* ANIMATION (DYNAMIC REMAINS TO BE DONE) */

        //GOOGLE MAPS
        $(document).on('click','.btn-map',function(){
            var location=$(this).attr('data-location'),
                map_canvas='<div id="map" style="width:100%; height:500px"></div>',
                lt=parseFloat(location.split(',')[0]),
                lg=parseFloat(location.split(',')[1]);
            $('#document-description').html('');
            $('#document-description').append(map_canvas);

            initMap(lt,lg);
        });

        function initMap(lt,lg) {
            var mapOptions = {
                zoom: 15,
                center: {lat:lt,lng:lg}
            }
            var map = new google.maps.Map(document.getElementById("map"), mapOptions);
            var marker = new google.maps.Marker({
                position: {lat:lt,lng:lg},
                map: map,
                title:"Hello World!"
            });
            google.maps.event.addListener(map, 'idle', function() {
                var markPos = marker.getPosition(); // returns LatLng object
                map.setCenter(markPos);
                marker.setMap(map);
                google.maps.event.trigger(map, "resize");
            });
        }
        </script>
    @endsection
@endif
