@extends('admin.dashboard')

@section('admin-content')
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 text-success">
                <i class="fas fa-search me-2"></i>Scan History
            </h1>
            <p class="text-muted mb-0">View all scan records</p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select" id="filterDisease">
                <option value="">All Diseases</option>
                <option value="Sigatoka">Sigatoka</option>
                <option value="Panama">Panama</option>
                <option value="Healthy">Healthy</option>
            </select>
            <input type="date" class="form-control" id="filterDate">
        </div>
    </div>
</div>

<div class="table-container mb-4">
    <div class="p-3 border-bottom">
        <h5 class="mb-0">Scan Records ({{ $scans->total() }})</h5>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Image</th>
                    <th>Disease</th>
                    <th>Confidence</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scans as $scan)
                <tr>
                    <td>
                        <small class="text-muted">#{{ str_pad($scan->id, 5, '0', STR_PAD_LEFT) }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($scan->user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $scan->user->profile_picture)))
                                <img src="{{ asset('uploads/profile_pictures/' . $scan->user->profile_picture) }}" 
                                     class="rounded-circle me-2" 
                                     style="width: 30px; height: 30px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center me-2" 
                                     style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $scan->user->name }}</div>
                                <small class="text-muted">{{ $scan->user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($scan->image_path)
                            @php
                                // Check if image exists in the new scans folder
                                $imageExists = file_exists(public_path('uploads/scans/' . $scan->image_path));
                                $imagePath = $imageExists ? 'uploads/scans/' . $scan->image_path : null;
                                
                                // If not, check old format (full path stored)
                                if (!$imageExists && file_exists(public_path($scan->image_path))) {
                                    $imagePath = $scan->image_path;
                                }
                            @endphp
                            
                            @if($imagePath)
                                <img src="{{ asset($imagePath) }}" 
                                     class="img-thumbnail" 
                                     style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal"
                                     data-image="{{ asset($imagePath) }}">
                            @else
                                <span class="text-muted">Image not found</span>
                            @endif
                        @else
                            <span class="text-muted">No image</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $scan->disease_color }}">
                            {{ $scan->disease_type }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-{{ $scan->disease_color }}" 
                                     style="width: <?php echo $scan->confidence; ?>%"></div>
                            </div>
                            <span>{{ $scan->confidence }}%</span>
                        </div>
                    </td>
                    <td>{{ $scan->created_at->format('M d, Y H:i') }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary view-details-btn" 
                                    data-scan-id="{{ $scan->id }}"
                                    title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-danger delete-scan-btn" 
                                    data-scan-id="{{ $scan->id }}"
                                    title="Delete Record">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-search fa-2x text-muted mb-2"></i>
                        <p class="text-muted">No scan records found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($scans->hasPages())
    <div class="p-3 border-top">
        {{ $scans->links() }}
    </div>
    @endif
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Scan Image">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Scan Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Details will be loaded here via AJAX -->
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-success mb-3"></i>
                    <p>Loading details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Scan Modal -->
<div class="modal fade" id="deleteScanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Scan Record
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this scan record?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" id="deleteScanForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Filter functionality
    document.getElementById('filterDisease').addEventListener('change', function(e) {
        filterScans();
    });
    
    document.getElementById('filterDate').addEventListener('change', function(e) {
        filterScans();
    });
    
    function filterScans() {
        const diseaseFilter = document.getElementById('filterDisease').value;
        const dateFilter = document.getElementById('filterDate').value;
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            if (row.classList.contains('no-data-row')) return;
            
            let showRow = true;
            
            if (diseaseFilter) {
                const diseaseBadge = row.querySelector('.badge');
                if (diseaseBadge && diseaseBadge.textContent.trim() !== diseaseFilter) {
                    showRow = false;
                }
            }
            
            if (dateFilter && showRow) {
                const dateCell = row.querySelector('td:nth-child(6)');
                if (dateCell) {
                    const scanDate = new Date(dateCell.textContent);
                    const filterDate = new Date(dateFilter);
                    if (scanDate.toDateString() !== filterDate.toDateString()) {
                        showRow = false;
                    }
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Image modal
    document.getElementById('imageModal').addEventListener('show.bs.modal', function(e) {
        const button = e.relatedTarget;
        const imageUrl = button.getAttribute('data-image');
        document.getElementById('modalImage').src = imageUrl;
    });
    
    // View details
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            const scanId = this.dataset.scanId;
            
            // Show loading state
            document.getElementById('detailsContent').innerHTML = `
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-2x text-success mb-3"></i>
                    <p>Loading details...</p>
                </div>
            `;
            
            // Fetch scan details via AJAX
            fetch(`/admin/scans/${scanId}/details`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch details');
                    }
                    return response.json();
                })
                .then(data => {
                    let preventiveMeasures = '';
                    
                    if (data.disease_type === 'Panama') {
                        preventiveMeasures = `
                            <div class="alert alert-info mt-3">
                                <h6>Preventive Measures for Panama Disease:</h6>
                                <ul class="mb-0">
                                    <li>Use certified disease-free planting materials.</li>
                                    <li>Implement strict quarantine measures.</li>
                                    <li>Improve drainage to reduce soil moisture.</li>
                                    <li>Apply fungicides specifically labeled for Panama wilt.</li>
                                    <li>Rotate crops and avoid continuous cultivation of susceptible hosts.</li>
                                    <li>Remove and destroy infected plants immediately.</li>
                                </ul>
                            </div>
                        `;
                    } else if (data.disease_type === 'Sigatoka') {
                        preventiveMeasures = `
                            <div class="alert alert-info mt-3">
                                <h6>Preventive Measures for Sigatoka Disease:</h6>
                                <ul class="mb-0">
                                    <li>Apply fungicides containing copper or chlorothalonil regularly.</li>
                                    <li>Ensure proper spacing between plants.</li>
                                    <li>Remove and destroy infected leaves immediately.</li>
                                    <li>Water at the base of the plant to avoid wetting foliage.</li>
                                    <li>Rotate fungicide applications to prevent resistance.</li>
                                </ul>
                            </div>
                        `;
                    } else if (data.disease_type === 'Healthy') {
                        preventiveMeasures = `
                            <div class="alert alert-success mt-3">
                                <h6>Healthy Plant Maintenance:</h6>
                                <ul class="mb-0">
                                    <li>Continue regular monitoring and inspection.</li>
                                    <li>Maintain proper nutrition and watering schedule.</li>
                                    <li>Practice good sanitation in the plantation.</li>
                                    <li>Implement preventive fungicide applications during high-risk periods.</li>
                                    <li>Regularly check for early signs of disease.</li>
                                </ul>
                            </div>
                        `;
                    }
                    
                    document.getElementById('detailsContent').innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Basic Information</h6>
                                <p><strong>Scan ID:</strong> #${data.id}</p>
                                <p><strong>User:</strong> ${data.user_name}</p>
                                <p><strong>Email:</strong> ${data.user_email}</p>
                                <p><strong>Scan Date:</strong> ${data.created_at}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Detection Results</h6>
                                <p><strong>Disease Type:</strong> 
                                    <span class="badge bg-${data.disease_color}">${data.disease_type}</span>
                                </p>
                                <p><strong>Confidence Level:</strong> ${data.confidence}%</p>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-${data.disease_color}" style="width: ${data.confidence}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                ${data.image_path ? 
                                    `<img src="${data.image_url}" class="img-fluid rounded shadow" alt="Scan Image" style="max-height: 300px;">` : 
                                    '<p class="text-muted">No image available</p>'
                                }
                            </div>
                        </div>
                        ${preventiveMeasures}
                    `;
                })
                .catch(error => {
                    document.getElementById('detailsContent').innerHTML = `
                        <div class="alert alert-danger">
                            <h6>Error</h6>
                            <p>Failed to load scan details: ${error.message}</p>
                        </div>
                    `;
                });
            
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        });
    });
    
    // Delete scan confirmation
    document.querySelectorAll('.delete-scan-btn').forEach(button => {
        button.addEventListener('click', function() {
            const scanId = this.dataset.scanId;
            document.getElementById('deleteScanForm').action = `/admin/scans/${scanId}`;
            new bootstrap.Modal(document.getElementById('deleteScanModal')).show();
        });
    });
</script>

<style>
    .table-container {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .table-container .table {
        margin-bottom: 0;
    }
    
    .table-container .table thead th {
        border-bottom: 2px solid #f0f0f0;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
    
    .table-container .table tbody tr:hover {
        background-color: rgba(25, 135, 84, 0.02);
    }
    
    .badge.bg-success {
        background-color: #198754 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .badge.bg-primary {
        background-color: #0d6efd !important;
    }
    
    .progress-bar.bg-success {
        background-color: #198754 !important;
    }
    
    .progress-bar.bg-warning {
        background-color: #ffc107 !important;
    }
    
    .progress-bar.bg-danger {
        background-color: #dc3545 !important;
    }
</style>
@endsection