@extends('layouts.app')

@section('title', 'Assigned Projects | Tanaman')

@section('content')

<style>
    :root {
        --primary-green: #319B72;
        --primary-dark: #277c5b;
        --text-dark: #334155;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --card-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .page-header p {
        color: var(--text-muted);
        margin: 4px 0 0;
        font-size: 0.9rem;
    }

    .search-wrapper {
        position: relative;
        max-width: 320px;
        width: 100%;
    }

    .search-input {
        width: 100%;
        padding: 10px 12px 10px 38px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        outline: none;
        font-size: 0.9rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .search-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 3px rgba(49, 155, 114, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .project-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }

    .project-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        overflow: hidden;
        height: 100%;
    }

    .project-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--card-shadow);
        border-color: #cbd5e1;
    }

    .card-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        gap: 10px;
    }

    .project-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.3;
    }

    .client-name {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .status-badge.active {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .status-badge.completed {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .project-description {
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 20px;
        display: -webkit-box;
        /* -webkit-line-clamp: 3; */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .meta-info {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px dashed #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .meta-item i {
        width: 16px;
        text-align: center;
        color: #94a3b8;
    }

    .meta-item.due-date i {
        color: #ef4444;
    }

    .meta-item.due-date span {
        color: #334155;
        font-weight: 600;
    }

    .card-footer {
        padding: 16px 20px;
        background-color: #fafafa;
        border-top: 1px solid var(--border-color);
    }

    .card-footer a {
        color: #fff;
    }

    .btn-view-details {
        display: block;
        width: 100%;
        text-align: center;
        background-color: var(--primary-green);
        color: white;
        padding: 10px 0;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: background-color 0.2s, transform 0.1s;
        border: none;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .btn-view-details:hover {
        background-color: var(--primary-dark);
        color: white;
    }

    .btn-view-details:active {
        transform: translateY(1px);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 2.5rem;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: var(--text-muted);
        font-size: 1rem;
    }
</style>

<div class="page-header">
    <div>
        <h2>Assigned Projects</h2>
        <p>Your active tasks and schedules</p>
    </div>

    <div class="search-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input type="text" id="projectSearch" class="search-input" placeholder="Search projects...">
    </div>
</div>

<div class="project-grid">
    @forelse($projects as $project)
        <div class="project-card" data-search="{{ strtolower($project->project_name . ' ' . ($project->project_location ?? '')) }}">
            
            <div class="card-body">
                <div class="card-top">
                    <div>
                        <h3 class="project-title">{{ $project->project_name }}</h3>
                        <div class="client-name">{{ $project->client->name ?? 'Unknown Client' }}</div>
                    </div>
                    
                    <span class="status-badge {{ $project->is_active ? 'active' : 'completed' }}">
                        {{ $project->is_active ? 'Active' : 'Done' }}
                    </span>
                </div>

                <div class="project-description">
                    {{ Str::limit($project->project_description ?? 'No description provided.', 200) }}
                </div>

                <div class="meta-info">
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $project->project_location ?? 'No location provided' }}</span>
                    </div>
                    <div class="meta-item due-date">
                        <i class="far fa-calendar-check"></i>
                        <span>Due: {{ \Carbon\Carbon::parse($project->project_end_date)->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('employee.panel', $project->id) }}" class="btn-view-details">
                    View Project Details
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>No projects assigned to you yet.</p>
        </div>
    @endforelse
</div>

@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('projectSearch');
    const cards = document.querySelectorAll('.project-card');

    searchInput.addEventListener('keyup', (e) => {
        const term = e.target.value.toLowerCase();
        
        cards.forEach(card => {
            const searchData = card.getAttribute('data-search');
            if (searchData.includes(term)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endpush