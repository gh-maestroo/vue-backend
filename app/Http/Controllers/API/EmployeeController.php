<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    protected $employee;
    public function __construct()
    {
        $this->employee = new Employee();
    }
    public function index()
    {
        try {
            return response()->json($this->employee->all(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Ensure valid image file
        ]);

        try {
            if ($request->hasFile('image')) {
                $imagePath = $this->storeImage($request->file('image'));
                $validatedData['image'] = $imagePath;
            }

            $employee = $this->employee->create($validatedData);
            return response()->json($employee, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function show(string $id)
    {
        try {
            $employee = $this->employee->find($id);
            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }
            return response()->json($employee, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function update(Request $request, string $id)
    {
        $employee = $this->employee->find($id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Ensure valid image file
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($employee->image && File::exists(public_path($employee->image))) {
                    File::delete(public_path($employee->image));
                }
                $imagePath = $this->storeImage($request->file('image'));
                $validatedData['image'] = $imagePath;
            } else {
                // Keep the existing image path if no new image is uploaded
                $validatedData['image'] = $employee->image;
            }

            $employee->update($validatedData);
            return response()->json($employee, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    private function storeImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        return 'images/' . $imageName;
    }

    public function destroy(string $id)
    {
        try {
            $employee = $this->employee->find($id);
            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }
            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
