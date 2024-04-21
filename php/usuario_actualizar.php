<?php 
    require_once "../inc/session_start.php";
    require_once "main.php";

    $id=limpiar_cadena($_POST['usuario_id']);

    //verificar el usuario

    $check_usuario=conectar();
    $check_usuario = $check_usuario->query("SELECT * FROM usuarios WHERE usuario_id='$id'");

    if($check_usuario->rowCount()<=0){
        
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    EL usuario no existe
                </div';
            exit();

    }else{
        $datos = $check_usuario->fetch();
    }
    $check_usuario=null;

    $admin_usuario=limpiar_cadena($_POST['administrador_usuario']);
    $admin_clave=limpiar_cadena($_POST['administrador_clave']);

    #campos obligatorios

    if($admin_usuario=="" || $admin_clave==""){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios que corresponeden a su usuario y clave
                </div';
        exit();
    }   

    #verificamos la integridad de los datos

    if(verificar_datos("[a-zA-Z0-9]{4,20}",$admin_usuario)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario no cumple con el formato solicitado xd
                </div';
        exit();
    }
    if(verificar_datos("[a-zA-Z0-9]{4,20}",$admin_clave)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La clave no cumple con el formato solicitado
                </div';
        exit();
    }
    $check_admin=conectar();
    $check_admin = $check_admin->query("SELECT * FROM usuarios WHERE usuario_usuario='$admin_usuario' AND usuario_id='".$_SESSION['id']."'");

    if($check_admin->rowCount()==1){
        $check_admin=$check_admin->fetch();
        if($check_admin['usuario_usuario']!=$admin_usuario || !password_verify($admin_clave,$check_admin['usuario_clave'])){
            echo    '<div class="notification is-danger is-light">
            <strong>¡Ocurrio un error inesperado!</strong><br>
            El usuario o la clave no coinciden
        </div';
            exit();
        }
    }else{
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario o la clave no coinciden
                </div';
        exit();
    }
    $check_admin=null;

    #almacenamos los datos del formulario

    $nombre=limpiar_cadena($_POST['usuario_nombre']);
    $apellido=limpiar_cadena($_POST['usuario_apellido']);

    $usuario=limpiar_cadena($_POST['usuario_usuario']); 
    $email=limpiar_cadena($_POST['usuario_email']);

    $clave_1=limpiar_cadena($_POST['usuario_clave_1']);
    $clave_2=limpiar_cadena($_POST['usuario_clave_2']); 

    if($nombre=="" || $apellido=="" || $usuario==""){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div';
        exit();
    }    
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$nombre)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El nombre no cumple con el formato solicitado
                </div';
        exit();
    }
    if(verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}",$apellido)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El apellido no cumple con el formato solicitado
                </div';
        exit();
    }
    if(verificar_datos("[a-zA-Z0-9]{4,20}",$usuario)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario no cumple con el formato solicitado
                </div';
        exit();
    }

    
    if($email!="" && $email!=$datos['usuario_email']){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            $check_email=conectar();
            $check_email=$check_email->query("SELECT usuario_email FROM usuarios WHERE usuario_email='$email'");
            if($check_email->rowCount()>0){
                echo    '<div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            El email ya se encuentra registrado
                        </div';
                exit();
            }
            $check_email=null;
        }else{
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El email no cumple con el formato solicitado
                    </div';
            exit();
        }

    }
    if($usuario!=$datos['usuario_usuario']){

        $check_usuario=conectar();
        $check_usuario=$check_usuario->query("SELECT usuario_usuario FROM usuarios WHERE usuario_usuario='$usuario'");
        if($check_usuario->rowCount()>0){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El usuario ya se encuentra registrado
                    </div';
            exit();
        }
        $check_usuario=null;
    }

    if($clave_1!="" || $clave_2!=""){
        
        if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_2)){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        Las claves no cumplen con el formato solicitado
                    </div';
            exit();
        }else{
            if($clave_1!=$clave_2){
                echo    '<div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            Las claves no coinciden
                        </div';
                exit();
            }else{
                $clave=password_hash($clave_1,PASSWORD_BCRYPT,['cost'=>10]);
            }
        }
    
    }else{
        $clave=$datos['usuario_clave'];
    }

    $actualizar_usuario=conectar();
    $actualizar_usuario=$actualizar_usuario->prepare("UPDATE usuarios SET usuario_nombre=:nombre, usuario_apellido=:apellido,
     usuario_usuario=:usuario, usuario_clave=:clave, usuario_email=:email WHERE usuario_id=:id");

    $marcadores=[
        ':nombre'=>$nombre,
        ':apellido'=>$apellido,
        ':usuario'=>$usuario,
        ':clave'=>$clave,
        ':email'=>$email,
        ':id'=>$id
    ];
    
    if($actualizar_usuario->execute($marcadores)){
        echo    '<div class="notification is-info  is-light">
                    <strong>¡Registro exitoso!</strong><br>
                    El usuario se ha actualizado correctamente
                </div';
    }else{
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario no se ha actualizado, intentelo nuevamente
                </div';
    }
    $actualizar_usuario=null;