@extends('layouts.app')
@if(Auth::check())
    @section('content')
    <?php
        $obj=new CheckUserPermission();
        $uri=$_SERVER['REQUEST_URI'];
        $permissions=$obj->checkPerission($uri);

        function calculate($new_records,$duplicated,$errores){
            if($new_records<=0 && $duplicated<=0 && $errores<=0)
                return 0;
            else{
                $total=$new_records+$duplicated+$errores;
                $total_new=($new_records*100)/$total;
                $total_duplicated=($duplicated*100)/$total;
                $total_error=($errores*100)/$total;
                return array('new'=>$total_new,'duplicated'=>$total_duplicated,'errors'=>$total_error);
            }

        }
    ?>
    <div class="loading" style="display:none;">
        <div>
            <div class="c1"></div>
            <div class="c2"></div>
            <div class="c3"></div>
            <div class="c4"></div>
        </div>
        <span>Cargando</span>
    </div>

        <div class="navbar-company">
            <nav class="navbar" role="navigation">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <?php if(isset($permissions['insert']) && !empty($permissions['insert'])){ ?>
                        <span class="navbar-brand">Cargar Documento</span>
                    <?php } ?>
                </div>
                <div class="collapse navbar-collapse" id="bs-navbar-collapse">
                    <?php if(isset($permissions['insert']) && !empty($permissions['insert'])){ ?>
                        <div class="navbar-form">
                           <!-- <div class="form-group">
                                <input type="file" name="file" id="file" accept=".xls,.xlsx,.csv" >
                            </div>-->
                            <div class="file-drop-area">
                                <span class="fake-btn">Selecciona archivo</span>
                                <input class="file-input" name="upload_file" id="upload_file" accept=".xls,.xlsx,.csv" type="file" multiple>
                                <span class="file-msg js-set-number">O arrastralo a esta area</span>
                            </div>

                            <button type="submit" id="upload_btn" class="btn btn-info btn-flat" disabled="disabled"><span class="glyphicon glyphicon-cloud-upload"></span></button>
                        </div>
                    <?php } ?>

                </div>
            </nav>
        </div>

        <table id="table" class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th width="30%" data-align="left"><span class="glyphicon glyphicon-file"></span> Archivo</th>
                    <th width="11%"><small>Oficinas</small></th>
                    <th width="11%"><small>Transportistas</small></th>
                    <th width="11%"><small>Veh&iacute;culos</small></th>
                    <th width="11%"><small>Clientes</small></th>
                    <th width="11%"><small>Documentos</small></th>
                    <th width="11%"><span class="glyphicon glyphicon-user"></span> Usuario</th>
                </tr>
            </thead>
            <tbody>
                <?php $count=1;
                    foreach($documents as $key=>$val){
                ?>
                    <tr>
                        <td><div class="jumbotron"><?php echo $count; ?></div></td>
                        <td><div class="jumbotron"><a href="/docs/<?php echo $val['name']; ?>" download><?php echo $val['name']; ?></a></div></td>
                        <td>
                            <?php
                                $percents=calculate($val['office_new'],$val['office_duplicated'],$val['office_error']);
                            ?>
                            <div class="progress progress-dashboard">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $percents['new'];?>%" title="<?php echo $val['office_new']; ?> nuevo(s)"></div>
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $percents['duplicated'];?>%" title="<?php echo $val['office_duplicated']; ?> repetido(s)"></div>
                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $percents['errors'];?>%" title="<?php echo $val['office_error']; ?> con error"></div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $percents=calculate($val['employee_new'],$val['employee_duplicated'],$val['employee_error']);
                            ?>
                            <div class="progress progress-dashboard">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $percents['new'];?>%" title="<?php echo $val['employee_new']; ?> nuevo(s)"></div>
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $percents['duplicated'];?>%" title="<?php echo $val['employee_duplicated']; ?> repetido(s)"></div>
                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $percents['errors'];?>%" title="<?php echo $val['employee_error']; ?> con error"></div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $percents=calculate($val['vehicle_new'],$val['vehicle_duplicated'],$val['vehicle_error']);
                            ?>
                            <div class="progress progress-dashboard">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $percents['new'];?>%" title="<?php echo $val['vehicle_new']; ?> nuevo(s)"></div>
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $percents['duplicated'];?>%" title="<?php echo $val['vehicle_duplicated']; ?> repetido(s)"></div>
                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $percents['errors'];?>%" title="<?php echo $val['vehicle_error']; ?> con error"></div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $percents=calculate($val['customer_new'],$val['customer_duplicated'],$val['customer_error']);
                            ?>
                            <div class="progress progress-dashboard">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $percents['new'];?>%" title="<?php echo $val['customer_new']; ?> nuevo(s)"></div>
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $percents['duplicated'];?>%" title="<?php echo $val['customer_duplicated']; ?> repetido(s)"></div>
                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $percents['errors'];?>%" title="<?php echo $val['customer_error']; ?> con error"></div>
                            </div>
                        </td>
                        <td>
                            <?php
                                $percents=calculate($val['document_new'],$val['document_duplicated'],$val['document_error']);
                            ?>
                            <div class="progress progress-dashboard">
                                <div class="progress-bar progress-bar-success" style="width: <?php echo $percents['new'];?>%" title="<?php echo $val['document_new']; ?> nuevo(s)"></div>
                                <div class="progress-bar progress-bar-warning" style="width: <?php echo $percents['duplicated'];?>%" title="<?php echo $val['document_duplicated']; ?> repetido(s)"></div>
                                <div class="progress-bar progress-bar-danger" style="width: <?php echo $percents['errors'];?>%" title="<?php echo $val['document_error']; ?> con error"></div>
                            </div>
                        </td>
                        <td><?php echo $val['email'][0]; ?><br><span class="badge"><?php echo $val['created']; ?></span></td>
                    </tr>
                <?php $count++; } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><small>Oficinas</small></th>
                    <th><small>Transportistas</small></th>
                    <th><small>Veh&iacute;culos</small></th>
                    <th><small>Clientes</small></th>
                    <th><small>Documentos</small></th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
        <div id="snackbar"></div>
        <?php if(isset($permissions['insert']) && !empty($permissions['insert'])){ ?>
        <script>
            var upload_file=function(input_file,format){
                var formData = new FormData();
                formData.append("upload_file",input_file);
               // formData.append("format",format);
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType:  false,
                    data: formData,
                    cache:false,
                    url: '/file/upload'
                });
            }

            $(document).on('change','#upload_file',function(){
                var file_name=$(this).prop('files')[0].name;
                if($(this).val()!=''){
                    $('#upload_btn').removeAttr('disabled');
                    $(this).next().text(file_name);
                }
                else
                    $('#upload_btn').attr('disabled','disabled');
            });
            $(document).on('click','#upload_btn',function(){
              var upload,
                  file=$('#upload_file').prop('files')[0];
                if(file){
                    $('.loading').css('display','block');
                    upload=upload_file(file,'csv');
                    upload.done(function(r){
                        if(r.errors.length>0){
                            var errors=r.errors;
                            var x = document.getElementById("snackbar");
                            x.className = "show";
                            for(var i in errors){
                                $('#snackbar').append('- '+errors[i]+'<br>');
                            }
                            setTimeout(function(){ x.className = x.className.replace("show", ""); }, 5000);
                        }else
                            location.reload();

                        $('#upload_file').next().text('O arrastralo a esta area');
                        $('#upload_file').val('');
                    });
                }else
                    console.log('File cannot be empty');
            });
        </script>
    <?php } ?>
    @endsection
@endif
