<?php
    $product_id_del=limpiar_cadena($_GET['product_id_del']);

        $check_product=conectar();
        $check_product=$check_product->query("SELECT * FROM productos WHERE producto_id='$product_id_del' ");

        if($check_product->rowCount()==1){
                $datos=$check_product->fetch();
                $eliminar_producto=conectar();
                $eliminar_producto=$eliminar_producto->prepare("DELETE FROM productos WHERE producto_id=:id");
                
                $eliminar_producto->execute([":id"=>$product_id_del]);

                if($eliminar_producto->rowCount()==1){
                        
                    if(is_file("./img/productos/".$datos['producto_foto'])){
                        chmod("./img/productos/".$datos['producto_foto'],0777);
                        unlink("./img/productos/".$datos['producto_foto']);
                    }

                     echo '
                        <div class="container is-fluid">
                            <div class="notification is-info is-light">
                                <strong>Los datos del producto han sido eliminados con exito</strong>
                        </div>';
                }else{
                        echo '
                        <div class="container is-fluid">
                            <div class="notification is-danger">
                                <strong>El producto no se pudo eliminar, intentelo nuevamente</strong>
                        </div>';
                }
                $eliminar_producto=null;
        }else{
            echo '
                 <div class="notification is-danger is-light">
                    <strong>El producto que intenta eliminar no existe</strong>
                </div>
                ';
        }
        $check_product=null;

?>