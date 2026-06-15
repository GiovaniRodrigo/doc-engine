@extends('documentation-engine::layouts.default')

@section('content')
<div class="docs-catalog-container">
    <!-- Hero / Title Section -->
    <div class="docs-catalog-hero">
        <h1 class="docs-catalog-title">{{ __('documentation-engine::messages.title') }}</h1>
        <p class="docs-catalog-subtitle">{{ __('documentation-engine::messages.subtitle') }}</p>
        
        <!-- Search Wrapper -->
        <form method="GET" action="{{ url('/docs/search') }}" class="docs-catalog-search-wrapper">
            <button type="submit" class="docs-catalog-search-button" aria-label="{{ __('documentation-engine::messages.search') }}">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            <input 
                type="text" 
                name="q"
                id="docs-catalog-search" 
                class="docs-catalog-search-input" 
                placeholder="{{ __('documentation-engine::messages.search_placeholder') }}"
                aria-label="{{ __('documentation-engine::messages.search_aria_label') }}"
            >
        </form>
    </div>

    <!-- Stats Dashboard Grid -->
    <div class="docs-stats-grid">
        <div class="docs-stat-card" onclick="document.getElementById('docs-catalog-grid').scrollIntoView({ behavior: 'smooth' })">
            <div class="docs-stat-icon-wrapper">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div class="docs-stat-info">
                <div class="docs-stat-value">{{ $stats['total_docs'] }}</div>
                <div class="docs-stat-label">{{ __('documentation-engine::messages.active_documents') }}</div>
            </div>
        </div>

        <div class="docs-stat-card" onclick="const filters = document.querySelector('.docs-catalog-filters'); if (filters) filters.scrollIntoView({ behavior: 'smooth' });">
            <div class="docs-stat-icon-wrapper">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
            </div>
            <div class="docs-stat-info">
                <div class="docs-stat-value">{{ $stats['total_tags'] }}</div>
                <div class="docs-stat-label">{{ __('documentation-engine::messages.category_tags') }}</div>
            </div>
        </div>

        <div class="docs-stat-card" onclick="const card = document.querySelector('.docs-catalog-card'); if (card) card.scrollIntoView({ behavior: 'smooth' });">
            <div class="docs-stat-icon-wrapper">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div class="docs-stat-info">
                <div class="docs-stat-value" style="font-size: 1.15rem; font-weight: 700; margin-top: 6px;">
                    @if($stats['latest_update'])
                        {{ \Carbon\Carbon::parse($stats['latest_update'])->diffForHumans() }}
                    @else
                        N/A
                    @endif
                </div>
                <div class="docs-stat-label" style="margin-top: 4px;">{{ __('documentation-engine::messages.latest_update') }}</div>
            </div>
        </div>
    </div>

    <!-- Quick Filter Tags -->
    @if(!empty($popularTags))
    <div class="docs-catalog-filters">
        <span class="docs-filter-label">{{ __('documentation-engine::messages.quick_filters') }}</span>
        <button class="docs-filter-btn active" data-tag="all">{{ __('documentation-engine::messages.all') }}</button>
        @foreach($popularTags as $tag)
            <button class="docs-filter-btn" data-tag="{{ $tag }}">{{ $tag }}</button>
        @endforeach
    </div>
    @endif

    <!-- Catalog Card Grid -->
    <div class="docs-catalog-grid" id="docs-catalog-grid">
        @foreach($documents as $doc)
            @php
                $segments = explode('/', $doc['slug']);
                $category = count($segments) > 1 ? $segments[0] : __('documentation-engine::messages.general');
            @endphp
            <a 
                href="{{ url('/docs/' . $doc['slug']) }}" 
                class="docs-catalog-card" 
                data-slug="{{ $doc['slug'] }}"
                data-title="{{ strtolower($doc['title']) }}"
                data-tags="{{ implode(',', array_map('strtolower', $doc['tags'])) }}"
            >
                <div class="docs-card-header">
                    <span class="docs-card-category">{{ $category }}</span>
                    <span class="docs-card-version">v{{ strlen($doc['version']) === 36 ? substr($doc['version'], 0, 8) : $doc['version'] }}</span>
                </div>
                
                <h2 class="docs-card-title">{{ $doc['title'] }}</h2>
                <p class="docs-card-excerpt">{{ $doc['excerpt'] }}</p>
                
                <div class="docs-card-footer">
                    <div class="docs-card-tags">
                        @foreach(array_slice($doc['tags'], 0, 3) as $tag)
                            <span class="docs-card-tag-badge">{{ $tag }}</span>
                        @endforeach
                        @if(count($doc['tags']) > 3)
                            <span class="docs-card-tag-badge">+{{ count($doc['tags']) - 3 }}</span>
                        @endif
                    </div>
                    
                    <span class="docs-card-date">
                        @if($doc['createdAt'])
                            {{ \Carbon\Carbon::parse($doc['createdAt'])->format('d/m/Y') }}
                        @endif
                    </span>
                </div>
            </a>
        @endforeach

        <div class="docs-no-results" id="docs-no-results" style="display: none;">
            <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px; display: block; opacity: 0.6;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                <line x1="8" y1="11" x2="14" y2="11"></line>
            </svg>
            {{ __('documentation-engine::messages.empty_title') }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('docs-catalog-search');
        var cards = document.querySelectorAll('.docs-catalog-card');
        var filterBtns = document.querySelectorAll('.docs-filter-btn');
        var noResults = document.getElementById('docs-no-results');
        
        var currentSearch = '';
        var currentFilter = 'all';

        function filterCatalog() {
            var visibleCount = 0;

            cards.forEach(function(card) {
                var title = card.getAttribute('data-title') || '';
                var slug = card.getAttribute('data-slug') || '';
                var tags = card.getAttribute('data-tags') || '';
                
                var matchesSearch = title.includes(currentSearch) || 
                                    slug.includes(currentSearch) || 
                                    tags.includes(currentSearch);
                                    
                var matchesFilter = currentFilter === 'all' || 
                                    tags.split(',').includes(currentFilter);

                if (matchesSearch && matchesFilter) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.style.display = 'block';
            } else {
                noResults.style.display = 'none';
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                currentSearch = e.target.value.toLowerCase().trim();
                filterCatalog();
            });
        }

        filterBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                filterBtns.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                
                currentFilter = btn.getAttribute('data-tag').toLowerCase();
                filterCatalog();
            });
        });
    });
</script>
@endsection
