/*
 * Uji tampilan otomatis untuk tema filament-shadcn.
 *
 *   node run.mjs                     # Chromium + Safari (Safari dilewati bila belum diizinkan)
 *   node run.mjs --browser=chromium
 *   node run.mjs --browser=safari
 *
 * Pengaturan lewat env (bawaan: proyek uji ../shadcn-playground):
 *   UI_BASE_URL   http://127.0.0.1:8765/admin
 *   UI_EMAIL      admin@example.com
 *   UI_PASSWORD   password
 *   UI_PAGES      ,appearance,profile,users,users/create        (path relatif terhadap UI_BASE_URL)
 *   UI_VIEWPORTS  1440x900,820x1180,390x844
 *
 * Hasil: output/<browser>/*.png dan output/report.json. Keluar dengan kode 1 bila ada pelanggaran.
 */

import { spawn } from 'node:child_process';
import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const checksSource = readFileSync(join(here, 'checks.js'), 'utf8');

const BASE = (process.env.UI_BASE_URL || 'http://127.0.0.1:8765/admin').replace(/\/$/, '');
const EMAIL = process.env.UI_EMAIL || 'admin@example.com';
const PASSWORD = process.env.UI_PASSWORD || 'password';
const PAGES = (process.env.UI_PAGES ?? ',appearance,profile,users,users/create').split(',');
const VIEWPORTS = (process.env.UI_VIEWPORTS || '1440x900,820x1180,390x844').split(',').map((v) => v.split('x').map(Number));
const THEMES = ['light', 'dark'];

const browserArg = (process.argv.find((a) => a.startsWith('--browser=')) || '').split('=')[1];
const browsers = browserArg ? [browserArg] : ['chromium', 'safari'];

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));
const slug = (path) => (path || 'dashboard').replace(/[^a-z0-9]+/gi, '-');

/* ---------------------------------------------------------------- driver: Chromium */

async function chromiumDriver() {
    const { chromium } = await import('playwright');
    const browser = await chromium.launch();
    const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    const errors = [];
    page.on('pageerror', (e) => errors.push(String(e).slice(0, 200)));
    page.on('response', (r) => r.status() >= 500 && errors.push(`${r.status()} ${r.url()}`));

    return {
        name: 'chromium',
        async goto(url) { await page.goto(url); await page.waitForLoadState('networkidle'); await sleep(400); },
        async viewport(w, h) { await page.setViewportSize({ width: w, height: h }); return w; },
        async eval(script, arg) { return page.evaluate(([s, a]) => (new Function('arg', s))(a), [script, arg]); },
        async screenshot(path) { await page.screenshot({ path, fullPage: true }); },
        takeErrors() { return errors.splice(0); },
        async close() { await browser.close(); },
    };
}

/* ---------------------------------------------------------------- driver: Safari (safaridriver) */

async function safariDriver() {
    const port = 4723 + Math.floor(Math.random() * 100);
    const proc = spawn('safaridriver', ['-p', String(port)], { stdio: 'ignore' });
    const url = `http://127.0.0.1:${port}`;
    await sleep(1500);

    const call = async (method, path, body) => {
        const res = await fetch(url + path, { method, headers: { 'Content-Type': 'application/json' }, body: body ? JSON.stringify(body) : undefined });
        const json = await res.json();
        if (json.value && json.value.error) throw new Error(json.value.message || json.value.error);
        return json.value;
    };

    let session;
    try {
        session = (await call('POST', '/session', { capabilities: { alwaysMatch: { browserName: 'safari' } } })).sessionId;
    } catch (e) {
        proc.kill();
        throw new Error(`Safari tidak bisa dikendalikan: ${e.message}`);
    }

    const s = (path) => `/session/${session}${path}`;

    return {
        name: 'safari',
        async goto(target) {
            await call('POST', s('/url'), { url: target });
            for (let i = 0; i < 50; i++) {
                if ((await call('POST', s('/execute/sync'), { script: 'return document.readyState', args: [] })) === 'complete') break;
                await sleep(200);
            }
            await sleep(900);
        },
        async viewport(w, h) {
            const chrome = await call('POST', s('/execute/sync'), { script: 'return [outerWidth - innerWidth, outerHeight - innerHeight]', args: [] });
            await call('POST', s('/window/rect'), { width: w + chrome[0], height: h + chrome[1] });
            await sleep(300);
            return call('POST', s('/execute/sync'), { script: 'return innerWidth', args: [] });
        },
        async eval(script, arg) { return call('POST', s('/execute/sync'), { script: 'return (new Function("arg", arguments[0]))(arguments[1]);', args: [script, arg] }); },
        async screenshot(path) { writeFileSync(path, Buffer.from(await call('GET', s('/screenshot')), 'base64')); },
        takeErrors() { return []; },
        async close() { await call('DELETE', s('')).catch(() => {}); proc.kill(); },
    };
}

/* ---------------------------------------------------------------- skenario */

const runChecksScript = `${checksSource}; return shadcnUiChecks(arg);`;

const renderedScript = `
    if (!document.querySelector('.fi-layout, .fi-simple-layout')) {
        return [{ rule: 'page-rendered', where: document.title, detail: (document.body.innerText || '').slice(0, 120) }];
    }
    return [];
`;

async function check(driver, label, ctx, shot) {
    const fails = [...(await driver.eval(renderedScript)), ...(await driver.eval(runChecksScript, ctx))];
    for (const e of driver.takeErrors()) fails.push({ rule: 'no-js-or-server-error', where: label, detail: e });
    await driver.screenshot(shot);

    return fails.map((f) => ({ browser: driver.name, scenario: label, ...f }));
}

async function setTheme(driver, theme) {
    await driver.eval(`localStorage.setItem('filament:theme:mode', arg); return true;`, theme);
}

async function login(driver) {
    await driver.goto(`${BASE}/login`);
    await driver.eval(`
        const set = (el, v) => { el.value = v; el.dispatchEvent(new Event('input', { bubbles: true })); };
        set(document.querySelector('input[type=email]'), arg[0]);
        set(document.querySelector('input[type=password]'), arg[1]);
        document.querySelector('form button[type=submit]').click();
        return true;
    `, [EMAIL, PASSWORD]);

    for (let i = 0; i < 60; i++) {
        await sleep(250);
        // Halaman bisa sedang berpindah saat dibaca (server baru menyala = lambat): coba lagi.
        const path = await driver.eval('return location.pathname').catch(() => '/login');
        if (!path.endsWith('/login')) return;
    }

    throw new Error('Gagal masuk: periksa UI_EMAIL / UI_PASSWORD.');
}

async function runBrowser(name) {
    const driver = name === 'safari' ? await safariDriver() : await chromiumDriver();
    const out = join(here, 'output', name);
    mkdirSync(out, { recursive: true });
    const results = [];

    try {
        // Halaman login (tamu).
        await driver.goto(`${BASE}/login`);
        for (const [w, h] of VIEWPORTS) {
            const actual = await driver.viewport(w, h);
            for (const theme of THEMES) {
                await setTheme(driver, theme);
                await driver.goto(`${BASE}/login`);
                results.push(...await check(driver, `login @${actual} ${theme}`, {}, join(out, `login-${w}-${theme}.png`)));
            }
        }

        await driver.viewport(1440, 900);
        await setTheme(driver, 'light');
        await login(driver);

        // Halaman panel.
        for (const path of PAGES) {
            for (const [w, h] of VIEWPORTS) {
                const actual = await driver.viewport(w, h);
                for (const theme of THEMES) {
                    await setTheme(driver, theme);
                    await driver.goto(`${BASE}/${path}`);
                    results.push(...await check(driver, `/${path} @${actual} ${theme}`, {}, join(out, `${slug(path)}-${w}-${theme}.png`)));
                }
            }
        }

        // Sidebar diciutkan (desktop).
        await driver.viewport(1440, 900);
        await setTheme(driver, 'light');
        await driver.goto(`${BASE}/`);
        const canCollapse = await driver.eval(`
            const btn = document.querySelector('.sc-header-sidebar-btn');
            if (!btn || getComputedStyle(btn).display === 'none') return false;
            btn.click();
            return true;
        `);
        if (canCollapse) {
            await new Promise((r) => setTimeout(r, 700));
            results.push(...await check(driver, 'sidebar ciut @1440', { collapsed: true }, join(out, 'sidebar-collapsed.png')));
            await driver.eval(`document.querySelector('.sc-header-sidebar-btn').click(); return true;`);
            await new Promise((r) => setTimeout(r, 400));
        }
    } finally {
        await driver.close();
    }

    return results;
}

/* ---------------------------------------------------------------- utama */

const all = [];
const skipped = [];

for (const name of browsers) {
    try {
        console.log(`\n▶ ${name}`);
        const results = await runBrowser(name);
        all.push(...results);
        console.log(results.length ? `  ✗ ${results.length} pelanggaran` : '  ✓ semua aturan lolos');
    } catch (e) {
        if (name === 'safari' && !browserArg) {
            skipped.push(`${name}: ${e.message}`);
            console.log(`  – dilewati: ${e.message}`);
        } else {
            throw e;
        }
    }
}

mkdirSync(join(here, 'output'), { recursive: true });
writeFileSync(join(here, 'output', 'report.json'), JSON.stringify({ base: BASE, failures: all, skipped }, null, 2));

if (all.length) {
    console.log('\nPelanggaran:');
    for (const f of all) console.log(`  [${f.browser}] ${f.rule} — ${f.scenario} — ${f.where} ${f.detail}`);
    process.exit(1);
}

console.log(`\nSelesai tanpa pelanggaran. Screenshot: ${join(here, 'output')}`);
