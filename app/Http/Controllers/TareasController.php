<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\AlumnoTareas;
use App\Models\Asignaturas;
use App\Models\CicloEscolar;
use App\Models\Clases;
use App\Models\Grados;
use App\Models\GrupoAlumno;
use App\Models\Grupos;
use App\Models\ListaDoc;
use App\Models\Periodos;
use App\Models\Personal;
use App\Models\Tareas;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TareasController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $maestro_id = Personal::where('usuario_id', Auth::user()->id)->first()->id;
        $clases = Clases::where('maestro_id', $maestro_id)->get();
        $periodos = Periodos::all();
        $grupos = clases::select('grupo_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $grados = clases::select('grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $filtrado = $this->super_unique($clases, 'asignatura_id');
        $tareas = Tareas::where('maestro_id', $maestro_id)->get();
        $tipo = Tareas::select('tipo')->where('maestro_id', $maestro_id)->distinct()->orderBy('tipo', 'asc')->get();

        return view('tareas.Index', compact('periodos', 'grupos', 'filtrado', 'grados', 'tareas', 'tipo'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function filtro(Request $request)
    {
        $maestro_id = Personal::where('usuario_id', Auth::user()->id)->first()->id;
        $clases = Clases::where('maestro_id', $maestro_id)->get();
        $periodos = Periodos::all();
        $grupos = clases::select('grupo_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $grados = clases::select('grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $filtrado = $this->super_unique($clases, 'asignatura_id');
        $tipo = Tareas::select('tipo')->where('maestro_id', $maestro_id)->distinct()->orderBy('tipo', 'asc')->get();
        $tareas = Tareas::where('maestro_id', $maestro_id)
            ->where('materia_id', $request->asignatura)
            ->where('grado_id', $request->grado)
            ->where('grupo_id', $request->grupo)
            ->where('periodo_id', $request->periodo)
            ->where('tipo', $request->tipo)
            ->get();
        return view('tareas.Index', compact('periodos', 'grupos', 'grados', 'filtrado', 'tareas', 'tipo'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $maestro_id = Personal::where('usuario_id', Auth::user()->id)->first()->id;
        $clases = Clases::where('maestro_id', $maestro_id)->get();
        $periodos = Periodos::all();
        $grupos = clases::select('grupo_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $grados = clases::select('grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $filtrado = $this->super_unique($clases, 'asignatura_id');
        $ciclo = CicloEscolar::orderBy('fecha_inicio', 'asc')->first();
        return view('tareas.Create', compact('periodos', 'ciclo', 'grupos', 'grados', 'filtrado',));
    }

    public function getMax($asignatura, $grado, $grupo, $periodo, $tipo)
    {
        $valor_max = Tareas::select('valor')->where('materia_id', $asignatura)->where('grado_id', $grado)->where("grupo_id", $grupo)->where('periodo_id', $periodo)->where('tipo', $tipo)->get()->sum('valor');
        return response()->json($valor_max,200);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $tarea = new Tareas;
        $tarea->nombre = $request->nombre;
        $tarea->descripcion = $request->descripcion;
        $tarea->valor = $request->valor;
        $tarea->periodo_id = $request->periodo;
        $tarea->grado_id = $request->grado;
        $tarea->grupo_id = $request->grupo;
        $tarea->ciclo_escolar_id = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;
        $tarea->materia_id = $request->materia;
        $maestro_id = Personal::where('usuario_id', Auth::user()->id)->first()->id;
        $tarea->tipo = $request->tipo;
        $tarea->isCaptured = false;
        $tarea->maestro_id = $maestro_id;
        $tarea->save();
        return $this->index();
    }

    /**
     * @param $tarea
     * @return Application|Factory|View
     */
    public function viewUpdateTask($tarea)
    {
        $maestro_id = Personal::where('usuario_id', Auth::user()->id)->first()->id;
        $clases = Clases::where('maestro_id', $maestro_id)->get();
        $periodos = Periodos::all();
        $grupo = clases::select('grupo_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $grados = clases::select('grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
//        dd($grados,$grupo);
        $filtrado = $this->super_unique($clases, 'asignatura_id');
        $ciclo = CicloEscolar::orderBy('fecha_inicio', 'asc')->first();
        $tareas = Tareas::find($tarea);
        return view('tareas.update', compact('tareas', 'periodos', 'filtrado', 'grados', 'grupo', 'ciclo'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function updateTask(Request $request)
    {
//       dd($request->all());
        $tarea = Tareas::find($request->id);
        $tarea->nombre = $request->nombre;
        $tarea->descripcion = $request->descripcion;
        $tarea->valor = $request->valor;
        $tarea->tipo = $request->tipo;
        $tarea->save();
        return $this->index();
    }

    public function destroy($tarea)
    {
        try {
            if (Tareas::find($tarea)->isCaptured) {
                AlumnoTareas::where('tarea_id', $tarea)->delete();
                Tareas::destroy($tarea);
            } else {
                Tareas::destroy($tarea);
            }
            return $this->index();
        } catch (Exception $exception) {
            dd("Fail");
        }
    }

    /**
     * @param $tarea
     * @param $grado
     * @param $grupo
     * @param $periodo
     * @return Application|Factory|View
     */
    public function alumnoTarea($tarea, $grado, $grupo, $periodo)
    {
        $ciclo = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;
        $grupoAlumno = GrupoAlumno::where('grado_id', $grado)->where('grupo_id', $grupo)->where('ciclo_escolar_id', $ciclo)->get();
        $alumnos = [];
        foreach ($grupoAlumno as $item) {
            array_push($alumnos, Alumno::where('id', $item->alumno_id)->first());
        }
        sort($alumnos);
        return view('tareas.Alumnos', compact('alumnos', 'tarea', 'grado', 'grupo', 'periodo', 'ciclo'));
    }

    /**
     * @param $tarea
     * @param $grado
     * @param $grupo
     * @param $periodo
     * @return Application|Factory|View
     */
    public function detailsTarea($tarea, $grado, $grupo, $periodo)
    {
        $ciclo = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;
        $grupoAlumno = GrupoAlumno::where('grado_id', $grado)->where('grupo_id', $grupo)->where('ciclo_escolar_id', $ciclo)->get();
        $alumnos = [];
        foreach ($grupoAlumno as $item) {
            array_push($alumnos, Alumno::where('id', $item->alumno_id)->first());
        }
        sort($alumnos);
        return view('tareas.Details', compact('alumnos', 'tarea', 'grado', 'grupo', 'periodo', 'ciclo'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function storeAlumno(Request $request)
    {
        $tareaMaestro = Tareas::find($request->tarea);

        foreach ($request->Alumnos['calif'] as $key => $calif) {
            $alumnoTarea = new AlumnoTareas;
            $alumnoTarea->grupo_id = $request->grupo;
            $alumnoTarea->grado_id = $request->grado;
            $alumnoTarea->tarea_id = $request->tarea;
            $alumnoTarea->ciclo_escolar_id = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;
            $alumnoTarea->periodo_id = $request->periodo;
            $alumnoTarea->alumno_id = $request->Alumnos['id'][$key];
            $alumnoTarea->calificacion = $calif;
            $alumnoTarea->tipo_tarea = $tareaMaestro->tipo;
            $alumnoTarea->save();
        }
        $tareaMaestro->isCaptured = true;
        $tareaMaestro->save();
        return $this->index();
    }

    /**
     * @param $tarea
     * @param $grado
     * @param $grupo
     * @param $periodo
     * @return Application|Factory|View
     */
    public function viewEditStudentTask($tarea, $grado, $grupo, $periodo)
    {
        $ciclo = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;
        $grupoAlumno = GrupoAlumno::where('grado_id', $grado)->where('grupo_id', $grupo)->where('ciclo_escolar_id', $ciclo)->get();
        $alumnos = [];
        foreach ($grupoAlumno as $item) {
            array_push($alumnos, Alumno::where('id', $item->alumno_id)->first());
        }
        sort($alumnos);
        return view('tareas.AlumnosUpdate', compact('tarea', 'alumnos'));
    }

    /**
     * @return Application|Factory|View
     */
    public function editStudentTask(Request $request)
    {
        $tareaMaestro = Tareas::find($request->tarea);

        foreach ($request->Alumnos['calif'] as $key => $calif) {
            $alumnoTarea = AlumnoTareas::where('tarea_id', $request->tarea)->where('alumno_id', $request->Alumnos['id'][$key])->first();
            if ($alumnoTarea == null) {
                $alumnoTarea = new AlumnoTareas;
                $alumnoTarea->grupo_id = $tareaMaestro->grupo_id;
                $alumnoTarea->grado_id = $tareaMaestro->grado_id;
                $alumnoTarea->tarea_id = $request->tarea;
                $alumnoTarea->ciclo_escolar_id = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;
                $alumnoTarea->periodo_id = $tareaMaestro->periodo_id;
                $alumnoTarea->alumno_id = $request->Alumnos['id'][$key];
                $alumnoTarea->calificacion = $calif;
                $alumnoTarea->save();
            } else {
                $alumnoTarea->calificacion = $calif;
                $alumnoTarea->save();
            }

        }
        return $this->index();
    }

    /**
     * @return Application|Factory|View
     */
    public function viewTaskCalif()
    {
        $maestro_id = Personal::where('usuario_id', Auth::user()->id)->first()->id;
        $clases = Clases::where('maestro_id', $maestro_id)->get();
        $periodos = Periodos::all();
        $grupos = clases::select('grupo_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
        $grados = clases::select('grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'clases.asignatura_id')
            ->where('maestro_id', $maestro_id)
            ->distinct()
            ->get();
//        dd($grados,$grupo);
        $filtrado = $this->super_unique($clases, 'asignatura_id');
        $tipos = Tareas::select('tipo')->where('maestro_id', $maestro_id)->distinct()->orderBy('tipo', 'asc')->get();
        return view('tareas.CalificacionesPeridoo', compact('filtrado', 'periodos', 'grados', 'grupos', 'tipos'));
    }

    /**
     * @param $asignatura
     * @param $grado
     * @param $grupo
     * @param $periodo
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadTasksDoc($asignatura, $grado, $grupo, $periodo, $tipo)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $fontStyleName = 'oneUserDefinedStyle';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
        );
        $header = array('size' => 14, 'bold' => true, 'align' => 'both');
        $nature = array('size' => 11, 'bold' => true);
        $nature2 = array('size' => 9, 'bold' => false);

        //source =;
        $section->addImage(
            realpath(__DIR__ .'./img/imagen.png'),
            array(
                'width' => 265,
                'height' => 100,
                'marginTop' => 500,
                'marginLeft' => 500,
                'wrappingStyle' => 'behind',
                'positioning' => 'absolute',
            )
        );
        $nombre_asignatura = Asignaturas::find($asignatura)->nombre;
        $nombre_periodo = Periodos::find($periodo)->nombre;
        $fechaComoEntero = strtotime(Periodos::find($periodo)->fecha_inicio);
        $mesperiodo = strftime('%b', $fechaComoEntero);
        $maestro = Personal::where('usuario_id', Auth::user()->id)->first();
        $nombre_maestro = $maestro->nombres . ' ' . $maestro->apellidos;
        $nombre_ciclo = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->nombre;
        $nombre_grado = Grados::find($grado)->nombre_corto;
        $nombre_grupo = Grupos::find($grupo)->nombre;
//head document
        $section->addText('                                                                       ' . $nombre_ciclo, $header);
        $section->addText('                                                                                               PROF:' . $nombre_maestro . '   ASIGNATURA:' . $nombre_asignatura . '', $fontStyleName);
        $section->addText('                                                                                               MES:' . $mesperiodo . '  PERIODO:' . $nombre_periodo . ' GRUPO:' . $nombre_grado . $nombre_grupo, $fontStyleName);
        $section->addText('                                                                                               TIPO: ' . $tipo, $fontStyleName);


        $fancyTableStyleName = 'Prueba Con Tablas';
        $fancyTableStyle = array('borderSize' => 5, 'borderColor' => '000000', 'cellMargin' => 0, 'alignment' => JcTable::CENTER, 'cellSpacing' => 0);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 5, 'borderBottomColor' => '000000');
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $section->addTable($fancyTableStyleName);

        $ciclo_escolar = CicloEscolar::orderBy('fecha_inicio', 'asc')->first()->id;

        //get alumnos
        $alumnos = Alumno::select('*')
            ->join('grupo_alumnos', 'grupo_alumnos.alumno_id', '=', 'alumnos.id')
            ->where('grupo_alumnos.ciclo_escolar_id', $ciclo_escolar)
            ->where('grupo_id', $grupo)
            ->where('grado_id', $grado)->orderBy('apellido_paterno', 'asc')->get();
        //head table
        $table->addRow();
        $table->addCell(500)->addText(' No', $nature);
        $table->addCell(4000)->addText('NOMBRE', $nature);
        $tareas = Tareas::select('*')->where('ciclo_escolar_id', $ciclo_escolar)->where('materia_id', $asignatura)->where('grupo_id', $grupo)
            ->where('grado_id', $grado)->where('maestro_id', Personal::where('usuario_id', Auth::user()->id)->first()->id)->where('isCaptured', true)->where('tipo', $tipo)->get();

        if (count($tareas) === 0) {
            return redirect()->route('Tarea.Calif')->with('danger', 'No hay calificaciones de tareas con esos filtros');
        } else {
            foreach ($tareas as $tarea) {
                $table->addCell(500)->addText($tarea->nombre, $nature);
            }
            $table->addCell(500)->addText('Promedio');

//start student row
            foreach ($alumnos as $key => $alumno) {
                $table->addRow();
                $table->addCell(500)->addText($key + 1);
                $table->addCell(6000)->addText($alumno->apellido_paterno . ' ' . $alumno->apellido_materno . ' ' . $alumno->nombres);
                $calificacion = 0;
                $totalTareas = 0;
                $promedio = 0;
                foreach ($tareas as $tarea) {
                    $alumnoTareas = AlumnoTareas::select('*')->where('alumno_id', $alumno->alumno_id)->where('tarea_id', $tarea->id)->first();
                    if ($alumnoTareas !== null) {
                        $calif = round(($alumnoTareas->calificacion * $tarea->valor) / 100, 2);
                        $table->addCell(500)->addText($alumnoTareas->calificacion);
                        $calificacion += $calif;
                        $totalTareas += $tarea->valor;
                    } else {
                        $table->addCell(500)->addText('0');
                    }
                }
                $promedio = round(($calificacion / $totalTareas) * 100, 2);
                $table->addCell(500)->addText($promedio);
            }
            //create doc
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save('Calificacion' . $nombre_grado . $nombre_grupo . '.docx');

            //download doc and delete after send
            return response()->download('Calificacion' . $nombre_grado . $nombre_grupo . '.docx')->deleteFileAfterSend();
        }

    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    function super_unique($array, $key)
    {
        $temp_array = [];
        foreach ($array as &$v) {
            if (!isset($temp_array[$v[$key]]))
                $temp_array[$v[$key]] =& $v;
        }
        $array = array_values($temp_array);
        return $array;

    }

}
