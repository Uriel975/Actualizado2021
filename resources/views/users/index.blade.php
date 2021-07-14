<x-app-layout>
    <div class="container mt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header card-header-primary">
                                    <h4 class="card-title">Usuarios</h4>
                                    <p class="card-category">Usuarios registrados</p>
                                </div>
                                <div class="card-body">
                                    @if (session('success'))
                                        <div class="alert alert-success" role="success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">Añadir
                                                usuario</a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead class="text-primary">
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Username</th>
                                            <th>Created_at</th>
                                            <th class="text-right">Roles</th>
                                            </thead>
                                            <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->id }}</td>
                                                    <td>
                                                        <span class="{{$user->person ? '' : 'badge bg-danger'}}">
                                                            {{ $user->person ? $user->person->nombres : 'personal no asignado' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->username }}</td>
                                                    <td>{{ $user->created_at }}</td>
                                                    <td>
                                                        @forelse ($user->roles as $role)
                                                            <span
                                                                class="badge badge-info bg-primary">{{ $role->name }}</span>
                                                        @empty
                                                            <span class="badge bg-danger">rol no asignado</span>
                                                        @endforelse
                                                    </td>
                                                    <td class="td-actions text-right">
                                                        <a href="{{ route('users.show', $user->id) }}"
                                                           class="btn btn-info"><i class="material-icons">person</i></a>
                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                           class="btn btn-warning"><i
                                                                class="material-icons">edit</i></a>
                                                        <form action="{{ route('users.destroy', $user->id) }}"
                                                              method="POST" style="display: inline-block;"
                                                              onsubmit="return confirm('Seguro?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-danger" type="submit" rel="tooltip">
                                                                <i class="material-icons">close</i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
