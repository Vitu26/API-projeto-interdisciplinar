<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultasStoreRequest;
use App\Models\Consultas;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;


class ConsultasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //todas as consultas
        $consultas = Consultas::all();

        return response() -> json([
            'consultas' => $consultas
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ConsultasStoreRequest $request)
    {
        try {
            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();

            // Formate a data para o formato correto (Y-m-d) antes de salvar no banco de dados
            $formattedDate = Carbon::createFromFormat('d/m/Y', $request->data)->format('Y-m-d');

            // Crie a consulta
            Consultas::create([
                'name' => $request->name,
                'image' => $imageName,
                'data' => $formattedDate, // Use a data formatada aqui
                'description' => $request->description
            ]);

            // Salve a imagem na pasta de armazenamento
            Storage::disk('public')->put($imageName, file_get_contents($request->image));

            // Retorne uma resposta JSON de sucesso
            return response()->json([
                'message' => "Consulta criada com sucesso."
            ], 200);
        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON com erro e uma mensagem detalhada
            return response()->json([
                'message' => "Erro ao criar consulta: " . $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
       // Product Detail
       $consultas = Consultas::find($id);
       if(!$consultas){
         return response()->json([
            'message'=>'Product Not Found.'
         ],404);
       }

       // Return Json Response
       return response()->json([
          'consultas' => $consultas
       ],200);
    }

    // public function update(ConsultasStoreRequest $request, $id)
    // {
    //     try {
    //         // Find product
    //         $consultas = Consultas::find($id);
    //         if(!$consultas){
    //           return response()->json([
    //             'message'=>'consultas Not Found.'
    //           ],404);
    //         }

    //         //echo "request : $request->image";
    //         $consultas->name = $request->name;
    //         $consultas->data = $request->date;
    //         $consultas->description = $request->description;

    //         if($request->image) {

    //             // Public storage
    //             $storage = Storage::disk('public');

    //             // Old iamge delete
    //             if($storage->exists($consultas->image))
    //                 $storage->delete($consultas->image);

    //             // Image name
    //             $imageName = Str::random(32).".".$request->image->getClientOriginalExtension();
    //             $consultas->image = $imageName;

    //             // Image save in public folder
    //             $storage->put($imageName, file_get_contents($request->image));
    //         }

    //         // Update Product
    //         $consultas->save();

    //         // Return Json Response
    //         return response()->json([
    //             'message' => "consultas successfully updated."
    //         ],200);
    //     } catch (\Exception $e) {
    //         print($e);
    //         // Return Json Response
    //         return response()->json([

    //             'message' => "Something went really wrong!"
    //         ],500);
    //     }
    // }
    public function update(ConsultasStoreRequest $request, $id)
{
    try {
        $consultas = Consultas::find($id);
        if (!$consultas) {
            return response()->json(['message' => 'Consulta não encontrada.'], 404);
        }

        $consultas->name = $request->name;
        $consultas->data = $request->data; // Corrigido para $request->data
        $consultas->description = $request->description;

        if ($request->hasFile('image')) {
            $storage = Storage::disk('public');
            if ($storage->exists($consultas->image)) {
                $storage->delete($consultas->image);
            }

            $imageName = Str::random(32) . "." . $request->image->getClientOriginalExtension();
            $consultas->image = $imageName;
            $storage->put($imageName, file_get_contents($request->image));
        }

        $consultas->save();
        return response()->json(['message' => 'Consulta atualizada com sucesso.'], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Erro ao atualizar consulta: ' . $e->getMessage()], 500);
    }
}

    public function destroy($id)
    {
        // Detail
        $consultas = Consultas::find($id);
        if(!$consultas){
          return response()->json([
             'message'=>'consultas Not Found.'
          ],404);
        }

        // Public storage
        $storage = Storage::disk('public');

        // Iamge delete
        if($storage->exists($consultas->image))
            $storage->delete($consultas->image);

        // Delete Product
        $consultas->delete();

        // Return Json Response
        return response()->json([
            'message' => "consulta successfully deleted."
        ],200);
    }
}
