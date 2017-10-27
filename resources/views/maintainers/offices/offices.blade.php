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
                    <span class="navbar-brand">Nueva Oficinas</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="nombre" id="name">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="descripcion" id="description">
                            <span>Actulizar</span>
                            <div class="btn-group" data-toggle="buttons" data-validate="radios">
								<label class="btn btn-default "><input class="form-control input-sm" type="radio" name="autoupdate" value="true">Sí</label>
								<label class="btn btn-default active"><input class="form-control input-sm" type="radio" name="autoupdate" value="false" checked="checked">No</label>
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
                <th>Nombre</th>
                <th>Descripci&#243;n</th>
                <th>Grupo de Veh&#237;culos</th>
                <th>Autoactualizaci&#243;n</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter=1;
                $today = date("Y-m-d");
                foreach($result as $key=>$val){
                    $label=$val['label'];
                    $description=$val['description'];
                    $n_vehicles=$val['count'];
                    $autoupdate=$val['autoupdate'];
                    $date=$val['created'];
                    $id=$val['id'];
            ?>
            <tr id="<?php echo $val['id']; ?>">
                <td class="counter"><span class="badge"><?php echo $counter; ?></span></td>
                <td class="label-val"><?php echo $label; ?></td>
                <td><span class="description-val"><?php echo $description; ?></span></td>
                <td><span class="badge"><?php echo $n_vehicles; ?></span></td>
                <td><?php $val=$autoupdate ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>'; echo $val; ?></td>
                <td><span class="badge"><?php echo $date; ?></span></td>
                <td>
                    <?php if(isset($permissions['update']) && !empty($permissions['update'])){ ?>
                        <span class="label label-success action-btn edit" data-id="<?php echo $id; ?>" data-label="<?php echo $label; ?>"  data-description="<?php echo $description; ?>" data-autoupdate="<?php echo $autoupdate; ?>" data-toggle="modal" data-target="#editModal">Editar</span>
                    <?php } ?>

                    <?php if(isset($permissions['delete']) && !empty($permissions['delete'])){ ?>
                        <span class="label label-danger action-btn delete" data-id="<?php echo $id; ?>" data-label="<?php echo $label; ?>">Eliminar</span>
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
                          <label>Nombre</label>
                          <div class="input-group" data-validate="label">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" name="label" id="edit-label" type="text" placeholder="etiqueta" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Descripcion</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="description" id="edit-description" type="text" placeholder="imei">
                          </div>
                      </div>
                      <div class="form-group">
							<label>Autoactualizar:</label>
							<div class="input-group" data-validate="radios">
								<div class="btn-group btn-group-justified" data-toggle="buttons" data-validate="radios">
									<label class="btn btn-default"><input class="form-control input-sm" type="radio" name="edit-autoupdate" value="1">Sí</label>
									<label class="btn btn-default"><input class="form-control input-sm" type="radio" name="edit-autoupdate" value="0">No</label>
								</div>
							</div>
						</div>
                      <div class="form-group">
                          <label>Asociar Grupo de Vehiclos</label>
                          <select class="form-control" id="vehicles_group" multiple>
                              <?php foreach($g_vehicles as $key=>$val){ ?>
                                  <option class="option_vehicles" value="<?php echo $val->id; ?>"><?php echo $val->label; ?></option>
                              <?php } ?>
                          </select>
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
        // UPDATE
            var update_group = function(arr,id,g_vehicles){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("id",id);
                if(g_vehicles.length>0)
                    formData.append("g_vehicles",g_vehicles);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/maintainers/update/office'
                });
            }
            
            var group_vehicles = function(id){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    //contentType:  false,
                    data:{id:id},
                    url: '/get/vehicles/office-id'
                });
            }

            $(document).on("click","#update",function() { // Button update in modal
               var group_label=$('#edit-label').val(),
                   description=$('#edit-description').val(),
                   group_id=$(this).attr('data-id'),
                   autoupdate=$("input:radio[name=edit-autoupdate]:checked").val(),
                   g_vehicles=$('#vehicles_group').val(),
                   arr={};
                if(group_label!='')
                    arr['label']=group_label;
                if(description!='')
                    arr['description']=description;
                arr['autoupdate']=autoupdate;
                if(Object.keys(arr).length>=1 && group_id!=''){
                    var update=update_group(arr,group_id,g_vehicles);
                    update.done(function(r){
                        if(r){
                            swal({
                                title: "Modificado",
                                text: group_label+" Ha sido actualizado correctamente!",
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
                                    $('#'+group_id +' .'+css).text(arr[i]);
                                    $('#'+group_id).css('background-color','#ff6');
                                    var timer=setTimeout(function(){
                                        clearInterval(timer);
                                        $('#'+group_id).fadeTo('slow', 0.3, function(){
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
                var group_label=$(this).attr('data-label'),
                    group_description=$(this).attr('data-description'),
                    autoupdate=$(this).attr('data-autoupdate'),
                    id=$(this).attr('data-id');
                if(autoupdate=='')
                    autoupdate=0;
                $("#vehicles_group option:selected").removeAttr("selected");
                $('.modal-title').text('Editar '+group_label);
                $('#edit-label').val(group_label);
                $('#edit-description').val(group_description);
                $('#update').attr('data-id',id);
                $("input:radio[value="+autoupdate+"]").click();
                g_vehicles=group_vehicles(id);
                g_vehicles.done(function(r){
                    if(Object.keys(r).length>0){
                        for(var i in r){
                            $('.option_vehicles[value=' + r[i].id + ']').attr('selected', true);
                        }
                    }
                });
            });
        // UPDATE

        // DELETE
            var delete_group = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/maintainers/delete/office'
                });
            }
            $(document).on("click",".delete",function() {
                var group_id=$(this).attr('data-id'),
                    group_label=$(this).attr('data-label');
                swal({
                    title: "¿Está seguro?",
                    text: group_label+" no podrá ser recuperado!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, estoy seguro!",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                },
                function(){
                    var delete_record=delete_group(group_id);
                    delete_record.done(function(r){
                        console.log(r);
                        if(r){
                            swal('¡'+group_label+" fué Elminado!", "Los datos fueron eliminados correctamente", "success");
                            $('#'+group_id).hide('slow', function(){$(this).remove();});
                        }
                    });
                });
            });
        // DELETE

        // SAVE
            var save_office = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/maintainers/create/offices'
                });
            }
            $(document).on("click","#save",function() {
                var name=$('#name').val(),
                    description=$('#description').val(),
                    autoupdate=$("input:radio[name=autoupdate]:checked").val(),
                    arr={},
                    error=[];
                name!='' ? arr['label']=name : error.push('Agregar nombre');
                description!='' ? arr['description']=description : error.push('Agregar descripcion');
                autoupdate!='' ? arr['autoupdate']=autoupdate : error.push('Seleccione una opcion en Acutoactualizar');
                if(error.length<=0){
                    arr['enable']=true;
                    var create=save_office(arr);
                    create.done(function(r){
                        if(Object.keys(r).length>0){
                            var auto='';
                            if(r['autoupdate']=='true')
                                auto='<span class="glyphicon glyphicon-ok"></span>';
                            else
                                auto='<span class="glyphicon glyphicon-remove"></span>';
                            $('<tr id="'+r['id']+'"><td class="counter"><span class="badge">0</span></td><td class="label-val">'+r['label']+'</td><td><span class="">'+r['description']+'</span></td><td><span class="badge">0</span></td><td><span class="glyphicon glyphicon-ok"></span></td><td><span class="badge">'+r['created']+'</span></td><td><span class="label label-success action-btn edit" data-id="'+r['id']+'" data-label="'+r['label']+'" data-description="'+r['description']+'" data-toggle="modal" data-target="#editModal">Editar</span><span class="label label-danger action-btn delete" data-id="'+r['id']+'" data-label="'+r['label']+'">Eliminar</span></td></tr>').prependTo("table > tbody").hide().fadeIn('slow');
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

        document.getElementById('edit-label').addEventListener('input', function(){
            $('.modal-title').text('Editar '+this.value);
        }, true);
    </script>
    @endsection
@endif
