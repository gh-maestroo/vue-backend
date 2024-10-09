<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // Add this at the top of your file

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
            Log::error("Employee not found for ID: {$id}");
            return response()->json(['error' => 'Employee not found'], 404);
        }
    
        // Log the request data
        Log::info("Update request received for Employee ID: {$id}", [
            'request_data' => $request->all(),
            'file_data' => $request->file('image') ? $request->file('image')->getClientOriginalName() : null,
        ]);
    
        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'image' => 'nullable', // Ensure valid image file
        ]);
    
        try {
            if ($request->hasFile('image')) {
                // Log before deleting the old image
                if ($employee->image && File::exists(public_path($employee->image))) {
                    Log::info("Deleting old image: " . public_path($employee->image));
                    File::delete(public_path($employee->image));
                }
    
                $imagePath = $this->storeImage($request->file('image'));
                $validatedData['image'] = $imagePath;
            }
    
            $employee->update($validatedData);
    
            Log::info("Employee updated successfully", [
                'employee_id' => $employee->id,
                'updated_data' => $validatedData,
            ]);
    
            return response()->json($employee, 200);
        } catch (\Exception $e) {
            // Log the exception details
            Log::error("Failed to update employee. Error: " . $e->getMessage(), [
                'exception' => $e
            ]);
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
            
            // Delete image if exists
            if ($employee->image && File::exists(public_path($employee->image))) {
                File::delete(public_path($employee->image));
            }
            
            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
