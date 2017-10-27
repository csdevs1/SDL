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
                    <span class="navbar-brand">Nuevo Vehículo</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="patente" id="save_plate">
                            <div class="input-group">
                                <input class="form-control input-sm must" type="number" placeholder="metros cubicos" id="save_volume">
                                <span class="input-group-btn">
                                </span>
                            </div>
                            <div class="input-group">
                                <input class="form-control input-sm must" type="number" placeholder="tonelaje" id="save_weight">
                                <span class="input-group-btn">
                                </span>
                            </div>
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
                <th>Patente</th>
                <th>Volumen M<sup>3</sup></th>
                <th>Tonelaje</th>
                <th>Conductor</th>
                <th>Android</th>
                <th>Fecha</th>
                <th>Acci&#243;n</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter=1;
                $today = date("Y-m-d");
                foreach($result as $key=>$val){
                    $date = is_null($val["modified"]) ? $val["created"] : $val["modified"];
                    $date = substr($date, 0, 19);
                    $tr_class = substr($date, 0, 10) == $today ? "info" : "";
                    $tr_class = "{$tr_class} tr-{$val["id"]}";
                    if(isset($val['driver']['name']) && !empty($val['driver']['name']))
                        $driver_name=$val['driver']['name'];
                    else
                        $driver_name='Sin asignaci&#243;n';
                    $select_driver=!empty($val['driver']['id']) ? $val['driver']['id']:'';
            ?>
            <tr id="<?php echo $val['id']; ?>">
                <td class="counter"><span class="badge"><?php echo $counter; ?></span></td>
                <td><?php echo $val['plate_number']; ?></td>
                <td><span class="badge volume"><?php echo $val['volume']; ?></span></td>
                <td><span class="badge weight"><?php echo $val['weight']; ?></span></td>
                <td class="id_employee"><?php echo $driver_name; ?></td>
                <td><?php echo !empty($val['label_device']) ? $val['label_device']:'Sin asignaci&#243;n'; ?></td>
                <td class="date"><span class="badge"><?php echo $date; ?></span></td>
                <td>
                    <?php if(isset($permissions['update']) && !empty($permissions['update'])){ ?>
                        <span class="label label-success action-btn edit" data-value="<?php echo $val['id']; ?>" data-volume="<?php echo $val['volume']; ?>"  data-weight="<?php echo $val['weight']; ?>" data-driver="<?php echo $select_driver; ?>"  data-toggle="modal" data-target="#editModal" data-plate="<?php echo $val['plate_number']; ?>">Editar</span>
                    <?php } ?>

                    <?php if(isset($permissions['delete']) && !empty($permissions['delete'])){ ?>
                        <span class="label label-danger action-btn delete" data-plate="<?php echo $val['plate_number']; ?>" data-value="<?php echo $val['id']; ?>">Eliminar</span>
                    <?php } ?>
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
                          <label>Patente:</label>
                          <div class="input-group" data-validate="label">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <label class="form-control" name="name" id="edit-name">ANTO15</label>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Volumen M<sup>3</sup>:</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="vol" id="edit-vol" type="text" placeholder="metros cubicos">
                              <span class="input-group-btn">
                                  <button class="btn btn-default btn-up" type="button"><span class="glyphicon glyphicon-chevron-up"></span></button>
                                  <button class="btn btn-default btn-dn" type="button"><span class="glyphicon glyphicon-chevron-down"></span></button>
                              </span>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Tonelaje:</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="ton" id="edit-weight" type="text" placeholder="tonelaje">
                              <span class="input-group-btn">
                                  <button class="btn btn-default btn-up" type="button"><span class="glyphicon glyphicon-chevron-up"></span></button>
                                  <button class="btn btn-default btn-dn" type="button"><span class="glyphicon glyphicon-chevron-down"></span></button>
                              </span>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Conductor:</label>
                          <div class="input-group" data-validate="select">
                              <select class="form-control" data-validate="select" id="driver-edit" >
                                  <option class="driver-option" value="">-- Seleccione un conductor --</option>
                                  <?php
                                    $vehicleObj=new VehiclesList();
                                    $dirvers=$vehicleObj->DriversList();
                                    foreach($dirvers as $driver){ ?>
                                        <option class="driver-option" value="<?php echo $driver->id; ?>"><?php echo $driver->name; ?></option>
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
            var update_vehicle = function(arr,id){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("id",id);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/vehicle/update'
                });
            }

            $vehicle_id='';
            $(document).on("click","#update",function() { // Button update in modal
               var vehicle_name=$('#edit-name').val(),
                   vol=$('#edit-vol').val(),
                   weight=$('#edit-weight').val(),
                   driver_id=$('.driver-option:selected').val(),
                   driver_name=$('.driver-option:selected').html(),
                   arr={};
               /* if(vehicle_name!='')
                    arr['plate_number']=vehicle_name;*/
                if(vol!='' && vol>0)
                    arr['volume']=vol;
                if(weight!='' && weight>0)
                    arr['weight']=weight;
                if(driver_id)
                    arr['id_employee']=driver_id;
                if(Object.keys(arr).length>=1 && $vehicle_id!=''){
                    var update=update_vehicle(arr,$vehicle_id);
                    update.done(function(r){
                        if(r){
                            swal({
                                title: "Modificado",
                                text: vehicle_name+" Ha sido actualizado correctamente!",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                                $('#editModal').modal('toggle');
                                $('.driver-option[value=""]').attr('selected','selected');
                                for(var i in arr){
                                    if(i=='id_employee')
                                        arr[i]=driver_name;
                                    $('#'+$vehicle_id +' .'+i).html(arr[i]);
                                    $('#'+$vehicle_id).css('background-color','#ff6');
                                    var timer=setTimeout(function(){
                                        clearInterval(timer);
                                        $('#'+$vehicle_id).fadeTo('slow', 0.3, function(){
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
                $vehicle_id=$(this).attr('data-value');
                var vehicle_plate=$(this).attr('data-plate'),
                    vehicle_volume=$(this).attr('data-volume'),
                    vehicle_weight=$(this).attr('data-weight'),
                    driver=$(this).attr('data-driver'),
                    arr={};
                $('.modal-title').text('Editar '+vehicle_plate);
                $('#edit-name').val(vehicle_plate);
                $('#edit-vol').val(vehicle_volume);
                $('#edit-weight').val(vehicle_weight);
                if(driver!='')
                    $('.driver-option[value="'+driver+'"]').attr('selected','selected');
            });
        // UPDATE

        // DELETE
            var delete_vehicle = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/vehicle/delete'
                });
            }
            $(document).on("click",".delete",function() {
                var vehicle_id=$(this).attr('data-value'),
                    vehicle_plate=$(this).attr('data-plate');
                swal({
                    title: "¿Está seguro elinimar "+vehicle_plate+"?",
                    text: "Estos datos no podrán ser recuperados!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, estoy seguro!",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                },
                function(){
                    var delete_record=delete_vehicle(vehicle_id);
                    delete_record.done(function(r){
                        console.log(r);
                        if(r){
                            swal('¡'+vehicle_plate+" fué Elminado!", "Los datos fueron eliminados correctamente", "success");
                            $('#'+vehicle_id).hide('slow', function(){$(this).remove();});
                        }
                    });
                });
            });
        // DELETE

        // SAVE
            var save_vehicle = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/vehicle/save'
                });
            }
            $(document).on("click","#save",function() {
                var plate=$('#save_plate').val(),
                    vol=$('#save_volume').val(),
                    weight=$('#save_weight').val(),
                    arr={},
                    error=[];
                if(plate!=''){
                    arr['plate_number']=plate;
                    arr['label']=plate;
                }else{
                    error.push('Agregar patente');
                }
                vol!='' ? arr['volume']=vol : error.push('Agregar metros cubicos');
                weight!='' ? arr['weight']=weight : error.push('Agregar tonelaje');
                if(error.length<=0){
                    arr['enable']=true;
                    var create=save_vehicle(arr);
                    create.done(function(r){
                        console.log(r);
                        if(r[0]){
                            $('<tr id="'+r[1]+'"><td class="counter"><span class="badge">0</span></td><td>'+plate+'</td><td><span class="badge volume">'+vol+'</span></td><td><span class="badge weight">'+weight+'</span></td><td class="id_employee">Sin asignación</td><td>Sin asignación</td><td class="date"><span class="badge">'+r[2]+'</span></td><td><span class="label label-success action-btn edit" data-value="'+r[1]+'" data-volume="'+vol+'" data-weight="'+weight+'" data-driver="" data-toggle="modal" data-target="#editModal" data-plate="'+plate+'">Editar</span><span class="label label-danger action-btn delete" data-plate="'+plate+'" data-value="'+r[1]+'">Eliminar</span></td></tr>').prependTo("table > tbody").hide().fadeIn('slow');
                            $('#save_plate').val('');
                            $('#save_volume').val('');
                            $('#save_weight').val('');
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
    </script>
    @endsection
@endif
