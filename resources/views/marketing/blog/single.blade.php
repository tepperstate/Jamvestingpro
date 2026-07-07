<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$data->title}} | {{site()->name}}</title>
    <meta name="description" content="{{Str::limit(strip_tags($data->body), 160)}}">
    @if($data->category)<meta name="keywords" content="{{$data->category}}, market analysis, trading, finance, {{site()->name}}">@endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{$data->title}}">
    <meta property="og:description" content="{{Str::limit(strip_tags($data->body), 160)}}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    @if($data->image)<meta property="og:image" content="{{ Str::startsWith($data->image, ['http://', 'https://']) ? $data->image : asset('storage/image/'.$data->image) }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{$data->title}}">
    <meta name="twitter:description" content="{{Str::limit(strip_tags($data->body), 160)}}">
    @if($data->image)<meta name="twitter:image" content="{{ Str::startsWith($data->image, ['http://', 'https://']) ? $data->image : asset('storage/image/'.$data->image) }}">@endif
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
    
    /* Article Main */
    .article-main{max-width:760px}
    .back-link{font-family:var(--mono);font-size:10px;letter-spacing:.12em;text-transform:uppercase;color:var(--indigo);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .2s;margin-bottom:24px}.back-link:hover{color:var(--emerald)}
    
    .image-wrapper { position: relative; overflow: hidden; }
    .watermark { position: absolute; bottom: 12px; right: 12px; z-index: 5; opacity: 0.6; pointer-events: none; width: 120px; height: auto; }
    .hero-img-wrapper { position: relative; overflow: hidden; width: 100%; border-radius: 6px; margin-bottom: 32px; border: 1px solid var(--border); }
    .hero-img-wrapper .hero-img { border: none; margin-bottom: 0; width: 100%; display: block; border-radius: 0; }
    .hero-img-wrapper .watermark { width: 180px; bottom: 20px; right: 20px; }
    
    .hero-img{width:100%;height:auto;max-height:400px;object-fit:cover;border-radius:6px;margin-bottom:32px;border:1px solid var(--border)}
    .article-meta{display:flex;gap:16px;align-items:center;margin-bottom:24px;flex-wrap:wrap;padding-bottom:16px;border-bottom:1px solid var(--border)}
    .meta-author{font-size:13px;font-weight:600}.meta-date{font-family:var(--mono);font-size:11px;color:var(--t3)}
    .article-title{font-family:var(--serif);font-size:clamp(1.8rem,4vw,2.8rem);font-weight:400;line-height:1.15;margin-bottom:24px}
    .article-content{font-size:16px;line-height:1.8;color:var(--t2)}
    .article-content h1,.article-content h2,.article-content h3,.article-content h4{font-family:var(--serif);font-weight:500;color:var(--t1);margin:32px 0 12px}
    .article-content h2{font-size:24px;border-bottom:1px solid var(--border);padding-bottom:8px}
    .article-content h3{font-size:20px}.article-content h4{font-size:17px}
    .article-content p{margin:0 0 16px}
    .article-content a{color:var(--indigo);text-decoration:underline;text-underline-offset:3px;transition:color .2s}.article-content a:hover{color:var(--emerald)}
    .article-content blockquote{border-left:3px solid var(--emerald);padding:12px 20px;margin:20px 0;background:rgba(0,255,133,.02);border-radius:0 6px 6px 0}
    .article-content blockquote p{font-family:var(--serif);font-style:italic;color:var(--t2);margin:0}
    .article-content code{font-family:var(--mono);font-size:14px;background:rgba(255,255,255,.04);padding:2px 6px;border-radius:4px;border:1px solid rgba(255,255,255,.06)}
    .article-content pre{background:rgba(0,0,0,.6);border:1px solid var(--border);border-radius:6px;padding:16px 20px;overflow-x:auto;margin:16px 0}
    .article-content pre code{background:none;border:none;padding:0;font-size:13px;color:var(--emerald)}
    .article-content ul,.article-content ol{margin:12px 0;padding-left:24px;color:var(--t2)}
    .article-content li{margin:4px 0}
    .article-content img{max-width:100%;border-radius:6px;margin:16px 0;border:1px solid var(--border)}
    .share-bar{display:flex;gap:8px;margin-top:40px;padding-top:24px;border-top:1px solid var(--border);flex-wrap:wrap;align-items:center}
    .share-btn{padding:8px 16px;border-radius:6px;border:1px solid var(--border);background:var(--surface);color:var(--t2);font-family:var(--mono);font-size:10px;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;text-decoration:none;transition:all .2s}.share-btn:hover{border-color:rgba(79,70,229,.3);color:var(--t1)}
    
    /* Sidebar Styles */
    .sidebar { position: sticky; top: 120px; }
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
    
    .copy-toast{position:fixed;bottom:24px;right:24px;padding:10px 20px;background:var(--emerald);color:#000;font-family:var(--mono);font-size:11px;font-weight:700;letter-spacing:.1em;border-radius:6px;opacity:0;transform:translateY(10px);transition:all .3s;z-index:100;pointer-events:none}
    .copy-toast.show{opacity:1;transform:translateY(0)}

    .blog-category-badge { font-family: var(--mono); font-size: 9px; letter-spacing: 0.05em; text-transform: uppercase; padding: 2px 8px; border-radius: 4px; display: inline-block; font-weight: 500; }
    .blog-category-badge.course { background: rgba(153,0,0,0.08); color: var(--gold); border: 1px solid rgba(153,0,0,0.2); }
    .blog-category-badge.research { background: rgba(0,208,148,0.08); color: var(--emerald); border: 1px solid rgba(0,208,148,0.2); }
    .blog-category-badge.webinar { background: rgba(0,229,255,0.08); color: #00e5ff; border: 1px solid rgba(0,229,255,0.2); }
    .blog-category-badge.general { background: rgba(255,255,255,0.05); color: var(--t2); border: 1px solid rgba(255,255,255,0.1); }

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

    /* Motion Init */
    .back-link, .hero-img, .article-title, .article-meta, .article-content > *, .sidebar-section { opacity: 0; }
    </style>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "NewsArticle",
          "headline": "{{$data->title}}",
          "author": {"@type": "Person", "name": "{{$data->author ?? site()->name}}"},
          "datePublished": "{{$data->created_at->toIso8601String()}}",
          "dateModified": "{{$data->updated_at->toIso8601String()}}",
          "publisher": {"@type": "Organization", "name": "{{site()->name}}"},
          "image": "{{$data->image ? (Str::startsWith($data->image, ['http://', 'https://']) ? $data->image : asset('storage/image/'.$data->image)) : ''}}"
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
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": "{{$data->category ?? 'Article'}}",
              "item": "{{ route('public.blog.index', ['category' => $data->category]) }}"
            },
            {
              "@type": "ListItem",
              "position": 4,
              "name": "{{$data->title}}",
              "item": "{{ url()->current() }}"
            }
          ]
        }
      ]
    }
    </script>
</head>
<body>
    @include('marketing.partials.ambient')
    
    <header class="blog-header">
        <a href="{{url('/')}}" style="display: flex; align-items: center; text-decoration: none;">
            <x-ui.logo variant="light" size="sm" />
        </a>
        <nav class="blog-nav">
            <a href="{{ route('public.blog.index') }}" class="blog-nav-link">All Insights</a>
            @if(isset($categories))
                @foreach($categories as $cat)
                    <a href="{{ route('public.blog.index', ['category' => $cat]) }}" class="blog-nav-link {{ $data->category == $cat ? 'active' : '' }}">{{ $cat }}</a>
                @endforeach
            @endif
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

    <div class="page-wrap">
        <div class="brand-logo" style="text-align:center; margin-bottom: 28px; display:flex; justify-content:center; width:100%;">
            <a href="{{url('/')}}" class="logo-bg-premium" style="background-color:transparent; display:flex; align-items:center; justify-content:center; width:100%; min-height:80px; margin-bottom:20px;">
                <img src="{{asset('assets/img/favicon.svg')}}" alt="{{site()->name}}" style="width:100%; max-width:320px; height:auto; object-fit:contain; filter:drop-shadow(0 4px 6px rgba(0,0,0,0.3));">
            </a>
        </div>
        <div class="layout-grid">
            <article class="article-main">
                <a href="{{route('public.blog.index')}}" class="back-link">← Back to Insights</a>
                <div class="hero-img-wrapper">
                    @if($data->image)
                        <img src="{{ Str::startsWith($data->image, ['http://', 'https://']) ? $data->image : asset('storage/image/'.$data->image) }}" alt="{{$data->title}}" class="hero-img" loading="lazy" style="margin-bottom:0">
                    @else
                        <div class="hero-img" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--surface),rgba(79,70,229,.08));color:var(--t3);font-family:var(--mono);font-size:24px;letter-spacing:0.1em;height:400px;margin-bottom:0"><span class="tl">{{strtoupper(site()->name)}}</span></div>
                    @endif
                    @if(site()->logo)
                        <img src="{{ asset('assets/img/favicon.svg') }}" class="watermark" alt="{{site()->name}}">
                    @endif
                </div>
                <header>
                    <h1 class="article-title">{{$data->title}}</h1>
                    <div class="article-meta">
                        @if($data->category)
                            @php
                                $catClass = strtolower($data->category);
                            @endphp
                            <span class="blog-category-badge {{ $catClass }}">{{ $data->category }}</span>
                        @endif
                        @if($data->author)<span class="meta-author">{{$data->author}}</span>@endif
                        <span class="meta-date">{{$data->created_at->format('F d, Y')}}</span>
                        <span class="meta-date">{{ceil(str_word_count(strip_tags($data->body)) / 200)}} min read</span>
                    </div>
                </header>
                <div class="article-content">
                    {!! $data->body !!}
                </div>
                <div class="share-bar">
                    <span class="tl" style="color:var(--t3);margin-right:8px">Share:</span>
                    <a href="https://twitter.com/intent/tweet?text={{urlencode($data->title)}}&url={{urlencode(route('public.blog.show', $data->slug))}}" target="_blank" rel="noopener" class="share-btn">Twitter</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{urlencode(route('public.blog.show', $data->slug))}}" target="_blank" rel="noopener" class="share-btn">LinkedIn</a>
                    <button class="share-btn" onclick="copyLink()">Copy Link</button>
                </div>
                <div style="margin-top:32px"><a href="{{route('public.blog.index')}}" class="back-link">← All Insights</a></div>
            </article>

            <aside class="sidebar">
                @php
                    // Related and trending posts are passed from the controller/route
                @endphp
                
                @if($featuredPosts->count() > 0)
                <div class="sidebar-section">
                    <div class="sidebar-title">Featured Insights</div>
                    @foreach($featuredPosts as $post)
                    <a href="{{route('public.blog.show', $post->slug)}}" class="side-card">
                        @if($post->image)
                            <img src="{{ Str::startsWith($post->image, ['http://', 'https://']) ? $post->image : asset('storage/image/'.$post->image) }}" class="side-img" loading="lazy">
                        @else
                            <div class="side-img" style="display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--surface),rgba(79,70,229,.08));color:var(--t3);font-size:10px;"><span class="tl">{{strtoupper(site()->name)}}</span></div>
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

    <div class="copy-toast" id="copyToast">Link copied to clipboard</div>
    <script type="module">
    import { animate, stagger, inView } from "https://cdn.jsdelivr.net/npm/motion@11.11.13/+esm";
    
    // Copy link logic
    window.copyLink = function(){
        navigator.clipboard.writeText(window.location.href);
        const t=document.getElementById('copyToast');
        t.classList.add('show');
        setTimeout(()=>t.classList.remove('show'),2000);
    };

    // Staggered Article Animation
    const sequence = [
        [".back-link", { opacity: [0, 1], x: [-10, 0] }, { duration: 0.4, easing: "ease-out" }],
        [".hero-img", { opacity: [0, 1], scale: [0.97, 1] }, { duration: 0.6, easing: "ease-out", at: "-0.2" }],
        [".article-title", { opacity: [0, 1], y: [20, 0] }, { duration: 0.5, easing: "ease-out", at: "-0.4" }],
        [".article-meta", { opacity: [0, 1], y: [10, 0] }, { duration: 0.4, easing: "ease-out", at: "-0.3" }],
        [".sidebar-section", { opacity: [0, 1], x: [20, 0] }, { duration: 0.5, delay: stagger(0.1), easing: "ease-out", at: "-0.2" }]
    ];
    animate(sequence);

    // Stagger content blocks on scroll
    inView(".article-content", () => {
        animate(".article-content > *", 
            { opacity: [0, 1], y: [20, 0] }, 
            { duration: 0.5, delay: stagger(0.05), easing: "ease-out" }
        );
    });
    </script>
</body>
</html>
