/*
 * Uji-diri aturan: memunculkan kembali bug tampilan lama secara sengaja (CSS/JS disuntikkan)
 * dan memastikan aturan di checks.js menangkapnya. Tambahkan kasus baru di sini setiap kali
 * menambah aturan.  Jalankan: node selftest.mjs  (env sama dengan run.mjs)
 */
import { chromium } from 'playwright';
import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const src = readFileSync(join(dirname(fileURLToPath(import.meta.url)), 'checks.js'), 'utf8');
const BASE = (process.env.UI_BASE_URL || 'http://127.0.0.1:8765/admin').replace(/\/$/, '');
const EMAIL = process.env.UI_EMAIL || 'admin@example.com';
const PASSWORD = process.env.UI_PASSWORD || 'password';
// Halaman edit dengan kolom tanggal (bawaan: resource User di proyek uji).
const EDIT = process.env.UI_EDIT_PAGE || 'users/2/edit';
const EDIT_EMPTY_DATE = process.env.UI_EDIT_EMPTY_DATE_PAGE || 'users/1/edit';
const b=await chromium.launch(); const page=await b.newPage({viewport:{width:1440,height:900}});
await page.goto(`${BASE}/login`); await page.fill('input[type=email]', EMAIL); await page.fill('input[type=password]', PASSWORD); await page.click('button[type=submit]');
await page.waitForURL(u=>!u.pathname.endsWith('/login'),{timeout:60000});
const run=(ctx)=>page.evaluate(([s,c])=>(new Function('arg', s+'; return shadcnUiChecks(arg)'))(c),[src,ctx||{}]);
const cases=[
 ['input tidak sejajar',EDIT,'.fi-input-wrp input.fi-input{min-height:0!important}',null,'input-vertically-centered'],
 ['avatar lonjong','','.fi-sidebar-footer .fi-avatar{width:20px!important;min-width:20px!important;max-width:20px!important}',null,'avatar-square'],
 ['form tanpa kartu',EDIT,'.fi-main .fi-sc-form > .fi-sc-has-gap{background:transparent!important;box-shadow:none!important}',null,'field-inside-card'],
 ['kalender bawaan',EDIT,null,"document.querySelector('.fi-fo-field input:not([type=hidden])').type='date'",'no-native-date-input'],
 ['tanggal kosong tanpa petunjuk',EDIT_EMPTY_DATE,null,"document.querySelectorAll('.fi-fo-date-time-picker input').forEach(i=>i.placeholder='')",'date-picker-placeholder'],
 ['overflow','',null,"document.body.insertAdjacentHTML('beforeend','<div style=\"width:3000px;height:1px\"></div>')",'no-horizontal-overflow'],
 ['sidebar ciut miring','','.fi-main-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav-groups{margin-inline:0!important}','COLLAPSE','collapsed-sidebar-centered'],
];
let ok=0;
for (const [name,path,css,js,rule] of cases) {
  await page.goto(`${BASE}/${path}`); await page.waitForLoadState('networkidle'); await page.waitForTimeout(300);
  if (css) await page.addStyleTag({content:css});
  let ctx={};
  if (js==='COLLAPSE') { await page.click('.sc-header-sidebar-btn'); await page.waitForTimeout(600); ctx={collapsed:true}; }
  else if (js) await page.evaluate(js);
  const fails=await run(ctx); const hit=fails.some(f=>f.rule===rule);
  console.log(`${hit?'✓ tertangkap':'✗ LOLOS (aturan gagal)'}  ${name.padEnd(30)} → ${rule}`); if(hit) ok++;
  if (js==='COLLAPSE') { await page.click('.sc-header-sidebar-btn'); await page.waitForTimeout(300); }
}
console.log(`${ok}/${cases.length} bug lama tertangkap`);
await b.close();
if (ok !== cases.length) process.exit(1);
