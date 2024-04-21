<?php
    $user_id_del=limpiar_cadena($_GET['user_id_del']);

    //verificamos que el usuario exista
    $check_usuario=conectar();
    $check_usuario=$check_usuario->query("SELECT usuario_id FROM usuarios WHERE usuario_id='$user_id_del' ");
    
    if($check_usuario->rowCount()==1){
        
            //verificamos que el usuario haya registrado productos

            $check_productos=conectar();
            $check_productos=$check_productos->query("SELECT usuario_id FROM productos WHERE usuario_id='$user_id_del' LIMIT 1");
    
            if($check_productos->rowCount()<=0){
                
                $eliminar_usuario=conectar();
                $eliminar_usuario=$eliminar_usuario->prepare("DELETE FROM usuarios WHERE usuario_id=:id");
                
                $eliminar_usuario->execute([":id"=>$user_id_del]);

                if($eliminar_usuario->rowCount()==1){
                    echo '
                    <div class="container is-fluid">
                        <div class="notification is-info is-light">
                            <strong>Los datos del usuario han sido eliminados con exito</strong>
                    </div>';
                }else{
                    echo '
                    <div class="container is-fluid">
                        <div class="notification is-danger">
                            <strong>El usuario no se pudo eliminar, intentelo nuevamente</strong>
                    </div>';
                }
            }else{
                echo '
        
                <div class="container is-fluid">
                    <div class="notification is-danger">
                        <strong>El usuario no se puede eliminar ya que tiene productos registrados</strong>
                </div>';
            }
    }else{

        echo '
        <div class="container is-fluid">
            <div class="notification is-danger">
                <strong>El usuario que intenta eliminar no existe</strong>
        </div>';
    }
    $check_usuario=null;