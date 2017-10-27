@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <?php
        $res = new ListVehicles();
        $id_g_office=0;
        if(isset($_POST['g_id']) && !empty($_POST['g_id']))
            $id_g_office=$_POST['g_id'];    

        if(isset($_POST['id_office']) && !empty($_POST['id_office']))
            $id_office=$_POST['id_office'];
        else
            $id_office=$res->getLastId();
    
        if(isset($_POST['date']) && !empty($_POST['date']))
            $date_input=$_POST['date'];
        else
            $date_input=date('Y-m-d');
        //$date_input='2017-06-23'; // For date use date('Y-m-d') to get current records.

        $info=$res->ListComplete('full',$id_office,$date_input,$id_g_office);
        $info2=$res->ListComplete('full',$id_office,$date_input,0);
        if(isset($info) && !empty($info) && isset($info2) && !empty($info2)){
            $deault_vehicle_name=$info[0]->name;
            $vehicle_groups_default=$info[0]->vehicle_groups;
            $vehicle_groups_default2=$info2[0]->vehicle_groups;
            foreach($info as $key=>$val){
                $vehicle_groups=$info[$key]->vehicle_groups;
            }
            $vehicles_name_arr=array();
            $vehicle_groups=array();
            foreach($vehicle_groups_default as $key=>$val){
                if(!is_null($val->vehicles))
                    array_push($vehicle_groups,$val->vehicles); //Check if vehicles List is not null and store it in a variable;
            }
    //\Log::info($vehicle_groups);
    //dd($vehicle_groups);
            foreach($vehicle_groups[0] as $key=>$val){
                    $plate=$val->name;
                    array_push($vehicles_name_arr,$plate);
            }

            $vehicleObj=new VehiclesList();
            $default_plates=$vehicleObj->VehicleList($vehicles_name_arr,$date_input); // For date use date('Y-m-d') to get current records.
            $class = array("progress-bar-info","progress-bar-success","","progress-bar-warning","progress-bar-orange","progress-bar-danger");
            $title = array("en reparto","entregados","redespachados","devueltos parcialmente","devueltos totalmente","no entregado");
        }
    ?>
    <input type="hidden" value="<?php echo $id_office; ?>" id="id_office">
    <input type="hidden" value="<?php echo $date_input; ?>" id="date_input">
    <input type="hidden" value="<?php echo $id_g_office; ?>" id="id_g_office">
        <!-- NAV -->
        <div class="navbar-company">
            <nav class="navbar" role="navigation">
                 <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                        <div class="select-group-parent">
                            <ul class="nav navbar-nav">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="/images/offices.png"> <span class="title"><?php echo !empty($deault_vehicle_name)? $deault_vehicle_name:''; ?></span> <span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">

                                        <?php
                                        if(isset($info) && !empty($info)){
                                            foreach($info as $vehicle){
                                                $vehicle_name=$vehicle->name;
                                        ?>
                                        <li>
                                            <a href="#" class="group-parent" data-value="0" data-type="offices"><span class="title"><?php  echo $vehicle_name; ?> </span><span class="badge">1</span></a>
                                        </li>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <form class="navbar-form navbar-right " role="search" id="date-form" method="post" action="/deliveries/deliveries">
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon btn-reload" data-toggle="tooltip" data-placement="bottom" title="recarga datos de sesion"><span class="glyphicon glyphicon-refresh glyphicon-spin"></span></div>
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
                                    <input class="form-control input-sm calendar-input" type="text" name="date" id="date" value="<?php echo $date_input;?>">
                                </div>
                            </div>
                        </form>
                        <div class="perfect-scrollbar scroller perfect-scrollbar-horizontal">
                            <ul class="nav navbar-nav container perfect-scrollbar-horizontal-content group-list">
                                <?php
                                if(isset($vehicle_groups_default2) && !empty($vehicle_groups_default2)){
                                    foreach($vehicle_groups_default2 as $key=>$val){
                                        $vehicle_group_name=$val->name;
                                        $active='';
                                        $n_docs=0;
                                        if(isset($_POST['g_name']) && !empty($_POST['g_name']) && $vehicle_group_name==$_POST['g_name'])
                                            $active='active';
                                        foreach($val->vehicles as $k=>$v){
                                            if(!is_null($v->documents[0])){
                                                $n_docs++;
                                            }
                                        }
                                ?>
                                    <li class="<?php echo $active; ?>">
                                        <a href="#" onclick="$(this).next('form').submit()" class="group" data-type="vehicle_groups"><img src="/images/vehicle_groups.png"><?php echo $vehicle_group_name; ?><span style="margin-left:5px" class="badge"><?php echo $n_docs; ?></span></a>
                                        <form method="post" action="/deliveries/deliveries">
                                            <input type="hidden" id="g_id" name="g_id" value="<?php echo $val->id; ?>">
                                            <input type="hidden" id="g_name" name="g_name" value="<?php echo $vehicle_group_name; ?>">
                                            <input type="hidden" name="date" value="<?php echo $date_input; ?>">
                                        </form>
                                    </li>
                                <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-block btn-flat btn-dashboard" data-toggle="modal" data-target="#modal-dashboard" disabled="disabled">Resumen</button>
        </div>
        <!-- NAV -->
        <?php
        if(isset($default_plates) && !empty($default_plates)){
            $total_docs=count($default_plates);
            $num_container=ceil(count($default_plates)/3);
            if($num_container>=3)
                $col_bootstrap='col-sm-4';
            if($num_container==2)
                $col_bootstrap='col-sm-6';
            else
                $col_bootstrap='col-sm-12';
        ?>
        <!-- FIRST COL -->
        <div class="col-sm-4">
            <table class="table table-condensed table-hover table-striped table-cloned">
                    <thead>
                        <tr>
                            <th width="1%" rowspan="2">#</th>
                            <th width="5%" rowspan="2">Patente</th>
                            <th width="10%" colspan="2">Entregas</th>
                            <th width="10%" colspan="4">Detalle</th>
                        </tr>
                        <tr>
                            <th width="5%"><small>Primera</small></th>
                            <th width="5%"><small>Clientes</small></th>
                        </tr>
                    </thead>
                    <tbody id="table-tbody">
                        <?php
                            // \Log::info($default_plates);
                            //dd($default_plates);
                            $count=1;
                            for($key=0;$key<$num_container;$key++){
                                    $driver_name='';
                                    $driver_phone='';
                                    $client_unique = array(null,null,null,null,null);
                                    $first_delivery= "--:--:--";
                                    $last_delivery= "--:--:--";
                                    $average_delivery= "--:--:--";
                                    $delivered = 0;
                                    $dispatch_quantity = 0;
                                    $rejected_quantity = 0;
                                    $plate_number=$default_plates[$key]['plate_number'];
                                    $documents=$default_plates[$key]['documents'];
                                    $number_documents = count($documents);
                                    $doc = array(0,0,0,0,0,0);
                                    $pdocument = array(0,0,0,0,0,0);
                                    $pbulkqt = array(0,0,0,0,0,0);
                                    $bulk = array(0,0,0,0,0,0);
                                    $bulkqt = array(0,0,0,0,0,0);
                                    if(!empty($default_plates[$key]['driver']) && isset($default_plates[$key]['driver'])){
                                        $driver_name=isset($default_plates[$key]['driver']['name']) ? $default_plates[$key]['driver']['name'] : '';
                                        $driver_phone=$default_plates[$key]['driver']['phone']!=0 ? $default_plates[$key]['driver']['phone'] : '';
                                    }
                                    if(isset($default_plates[$key]['documents']) && count($default_plates[$key]['documents'])>0)
                                    {
                                        foreach($documents as $k=>$val){
                                            $document=json_decode(json_encode($val), true);
                                            $status=$document['status'];
                                            $client_unique[$status][$document["id_customer"]] = true;
                                            $dispatch_quantity += $document["dispatch_quantity"];
                                            $rejected_quantity += $document["rejected_quantity"];
                                            $doc[$status]++;
                                            $bulkqt[$status] += $document["dispatch_quantity"];
                                            $bulk[$status] += $document["dispatch_bulk"];
                                            if(isset($document['modified']) && !empty($document['modified']) && $status){
                                                $t = explode(" ",$document['modified']);
                                                $t = date("H:i:s",strtotime($t[1]));
                                                if ($t < $first_delivery || $first_delivery == "--:--:--") $first_delivery = $t;
                                                if ($t > $last_delivery) $last_delivery = $t;
                                                ++$delivered;
                                            }
                                            
                                            if (isset($document["start_time"]) && !empty($document["start_time"])){
                                                $s = explode(" ",$document["start_time"]);
                                                $start_time = date("H:i",strtotime($s[1]));
                                            }
                                            else{
                                                $start_time = "--:--";
                                            }
                                        }
                                        //\log::info($client_unique);
                                        //dd($client_unique);
                                        for ($c=0;$c<5;$c++) {
                                            $clients[$c] = count($client_unique[$c]);
                                            $bulks[$c] = isset($bulk_unique[$c]) ? count($bulk_unique[$c]) : 0;
                                            $pdocument[$c] = $doc[$c]*100/$number_documents;
                                            $pbulkqt[$c]= round(($bulkqt[$c]*100/$dispatch_quantity) * 10) / 10;
                                        }

                                        $total_clients = array_sum($clients);
                                        if($delivered>0)
                                            $average_delivery = $first_delivery == "--:--" ? "--:--" : gmdate("H:i",(intval((strtotime($last_delivery)-strtotime($first_delivery))/$delivered)));

                                        $delivery_date=date("Y-m-d"); //USE THIS VARIABLE WITH THE URL
                                ?>
                                <tr>
                                    <td><span class="label label-success"><b><?php echo $count; ?></b></span></td>
                                    <td>
                                        <a href="/deliveries/vehicle/list/vehicle/<?php echo $plate_number.'/'.$date_input; // <-- Here use the $delivery_date variable ?>" class="btn btn-primary btn-sm btn-block"><?php echo $plate_number; ?></a>
                                        <a href="#" class="map-icon" data-plate="<?php echo $plate_number; ?>" data-toggle="modal" data-target="#modal-map"><i class="ion-map"></i></a>
                                    </td>
                                    <td><span class="badge"><?php echo $start_time; ?></span><span class="badge"><?php echo $first_delivery; ?></span></td>
                                    <td><span class="badge"><?php echo $total_clients; ?></span><br><span class="badge"><?php echo $number_documents; //$dispatch_quantity; ?></span></td>
                                    <td>
                                        <!--<div class="speedometer" style="min-width: 210px; max-width: 300px; height: 200px; margin: 0 auto"></div>-->
                                        <!--<div class="progress progress-dashboard" data-toggle="tooltip" data-placement="right" title="documentos">
                                            <?php for($c=0;$c<5;$c++) { ?>
                                                <div class="progress-bar <?php echo $class[$c];?>" style="width: <?php echo $pdocument[$c]; ?>%" title="<?php echo "{$title[$c]}:".round($pdocument[$c],1); ?>%"><?php echo number_format($doc[$c], 0, ",", "."); ?></div>
                                            <?php } ?>
                                        </div>-->

                                        <?php  //for($c=0;$c<5;$c++) { ?>
                                            <div class="documents" style="min-width: 100%; max-width: 100%; height: 270px; margin: 0 auto" id="d-<?php echo $count; ?>" total-documents="<?php echo $number_documents; ?>" data-title="<?php echo "Documentos"; ?>" data-delivered="<?php echo $doc[1]; ?>" data-rejected="<?php echo $doc[4]; ?>" data-partial="<?php echo $doc[3]; ?>" plate-number="<?php echo $plate_number; ?>"></div>
                                        <?php //}  ?>

                                        <!--<div class="progress progress-dashboard" data-toggle="tooltip" data-placement="right" title="bultos">
                                            <?php  for($c=0;$c<5;$c++) { ?>
                                                <div class="bulkqt" data-bulk="<?php echo number_format($bulkqt[$c], 0, ",", "."); ?>"></div>
                                                <div class="progress-bar <?php echo $class[$c];?>" style="width: <?php echo $pbulkqt[$c]; ?>%" title="<?php echo "{$title[$c]}: {$pbulkqt[$c]}"; ?>%"><?php echo number_format($bulkqt[$c], 0, ",", "."); ?></div>
                                            <?php }  ?>
                                        </div>-->
                                    </td>
                                </tr>
                                <?php
                                    }
                                    $count++;
                                }
                        ?>
                    </tbody>
                </table>
        </div>
        
        <!-- SECOND COL -->
        <div class="col-sm-4">
            <table class="table table-condensed table-hover table-striped table-cloned">
                <thead>
                    <tr>
                        <th width="1%" rowspan="2">#</th>
                        <th width="5%" rowspan="2">Patente</th>
                        <th width="10%" colspan="2">Entregas</th>
                        <th width="10%" colspan="4">Detalle</th>
                    </tr>
                    <tr>
                        <th width="5%"><small>Primera</small></th>
                        <th width="5%"><small>Clientes</small></th>
                    </tr>
                </thead>
                <?php if(isset($info) && !empty($info) && isset($info2) && !empty($info2)){ ?>
                    <tbody id="table-tbody">
                        <?php
                            // \Log::info($default_plates);
                            //dd($default_plates);
                            $count=$num_container+1;
                            for($key=$num_container;$key<$num_container*2;$key++){
                            //for($key=$left;$key<=$total_docs-1;$key++){
                                    $driver_name='';
                                    $driver_phone='';
                                    $client_unique = array(null,null,null,null,null);
                                    $first_delivery= "--:--:--";
                                    $last_delivery= "--:--:--";
                                    $average_delivery= "--:--:--";
                                    $delivered = 0;
                                    $dispatch_quantity = 0;
                                    $rejected_quantity = 0;
                                    $plate_number=$default_plates[$key]['plate_number'];
                                    $documents=$default_plates[$key]['documents'];
                                    $number_documents = count($documents);
                                    $doc = array(0,0,0,0,0,0);
                                    $pdocument = array(0,0,0,0,0,0);
                                    $pbulkqt = array(0,0,0,0,0,0);
                                    $bulk = array(0,0,0,0,0,0);
                                    $bulkqt = array(0,0,0,0,0,0);
                                    if(!empty($default_plates[$key]['driver']) && isset($default_plates[$key]['driver'])){
                                        $driver_name=isset($default_plates[$key]['driver']['name']) ? $default_plates[$key]['driver']['name'] : '';
                                        $driver_phone=$default_plates[$key]['driver']['phone']!=0 ? $default_plates[$key]['driver']['phone'] : '';
                                    }
                                    if(isset($default_plates[$key]['documents']) && count($default_plates[$key]['documents'])>0)
                                    {
                                        foreach($documents as $k=>$val){
                                            $document=json_decode(json_encode($val), true);
                                            $status=$document['status'];
                                            $client_unique[$status][$document["id_customer"]] = true;
                                            $dispatch_quantity += $document["dispatch_quantity"];
                                            $rejected_quantity += $document["rejected_quantity"];
                                            $doc[$status]++;
                                            $bulkqt[$status] += $document["dispatch_quantity"];
                                            $bulk[$status] += $document["dispatch_bulk"];
                                            if(isset($document['modified']) && !empty($document['modified']) && $status){
                                                $t = explode(" ",$document['modified']);
                                                $t = date("H:i:s",strtotime($t[1]));
                                                if ($t < $first_delivery || $first_delivery == "--:--:--") $first_delivery = $t;
                                                if ($t > $last_delivery) $last_delivery = $t;
                                                ++$delivered;
                                            }
                                        }
                                        for ($c=0;$c<5;$c++) {
                                            $clients[$c] = count($client_unique[$c]);
                                            $bulks[$c] = isset($bulk_unique[$c]) ? count($bulk_unique[$c]) : 0;
                                            $pdocument[$c] = $doc[$c]*100/$number_documents;
                                            $pbulkqt[$c]= round(($bulkqt[$c]*100/$dispatch_quantity) * 10) / 10;
                                        }

                                        $total_clients = array_sum($clients);
                                        if($delivered>0)
                                            $average_delivery = $first_delivery == "--:--" ? "--:--" : gmdate("H:i",(intval((strtotime($last_delivery)-strtotime($first_delivery))/$delivered)));

                                        $delivery_date=date("Y-m-d"); //USE THIS VARIABLE WITH THE URL
                                ?>
                                <tr>
                                    <td><span class="label label-success"><b><?php echo $count; ?></b></span></td>
                                    <td>
                                        <a href="/deliveries/vehicle/list/vehicle/<?php echo $plate_number.'/'.$date_input; // <-- Here use the $delivery_date variable ?>" class="btn btn-primary btn-sm btn-block"><?php echo $plate_number; ?></a>
                                        <a href="#" class="map-icon" data-plate="<?php echo $plate_number; ?>" data-toggle="modal" data-target="#modal-map"><i class="ion-map"></i></a>
                                    </td>
                                    <td><span class="badge"><?php echo $first_delivery; ?></span><span class="badge"><?php echo $average_delivery; ?></span></td>
                                    <td><span class="badge"><?php echo $total_clients; ?></span><br><span class="badge"><?php echo $number_documents; ?></span></td>
                                    <td>
                                        <!--<div class="speedometer" style="min-width: 210px; max-width: 300px; height: 200px; margin: 0 auto"></div>-->
                                        <!--<div class="progress progress-dashboard" data-toggle="tooltip" data-placement="right" title="documentos">
                                            <?php for($c=0;$c<5;$c++) { ?>
                                                <div class="progress-bar <?php echo $class[$c];?>" style="width: <?php echo $pdocument[$c]; ?>%" title="<?php echo "{$title[$c]}:".round($pdocument[$c],1); ?>%"><?php echo number_format($doc[$c], 0, ",", "."); ?></div>
                                            <?php } ?>
                                        </div>-->

                                        <?php  //for($c=0;$c<5;$c++) { ?>
                                            <div class="documents" style="min-width: 100%; max-width: 100%; height: 270px; margin: 0 auto" id="d-<?php echo $count; ?>" total-documents="<?php echo $number_documents; ?>" data-title="<?php echo "Documentos"; ?>" data-delivered="<?php echo $doc[1]; ?>" data-rejected="<?php echo $doc[4]; ?>" data-partial="<?php echo $doc[3]; ?>" plate-number="<?php echo $plate_number; ?>"></div>
                                        <?php //}  ?>

                                        <!--<div class="progress progress-dashboard" data-toggle="tooltip" data-placement="right" title="bultos">
                                            <?php  for($c=0;$c<5;$c++) { ?>
                                                <div class="bulkqt" data-bulk="<?php echo number_format($bulkqt[$c], 0, ",", "."); ?>"></div>
                                                <div class="progress-bar <?php echo $class[$c];?>" style="width: <?php echo $pbulkqt[$c]; ?>%" title="<?php echo "{$title[$c]}: {$pbulkqt[$c]}"; ?>%"><?php echo number_format($bulkqt[$c], 0, ",", "."); ?></div>
                                            <?php }  ?>
                                        </div>-->
                                    </td>
                                </tr>
                                <?php
                                    }
                                    $count++;
                                }
                        ?>
                    </tbody>
                    <?php } ?>
                </table>
            </div>

        <!-- THIRD COL -->
        <div class="col-sm-4">
            <table class="table table-condensed table-hover table-striped table-cloned">
                <thead>
                    <tr>
                        <th width="1%" rowspan="2">#</th>
                        <th width="5%" rowspan="2">Patente</th>
                        <th width="10%" colspan="2">Entregas</th>
                        <th width="10%" colspan="4">Detalle</th>
                    </tr>
                    <tr>
                        <th width="5%"><small>Primera</small></th>
                        <th width="5%"><small>Clientes</small></th>
                    </tr>
                </thead>
                <?php if(isset($info) && !empty($info) && isset($info2) && !empty($info2)){ ?>
                    <tbody id="table-tbody">
                        <?php
                            // \Log::info($default_plates);
                            //dd($default_plates);
                            $count=($num_container*2)+1;
                            for($key=$num_container*2;$key<$total_docs;$key++){
                                    $driver_name='';
                                    $driver_phone='';
                                    $client_unique = array(null,null,null,null,null);
                                    $first_delivery= "--:--:--";
                                    $last_delivery= "--:--:--";
                                    $average_delivery= "--:--:--";
                                    $delivered = 0;
                                    $dispatch_quantity = 0;
                                    $rejected_quantity = 0;
                                    $plate_number=$default_plates[$key]['plate_number'];
                                    $documents=$default_plates[$key]['documents'];
                                    $number_documents = count($documents);
                                    $doc = array(0,0,0,0,0,0);
                                    $pdocument = array(0,0,0,0,0,0);
                                    $pbulkqt = array(0,0,0,0,0,0);
                                    $bulk = array(0,0,0,0,0,0);
                                    $bulkqt = array(0,0,0,0,0,0);
                                    if(!empty($default_plates[$key]['driver']) && isset($default_plates[$key]['driver'])){
                                        $driver_name=isset($default_plates[$key]['driver']['name']) ? $default_plates[$key]['driver']['name'] : '';
                                        $driver_phone=$default_plates[$key]['driver']['phone']!=0 ? $default_plates[$key]['driver']['phone'] : '';
                                    }
                                    if(isset($default_plates[$key]['documents']) && count($default_plates[$key]['documents'])>0)
                                    {
                                        foreach($documents as $k=>$val){
                                            $document=json_decode(json_encode($val), true);
                                            $status=$document['status'];
                                            $client_unique[$status][$document["id_customer"]] = true;
                                            $dispatch_quantity += $document["dispatch_quantity"];
                                            $rejected_quantity += $document["rejected_quantity"];
                                            $doc[$status]++;
                                            $bulkqt[$status] += $document["dispatch_quantity"];
                                            $bulk[$status] += $document["dispatch_bulk"];
                                            if(isset($document['modified']) && !empty($document['modified']) && $status){
                                                $t = explode(" ",$document['modified']);
                                                $t = date("H:i:s",strtotime($t[1]));
                                                if ($t < $first_delivery || $first_delivery == "--:--:--") $first_delivery = $t;
                                                if ($t > $last_delivery) $last_delivery = $t;
                                                ++$delivered;
                                            }
                                        }
                                        for ($c=0;$c<5;$c++) {
                                            $clients[$c] = count($client_unique[$c]);
                                            $bulks[$c] = isset($bulk_unique[$c]) ? count($bulk_unique[$c]) : 0;
                                            $pdocument[$c] = $doc[$c]*100/$number_documents;
                                            $pbulkqt[$c]= round(($bulkqt[$c]*100/$dispatch_quantity) * 10) / 10;
                                        }

                                        $total_clients = array_sum($clients);
                                        if($delivered>0)
                                            $average_delivery = $first_delivery == "--:--" ? "--:--" : gmdate("H:i",(intval((strtotime($last_delivery)-strtotime($first_delivery))/$delivered)));

                                        $delivery_date=date("Y-m-d"); //USE THIS VARIABLE WITH THE URL
                                ?>
                                <tr>
                                    <td><span class="label label-success"><b><?php echo $count; ?></b></span></td>
                                    <td>
                                        <a href="/deliveries/vehicle/list/vehicle/<?php echo $plate_number.'/'.$date_input; // <-- Here use the $delivery_date variable ?>" class="btn btn-primary btn-sm btn-block"><?php echo $plate_number; ?></a>
                                        <a href="#" class="map-icon" data-plate="<?php echo $plate_number; ?>" data-toggle="modal" data-target="#modal-map"><i class="ion-map"></i></a>
                                    </td>
                                    <td><span class="badge"><?php echo $first_delivery; ?></span><span class="badge"><?php echo $average_delivery; ?></span></td>
                                    <td><span class="badge"><?php echo $total_clients; ?></span><br><span class="badge"><?php echo $number_documents; ?></span></td>
                                    <td>
                                        <!--<div class="speedometer" style="min-width: 210px; max-width: 300px; height: 200px; margin: 0 auto"></div>-->
                                        <!--<div class="progress progress-dashboard" data-toggle="tooltip" data-placement="right" title="documentos">
                                            <?php for($c=0;$c<5;$c++) { ?>
                                                <div class="progress-bar <?php echo $class[$c];?>" style="width: <?php echo $pdocument[$c]; ?>%" title="<?php echo "{$title[$c]}:".round($pdocument[$c],1); ?>%"><?php echo number_format($doc[$c], 0, ",", "."); ?></div>
                                            <?php } ?>
                                        </div>-->

                                        <?php  //for($c=0;$c<5;$c++) { ?>
                                            <div class="documents" style="min-width: 100%; max-width: 100%; height: 270px; margin: 0 auto" id="d-<?php echo $count; ?>" total-documents="<?php echo $number_documents; ?>" data-title="<?php echo "Documentos"; ?>" data-delivered="<?php echo $doc[1]; ?>" data-rejected="<?php echo $doc[4]; ?>" data-partial="<?php echo $doc[3]; ?>" plate-number="<?php echo $plate_number; ?>"></div>
                                        <?php //}  ?>

                                        <!--<div class="progress progress-dashboard" data-toggle="tooltip" data-placement="right" title="bultos">
                                            <?php  for($c=0;$c<5;$c++) { ?>
                                                <div class="bulkqt" data-bulk="<?php echo number_format($bulkqt[$c], 0, ",", "."); ?>"></div>
                                                <div class="progress-bar <?php echo $class[$c];?>" style="width: <?php echo $pbulkqt[$c]; ?>%" title="<?php echo "{$title[$c]}: {$pbulkqt[$c]}"; ?>%"><?php echo number_format($bulkqt[$c], 0, ",", "."); ?></div>
                                            <?php }  ?>
                                        </div>-->
                                    </td>
                                </tr>
                                <?php
                                    }
                                    $count++;
                                }
                        ?>
                    </tbody>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>

        <div class="modal fade" id="modal-map" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <div class="modal-title" id="modal-title"></div>
                    </div>
                    <div class="modal-body">
                        <div id="map" style="width:100%; height:500px"></div>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <abbr class="modal-title plate-title">BBXW43</abbr>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped modal-table">
                            <thead>
                                <tr>
                                    <th>Ruta</th>
                                    <th>Documento</th>
                                    <th>Pedido</th>
                                    <th>Cliente</th>
                                    <th>Hora de Entrega</th>
                                </tr>
                            </thead>
                            <tbody id="docs_stat">
                                <tr>
                                    <td>12214</td>
                                    <td><span class="label label-primary">3448</span></td>
                                    <td><small><b>ALVI SUPERMERCADOS MAYORISTAS S.A.</b><br><i>EYZAGUIRRE 2909</i></small></td>
                                </tr>
                                <tr>
                                    <td>12214</td>
                                    <td><span class="label label-primary">3448</span></td>
                                    <td><small><b>ALVI SUPERMERCADOS MAYORISTAS S.A.</b><br><i>EYZAGUIRRE 2909</i></small></td>
                                </tr>
                                <tr>
                                    <td>12214</td>
                                    <td><span class="label label-primary">3448</span></td>
                                    <td><small><b>ALVI SUPERMERCADOS MAYORISTAS S.A.</b><br><i>EYZAGUIRRE 2909</i></small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
        <!---
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.exp&sensor=false&extn=.js"></script>-->
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC8hUCpEaLIK2KWQqBLoVddeFJG3wTEcM8"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.0/js/bootstrap-datepicker.min.js"></script>

        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/highcharts-more.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <!-- Boost for HighCharts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/5.0.14/js/modules/boost.js"></script>
        <script>
            $('.btn-reload').on('click',function(){
                location.reload();
            });
            
            var get_gauge_data = function(index,id_office,date_input,id_g_office){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    contentType:  false,
                    data:{index:index,id_office:id_office,date_input:date_input,id_g_office:id_g_office},
                    url: '/update/gauge'
                });
            }

            $(document).ready(function(){
                var today = new Date(),
                    date_end = date_start= today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
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
                var index_gauge=0;
                $.each($('.documents'), function() {
                    var docs=$(this).attr('total-documents'),
                        delivered=parseInt($(this).attr('data-delivered')),
                        rejected=parseInt($(this).attr('data-rejected')),
                        partial=parseInt($(this).attr('data-partial')),
                        title=$(this).attr('data-title'),
                        plate=$(this).attr('plate-number'),
                        id=$(this).attr('id'),
                        total=delivered+rejected+partial;
                    if(docs>0)
                        gauge(docs,title,total,delivered,rejected,partial,id,index_gauge,plate);
                    index_gauge++;
                });
            });

            $('#date').on('change',function(){
                $('#date-form').submit();
            });
        
            var gauge=function(val,title,total,delivered,rejected,partial,id,index_gauge,plate){
                var chart=Highcharts.chart(id, {
                    chart: {
                        width: 190,
                        type: 'gauge',
                        alignTicks: false,
                        plotBackgroundColor: null,
                        plotBackgroundImage: null,
                        plotBorderWidth: 0,
                        plotShadow: false,
                        backgroundColor:'rgba(255, 255, 255, 0)',
                        cursor: 'pointer'
                        /*
                        events: {
                            click: function (e) {
                                openModal();
                            }
                        }*/
                    },
                    boost: {
                        useGPUTranslations: true,
                        usePreAllocated: true
                    },
                    exporting: { enabled: false },
                    title: {
                        text: ''
                    },
                    pane: {
                        startAngle: -150,
                        endAngle: 150,
                        background: {
                            from: 0,
                            to: partial+rejected,
                            backgroundColor: '#f55',
                            innerRadius: '85%',
                            outerRadius: '50%',
                            shape: 'arc'
                        }
                    },
                    yAxis: [{
                        lineWidth: 0,
                        min: 0,
                        max: parseInt(val),
                        tickInterval: 1,
                        tickPosition: 'outside',
                        minorTickColor: '#FF0000',
                        tickColor: '#FF0000',
                        tickWidth: 2,
                        tickLength: 10,
                        minorTickPosition: 'outside',
                        tickLength: 15,
                        minorTickLength: 5,
                        title:{text:'Total: '+val,style:{ color:"#333" }},
                        labels: {
                            distance: 25,
                        },
                        offset: 5,
                        endOnTick: false,
                        plotOptions: {
                            solidgauge: {
                                dataLabels: {
                                    y: 5,
                                    borderWidth: 0,
                                    useHTML: true
                                }
                            }
                        },
                        plotBands: [{
                            shadow: true,
                            from: 0,
                            to: delivered,
                            color: '#21A121',
                            thickness: '15%',
                            id: 'plot-band-1',
                            events: {
                                click: function (e) {
                                    openModal(3,plate,date_input);
                                }
                            }
                        }]
                    }],                    
                    
                    series: [{
                        name: 'Gestionados',
                        data: [{
                            id: 'deliver',
                            y: parseInt(total),
                            dial: {
                                backgroundColor:'#D9972E',
                                radius: '100%',
                                baseWidth: 10,
                                baseLength: '5%',
                                baseWidth: 15,
                                rearLength: '0%',
                            }
                        }],
                        dataLabels: {
                            useHTML: true,
                            formatter: function () {
                                var total = this.y;
                                var html="";
                                if(rejected>0)
                                    html+='<span title="RT" onclick="openModal(28,\''+plate+'\')" class="rejects-icon chart-icons" style="color:rgba(255,0,0,0.9);">⦻<span class="block">'+rejected+'</span></span>';
                                if(partial>0)
                                    html+='<span title="RP" onclick="openModal(7,\''+plate+'\')" class="partial-rejects-icon chart-icons" style="color:rgba(255,100,0,0.9);">⨵<span class="block">'+partial+'</span></span>';
                                return html;
                            },
                            borderWidth: 1,
                            style: {
                                fontSize: "15px"
                            },
                            borderColor: "rgba(255,255,255, 0)",
                            backgroundColor: {
                                linearGradient: {
                                    x1: 10,
                                    y1: 10,
                                    x2: 10,
                                    y2: 10
                                },
                                stops: [
                                    [0, 'rgba(255,255,255, 0)'],
                                ]
                            }
                        },
                        tooltip: {
                            valueSuffix: ' '
                        }
                    }]
                });
                
                var id_office=$('#id_office').val(),
                    date_input=$('#date_input').val(),
                    id_g_office=$('#id_g_office').val();
                $interval=setInterval(function () {
                    var new_data=get_gauge_data(index_gauge,id_office,date_input,id_g_office);
                    new_data.done(function(data){
                        var new_value = data['n_visited_points'];//the value you wish to update and set to guage
                        var point = chart.series[0].points[0];
                        point.update(new_value); //Update Tick
                        
                        chart.yAxis[0].setExtremes(0,data['total_docs']); //Update gauge total docs
                        
                        plotBand = {color:"#21A121",from:0,id:"plot-band-"+index_gauge,shadow:true,thickness:"15%",to:data['delivered']};
                        chart.yAxis[0].update({ //Update Delivered plotband
                            plotLines:[plotBand]
                        });
                        
                        chart.yAxis[0].axisTitle.attr({
                            text: 'Total: '+data['total_docs']
                        });
                        chart.pane[0].options.background={
                            from: 0,
                            to: data['sum_rt_pt'],
                            backgroundColor: '#f55',
                            innerRadius: '85%',
                            outerRadius: '50%',
                            shape: 'arc',
                        };
                        
                         chart.update({
                             series: [{
                                 dataLabels: {
                                     formatter: function () {
                                         var html="";
                                         if(data['rejected']>0)
                                             html+='<span title="RT" onclick="openModal(28,\''+plate+'\',\''+date_input+'\')" class="rejects-icon chart-icons" style="color:rgba(255,0,0,0.9);">⦻<span class="block">'+rejected+'</span></span>';
                                         if(data['partial_reject']>0)
                                             html+='<span title="RP" onclick="openModal(7,\''+plate+'\',\''+date_input+'\')" class="partial-rejects-icon chart-icons" style="color:rgba(255,100,0,0.9);">⨵<span class="block">'+partial+'</span></span>';
                                         return html;
                                     },
                                     borderWidth: 1,
                                     style: {
                                         fontSize: "15px"
                                     },
                                     borderColor: "rgba(255,255,255, 0)",
                                     backgroundColor: {
                                         linearGradient: {
                                             x1: 10,
                                             y1: 10,
                                             x2: 10,
                                             y2: 10
                                         },
                                         stops: [
                                             [0, 'rgba(255,255,255, 0)'],
                                         ]
                                     }
                                 }
                             }]
                         });
                    });
                }, 1000);
            }
            
            var get_docs_by_status = function(status,plate,delivery_date){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    contentType:  false,
                    data:{status:status,plate:plate,delivery_date:delivery_date},
                    url: '/get/documents/status'
                });
            }
            
            var openModal=function(status,plate,delivery_date){
                $('#modal').modal('toggle');
                var get_docs=get_docs_by_status(status,plate,delivery_date);
                if($('#reject-reason'))
                    $('#reject-reason').remove();
                if(status==28 || status==7)
                    $('.modal-table thead tr').append('<th id="reject-reason">Motivo Rechazo</th>');
                get_docs.done(function(docs){
                    $('#docs_stat').html('');
                    $('.plate-title').html(plate);
                    var html='';
                    for(var i in docs){
                        html+='<tr>';
                        html+='<td>'+docs[i]['route']+'</td>';
                        html+='<td><span class="label label-primary">'+docs[i]['document']+'</span></td>';
                        html+='<td><span class="label label-success">'+docs[i]['order_number']+'</span></td>';
                        html+='<td><small><b>'+docs[i]['customer_name']+'</b><br><i>'+docs[i]['customer_address']+'</i></small></td>';
                        html+='<td>'+docs[i]['arrival_time']+'</td>';
                        if(typeof docs[i]['reject_reason']['description']!= 'undefined')
                            html+='<td>'+docs[i]['reject_reason']['description']+'</td>';
                        html+='</tr>';
                    }
                    $('#docs_stat').append(html);
                });
            }
            // Vehicle Route
            var get_route = function(plate,delivery_date){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    contentType:  false,
                    data:{plate:plate,delivery_date:delivery_date},
                    url: '/vehicle/route'
                });
            }

            $('.map-icon').on('click',function(){
                event.preventDefault();
                var plate=$(this).attr('data-plate'),
                    delivery_date=$(".calendar-input").val(),
                    route=get_route(plate,delivery_date);
                route.done(function(r){
                    var documents=r['document'],
                        route=[],
                        inRoute=[],
                        status=[],
                        customers=[],
                        eta=[],
                        wp=[];
                    $('#modal-title').html('');
                    if(r['device_location']!=null && Object.keys(r['device_location']).length>0){
                        var device=r['device_location'],
                            title='<b>Vehiculo: </b><span class="label label-default">'+plate+'</span><b> Presición GPS: </b><span class="label label-default">'+device['precision']+'</span>'+'</span><b> Bateria: </b><span class="label label-default">'+device['battery']+'%</span>'+'</span><b> Actualizado: </b><span class="label label-default">'+device['updated']+'</span>'+'</span><b> Señal: </b><span class="label label-default">'+device['signal']+'</span>'+'</span><b> Memoria externa disponible: </b><span class="label label-default">'+device['available_mb']+' MB</span>'+'</span><b> Memoria externa total: </b><span class="label label-default">'+device['total_mb']+' MB</span>';
                        $('#modal-title').html(title);
                    }
                    for(var i in documents){
                        var lat=documents[i]['lat'],
                            lng=documents[i]['lng'];
                        documents[i]['customer']['distance']=documents[i]['distance']+'km';
                        customers.push(documents[i]['customer']);
                        eta.push(documents[i]['eta']);
                        route.push({lat:lat,lng:lng});
                        status.push(documents[i]['status']);
                        if(documents[i]['status']!=1){
                            wp.push({'location':lat+','+lng,'stopover':true});
                            inRoute.push({lat:lat,lng:lng});
                        }
                    }
                    var origin=inRoute[0];
                    initMap(route,wp,origin,status,customers,eta);
                });
            });

            var map;
            var infowindow = new google.maps.InfoWindow();
            var colors = ["rgba(120, 10, 80, 1)","rgba(250, 250, 25, 1)","rgba(0, 150, 255, 1)","rgba(0, 150, 25, 1)","rgba(250, 0, 0, 1)"];
            function bindInfoWindow(marker, map, infowindow, html) {
                marker.addListener('click', function() {
                    infowindow.setContent(html);
                    infowindow.open(map, this);
                });
            }

            function genCharArray(charA, charZ) {
                var a = [], i = charA.charCodeAt(0), j = charZ.charCodeAt(0);
                for (; i <= j; ++i) {
                    a.push(String.fromCharCode(i));
                }
                return a;
            }

            function initMap(route,wp,origin,status,customers,eta) {
                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer({
                    suppressPolylines: true,
                    infoWindow: infowindow
                });
                map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 12,
                    center: {
                        lat: -33.3871787,
                        lng: -70.808392
                    }
                });
                var letter = genCharArray('A', 'Z');
                for(var i in route){
                        switch(status[i]){
                            case 0:case 2: var url='/image/letter?letter='+letter[i]+'&icon=place_box.png'; break;
                            case 3:case 4: var url='/image/letter?letter='+letter[i]+'&icon=place_truck.png'; break;
                            default: var url='/image/letter?letter='+letter[i]+'&icon=place_check.png'; break;
                        }

                        var icon = {
                            url: url, // url
                            scaledSize: new google.maps.Size(25, 35), // scaled size
                            origin: new google.maps.Point(0,0), // origin
                            anchor: new google.maps.Point(15, 30) // anchor
                        };
                        var customMarker = new google.maps.Marker({
                            position: route[i],
                            map: map,
                            title:customers[i]['name'],
                            icon: icon,
                            animation: google.maps.Animation.DROP
                        });
                        var infoContent="<h4>"+customers[i]['name']+"</h4> "+customers[i]['address']+'<br><b>Distancia: </b>'+customers[i]['distance']+'<br><br><b>Hora estimamda de llegada: </b>'+eta[i];
                        bindInfoWindow(customMarker, map, infowindow, infoContent);
                    }
                google.maps.event.addListener(map, 'idle', function() {
                    google.maps.event.trigger(map, "resize");
                });
                var waypoints=wp.slice(1, -1);
                calculateAndDisplayRoute(waypoints,origin,route[route.length-1],directionsService, directionsDisplay);
            }

            function calculateAndDisplayRoute(waypoints,origin,destination,directionsService, directionsDisplay) {
                directionsService.route({
                    origin: origin,
                    destination: destination,
                    waypoints:waypoints,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING
                }, function(response, status) {
                    if (status === google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setOptions({
                            directions: response,
                        });
                        renderDirectionsPolylines(response, 'rgba(250, 0, 0, 1)');
                    } else {
                        window.alert('Directions request failed due to ' + status);
                    }
                });
                directionsDisplay = new google.maps.DirectionsRenderer({
                    suppressMarkers: true
                });
            }
            var polylineOptions = {
                strokeColor: '#C83939',
                strokeOpacity: 1,
                strokeWeight: 4
            };

            var polylines = [];

            function renderDirectionsPolylines(response) {
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0; i < polylines.length; i++) {
                    polylines[i].setMap(null);
                }
                var legs = response.routes[0].legs;

                for (i = 0; i < legs.length; i++) {
                    var steps = legs[i].steps;
                    for (j = 0; j < steps.length; j++) {
                        var nextSegment = steps[j].path;
                        var stepPolyline = new google.maps.Polyline(polylineOptions);
                        stepPolyline.setOptions({
                            strokeColor: 'rgba(250, 0, 0, 1)'
                        })
                        for (k = 0; k < nextSegment.length; k++) {
                            stepPolyline.getPath().push(nextSegment[k]);
                            bounds.extend(nextSegment[k]);
                        }
                        polylines.push(stepPolyline);
                        stepPolyline.setMap(map);
                        // route click listeners, different one on each step
                        google.maps.event.addListener(stepPolyline, 'click', function(evt) {
                            infowindow.setContent("Ruta:<br>" + evt.latLng.toUrlValue(6));
                            infowindow.setPosition(evt.latLng);
                            infowindow.open(map);
                        })
                    }
                }
                map.fitBounds(bounds);
            }

        </script>
    @endsection
@endif
