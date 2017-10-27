@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <?php
        $obj=new CheckUserPermission();
        $uri=$_SERVER['REQUEST_URI'];
        $permissions=$obj->checkPerission($uri);
    ?>
    <h3 style="text-align:center;">Asignar permisos para <b><?php echo $profile->label; ?></b></h3>
    <input type="hidden" value="<?php echo $profile->id; ?>" id="profile-id">
    <div class="container">
        <div class="row">
            <?php foreach($modules as $module){ ?>
            <div class="col-md-3">
                <div class="card-block">
                    <h4 class="card-title"><?php echo $module->label; ?></h4>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo $module->created_at; ?></h6>
                    <p class="card-text"><?php echo $module->description; ?></p>
                    <a href="#" class="card-link" data-toggle="modal" data-target="#myModal" data-id="<?php echo $module->id; ?>" data-module="<?php echo $module->label; ?>">Permisos</a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div id="snackbar"></div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"></h4>
                </div>
                <div class="modal-body">
                    <?php foreach($permissionsList as $p){ ?>
                        <div class="col-md-3">
                            <label><input type="checkbox" name="permission[]" value="<?php echo $p->id; ?>"> <?php echo $p->permission; ?></label>
                        </div>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="save">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // SAVE
            var save_perission = function(permissions_id,id_profile,id_module){
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    //processData: false,
                   // contentType:  false,
                    data:{permissions_id:permissions_id,id_profile:id_profile,id_module:id_module},
                    url: '/maintainers/create/profile/permission'
                });
            }
            var get_perimssions = function(id_profile){
                return $.ajax({
                    type: 'GET',
                    dataType: 'json',
                    //processData: false,
                    //contentType:  false,
                    data:{id:id_profile},
                    url: '/get/permimssions/id'
                });
            }
            $(document).on("click",".card-link",function() {
                var module_name=$(this).attr('data-module'),
                    id_profile=$('#profile-id').val(),
                    id=$(this).attr('data-id');
                $('.modal-title').text('Asignar permisos a '+module_name);
                $('#save').attr('data-id',id);
                var permissions=get_perimssions(id_profile);
                permissions.done(function(r){
                    for(var i in r){
                        $("input[name='permission[]']").map(function(){
                            if($(this).val()==r[i]['id'])
                                $(this).attr('checked', true);
                        });
                    }
                });
            });            
            $(document).on("click","#save",function() {
                var permissions_id=[],
                    id_profile=$('#profile-id').val(),
                    id_module=$(this).attr('data-id'),
                    error=[];
                $("input[name='permission[]']:checked").map(function(){
                    permissions_id.push($(this).val());
                });
                if(permissions_id.length<=0)
                    error.push('Seleccione un permiso');
                if(error.length<=0){
                    var create=save_perission(permissions_id,id_profile,id_module);
                    create.done(function(r){
                        if(Object.keys(r).length>0){
                            // Get the snackbar DIV
                            var x = document.getElementById("snackbar")
                            // Add the "show" class to DIV
                            x.className = "show";
                            // After 3 seconds, remove the show class from DIV
                            $('#snackbar').text('Permisos asignados correctamente!');
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