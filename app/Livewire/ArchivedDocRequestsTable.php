<?php

namespace App\Livewire;

use App\Http\Controllers\Documents\DocumentRequestController;use App\Models\ArchivedDocumentRequests;
use App\Models\DocumentRequestModel;
use Livewire\Component;
use Livewire\WithPagination;

class ArchivedDocRequestsTable extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'all';
    public $sort = 'default';

    protected $updatesQueryString = ['search', 'filter', 'sort'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filter = 'all';
        $this->sort = 'default';
        $this->resetPage();
    }

    public function render()
    {
        $query = DocumentRequestModel::query()->with(['claimer', 'studentInformation', 'documents']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                switch ($this->filter) {
                    case 'school':
                        $q->where('request_schl_entity', 'like', '%' . $this->search . '%');
                        break;
                    case 'reqno':
                        $q->where('req_no', 'like', '%' . $this->search . '%');
                        break;
                    case 'status':
                        $q->where('status', 'like', '%' . $this->search . '%');
                        break;
                    case 'all':
                    default:
                        $q->where('request_schl_entity', 'like', '%' . $this->search . '%')
                            ->orWhere('req_no', 'like', '%' . $this->search . '%')
                            ->orWhere('status', 'like', '%' . $this->search . '%')
                            ->orWhereHas('claimer', function ($subQ) {
                                $subQ->whereRaw("CONCAT(Fname, ' ', Lname) LIKE ?", ['%' . $this->search . '%']);
                            })
                            ->orWhereHas('studentInformation', function ($subQ) {
                                $subQ->whereRaw("CONCAT(FirstName, ' ', LastName) LIKE ?", ['%' . $this->search . '%']);
                            })
                            ->orWhereHas('documents', function ($subQ) {
                                $subQ->where('DocType', 'like', '%' . $this->search . '%');
                            });
                        break;
                }
            });
        }

        // Apply sorting
        switch ($this->sort) {
            case 'asc':
                $query->orderBy('req_no', 'asc');
                break;
            case 'desc':
                $query->orderBy('req_no', 'desc');
                break;
            case 'default':
            default:
                $query->orderBy('request_date', 'desc');
                break;
        }

        $requests = $query->paginate(10);

        return view('livewire.archived-doc-requests-table', [
            'requests' => $requests,
        ]);
    }
}
