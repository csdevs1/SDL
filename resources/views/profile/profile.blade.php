@extends('layouts.app')
@if(Auth::check())
    @section('content')
        <div class="navbar-company">
            <nav class="navbar" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <span class="navbar-brand">Cambio clave de acceso</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input type="password" class="form-control input-sm must" data-validate="password" size="30" id="current_password" placeholder="clave actual [obligatorio]">
                            <input type="password" class="form-control input-sm must" data-validate="password" size="30" id="password" placeholder="nueva clave  [obligatorio]">
                            <input type="password" class="form-control input-sm must" data-validate="password" id="check_password" size="30" placeholder="repetir nueva clave">
                        </div>
                        <button type="button" id="update_password" class="btn btn-info btn-flat btn-edit"><span class="glyphicon glyphicon-pencil"></span></button>
                    </div>
                </div>
            </nav>
        </div>
        <script>
            var change_password = function(arr,current_password){
                var formData = new FormData();
                    formData.append("arr",JSON.stringify(arr));
                    formData.append("current_password",current_password);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data:formData,
                    url: '/user/update'
                });
            }
            
            $(document).on("click","#update_password",function() {
               var password=$('#password').val(),
                   current_password=$('#current_password').val(),
                   check_password=$('#check_password').val(),
                   arr={},
                   errors=[];
                if(password!=''){
                    if(password==check_password)
                        arr['password']=password;
                    else
                        errors.push('Contraseña no coincide con verificacion.');
                }else
                    errors.push('Ingrese contraseña nueva.');
                if(current_password=='')
                    errors.push('Ingrese contraseña actual.');
                if(Object.keys(arr).length>=1 && errors.length==0){
                    var update=change_password(arr,current_password);
                    update.done(function(r){
                        console.log(r);
                        if(r['error']!='' && r['error'] != undefined){
                            swal({
                                title: "Error",
                                text: r['error'],
                                type: "error",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                            });
                        }else{
                            swal({
                                title: "Bien!",
                                text: 'Contraseña actualizada correctamente.',
                                type: "success",
                                showCancelButton: false,
                                confirmButtonText: "Ok",
                                closeOnConfirm: true
                            },
                            function(){
                                $('#password').val('');
                                $('#current_password').val('');
                                $('#check_password').val('');
                            });
                        }
                    });
                }else{
                    swal({
                        title: "Error",
                        text: errors.join('<br>'),
                        type: "error",
                        showCancelButton: false,
                        confirmButtonText: "Ok",
                        closeOnConfirm: true,
                        html: true
                    },
                         function(){
                    });
                }
            });
        </script>
    @endsection
@endif