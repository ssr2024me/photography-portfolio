<?php
require_once 'config.php';
$admin_key = $_POST['admin_key'] ?? $_GET['key'] ?? '';
$valid_key = defined('ADMIN_KEY') ? ADMIN_KEY : //pass;
if ($admin_key !== $valid_key) {
    if (isset($_POST['admin_key'])) $error = "❌ Wrong admin key!";
    ?><!DOCTYPE html><html><head><title>Bulk Upload</title><meta charset="UTF-8">
    <style>body{font-family:Arial;max-width:400px;margin:100px auto;padding:20px;background:#1a1a2e;color:#eee}h2{color:#e94560;text-align:center}input{width:100%;padding:12px;margin:8px 0;background:#16213e;color:#eee;border:1px solid #0f3460;border-radius:5px;font-size:15px}button{background:#e94560;color:white;padding:13px;border:none;border-radius:5px;cursor:pointer;font-size:16px;width:100%;margin-top:10px}.error{color:#ff6b6b;background:#2d1b1b;padding:10px;border-radius:5px;margin-bottom:10px;text-align:center}.lock{text-align:center;font-size:50px;margin-bottom:10px}</style>
    </head><body><div class="lock">🔐</div><h2>Bulk Upload</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST"><input type="password" name="admin_key" placeholder="Admin Key..." autofocus><button type="submit">Login →</button></form>
    </body></html><?php exit;
}
$cats = ['General','Nature','Portrait','Landscape','Macro','Street','Architecture','Wildlife','Travel','Abstract','Fashion','Sports','Food','Event','Other'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Bulk Photo Upload</title>
<meta charset="UTF-8">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#1a1a2e;color:#eee;min-height:100vh}
.header{background:#16213e;padding:15px 25px;border-bottom:2px solid #e94560;display:flex;align-items:center;gap:12px}
.header h1{font-size:20px;color:#e94560}
.badge{background:#0f3460;padding:4px 12px;border-radius:20px;font-size:13px;color:#4ecca3}
.steps{display:flex;margin:20px 25px}
.step{flex:1;text-align:center;padding:10px;border-bottom:3px solid #0f3460;color:#666;font-size:13px;transition:all .3s}
.step.active{border-bottom-color:#e94560;color:#e94560;font-weight:bold}
.step.done{border-bottom-color:#4ecca3;color:#4ecca3}
.page{display:none;padding:20px 25px}
.page.active{display:block}
.upload-area{border:2px dashed #0f3460;border-radius:12px;padding:60px 20px;text-align:center;cursor:pointer;transition:all .3s;background:#16213e}
.upload-area:hover,.upload-area.drag{border-color:#e94560;background:#1f2b50}
.upload-area .ico{font-size:60px;margin-bottom:15px}
.upload-area h3{font-size:20px;margin-bottom:8px}
.upload-area p{color:#888;font-size:14px;margin-top:6px}
input[type="file"]{display:none}
.defaults-bar{background:#16213e;border-radius:10px;padding:12px 18px;margin:15px 0;display:flex;gap:12px;align-items:center;flex-wrap:wrap}
.defaults-bar label{font-size:13px;color:#aaa;white-space:nowrap}
.defaults-bar select,.defaults-bar input[type="text"]{background:#0f3460;color:#eee;border:1px solid #1a4080;border-radius:5px;padding:6px 10px;font-size:13px}
.apply-btn{background:#0f3460;color:#4ecca3;border:1px solid #4ecca3;border-radius:5px;padding:6px 14px;cursor:pointer;font-size:13px;white-space:nowrap}
.apply-btn:hover{background:#4ecca3;color:#1a1a2e}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:15px;margin:15px 0}
.card{background:#16213e;border-radius:10px;overflow:hidden;border:1px solid #0f3460;transition:border-color .2s;position:relative}
.card:hover{border-color:#e94560}
.card .thumb{width:100%;height:160px;object-fit:cover;display:block}
.card .body{padding:12px}
.card .fsz{font-size:11px;color:#888;margin-bottom:8px}
.card input[type="text"]{width:100%;background:#0f3460;color:#eee;border:1px solid #1a4080;border-radius:5px;padding:7px 10px;font-size:13px;margin-bottom:8px}
.card input[type="text"]:focus{outline:none;border-color:#e94560}
.card select{width:100%;background:#0f3460;color:#eee;border:1px solid #1a4080;border-radius:5px;padding:7px 10px;font-size:13px;margin-bottom:8px}
.card .row{display:flex;gap:8px;align-items:center;margin-bottom:6px}
.card .row label{font-size:12px;color:#aaa;white-space:nowrap}
.card .row input[type="checkbox"]{margin:0}
.rm-btn{background:#2d1b1b;color:#ff6b6b;border:none;border-radius:4px;padding:4px 10px;cursor:pointer;font-size:12px;float:right}
.rm-btn:hover{background:#ff6b6b;color:white}
.sbadge{display:none;position:absolute;top:8px;right:8px;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:bold}
.card.uploading .sbadge,.card.success .sbadge,.card.failed .sbadge{display:block}
.card.uploading{opacity:.7}.card.uploading .sbadge{background:#0f3460;color:#4ecca3}
.card.success{border-color:#4ecca3}.card.success .sbadge{background:#1b3a2d;color:#4ecca3}
.card.failed{border-color:#ff6b6b}.card.failed .sbadge{background:#2d1b1b;color:#ff6b6b}
.bottom-bar{position:sticky;bottom:0;background:#16213e;border-top:2px solid #0f3460;padding:12px 25px;display:flex;gap:12px;align-items:center;justify-content:space-between}
.cinfo{font-size:14px;color:#aaa}
.cinfo span{color:#4ecca3;font-weight:bold}
.btn{padding:10px 25px;border:none;border-radius:6px;cursor:pointer;font-size:15px;font-weight:bold;transition:all .2s}
.btn-p{background:#e94560;color:white}
.btn-p:hover{background:#c73652}
.btn-p:disabled{background:#555;cursor:not-allowed}
.btn-s{background:#0f3460;color:#eee}
.btn-s:hover{background:#1a4080}
.prog-sec{background:#16213e;border-radius:10px;padding:25px;margin:15px 0}
.pbwrap{background:#0f3460;border-radius:20px;height:24px;overflow:hidden;margin:15px 0}
.pbfill{height:100%;background:linear-gradient(90deg,#e94560,#ff8fa3);border-radius:20px;width:0%;transition:width .4s}
.ptxt{font-size:18px;font-weight:bold;color:#4ecca3;margin-bottom:5px}
.psub{color:#888;font-size:14px}
.logbox{background:#0f1b30;border-radius:8px;padding:15px;max-height:350px;overflow-y:auto;margin-top:15px}
.ll{padding:4px 0;font-size:13px;border-bottom:1px solid #1a2a45}
.ll.ok{color:#4ecca3}.ll.err{color:#ff6b6b}.ll.info{color:#aaa}
.sumbox{background:#16213e;border-radius:10px;padding:30px;text-align:center}
.sumico{font-size:60px;margin-bottom:10px}
.stats{display:flex;gap:20px;justify-content:center;margin:20px 0}
.stat{background:#0f3460;border-radius:8px;padding:15px 25px}
.stat .num{font-size:30px;font-weight:bold}
.stat .lbl{font-size:12px;color:#aaa}
.stat.green .num{color:#4ecca3}.stat.red .num{color:#ff6b6b}
.alert{background:#1b3a1b;border:1px solid #4ecca3;border-radius:8px;padding:12px 15px;margin:10px 0;font-size:14px;color:#4ecca3}
.alert.warn{background:#2d1b1b;border-color:#ff6b6b;color:#ff6b6b}
</style>
</head>
<body>

<div class="header">
    <span class="badge">📸</span>
    <h1>Bulk Photo Upload</h1>
    <span class="badge" id="hcount">0 photos</span>
</div>

<div class="steps">
    <div class="step active" id="s1">① Photos Select</div>
    <div class="step" id="s2">② Title / Category Edit</div>
    <div class="step" id="s3">③ Upload</div>
    <div class="step" id="s4">④ Done!</div>
</div>

<!-- PAGE 1 -->
<div class="page active" id="p1">
    <div class="upload-area" id="uarea" onclick="document.getElementById('fi').click()">
        <div class="ico">📁</div>
        <h3>Yahan click karo — Photos select karo</h3>
        <p>Ya files drag & drop karo</p>
        <p style="color:#4ecca3;font-weight:bold;margin-top:15px">Ctrl+A = Saari select &nbsp;|&nbsp; Ctrl+Click = Kuch select</p>
        <p>JPG, PNG, WEBP — Max 15MB each</p>
    </div>
    <input type="file" id="fi" multiple accept="image/*">
</div>

<!-- PAGE 2 -->
<div class="page" id="p2">
    <div class="defaults-bar">
        <label>⚡ Sabhi photos pe ek saath apply karo:</label>
        <label>Category:</label>
        <select id="dcat"><?php foreach($cats as $c) echo "<option>$c</option>"; ?></select>
        <label>Watermark:</label>
        <input type="text" id="dwm" value="Photography-life4me" style="width:180px">
        <button class="apply-btn" onclick="applyAll()">✅ Apply to All</button>
    </div>
    <div id="grid" class="grid"></div>
</div>

<!-- PAGE 3 -->
<div class="page" id="p3">
    <div class="prog-sec">
        <div class="ptxt" id="ptxt">Shuru ho raha hai...</div>
        <div class="psub" id="psub">Please wait...</div>
        <div class="pbwrap"><div class="pbfill" id="pbf"></div></div>
        <div id="cfile" style="font-size:13px;color:#888;margin-top:5px"></div>
    </div>
    <div class="logbox" id="logbox"></div>
</div>

<!-- PAGE 4 -->
<div class="page" id="p4">
    <div class="sumbox">
        <div class="sumico" id="sico">🎉</div>
        <h2 id="stitle">Upload Complete!</h2>
        <div class="stats">
            <div class="stat green"><div class="num" id="sok">0</div><div class="lbl">✅ Success</div></div>
            <div class="stat red"><div class="num" id="sfail">0</div><div class="lbl">❌ Failed</div></div>
            <div class="stat"><div class="num" id="stotal">0</div><div class="lbl">📸 Total</div></div>
        </div>
        <div id="smsg" class="alert"></div>
        <div style="margin-top:20px;display:flex;gap:10px;justify-content:center">
            <button class="btn btn-s" onclick="location.reload()">🔄 Aur Upload Karo</button>
            <button class="btn btn-p" onclick="window.open('/','_blank')">🌐 Website Dekho</button>
        </div>
    </div>
</div>

<!-- Bottom Bar -->
<div class="bottom-bar" id="bbar" style="display:none">
    <div class="cinfo"><span id="sc">0</span> photos | <span id="rc">0</span> ready</div>
    <div style="display:flex;gap:10px">
        <button class="btn btn-s" id="backb" onclick="goBack()" style="display:none">← Back</button>
        <button class="btn btn-p" id="nextb" onclick="goNext()">Edit Details →</button>
    </div>
</div>

<script>
let photos = [];
let cur = 1;
let ok = 0, fail = 0;
const KEY = '<?= htmlspecialchars($valid_key) ?>';
const CATS = <?= json_encode($cats) ?>;

document.getElementById('fi').addEventListener('change', function(){ loadFiles(Array.from(this.files)); });

const ua = document.getElementById('uarea');
ua.addEventListener('dragover', e=>{ e.preventDefault(); ua.classList.add('drag'); });
ua.addEventListener('dragleave', ()=>ua.classList.remove('drag'));
ua.addEventListener('drop', e=>{ e.preventDefault(); ua.classList.remove('drag'); loadFiles(Array.from(e.dataTransfer.files).filter(f=>f.type.startsWith('image/'))); });

function loadFiles(files){
    if(!files.length) return;
    photos = files.map(f=>({
        f, id: Math.random().toString(36).substr(2,9),
        title: f.name.replace(/\.[^/.]+$/,'').replace(/[-_]/g,' ').replace(/\b\w/g,l=>l.toUpperCase()),
        cat:'General', wm:'Photography-life4me', desc:'', feat:false
    }));
    updateCount();
    buildGrid();
    go(2);
}

function buildGrid(){
    const g = document.getElementById('grid');
    g.innerHTML = '';
    photos.forEach((p,i)=>{
        const c = document.createElement('div');
        c.className='card'; c.id='c-'+p.id;
        const sz=(p.f.size/1024).toFixed(0); const szTxt=sz>1024?(sz/1024).toFixed(1)+' MB':sz+' KB';
        const copts = CATS.map(x=>`<option value="${x}" ${x===p.cat?'selected':''}>${x}</option>`).join('');
        c.innerHTML=`<div class="sbadge" id="b-${p.id}"></div>
        <img class="thumb" id="t-${p.id}" src="">
        <div class="body">
            <div class="fsz">📄 ${eh(p.f.name)} &nbsp;|&nbsp; ${szTxt}</div>
            <input type="text" placeholder="Title..." value="${eh(p.title)}" onchange="photos[${i}].title=this.value" oninput="photos[${i}].title=this.value">
            <select onchange="photos[${i}].cat=this.value">${copts}</select>
            <input type="text" placeholder="Watermark..." value="${eh(p.wm)}" onchange="photos[${i}].wm=this.value" oninput="photos[${i}].wm=this.value">
            <input type="text" placeholder="Description (optional)..." value="${eh(p.desc)}" onchange="photos[${i}].desc=this.value" oninput="photos[${i}].desc=this.value">
            <div class="row">
                <input type="checkbox" id="ft-${p.id}" ${p.feat?'checked':''} onchange="photos[${i}].feat=this.checked">
                <label for="ft-${p.id}">⭐ Featured</label>
                <button class="rm-btn" onclick="removeP('${p.id}')">🗑 Remove</button>
            </div>
        </div>`;
        g.appendChild(c);
        const r=new FileReader(); r.onload=e=>{ const el=document.getElementById('t-'+p.id); if(el) el.src=e.target.result; }; r.readAsDataURL(p.f);
    });
}

function removeP(id){
    photos = photos.filter(p=>p.id!==id);
    const el=document.getElementById('c-'+id); if(el) el.remove();
    updateCount(); buildGrid();
}

function updateCount(){
    const n=photos.length;
    document.getElementById('hcount').textContent=n+' photos';
    document.getElementById('sc').textContent=n;
    document.getElementById('rc').textContent=n;
    document.getElementById('bbar').style.display=n?'flex':'none';
}

function applyAll(){
    const cat=document.getElementById('dcat').value;
    const wm=document.getElementById('dwm').value;
    photos.forEach(p=>{ p.cat=cat; p.wm=wm; });
    buildGrid();
    toast('✅ '+photos.length+' photos pe apply ho gaya!');
}

function go(n){
    cur=n;
    document.querySelectorAll('.page').forEach((p,i)=>p.classList.toggle('active',i+1===n));
    document.querySelectorAll('.step').forEach((s,i)=>{ s.classList.toggle('active',i+1===n); s.classList.toggle('done',i+1<n); });
    document.getElementById('backb').style.display=(n>1&&n<3)?'block':'none';
    document.getElementById('nextb').style.display=n<3?'block':'none';
    if(n===2) document.getElementById('nextb').textContent='🚀 Upload Shuru Karo';
    if(n>=3) document.getElementById('bbar').style.display='none';
}

function goNext(){ if(cur===2){ if(!photos.length){alert('Koi photo nahi!');return;} startUpload(); } }
function goBack(){ if(cur>1) go(cur-1); }

async function startUpload(){
    go(3); ok=0; fail=0;
    const total=photos.length;
    for(let i=0;i<total;i++){
        const p=photos[i];
        const card=document.getElementById('c-'+p.id);
        if(card){ card.classList.add('uploading'); const b=document.getElementById('b-'+p.id); if(b) b.textContent='⏳'; }
        document.getElementById('ptxt').textContent=`Upload... ${i+1} / ${total}`;
        document.getElementById('psub').textContent=`${ok} success, ${fail} failed`;
        document.getElementById('cfile').textContent='📤 '+p.title+' ('+p.f.name+')';
        document.getElementById('pbf').style.width=((i/total)*100)+'%';
        addLog('📤 Uploading: '+p.title,'info');
        const fd=new FormData();
        fd.append('action','upload'); fd.append('photo',p.f);
        fd.append('title',p.title||p.f.name); fd.append('category',p.cat);
        fd.append('watermark_text',p.wm); fd.append('description',p.desc);
        fd.append('is_featured',p.feat?'1':'0'); fd.append('admin_key',KEY);
        try{
            const r=await fetch('api.php',{method:'POST',body:fd});
            const d=await r.json();
            if(d.success||d.id){ ok++; addLog('✅ "'+p.title+'" uploaded! ID: '+(d.id||'?'),'ok'); if(card){card.classList.remove('uploading');card.classList.add('success');const b=document.getElementById('b-'+p.id);if(b)b.textContent='✅';} }
            else{ fail++; addLog('❌ "'+p.title+'": '+(d.error||'Unknown'),'err'); if(card){card.classList.remove('uploading');card.classList.add('failed');const b=document.getElementById('b-'+p.id);if(b)b.textContent='❌';} }
        }catch(e){ fail++; addLog('❌ "'+p.title+'": '+e.message,'err'); if(card){card.classList.remove('uploading');card.classList.add('failed');} }
        await new Promise(r=>setTimeout(r,400));
    }
    document.getElementById('pbf').style.width='100%';
    document.getElementById('ptxt').textContent='🎉 Complete!';
    showSummary(total);
}

function showSummary(total){
    go(4);
    document.getElementById('sok').textContent=ok;
    document.getElementById('sfail').textContent=fail;
    document.getElementById('stotal').textContent=total;
    if(!fail){ document.getElementById('sico').textContent='🎉'; document.getElementById('stitle').textContent='Sab upload ho gayi!'; document.getElementById('smsg').textContent=ok+' photos successfully upload hui!'; document.getElementById('smsg').className='alert'; }
    else if(!ok){ document.getElementById('sico').textContent='😔'; document.getElementById('stitle').textContent='Upload fail ho gaya'; document.getElementById('smsg').textContent='Koi photo upload nahi hui. API check karo.'; document.getElementById('smsg').className='alert warn'; }
    else{ document.getElementById('sico').textContent='⚠️'; document.getElementById('stitle').textContent='Partially done'; document.getElementById('smsg').textContent=ok+' success, '+fail+' fail. Failed photos dobara try karo.'; document.getElementById('smsg').className='alert warn'; }
}

function addLog(msg,type){ const b=document.getElementById('logbox'); const d=document.createElement('div'); d.className='ll '+type; d.textContent=new Date().toLocaleTimeString()+' — '+msg; b.appendChild(d); b.scrollTop=b.scrollHeight; }
function eh(s){ return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }
function toast(msg){ const t=document.createElement('div'); t.textContent=msg; t.style.cssText='position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#4ecca3;color:#1a1a2e;padding:10px 20px;border-radius:20px;font-weight:bold;z-index:999;'; document.body.appendChild(t); setTimeout(()=>t.remove(),2500); }
</script>
</body>
</html>
