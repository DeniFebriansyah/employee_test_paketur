<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    // store function Company
    public function store(){
        if (auth()->user()->role !== 'super_admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. Only super admin can access this function.'
            ], 403);
        }

        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        $validatedData = $validator->validated();
        $company = Company::create($validatedData);
        $manager = User::create([
            'name' => 'Manager_ ' . $company->name,
            'email' => $company->email,
            'password' => Hash::make('password'), // default password
            'role' => 'manager',
            'company_id' => $company->id,
        ]);
        $employee = Employee::create([
            'name' => 'Employee_ ' . $company->name,
            'role' => 'manager',
            'company_id' => $company->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], 201);
    }
    //store function Employee
    public function storeEmployee(){
        if (auth()->user()->role !== 'manager') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access. Only super admin can access this function.'
            ], 403);
        }
        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'role' => 'required|in:manager,employee',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['company_id'] = Company::where('email', auth()->user()->email)->first()->id;
        $employee = Employee::create($validatedData);
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['name'] = str_replace(' ', '', strtolower($validatedData['name'])).'@'.strtolower($employee->company->name).'.com',
            'password' => Hash::make('password'), // default password
            'role' => $validatedData['role'],
            'company_id' => auth()->user()->company_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Employee created successfully',
            'data' => $employee
        ], 201);
    }
    //update function employee
    public function updateEmployee(Request $request, $id){
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        if (auth()->user()->role == 'manager') {
            if ($employee->company_id != auth()->user()->company_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        } else if (auth()->user()->role == 'employee') {
            if ($employee->id != auth()->user()->employee->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();
        $employee->update($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ], 200);
    }
    public function getAllEmployees(){
        if (auth()->user()->role == 'employee') {
            $employees = Employee::where('company_id', auth()->user()->company_id)->where('role', 'employee')->paginate(10);
        }else{
            $employees = Employee::where('company_id', auth()->user()->company_id)->paginate(10);
        }
        return response()->json([
            'status' => true,
            'message' => 'Employees retrieved successfully',
            'data' => $employees
        ], 200);
    }
    public function detail($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        if (auth()->user()->role == 'manager') {
            if ($employee->company_id != auth()->user()->company_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        } else if (auth()->user()->role == 'employee') {
            if ($employee->company_id != auth()->user()->company_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Employee details retrieved successfully',
            'data' => $employee
        ], 200);
    }
    public function deleteCompany($id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found'
            ], 404);
        }

        // Delete all employees associated with the company
        Employee::where('company_id', $id)->delete();

        // Delete the company
        $company->delete();

        return response()->json([
            'status' => true,
            'message' => 'Company and associated employees deleted successfully'
        ], 200);
    }

    public function deleteEmployee($id)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        if (auth()->user()->role == 'manager') {
            if ($employee->company_id != auth()->user()->company_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        $employee->delete();

        return response()->json([
            'status' => true,
            'message' => 'Employee deleted successfully'
        ], 200);
    }
    public function restoreCompany($id)
    {
        $company = Company::withTrashed()->find($id);
        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found'
            ], 404);
        }

        // Restore the company
        $company->restore();

        // Restore all employees associated with the company
        Employee::withTrashed()
            ->where('company_id', $id)
            ->restore();

        return response()->json([
            'status' => true,
            'message' => 'Company and associated employees restored successfully'
        ], 200);
    }

    public function restoreEmployee($id)
    {
        $employee = Employee::withTrashed()->find($id);
        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        if (auth()->user()->role == 'manager') {
            if ($employee->company_id != auth()->user()->company_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        $employee->restore();

        return response()->json([
            'status' => true,
            'message' => 'Employee restored successfully'
        ], 200);
    }

}