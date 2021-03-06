<?php

namespace App\Http\Controllers;
use App\Producto;
use App\Proveedor;
use App\Categoria;
use App\Sucursal;
use Illuminate\Http\Request;
use PDF;

class ProductoController extends Controller {
    public function index()
    {
        $productos = Producto::all();
        $title = "Lista de Productos";
        $numRegistros = $productos->count();
        return view('productos')
            ->with('productos', $productos)
            ->with('title', $title)
            ->with('numRegistros', $numRegistros);
    }
    public function buscar($buscar)
    {
        $productos = Producto::where('nombre','like', $buscar.'%')
            ->orWhere('descripcion','like', $buscar.'%')
            ->orWhere('precio', $buscar)
            ->orWhere('costo', $buscar)
            ->get();
        $title = "Lista de Productos | ".$buscar;
        $numRegistros = $productos->count();
        return view('productos')
            ->with('productos', $productos)
            ->with('title', $title)
            ->with('numRegistros', $numRegistros)
            ->with('buscar', $buscar);
    }
    public function downloadPDF($buscar = null){
        if( !isset($buscar) || $buscar == null){
            $productos = Producto::all();
        }else {
            $productos = Producto::where('nombre', 'like', $buscar . '%')
                ->orWhere('descripcion', 'like', $buscar . '%')
                ->orWhere('precio', $buscar)
                ->orWhere('costo', $buscar)
                ->get();
        }
        $title = "Lista de Productos | " . $buscar;
        $numRegistros = $productos->count();

        $pdf = PDF::loadView('productosPDF', compact('productos', 'title', 'numRegistros'));
        return $pdf->download('productos.pdf');

    }
    public function eliminar($producto_id)
    {
        if ($producto_id) {
            try {
                if(Producto::destroy($producto_id)){
                    return response()->json(['mensaje' => 'Producto eliminado', 'status' => 'ok'], 200);
                }else{
                    return response()->json(['mensaje' => 'El producto no se pudo eliminar', 'status' => 'error'], 400);
                }
            } catch (Exception $e) {
                return response()->json(['mensaje' => 'Error al eliminar el producto'], 400);
            }
        }else{
            return response()->json(['mensaje' => 'Error al eliminar el producto, producto no encontrado '], 400);
        }

    }
    public function nuevo()
    {
        $title = "Nuevo Producto";
        $proveedores = Proveedor::all();
        $categorias = Categoria::all();
        $sucursales = Sucursal::all();
        $producto = null;
        $accion = "nuevo";
        return view('productosForm')
            ->with('title', $title)
            ->with('proveedores', $proveedores)
            ->with('categorias', $categorias)
            ->with('sucursales', $sucursales)
            ->with('producto', $producto)
            ->with('accion', $accion);

    }
    public function guardar(Request $request)
    {
        try {
            if($request->accion == 'nuevo') {
                $producto = new Producto();
                $producto->nombre = $request->nombre_producto;
                $producto->descripcion = $request->descripcion;
                $producto->precio = $request->precio;
                $producto->costo = $request->costo;
                $producto->proveedor_id = $request->proveedor;
                $producto->categoria_id = $request->categoria;
                $producto->sucursal_id = $request->sucursal;
                if ($producto->save()) {
                    $producto_id = $producto->id;
                    if( $request->hasFile('imagen')) {
                        $file = $request->file('imagen');
                        $extension = $file->getClientOriginalExtension();
                        $fileName = $producto_id . '.' . $extension;
                        $path = public_path('img/productos/');
                        $request->file('imagen')->move($path, $fileName);
                    }
                    return response()->json(['mensaje' => 'Producto agregado sin imagen', 'status' => 'ok'], 200);

                } else {
                    return response()->json(['mensaje' => 'Error al agregar el producto', 'status' => 'error'], 400);
                }
            }else if($request->accion == 'editar'){
                if($producto = Producto::find($request->id)){
                    $producto->nombre = $request->nombre_producto;
                    $producto->descripcion = $request->descripcion;
                    $producto->precio = $request->precio;
                    $producto->costo = $request->costo;
                    $producto->proveedor_id = $request->proveedor;
                    $producto->categoria_id = $request->categoria;
                    $producto->sucursal_id = $request->sucursal;
                    if ($producto->save()) {
                        if( $request->hasFile('imagen')) {
                            $producto_id = $request->id;
                            $file = $request->file('imagen');
                            //$extension = $file->getClientOriginalExtension();
                            $extension = 'jpg';
                            $fileName = $producto_id . '.' . $extension;
                            $path = public_path('img/productos/');
                            $request->file('imagen')->move($path, $fileName);
                        }
                        return response()->json(['mensaje' => 'Cambios guardados correctamente', 'status' => 'ok'], 200);
                    } else {
                        return response()->json(['mensaje' => 'Error al intentar guardar los cambios', 'status' => 'error'], 400);
                    }
                }else{
                    return response()->json(['mensaje' => 'Producto no encontrado', 'status' => 'error'], 400);
                }
            }

        } catch (Exception $e) {
            return response()->json(['mensaje' => 'Error al agregar el producto'], 403);
        }
    }
    public function editar($producto_id)
    {
        if ($producto_id) {
            $accion = "editar";
            try {
                if($producto = Producto::find($producto_id)){
                    $title = "Editar Producto";
                    $proveedores = Proveedor::all();
                    $categorias = Categoria::all();
                    $sucursales = Sucursal::all();
                    return view('productosForm')
                        ->with('title', $title)
                        ->with('proveedores', $proveedores)
                        ->with('categorias', $categorias)
                        ->with('sucursales', $sucursales)
                        ->with('producto', $producto)
                        ->with('accion', $accion);
                }else{
                    return response()->json(['mensaje' => 'Producto no encontrado', 'status' => 'error'], 400);
                }
            } catch (Exception $e) {
                return response()->json(['mensaje' => 'Error al eliminar el producto'], 400);
            }
        }else{
            return response()->json(['mensaje' => 'Error al eliminar el producto, producto no encontrado '], 400);
        }

    }
}