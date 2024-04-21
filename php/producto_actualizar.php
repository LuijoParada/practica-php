<?php 

    require_once "main.php";

    $id=limpiar_cadena($_POST['producto_id']);

    //verificar la categoria

    $check_producto=conectar();
    $check_producto = $check_producto->query("SELECT * FROM productos WHERE producto_id='$id'");

    if($check_producto->rowCount()<=0){
        echo    '<div class="notification is-danger is-light">
                    <strong>¡Ocurrio un error inesperado!</strong><br>
                    El producto no existe
                </div';
        exit();
    }else{
        $datos = $check_producto->fetch();
    }
    $check_producto=null;

    #limpiamos y almacenamos los datos del formulario
      $codigo=limpiar_cadena($_POST['producto_codigo']);
      $nombre=limpiar_cadena($_POST['producto_nombre']);
  
      $precio=limpiar_cadena($_POST['producto_precio']);
      $stock=limpiar_cadena($_POST['producto_stock']);
      $categoria=limpiar_cadena($_POST['producto_categoria']);
  
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

    if($datos['producto_codigo']!=$codigo){
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

    }
    
    if($datos['producto_nombre']!=$nombre){
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

    }

    if($datos['categoria_id']!=$categoria){
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

    }   
     #actualizar datos
     $actualizar_producto=conectar();
     $actualizar_producto=$actualizar_producto->prepare("UPDATE productos SET 
     producto_codigo=:codigo, producto_nombre=:nombre, producto_precio=:precio, producto_stock=:stock, categoria_id=:categoria WHERE producto_id=:id");
 
     $marcadores=[
         'codigo'=>$codigo,
         ':nombre'=>$nombre,
         ':precio'=>$precio,
         ':stock'=>$stock,
         ':categoria'=>$categoria,
         ':id'=>$id
     ];
     
     if($actualizar_producto->execute($marcadores)){
         echo    '<div class="notification is-info  is-light">
                     <strong>¡Registro exitoso!</strong><br>
                     El producto se ha actualizado correctamente
                 </div';
     }else{
         echo    '<div class="notification is-danger is-light">
                     <strong>¡Ocurrio un error inesperado!</strong><br>
                        El producto no se ha actualizado, intentelo nuevamente
                 </div';
     }
     $actualizar_producto=null;