<?php
    require_once "main.php";
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
    #verificamos el nombre

    $check_nombre=conectar();
    $check_nombre=$check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
    if($check_nombre->rowCount()>0){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El usuario ya se encuentra registrado
                </div';
        exit();
    }
    $check_nombre=null;
   
    #Guardamos los datos

    $guardar_categoria=conectar();
    $guardar_categoria=$guardar_categoria->prepare("INSERT INTO categoria(categoria_nombre,categoria_ubicacion)
    VALUES(:nombre,:ubicacion)");

    $marcadores=[
        ':nombre'=>$nombre,
        ':ubicacion'=>$ubicacion,

    ];
    $guardar_categoria->execute($marcadores);

    if($guardar_categoria->rowCount()==1){
        echo    '<div class="notification is-info is-light">
                    <strong>¡Perfecto!</strong><br>
                    La categoría se ha guardado correctamente
                </div';
    }else{ 
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoría no se ha guardado
                </div';
    }
    $guardar_categoria=null;
