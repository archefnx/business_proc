<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Process;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    public function index(Request $request)
    {
//        return 'asd';
        // Получение количества элементов на страницу из запроса или использование значения по умолчанию
        $pageSize = $request->input('page_size', 15); // можно задать любое значение по умолчанию

        // Использование метода paginate() для автоматической постраничной навигации
        $fields = Field::with(['process', 'type'])->paginate($pageSize);

        // Возвращение результата вместе с пагинацией
        return response()->json($fields);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        $process = Process::create($validatedData);
        return response()->json($process, 201);
    }

    public function show($id)
    {
        $process = Process::findOrFail($id);
        return response()->json($process);
    }

    public function update(Request $request, $id)
    {
        $process = Process::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string'
        ]);

        $process->update($validatedData);
        return response()->json($process);
    }

    public function destroy($id)
    {
        $process = Process::findOrFail($id);
        $process->delete();
        return response()->json('success delete!');
    }
}
