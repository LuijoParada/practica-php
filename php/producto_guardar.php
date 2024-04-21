<?php 
    require_once "../inc/session_start.php";
    require_once "main.php";

   
    #almacenamos los datos del formulario
    $codigo=limpiar_cadena($_POST['producto_codigo']);
    $nombre=limpiar_cadena($_POST['producto_nombre']);

    $precio=limpiar_cadena($_POST['producto_precio']);
    $stock=limpiar_cadena($_POST['producto_stock']);
    $categoria=limpiar_cadena($_POST['producto_categoria']);

    #campos obligatorios

    if($codigo=="" || $nombre=="" || $precio=="" || $stock=="" || $categoria==""){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No has llenado todos los campos que son obligatorios
                </div';
        exit();
    }    

    
    if(verificar_datos("[a-zA-Z0-9- ]{1,70}",$codigo)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El codigo no cumple con el formato solicitado
                </div';
        exit();
    }
    if(verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}",$nombre)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El nombre no cumple con el formato solicitado
                </div';
        exit();
        
    }
    if(verificar_datos("[0-9.]{1,25}",$precio)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El precio no cumple con el formato solicitado
                </div';
        exit();
        
    }
    if(verificar_datos("[0-9]{1,25}",$stock)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El stock no cumple con el formato solicitado
                </div';
        exit(); 
    }

    $check_codigo=conectar();
    $check_codigo=$check_codigo->query("SELECT producto_codigo FROM productos WHERE producto_codigo='$codigo'");
    if($check_codigo->rowCount()>0){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El codigo ya se encuentra registrado
                </div';
        exit();
    }
    $check_codigo=null;

    $check_nombre=conectar();
    $check_nombre=$check_nombre->query("SELECT producto_nombre FROM productos WHERE producto_nombre='$nombre'");
    if($check_nombre->rowCount()>0){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El nombre ya se encuentra registrado
                </div';
        exit();
    }
    $check_nombre=null;
    
    $check_categoria=conectar();
    $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
    if($check_categoria->rowCount()<=0){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    La categoria no existe
                </div';
        exit();
    }
    $check_categoria=null;
    

    #directorio de imagenes

    $img_dir="../img/productos/";

    #verificamos si la imagen fue subida

    if($_FILES['producto_foto']['name']!="" && $_FILES['producto_foto']['size']>0){
        #creando el directorio
        if(!file_exists($img_dir)){
            if(!mkdir($img_dir,0777)){
                echo    '<div class="notification is-danger is-light">
                            <strong>¡Ocurrio un error inesperado!</strong><br>
                            No se pudo crear el directorio de imagenes
                        </div>';
                exit();  
            }

        }
        #veridicamos si la imagen es valida
        if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && 
        mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png" ){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El formato de la imagen no es valido
                    </div';
            exit();
        }
        # verificamos el peso de la imagen
        if($_FILES['producto_foto']['size']/1024>3072){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        La imagen es muy pesada
                    </div';
            exit();
        }
        #extension de la imagen
        switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
            case "image/jpeg":
                $img_ext=".jpg";
                break;
            case "image/png":
                $img_ext=".png";
                break;
            }
        chmod($img_dir,0777);
        $img_nombre=renombrar_fotos($nombre);
        $foto=$img_nombre.$img_ext;

        #movemos la imagen al directorio

        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        No se pudo subir la imagen
                    </div';
            exit();
        }

    }else{
        $foto="";
    }

    #guardamos los datos en la base de datos

    $guardar_producto=conectar();
    $guardar_producto=$guardar_producto->prepare("INSERT INTO productos(producto_codigo,producto_nombre,producto_precio,producto_stock,producto_foto,categoria_id,usuario_id)
    VALUES(:codigo,:nombre,:precio,:stock,:foto,:categoria,:usuario)");

    $marcadores=[
        ':codigo'=>$codigo,
        ':nombre'=>$nombre,
        ':precio'=>$precio,
        ':stock'=>$stock,
        ':foto'=>$foto,
        ':categoria'=>$categoria,
        ':usuario'=>$_SESSION['id']
    ];

    $guardar_producto->execute($marcadores);

    if($guardar_producto->rowCount()==1){


        echo    '<div class="notification is-success is-light">
                    <strong>¡Registro exitoso!</strong><br>
                    El producto se guardo correctamente
                </div';
        exit();
    
     }else{
        if(is_file($img_dir.$foto)){
            chmod($img_dir.$foto,0777);
            unlink($img_dir.$foto);
        }
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo guardar el producto, por favor intente nuevamente.
                </div';
        exit();
     }
     $guardar_producto=null;