<table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark" >
                            <tr>
                                <th>Request Number</th>
                                <th>Student</th>
                                <th>Document</th>
                                <th>School/Entity</th>
                                <th>Requested Via</th>
                                <th>Release Mode</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Approved Date</th>
                                <th>For Realease Date</th>
                                <th>Claimed Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($DocRequests as $item)
                            <tr>
                                <td>{{ $item->req_no }}</td>
                                <td>{{ $item->studentInformation->full_name }}</td>
                                <td>{{ $item->documents->DocType }}</td>
                                <td>{{ $item->request_schl_entity }}</td>
                                <td>{{ $item->request_mode }}</td>
                                <td>{{ $item->release_mode }}</td>
                                <td>{{ $item->remarks }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $item->status }}</span></td>
                                <td>{{ $item->request_date }}</td>
                                <td>{{ $item->approve_date }}</td>
                                <td>{{ $item->forRelease_date }}</td>
                                <td>{{ $item->claimed_date }}</td>
                                <td class="text-nowrap">
                                    @if(!empty($approvePending))
                                    <form action="{{ route('pending.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger mb-1">Decline</button>
                                    </form>

                                    <form action="{{ route('document-request.complete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success mb-1">Accept</button>
                                    </form>
                                    @endif

                                    @if(!empty($PermissionEdit))
                                    <a href="{{ route('pending.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1">Edit</a>
                                    @endif

                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#receiptModal{{ $item->id }}">
                                                Receipt
                                            </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $DocRequests->links() }}
                </div>
