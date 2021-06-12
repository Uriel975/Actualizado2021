<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class GrupoAlumno extends Model
{
    protected $table = 'grupo_alumos';
    protected $primaryKey = 'id';
    protected $fillable = ['id','ciclo_escolar_id','grupo_id','grado_id','alumno_id'];
}
