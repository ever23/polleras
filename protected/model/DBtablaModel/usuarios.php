<?php 

 /*
 * 
 * Powered by CcMvc 
 * 
 */
namespace Cc\Mvc\TablaModel;

use Cc\Mvc\TablaModel;
 /*
 * Clase modelo para la tabla usuarios
 *
 */
class  usuarios extends TablaModel
{

    /**
    * Este metod sera llamado cuando se este por crear la tabla usuarios
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
        $this->Colum('id_usuarios')->INT(11)->PrimaryKey()->autoincrement()->NotNull();
        $this->Colum('user')->VARCHAR(45)->NotNull();
        $this->Colum('hash')->VARCHAR(255)->NotNull();
        $this->Colum('permisos')->ENUM('root','admin')->NotNull();
        $this->Colum('nombres')->VARCHAR(100)->NotNull();
        $this->Colum('apellidos')->VARCHAR(100)->NotNull();
    }

    /**
    * Este metod sera llamado cuando se inicialize la tabla usuarios
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
        $this->Insert(1,"root","\$2y\$09\$nl78FKbO9sEZgHs6RO1PNuuiNCuuQH/PMVpiyopTFFd4zZJGlf0yy","root","root","root");
        $this->Insert(2,"admin","\$2y\$09\$x8NqZ3QEUbMDqOsnLRhFKOxkwm0XwcL1pnVszaKihcPPEKvjqP2be","admin","admin","admin");
    }
}
