{{-- Shared design system CSS for all marketing pages --}}
<style>
:root{--bg:#000000;--surface:#0a0a0c;--emerald:#ff3333;--indigo:#3b82f6;--gold:#e2e8f0;--red:#EF4444;--border:rgba(255,255,255,0.06);--t1:#ffffff;--t2:rgba(255,255,255,.60);--t3:rgba(255,255,255,.30);--sans:'Inter Tight',system-ui,sans-serif;--serif:'Inter Tight',system-ui,sans-serif;--mono:'SF Mono','Cascadia Code','Consolas',monospace;--gold-border:rgba(226,232,240,.15);--gold-glow:rgba(226,232,240,.08)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}body{font-family:var(--sans);background:var(--bg);color:var(--t1);min-height:100vh;overflow-x:clip;-webkit-font-smoothing:antialiased}::selection{background:var(--t1);color:#000}
.tl{font-family:var(--mono);font-size:10px;letter-spacing:.12em;text-transform:uppercase;font-weight:600}
.bg-grid{position:fixed;inset:0;background:radial-gradient(#0c0c0e 1px,transparent 1px);background-size:24px 24px;opacity:.6;pointer-events:none;z-index:0}
.bg-g1{position:fixed;top:0;left:50%;transform:translateX(-50%);width:1200px;height:500px;pointer-events:none;z-index:0}
.bg-g2{position:fixed;bottom:-10%;left:15%;width:600px;height:600px;pointer-events:none;z-index:0}
@keyframes bF{0%{transform:translateY(0) translateX(0) scale(1);opacity:0}8%{opacity:1}50%{transform:translateY(-50vh) translateX(30px) scale(1.1)}85%{opacity:.5}100%{transform:translateY(-105vh) translateX(-20px) scale(.8);opacity:0}}
.bub{position:absolute;border-radius:50%;animation:bF linear infinite}
@keyframes headerSlideIn {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
@keyframes cascadeFadeIn {
  from {
    transform: translateY(-12px);
    opacity: 0;
    filter: blur(4px);
  }
  to {
    transform: translateY(0);
    opacity: 1;
    filter: blur(0);
  }
}
@keyframes borderGlowFlow {
  0% { background-position: 0% 50%; }
  100% { background-position: 200% 50%; }
}
@keyframes shine {
  0% { left: -100%; }
  100% { left: 200%; }
}
/* Header — gold bottom border, larger logo */
.hdr{position:sticky;top:0;z-index:50;width:100%;background:rgba(2,2,2,.80);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);box-shadow:0 4px 30px rgba(0,0,0,.4);animation:headerSlideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) both}
.hdr::after{content:'';position:absolute;bottom:0;left:0;width:100%;height:1px;background:linear-gradient(90deg,transparent,var(--gold-border) 15%,var(--gold) 50%,var(--emerald) 85%,transparent);background-size:200% 100%;animation:borderGlowFlow 8s linear infinite;opacity:0.8}
.hdr-i{max-width:1400px;margin:0 auto;padding:0 24px;height:72px;display:flex;align-items:center;justify-content:space-between}
.ll{display:flex;align-items:center;gap:14px;text-decoration:none;animation:cascadeFadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;animation-delay:calc(var(--i) * 0.05s + 0.15s);transition:transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)}
.ll:hover{transform:scale(1.02)}
.ll img{width:200px;height:auto;object-fit:contain;filter:brightness(1.1) drop-shadow(0 0 8px rgba(153,0,0,.15));transition:filter 0.3s ease}
.ll:hover img{filter:brightness(1.25) drop-shadow(0 0 12px rgba(255, 51, 51, 0.25))}
.lf{width:32px;height:32px;background:linear-gradient(135deg,var(--gold),var(--emerald));border-radius:6px;box-shadow:0 0 16px rgba(153,0,0,.2);transition:transform 0.3s ease}
.ll:hover .lf{transform:rotate(10deg) scale(1.05)}
.lt{font-weight:700;letter-spacing:.2em;font-size:15px;font-family:var(--serif);text-transform:uppercase;color:var(--gold);text-decoration:none;text-shadow:0 0 20px rgba(153,0,0,.15);transition:all 0.3s ease}
.ll:hover .lt{color:#fff;text-shadow:0 0 25px rgba(153,0,0,.4), 0 0 5px var(--gold)}
.nv{display:none;align-items:center;gap:22px;font-size:10px;font-family:var(--mono);letter-spacing:.12em;text-transform:uppercase}@media(min-width:768px){.nv{display:flex}}
.nv a, .nv .nav-dropdown{animation:cascadeFadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;animation-delay:calc(var(--i) * 0.05s + 0.15s)}
.nv a{position:relative;color:rgba(255, 255, 255, 0.65);text-decoration:none;padding:6px 0;transition:color 0.3s cubic-bezier(0.25, 1, 0.5, 1)}
.nv a::after{content:'';position:absolute;bottom:0;left:0;width:100%;height:1.5px;background:linear-gradient(90deg,var(--gold),var(--emerald));transform:scaleX(0);transform-origin:right;transition:transform 0.3s cubic-bezier(0.25, 1, 0.5, 1)}
.nv a:hover{color:#fff}
.nv a:hover::after{transform:scaleX(1);transform-origin:left}
.nv a.ac{color:var(--gold)}
.nv a.ac::after{transform:scaleX(1);background:var(--gold)}
.ha{display:flex;align-items:center;gap:16px}
.ha > a, .ha > button{animation:cascadeFadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;animation-delay:calc(var(--i) * 0.05s + 0.15s)}
.bl{font-size:12px;font-family:var(--mono);letter-spacing:.1em;color:rgba(255, 255, 255, 0.7);text-decoration:none;transition:color 0.3s ease, text-shadow 0.3s ease}
.bl:hover{color:var(--gold);text-shadow:0 0 8px rgba(153, 0, 0, 0.3)}
.bc{display:inline-block;padding:8px 20px;border-radius:6px;background:linear-gradient(135deg,var(--gold),#c4a030);color:#000;font-size:11px;font-family:var(--serif);letter-spacing:.1em;font-weight:700;text-decoration:none;transition:all 0.3s cubic-bezier(0.25, 1, 0.5, 1);box-shadow:0 4px 16px rgba(153,0,0,.2);position:relative;overflow:hidden}
.bc::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255, 255, 255, 0.3),transparent);transform:skewX(-25deg)}
.bc:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(153, 0, 0, 0.4), 0 0 0 1px rgba(153, 0, 0, 0.1)}
.bc:hover::before{animation:shine 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards}
.bc:active{transform:translateY(0);box-shadow:0 4px 12px rgba(153, 0, 0, 0.2)}
@keyframes fU{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.rv { opacity: 0; transform: translateY(15px); transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1); } .rv.vis { opacity: 1; transform: translateY(0); }
.rv:nth-child(2){transition-delay:.08s}.rv:nth-child(3){transition-delay:.16s}.rv:nth-child(4){transition-delay:.24s}.rv:nth-child(5){transition-delay:.32s}.rv:nth-child(6){transition-delay:.4s}
.ph{width:100%;margin:0 auto;padding:80px 24px 48px;text-align:center}
.ph h1{font-family:var(--serif);font-size:clamp(2rem,5vw,3.5rem);font-weight:400;line-height:1.05;animation:fU .6s ease both}
.ph h1 em{font-style:italic;background:linear-gradient(90deg,var(--gold),rgba(255,255,255,.7));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.ph p{font-size:15px;color:var(--t2);max-width:540px;margin:20px auto 0;line-height:1.7;animation:fU .7s ease both .1s}
.cw{max-width:1280px;margin:0 auto;padding:0 24px}
.gc{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:24px;transition:border-color .2s}.gc:hover{border-color:var(--gold-border)}
.mg{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:48px;justify-content:center}@media(min-width:768px){.mg{display:flex;flex-wrap:wrap;justify-content:center;gap:16px}.mg > *{flex:1;min-width:200px;max-width:320px}}
.mb{padding:16px;background:var(--surface);border:1px solid var(--border);border-radius:10px;text-align:center}
.mb .mv{font-family:var(--mono);font-size:22px;font-weight:600;display:block;margin-top:4px}
.te{color:var(--emerald)}.ti{color:var(--indigo)}.tg{color:var(--gold)}.tr{color:var(--red)}
.bp{display:inline-block;padding:12px 28px;border-radius:6px;border:none;background:linear-gradient(135deg,var(--gold),#c4a030);color:#000;font-family:var(--serif);font-size:12px;font-weight:700;letter-spacing:.08em;cursor:pointer;text-decoration:none;transition:all .25s;box-shadow:0 4px 16px rgba(153,0,0,.2)}.bp:hover{transform:translateY(-1px);box-shadow:0 6px 24px rgba(153,0,0,.3)}
.site-footer{width:100%;border-top:1px solid var(--gold-border);padding:48px 0;margin-top:0;text-align:center}
/* Mobile menu */
.mob-toggle{display:flex;flex-direction:column;gap:5px;background:none;border:none;cursor:pointer;padding:6px;z-index:101}@media(min-width:768px){.mob-toggle{display:none}}
.mob-toggle span{display:block;width:20px;height:2px;background:var(--gold);border-radius:2px;transition:transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s}
.mob-toggle.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}.mob-toggle.open span:nth-child(2){opacity:0}.mob-toggle.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
@keyframes mobileNavSlide{from{transform:translateY(-20px);opacity:0;backdrop-filter:blur(0px)}to{transform:translateY(0);opacity:1;backdrop-filter:blur(24px)}}
@media(max-width:767px){.nv.nv-open{display:flex;flex-direction:column;position:fixed;top:72px;left:0;right:0;bottom:0;background:rgba(2,2,2,0.98);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);padding:40px 32px;gap:24px;z-index:100;animation:mobileNavSlide 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;border-bottom:1px solid var(--gold-border)}.nv.nv-open a{font-size:16px;letter-spacing:.15em;opacity:0;animation:cascadeFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both}.nv.nv-open a:nth-child(1){animation-delay:0.1s}.nv.nv-open .nav-dropdown{opacity:0;animation:cascadeFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;animation-delay:0.15s}.nv.nv-open a:nth-child(3){animation-delay:0.2s}.nv.nv-open a:nth-child(4){animation-delay:0.25s}.nv.nv-open a:nth-child(5){animation-delay:0.3s}.nv.nv-open a:nth-child(6){animation-delay:0.35s}}
/* Navigation Dropdown */
.nav-dropdown{position:relative}.nav-dropdown::after{content:'';position:absolute;top:100%;left:50%;transform:translateX(-50%);width:220px;height:20px;background:transparent;z-index:59;display:none}.nav-dropdown:hover::after{display:block}.nav-dd-trigger{cursor:pointer;display:flex;align-items:center}
.dd-arrow{display:inline-block;font-size:8px;margin-left:4px;transition:transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)}.nav-dropdown:hover .dd-arrow{transform:rotate(180deg);color:var(--gold)}
.nav-dd-menu{display:block;position:absolute;top:100%;left:50%;transform:translateX(-50%) translateY(15px) scale(0.95);transform-origin:top center;min-width:200px;background:rgba(8, 8, 10, 0.96);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--gold-border);border-radius:10px;padding:10px 0;margin-top:12px;box-shadow:0 20px 50px rgba(0, 0, 0, 0.7);z-index:60;opacity:0;visibility:hidden;pointer-events:none;transition:opacity 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), visibility 0.3s}
.nav-dropdown:hover .nav-dd-menu{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0) scale(1);pointer-events:auto}
.nav-dd-menu::before{content:'';position:absolute;top:-6px;left:50%;transform:translateX(-50%) rotate(45deg);width:10px;height:10px;background:rgba(8,8,10,.96);border-top:1px solid var(--gold-border);border-left:1px solid var(--gold-border)}
.nav-dd-menu a{display:block;padding:10px 20px;font-family:var(--mono);font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,0.70);text-decoration:none;transform:translateX(0);transition:color 0.2s ease, background 0.2s ease, transform 0.25s cubic-bezier(0.25, 1, 0.5, 1), padding-left 0.25s ease;white-space:nowrap}
.nav-dd-menu a:hover{color:var(--gold);background:rgba(153,0,0,0.06);padding-left:24px;transform:translateX(4px)}
@media(max-width:767px){.nav-dropdown::after{display:none !important}.nav-dd-menu{position:static;transform:none !important;min-width:auto;background:transparent;border:none;box-shadow:none;padding:0 0 0 16px;margin-top:0;backdrop-filter:none;opacity:1 !important;visibility:visible !important;pointer-events:auto !important;transition:none !important}.nav-dd-menu::before{display:none}.nav-dropdown:hover .nav-dd-menu,.nav-dd-menu{display:block}.nav-dd-menu a{padding:8px 0;font-size:12px;opacity:1 !important;transform:none !important}.nav-dd-menu a:hover{padding-left:0;transform:none;background:transparent}}

/* Framer Motion Inspired CSS Spring Transitions */
@keyframes framerFadeInUp {
  0% { opacity: 0; transform: translateY(20px) scale(0.98); filter: blur(4px); }
  100% { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
}
@keyframes framerFadeInLeft {
  0% { opacity: 0; transform: translateX(-20px); }
  100% { opacity: 1; transform: translateX(0); }
}
@keyframes framerScaleIn {
  0% { opacity: 0; transform: scale(0.97); }
  100% { opacity: 1; transform: scale(1); }
}
.f-fade-up {
  opacity: 0;
  animation: framerFadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.f-fade-left {
  opacity: 0;
  animation: framerFadeInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.f-scale {
  opacity: 0;
  animation: framerScaleIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.f-delay-1 { animation-delay: 0.1s; }
.f-delay-2 { animation-delay: 0.2s; }
.f-delay-3 { animation-delay: 0.3s; }
.f-delay-4 { animation-delay: 0.4s; }
.f-delay-5 { animation-delay: 0.5s; }
.f-delay-6 { animation-delay: 0.6s; }
</style>
