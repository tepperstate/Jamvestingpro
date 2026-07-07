<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sovereign Intelligence — Market & Tech Insights | {{site()->name}}</title>
    <meta name="description" content="Expert market analysis, trading strategies, and technology insights from {{site()->name}}. Stay ahead with institutional-grade research.">
    <meta name="keywords" content="market analysis, trading strategies, tech insights, finance blog, {{site()->name}}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="Sovereign Intelligence — Market & Tech Insights | {{site()->name}}">
    <meta property="og:description" content="Expert market analysis, trading strategies, and technology insights from {{site()->name}}. Stay ahead with institutional-grade research.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Sovereign Intelligence — Market & Tech Insights | {{site()->name}}">
    <meta name="twitter:description" content="Expert market analysis, trading strategies, and technology insights from {{site()->name}}. Stay ahead with institutional-grade research.">
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "CollectionPage",
          "@id": "{{ url()->current() }}",
          "url": "{{ url()->current() }}",
          "name": "Sovereign Intelligence Blog | {{site()->name}}",
          "description": "Expert market analysis, trading strategies, and technology insights.",
          "publisher": {
            "@type": "Organization",
            "name": "{{site()->name}}",
            "logo": {
              "@type": "ImageObject",
              "url": "{{ asset('assets/img/favicon.svg') }}"
            }
          }
        },
        {
          "@type": "BreadcrumbList",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Home",
              "item": "{{ url('/') }}"
            },
            {
              "@type": "ListItem",
              "position": 2,
              "name": "Insights",
              "item": "{{ route('public.blog.index') }}"
            }
          ]
        }
      ]
    }
    </script>
    <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
    @include('marketing.partials.design-system')
    <style>
    .page-wrap {
        max-width: 1200px;
        margin: 40px auto 80px;
        padding: 40px 24px 80px;
        position: relative;
        z-index: 10;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(40px);
        -webkit-backdrop-filter: blur(40px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 28px;
        box-shadow: 0 40px 80px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    .layout-grid{display:grid;grid-template-columns:1fr 340px;gap:64px;align-items:start}
    @media(max-width: 900px){.layout-grid{grid-template-columns:1fr;gap:48px}}
    
    .blog-grid{display:grid;grid-template-columns:1fr;gap:24px}@media(min-width:640px){.blog-grid{grid-template-columns:repeat(2,1fr)}}
    .blog-card{background:var(--surface);border:1px solid var(--border);border-radius:6px;overflow:hidden;transition:all .3s;cursor:pointer;text-decoration:none;color:inherit;display:block}
    .blog-card:hover{border-color:rgba(79,70,229,.3);transform:translateY(-3px);box-shadow:0 16px 40px rgba(0,0,0,.4)}
    .blog-img{width:100%;height:200px;object-fit:cover;filter:grayscale(30%) brightness(.8);transition:all .4s}.blog-card:hover .blog-img{filter:grayscale(0) brightness(1)}
    
    .image-wrapper { position: relative; overflow: hidden; }
    .watermark { position: absolute; bottom: 12px; right: 12px; z-index: 5; opacity: 0.6; pointer-events: none; width: 120px; height: auto; }
    .hero-img-wrapper { position: relative; overflow: hidden; width: 100%; border-radius: 8px; margin-bottom: 32px; }
    .hero-img-wrapper .watermark { width: 180px; bottom: 20px; right: 20px; }
    
    .blog-img-placeholder{width:100%;height:200px;background:linear-gradient(135deg,var(--surface),rgba(79,70,229,.08));display:flex;align-items:center;justify-content:center}
    .blog-body{padding:24px}
    .blog-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;flex-wrap:wrap;gap:4px}
    .blog-date{font-family:var(--mono);font-size:10px;color:var(--t3);letter-spacing:.1em}
    .blog-author{font-family:var(--mono);font-size:10px;color:var(--indigo);letter-spacing:.08em}
    .blog-title{font-family:var(--serif);font-size:20px;font-weight:500;line-height:1.3;margin-bottom:12px;transition:color .2s}.blog-card:hover .blog-title{color:var(--indigo)}
    .blog-excerpt{font-size:14px;color:var(--t3);line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .blog-cta{display:inline-block;margin-top:16px;font-family:var(--mono);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:var(--indigo);transition:color .2s}.blog-card:hover .blog-cta{color:var(--emerald)}
    .empty-state{text-align:center;padding:80px 24px;color:var(--t3)}
    .empty-state h3{font-family:var(--serif);font-size:24px;color:var(--t2);margin-bottom:8px}

    /* Sidebar Styles */
    .sidebar-section { margin-bottom: 40px; background: rgba(5,5,5,0.4); border: 1px solid rgba(255,255,255,0.05); padding: 24px; border-radius: 6px; }
    .sidebar-title { font-family: var(--mono); font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--gold); margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 12px; display: flex; align-items: center; gap: 8px; }
    .sidebar-title::before { content: ''; display: block; width: 6px; height: 6px; background: var(--emerald); border-radius: 50%; }
    
    .side-card { display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; text-decoration: none; transition: transform 0.3s; }
    .side-card:last-child { margin-bottom: 0; }
    .side-card:hover { transform: translateX(4px); }
    .side-card:hover .side-title { color: var(--indigo); }
    .side-img { width: 100%; height: 160px; object-fit: cover; border-radius: 6px; filter: grayscale(30%) brightness(0.8); transition: filter 0.3s; border: 1px solid rgba(255,255,255,0.05); }
    .side-card:hover .side-img { filter: grayscale(0) brightness(1); }
    .side-title { font-family: var(--serif); font-size: 17px; font-weight: 500; color: var(--t1); transition: color 0.2s; line-height: 1.3; }
    .side-meta { font-family: var(--mono); font-size: 10px; color: var(--t3); letter-spacing: 0.05em; }
    
    .trending-list { display: flex; flex-direction: column; gap: 20px; }
    .trending-item { display: flex; gap: 16px; align-items: flex-start; text-decoration: none; transition: transform 0.3s; }
    .trending-item:hover { transform: translateX(4px); }
    .trending-item:hover .side-title { color: var(--emerald); }
    .trending-num { font-family: var(--serif); font-size: 32px; font-weight: 300; color: var(--gold); opacity: 0.4; line-height: 1; transition: opacity 0.3s; }
    .trending-item:hover .trending-num { opacity: 1; }
    .trending-content { display: flex; flex-direction: column; gap: 4px; }

    /* Filter Bar & Badges */
    .filter-bar { display: flex; gap: 12px; margin-bottom: 32px; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 16px; flex-wrap: wrap; }
    .filter-btn { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); padding: 8px 16px; border-radius: 6px; color: var(--t2); cursor: pointer; font-family: var(--mono); font-size: 11px; letter-spacing: 0.05em; transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1); }
    .filter-btn:hover { border-color: rgba(255,255,255,0.2); background: rgba(255,255,255,0.04); color: #fff; }
    
    .filter-btn[data-filter="all"].active,
    .filter-btn[data-filter="Course"].active { border-color: var(--gold); background: rgba(153,0,0,0.08); color: var(--gold); box-shadow: 0 0 15px rgba(153,0,0,0.05); }
    .filter-btn[data-filter="Research"].active { border-color: var(--emerald); background: rgba(0,208,148,0.08); color: var(--emerald); box-shadow: 0 0 15px rgba(0,208,148,0.05); }
    .filter-btn[data-filter="Webinar"].active { border-color: #00e5ff; background: rgba(0,229,255,0.08); color: #00e5ff; box-shadow: 0 0 15px rgba(0,229,255,0.05); }

    .blog-category-badge { font-family: var(--mono); font-size: 9px; letter-spacing: 0.05em; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; display: inline-block; font-weight: 500; }
    .blog-category-badge.course { background: rgba(153,0,0,0.08); color: var(--gold); border: 1px solid rgba(153,0,0,0.2); }
    .blog-category-badge.research { background: rgba(0,208,148,0.08); color: var(--emerald); border: 1px solid rgba(0,208,148,0.2); }
    .blog-category-badge.webinar { background: rgba(0,229,255,0.08); color: #00e5ff; border: 1px solid rgba(0,229,255,0.2); }
    .blog-category-badge.general { background: rgba(255,255,255,0.05); color: var(--t2); border: 1px solid rgba(255,255,255,0.1); }

    /* Motion Init */
    .ph .tl, .ph h1, .ph p, .blog-card, .sidebar-section { opacity: 0; }
        /* Pagination styles */
        .pagination { display: flex; padding-left: 0; list-style: none; gap: 0.5rem; justify-content: center; align-items: center; }
        .page-link { position: relative; display: block; padding: 0.5rem 0.75rem; color: var(--t2); text-decoration: none; background-color: var(--b2); border: 1px solid var(--border); border-radius: 4px; transition: all 0.2s; }
        .page-link:hover { z-index: 2; color: var(--t1); background-color: var(--b3); border-color: var(--border); }
        .page-item.active .page-link { z-index: 3; color: var(--b1); background-color: var(--accent); border-color: var(--accent); }
        .page-item.disabled .page-link { color: var(--t3); pointer-events: none; background-color: var(--b2); border-color: var(--border); opacity: 0.5; }
        .page-item:first-child .page-link { border-top-left-radius: 4px; border-bottom-left-radius: 4px; }
        .page-item:last-child .page-link { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
        .pagination-wrapper { width: 100%; margin-top: 2rem; }
        
        /* Premium Header Styles */
        .blog-header { position: sticky; top: 0; z-index: 100; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; background: rgba(5, 5, 5, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .blog-nav { display: flex; gap: 20px; align-items: center; overflow-x: auto; scrollbar-width: none; }
        .blog-nav::-webkit-scrollbar { display: none; }
        .blog-nav-link { color: var(--t2); text-decoration: none; font-size: 13px; font-weight: 500; white-space: nowrap; transition: color 0.2s; }
        .blog-nav-link:hover, .blog-nav-link.active { color: var(--gold); }
        .header-actions { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }
        
        @media(max-width: 768px) {
            .blog-header { flex-direction: column; gap: 16px; align-items: flex-start; padding: 16px; }
            .header-actions { align-self: flex-end; width: 100%; justify-content: flex-end; }
        }
    </style>
</head>
<body>
    @include('marketing.partials.ambient')
    
    <header class="blog-header">
        <a href="{{url('/')}}" style="display: flex; align-items: center; text-decoration: none;">
            <x-ui.logo variant="light" size="sm" />
        </a>
        <nav class="blog-nav">
            <a href="{{ route('public.blog.index') }}" class="blog-nav-link {{ request('category') ? '' : 'active' }}">All Insights</a>
            @foreach($categories as $cat)
                <a href="{{ route('public.blog.index', ['category' => $cat]) }}" class="blog-nav-link {{ request('category') == $cat ? 'active' : '' }}">{{ $cat }}</a>
            @endforeach
        </nav>
        <div class="header-actions">
            @auth
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; padding: 8px 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; padding: 8px 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">Log In</a>
                <a href="{{ route('register') }}" class="btn btn-sm" style="background: var(--gold); color: #000; border-radius: 8px; font-weight: 600; padding: 8px 16px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">Get Started</a>
            @endauth
        </div>
    </header>

    <main style="position:relative;z-index:10">
        <div class="page-wrap">
            <div class="brand-logo" style="text-align:center; margin-bottom: 28px; display:flex; justify-content:center; width:100%;">
                <a href="{{url('/')}}" class="logo-bg-premium" style="background-color:transparent; display:flex; align-items:center; justify-content:center; width:100%; min-height:80px; margin-bottom:20px;">
                    <img src="{{asset('assets/img/favicon.svg')}}" alt="{{site()->name}}" style="width:100%; max-width:320px; height:auto; object-fit:contain; filter:drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
                </a>
            </div>
            <section class="ph" style="text-align:center; margin-bottom:40px;">
                <span class="tl" style="color:var(--indigo)">Market & Tech Insights</span>
                <h1>Sovereign Intelligence<br><em>Research Archive</em></h1>
                <p style="color:rgba(255,255,255,0.7); max-width:600px; margin:16px auto 0;">Institutional-grade analysis, actionable strategies, and market intelligence from our research desk. Stay informed. Stay ahead.</p>
            </section>
            <div class="layout-grid">
                <div class="main-content">
                    <div class="filter-bar">
                        <a href="{{ route('public.blog.index') }}" class="filter-btn {{ !request('category') ? 'active' : '' }}">All Insights</a>
                        @foreach($categories as $cat)
                            <a href="{{ route('public.blog.index', ['category' => $cat]) }}" class="filter-btn {{ request('category') == $cat ? 'active' : '' }}">{{ $cat }}</a>
                        @endforeach
                    </div>
                    <div class="blog-grid">
                        @forelse($data as $item)
                        @php
                            $catClass = strtolower($item->category ?? 'general');
                            $catLabel = $item->category ?? 'General';
                        @endphp
                        <a href="{{route('public.blog.show', $item->slug)}}" class="blog-card rv" data-category="{{ $item->category ?? 'General' }}">
                            <div class="image-wrapper">
                                @if($item->image)
                                    <img src="{{ Str::startsWith($item->image, ['http://', 'https://']) ? $item->image : asset('storage/image/'.$item->image) }}" alt="{{$item->title}}" class="blog-img" loading="lazy">
                                @else
                                    <div class="blog-img-placeholder"><span class="tl" style="color:var(--t3)">{{strtoupper(site()->name)}}</span></div>
                                @endif
                                @if(site()->logo)
                                    <img src="{{ asset('assets/img/favicon.svg') }}" class="watermark" alt="{{site()->name}}">
                                @endif
                            </div>
                            <div class="blog-body">
                                <div class="blog-meta">
                                    <span class="blog-date">{{$item->created_at->format('M d, Y')}}</span>
                                    <span class="blog-category-badge {{ $catClass }}">{{ $catLabel }}</span>
                                    @if($item->author)<span class="blog-author">{{$item->author}}</span>@endif
                                </div>
                                <h2 class="blog-title">{{$item->title}}</h2>
                                <p class="blog-excerpt">{{Str::limit(strip_tags($item->body), 130)}}</p>
                                <span class="blog-cta">Read Analysis →</span>
                            </div>
                        </a>
                        @empty
                        <div class="empty-state" style="grid-column:1/-1">
                            <h3>No Intelligence Reports Published Yet</h3>
                            <p>Our research team is preparing the first wave of market insights. Check back soon.</p>
                        </div>
                        @endforelse
                </div>
                
                <div class="pagination-wrapper mt-5">
                    {{ $data->links('pagination::bootstrap-4') }}
                </div>
                </div>

                <aside class="sidebar">
                    @php
                        // Featured and trending posts are passed from the controller/route
                    @endphp
                    
                    @if($featuredPosts->count() > 0)
                    <div class="sidebar-section">
                        <div class="sidebar-title">Featured Insights</div>
                        @foreach($featuredPosts as $post)
                        <a href="{{route('public.blog.show', $post->slug)}}" class="side-card">
                            @if($post->image)
                                <img src="{{ Str::startsWith($post->image, ['http://', 'https://']) ? $post->image : asset('storage/image/'.$post->image) }}" class="side-img" loading="lazy">
                            @endif
                            <div>
                                <div class="side-title">{{$post->title}}</div>
                                <div class="side-meta">{{$post->created_at->format('M d, Y')}}</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif

                    @if($trendingNews->count() > 0)
                    <div class="sidebar-section">
                        <div class="sidebar-title">Trending News</div>
                        <div class="trending-list">
                            @foreach($trendingNews as $index => $news)
                            <a href="{{route('public.blog.show', $news->slug)}}" class="trending-item">
                                <span class="trending-num">0{{$index + 1}}</span>
                                <div class="trending-content">
                                    <span class="side-title" style="font-size:15px">{{$news->title}}</span>
                                    <span class="side-meta">{{$news->created_at->format('M d, Y')}}</span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </main>
    <script type="module">
    import { animate, stagger, inView } from "https://cdn.jsdelivr.net/npm/motion@11.11.13/+esm";
    
    // Animate Header
    animate(".ph .tl", { opacity: [0, 1], y: [20, 0] }, { duration: 0.6, easing: "ease-out" });
    animate(".ph h1", { opacity: [0, 1], y: [20, 0] }, { duration: 0.6, delay: 0.1, easing: "ease-out" });
    animate(".ph p", { opacity: [0, 1], y: [20, 0] }, { duration: 0.6, delay: 0.2, easing: "ease-out" });

    // Animate Blog Cards on scroll
    inView(".layout-grid", () => {
        animate(".blog-card", 
            { opacity: [0, 1], y: [30, 0] }, 
            { duration: 0.5, delay: stagger(0.1), easing: "ease-out" }
        );
        animate(".sidebar-section", 
            { opacity: [0, 1], x: [20, 0] }, 
            { duration: 0.5, delay: stagger(0.15), easing: "ease-out" }
        );
    });
    </script>
</body>
</html>
