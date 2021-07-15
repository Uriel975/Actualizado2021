@extends('layout')

@section('template_title')
    {{ $clase->name ?? 'Show Clase' }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="float-left">
                            <span class="card-title">Show Clase</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary" href="{{ route('clases.index') }}"> Back</a>
                        </div>
                    </div>

                    <div class="card-body">
                        
                        <div class="form-group">
                            <strong>Maestro Id:</strong>
                            {{ $clase->maestro_id }}
                        </div>
                        <div class="form-group">
                            <strong>Grado Id:</strong>
                            {{ $clase->grado_id }}
                        </div>
                        <div class="form-group">
                            <strong>Grupo Id:</strong>
                            {{ $clase->grupo_id }}
                        </div>
                        <div class="form-group">
                            <strong>Asignatura Id:</strong>
                            {{ $clase->asignatura_id }}
                        </div>
                        <div class="form-group">
                            <strong>Ciclo Escolar Id:</strong>
                            {{ $clase->ciclo_escolar_id }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
