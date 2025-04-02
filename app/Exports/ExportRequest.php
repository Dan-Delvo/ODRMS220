<?php

namespace App\Exports;

use App\Models\DocumentRequestModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportRequest implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {


        return DocumentRequestModel::with('claimer', 'studentInformation', 'documents')
            ->get()
            ->map(function ($item) {
                return [
                    'ID' => $item->id,
                    'Claimer' => $item->claimer->full_name ?? 'N/A',
                    'Student' => $item->studentInformation->full_name ?? 'N/A',
                    'Document' => $item->documents->DocType ?? 'N/A',
                    'School' => $item->request_schl_entity,
                    'Requested Via' => $item->request_mode,
                    'Release Mode' => $item->release_mode,
                    'Remarks' => $item->remarks,
                    'Status' => $item->status,
                ];
            });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Claimer',
            'Student',
            'Document',
            'School',
            'Requested Via',
            'Release Mode',
            'Remarks',
            'Status',
        ];
    }
}
