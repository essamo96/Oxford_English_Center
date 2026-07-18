@extends('frontend.layouts.master')

@section('content')

{{-- Page Hero --}}
<section class="ox-pagehero" style="background-image: url('{{ url('assets/oxford/img/bg/contact-hero.jpg') }}');">
    <div class="ox-container">
        <div class="ox-pagehero__inner">
            <h1 class="ox-pagehero__title">Search Results</h1>
            <nav class="ox-breadcrumb" aria-label="breadcrumb">
                <ul>
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li>Search</li>
                </ul>
            </nav>
        </div>
    </div>
</section>

{{-- Search Results Section --}}
<section class="ox-section">
    <div class="ox-container">
        <div class="ox-grid ox-grid--split" style="grid-template-columns: 1fr; gap: var(--ox-s-12); max-width: 800px; margin: 0 auto;">
            
            <div class="ox-search-header" style="margin-bottom: 20px; text-align: center;">
                <h2>Results for: "{{ $query }}"</h2>
                <p>Found {{ $results->total() }} matches</p>
                
                <form action="{{ route('search.full') }}" method="GET" style="display: flex; gap: 8px; justify-content: center; margin-top: 20px;">
                    <input class="ox-input" type="text" name="q" value="{{ $query }}" placeholder="Search again..." style="max-width: 400px; width: 100%;">
                    <button type="submit" class="ox-btn ox-btn--primary">Search</button>
                </form>
            </div>

            @if($results->count() > 0)
                <div class="ox-search-list">
                    @foreach($results as $item)
                        <div class="ox-search-card" style="background: var(--ox-white); padding: 20px; border-radius: var(--ox-radius-md); box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 16px;">
                            <span class="ox-badge ox-badge--outline" style="font-size: 0.75rem; margin-bottom: 8px; display: inline-block;">{{ $item->model_type }}</span>
                            <h3 style="margin: 0 0 8px 0; font-size: 1.25rem;">
                                @if($item->model_type == 'Course / Page')
                                    <a href="{{ url('page/' . $item->link_identifier) }}" style="color: var(--ox-gray-900); text-decoration: none;">{{ $item->title }}</a>
                                @elseif($item->model_type == 'Main Menu')
                                    <a href="{{ url($item->link_identifier) }}" style="color: var(--ox-gray-900); text-decoration: none;">{{ $item->title }}</a>
                                @else
                                    <a href="{{ url('posts/' . $item->link_identifier) }}" style="color: var(--ox-gray-900); text-decoration: none;">{{ $item->title }}</a>
                                @endif
                            </h3>
                            <p style="color: var(--ox-gray-600); margin: 0; font-size: 0.95rem;">
                                {{ \Str::limit(strip_tags($item->description), 150) }}
                            </p>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="ox-pagination" style="margin-top: 30px; display: flex; justify-content: center;">
                    {{ $results->links() }}
                </div>
            @else
                <div class="ox-alert ox-alert--info" style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px;">
                    <h4>No results found</h4>
                    <p>We couldn't find any matches for "{{ $query }}". Please try using different keywords.</p>
                </div>
            @endif

        </div>
    </div>
</section>

@endsection
