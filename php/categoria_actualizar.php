<?php
    require_once "main.php";
    
    $id=limpiar_cadena($_POST['categoria_id']);

    //verificar la categoria

    $check_categoria=conectar();
    $check_categoria = $check_categoria->query("SELECT * FROM categoria WHERE categoria_id='$id'");

    if($check_categoria->rowCount()<=0){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoria no existe
                </div';
        exit();
    }else{
        $datos = $check_categoria->fetch();
    }
    $check_categoria=null;

    #almacenamos los datos del formulario

    $nombre=limpiar_cadena($_POST['categoria_nombre']);
    $ubicacion=limpiar_cadena($_POST['categoria_ubicacion']);

    if($nombre==""){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div';
        exit();
    }    

    
    #verificamos la integridad de los datos

    if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}",$nombre)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El nombre no cumple con el formato solicitado
                </div';
        exit();
    }
    if($ubicacion!=""){
        if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}",$ubicacion)){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La ubicación no cumple con el formato solicitado
                    </div';
            exit();
        }
    }

        #verificamos si cambiamos el nombre para ver si ya existe otra categoria con el nombre que estamos tratando ingresar
    if($nombre!=$datos['categoria_nombre']){
        $check_nombre=conectar();
        $check_nombre=$check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
        if($check_nombre->rowCount()>0){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El nombre ya se encuentra registrado
                    </div';
            exit();
        }
        $check_nombre=null;
    }
    #actualizar datos
    $actualizar_categoria=conectar();
    $actualizar_categoria=$actualizar_categoria->prepare("UPDATE categoria SET 
    categoria_nombre=:nombre, categoria_ubicacion=:ubicacion WHERE categoria_id=:id");

    $marcadores=[
        ':nombre'=>$nombre,
        ':ubicacion'=>$ubicacion,
        ':id'=>$id
    ];
    
    if($actualizar_categoria->execute($marcadores)){
        echo    '<div class="notification is-info  is-light">
                    <strong>¡Registro exitoso!</strong><br>
                    La categoria se ha actualizado correctamente
                </div';
    }else{
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoria no se ha actualizado, intentelo nuevamente
                </div';
    }
    $actualizar_categoria=null;