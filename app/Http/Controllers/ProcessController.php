<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Process;
use App\Models\Type;
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

    public function addFields(Request $request)
    {
        $processName = $request->input('process_name');
        $fields = $request->input('fields');
        $errors = []; // массив для хранения информации об ошибках

        // Проверка существования процесса
        $process = Process::where('name', $processName)->first();
        if (!$process) {
            return response()->json(['error' => 'Process not found'], 404);
        }

        $allowedTypes = ['text', 'number', 'date']; // Допустимые типы
        foreach ($fields as $field) {
            if (!in_array($field['type'], $allowedTypes)) {
                $errors[] = "Field type '{$field['type']}' is not allowed"; // добавление ошибки в массив
                continue; // пропуск текущей итерации
            }

            // Найти или создать тип
            $type = Type::firstOrCreate(
                ['name' => $field['type']],
                ['format' => $field['format'] ?? '', 'description' => 'Automatically created type']
            );

            // Поиск существующего поля
            $existingField = Field::where('process_id', $process->id)
                ->where('type_id', $type->id)
                ->where('name', $field['name'])
                ->first();

            if ($existingField) {
                // Обновление существующего поля
                $existingField->update([
                    'value' => $field['value'],
                    'description' => $field['description'] ?? $existingField->description
                ]);
            } else {
                // Создание нового поля
                Field::create([
                    'process_id' => $process->id,
                    'type_id' => $type->id,
                    'name' => $field['name'],
                    'value' => $field['value'],
                    'description' => $field['description'] ?? ''
                ]);
            }
        }

        if (!empty($errors)) {
            // Возврат ошибок, если они были найдены, но продолжение обработки других полей
            return response()->json(['errors' => $errors], 200);
        }

        return response()->json(['message' => 'Fields processed successfully'], 201);
    }


}
