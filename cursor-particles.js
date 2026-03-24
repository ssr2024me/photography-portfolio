// ── CURSOR + PARTICLE ENGINE — Photography-life4me ──
// Automatically loads settings from api.php and applies them.

(async function(){
  // Load settings — API returns { settings: {...} }
  let s = {};
  try {
    const data = await (await fetch('api.php?action=get_settings')).json();
    s = data.settings || {};
  } catch(e) {}

  const cursorStyle   = s.cursor_style   || 'golden-ring';
  const cursorColor   = s.cursor_color   || '#d4a853';
  const particleStyle = s.particle_style || 'none';
  const particleColor = s.particle_color || '#d4a853';
  const particleCount = parseInt(s.particle_count || '80');

  // ── CURSOR ENGINE ────────────────────────────────
  (function(){
    let style = cursorStyle, color = cursorColor;
    let rx=0, ry=0, mx=window.innerWidth/2, my=window.innerHeight/2;
    const trails=[];
    let ring, dot;

    function hexToRgb(h){
      const r=parseInt(h.slice(1,3),16),g=parseInt(h.slice(3,5),16),b=parseInt(h.slice(5,7),16);
      return `${r},${g},${b}`;
    }

    function initCursor(s,c){
      style=s; color=c;
      document.querySelectorAll('.custom-cursor,.cursor-trail').forEach(e=>e.remove());
      trails.length=0;
      if(s==='none'||!s){document.body.style.cursor='';return;}
      document.body.style.cursor='none';

      ring=document.createElement('div');
      ring.className='custom-cursor';
      dot=document.createElement('div');
      dot.className='custom-cursor';

      if(s==='golden-ring'||s==='magnetic-blob'){
        ring.style.cssText=`position:fixed;pointer-events:none;z-index:99999;border-radius:50%;border:1.5px solid ${color};transform:translate(-50%,-50%);transition:width .2s,height .2s;`;
        ring.style.width=s==='magnetic-blob'?'22px':'30px';
        ring.style.height=s==='magnetic-blob'?'22px':'30px';
        if(s==='magnetic-blob') ring.style.background=`rgba(${hexToRgb(color)},.12)`;
        dot.style.cssText=`position:fixed;pointer-events:none;z-index:100000;border-radius:50%;background:${color};width:5px;height:5px;transform:translate(-50%,-50%);`;
        document.body.appendChild(ring);
        document.body.appendChild(dot);
      }
      else if(s==='camera-shutter'){
        ring.style.cssText=`position:fixed;pointer-events:none;z-index:99999;border-radius:50%;border:1.5px dashed ${color};width:36px;height:36px;transform:translate(-50%,-50%);`;
        dot.style.cssText=`position:fixed;pointer-events:none;z-index:100000;border-radius:50%;border:1px solid ${color};width:10px;height:10px;transform:translate(-50%,-50%);`;
        document.body.appendChild(ring);
        document.body.appendChild(dot);
        document.addEventListener('click',()=>{
          const f=document.createElement('div');
          f.style.cssText=`position:fixed;inset:0;background:rgba(${hexToRgb(color)},.12);pointer-events:none;z-index:99998;animation:_cf .3s forwards`;
          document.head.insertAdjacentHTML('beforeend','<style>@keyframes _cf{0%{opacity:1}100%{opacity:0}}</style>');
          document.body.appendChild(f);setTimeout(()=>f.remove(),350);
        });
      }
      else if(s==='crosshair'){
        ring.innerHTML=`<svg width="36" height="36" viewBox="0 0 36 36" fill="none"><circle cx="18" cy="18" r="14" stroke="${color}" stroke-width="1"/><line x1="18" y1="2" x2="18" y2="10" stroke="${color}" stroke-width="1"/><line x1="18" y1="26" x2="18" y2="34" stroke="${color}" stroke-width="1"/><line x1="2" y1="18" x2="10" y2="18" stroke="${color}" stroke-width="1"/><line x1="26" y1="18" x2="34" y2="18" stroke="${color}" stroke-width="1"/><circle cx="18" cy="18" r="3" stroke="${color}" stroke-width="1"/></svg>`;
        ring.style.cssText=`position:fixed;pointer-events:none;z-index:99999;transform:translate(-50%,-50%);`;
        document.body.appendChild(ring);
        dot=null;
      }
      else if(s==='neon-trail'){
        ring=null; dot=null;
        for(let i=0;i<14;i++){
          const t=document.createElement('div');
          t.className='cursor-trail';
          const sz=Math.max(3,8-i*.35);
          t.style.cssText=`position:fixed;pointer-events:none;z-index:99999;border-radius:50%;background:${color};width:${sz}px;height:${sz}px;opacity:${1-i*.065};transform:translate(-50%,-50%);`;
          document.body.appendChild(t);
          trails.push({el:t,x:mx,y:my});
        }
        document.body.style.cursor='none';
      }
    }

    function animCursor(){
      if(ring&&ring.parentElement){
        rx+=(mx-rx)*.1; ry+=(my-ry)*.1;
        ring.style.left=rx+'px'; ring.style.top=ry+'px';
        if(style==='camera-shutter') ring.style.transform=`translate(-50%,-50%) rotate(${Date.now()/8}deg)`;
        if(style==='magnetic-blob'){
          const t=Date.now()/600;
          const sz=22+Math.sin(t)*5;
          ring.style.width=sz+'px'; ring.style.height=sz+'px';
        }
      }
      if(dot&&dot.parentElement){dot.style.left=mx+'px';dot.style.top=my+'px';}
      if(trails.length){
        for(let i=trails.length-1;i>0;i--){
          trails[i].x+=(trails[i-1].x-trails[i].x)*.35;
          trails[i].y+=(trails[i-1].y-trails[i].y)*.35;
          trails[i].el.style.left=trails[i].x+'px';
          trails[i].el.style.top=trails[i].y+'px';
        }
        trails[0].x=mx; trails[0].y=my;
        trails[0].el.style.left=mx+'px'; trails[0].el.style.top=my+'px';
      }
      requestAnimationFrame(animCursor);
    }

    document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;});
    animCursor();
    initCursor(style, color);
    window._initCursor=initCursor;
  })();

  // ── PARTICLE ENGINE ──────────────────────────────
  (function(){
    const cv=document.createElement('canvas');
    cv.style.cssText='position:fixed;inset:0;pointer-events:none;z-index:0;';
    document.body.appendChild(cv);
    const ctx=cv.getContext('2d');
    let W=window.innerWidth,H=window.innerHeight;
    cv.width=W; cv.height=H;
    window.addEventListener('resize',()=>{W=window.innerWidth;H=window.innerHeight;cv.width=W;cv.height=H;});

    let style=particleStyle, color=particleColor, count=particleCount;
    let pts=[], mx=W/2, my=H/2;
    document.addEventListener('mousemove',e=>{mx=e.clientX;my=e.clientY;});
    const bursts=[];
    document.addEventListener('click',e=>{
      if(style==='lens-flare') bursts.push({x:e.clientX,y:e.clientY,r:0,a:1});
    });

    function hexAlpha(h,a){
      const r=parseInt(h.slice(1,3),16),g=parseInt(h.slice(3,5),16),b=parseInt(h.slice(5,7),16);
      return `rgba(${r},${g},${b},${a})`;
    }

    function initParticles(s,c,n){
      style=s; color=c; count=n||80;
      pts=[];
      if(s==='none'){cv.style.display='none';return;}
      cv.style.display='block';
      for(let i=0;i<count;i++) pts.push({
        x:Math.random()*W,y:Math.random()*H,
        vx:(Math.random()-.5)*.4,vy:(Math.random()-.5)*.4,
        r:Math.random()*1.8+.4,phase:Math.random()*6.28,sp:Math.random()*.003+.001
      });
    }

    function draw(){
      if(style==='none'){requestAnimationFrame(draw);return;}
      const t=Date.now()*.001;
      ctx.clearRect(0,0,W,H);

      if(style==='constellation'){
        pts.forEach(p=>{
          p.x+=p.vx;p.y+=p.vy;
          if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;
          const a=.3+Math.sin(t+p.phase)*.2;
          ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
          ctx.fillStyle=hexAlpha(color,a);ctx.fill();
        });
        for(let i=0;i<pts.length;i++){
          for(let j=i+1;j<pts.length;j++){
            const dx=pts[i].x-pts[j].x,dy=pts[i].y-pts[j].y;
            const d=Math.sqrt(dx*dx+dy*dy);
            if(d<100){ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(pts[j].x,pts[j].y);ctx.strokeStyle=hexAlpha(color,.12*(1-d/100));ctx.lineWidth=.5;ctx.stroke();}
          }
          const dx=pts[i].x-mx,dy=pts[i].y-my,d=Math.sqrt(dx*dx+dy*dy);
          if(d<140){ctx.beginPath();ctx.moveTo(pts[i].x,pts[i].y);ctx.lineTo(mx,my);ctx.strokeStyle=hexAlpha(color,.45*(1-d/140));ctx.lineWidth=.8;ctx.stroke();}
        }
      }
      else if(style==='firefly'){
        ctx.fillStyle='rgba(0,0,0,.12)';ctx.fillRect(0,0,W,H);
        pts.forEach((p,i)=>{
          if(i<15){const dx=mx-p.x,dy=my-p.y,d=Math.sqrt(dx*dx+dy*dy);if(d>20){p.vx+=dx/d*.05;p.vy+=dy/d*.05;}}
          else{p.vx+=Math.sin(t*p.sp+p.phase)*.02;p.vy+=Math.cos(t*p.sp+p.phase)*.02;}
          p.vx*=.95;p.vy*=.95;p.x+=p.vx;p.y+=p.vy;
          if(p.x<0)p.x=W;if(p.x>W)p.x=0;if(p.y<0)p.y=H;if(p.y>H)p.y=0;
          const glow=.4+Math.sin(t*2+p.phase)*.4;
          const g=ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,p.r*6);
          g.addColorStop(0,hexAlpha(color,glow*.4));g.addColorStop(1,'transparent');
          ctx.beginPath();ctx.arc(p.x,p.y,p.r*6,0,Math.PI*2);ctx.fillStyle=g;ctx.fill();
          ctx.beginPath();ctx.arc(p.x,p.y,p.r*.8,0,Math.PI*2);ctx.fillStyle=hexAlpha(color,glow);ctx.fill();
        });
      }
      else if(style==='dark-matter'){
        ctx.fillStyle='rgba(0,0,0,.15)';ctx.fillRect(0,0,W,H);
        pts.forEach(p=>{
          const dx=mx-p.x,dy=my-p.y,d=Math.sqrt(dx*dx+dy*dy);
          if(d>25&&d<200){p.vx+=dx/d*.06;p.vy+=dy/d*.06;}
          p.vx*=.96;p.vy*=.96;p.x+=p.vx;p.y+=p.vy;
          if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;
          const spd=Math.sqrt(p.vx*p.vx+p.vy*p.vy);
          ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
          ctx.fillStyle=hexAlpha(color,Math.min(.8,spd*.25+.2));ctx.fill();
          if(spd>.5){ctx.beginPath();ctx.moveTo(p.x,p.y);ctx.lineTo(p.x-p.vx*3,p.y-p.vy*3);ctx.strokeStyle=hexAlpha(color,spd*.12);ctx.lineWidth=.8;ctx.stroke();}
        });
      }
      else if(style==='starfield'){
        pts.forEach(p=>{
          p.x+=p.vx*.2;p.y+=p.vy*.2;
          if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;
          const a=.2+Math.sin(t*1.5+p.phase)*.3;
          ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
          ctx.fillStyle=hexAlpha(color,a);ctx.fill();
        });
      }
      else if(style==='lens-flare'){
        pts.forEach(p=>{
          p.x+=p.vx*.15;p.y+=p.vy*.15;
          if(p.x<0||p.x>W)p.vx*=-1;if(p.y<0||p.y>H)p.vy*=-1;
          const a=.15+Math.sin(t*1.2+p.phase)*.25;
          ctx.beginPath();ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
          ctx.fillStyle=hexAlpha(color,a);ctx.fill();
          if(p.r>1.2){
            ctx.strokeStyle=hexAlpha(color,a*.5);ctx.lineWidth=.4;
            ctx.beginPath();ctx.moveTo(p.x-7,p.y);ctx.lineTo(p.x+7,p.y);ctx.stroke();
            ctx.beginPath();ctx.moveTo(p.x,p.y-7);ctx.lineTo(p.x,p.y+7);ctx.stroke();
          }
        });
        for(let i=bursts.length-1;i>=0;i--){
          const b=bursts[i];b.r+=3.5;b.a-=.018;
          if(b.a<=0){bursts.splice(i,1);continue;}
          ctx.beginPath();ctx.arc(b.x,b.y,b.r,0,Math.PI*2);
          ctx.strokeStyle=hexAlpha(color,b.a);ctx.lineWidth=1.2;ctx.stroke();
        }
      }
      requestAnimationFrame(draw);
    }
    draw();
    initParticles(style,color,count);
    window._initParticles=initParticles;
  })();

})();
