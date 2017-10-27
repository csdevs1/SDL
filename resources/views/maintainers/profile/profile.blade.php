@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <?php
        $obj=new CheckUserPermission();
        $uri=$_SERVER['REQUEST_URI'];
        $permissions=$obj->checkPerission($uri);
    ?>
<?php //if(isset($permissions['insert']) && !empty($permissions['insert'])){ ?>
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
                    <span class="navbar-brand">Nuevo Perfil</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="nombre" id="name">
                            <input class="form-control input-sm must" type="text" name="label" placeholder="descripcion" id="description">
                        </div>
                        <button class="btn btn-success btn-flat btn-create" id="save"><span class="glyphicon glyphicon-plus-sign"></span></button>
                    </div>
                </div>
            </div>
        </nav>
    </div>
<?php //} ?>
    <table class="table table-hover table-condensed">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Descripci&#243;n</th>
                <th>Fecha</th>
                <th>Acci&#243;n</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter=1;
                foreach($result as $key=>$val){
                    $label=$val->label;
                    $description=$val->description;
                    $date=$val->created;
                    $id=$val->id;
            ?>
            <tr id="<?php echo $val['id']; ?>">
                <td class="counter"><span class="badge"><?php echo $counter; ?></span></td>
                <td class="label-val"><?php echo $label; ?></td>
                <td class="description-val"><?php echo $description; ?></td>
                <td><span class="badge"><?php echo $date; ?></span></td>
                <td>
                    <?php //if(isset($permissions['update']) && !empty($permissions['update'])){ ?>
                        <span class="label label-success action-btn edit" data-id="<?php echo $id; ?>" data-label="<?php echo $label; ?>"  data-description="<?php echo $description; ?>" data-toggle="modal" data-target="#editModal">Editar</span>
                        <a class="label btn-primary" href="/maintainers/profiles/permission/<?php echo $id; ?>">Permisos</a>
                    <?php //} ?>

                    <?php //if(isset($permissions['delete']) && !empty($permissions['delete'])){ ?>
                        <span class="label label-danger action-btn delete" data-id="<?php echo $id; ?>" data-label="<?php echo $label; ?>">Eliminar</span>
                    <?php //} ?>
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
                              <input class="form-control must" name="label" id="edit-label" type="text" placeholder="Etiqueta" required>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Descripcion</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-chevron-right"></span></span>
                              <input class="form-control must" data-validate="number" name="description" id="edit-description" type="text" placeholder="Descripcion">
                          </div>
                      </div>
                      <!--<div class="form-group">
                          <label>Asignar Permisos</label>
                          <div class="row">
                              <div class="col-xs-4"><label><input type="checkbox" name="permission" value="1"> Insertar</label></div>
                              <div class="col-xs-4"><label><input type="checkbox" name="permission" value="2"> Actualizar</label></div>
                              <div class="col-xs-4"><label><input type="checkbox" name="permission" value="3"> Eliminar</label></div>
                          </div>
                      </div>-->
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
            var update_profile = function(arr,id,vehicles){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("id",id);
                if(vehicles.length>0)
                    formData.append("vehicles",vehicles);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/maintainers/profile/update'
                });
            }
            
           /* var get_vehicles = function(id){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    //contentType:  false,
                    data:{id:id},
                    url: '/get/vehicles/vehicle-group-id'
                });
            }*/

            $(document).on("click","#update",function() { // Button update in modal
               var profile_label=$('#edit-label').val(),
                   description=$('#edit-description').val(),
                   profile_id=$(this).attr('data-id'),
                   permission=$(this).attr('data-id'),
                   arr={};
                if(profile_label!='')
                    arr['label']=profile_label;
                if(description!='')
                    arr['description']=description;
                if(Object.keys(arr).length>=1 && profile_id!=''){
                    var update=update_profile(arr,profile_id,vehicles);
                    update.done(function(r){
                        if(r){
                            swal({
                                title: "Modificado",
                                text: profile_label+" Ha sido actualizado correctamente!",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                                $('#editModal').modal('toggle');
                                for(var i in arr){
                                    var css=i;
                                    $('#'+profile_id +' .'+css+'-val').text(arr[i]);
                                    $('#'+profile_id).css('background-color','#ff6');
                                    var timer=setTimeout(function(){
                                        clearInterval(timer);
                                        $('#'+profile_id).fadeTo('slow', 0.3, function(){
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
                    id=$(this).attr('data-id');
                $("#offices option:selected").removeAttr("selected");
                $('.modal-title').text('Editar '+group_label);
                $('#edit-label').val(group_label);
                $('#edit-description').val(group_description);
                $('#update').attr('data-id',id);
              /*  vehicles=get_vehicles(id);
                vehicles.done(function(r){
                    if(Object.keys(r).length>0){
                        for(var i in r){
                            $('.option_vehicles[value=' + r[i].id + ']').attr('selected', true);
                        }
                    }
                });*/
            });
        // UPDATE

        // DELETE
            var delete_profile = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/maintainers/profile/delete'
                });
            }
            $(document).on("click",".delete",function() {
                var profile_id=$(this).attr('data-id'),
                    profile_label=$(this).attr('data-label');
                swal({
                    title: "¿Está seguro?",
                    text: profile_label+" no podrá ser recuperado!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, estoy seguro!",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                },
                function(){
                    var delete_record=delete_profile(profile_id);
                    delete_record.done(function(r){
                        console.log(r);
                        if(r){
                            swal('¡'+profile_label+" fué Elminado!", "Los datos fueron eliminados correctamente", "success");
                            $('#'+profile_id).hide('slow', function(){$(this).remove();});
                        }
                    });
                });
            });
        // DELETE

        // SAVE
            var save_profile = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/maintainers/create/profile'
                });
            }
            $(document).on("click","#save",function() {
                var name=$('#name').val(),
                    description=$('#description').val(),
                    arr={},
                    error=[];
                name!='' ? arr['label']=name : error.push('Agregar nombre');
                description!='' ? arr['description']=description : error.push('Agregar descripcion');
                if(error.length<=0){
                    arr['enable']=true;
                    var create=save_profile(arr);
                    create.done(function(r){
                        if(Object.keys(r).length>0){
                            $('<tr id="'+r['id']+'"><td class="counter"><span class="badge">0</span></td><td class="label-val">'+r['label']+'</td><td><span class="">'+r['description']+'</span></td><td><span class="badge">'+r['created']+'</span></td><td><span class="label label-success action-btn edit" data-id="'+r['id']+'" data-label="'+r['label']+'" data-description="'+r['description']+'" data-toggle="modal" data-target="#editModal">Editar</span><span class="label label-danger action-btn delete" data-id="'+r['id']+'" data-label="'+r['label']+'">Eliminar</span></td></tr>').prependTo("table > tbody").hide().fadeIn('slow');
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
                            $('#snackbar').text('Mantenedor guardado correctamente!');
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