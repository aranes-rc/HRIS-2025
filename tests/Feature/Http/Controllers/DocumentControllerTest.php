<?php

use App\Enums\DocumentType;
use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\Document;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('DocumentController as Admin', function () {
    beforeEach(function () {
        Storage::fake('public');
        
        $user = User::factory()->create(['roles' => [UserRole::ADMIN->value]]);
        $this->actingAs($user);
        $this->employee = Employee::factory()->create(['user_id' => $user->id]);
    });

    it('can view all documents', function () {
        // Create some documents
        $documents = Document::factory()->count(3)->create();
        
        $response = $this->get(route('document.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('document.index');
        $response->assertViewHas('documents');
    });

    it('can view the create document form', function () {
        $response = $this->get(route('document.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('document.create');
    });

    it('can store a new document', function () {
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        $response = $this->post(route('document.store'), [
            'document_type' => DocumentType::CLEARANCE->value,
            'document_file' => $file,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'employee_id' => $this->employee->id,
            'document_type' => DocumentType::CLEARANCE->value,
            'status' => RequestStatus::PENDING->value,
        ]);
        Storage::disk('public')->assertExists('documents/' . $this->employee->id . '/' . DocumentType::CLEARANCE->value . '/' . $file->hashName());
    });

    it('can view a document', function () {
        $document = Document::factory()->create();
        
        $response = $this->get(route('document.show', $document));
        
        $response->assertStatus(200);
        $response->assertViewIs('document.show');
        $response->assertViewHas('document');
    });

    it('can edit a document', function () {
        $document = Document::factory()->create();
        
        $response = $this->get(route('document.edit', $document));
        
        $response->assertStatus(200);
        $response->assertViewIs('document.edit');
        $response->assertViewHas('document');
    });

    it('can update a document with a new file', function () {
        $document = Document::factory()->create([
            'document_type' => DocumentType::RESUME->value,
            'file_path' => 'old_path.pdf',
        ]);
        
        Storage::disk('public')->put($document->file_path, 'old content');
        
        $newFile = UploadedFile::fake()->create('new_document.pdf', 1000);
        
        $response = $this->put(route('document.update', $document), [
            'document_type' => DocumentType::ID->value,
            'document_file' => $newFile,
            'status' => RequestStatus::APPROVED->value,
        ]);
        
        $response->assertRedirect(route('document.show', $document));
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'document_type' => DocumentType::ID->value,
            'status' => RequestStatus::APPROVED->value,
        ]);
        
        // Old file should be deleted
        Storage::disk('public')->assertMissing($document->file_path);
        
        // New file should be stored
        $document->refresh();
        Storage::disk('public')->assertExists($document->file_path);
    });

    it('can update a document status without changing the file', function () {
        $document = Document::factory()->create([
            'status' => RequestStatus::PENDING->value,
            'file_path' => 'test_path.pdf',
        ]);
        
        Storage::disk('public')->put($document->file_path, 'test content');
        
        $response = $this->put(route('document.update', $document), [
            'document_type' => $document->document_type->value,
            'status' => RequestStatus::APPROVED->value,
        ]);
        
        $response->assertRedirect(route('document.show', $document));
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'status' => RequestStatus::APPROVED->value,
            'file_path' => 'test_path.pdf',
        ]);
        
        // File should still exist
        Storage::disk('public')->assertExists('test_path.pdf');
    });

    it('can delete a document', function () {
        $document = Document::factory()->create([
            'file_path' => 'test_delete.pdf',
        ]);
        
        Storage::disk('public')->put($document->file_path, 'delete me');
        
        $response = $this->delete(route('document.destroy', $document));
        
        $response->assertRedirect(route('document.index'));
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('public')->assertMissing($document->file_path);
    });
});

describe('DocumentController as HR', function () {
    beforeEach(function () {
        Storage::fake('public');
        
        $user = User::factory()->create(['roles' => [UserRole::HR->value]]);
        $this->actingAs($user);
        $this->employee = Employee::factory()->create(['user_id' => $user->id]);
    });

    it('can view all documents', function () {
        Document::factory()->count(3)->create();
        
        $response = $this->get(route('document.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('document.index');
    });

    it('can update document status', function () {
        $document = Document::factory()->create([
            'status' => RequestStatus::PENDING->value,
        ]);
        
        $response = $this->put(route('document.update', $document), [
            'document_type' => $document->document_type->value,
            'status' => RequestStatus::APPROVED->value,
        ]);
        
        $response->assertRedirect(route('document.show', $document));
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'status' => RequestStatus::APPROVED->value,
        ]);
    });
});

describe('DocumentController as Employee', function () {
    beforeEach(function () {
        Storage::fake('public');
        
        $user = User::factory()->create(['roles' => [UserRole::EMPLOYEE->value]]);
        $this->actingAs($user);
        $this->employee = Employee::factory()->create(['user_id' => $user->id]);
    });

    it('can only view own documents', function () {
        // Create another employee's documents
        $otherEmployee = Employee::factory()->create();
        Document::factory()->count(2)->create([
            'employee_id' => $otherEmployee->id,
        ]);
        
        // Create this employee's documents
        Document::factory()->count(3)->create([
            'employee_id' => $this->employee->id,
        ]);
        
        $response = $this->get(route('document.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('document.index');
        $response->assertViewHas('documents', function ($documents) {
            // Should only see their own documents
            return $documents->count() === 3 && 
                $documents->every(fn ($doc) => $doc->employee_id === $this->employee->id);
        });
    });

    it('cannot update document status', function () {
        $document = Document::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => RequestStatus::PENDING->value,
        ]);
        
        $response = $this->put(route('document.update', $document), [
            'document_type' => $document->document_type,
            'status' => RequestStatus::APPROVED->value,
        ]);
        
        // Status should remain unchanged
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'status' => RequestStatus::PENDING->value,
        ]);
    });

    it('can upload new document for self', function () {
        $file = UploadedFile::fake()->create('employee_doc.pdf', 1000);
        
        $response = $this->post(route('document.store'), [
            'document_type' => DocumentType::ID->value,
            'document_file' => $file,
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'employee_id' => $this->employee->id,
            'document_type' => DocumentType::ID->value,
            'status' => RequestStatus::PENDING->value,
        ]);
    });

    it('cannot access documents of other employees', function () {
        $otherEmployee = Employee::factory()->create();
        $document = Document::factory()->create([
            'employee_id' => $otherEmployee->id,
        ]);
        
        $this->get(route('document.show', $document))->assertForbidden();
        $this->get(route('document.edit', $document))->assertForbidden();
        $this->put(route('document.update', $document), ['document_type' => $document->document_type->value])->assertForbidden();
        $this->delete(route('document.destroy', $document))->assertForbidden();
    });
});