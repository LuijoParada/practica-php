<?php
   require_once "main.php";
   
#almacenamos los datos del formulario

    $nombre=limpiar_cadena($_POST['usuario_nombre']);
    $apellido=limpiar_cadena($_POST['usuario_apellido']);

    $usuario=limpiar_cadena($_POST['usuario_usuario']); 
    $email=limpiar_cadena($_POST['usuario_email']);

    $clave_1=limpiar_cadena($_POST['usuario_clave_1']);
    $clave_2=limpiar_cadena($_POST['usuario_clave_2']);

#campos obligatorios

    if($nombre=="" || $apellido=="" || $usuario=="" || $clave_1=="" || $clave_2==""){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div';
        exit();
    }    


#verificamos la integridad de los datos

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
    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_1) || verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave_2)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Las claves no cumplen con el formato solicitado
                </div';
        exit();
    }


#verificamos el email


    if($email!=""){
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


#verificamos el usuario


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


#verificamos las claves
    
    if($clave_1!=$clave_2){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Las claves no coinciden
                </div';
        exit();
    }else{
        $clave=password_hash($clave_1,PASSWORD_BCRYPT,['cost'=>10]);
    }


#Guardamos los datos

$guardar_usuario=conectar();
$guardar_usuario=$guardar_usuario->prepare("INSERT INTO usuarios(usuario_nombre,usuario_apellido,usuario_usuario,usuario_email,usuario_clave)
 VALUES(:nombre,:apellido,:usuario,:email,:clave)");

 $marcadores=[
    ':nombre'=>$nombre,
    ':apellido'=>$apellido,
    ':usuario'=>$usuario,
    ':email'=>$email,
    ':clave'=>$clave
 ];

 $guardar_usuario->execute($marcadores);

 if($guardar_usuario->rowCount()==1){
    echo    '<div class="notification is-success is-light">
                <strong>¡Registro exitoso!</strong><br>
                El usuario se guardo correctamente
            </div';
    exit();

 }else{
    echo    '<div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se pudo guardar el usuario, por favor intente nuevamente.
            </div';
    exit();
 }
 $guardar_usuario=null;