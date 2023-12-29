<?php
    #almacenado datos
    $usuario=limpiar_cadena($_POST['login_usuario']);
    $clave=limpiar_cadena($_POST['login_clave']);
    
    #verificamos campos obligatorios
    if($usuario=="" || $clave==""){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div';
        exit();
    }   
    #verificamos integridad de los datos
    if(verificar_datos("[a-zA-Z0-9]{4,20}",$usuario)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario no cumple con el formato solicitado
                </div';
        exit();
    }
    if(verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    Las claves no cumplen con el formato solicitado
                </div';
        exit();
    }
    #verificamos que el usuario exista
    $check_user=conectar();
    $check_user=$check_user->query("SELECT * FROM usuarios WHERE
     usuario_usuario ='$usuario'");

    if($check_user->rowCount()==1){
        $check_user=$check_user->fetch();
        
        if($check_user['usuario_usuario'] == $usuario 
        && password_verify($clave,$check_user['usuario_clave'])){
            
            $_SESSION['id'] = $check_user['usuario_id'];
            $_SESSION['nombre'] = $check_user['usuario_nombre'];
            $_SESSION['apellido'] = $check_user['usuario_apellido'];
            $_SESSION['usuario'] = $check_user['usuario_usuario'];
            
            
            if(headers_sent()){
                echo "<script>window.location.href='./index.php?vista=home';</script>";
            }else{
                header("Location: ./index.php?vista=home");
            }

        }else{
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El usuario o la clave no existe. 
                    </div';
            exit();
        }
    }else{
        echo '
        <div class="notification is-danger is-light">
        <strong>¡Ocurrio un error inesperado!</strong><br>
        El usuario o la clave no existe. 
        </div
        ';
        exit();
    }
    $check_user=null;