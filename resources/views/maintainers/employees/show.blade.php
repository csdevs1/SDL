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
                    <span class="navbar-brand">Nuevo Grupo de Conductor</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" type="text" name="name" id="name" placeholder="Nombre" id="name">
                            <div class="input-group">
                                <input class="form-control input-sm must" type="text" placeholder="Codigo" id="code">
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
                <th>C&#243;digo</th>
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
            ?>
            <tr id="<?php echo $val['id']; ?>">
                <td class="counter"><span class="badge"><?php echo $counter; ?></span></td>
                <td><span class="name"><?php echo $val['name']; ?></span></td>
                <td><span class="badge code"><?php echo $val['code']; ?></span></td>
                <td class="date"><span class="badge"><?php echo $date; ?></span></td>
                <td>
                    <?php if(isset($permissions['update']) && !empty($permissions['update'])){ ?>
                        <span class="label label-success action-btn edit" data-id="<?php echo $val['id']; ?>" data-name="<?php echo $val['name']; ?>" data-code="<?php echo $val['code']; ?>" data-toggle="modal" data-target="#editModal">Editar</span>
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
                                <label>Nombre</label>
                                <div class="input-group" data-validate="numbers">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                    <input class="form-control must" name="edit-name" id="edit-name" type="text" placeholder="Nombre">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Codigo</label>
                                <div class="input-group" data-validate="numbers">
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-triangle-right"></span></span>
                                    <input class="form-control must" name="edit-name" id="edit-code" type="text" placeholder="Codigo">
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
            var update_employee = function(arr,id){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("id",id);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/maintainers/employees/update'
                });
            }

            $(document).on("click","#update",function() { // Button update in modal
               var name=$('#edit-name').val(),
                   code=$('#edit-code').val(),
                   id_emmployee=$(this).val(),
                   arr={};
                if(name!='')
                    arr['name']=name;
                if(code!='')
                    arr['code']=code;
                if(Object.keys(arr).length>=1 && id_emmployee!=''){
                    var update=update_employee(arr,id_emmployee);
                    update.done(function(r){
                        console.log(r);
                        if(r){
                            swal({
                                title: "Modificado",
                                text: name+" Ha sido actualizado correctamente!",
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                                $('#editModal').modal('toggle');
                                $('.driver-option[value=""]').attr('selected','selected');
                                for(var i in arr){
                                    $('#'+id_emmployee +' .'+i).html(arr[i]);
                                    $('#'+id_emmployee).css('background-color','#ff6');
                                    var timer=setTimeout(function(){
                                        clearInterval(timer);
                                        $('#'+id_emmployee).fadeTo('slow', 0.3, function(){
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
                var name=$(this).attr('data-name'),
                    code=$(this).attr('data-code'),
                    id_employee=$(this).attr('data-id'),
                    arr={};
                $('.modal-title').text('Editar '+name);
                $('#edit-name').val(name);
                $('#edit-code').val(code);
                $('#update').val(id_employee);
            });
        // UPDATE

        // DELETE
            var delete_user = function(id){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data:{id:id},
                    url: '/maintainers/employees/delete'
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
            var save_employee = function(arr){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/maintainers/employees/create'
                });
            }
            $(document).on("click","#save",function() {
                var name=$('#name').val(),
                    code=$('#code').val(),
                    arr={},
                    error=[];
                if(name!=''){
                    arr['name']=name;
                }else{
                    error.push('Agregar nombre');
                }
                if(code!=''){
                    arr['code']=code;
                }else{
                    error.push('Agregar codigo');
                }
                if(error.length<=0){
                    arr['enable']=true;
                    arr['mobile']=0;
                    arr['type_employee']=0;
                    arr['boss_id']=0;
                    var create=save_employee(arr);
                    create.done(function(r){
                        console.log(r);
                        if(Object.keys(r).length>0){
                            $('<tr id="'+r.id+'"><td class="counter"><span class="badge">0</span></td><td><span class="name">'+r.name+'</span></td><td><span class="badge code">'+r.code+'</span></td><td class="date"><span class="badge">'+r.created+'</span></td><td><span class="label label-success action-btn edit" data-id="'+r.id+'" data-toggle="modal" data-target="#editModal">Editar</span><span class="label label-danger action-btn delete" data-id="'+r.id+'">Eliminar</span></td></tr>').prependTo("table > tbody").hide().fadeIn('slow');
                            $('#name').val('');
                            $('#code').val('');
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
