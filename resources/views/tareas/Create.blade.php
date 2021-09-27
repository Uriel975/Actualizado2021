<x-app-layout>
    <link rel="stylesheet" href="{{asset('css/tareas/create.css')}}">
    <div class="alert-warning" id="alert">
        No puede agregar mas tareas
    </div>
    <div class="alert-warning" id="alertselect">
        Complete todos los campos
    </div>
    <div class="signup-form">
        <form action="{{route('Tarea.post')}}" method="post" class="form-horizontal">
            @csrf
            <div class="row">
                <div class="col-8 offset-4">
                    <h2>
                        <center>Agregar Criterio
                        </center>
                    </h2>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-4">Criterio</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="nombre" required="required">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-4">Descripción</label>
                <div class="col-8">
                    <input type="text" class="form-control" name="descripcion" required="required">
                </div>
            </div>

            <div class="form-group row">
                <label for="materia" class="col-form-label col-4">Materia</label>
                <select class="form-control" name="materia" id="materia" required="required">
                    <option value="">selecciona...</option>
                    @foreach($filtrado as $item)
                        <option
                            value="{{$item->asignatura_id}}">{{\App\Models\Asignaturas::find($item->asignatura_id)->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group row">
                <label for="periodo" class="col-form-label col-4">Periodo</label>
                <select class="form-control" name="periodo" id="periodo" required="required">
                    <option value="">selecciona...</option>
                    @foreach($periodos as $item)
                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group row">
                <label for="grado" class="col-form-label col-4">Grado</label>
                <select class="form-control" name="grado" id="grado" required="required">
                    <option value="">selecciona...</option>
                    @foreach($grados as $item)
                        <option
                            value="{{$item->grado_id}}">{{\App\Models\Grados::find($item->grado_id)->nombre_largo}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group row">
                <label for="grupo" class="col-form-label col-4">Grupo</label>
                <select class="form-control" name="grupo" id="grupo" required="required">
                    <option value="">selecciona...</option>
                    @foreach($grupos as $item)
                        <option
                            value="{{$item->grupo_id}}">{{\App\Models\Grupos::find($item->grupo_id)->nombre}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-4">Tipo</label>
                <select class="form-control" name="tipo" id="tipo" required="required">
                    <option value="">selecciona...</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                </select>
                <div class="form-group row">
                    <label for="ciclo_escolar" class="col-form-label col-4">Ciclo escolar</label>
                    <input name="ciclo_escolar" value="{{$ciclo->nombre}}" type="text" readonly>
                </div>
                <div class="form-group row mt-3">
                    <label class="col-form-label col-4">Valor de la Tarea</label>
                    <div class="col-8">
                        <select onfocus="getmax()" class="form-control" name="valor" id="valor" required="required">
                            <option value="">selecciona...</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-8 offset-4">
                        <button id="button" type="submit" class="btn btn-primary btn-lg">Guardar</button>
                        <input type="button" class="btn btn-primary btn-lg" onclick="history.back()"
                               name="volver atrás"
                               value="volver atrás">
                    </div>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>

<script>
    var alert = document.getElementById('alert');
    let alertselect = document.getElementById('alertselect');
    alert.style.visibility = 'hidden';
    alertselect.style.visibility = 'hidden';
    const getmax = async () => {
        let asignatura = document.getElementById('materia').value;
        let grado = document.getElementById('grado').value;
        let grupo = document.getElementById('grupo').value;
        let periodo = document.getElementById('periodo').value;
        let tipo = document.getElementById('tipo').value;
        let select = document.getElementById('valor');
        let button = document.getElementById('button');
        let url = `/getmax/${asignatura}/${grado}/${grupo}/${periodo}/${tipo}`;
        if (asignatura !== '' && grado !== '' && grupo !== '' && periodo !== '' && tipo !== '') {
            await fetch(url).then((response) => {
                return response.json();
            }).then((data) => {

                if (data === 0) {
                    clean(select);
                    alert.style.visibility = 'hidden';
                    addOption(100, select)
                    button.disabled = false;
                } else if (data === 100) {
                    clean(select);
                    alert.style.visibility = 'visible';
                    button.disabled = true;
                } else {
                    clean(select);
                    alert.style.visibility = 'hidden';
                    addOption(100 - data, select)
                    button.disabled = false;

                }
            });
        } else {
            alertselect.style.visibility = 'visible';
            location.reload();
        }
    }
    const addOption = (max, select) => {
        for (let i = 1; i <= max; i++) {
            let option = document.createElement('option');
            option.value = i;
            option.text = i;
            select.appendChild(option)
        }
    }
    const clean = (select) => {
        for (let i = select.options.length; i >= 0; i--) {
            select.remove(i)
        }
        let option = document.createElement('option');
        option.value = '';
        option.text = 'Selecciona...';
        select.appendChild(option)
    }
</script>
