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
                    <span class="navbar-brand">Nuevo Usuario</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" type="email" name="email" id="email" placeholder="Email" id="email">
                            <div class="input-group">
                                <input class="form-control input-sm must" type="password" placeholder="Contraseña" id="password">
                            </div>                            
                            <div class="btn-group bootstrap-select must">                                
                                <select class="form-control" id="profiles">
                                    <option class="profile" value=''>-- Selectione un perfil --</option>
                                    <?php foreach($profiles as $k=>$v){ ?>
                                        <option class="profile" value='<?php echo $v->id ?>'><?php echo $v->label.': '.$v->description; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="btn-group bootstrap-select must">                                
                                <select class="form-control" id="offices">
                                    <option class="office" value=''>-- Selectione un grupo de oficina --</option>
                                    <?php foreach($group_of_offices as $k=>$v){ ?>
                                        <option class="office" value='<?php echo $v->id ?>'><?php echo $v->label; ?></option>
                                    <?php } ?>
                                </select>
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
                <th>Usurio</th>
                <th>Perfil</th>
                <th>Grupo de Oficinas</th>
                <th>Acceso</th>
                <th>Fecha</th>
                <th>Acci&#243;n</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $counter=1;
                $today = date("Y-m-d");
                foreach($users as $key=>$val){
                    $date = is_null($val["modified"]) ? $val["created"] : $val["modified"];
                    $date = substr($date, 0, 19);
                    $span=$val['seller']?'<span class="glyphicon glyphicon-ok-sign text-primary"></span>':'<span class="glyphicon glyphicon-remove-sign text-danger"></span>';
            ?>
            <tr id="<?php echo $val['id']; ?>">
                <td class="counter"><span class="badge"><?php echo $counter; ?></span></td>
                <td><span class="badge email"><?php echo $val['email']; ?></span></td>
                <td><span class="badge id_profile"><?php echo $val['label_profile']; ?></span></td>
                <td class="id_group_of_office"><?php echo $val['label_group_office']; ?></td>
                <td class="seller"><?php echo $span; ?></td>
                <td class="date"><span class="badge"><?php echo $date; ?></span></td>
                <td>
                    <?php if(isset($permissions['update']) && !empty($permissions['update'])){ ?>
                        <span class="label label-success action-btn edit" data-email="<?php echo $val['email']; ?>" data-id="<?php echo $val['id']; ?>" data-profile="<?php echo $val['id_profile']; ?>" data-group="<?php echo $val['id_group_of_office']; ?>"   data-toggle="modal" data-target="#editModal">Editar</span>
                    <?php } ?>

                    <?php if(isset($permissions['delete']) && !empty($permissions['delete'])){ ?>
                        <span class="label label-danger action-btn delete" data-id="<?php echo $val['id']; ?>">Eliminar</span>
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
                      <div class="form-group">
                          <label>Email</label>
                          <div class="input-group" data-validate="numbers">
                              <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                              <input class="form-control must" name="edit-email" id="edit-email" type="email" placeholder="Email">
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Perfil:</label>
                          <div class="input-group" data-validate="select">
                              <select class="form-control" id="edit-profile">
                                    <?php foreach($profiles as $k=>$v){ ?>
                                        <option class="edit-profile" value='<?php echo $v->id ?>'><?php echo $v->label.': '.$v->description; ?></option>
                                    <?php } ?>
                                </select>
                          </div>
                      </div>
                      <div class="form-group">
                          <label>Grupo de oficina:</label>
                          <div class="input-group" data-validate="select">
                              <select class="form-control" id="edit-group">
                                    <?php foreach($group_of_offices as $k=>$v){ ?>
                                        <option class="edit-group" value='<?php echo $v->id ?>'><?php echo $v->label; ?></option>
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
            var update_user = function(arr,id){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("id",id);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/user/update'
                });
            }

            $(document).on("click","#update",function() { // Button update in modal
               var user_email=$('#edit-email').val(),
                   id_profile=$('#edit-profile').val(),
                   id_group=$('#edit-group').val(),
                   label_profile=$('#edit-profile option:selected').html(),
                   label_group=$('#edit-group option:selected').html(),
                   id_user=$(this).val(),
                   arr={};
                if(user_email!='')
                    arr['email']=user_email;
                if(id_profile!='')
                    arr['id_profile']=id_profile;
                if(id_group)
                    arr['id_group_of_office']=id_group;
                if(Object.keys(arr).length>=1 && id_user!=''){
                    var update=update_user(arr,id_user);
                    update.done(function(r){
                        console.log(r);
                        if(r){
                            swal({
                                title: "Modificado",
                                text: user_email+" Ha sido actualizado correctamente!",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                                $('#editModal').modal('toggle');
                                $('.driver-option[value=""]').attr('selected','selected');
                                for(var i in arr){
                                    if(i=='id_group_of_office')
                                        arr[i]=label_group;
                                    if(i=='id_profile')
                                        arr[i]=label_profile.split(':')[0];
                                    $('#'+id_user +' .'+i).html(arr[i]);
                                    $('#'+id_user).css('background-color','#ff6');
                                    var timer=setTimeout(function(){
                                        clearInterval(timer);
                                        $('#'+id_user).fadeTo('slow', 0.3, function(){
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
                var user_email=$(this).attr('data-email'),
                    user_id=$(this).attr('data-id'),
                    id_profile=$(this).attr('data-profile'),
                    id_group_of_office=$(this).attr('data-group'),
                    arr={};
                $('.modal-title').text('Editar '+user_email);
                $('#edit-email').val(user_email);
                $('#update').val(user_id);
                if(id_profile!='')
                    $('.edit-profile[value="'+id_profile+'"]').attr('selected','selected');
                if(id_group_of_office!='')
                    $('.edit-group[value="'+id_group_of_office+'"]').attr('selected','selected');
            });
        // UPDATE

        // DELETE
            var delete_user = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/user/delete'
                });
            }
            $(document).on("click",".delete",function() {
                var id=$(this).attr('data-id');
                swal({
                    title: "¿Está seguro elinimar este usuario?",
                    text: "Estos datos no podrán ser recuperados!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Sí, estoy seguro!",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                },
                function(){
                    var delete_record=delete_user(id);
                    delete_record.done(function(r){
                        console.log(r);
                        if(r){
                            swal('¡El usuario fué Elminado!"', "Los datos fueron eliminados correctamente", "success");
                            $('#'+id).hide('slow', function(){$(this).remove();});
                        }
                    });
                });
            });
        // DELETE

        // SAVE
            var save_user = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/user/create'
                });
            }
            $(document).on("click","#save",function() {
                var email=$('#email').val(),
                    psswd=$('#password').val(),
                    profile=$('.profile:selected').val(),
                    office=$('.office:selected').val(),
                    arr={},
                    error=[];
                if(email!=''){
                    arr['email']=email;
                }else{
                    error.push('Agregar email');
                }
                if(psswd!=''){
                    arr['password']=psswd;
                }else{
                    error.push('Agregar contraseña');
                }
                if(profile!=''){
                    arr['id_profile']=parseInt(profile);
                }else{
                    error.push('Seleccionar perfil');
                }
                if(office!=''){
                    arr['id_group_of_office']=parseInt(office);
                }else{
                    error.push('Seleccionar grupo de oficina');
                }
                if(error.length<=0){
                    arr['enable']=true;
                    var create=save_user(arr);
                    create.done(function(r){
                        console.log(r);
                        if(Object.keys(r).length>0){
                            $('<tr id="'+r['id']+'"><td class="counter"><span class="badge">0</span></td><td><span class="badge email">'+r['email']+'</span></td><td><span class="badge label_profile">'+r['profile']+'</span></td><td class="group_office">'+r['group']+'</td><td class="seller"><span class="glyphicon glyphicon-remove-sign text-danger"></span></td><td class="date"><span class="badge">'+r['created']+'</span></td><td><span class="label label-success action-btn edit" data-id="'+r['id']+'" data-toggle="modal" data-target="#editModal">Editar</span><span class="label label-danger action-btn delete" data-id="'+r['id']+'">Eliminar</span></td></tr>').prependTo("table > tbody").hide().fadeIn('slow');
                            $('#email').val('');
                            $('#password').val('');
                            $('#profiles').val($("#profiles option:first").val());
                            $('#offices').val($("#offices option:first").val());
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
