<?php
    $category_id_del=limpiar_cadena($_GET['category_id_del']);

        //verificamos que la categoria exista
        $check_categoria=conectar();
        $check_categoria=$check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$category_id_del' ");
        if($check_categoria->rowCount()==1){
            //verificamos productos
            $check_productos=conectar();
            $check_productos=$check_productos->query("SELECT categoria_id FROM productos WHERE categoria_id='$category_id_del' LIMIT 1");
            
            if($check_productos->rowCount()<=0){
                $eliminar_categoria=conectar();
                $eliminar_categoria=$eliminar_categoria->prepare("DELETE FROM categoria WHERE categoria_id=:id");
                
                $eliminar_categoria->execute([":id"=>$category_id_del]);

                if($eliminar_categoria->rowCount()==1){
                    echo '
                    <div class="container is-fluid">
                        <div class="notification is-info is-light">
                            <strong>Los datos de la categoria han sido eliminados con exito</strong>
                    </div>';
                }else{
                    echo '
                    <div class="container is-fluid">
                        <div class="notification is-danger">
                            <strong>La categoria no se pudo eliminar, intentelo nuevamente</strong>
                    </div>';
                }


            }else{
                echo '
                    <div class="notification is-danger is-light">
                        <strong>La categoria que intenta eliminar tiene productos registrados</strong>
                    </div>
                    ';
            }
        }else{
            echo '
                 <div class="notification is-danger is-light">
                    <strong>La categoria que intenta eliminar no existe</strong>
                </div>
                ';
        } 
        $check_categoria=null;