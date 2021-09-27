<?php

namespace App\Http\Controllers;
use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Grados;
use App\Models\Grupos;
use App\Models\ListaDoc;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\JcTable;

class ListaDocController extends Controller
{
    public function index()
    {
        $grupos = Grupos::all();
        $grados = Grados::all();
        return view('ListaAsistencia',compact('grupos','grados'));
    }

    public function asistenciaword($grado,$grupo)
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection(array('orientation' => 'landscape'));
        $fontStyleName = 'oneUserDefinedStyle';
        $phpWord->addFontStyle(
            $fontStyleName,
            array('name' => 'Tahoma', 'size' => 10, 'color' => '1B2232', 'bold' => true)
        );
        $header = array('size' => 14, 'bold' => true,'align'=>'both');
        $nature = array('size' => 11, 'bold' => true);
        // $sources = ;
        $section->addImage(
            realpath(__DIR__ .'./img/imagen.png'),
            array(
                'width'         => 265,
                'height'        => 100,
                'marginTop'     => 500,
                'marginLeft'    => 500,
                'wrappingStyle' => 'behind',
                'positioning' => 'absolute',
            )
        );
        $ciclo_escolar = CicloEscolar::orderBy('fecha_inicio','asc')->first();
        $nombre_ciclo = $ciclo_escolar->nombre;
        $nombre_grado = Grados::find($grado)->nombre_corto;
        $nombre_grupo = Grupos::find($grupo)->nombre;
//head document
        //$section->addTextBreak(1);
        $section->addText('                                                                       '.$nombre_ciclo, $header);
        $section->addText('                                                                                               PROF:___________________________   ASIGNATURA:________________', $fontStyleName);
        $section->addText('                                                                                               MES:___________________________  UNIDAD________________ GRUPO:'.$nombre_grado.$nombre_grupo, $fontStyleName);
        $section->addText(' ');


        $fancyTableStyleName = 'Prueba Con Tablas';
        $fancyTableStyle = array('borderSize' => 5, 'borderColor' => '000000', 'cellMargin' => 0, 'alignment' => JcTable::CENTER, 'cellSpacing' => 0);
        $fancyTableFirstRowStyle = array('borderBottomSize' => 5, 'borderBottomColor' => '000000');
        $phpWord->addTableStyle($fancyTableStyleName, $fancyTableStyle, $fancyTableFirstRowStyle);
        $table = $section->addTable($fancyTableStyleName);

        //get alumnos
        $alumnos = Alumno::select('*')
            ->join('grupo_alumnos','grupo_alumnos.alumno_id','=','alumnos.id')
            ->where('grupo_alumnos.ciclo_escolar_id','=',$ciclo_escolar->id)
            ->where('grupo_id','=',$grupo)
            ->where('grado_id','=',$grado)->orderBy('apellido_paterno','asc')->get();
        //head table
        $table->addRow();
        $table->addCell(500)->addText(' No',$nature);
        $table->addCell(4000)->addText('NOMBRE',$nature);
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
        $table->addCell(500)->addText(' ');
//start student row
        foreach ($alumnos as $key=>$alumno){
            $table->addRow();
            $table->addCell(500)->addText($key+1);
            $table->addCell(6000)->addText($alumno->apellido_paterno.' '.$alumno->apellido_materno.' '.$alumno->nombres);
            $table->addCell(500)->addText('');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
            $table->addCell(500)->addText(' ');
        }
        //create doc
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('Asistencia.docx');

        //download doc and delete after send
        return response()->download('Asistencia.docx')->deleteFileAfterSend();
    }
}


