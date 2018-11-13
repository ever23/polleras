<?php 

 /*
 * 
 * Powered by CcMvc 
 * 
 */
namespace Cc\Mvc\TablaModel;

use Cc\Mvc\TablaModel;
 /*
 * Clase modelo para la tabla notificaciones
 *
 */
class  notificaciones extends TablaModel
{

    /**
    * Este metod sera llamado cuando se este por crear la tabla notificaciones
    * Ejemplo del codigo 
    * <code>
    * <?php //Ejemplo del codigo 
    * $this->Colum('mi_campo')->PrimaryKey(); // UN CAMPO PARA LA TABLA
    * $this->Colum('mi_otro_campo')->VARCHAR(50); // OTRO CAMPO PARA LA TABLA
    * $this->ForeingKey('mi_campo')->References('mi_otra_tabla')->OnUpdate('CASCADE'); // UNA CLAVE FORANEA  
    * ?>
    * </code>
    */
    public function Create()
    {
        // Columnas de la tabla 
        $this->Colum('id_notificacion')->INT(11)->PrimaryKey()->autoincrement()->NotNull();
        $this->Colum('desc_notificacion')->VARCHAR(250);
        $this->Colum('href_notificacion')->VARCHAR(250);
        $this->Colum('icon_notification')->VARCHAR(45);
        $this->Colum('fech_notificacion')->DATETIME();
        $this->Colum('tipo_notificacion')->ENUM('success','info','warning','danger');
        $this->Colum('visto')->TINYINT(1)->DefaultValue('0');
        $this->Colum('cod_tinger')->VARCHAR(45);
        $this->Colum('id_usuarios')->INT(11)->NotNull();
        // Claves foraneas de la tabla 
        $this->ForeingKey('id_usuarios')->References('usuarios','id_usuarios');
    }

    /**
    * Este metod sera llamado cuando se inicialize la tabla notificaciones
    * Ejemplo del codigo 
    * <code>
    * <?php //Ejemplo del codigo
    * $this->Insert('hola1','hola2');//insertando usando el formato de parametros
    * $this->Insert(['hola1','hola2']);//insertando usando el formato arrays simples
    * $this->Insert(['campo1'=>'hola1','campo2'=>'hola2']);//insertando usando el formato arrays asociativos o diccionario
    * ?>
    * </code>
    */
    public function Initialized()
    {
        // tu condigo aqui
    }
}
