@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <?php
        $obj=new CheckUserPermission();
        $uri=$_SERVER['REQUEST_URI'];
        $permissions=$obj->checkPerission($uri);
        $today = date("Y-m-d");
        $access_ok='glyphicon-ok-sign';
        $access_no='glyphicon-ban-circle';

        $reasons_documents=null;
        $reasons_items=null;
        foreach ($result as $reason) {
            if ($reason["status"] == 4) {
                if ($reason["is_document"]) {
                    $reasons_documents[] = $reason;
                } else {
                    $reasons_items[] = $reason;
                }
            }
        }
    ?>
<?php if(isset($permissions['insert']) && !empty($permissions['insert'])){ ?>
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
                    <span class="navbar-brand">Nuevo Motivo de Rechazo</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <span></span>
                            <div class="btn-group" data-toggle="buttons" data-validate="radios">
                                <label class="btn btn-default active"><input class="form-control input-sm" type="radio" name="type" value="true" checked="checked">Documento</label>
                                <label class="btn btn-default "><input class="form-control input-sm" type="radio" name="type" value="false">Producto</label>
                            </div>
                            <input class="form-control input-sm must" data-validate="text" type="text" id="code" placeholder="codigo">
                            <input class="form-control input-sm must" data-validate="text" type="text" id="reason" placeholder="motivo">
                        </div>
                        <button class="btn btn-success btn-flat btn-create" id="save"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
<?php } ?>
    <div class="container-fluid">
        <div class="col-xs-6">
            <h3>C&oacute;digos y motivos de rechazo de documentos</h3>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="5%">C&oacute;digo</th>
                        <th width="40%">Motivo</th>
                        <th width="5%">Acceso</th>
                        <th width="10%">Fecha</th>
                        <th width="5%"> Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody id="documents_table">
                    <?php
                        $i = 1;
                        foreach ($reasons_documents as $reason) {
                            $date = is_null($reason["modified"]) ? $reason["created"] : $reason["modified"];
                            $date = substr($date, 0, 19);
                            $tr_class = substr($date, 0, 10) == $today ? "info" : "";
                            $enable=$reason['enable']?'success':'warning';
                            $title=$reason['enable']?'Activo':'Inactivo';
                    ?>
                        <tr class="<?php echo $tr_class;?>" id="<?php echo $reason["id"];?>">
                            <td><span class="badge"><?php echo $i++;?></span></td>
                            <td><span class="badge alert-danger"><?php echo  $reason["code"];?></span></td>
                            <td><?php echo $reason["description"];?></td>
                            <td><span class="glyphicon <?php echo $reason["enable"] ? $access_ok:$access_no; ?>"></span></td>
                            <td><span class="badge"><?php echo $date; ?></span></td>
                            <td><button class="btn btn-<?php echo $enable; ?> btn-flat btn-sm btn-access update_access" title="<?php echo $title; ?>" value="<?php echo $reason["id"]; ?>"><span class="glyphicon glyphicon-log-in"></span></button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="clearfix visible-xs-block"></div>
        <div class="col-xs-6">
            <h3>C&oacute;digos y motivos de rechazo de productos</h3>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="5%">C&oacute;digo</th>
                        <th width="40%">Motivo</th>
                        <th width="5%">Acceso</th>
                        <th width="10%">Fecha</th>
                        <th width="5%"> Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody id="products_table">
                    <?php
                        $i = 1;
                        foreach ($reasons_items as $reason) { 
                            $date = is_null($reason["modified"]) ? $reason["created"] : $reason["modified"];
                            $date = substr($date, 0, 19);
                            $tr_class = substr($date, 0, 10) == $today ? "info" : "";      
                            $enable=$reason['enable']?'success':'warning';
                            $title=$reason['enable']?'Activo':'Inactivo';
                    ?>
                        <tr class="<?php echo $tr_class;?>" id="<?php echo $reason["id"];?>">
                            <td><span class="badge"><?php echo $i++;?></span></td>
                            <td><span class="badge alert-danger"><?php echo  $reason["code"];?></span></td>
                            <td><?php echo $reason["description"];?></td>
                            <td><span class="glyphicon <?php echo $reason["enable"] ? $access_ok:$access_no; ?>"></span></td>
                            <td><span class="badge"><?php echo $date; ?></span></td>
                            <td>
                                <button class="btn btn-<?php echo $enable; ?> btn-flat btn-sm btn-access" title="<?php echo $title; ?>" value="<?php echo $reason["id"]; ?>"><span class="glyphicon glyphicon-log-in"></span></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="snackbar"></div>
    <script>
        // UPDATE
            var update_access = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/status/update/access'
                });
            }

            $(document).on("click",".update_access",function() { // Button update in modal
               var status_id=$(this).val(),
                   el=$(this),
                   update=update_access(status_id);
                update.done(function(r){
                    var enable=r?'success':'warning',
                        remove=!r?'success':'warning';
                    $(el).addClass('btn-'+enable);
                    $(el).removeClass('btn-'+remove);
                });
            });
        // UPDATE

        // SAVE
            var save_motive = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/status/create'
                });
            }
            $(document).on("click","#save",function() {
                var type=$('input[name="type"]:checked').val(),
                    code=$('#code').val(),
                    reason=$('#reason').val(),
                    arr={},
                    error=[];
                reason!='' ? arr['description']=reason : error.push('Agregar motivo');
                code!='' ? arr['code']=code : error.push('Agregar codigo');
                arr['is_document']=type=='true'?true:false;
                if(error.length<=0){
                    arr['enable']=true;
                    arr['status']=4;
                    arr['label']=type=='true' ? 'Factura rechazada' : 'Producto rechazado';
                    var create=save_motive(arr);
                    create.done(function(r){
                        console.log(r);
                        if(Object.keys(r).length>0){
                            var table_id='#documents_table';
                            if(!r['is_document'])
                                table_id='#products_table';
                            $('<tr class="" id="'+r['id']+'"><td><span class="badge">0</span></td><td><span class="badge alert-danger">'+r['code']+'</span></td><td>'+r['reason']+'</td><td><span class="glyphicon glyphicon-ok-sign"></span></td><td><span class="badge">'+r['created']+'</span></td><td><button class="btn btn-warning btn-flat btn-sm btn-access" value="'+r['id']+'"><span class="glyphicon glyphicon-log-in"></span></button></td></tr>').prependTo(table_id).hide().fadeIn('slow');
                            $('#name').val('');
                            $('#description').val('');
                            $('#'+r['id']).css('background-color','#4fcece');
                            var timer=setTimeout(function(){
                                clearInterval(timer);
                                $('#'+r[1]).fadeTo('slow', 0.3, function(){
                                    $(this).css('background-color', '#fff');
                                }).fadeTo('slow', 1);
                            }, 3000);
                            // Get the snackbar DIV
                            var x = document.getElementById("snackbar")
                            // Add the "show" class to DIV
                            x.className = "show";
                            // After 3 seconds, remove the show class from DIV
                            $('#snackbar').text('Grupo de oficina guardado correctamente!');
                            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
                        }
                    });
                }else{
                    $('#snackbar').text('');
                    for(var i in error)
                        $('#snackbar').append('- '+error[i]+'<br>');
                    var x = document.getElementById("snackbar")
                    x.className = "show";
                    setTimeout(function(){ x.className = x.className.replace("show", ""); }, 6000);
                }
            });
        // SAVE
    </script>
    @endsection
@endif