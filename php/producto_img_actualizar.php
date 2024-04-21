<?php

require_once "main.php";

$product_id=limpiar_cadena($_POST['img_up_id']);

//verificar 

$check_producto=conectar();
$check_producto = $check_producto->query("SELECT * FROM productos WHERE producto_id='$product_id'");

if($check_producto->rowCount()==1){
    $datos = $check_producto->fetch();
}else{
    echo    '<div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                La imagen del producto no existe
            </div>';
    exit();
    
}
$check_producto=null;


$img_dir="../img/productos/";

#comprobar que se seleciona una imagen
if($_FILES['producto_foto']['name']=="" && $_FILES['producto_foto']['size']==0){
    echo    '<div class="notification is-danger is-light">
                <strong>¡Ocurrio un error inesperado!</strong><br>
                No se selecciono ninguna imagen
            </div>';
    exit();
    if(!file_exists($img_dir)){
        if(!mkdir($img_dir,0777)){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        No se pudo crear el directorio de imagenes
                    </div>';
            exit();  
        }

    }
}
    chmod($img_dir,0777);

    #veridicamos si la imagen es valida
    if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && 
        mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png" ){
            echo    '<div class="notification is-danger is-light">
                        <strong>¡Ocurrio un error inesperado!</strong><br>
                        El formato de la imagen no es valido
                    </div';
            exit();
    }
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
        $img_nombre=renombrar_fotos($datos['producto_nombre']);
        $foto=$img_nombre.$img_ext;

    if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    No se pudo subir la imagen
                </div';
        exit();
    }

    #borramos la imagen anterior si....
    if(is_file($img_dir.$datos['producto_foto']) && $datos['producto_foto']!=$foto){
        chmod($img_dir.$datos['producto_foto'],0777);
        unlink($img_dir.$datos['producto_foto']);
    }


    #actualizar datos
    $actualizar_producto=conectar();
    $actualizar_producto=$actualizar_producto->prepare("UPDATE productos SET producto_foto=:foto WHERE producto_id=:id");
    $marcadores=[
        ':foto'=>$foto,
        ':id'=>$product_id
        ];
    if($actualizar_producto->execute($marcadores)){
        echo    '<div class="notification is-info  is-light">
                    <strong>¡Imagen actualizada!</strong><br>
                    La imagen se ha actualizado correctamente, 
                    pulse Aceptar para recargar los cambios
                    <p class"has-text-centered pt-5 pb-5">
                        <a href="index.php?vista=product_img&product_id_up='.$product_id.'" class="button is-link is-rounded">Aceptar</a>
                    </p>
                </div';
    }else{
        if(is_file($img_dir.$foto)){
            chmod($img_dir.$foto,0777);
            unlink($img_dir.$foto);
        }
        echo    '<div class="notification is-warning is-light">
                    <strong>¡Ha ocurrido un error!</strong><br>
                    No se pudo actualizar la imagen en este momento
                </div';
    }
    $actualizar_usuario=null;