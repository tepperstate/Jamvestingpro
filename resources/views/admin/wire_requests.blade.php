@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <h3 class="page-title">Bank Wire Requests</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Wire Requests</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box glass-card">
                        <div class="box-header with-border">
                            <h4 class="box-title">Recent Submissions</h4>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-dark">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>User</th>
                                            <th>Bank Name</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $v)
                                        <tr>
                                            <td>{{ $v->created_at->diffForHumans() }}</td>
                                            <td>
                                                <strong>{{ $v->account_name }}</strong><br>
                                                <small class="text-secondary">{{ $v->user->email ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ $v->bank_name }}</td>
                                            <td class="text-success font-weight-bold">${{ number_format($v->amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $v->status == 'pending' ? 'warning' : ($v->status == 'approved' ? 'success' : 'danger') }}">
                                                    {{ strtoupper($v->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-primary btn-sm" onclick="viewDetails({{ $v->id }})">View</button>
                                                    <a href="{{ route('admin.wire_request.delete', $v->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this request?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $data->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modal for details (simplified for now) -->
<script>
    function viewDetails(id) {
        // Logic to show modal with full details and notes
        alert('Internal Request ID: ' + id + '\nCheck database for full notes.');
    }
</script>
@endsection
