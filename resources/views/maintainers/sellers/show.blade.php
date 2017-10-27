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
                    <span class="navbar-brand">Nuevo Vendedor</span>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <div class="navbar-form navbar-left" role="form">
                        <div class="form-group">
                            <input class="form-control input-sm must" data-validate="text" type="text" id="name" placeholder="nombre">
                            <input class="form-control input-sm must" data-validate="text" type="text"id="lname" placeholder="apellido">
                            <input class="form-control input-sm must" data-validate="email" type="text" id="email" placeholder="email">
                            <input class="form-control input-sm must" data-validate="password" type="password" id="password" placeholder="clave de acceso">
                            <input class="form-control input-sm must" data-validate="text" type="text" id="code" placeholder="codigo">
                            <select class="must form-control" data-validate="select" id="profiles">
                                <option class="profile">- Seleccione perfil -</option>
                                <option class="profile" value="<?php echo $id_profile_seller; ?>">Consultas: Consultores</option>
                                <option class="profile" value="<?php echo $id_profile_consoulting; ?>">Ventas: vendedores</option>
                            </select>
                        </div>
                        <button class="btn btn-success btn-flat btn-create"><span class="glyphicon glyphicon-plus-sign" id="save"></span></button>
				</div>
                </div>
            </div>
        </nav>
    </div>
<?php } ?>
    <div class="container-fluid">
        <div class="col-xs-4">
            <h4><span class="badge pull-right n_sellers"><?php echo count($result); ?></span>Vendedores:</h4>
            <div class="object-container sellers-box">
                <?php
                $max = 0;
                foreach($result as $object) { $l = strlen(trim($object["code"])); if ($max < $l) $max = $l; }
                foreach($result as $object) {
                    $code = str_pad(trim($object["code"]), $max, "|", STR_PAD_LEFT);
                    $code = str_replace("|", "&nbsp;", $code);
                ?>
                <div class="input-group">
                    <label class="form-control" data-value="<?php echo $object["id"]; ?>"><span class="badge pull-right"><?php echo count($object["customers"]); ?></span><tt class="overflow"><span class='label label-info'><?php echo $code; ?></span> <?php echo $object["name"]; ?></tt></label>
                    <span class="input-group-btn"><button class="btn btn-default btn-seller" type="button" value="<?php echo $object["id"]; ?>"><b>&gt;</b></button></span>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="clearfix visible-xs-block"></div>
        <div class="col-xs-8">
            <h4><span class="badge pull-right"></span>Clientes: </h4>
            <?php if(isset($permissions['insert']) && !empty($permissions['insert'])){ ?>
                <button class="btn btn-primary associate">Asociar</button>
            <?php } ?>

            <div class="object-container">
                <div class="well object-container-title">Seleccione un vendedor para listar sus clientes</div>
                <ul class="list-group checked-list-box clients">
                    <?php /*foreach($customers as $customer) { ?>
                        <li class="list-group-item client" data-value="<?php echo $customer["id"]; ?>"><tt class=""><span class='label label-subcode'><?php echo $customer["subcode"]; ?></span> <?php echo $customer["name"]; ?></tt></li>
                    <?php }*/ ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="snackbar"></div>
    <script>
        $(document).ready(function(){
            $('.associate').hide();
        });
        var get_clients = function(id){
            return $.ajax({
                type: 'GET',
                dataType: 'json',
                data:{id:id},
                url: '/sellers/get/clients/id'
            });
        }
        var get_all_clients = function(id){
            return $.ajax({
                type: 'GET',
                dataType: 'json',
                data:{id:id},
                url: '/sellers/get/clients'
            });
        }
        $(document).on('click','.btn-seller',function(){
            $('.associate').show(100);
            $('.btn-success').removeClass('btn-success').html('Asociar').removeAttr('id').addClass('associate');
            var id_customer=$(this).val(),
                clients=get_clients(id_customer);
            clients.done(function(r){
                $('.clients').html('');
                $('.associate').show(100);
                $('.associate').attr('data-seller',id_customer);
                if(Object.keys(r).length>0){
                    $('.well').hide();
                    for(var i in r){
                        var html="<li class='list-group-item client' data-value='"+r[i].id+"'><tt class=''><span class='label label-subcode'>"+r[i].subcode+"</span> "+r[i].name+"</tt></li>";
                        $('.clients').append(html);
                    }
                }else{
                    $('.well').show();
                }
            });
        });
        
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
                    url: '/seller/create'
                });
            }
            $(document).on("click","#save",function() {
                var name=$('#name').val(),
                    lname=$('#lname').val(),
                    email=$('#email').val(),
                    psswd=$('#password').val(),
                    code=$('#code').val(),
                    profile=$('.profile:selected').val(),
                    arr={},
                    error=[];
                if(name!='' || lname!=''){
                    arr['name']=name+' '+lname;
                }else{
                    error.push('Agregar nombre');
                }
                if(email!=''){
                    arr['email']=email;
                }else{
                    error.push('Ingrese un correo');
                }
                if(psswd!=''){
                    arr['password']=psswd;
                }else{
                    error.push('Ingrese una contraseña');
                }
                if(code!=''){
                    arr['code']=code;
                }else{
                    error.push('Ingrese un codigo');
                }
                if(profile!=''){
                    arr['id_profile']=profile;
                }else{
                    error.push('Seleccionar un perfil');
                }
                if(error.length<=0){
                    arr['enable']=true;
                    arr['id_group_of_office']=0;
                    var create=save_user(arr);
                    create.done(function(r){
                        console.log(r);
                        if(Object.keys(r).length>0){
                            $('<div id="'+r.id+'" class="input-group"><label class="form-control" data-value="'+r.id+'"><span class="badge pull-right">0</span><tt class="overflow"><span class="label label-info">'+r.code+'</span> '+r.name+'</tt></label><span class="input-group-btn"><button class="btn btn-default btn-seller" type="button" value="'+r.id+'"><b>&gt;</b></button></span></div>').prependTo(".sellers-box").hide().fadeIn('slow');
                            $('#email').val('');
                            $('#password').val('');
                            $('#code').val('');
                            $('#name').val('');
                            $('#lname').val('');
                            $('#profiles').val($("#profiles option:first").val());
                            $('#'+r.id).css('background-color','#4fcece');
                            var n_sellers=$('.n_sellers').text();
                            $('.n_sellers').text(parseInt(n_sellers)+1);
                            var timer=setTimeout(function(){
                                clearInterval(timer);
                                $('#'+r.id).fadeTo('slow', 0.3, function(){
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
        
        $(document).on('click','.associate',function(){
            var id=$(this).attr('data-seller'),clients=get_all_clients(id);
            $(this).addClass('btn-success').html('Guardar').attr('id','associate').removeClass('associate');
            $('.clients').html('');
            clients.done(function(r){
                console.log(r);
                if(Object.keys(r).length>0){
                    $('.associate').show(100);
                    $('.well').hide();
                    for(var i in r){
                        var html="<li class='list-group-item client' data-value='"+r[i].id+"'><input type='checkbox' name='cli' class='checkbox_associate' value='"+r[i].id+"'></span><tt class=''><span class='label label-subcode'>"+r[i].subcode+"</span> "+r[i].name+"</tt></li>";
                        $('.clients').append(html);
                    }
                }else{
                    $('.associate').hide(100);
                    $('.well').show();
                }
            });
        });
        
        var associate_customers = function(arr,id){
            var formData = new FormData();
            formData.append("arr",JSON.stringify(arr));
            formData.append("id",id);
            return $.ajax({
                type: 'POST',
                dataType: 'json',
                processData: false,
                contentType:  false,
                data:formData,
                url: '/maintainers/sellers/associate/clients'
            });
        }
        $(document).on('click','#associate',function(){
            var clients_selected=[],
                id_customer=$(this).attr('data-seller');
            $(this).removeClass('btn-success').html('Asociar').removeAttr('id').addClass('associate');
            $('input[name="cli"]:checked').map(function(){
                    clients_selected.push($(this).val());
            });
            if(clients_selected.length>0){
                var associate=associate_customers(clients_selected,id_customer);
                associate.done(function(r){
                console.log(r);
                clients=get_clients(id_customer);
                $('.clients').html('');
                console.log(clients_selected);
                clients.done(function(r){
                    if(Object.keys(r).length>0){
                        $('.associate').show(100);
                        $('.well').hide();
                        for(var i in r){
                            var html="<li class='list-group-item client' data-value='"+r[i].id+"'><tt class=''><span class='label label-subcode'>"+r[i].subcode+"</span> "+r[i].name+"</tt><input type='checkbox' class='hidden checkbox_associate' value='"+r[i].id+"'></li>";
                            $('.clients').append(html);
                        }
                    }else{
                        $('.associate').hide(100);
                        $('.well').show();
                    }
                });
            });
            }
        });
    </script>
    @endsection
@endif