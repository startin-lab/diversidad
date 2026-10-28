(function(){
"use strict";
const $=s=>document.querySelector(s), $$=s=>[...document.querySelectorAll(s)];
const root=document.documentElement;
const store={get(k){try{return localStorage.getItem(k)}catch(e){return null}},set(k,v){try{localStorage.setItem(k,v)}catch(e){}}};
const reduced=()=>matchMedia("(prefers-reduced-motion: reduce)").matches||root.dataset.motion==="off";
const esc=s=>String(s).replace(/[&<>"]/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;"}[m]));
const wait=ms=>new Promise(r=>setTimeout(r,reduced()?0:ms));

/* ================= DATOS: nueve pliegues ================= */
const P=[
 {id:"mayores",img:"mayor",h:.88,label:"Personas mayores",c:"#F0A04B",
  big:"15 %",st:"de la población de Colombia tiene 60 años o más: 7,9 millones de personas en 2024. En 1985 eran el 6 %.",
  easy:"Hoy, 15 de cada 100 personas en Colombia tienen 60 años o más.",
  stats:["En 2036 habrá más personas de 60 años o más que menores de 15 (DANE).","En 2070 serán el 36 % de la población: cerca de 20 millones.","1 de cada 4 personas mayores no tiene ingresos en la vejez.","Cerca de la mitad de los hombres y una cuarta parte de las mujeres de 60+ trabajan o quieren trabajar."],
  bars:[["1985",6],["2024",15],["2070 (proyección)",36]],barMax:40,unit:" %",
  act:"Rutas de talento intergeneracional: mentoría en dos vías, reentrenamiento y retiro flexible. Y lenguaje: «personas mayores», no «abuelitos».",
  src:"Fundación Saldarriaga Concha con datos DANE (feb 2025); DANE vía RTVC (jul 2025)."},
 {id:"genero",img:"mujer",h:.91,label:"Género y cuidado",c:"#E6DDBF",
  big:"11,4 %",st:"fue la tasa de desempleo de las mujeres en 2025. La de los hombres fue 7,0 %.",
  easy:"Más mujeres que hombres buscan trabajo y no lo encuentran.",
  stats:["La participación laboral de las mujeres está 24 puntos por debajo de la de los hombres.","Con título universitario, una mujer gana 82 centavos por cada peso de un hombre.","El 35 % de las mujeres que quieren trabajar y no buscan empleo lo atribuye a responsabilidades familiares."],
  bars:[["Desempleo mujeres",11.44],["Desempleo hombres",6.99]],barMax:15,unit:" %",
  act:"Corresponsabilidad real: licencias parentales equivalentes, horarios flexibles y retorno acompañado después de la licencia.",
  src:"Observatorio Laboral de la Universidad del Rosario (LaboUR), vía Valora Analitik (mar 2026)."},
 {id:"ninez",img:"nina",h:.46,label:"Educación inclusiva",c:"#5DBB52",
  big:"2 %",st:"de la matrícula escolar corresponde a estudiantes con discapacidad: 200.334 de cerca de 9,8 millones.",
  easy:"Muy pocos estudiantes con discapacidad llegan al colegio.",
  stats:["Asisten al colegio el 72,9 % de niñas, niños y jóvenes con discapacidad, frente al 95,4 % sin discapacidad.","Analfabetismo entre 5 y 16 años: 34,2 % con discapacidad frente a 9,2 % sin discapacidad.","El 5,8 % de la población tiene dificultad para al menos una actividad básica cotidiana (ENCV 2024)."],
  bars:[["Asistencia con discapacidad",72.9],["Asistencia sin discapacidad",95.4]],barMax:100,unit:" %",
  act:"El talento de 2035 se está quedando fuera del aula hoy: alianzas con instituciones educativas y prácticas accesibles.",
  src:"Laboratorio de Economía de la Educación, U. Javeriana (SIMAT 2023, ENCV 2024), vía Portafolio (jun 2026)."},
 {id:"discapacidad",img:"ciego",h:1,label:"Discapacidad y trabajo",c:"#E0736B",
  big:"17,9 %",st:"de las personas con discapacidad tiene empleo. Entre las personas sin discapacidad, la cifra es 60,9 %.",
  easy:"Solo 18 de cada 100 personas con discapacidad tienen trabajo.",
  stats:["Solo el 20,2 % participa en el mercado laboral, frente al 66,6 % de quienes no tienen discapacidad.","Su desocupación es 11,4 %, frente a 8,5 %.","El 55,8 % de quienes trabajan lo hace por cuenta propia."],
  bars:[["Ocupación con discapacidad",17.9],["Ocupación sin discapacidad",60.9]],barMax:100,unit:" %",
  act:"Selección accesible: vacantes en formatos legibles, pruebas adaptadas y entrevistas con ajustes razonables.",
  src:"DANE, GEIH jul–sep 2025, vía Radio Nacional de Colombia (dic 2025)."},
 {id:"ley",img:"silla",h:.79,label:"La ley ya cambió",c:"#62C3CA",
  big:"2 de 100",st:"trabajadores deben ser personas con discapacidad en las empresas privadas (Ley 2466 de 2025). La fiscalización empezó el 1 de julio de 2026.",
  easy:"Por ley, en una empresa de 100 personas, al menos 2 deben ser personas con discapacidad.",
  stats:["Las empresas de más de 500 empleados suman al menos una persona más por cada tramo de 100.","Quien cumple deduce el 200 % de salarios y prestaciones en renta.","50 % menos de cuota de aprendices SENA y puntaje adicional en licitaciones.","Negar un ajuste razonable sin justificación es discriminación (Ley 1618 de 2013)."],
  bars:null,
  act:"Haz hoy el diagnóstico: cuántas personas con certificado de discapacidad tienes, qué ajustes faltan y quién responde.",
  src:"Ley 2466 de 2025 y Ministerio del Trabajo, vía Infobae (mar 2026)."},
 {id:"etnia",img:"afro",h:1,label:"Población afrodescendiente",c:"#C98A57",
  big:"9,34 %",st:"de la población se estima negra, afrocolombiana, raizal o palenquera: 4,67 millones de personas.",
  easy:"Casi 1 de cada 10 personas en Colombia es afrodescendiente.",
  stats:["El censo de 2018 registró 2.982.224 personas autorreconocidas; el DANE ajustó la cifra con la Encuesta de Calidad de Vida.","En 2005 la cifra fue 4.311.757: el autorreconocimiento cambia con la pregunta y el contexto.","Preguntar bien también es incluir: variable étnica voluntaria y por autorreconocimiento."],
  bars:[["NARP (estimación DANE)",9.34],["Autorreconocidas en CNPV 2018",5.8]],barMax:12,unit:" %",
  act:"Incluye la variable étnica (voluntaria, por autorreconocimiento) en tu caracterización de talento para medir brechas reales.",
  src:"DANE, CNPV 2018 y estimación NARP 2019, vía Portafolio."},
 {id:"indigena",img:"indigena",h:1.04,label:"Pueblos indígenas y Rrom",c:"#E2A93B",
  big:"1,9 M",st:"personas se reconocieron indígenas en el censo de 2018: el 4,4 % de la población. El pueblo Rrom suma 2.649 personas.",
  easy:"Casi 2 millones de personas en Colombia son indígenas.",
  stats:["115 pueblos indígenas y 65 lenguas nativas vivas conviven en Colombia (Ministerio de Cultura).","El pueblo Rrom (gitano) se organiza en nueve kumpanias; 710 personas Rrom están reconocidas como víctimas del conflicto.","Entre las víctimas del conflicto registradas, el 16,3 % pertenece a un grupo étnico."],
  bars:[["Indígena (CNPV 2018)",4.4],["NARP (estimación)",9.34]],barMax:12,unit:" %",
  act:"Enfoque diferencial en selección y bienestar: reconocer lenguas, calendarios y formas propias de organización.",
  src:"DANE, CNPV 2018; Publimetro con datos DANE (nov 2025); Unidad para las Víctimas (2026)."},
 {id:"lgbt",img:"lgbt",h:1.03,label:"Personas LGBTIQ+",c:"#9B63D6",
  big:"496 mil",st:"personas mayores de 18 años se reconocen LGBT en Colombia: el 1,3 % de la población adulta.",
  easy:"Casi medio millón de personas adultas en Colombia se reconocen LGBT.",
  stats:["Su tasa de ocupación es 73,8 %, frente a 62,1 % de la población no LGBT.","Aun así, su desocupación es mayor: 10,3 % frente a 8,4 %.","En 23 ciudades y áreas metropolitanas son 349 mil personas (1,8 %)."],
  bars:[["Desocupación LGBT",10.3],["Desocupación no LGBT",8.4]],barMax:12,unit:" %",
  act:"Protocolos contra la discriminación, nombre identitario en los sistemas internos y beneficios para todas las familias.",
  src:"DANE, GEIH, año móvil jul 2025 – jun 2026 (el DANE usa la sigla LGBT)."},
 {id:"migracion",img:"migrante",h:1.09,label:"Migración y conflicto",c:"#5B8DEF",
  big:"2,8 M",st:"personas venezolanas viven en Colombia (corte marzo 2025). Y más de 10 millones de personas están en el Registro Único de Víctimas del conflicto.",
  easy:"Casi 3 millones de personas venezolanas viven en Colombia. Y 2 de cada 10 personas del país son víctimas del conflicto.",
  stats:["El 70,4 % de las personas migrantes venezolanas tiene Permiso por Protección Temporal (PPT).","Cerca del 78 % de las víctimas de desplazamiento forzado (más de 6 millones) no ha superado su situación de vulnerabilidad.","Entre las víctimas registradas, el 20,3 % son personas con discapacidad."],
  bars:[["Migrantes con PPT",70.4],["Víctimas de desplazamiento aún vulnerables",78]],barMax:100,unit:" %",
  act:"Acepta el PPT como documento válido en selección y bancarización; vincula víctimas del conflicto con los beneficios de la Ley 1448.",
  src:"Migración Colombia vía Portafolio (ene 2026); Unidad para las Víctimas, boletín 9 de abril de 2026."}
];

/* ================= NAVEGACIÓN POR ESCENAS ================= */
const SCENES=["inicio","pliegue","cifras","pliego","inscripcion","evento"];
let current="";
function go(name,push=true){
  if(!SCENES.includes(name))name="inicio";
  if(name===current)return;
  current=name;
  $$(".scene").forEach(s=>{s.hidden=s.dataset.scene!==name;});
  $$("[data-go]").forEach(a=>{const on=a.dataset.go===name;if(a.getAttribute("role")==="tab"||a.closest(".dock"))a.setAttribute("aria-selected",String(on));});
  if(push&&location.hash!=="#"+name)history.replaceState(null,"","#"+name);
  const sc=$("#s-"+name);if(sc){sc.scrollTop=0;window.scrollTo({top:0,behavior:"instant"});}
  if(name==="inicio")requestAnimationFrame(sizeCanvas);
  if(name==="evento")loadWall();
  const h=sc&&sc.querySelector("h1,h2");if(h&&document.activeElement&&document.activeElement.tagName==="A"){h.setAttribute("tabindex","-1");h.focus({preventScroll:true});}
}
document.addEventListener("click",e=>{const a=e.target.closest("[data-go]");if(!a)return;e.preventDefault();go(a.dataset.go);});
window.addEventListener("hashchange",()=>go(location.hash.replace("#","")||"inicio",false));

/* ================= ACCESIBILIDAD ================= */
const a11yBtn=$("#a11yBtn"),panelA=$("#a11yPanel");
a11yBtn.addEventListener("click",()=>{const o=panelA.hidden;panelA.hidden=!o;a11yBtn.setAttribute("aria-expanded",String(o));});
document.addEventListener("keydown",e=>{if(e.key==="Escape"&&!panelA.hidden){panelA.hidden=true;a11yBtn.setAttribute("aria-expanded","false");a11yBtn.focus();}});
document.addEventListener("click",e=>{if(!panelA.hidden&&!panelA.contains(e.target)&&!a11yBtn.contains(e.target)){panelA.hidden=true;a11yBtn.setAttribute("aria-expanded","false");}});
let scale=parseFloat(store.get("pl-scale")||"1");
const applyScale=()=>{root.style.setProperty("--scale",scale);store.set("pl-scale",String(scale));};
$("#sMinus").onclick=()=>{scale=Math.max(.9,+(scale-.1).toFixed(2));applyScale();};
$("#sPlus").onclick=()=>{scale=Math.min(1.5,+(scale+.1).toFixed(2));applyScale();};
$("#sReset").onclick=()=>{scale=1;applyScale();};
applyScale();
function toggle(btn,key,attr,onVal,cb){
  const set=(v,init)=>{btn.setAttribute("aria-pressed",String(v));btn.textContent=v?"Sí":"No";if(attr){if(v)root.dataset[attr]=onVal;else delete root.dataset[attr];}store.set(key,v?"1":"0");if(!init&&cb)cb(v);};
  btn.addEventListener("click",()=>set(btn.getAttribute("aria-pressed")!=="true"));
  set(store.get(key)==="1",true);
}
let easyChat=false,voice=false;
toggle($("#tContrast"),"pl-hc","contrast","alto",()=>drawFacets());
toggle($("#tEasy"),"pl-easy","easy","si",v=>{easyChat=v;$("#chatEasy").setAttribute("aria-pressed",String(v));renderPanel();});
toggle($("#tMotion"),"pl-motion","motion","off");
toggle($("#tVoice"),"pl-voice",null,null,v=>{voice=v;$("#chatVoice").setAttribute("aria-pressed",String(v));});
easyChat=root.dataset.easy==="si";voice=store.get("pl-voice")==="1";
$("#chatEasy").setAttribute("aria-pressed",String(easyChat));$("#chatVoice").setAttribute("aria-pressed",String(voice));
$("#chatEasy").addEventListener("click",function(){easyChat=this.getAttribute("aria-pressed")!=="true";this.setAttribute("aria-pressed",String(easyChat));});
$("#chatVoice").addEventListener("click",function(){voice=this.getAttribute("aria-pressed")!=="true";this.setAttribute("aria-pressed",String(voice));});
function speak(text){try{const s=window.speechSynthesis;if(!s)return false;s.cancel();const u=new SpeechSynthesisUtterance(text.replace(/\s*\([^)]*\)/g,""));u.lang="es-CO";u.rate=.98;const v=s.getVoices().find(v=>/^es/i.test(v.lang));if(v)u.voice=v;s.speak(u);return true;}catch(e){return false;}}

/* ================= FACETAS DE PAPEL ================= */
const cv=$("#facets"),cx=cv.getContext("2d");
const stops=[[0,[238,125,185]],[.35,[106,71,184]],[.62,[19,167,154]],[1,[12,22,70]]];
const mix=(a,b,t)=>a.map((v,i)=>Math.round(v+(b[i]-v)*t));
function grad(t){t=Math.max(0,Math.min(1,t));for(let i=1;i<stops.length;i++){if(t<=stops[i][0]){const a=stops[i-1],b=stops[i];return mix(a[1],b[1],(t-a[0])/(b[0]-a[0]));}}return stops[stops.length-1][1];}
function rng(seed){let s=seed>>>0;return()=>{s=(s*1664525+1013904223)>>>0;return s/4294967296;};}
var mesh=null,light={x:.75,y:.2},target={x:.75,y:.2},raf=null;
function buildMesh(w,h,cell,seed){const r=rng(seed),cols=Math.ceil(w/cell)+3,rows=Math.ceil(h/cell)+3,pts=[];
  for(let j=0;j<rows;j++){pts[j]=[];for(let i=0;i<cols;i++)pts[j][i]=[(i-1)*cell+(r()-.5)*cell*.7,(j-1)*cell+(r()-.5)*cell*.7];}
  const tris=[];for(let j=0;j<rows-1;j++)for(let i=0;i<cols-1;i++){const a=pts[j][i],b=pts[j][i+1],c=pts[j+1][i],d=pts[j+1][i+1];if(r()>.5)tris.push([a,b,d,r()],[a,d,c,r()]);else tris.push([a,b,c,r()],[b,d,c,r()]);}return tris;}
function paint(ctx,w,h,tris,lt,hc){ctx.clearRect(0,0,w,h);
  for(const t of tris){const mx=(t[0][0]+t[1][0]+t[2][0])/3,my=(t[0][1]+t[1][1]+t[2][1])/3;let col;
    if(hc){const g=10+t[3]*22;col=[g,g,g];}
    else{const along=(my/h)*.7+(1-mx/w)*.15+(mx/w>.6?-(mx/w-.6)*.5:0)+.05;col=grad(along+.25*(my/h));const dx=mx/w-lt.x,dy=my/h-lt.y,dist=Math.sqrt(dx*dx+dy*dy);const k=(t[3]-.5)*.22+Math.max(0,.28-dist)*.9;col=col.map(v=>Math.max(0,Math.min(255,Math.round(v*(1+k)))));}
    ctx.fillStyle=`rgb(${col[0]},${col[1]},${col[2]})`;ctx.beginPath();ctx.moveTo(t[0][0],t[0][1]);ctx.lineTo(t[1][0],t[1][1]);ctx.lineTo(t[2][0],t[2][1]);ctx.closePath();ctx.fill();ctx.strokeStyle=ctx.fillStyle;ctx.lineWidth=1;ctx.stroke();}}
function sizeCanvas(){const r=cv.getBoundingClientRect();if(!r.width)return;const dpr=Math.min(2,window.devicePixelRatio||1);cv.width=Math.max(1,r.width*dpr);cv.height=Math.max(1,r.height*dpr);cx.setTransform(dpr,0,0,dpr,0,0);mesh=buildMesh(r.width,r.height,Math.max(70,r.width/14),7);drawFacets();}
function drawFacets(){if(!mesh)return;const r=cv.getBoundingClientRect();paint(cx,r.width,r.height,mesh,light,root.dataset.contrast==="alto");}
function tick(){light.x+=(target.x-light.x)*.08;light.y+=(target.y-light.y)*.08;drawFacets();if(Math.abs(target.x-light.x)>.002||Math.abs(target.y-light.y)>.002)raf=requestAnimationFrame(tick);else raf=null;}
$("#s-inicio").addEventListener("pointermove",e=>{if(reduced())return;const r=cv.getBoundingClientRect();target={x:(e.clientX-r.left)/r.width,y:(e.clientY-r.top)/r.height};if(!raf)raf=requestAnimationFrame(tick);});
window.addEventListener("resize",()=>{clearTimeout(window.__rz);window.__rz=setTimeout(sizeCanvas,120);});

/* ================= CUENTA REGRESIVA ================= */
(function(){const T=new Date("2026-11-11T07:30:00-05:00").getTime(),box=$(".countdown");const pad=n=>String(n).padStart(2,"0");
  function tick(){const diff=T-Date.now();
    if(diff<=0){box.innerHTML='<span class="cd-title">¡Es hoy!</span><div class="cd done">Nos vemos en el HUB iEX de la Universidad El Bosque</div>';return;}
    const d=Math.floor(diff/864e5),h=Math.floor(diff%864e5/36e5),m=Math.floor(diff%36e5/6e4),s=Math.floor(diff%6e4/1e3);
    $("#cdD").textContent=d;$("#cdH").textContent=pad(h);$("#cdM").textContent=pad(m);$("#cdS").textContent=pad(s);
    box.setAttribute("aria-label",`Faltan ${d} días, ${h} horas y ${m} minutos para el Encuentro`);
    setTimeout(tick,reduced()?60000:1000);}
  tick();})();

/* ================= FILA DE FIGURAS ================= */
const row=$("#row");
P.forEach((p,i)=>{const b=document.createElement("button");b.className="fig in";b.setAttribute("role","listitem");b.dataset.label=p.label;b.setAttribute("aria-label","Ver la cifra: "+p.label);
  b.style.setProperty("--d",(i*.4)+"s");b.style.setProperty("--h",p.h);b.style.animationDelay=(i*.4)+"s,"+(i*.08)+"s";
  b.innerHTML=`<img src="assets/fig/${p.img}.webp?v=x" alt="" loading="eager" decoding="async">`;
  b.onclick=()=>{select(i,true);go("cifras");};row.appendChild(b);});

/* ================= CIFRAS ================= */
const chips=$("#chips");let cur=3;
P.forEach((p,i)=>{const c=document.createElement("button");c.className="chip";c.setAttribute("role","tab");c.id="tab-"+p.id;c.style.setProperty("--c",p.c);
  c.innerHTML=`<img src="assets/fig/${p.img}.webp?v=x" alt="">${esc(p.label)}`;c.onclick=()=>select(i,true);
  c.addEventListener("keydown",e=>{if(e.key==="ArrowDown"||e.key==="ArrowUp"||e.key==="ArrowRight"||e.key==="ArrowLeft"){e.preventDefault();const n=(i+((e.key==="ArrowDown"||e.key==="ArrowRight")?1:-1)+P.length)%P.length;select(n,true);chips.children[n].focus();}});
  chips.appendChild(c);});
function select(i,anim){cur=i;[...chips.children].forEach((c,j)=>{c.setAttribute("aria-selected",String(j===i));c.tabIndex=j===i?0:-1;});renderPanel(anim);}
function renderPanel(anim){const p=P[cur],pn=$("#panel"),easy=root.dataset.easy==="si";pn.style.setProperty("--c",p.c);pn.setAttribute("aria-labelledby","tab-"+p.id);
  const bars=p.bars?`<div class="bars">${p.bars.map((b,k)=>`<div><div class="bar-l"><span>${esc(b[0])}</span><b>${String(b[1]).replace(".",",")}${p.unit}</b></div><div class="bar-t"><div class="bar-f${k?" alt":""}" data-w="${(b[1]/p.barMax*100).toFixed(1)}"></div></div></div>`).join("")}</div>`:"";
  pn.innerHTML=`<img class="who" src="assets/fig/${p.img}.webp?v=x" alt=""><div><span class="eyebrow">${esc(p.label)}</span><div><span class="big${anim&&!reduced()?" unfold":""}">${esc(p.big)}</span></div><p class="statement">${esc(easy?p.easy:p.st)}</p>${bars}</div>
    <div><ul class="stats">${p.stats.map(s=>`<li>${esc(s)}</li>`).join("")}</ul><div class="action"><b>Qué puede doblar tu organización</b>${esc(p.act)}</div><p class="src">Fuente: ${esc(p.src)}</p>
    <div class="panel-tools"><button class="mini" id="pSay">Escuchar</button><button class="mini" id="pAsk">Preguntarle a Pliego</button><button class="mini" id="pNext">Siguiente pliegue →</button></div></div>`;
  requestAnimationFrame(()=>requestAnimationFrame(()=>$$(".bar-f").forEach(f=>f.style.width=f.dataset.w+"%")));
  $("#pSay").onclick=()=>speak(p.big.replace("%"," por ciento")+". "+(easy?p.easy:p.st));
  $("#pAsk").onclick=()=>{go("pliego");ask("Cuéntame más sobre "+p.label.toLowerCase()+" en Colombia");};
  $("#pNext").onclick=()=>select((cur+1)%P.length,true);
}
select(3,false);

/* ================= PLIEGO (agente) ================= */
const log=$("#log"),sugs=$("#sugs");let hist=[],busy=false;
const SUG=["¿Cuántas personas con discapacidad tienen empleo?","¿Qué exige la Ley 2466 sobre discapacidad?","¿Por qué la diversidad es buen negocio?","¿Cómo digo «discapacitado» de forma respetuosa?","¿Cuántas personas migrantes viven en Colombia?","¿Qué pasó en el encuentro anterior?","¿Qué es un ajuste razonable?"];
function addMsg(html,who){const d=document.createElement("div");d.className="msg "+who;d.innerHTML=html;log.appendChild(d);log.scrollTop=log.scrollHeight;return d;}
function renderSugs(){sugs.innerHTML="";SUG.slice().sort(()=>Math.random()-.5).slice(0,4).forEach(s=>{const b=document.createElement("button");b.type="button";b.textContent=s;b.onclick=()=>ask(s);sugs.appendChild(b);});}
function hello(){log.innerHTML="";hist=[];addMsg(`Hola, soy Pliego. Pregúntame por cifras de discapacidad, género, edad, etnia, migración, conflicto o diversidad sexual en Colombia, o pídeme ayuda con una palabra. Siempre te digo la fuente; si no tengo un dato, te lo digo.`,"bot");renderSugs();}
async function ask(q){q=q.trim();if(!q||busy)return;busy=true;addMsg(esc(q),"me");sugs.innerHTML="";hist.push({role:"user",content:q});
  const typing=addMsg(`<span class="typing" aria-label="Pliego está escribiendo"><i></i><i></i><i></i></span>`,"bot");
  try{const r=await fetch("api/agente.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({messages:hist.slice(-12),facil:easyChat})});
    const j=await r.json();typing.remove();
    if(!j.ok){addMsg(esc(j.error||"Pliego no pudo responder."),"bot");}
    else{hist.push({role:"assistant",content:j.answer});const m=addMsg(esc(j.answer)+`<div class="s"><button class="say" type="button">Escuchar</button></div>`,"bot");m.querySelector(".say").onclick=()=>speak(j.answer);if(voice)speak(j.answer);}
  }catch(e){typing.remove();addMsg("No hay conexión con Pliego en este momento. Revisa la pestaña Cifras mientras tanto.","bot");}
  busy=false;renderSugs();
}
$("#askForm").addEventListener("submit",e=>{e.preventDefault();const i=$("#askInput");ask(i.value);i.value="";});
$("#newChat").onclick=hello;hello();

/* ================= INSCRIPCIÓN + ORIGAMI ================= */
const picker=$("#colorPick");
P.forEach((p,i)=>{const l=document.createElement("label");l.innerHTML=`<input type="radio" name="pl" value="${i}"><span style="--c:${p.c}"><i></i>${esc(p.label)}</span>`;picker.appendChild(l);});
const sheet=$("#sheet"),flaps=$$(".flap"),stepEls=$$("#steps li"),msg=$("#studioMsg");
const pickIdx=()=>+(document.querySelector('input[name="pl"]:checked')||{value:-1}).value;
const NOPL={c:"#FFC20E",label:""},plOf=i=>P[i]||NOPL;
function setColor(){sheet.style.setProperty("--pc",plOf(pickIdx()).c);}picker.addEventListener("change",setColor);setColor();
function step(n){stepEls.forEach((s,i)=>s.classList.toggle("on",i===n));}step(0);
let photo=null,crop={zoom:1,ox:0,oy:0};
const cropBox=$("#cropBox"),cropCv=$("#cropCv"),cropZoom=$("#cropZoom");
function drawCrop(){if(!photo)return;const R=cropCv.width/2,x=cropCv.getContext("2d");x.clearRect(0,0,cropCv.width,cropCv.height);x.save();x.beginPath();x.arc(R,R,R-2,0,Math.PI*2);x.clip();drawPhotoIn(x,R,R,R-2);x.restore();x.strokeStyle="#FFC20E";x.lineWidth=3;x.beginPath();x.arc(R,R,R-2,0,Math.PI*2);x.stroke();}
function drawPhotoIn(x,cx,cy,R){const s0=Math.max(2*R/photo.width,2*R/photo.height)*crop.zoom;const w=photo.width*s0,h=photo.height*s0;const maxX=Math.max(0,(w-2*R)/2)/R,maxY=Math.max(0,(h-2*R)/2)/R;crop.ox=Math.max(-maxX,Math.min(maxX,crop.ox));crop.oy=Math.max(-maxY,Math.min(maxY,crop.oy));x.drawImage(photo,cx-w/2+crop.ox*R,cy-h/2+crop.oy*R,w,h);}
function photoData(){if(!photo)return "";try{const c=document.createElement("canvas");c.width=c.height=420;const x=c.getContext("2d");x.fillStyle="#fff";x.fillRect(0,0,420,420);drawPhotoIn(x,210,210,210);return c.toDataURL("image/jpeg",.86);}catch(e){return "";}}
function setPhotoFromData(url){return new Promise(res=>{const im=new Image();im.onload=()=>{photo=im;crop={zoom:1,ox:0,oy:0};cropZoom.value=1;cropBox.hidden=false;drawCrop();res(true);};im.onerror=()=>res(false);im.src=url;});}
$("#fPhoto").addEventListener("change",e=>{const f=e.target.files[0];if(!f){photo=null;cropBox.hidden=true;return;}const r=new FileReader();r.onload=()=>{const im=new Image();im.onload=()=>{photo=im;crop={zoom:1,ox:0,oy:0};cropZoom.value=1;cropBox.hidden=false;drawCrop();};im.src=r.result;};r.readAsDataURL(f);});
cropZoom.addEventListener("input",()=>{crop.zoom=+cropZoom.value;drawCrop();});
(function(){let drag=null;const R=()=>cropCv.width/2;
  cropCv.addEventListener("pointerdown",e=>{drag={x:e.clientX,y:e.clientY,ox:crop.ox,oy:crop.oy};cropCv.setPointerCapture(e.pointerId);});
  cropCv.addEventListener("pointermove",e=>{if(!drag)return;const k=cropCv.width/cropCv.getBoundingClientRect().width;crop.ox=drag.ox+(e.clientX-drag.x)*k/R();crop.oy=drag.oy+(e.clientY-drag.y)*k/R();drawCrop();});
  cropCv.addEventListener("pointerup",()=>drag=null);cropCv.addEventListener("pointercancel",()=>drag=null);
  $("#cropReset").onclick=()=>{crop={zoom:1,ox:0,oy:0};cropZoom.value=1;drawCrop();};})();
// pregunta 5: exclusivas
$("#groupPick").addEventListener("change",e=>{const t=e.target;if(!t.matches('input[name="grp"]'))return;const excl=t.closest("label").classList.contains("excl");
  if(t.checked){$$('input[name="grp"]').forEach(o=>{if(o===t)return;const oe=o.closest("label").classList.contains("excl");if(excl||oe)o.checked=false;});}
  $("#grpOtherWrap").hidden=!$("#grpOther").checked;});
$$('input[name="ajq"]').forEach(r=>r.addEventListener("change",()=>{$("#ajWrap").hidden=r.value!=="si"||!r.checked;}));
function resetSheet(){flaps.forEach(f=>f.classList.remove("down"));sheet.classList.remove("turn");$("#badgeOut").hidden=true;$("#foldStage").hidden=false;step(0);}
function initials(n){return n.split(/\s+/).filter(Boolean).slice(0,2).map(w=>w[0].toUpperCase()).join("")||"5°";}
$("#regForm").addEventListener("submit",async e=>{
  e.preventDefault();const err=$("#regErr"),btn=$("#regBtn");
  const name=$("#fName").value.trim(),org=$("#fOrg").value.trim(),mail=$("#fMail").value.trim().toLowerCase(),doc=$("#fDoc").value.replace(/[^0-9A-Za-z]/g,"");
  const grps=$$('input[name="grp"]:checked').map(x=>x.value),ajq=document.querySelector('input[name="ajq"]:checked');
  const miss=[];if(name.length<3)miss.push("nombres y apellidos");if(doc.length<5)miss.push("documento de identidad");if(org.length<2)miss.push("institución u organización");if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(mail))miss.push("un correo válido");
  if(!grps.length)miss.push("la pregunta 5 (puedes marcar «Prefiero no responder»)");if(!ajq)miss.push("la pregunta 6");if(!$("#fConsent").checked)miss.push("la autorización de datos");
  if(miss.length){err.hidden=false;err.textContent="Para doblar tu escarapela falta: "+miss.join(", ")+".";err.scrollIntoView({block:"center"});return;}
  err.hidden=true;btn.disabled=true;btn.textContent="Guardando…";
  const d={nombres:name,documento:doc,organizacion:org,correo:mail,pronombre:$("#fPron").value.trim(),grupos:grps,grupo_otro:$("#fGrpOther").value.trim(),requiere_ajuste:ajq.value==="si",
    ajustes:$$('input[name="aj"]:checked').map(x=>x.value),ajuste_otro:$("#fAjOther").value.trim(),pliegue:plOf(pickIdx()).label,compromiso:$("#fPledge").value.trim(),consentimiento:true,foto:photoData()};
  let res;try{const r=await fetch("api/registro.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(d)});res=await r.json();}catch(x){res={ok:false,error:"No hay conexión. Intenta de nuevo."};}
  btn.disabled=false;btn.textContent="Doblar mi escarapela";
  if(!res.ok){err.hidden=false;err.textContent=res.error||"No pudimos guardar tu inscripción.";return;}
  const code=res.codigo;resetSheet();$("#seal").textContent=initials(name);
  if(matchMedia("(max-width:900px)").matches)$(".studio").scrollIntoView({behavior:reduced()?"auto":"smooth",block:"start"});
  step(1);msg.textContent=res.actualizado?"Actualizamos tus datos y volvemos a doblar tu escarapela.":res.repetido?"Ya estabas inscrito/a. Volvemos a doblar tu escarapela.":"Marcar: reconocemos quién eres y lo que traes.";await wait(700);
  flaps[0].classList.add("down");await wait(600);flaps[2].classList.add("down");await wait(700);
  step(2);msg.textContent="Doblar: ajustamos la forma para que quepas tal como eres.";flaps[1].classList.add("down");await wait(600);flaps[3].classList.add("down");await wait(900);
  step(3);msg.textContent="Sostener: cada pliegue sostiene al siguiente.";sheet.classList.add("turn");await wait(1300);
  try{await Promise.race([Promise.all(["900 80px elza","700 40px elza","500 30px elza","400 30px elza","italic 400 30px elza"].map(f=>document.fonts.load(f))),new Promise(r=>setTimeout(r,2500))]);}catch(e){}
  try{await Promise.race([LOGOSTRIP.decode(),new Promise(r=>setTimeout(r,2500))]);}catch(e){}
  const url=renderBadge({name,org,pron:d.pronombre,pledge:d.compromiso,pl:pickIdx(),code});
  $("#badgeImg").src=url;$("#badgeImg").alt=`Escarapela del 5° Encuentro de ${name}, ${org}, código ${code}`;$("#dlBtn").href=url;$("#dlBtn").download=`escarapela-${code}.png`;
  const share=`Me inscribí al 5° Encuentro Diversidades e Inclusión: Gestión del cambio organizacional para la Inclusión y Equidad. 11 nov 2026, Universidad El Bosque.${d.compromiso?" Mi compromiso: "+d.compromiso:""} #DoblarNoEsRomper ${location.origin}`;
  $("#waBtn").href="https://wa.me/?text="+encodeURIComponent(share);
  step(4);$("#foldStage").hidden=true;$("#badgeOut").hidden=false;if(matchMedia("(max-width:900px)").matches)setTimeout(()=>$("#badgeImg").scrollIntoView({behavior:reduced()?"auto":"smooth",block:"center"}),150);
  msg.textContent=`¡Listo, ${name.split(" ")[0]}! Tu código es ${code}.${d.ajustes.length?" Registramos tus ajustes: "+d.ajustes.join(", ")+".":""}`;
  $("#mailNote").textContent="Enviando tu escarapela al correo…";
  try{const r=await fetch("api/correo.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({codigo:code,correo:mail,escarapela_png:url})});const j=await r.json();
    $("#mailNote").textContent=j.ok?(j.enviado||j.ya?"Te enviamos la escarapela y el código a "+mail+". Si no lo ves, revisa el correo no deseado.":"Guardamos tu inscripción, pero el correo no salió. Descarga tu escarapela aquí."):"Guardamos tu inscripción. Descarga tu escarapela aquí.";
  }catch(x){$("#mailNote").textContent="Guardamos tu inscripción. Descarga tu escarapela aquí.";}
  if(d.compromiso)loadWall();
});
/* ----- Recuperar (documento + correo) y editar la escarapela ----- */
function fillForm(j,doc){
  const set=(id,v)=>{const e=$(id);if(e)e.value=v||"";};
  set("#fName",j.nombres);set("#fDoc",doc);set("#fOrg",j.organizacion);set("#fMail",j.correo);set("#fPron",j.pronombre);set("#fPledge",j.compromiso);
  const pi=P.findIndex(p=>p.label===j.pliegue);$$('input[name="pl"]').forEach(o=>o.checked=false);if(pi>=0){const r=document.querySelector(`input[name="pl"][value="${pi}"]`);if(r)r.checked=true;}setColor();
  $$('input[name="grp"]').forEach(o=>o.checked=false);let other="";
  (j.grupos||[]).forEach(g=>{if(/^Otras: /.test(g)){other=g.slice(7);g="Otras";}const o=document.querySelector(`input[name="grp"][value="${CSS.escape(g)}"]`);if(o)o.checked=true;});
  $("#fGrpOther").value=other;$("#grpOtherWrap").hidden=!$("#grpOther").checked;
  const aq=document.querySelector(`input[name="ajq"][value="${j.requiere_ajuste?"si":"no"}"]`);if(aq){aq.checked=true;$("#ajWrap").hidden=!j.requiere_ajuste;}
  const known=$$('input[name="aj"]').map(o=>o.value),extra=[];$$('input[name="aj"]').forEach(o=>o.checked=(j.ajustes||[]).includes(o.value));
  (j.ajustes||[]).forEach(a=>{if(!known.includes(a))extra.push(a);});$("#fAjOther").value=extra.join(", ");
}
async function recoverBadge(){
  const err=$("#recErr"),btn=$("#recBtn"),doc=$("#recDoc").value.replace(/[^0-9A-Za-z]/g,""),mail=$("#recMail").value.trim().toLowerCase();
  if(doc.length<5){err.hidden=false;err.textContent="Escribe tu número de documento completo.";return;}
  if(!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(mail)){err.hidden=false;err.textContent="Escribe el correo con el que te inscribiste.";return;}
  err.hidden=true;btn.disabled=true;btn.textContent="Buscando…";
  let j;try{const r=await fetch("api/recuperar.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({documento:doc,correo:mail})});j=await r.json();}catch(x){j={ok:false,error:"No hay conexión. Intenta de nuevo."};}
  btn.disabled=false;btn.textContent="Ver mi escarapela";
  if(!j.ok){err.hidden=false;err.textContent=j.error||"No encontramos tu inscripción.";return;}
  if(j.foto)await setPhotoFromData(j.foto);
  fillForm(j,doc);
  try{await Promise.race([Promise.all(["900 80px elza","700 40px elza","500 30px elza","400 30px elza","italic 400 30px elza"].map(f=>document.fonts.load(f))),new Promise(r=>setTimeout(r,2500))]);}catch(e){}
  try{await Promise.race([LOGOSTRIP.decode(),new Promise(r=>setTimeout(r,2500))]);}catch(e){}
  const pl=P.findIndex(p=>p.label===j.pliegue);
  const url=renderBadge({name:j.nombres,org:j.organizacion,pron:j.pronombre||"",pledge:j.compromiso||"",pl,code:j.codigo});
  $("#badgeImg").src=url;$("#badgeImg").alt=`Escarapela del 5° Encuentro de ${j.nombres}, ${j.organizacion}, código ${j.codigo}`;$("#dlBtn").href=url;$("#dlBtn").download=`escarapela-${j.codigo}.png`;
  $("#waBtn").href="https://wa.me/?text="+encodeURIComponent(`Me inscribí al 5° Encuentro Diversidades e Inclusión: Gestión del cambio organizacional para la Inclusión y Equidad. 11 nov 2026, Universidad El Bosque. #DoblarNoEsRomper ${location.origin}`);
  step(4);$("#foldStage").hidden=true;$("#badgeOut").hidden=false;$("#editBtn").hidden=false;
  $("#studioMsg").textContent=`Hola, ${j.nombres.split(" ")[0]}. Esta es tu escarapela; tu código es ${j.codigo}.`;
  $("#mailNote").textContent=j.foto?"No se creó un registro nuevo. Para cambiar algo, pulsa «Editar mis datos».":"No se creó un registro nuevo. Tu inscripción no tiene foto guardada: pulsa «Editar mis datos» para agregarla.";
  setTimeout(()=>$("#badgeImg").scrollIntoView({behavior:reduced()?"auto":"smooth",block:"center"}),150);
}
$("#recBtn").addEventListener("click",recoverBadge);
[$("#recDoc"),$("#recMail")].forEach(i=>i.addEventListener("keydown",e=>{if(e.key==="Enter"){e.preventDefault();recoverBadge();}}));
$("#editBtn").addEventListener("click",()=>{$("#recBox").open=false;$("#editNote").hidden=false;$("#editNote").textContent="Cambia lo que necesites (puedes subir o reencuadrar tu foto), marca la autorización de datos y pulsa «Doblar mi escarapela». Conservas el mismo código.";$("#fName").scrollIntoView({behavior:reduced()?"auto":"smooth",block:"center"});setTimeout(()=>$("#fName").focus({preventScroll:true}),400);});
function wrapText(ctx,text,maxW){const words=text.split(/\s+/),lines=[];let line="";words.forEach(w=>{const t=line?line+" "+w:w;if(ctx.measureText(t).width>maxW&&line){lines.push(line);line=w;}else line=t;});if(line)lines.push(line);return lines;}
function drawQR(x,text,X,Y,S){try{if(typeof qrcode!=="function")return false;const q=qrcode(0,"M");q.addData(text);q.make();const n=q.getModuleCount(),cell=S/(n+4);x.fillStyle="#fff";x.fillRect(X,Y,S,S);x.fillStyle="#0C1646";for(let r=0;r<n;r++)for(let c=0;c<n;c++)if(q.isDark(r,c))x.fillRect(X+(c+2)*cell,Y+(r+2)*cell,Math.ceil(cell),Math.ceil(cell));return true;}catch(e){return false;}}
const LOGOSTRIP=new Image();LOGOSTRIP.src="assets/aliados-franja.webp?v=x";
function renderBadge(d){
  const W=1080,H=2080,Hc=H-220,c=document.createElement("canvas");c.width=W;c.height=H;const x=c.getContext("2d");
  paint(x,W,H,buildMesh(W,H,120,d.name.length*31+7),{x:.8,y:.15},false);
  const p=plOf(d.pl),m=70,top=520,fold=110;
  // ojal para el cordón (transparente de verdad)
  const sw=190,sh=46,sx=W/2-sw/2,sy=44;
  x.save();x.globalCompositeOperation="destination-out";x.beginPath();x.roundRect?x.roundRect(sx,sy,sw,sh,23):x.rect(sx,sy,sw,sh);x.fill();x.restore();
  x.strokeStyle="rgba(255,255,255,.85)";x.lineWidth=5;x.beginPath();x.roundRect?x.roundRect(sx,sy,sw,sh,23):x.rect(sx,sy,sw,sh);x.stroke();
  x.strokeStyle="rgba(12,22,70,.35)";x.lineWidth=2;x.beginPath();x.roundRect?x.roundRect(sx+6,sy+6,sw-12,sh-12,18):x.rect(sx+6,sy+6,sw-12,sh-12);x.stroke();
  // encabezado
  x.fillStyle="#FFC20E";x.font="900 190px elza, sans-serif";x.textBaseline="alphabetic";x.fillText("5°",m,330);
  x.fillStyle="#0C1646";x.font="700 46px elza, sans-serif";x.fillText("ENCUENTRO",m+240,230);x.font="700 56px elza, sans-serif";x.fillText("Diversidades",m+240,288);x.fillText("e Inclusión",m+240,346);
  x.fillStyle="#fff";x.font="700 34px elza, sans-serif";x.fillText("Gestión del cambio organizacional para la Inclusión y Equidad",m,430,W-2*m);
  // tarjeta de papel con esquina doblada
  x.fillStyle="#F7F4EE";x.beginPath();x.moveTo(m,top);x.lineTo(W-m-fold,top);x.lineTo(W-m,top+fold);x.lineTo(W-m,Hc-m);x.lineTo(m,Hc-m);x.closePath();x.fill();
  x.fillStyle="rgba(12,22,70,.18)";x.beginPath();x.moveTo(W-m-fold,top);x.lineTo(W-m-fold,top+fold);x.lineTo(W-m,top+fold);x.closePath();x.fill();
  x.fillStyle=p.c;x.fillRect(m,top,W-2*m-fold,18);
  // foto circular con anillo
  const cxp=W/2,cyp=top+250,rad=175;
  x.fillStyle=p.c;x.beginPath();x.arc(cxp,cyp,rad+14,0,Math.PI*2);x.fill();
  x.fillStyle="#fff";x.beginPath();x.arc(cxp,cyp,rad+6,0,Math.PI*2);x.fill();
  x.save();x.beginPath();x.arc(cxp,cyp,rad,0,Math.PI*2);x.clip();
  if(photo){drawPhotoIn(x,cxp,cyp,rad);}
  else{x.fillStyle=p.c;x.fillRect(cxp-rad,cyp-rad,2*rad,2*rad);x.fillStyle="rgba(255,255,255,.22)";x.beginPath();x.moveTo(cxp-rad,cyp+rad);x.lineTo(cxp+rad,cyp-rad);x.lineTo(cxp+rad,cyp+rad);x.closePath();x.fill();x.fillStyle="#0C1646";x.font="900 130px elza, sans-serif";x.textAlign="center";x.textBaseline="middle";x.fillText(initials(d.name),cxp,cyp+8);}
  x.restore();x.textAlign="center";x.textBaseline="alphabetic";
  x.fillStyle="#0C1646";let fs=86;x.font=`900 ${fs}px elza, sans-serif`;while(x.measureText(d.name).width>W-2*m-80&&fs>44){fs-=4;x.font=`900 ${fs}px elza, sans-serif`;}
  let y=cyp+rad+110;x.fillText(d.name,W/2,y);
  if(d.pron){y+=48;x.font="700 32px elza, sans-serif";x.fillStyle="#4B537F";x.fillText(d.pron,W/2,y);}
  y+=62;x.fillStyle="#0C1646";x.font="700 44px elza, sans-serif";x.fillText(d.org,W/2,y,W-2*m-60);
  if(p.label){y+=76;const label="MI PLIEGUE · "+p.label.toUpperCase();x.font="700 30px elza, sans-serif";const lw=x.measureText(label).width+60;
  x.fillStyle=p.c;x.beginPath();x.roundRect?x.roundRect(W/2-lw/2,y-42,lw,62,31):x.rect(W/2-lw/2,y-42,lw,62);x.fill();x.fillStyle="#0C1646";x.fillText(label,W/2,y);}
  if(d.pledge){y+=78;x.font="italic 400 36px elza, sans-serif";x.fillStyle="#0C1646";wrapText(x,"«"+d.pledge+"»",W-2*m-120).slice(0,3).forEach(l=>{x.fillText(l,W/2,y);y+=48;});}
  const qy=Hc-m-150-250,qs=220;const hasQR=drawQR(x,location.origin+"/v/"+d.code,W-m-60-qs,qy,qs);
  x.textAlign=hasQR?"left":"center";const cx0=hasQR?m+60:W/2;
  x.fillStyle="#4B537F";x.font="700 28px elza, sans-serif";x.fillText("CÓDIGO DE REGISTRO",cx0,qy+70);
  x.fillStyle="#0C1646";x.font="900 92px elza, sans-serif";x.fillText(d.code,cx0,qy+165);
  x.fillStyle="#4B537F";x.font="400 26px elza, sans-serif";x.fillText("Preséntalo en el registro el 11 de noviembre",cx0,qy+208);
  x.fillStyle="#0C1646";x.fillRect(m,Hc-m-150,W-2*m,150);
  x.textAlign="left";x.fillStyle="#FFC20E";x.font="700 40px elza, sans-serif";x.fillText("11 nov 2026 · 7:30 a.m.",m+40,Hc-m-86);
  x.fillStyle="#fff";x.font="400 30px elza, sans-serif";x.fillText("Universidad El Bosque · HUB iEX, Bogotá",m+40,Hc-m-38);
  x.textAlign="right";
  x.fillStyle="#F26A21";x.font="900 40px elza, sans-serif";x.fillText("ASISTENTE",W-m-30,Hc-m-62);
  // franja de aliados
  {const sy=Hc-m+24,sh=H-m-sy;x.fillStyle="#fff";x.beginPath();x.roundRect?x.roundRect(m,sy,W-2*m,sh,22):x.rect(m,sy,W-2*m,sh);x.fill();
   if(LOGOSTRIP.complete&&LOGOSTRIP.naturalWidth){const iw=W-2*m-28,ih=iw*LOGOSTRIP.naturalHeight/LOGOSTRIP.naturalWidth;x.drawImage(LOGOSTRIP,m+14,sy+(sh-ih)/2,iw,ih);}
   else{x.fillStyle="#4B537F";x.font="700 26px elza, sans-serif";x.textAlign="center";x.fillText("Aliados del 5° Encuentro Diversidades e Inclusión",W/2,sy+sh/2+9);}}
  
  return c.toDataURL("image/png");
}

/* ================= MURO ================= */
const wall=$("#wall");let wallLoaded=false;
function note(t,who,c){const n=document.createElement("article");n.className="note-card";n.style.setProperty("--c",c);n.innerHTML=`<p>${esc(t)}</p><small>${esc(who)}</small>`;return n;}
async function loadWall(){try{const r=await fetch("api/muro.php");const j=await r.json();if(!j.ok)return;wall.innerHTML="";
  if(!j.items.length){wall.appendChild(note("Sé la primera organización en dejar su pliegue aquí.","Muro de compromisos",P[3].c));}
  j.items.forEach(it=>{const p=P.find(q=>q.label===it.pliegue)||P[3];wall.appendChild(note(it.texto,it.organizacion,p.c));});wallLoaded=true;}catch(e){}}
$("#wallForm").addEventListener("submit",async e=>{e.preventDefault();const v=$("#wallIn").value.trim(),o=$("#wallOrg").value.trim();if(v.length<8)return;
  try{await fetch("api/muro.php",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({texto:v,organizacion:o,pliegue:P[cur].label})});}catch(x){}
  $("#wallIn").value="";loadWall();});

/* ================= ARRANQUE ================= */
go(location.hash.replace("#","")||"inicio",false);
sizeCanvas();
})();
