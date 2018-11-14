<?php 

 /*
 * 
 * Powered by CcMvc 
 * 
 */
namespace Cc\Mvc\TablaModel;

use Cc\Mvc\TablaModel;
 /*
 * Clase modelo para la tabla produccion_huevos
 *
 */
class  produccion_huevos extends TablaModel
{

    /**
    * Este metod sera llamado cuando se este por crear la tabla produccion_huevos
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
        $this->Colum('id_produccion')->INT(11)->PrimaryKey()->autoincrement()->NotNull();
        $this->Colum('cantidad')->INT(11)->NotNull();
        $this->Colum('fecha')->DATE()->NotNull();
        $this->Colum('detalles')->VARCHAR(300)->NotNull();
        $this->Colum('tipo')->ENUM('grande','pequeño')->NotNull();
        $this->Colum('id_galpon')->INT(11)->NotNull();
        // Claves foraneas de la tabla 
        $this->ForeingKey('id_galpon')->References('galpones','id_galpon');
    }

    /**
    * Este metod sera llamado cuando se inicialize la tabla produccion_huevos
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
