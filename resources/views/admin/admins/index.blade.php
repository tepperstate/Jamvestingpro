@extends('layouts.admin.app')
@section('content')

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold outfit">Admin Management</h1>
        <a href="#" data-toggle="modal" data-target="#addAdminModal" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm glass-button">
            <i class="ri-user-add-line fa-sm text-white-50"></i> Add New Admin
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success-glass satin-border mb-4 d-flex align-items-center justify-content-between shadow-lg" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px; padding: 1rem 1.5rem; backdrop-filter: blur(10px);">
            <div class="d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.2) !important; border: 1px solid rgba(16, 185, 129, 0.5);">
                    <i data-lucide="check" class="text-success" style="width: 18px; color: #34d399 !important;"></i>
                </div>
                <span class="text-white font-weight-bold" style="text-shadow: 0 1px 2px rgba(0,0,0,0.5);">{{ session('success') }}</span>
            </div>
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close" style="opacity: 0.5;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger-glass satin-border mb-4 d-flex align-items-center justify-content-between" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 1rem 1.5rem;">
            <div class="d-flex align-items-center">
                <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 32px; height: 32px; background: #ef4444 !important;">
                    <i data-lucide="alert-circle" class="text-white" style="width: 18px;"></i>
                </div>
                <span class="text-white font-weight-bold">{{ session('error') }}</span>
            </div>
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close" style="opacity: 0.5;">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif    <div class="card glass-panel border-0 mb-4">
        <div class="card-header py-3 bg-transparent border-bottom border-secondary" style="border-bottom-color: rgba(255,255,255,0.05)!important;">
            <h6 class="m-0 font-weight-bold text-primary">System Administrators</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table text-white" id="dataTable" width="100%" cellspacing="0">
                    <thead style="background: rgba(255, 255, 255, 0.02);">
                        <tr>
                            <th class="border-0">Admin ID</th>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Role</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr style="background: transparent; border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <td class="align-middle">#{{ $admin->id }}</td>
                            <td class="align-middle fw-bold">{{ $admin->name }}</td>
                            <td class="align-middle text-muted">{{ $admin->email }}</td>
                            <td class="align-middle">
                                @if($admin->is_super_admin)
                                    <span class="badge badge-success" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);">Super Admin</span>
                                @else
                                    <span class="badge badge-secondary" style="background: rgba(255, 255, 255, 0.1); color: #ccc;">Standard</span>
                                @endif

                                @if($admin->is_2fa_exempt)
                                    <span class="badge badge-warning ml-1" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2);">2FA Exempt</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($admin->status == 1)
                                    <span class="badge badge-primary" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">Active</span>
                                @else
                                    <span class="badge badge-danger" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">Suspended</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <a href="#" data-toggle="modal" data-target="#editAdminModal{{$admin->id}}" class="btn btn-sm glass-panel text-info px-2 py-1 border-0" title="Edit Admin">
                                    <i class="ri-edit-2-line"></i>
                                </a>
                                @if(auth('admin')->id() != $admin->id)
                                <a href="{{ route('admin.admins.delete', $admin->id) }}" class="btn btn-sm glass-panel text-danger px-2 py-1 border-0" onclick="return confirm('Permanently delete this admin?')" title="Delete Admin">
                                    <i class="ri-delete-bin-line"></i>
                                </a>
                                @endif
                            </td>
                        </tr>

                        <!-- Edit Admin Modal -->
                        <div class="modal fade" id="editAdminModal{{$admin->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content glass-panel border-0 text-white" style="background: #0f172a;">
                                    <div class="modal-header border-bottom border-secondary" style="border-bottom-color: rgba(255,255,255,0.05)!important;">
                                        <h5 class="modal-title font-weight-bold">Edit Admin Account</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.admins.update') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $admin->id }}">
                                        <div class="modal-body">
                                            <div class="form-group mb-3">
                                                <label class="small text-muted text-uppercase font-weight-bold">Full Name</label>
                                                <input type="text" name="name" class="form-control bg-transparent text-white border-secondary" value="{{ $admin->name }}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label class="small text-muted text-uppercase font-weight-bold">Email Address</label>
                                                <input type="email" name="email" class="form-control bg-transparent text-white border-secondary" value="{{ $admin->email }}" required>
                                            </div>
                                            <div class="form-group mb-4">
                                                <label class="small text-muted text-uppercase font-weight-bold">New Password <span class="text-xs font-weight-normal">(Leave blank to keep current)</span></label>
                                                <input type="password" name="password" class="form-control bg-transparent text-white border-secondary" placeholder="Enter new password">
                                            </div>
                                            
                                            <hr style="border-color: rgba(255,255,255,0.05);">
                                            <div class="form-group mb-3 custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="is_super_admin" id="superAdminSwitch{{$admin->id}}" value="1" {{ $admin->is_super_admin ? 'checked' : '' }} {{ (auth('admin')->id() == $admin->id) ? 'disabled' : '' }}>
                                                <label class="custom-control-label" for="superAdminSwitch{{$admin->id}}">Super Admin Privileges</label>
                                            </div>
                                            <div class="form-group mb-3 custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="is_2fa_exempt" id="exemptSwitch{{$admin->id}}" value="1" {{ $admin->is_2fa_exempt ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="exemptSwitch{{$admin->id}}">Exempt from 2FA (Not Recommended)</label>
                                            </div>
                                            <div class="form-group custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="status" id="statusSwitch{{$admin->id}}" value="1" {{ $admin->status ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="statusSwitch{{$admin->id}}">Account Active</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top border-secondary" style="border-top-color: rgba(255,255,255,0.05)!important;">
                                            <button class="btn btn-secondary glass-button text-white" type="button" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary glass-button text-white" type="submit">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-panel border-0 text-white" style="background: #0f172a;">
            <div class="modal-header border-bottom border-secondary" style="border-bottom-color: rgba(255,255,255,0.05)!important;">
                <h5 class="modal-title font-weight-bold">Create Admin Account</h5>
                <button class="close text-white" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="{{ route('admin.admins.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="small text-muted text-uppercase font-weight-bold">Full Name</label>
                        <input type="text" name="name" class="form-control bg-transparent text-white border-secondary" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="small text-muted text-uppercase font-weight-bold">Email Address</label>
                        <input type="email" name="email" class="form-control bg-transparent text-white border-secondary" required>
                    </div>
                    <div class="form-group mb-4">
                        <label class="small text-muted text-uppercase font-weight-bold">Password</label>
                        <input type="password" name="password" class="form-control bg-transparent text-white border-secondary" required>
                    </div>
                    
                    <hr style="border-color: rgba(255,255,255,0.05);">
                    <div class="form-group mb-3 custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="is_super_admin" id="newSuperAdminSwitch" value="1">
                        <label class="custom-control-label" for="newSuperAdminSwitch">Super Admin Privileges</label>
                    </div>
                    <div class="form-group mb-3 custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="is_2fa_exempt" id="newExemptSwitch" value="1">
                        <label class="custom-control-label" for="newExemptSwitch">Exempt from 2FA (Not Recommended)</label>
                    </div>
                    <div class="form-group custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" name="status" id="newStatusSwitch" value="1" checked>
                        <label class="custom-control-label" for="newStatusSwitch">Account Active</label>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary" style="border-top-color: rgba(255,255,255,0.05)!important;">
                    <button class="btn btn-secondary glass-button text-white" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary glass-button text-white" type="submit">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
