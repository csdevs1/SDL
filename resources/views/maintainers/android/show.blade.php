@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <?php
        $obj=new CheckUserPermission();
        $uri=$_SERVER['REQUEST_URI'];
        $permissions=$obj->checkPerission($uri);
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
                    <span class="navbar-brand">Nuevo Dispositivo Android</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="etiqueta" id="label">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="imei" id="imei">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="imsi" id="imsi">
                        </div>
                        <button class="btn btn-success btn-flat btn-create" id="save"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
<?php } ?>
    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>IMEI</th>
                <th>IMSI</th>
                <th>Acceso</th>
                <th>Versi&#243;n</th>
                <th>APK Actual</th>
                <th>APK Update</th>
                <th>Patente</th>
                <th>Fecha</th>
                <th>Acci&#243;n</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter=1;
                $today = date("Y-m-d");
                foreach($result as $key=>$val){
                    $date = is_null($val["modified"]) ? 'SIN CONEXION' : $val["modified"];
                    $warning_class = is_null($val["modified"]) ? 'badge-danger' : 'badge-warning';
                    $date = substr($date, 0, 19);
                    $fecha_apk = is_null($val["fecha_apk"]) ? '-' : $val["fecha_apk"];
                    $fecha_apk = substr($fecha_apk, 0, 19);
                    $apk_actual = is_null($val["actual"]) ? '-' : $val["actual"];
                    $plate = is_null($val["plate_number"]) ? 'Sin asignaci&#243;n' : $val["plate_number"];
                    $warning_apk = $apk_actual!=$val['apk_version'] ? 'badge-warning' : '';
            ?>
            <tr id="<?php echo $val['id']; ?>">
                <td class="counter"><span class="badge"><?php echo $counter; ?></span></td>
                <td class="label-val"><?php echo $val['label']; ?></td>
                <td><span class="badge imei"><?php echo $val['imei']; ?></span></td>
                <td><span class="badge imsi"><?php echo $val['imsi']; ?></span></td>
                <td><span class="glyphicon glyphicon-ok-sign"></span></td>
                <td><span class="badge apk_version"><?php echo $val['apk_version']; ?></span></td>
                <td><span class="badge <?php echo $warning_apk; ?>"><?php echo $apk_actual; ?></span></td>
                <td class="date"><span class="badge <?php echo $warning_class; ?>"><?php echo $date; ?></span></td>
                <td class="plate_number"><?php echo $plate; ?></td>
                <td><?php echo $fecha_apk; ?></td>
                <td>
                    <?php if(isset($permissions['update']) && !empty($permissions['update'])){ ?>
                        <a href='#' title="Editar" data-toggle="modal" data-target="#editModal" data-value="<?php echo $val['id']; ?>" data-version="<?php echo $val['apk_version']; ?>" data-plate="<?php echo $plate; ?>" data-label="<?php echo $val['label']; ?>" data-imei="<?php echo $val['imei']; ?>" data-imsi="<?php echo $val['imsi']; ?>" class="edit"><i class="glyphicon glyphicon-edit action-btn"></i></a>
                    <?php } ?>

                    <?php if(isset($permissions['delete']) && !empty($permissions['delete'])){ ?>
                        <a href='#' title="Eliminar" class="warning-del delete" data-value="<?php echo $val['id']; ?>" data-label="<?php echo $val['label']; ?>"><i class="glyphicon glyphicon-trash action-btn"></i></a>
                    <?php } ?>
                    <a href='<?php echo $val['apk_url']; ?>' title="Descargar" class="download" download><i class="glyphicon glyphicon-save action-btn"></i></a>
                </td>
            </tr>
            <?php
                    $counter++;
                }
            ?>
        </tbody>
    </table>
    <div id="snackbar"></div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Modal title</h4>
          </div>
          <div class="modal-body">
              <div class="panel panel-default">
                  <div class="panel-body panel-body-form">
                      <div class="form-group">
                          <label>Etiqueta</label>
                          <div class="input-group" data-validate="label">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" name="label" id="edit-label" type="text" placeholder="etiqueta" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>IMEI</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="imei" id="edit-imei" type="text" placeholder="imei">
                          </div>
                      </div>
                      <div class="form-group">
                          <label>IMSI</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="imsi" id="edit-imsi" type="text" placeholder="imei">
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Versi&#243;n</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="version" id="edit-version" type="text" placeholder="version">
                          </div>
                      </div>
                      <div class="or-spacer">
                          <div class="mask"></div>
                          <span><i class="glyphicon glyphicon-phone"></i></span>
                      </div>
                      <div class="form-group">
                          <label>Patentes Disponibles</label>
                          <div class="input-group" data-validate="select">
                              <select class="form-control" data-validate="select" id="driver-edit" >
                                  <option class="driver-option" value="">-- Seleccione una patente --</option>
                                  <?php
                                    $vehicleObj=new VehiclesList();
                                    $plates=$vehicleObj->PlateNumbers();
                                    foreach($plates as $plate){ ?>
                                        <option class="plate-option" value="<?php echo $plate->plate_number; ?>"><?php echo $plate->plate_number; ?></option>
                                  <?php } ?>
                              </select>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="update">Editar</button>
          </div>
        </div>
      </div>
    </div>
    <script>
        $(document).on("click",".btn-up",{},function() {
            var i = $(this).parent().prev(), v = parseInt(i.val());
            i.val($.isNumeric(v) ? v+1:0);
        });
        $(document).on("click",".btn-dn",{},function() {
            var i = $(this).parent().prev(), v = parseInt(i.val());
            i.val(v>0 ? v-1:0);
        });
        // UPDATE
            var update_device = function(arr,id){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("id",id);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/devices/update'
                });
            }

            $device_id='';
            $(document).on("click","#update",function() { // Button update in modal
               var device_label=$('#edit-label').val(),
                   imei=$('#edit-imei').val(),
                   imsi=$('#edit-imsi').val(),
                   version=$('#edit-version').val(),
                   plate=$('.plate-option:selected').html(),
                   arr={};
                arr['plate_number']=plate;
                if(device_label!='')
                    arr['label']=device_label;
                if(imei!='' && imei>0)
                    arr['imei']=imei;
                if(imsi!='' && imei>0)
                    arr['imsi']=imsi;
                if(version!='')
                    arr['apk_version']=version;
                if(Object.keys(arr).length>=1 && $device_id!=''){
                    arr['enable']=true;
                    var update=update_device(arr,$device_id);
                    update.done(function(r){
                        console.log(r);
                        if(r){
                            swal({
                                title: "Modificado",
                                text: device_label+" Ha sido actualizado correctamente!",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                                $('#editModal').modal('toggle');
                                for(var i in arr){
                                    var css=i;
                                    if(i=='label')
                                        css='label-val';
                                    $('#'+$device_id +' .'+css).text(arr[i]);
                                    $('#'+$device_id).css('background-color','#ff6');
                                    var timer=setTimeout(function(){
                                        clearInterval(timer);
                                        $('#'+$device_id).fadeTo('slow', 0.3, function(){
                                            $(this).css('background-color', '#fff');
                                        }).fadeTo('slow', 1);
                                    }, 3000);
                                }
                            });
                        }
                    });
                }
            });

            $(document).on("click",".edit",function() {
                $device_id=$(this).attr('data-value');
                var device_label=$(this).attr('data-label'),
                    device_imei=$(this).attr('data-imei'),
                    device_imsi=$(this).attr('data-imsi'),
                    device_version=$(this).attr('data-version'),
                    plate=$(this).attr('data-plate');
                $('.modal-title').text('Editar '+device_label);
                $('#edit-label').val(device_label);
                $('#edit-imei').val(device_imei);
                $('#edit-imsi').val(device_imsi);
                $('#edit-version').val(device_version);
                if(plate!='')
                    $('.plate-option[value="'+plate+'"]').attr('selected','selected');
            });
        // UPDATE

        // DELETE
            var delete_device = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/device/delete'
                });
            }
            $(document).on("click",".delete",function() {
                var device_id=$(this).attr('data-value'),
                    device_label=$(this).attr('data-label');
                swal({
                    title: "¿Está seguro?",
                    text: device_label+" no podrá ser recuperado!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, estoy seguro!",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                },
                function(){
                    var delete_record=delete_device(device_id);
                    delete_record.done(function(r){
                        console.log(r);
                        if(r){
                            swal('¡'+device_label+" fué Elminado!", "Los datos fueron eliminados correctamente", "success");
                            $('#'+device_id).hide('slow', function(){$(this).remove();});
                        }
                    });
                });
            });
        // DELETE

        // SAVE
            var save_device = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/devices/save'
                });
            }
            $(document).on("click","#save",function() {
                var label=$('#label').val(),
                    imei=$('#imei').val(),
                    imsi=$('#imsi').val(),
                    arr={},
                    error=[];
                label!='' ? arr['label']=label : error.push('Agregar etiqueta');
                imei!='' ? arr['imei']=imei : error.push('Agregar IMEI');
                imsi!='' ? arr['imsi']=imsi : error.push('Agregar IMSI');
                if(error.length<=0){
                    arr['enable']=true;
                    var create=save_device(arr);
                    create.done(function(r){
                        if(r['bool']){
                            $('<tr id="'+r['id']+'"><td class="counter"><span class="badge">0</span></td><td class="label-val">'+r['label']+'</td><td><span class="badge imei">'+r['imei']+'</span></td><td><span class="badge imsi">'+r['imsi']+'</span></td><td><span class="glyphicon glyphicon-ok-sign"></span></td><td><span class="badge apk_version">'+r['apk_version']+'</span></td><td><span class="badge badge-warning">-</span></td><td class="date"><span class="badge badge-danger">Sin conexión</span></td><td>Sin asignación</td><td>-</td><td><a href="#" data-toggle="modal" data-target="#editModal" data-value="'+r['id']+'" data-version="'+r['apk_version']+'" data-label="'+r['label']+'" data-imei="'+r['imei']+'" data-imsi="'+r['imsi']+'" class="edit"><i class="glyphicon glyphicon-edit action-btn"></i></a><a href="#" class="warning-del delete" data-value="'+r['id']+'" data-label="'+r['label']+'"><i class="glyphicon glyphicon-trash action-btn"></i></a></td></tr>').prependTo("table > tbody").hide().fadeIn('slow');
                            $('#label').val('');
                            $('#imei').val('');
                            $('#imsi').val('');
                            $('#'+r[1]).css('background-color','#4fcece');
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
                            $('#snackbar').text('Vehiculo guardado correctamente!');
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

        document.getElementById('edit-label').addEventListener('input', function(){
            $('.modal-title').text('Editar '+this.value);
        }, true);
    </script>
    @endsection
@endif
