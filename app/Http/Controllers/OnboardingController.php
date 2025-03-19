<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\DepartmentTeam;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use App\Models\User;
use App\Models\Employee;
use App\Models\FamilyInformation;
use App\Models\EducationInformation;
use App\Models\JobInformation;
use App\Models\OjtInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\UserRole;
use App\Models\CharacterReference;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class OnboardingController extends Controller
{
    public function showStep1(Request $request)
    {
        // If we're returning to step 1, retrieve data from session
        $data = $request->session()->get('onboarding_data.personal', []);
        return view('onboarding.step1', compact('data'));
    }
    
    public function processStep1(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'contact_number' => 'required|string|max:20',
            'birthdate' => 'required|date',
            'gender' => 'required|in:' . implode(',', Gender::values()),
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
        ]);

        // Store validated data in session
        $request->session()->put('onboarding_data.personal', $validated);
        
        // Move to step 2
        return redirect()->route('onboarding.step2');
    }
    
    public function showStep2(Request $request)
    {
        // Check if step 1 is completed
        if (!$request->session()->has('onboarding_data.personal')) {
            return redirect()->route('onboarding.step1');
        }
        
        $data = $request->session()->get('onboarding_data.family', []);
        return view('onboarding.step2', compact('data'));
    }
    
    public function processStep2(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'father_name' => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'number_of_children' => 'nullable|integer|min:0',
            'marital_status' => 'required|string',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_occupation' => 'nullable|string|max:255',
            'number_of_children' => 'nullable|integer|min:0',
        ]);
        
        // Store validated data in session
        $request->session()->put('onboarding_data.family', $validated);
        
        // Move to step 3
        return redirect()->route('onboarding.step3');
    }
    
    public function showStep3(Request $request)
    {
        // Check if previous steps are completed
        if (!$request->session()->has('onboarding_data.family')) {
            return redirect()->route('onboarding.step2');
        }
        
        $data = $request->session()->get('onboarding_data.job', []);
        return view('onboarding.step3', compact('data'));
    }
    
    public function processStep3(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'employment_type' => 'required|in:'.implode(',', EmploymentType::values()),
            'department' => 'required|in:'.implode(',', Department::values()),
            'department_team' => 'required|in:'.implode(',', DepartmentTeam::values()),
            'group_number' => 'nullable|integer|min:0',
            'date_of_start' => 'required|date',
            'date_of_orientation' => 'required|date',
        ]);
        
        // Store validated data in session
        $request->session()->put('onboarding_data.job', $validated);
        
        // Move to step 4
        return redirect()->route('onboarding.step4');
    }
    
    public function showStep4(Request $request)
    {
        // Check if previous steps are completed
        if (!$request->session()->has('onboarding_data.job')) {
            return redirect()->route('onboarding.step3');
        }
        
        $data = $request->session()->get('onboarding_data.education', []);
        return view('onboarding.step4', compact('data'));
    }
    
    public function processStep4(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'required_hours' => 'required|integer|min:1',
            'course' => 'nullable|string|max:255',
            'university_name' => 'nullable|string|max:255',
            'university_address' => 'nullable|string|max:255',
            'university_city' => 'nullable|string|max:255',
            'university_province' => 'nullable|string|max:255',
            'university_zip' => 'nullable|string|max:20',
        ]);
        
        // Store validated data in session
        $request->session()->put('onboarding_data.education', $validated);
        
        // Move to step 5
        return redirect()->route('onboarding.step5');
    }
    
    public function showStep5(Request $request)
    {
        // Check if previous steps are completed
        if (!$request->session()->has('onboarding_data.education')) {
            return redirect()->route('onboarding.step4');
        }
        
        $data = $request->session()->get('onboarding_data.ojt', []);
        return view('onboarding.step5', compact('data'));
    }
    
    public function processStep5(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'coordinator_name' => 'required|string|max:255',
            'coordinator_email' => 'nullable|email|max:255',
            'coordinator_phone' => 'nullable|string|max:20',
        ]);
        
        // Store validated data in session
        $request->session()->put('onboarding_data.ojt', $validated);
        
        // Move to step 6
        return redirect()->route('onboarding.step6');
    }
    
    public function showStep6(Request $request)
    {
        // Check if previous steps are completed
        if (!$request->session()->has('onboarding_data.ojt')) {
            return redirect()->route('onboarding.step6');
        }
        
        $data = $request->session()->get('onboarding_data.char_ref', []);
        return view('onboarding.step6', compact('data'));
    }
    
    public function processStep6(Request $request)
    {
        // Validate form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contact_number' => 'required|string|max:20',
            'relationship' => 'required|string|max:255',
            'name_of_employer' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'e_signature_path' => 'required|file|mimes:jpeg,jpg,png',
        ]);
        
        // Store validated data in session
        $request->session()->put('onboarding_data.char_ref', $validated);
        
        // Process final submission
        return $this->completeOnboarding($request);
    }
    
    public function completeOnboarding(Request $request)
    {
        // Retrieve all data from session
        $personalData = $request->session()->get('onboarding_data.personal');
        $familyData = $request->session()->get('onboarding_data.family');
        $jobData = $request->session()->get('onboarding_data.job');
        $educationData = $request->session()->get('onboarding_data.education');
        $ojtData = $request->session()->get('onboarding_data.ojt');
        $characterReferenceData = $request->session()->get('onboarding_data.char_ref');
        
        // Begin database transaction
        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'name' => $personalData['first_name'] . ' ' . $personalData['last_name'],
                'email' => $personalData['email'],
                'password' => Hash::make($personalData['password']),
                'roles' => [UserRole::EMPLOYEE->value],
            ]);
            
            // Create employee
            $employee = Employee::create([
                'user_id' => $user->id,
                'first_name' => $personalData['first_name'],
                'last_name' => $personalData['last_name'],
                'birthdate' => $personalData['birthdate'],
                'gender' => $personalData['gender'],
                'contact_number' => $personalData['contact_number'],
                'address' => $personalData['address'],
                'emergency_contact_name' => $personalData['emergency_contact_name'],
                'emergency_contact_number' => $personalData['emergency_contact_number'],
                'department' => $jobData['department'],
                'department_team' => $jobData['department_team'],
                'employment_type' => $jobData['employment_type'],
                'group_number' => $jobData['group_number'],
                'date_of_start' => $jobData['date_of_start'],
                'date_of_orientation_day' => $jobData['date_of_orientation_day'],
                'hide_date' => now(),
            ]);
            
            $employeeId = $employee->id;
            $prefixPath = "e-signatures/$employeeId";
            $eSignaturePath = $request->file('e_signature_path')->store($prefixPath, 'public');
            $employee->update(['e_signature_path' => $eSignaturePath]);
            
            // Create family information
            FamilyInformation::create(array_merge(
                ['employee_id' => $employee->id],
                $familyData
            ));
            
            // Create education information
            EducationInformation::create(array_merge(
                ['employee_id' => $employee->id],
                $educationData
            ));
            
            // Create OJT information
            OjtInformation::create(array_merge(
                ['employee_id' => $employee->id],
                $ojtData
            ));
            
            // Create character reference
            CharacterReference::create(array_merge(
                ['employee_id' => $employee->id],
                $characterReferenceData
            ));
            
            // Commit transaction
            DB::commit();
            
            // Clear session data
            $request->session()->forget('onboarding_data');
            
            // Log in the user
            Auth::login($user);
            
            return redirect()->route('dashboard')->with('success', 'Registration completed successfully!');
        } catch (\Exception $e) {
            // Rollback transaction
            DB::rollBack();
            
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    // Add this method to handle form cancellation
    public function cancel(Request $request)
    {
        $request->session()->forget('onboarding_data');
        return redirect()->route('login');
    }
}