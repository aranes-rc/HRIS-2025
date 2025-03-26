<?php

use App\Enums\DocumentType;
use App\Enums\RequestStatus;
use App\Enums\UserRole;
use App\Models\Document;
use App\Models\Employee;
use App\Models\User;

describe('Document Model', function () {
    beforeEach(function () {
        $user = User::factory()->create(['roles' => [UserRole::ADMIN->value]]);
        $this->actingAs($user);
        $this->employee = Employee::factory()->create(['user_id' => $user->id]);
    });

    it('can be created with valid attributes', function () {
        $document = Document::create([
            'employee_id' => $this->employee->id,
            'updated_by' => $this->employee->id,
            'document_type' => DocumentType::RESUME,
            'status' => RequestStatus::PENDING,
            'file_path' => 'documents/test.pdf',
        ]);
        
        expect($document)->toBeInstanceOf(Document::class)
            ->and($document->employee_id)->toBe($this->employee->id)
            ->and($document->document_type)->toBe(DocumentType::RESUME)
            ->and($document->status)->toBe(RequestStatus::PENDING)
            ->and($document->file_path)->toBe('documents/test.pdf');
    });

    it('casts document_type to DocumentType enum', function () {
        $document = Document::factory()->create([
            'document_type' => DocumentType::ID,
        ]);
        
        expect($document->document_type)->toBeInstanceOf(DocumentType::class)
            ->and($document->document_type)->toBe(DocumentType::ID);
    });
    
    it('casts status to RequestStatus enum', function () {
        $document = Document::factory()->create([
            'status' => RequestStatus::APPROVED,
        ]);
        
        expect($document->status)->toBeInstanceOf(RequestStatus::class)
            ->and($document->status)->toBe(RequestStatus::APPROVED);
    });
    
    it('belongs to an employee', function () {
        $document = Document::factory()->create([
            'employee_id' => $this->employee->id,
        ]);
        
        expect($document->employee)->toBeInstanceOf(Employee::class)
            ->and($document->employee->id)->toBe($this->employee->id);
    });
    
    it('belongs to an updater employee', function () {
        $document = Document::factory()->create([
            'updated_by' => $this->employee->id,
        ]);
        
        expect($document->updatedBy)->toBeInstanceOf(Employee::class)
            ->and($document->updatedBy->id)->toBe($this->employee->id);
    });
    
    describe('Status Methods', function () {
        it('can check if document is pending', function () {
            $pendingDocument = Document::factory()->create([
                'status' => RequestStatus::PENDING,
            ]);
            
            $approvedDocument = Document::factory()->create([
                'status' => RequestStatus::APPROVED,
            ]);
            
            expect($pendingDocument->isPending())->toBeTrue()
                ->and($approvedDocument->isPending())->toBeFalse();
        });
        
        it('can check if document is approved', function () {
            $approvedDocument = Document::factory()->create([
                'status' => RequestStatus::APPROVED,
            ]);
            
            $pendingDocument = Document::factory()->create([
                'status' => RequestStatus::PENDING,
            ]);
            
            expect($approvedDocument->isApproved())->toBeTrue()
                ->and($pendingDocument->isApproved())->toBeFalse();
        });
        
        it('can check if document is rejected', function () {
            $rejectedDocument = Document::factory()->create([
                'status' => RequestStatus::REJECTED,
            ]);
            
            $pendingDocument = Document::factory()->create([
                'status' => RequestStatus::PENDING,
            ]);
            
            expect($rejectedDocument->isRejected())->toBeTrue()
                ->and($pendingDocument->isRejected())->toBeFalse();
        });
    });
    
    describe('Scope Filters', function () {
        beforeEach(function () {
            // Create various test documents
            Document::factory()->create([
                'status' => RequestStatus::PENDING,
                'document_type' => DocumentType::RESUME,
            ]);
            
            Document::factory()->create([
                'status' => RequestStatus::APPROVED,
                'document_type' => DocumentType::RESUME,
            ]);
            
            Document::factory()->create([
                'status' => RequestStatus::REJECTED,
                'document_type' => DocumentType::ID,
            ]);
            
            Document::factory()->create([
                'status' => RequestStatus::PENDING,
                'document_type' => DocumentType::CONTRACT,
            ]);
        });
        
        it('can filter by status', function () {
            $pendingDocs = Document::filter(['status' => RequestStatus::PENDING])->get();
            $approvedDocs = Document::filter(['status' => RequestStatus::APPROVED])->get();
            $rejectedDocs = Document::filter(['status' => RequestStatus::REJECTED])->get();
            
            expect($pendingDocs)->toHaveCount(2)
                ->and($approvedDocs)->toHaveCount(1)
                ->and($rejectedDocs)->toHaveCount(1);
                
            // Verify all returned records have the correct status
            $pendingDocs->each(fn($doc) => expect($doc->status)->toBe(RequestStatus::PENDING));
            $approvedDocs->each(fn($doc) => expect($doc->status)->toBe(RequestStatus::APPROVED));
            $rejectedDocs->each(fn($doc) => expect($doc->status)->toBe(RequestStatus::REJECTED));
        });
        
        it('can filter by document type', function () {
            $resumeDocs = Document::filter(['document_type' => DocumentType::RESUME])->get();
            $idDocs = Document::filter(['document_type' => DocumentType::ID])->get();
            $contractDocs = Document::filter(['document_type' => DocumentType::CONTRACT])->get();
            
            expect($resumeDocs)->toHaveCount(2)
                ->and($idDocs)->toHaveCount(1)
                ->and($contractDocs)->toHaveCount(1);
                
            // Verify all returned records have the correct document type
            $resumeDocs->each(fn($doc) => expect($doc->document_type)->toBe(DocumentType::RESUME));
            $idDocs->each(fn($doc) => expect($doc->document_type)->toBe(DocumentType::ID));
            $contractDocs->each(fn($doc) => expect($doc->document_type)->toBe(DocumentType::CONTRACT));
        });
        
        it('can filter by both status and document type', function () {
            $pendingResumes = Document::filter([
                'status' => RequestStatus::PENDING,
                'document_type' => DocumentType::RESUME,
            ])->get();
            
            expect($pendingResumes)->toHaveCount(1)
                ->and($pendingResumes->first()->status)->toBe(RequestStatus::PENDING)
                ->and($pendingResumes->first()->document_type)->toBe(DocumentType::RESUME);
        });
        
        it('returns all records when no filters applied', function () {
            $allDocs = Document::filter([])->get();
            expect($allDocs)->toHaveCount(4);
        });
    });
});