<?php

use App\Enums\UserRole;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

describe('Login Controller', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'roles' => [UserRole::ADMIN->value],
        ]);
        $this->actingAs($this->user);
        $this->employee = Employee::factory()->create(['user_id' => $this->user->id]);
        Auth::logout();
    });

    it('logging in with incorrect details returns errors', function () {
        // Send a POST request with credentials that won't match any user.
        $response = $this->post('/authenticate', [
            'employee-number' => 999, // an employee number that won't match
            'remember-me'     => 'nonexistent@example.com', // used as email lookup
            'email'           => 'nonexistent@example.com', // for validation
            'password'        => 'wrongpassword',
        ]);
    
        // Assert that the session contains an error on the "email" key.
        $response->assertInvalid(['email']);
    });
    
    it('logging in with correct details returns success', function () {
        // Prepare the POST request data.
        // Note: We pass the user's email in the "remember-me" field because your controller
        // uses that to retrieve the email, and we pass the employee id in "employee-number".
        $this->post('/authenticate', [
            'employee-number' => $this->employee->id,
            'remember-me'     => 'on',  // used as email for lookup
            'email'           => $this->user->email,  // used for validation
            'password'        => 'password',          // plain text for authentication
        ]);
    
        // Assert that the user is authenticated.
        $this->assertAuthenticatedAs($this->user);
    });
    
    it('logging in with incorrect employee number returns errors', function () {
        $response = $this->post('/authenticate', [
            'employee-number' => -1,
            'remember-me'     => 'on',  // used as email for lookup
            'email'           => $this->user->email,  // used for validation
            'password'        => 'password',          // plain text for authentication
        ]);
    
        $response->assertInvalid(['email']);
    });
});

