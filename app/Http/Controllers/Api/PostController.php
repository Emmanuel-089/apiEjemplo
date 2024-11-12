<?php

namespace App\Http\Controllers\Api;
use Storage;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\PutRequest;

class PostController extends Controller
{

    public function index()// funcion del reporte principal
    {
        return response()->json(Post::with('category')->paginate(2));
    }

   
    public function store(StoreRequest $request)//APi de crear
    {
        return response()->json(Post::create($request->validated()));

    }


    public function show(Post $post)//API de mostrar
    {
        return response()->json($post);
    }

    public function slug(string $slug){//llamado del slug de una publicación
        $category=Post::where('slug',$slug)->firstOrFail();
        return response()->json($category);
    }

  
    
    public function update(PutRequest $request, Post $post)//API de ACTUALIZAR
    {
        
        $post->update($request->validated());
        return response()->json($post);
    }


    public function destroy(Post $post)//api de eliminar DELETE
    {
        $post->delete();
        return response()->json('ok');
    }

    function upload(Request $request, Post $post){
        
        $request->validate([
            'image'=>'required|mimes:jpg,jpeg,png,gif|max:10240'//validacion del archivo
        ]);

        Storage::disk('public_upload')->delete("image/".$post->image);

        $data['image']=$filename=time().'.'.$request['image']->extension();//el nombre de como se va a llamar el archivo en la BD
        $request->image->move(public_path('image'),$filename);//este es para la ruta de la carpeta donde se van a subir las imagenes
        $post->update($data);//actualizamos
        return response()->json($post);//retorno de la api
    }
}
