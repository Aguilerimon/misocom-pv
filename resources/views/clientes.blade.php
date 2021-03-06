@extends('layout_principal')
@section('content')
    
    <div class="row mt-5">
            <div class="col-8">
                <h1>{{$title}}</h1>
            </div>
            <div class="col-4">
                <div class="form-inline my-2 my-lg-0">
                    <input class="form-control mr-sm-2" id="buscar" type="text" placeholder="Ingresar búsqueda">
                <span>
                <button class="btn btn-outline-success my-2 my-sm-0" onclick="buscar()">Buscar</button>
                </div>
            </div>
        </div>
        
    <table class="table">
        <thead class="thead-dark">
        <tr>
            <th scope="col">#ID</th>
            <th scope="col">Imagen</th>
            <th scope="col">Nombre(s)</th>
            <th scope="col">Direccion</th>
            <th scope="col">Correo</th>
            <th scope="col">Contraseña</th>
            <th scope="col">Editar</th>
            <th scope="col">Eliminar</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clientes as $cliente)
            <tr id="renglon_{{$cliente->id}}">
                <th scope="row">{{$cliente->id}}</th>
                <td>
                    @if(file_exists(public_path('img/clientes/'.$cliente->id.'.jpg')))
                        <img src="{{url('img/clientes/'.$cliente->id)}}.jpg" width="50px">
                    @endif
                </td>
                <td>{{$cliente->nombres}}</td>
                <td>{{$cliente->direccion}}</td>
                <td>{{$cliente->correo}}</td>
                <td>{{$cliente->contrasena}}</td>
                <td>
                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Editar">
                        <a href="clientesEditar/{{$cliente->id}}">
                        <button type="button" class="btn btn-primary"><i class="fas fa-edit"></i></button>
                        </a>
                    </span>
                </td>
                <td>
                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Eliminar">
                        <button onclick="eliminarCliente({{$cliente->id}})" type="button" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
                    </span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    
    <h6>Numero de Registros: {{$numRegistros}}</h6>

    <script type="text/javascript">

        function buscar(){
            location.href="{{asset('/clientes/')}}/" + $('#buscar').val();
        }

        function imprimir(buscar) {
            location.href = "{{asset('/clientesPDF/')}}/" + buscar;
        }

        function eliminarCliente(cliente_id){
            $.ajax({
                url: "{{asset('clientesEliminar')}}/"+cliente_id,
                method: 'GET',
                data:{
                },
                dataType: 'json',
                beforeSend: function () {
                    //$("#form04_submit").removeClass("d-none");

                },
                success: function (response) {
                    if(response.status == 'ok'){
                        toastr["success"](response.mensaje);
                        $("#renglon_"+cliente_id).remove();
                    }else{
                        toastr["error"](response.mensaje);
                    }
                },
                error: function() {
                    toastr["error"]("Error, no se pudo completar la operación");
                },
                complete: function () {

                }

            })

        }
        $(document).ready(function() {

        });
    </script>
@endsection