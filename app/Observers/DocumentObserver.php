<?php

namespace App\Observers;

use App\Enums\DocumentType;
use App\Enums\LogAction;
use App\Models\Document;
use App\Services\ActionLogger;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        $employeeName = $document->employee->getFullName();
        $documentType = DocumentType::getLabel($document->document_type);

        ActionLogger::log(
            employeeId: $document->employee_id,
            action: LogAction::CREATE,
            description: "$employeeName created $documentType-type document entry with ID #{$document->id}",
            model: $document,
        );
    }

    /**
     * Handle the Document "updated" event.
     */
    public function updated(Document $document): void
    {
        $documentType = DocumentType::getLabel($document->document_type);
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $document->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName updated $documentType-type document of $ownerName with ID #{$document->id}",
            model: $document,
        );
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        $documentType = DocumentType::getLabel($document->document_type);
        $updaterEmployee = request()->user()->employee;
        $updaterEmployeeId = $updaterEmployee->id;
        $updaterName = $updaterEmployee->getFullName();
        $ownerName = $document->employee->getFullName();

        ActionLogger::log(
            employeeId: $updaterEmployeeId,
            action: LogAction::DELETE,
            description: "$updaterName deleted $documentType-type document of $ownerName with ID #{$document->id}",
            model: $document,
        );
    }

    /**
     * Handle the Document "restored" event.
     */
    public function restored(Document $document): void
    {
        //
    }

    /**
     * Handle the Document "force deleted" event.
     */
    public function forceDeleted(Document $document): void
    {
        //
    }
}
