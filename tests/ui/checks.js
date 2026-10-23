/*
 * Aturan tampilan yang diperiksa otomatis di setiap halaman.
 * Tiap aturan berasal dari masalah nyata yang pernah lolos (lihat README.md).
 * Dijalankan DI DALAM browser (Chromium lewat Playwright, Safari lewat safaridriver),
 * jadi tanpa import dan tanpa sintaks yang belum didukung Safari.
 *
 * @param {{collapsed?: boolean}} ctx
 * @returns {{rule: string, where: string, detail: string}[]}
 */
function shadcnUiChecks(ctx) {
    ctx = ctx || {};
    var fails = [];

    function describe(el) {
        if (!el) return '';
        var field = el.closest && el.closest('.fi-fo-field');
        var label = field && field.querySelector('.fi-fo-field-label-content');
        var cls = typeof el.className === 'string' && el.className ? '.' + el.className.trim().split(/\s+/)[0] : el.tagName.toLowerCase();
        return cls + (label ? ' [' + label.textContent.trim() + ']' : '');
    }

    function fail(rule, el, detail) {
        fails.push({ rule: rule, where: describe(el), detail: detail || '' });
    }

    function visible(el) {
        var r = el.getBoundingClientRect();
        var s = getComputedStyle(el);
        return r.width > 0 && r.height > 0 && s.visibility !== 'hidden' && s.display !== 'none';
    }

    function all(selector) {
        return Array.prototype.slice.call(document.querySelectorAll(selector)).filter(visible);
    }

    var isPanel = !!document.querySelector('.fi-main');

    // 1. Tidak ada scroll horizontal.
    var overflow = document.documentElement.scrollWidth - window.innerWidth;
    if (overflow > 1) fail('no-horizontal-overflow', document.body, overflow + 'px');

    // 2. Isian input/select sejajar di tengah kotaknya (teks sejajar prefix/suffix).
    all('.fi-input-wrp').forEach(function (wrap) {
        if (wrap.querySelector('textarea') || wrap.matches('.fi-fo-key-value, .fi-fo-rich-editor, .fi-fo-markdown-editor, .fi-fo-tags-input')) return;
        var w = wrap.getBoundingClientRect();
        if (w.height > 60) return;
        var inner = wrap.querySelector('input.fi-input:not([type=hidden]), select.fi-select-input, .fi-select-input-btn');
        if (!inner || !visible(inner)) return;
        var r = inner.getBoundingClientRect();
        var top = r.top - w.top;
        var bottom = w.bottom - r.bottom;
        if (Math.abs(top - bottom) > 2) fail('input-vertically-centered', inner, 'atas ' + top.toFixed(1) + 'px, bawah ' + bottom.toFixed(1) + 'px');
    });

    // 3. Avatar selalu bulat/persegi (tidak lonjong).
    all('.fi-avatar').forEach(function (avatar) {
        var r = avatar.getBoundingClientRect();
        if (Math.abs(r.width - r.height) > 1) fail('avatar-square', avatar, r.width.toFixed(1) + 'x' + r.height.toFixed(1));
    });

    // 4. Tidak ada kontrol tanggal bawaan browser (tampilannya beda per browser).
    all('input[type=date], input[type=datetime-local], input[type=time], input[type=month], input[type=week]').forEach(function (input) {
        fail('no-native-date-input', input, input.type);
    });

    // 4b. Kolom tanggal kosong tetap memberi petunjuk (placeholder), tidak tampak seperti rusak.
    all('.fi-fo-date-time-picker').forEach(function (picker) {
        var display = picker.querySelector('input[readonly], input:not([type=hidden])');
        if (display && !display.value && !display.placeholder) fail('date-picker-placeholder', picker, 'kosong tanpa petunjuk');
    });

    // 5. Di halaman panel, setiap field berada di dalam kartu (Section, Tabs, atau form berkartu).
    if (isPanel) {
        all('.fi-main .fi-fo-field').forEach(function (field) {
            if (field.closest('.fi-modal, .fi-dropdown-panel, .fi-ta')) return;
            var el = field.parentElement;
            var ok = false;
            while (el && !el.classList.contains('fi-main')) {
                var s = getComputedStyle(el);
                var hasBg = s.backgroundColor !== 'rgba(0, 0, 0, 0)' && s.backgroundColor !== 'transparent';
                if (hasBg && (s.boxShadow !== 'none' || s.borderTopWidth !== '0px')) { ok = true; break; }
                el = el.parentElement;
            }
            if (!ok) fail('field-inside-card', field, 'field menempel di halaman tanpa kartu');
        });
    }

    // 6. Shell: halaman panel punya header shadcn dan sidebar (termasuk halaman profil).
    if (isPanel && document.querySelector('html[data-sc-shell="on"]')) {
        if (!document.querySelector('.sc-header')) fail('shell-header-present', document.body, '');
        if (!document.querySelector('.fi-sidebar')) fail('sidebar-present', document.body, '');
    }
    if (/\/profile\/?$/.test(location.pathname) && !document.querySelector('.fi-sidebar')) {
        fail('profile-in-panel', document.body, 'profil tampil di layout terpisah');
    }

    // 7. Sidebar diciutkan: merek, ikon menu, dan avatar segaris tengah.
    if (ctx.collapsed) {
        var sidebar = document.querySelector('.fi-main-sidebar');
        if (sidebar) {
            var sb = sidebar.getBoundingClientRect();
            var mid = sb.left + sb.width / 2;
            all('.fi-main-sidebar .sc-brand-icon, .fi-main-sidebar .fi-sidebar-item-btn, .fi-sidebar-footer .fi-avatar').forEach(function (el) {
                var r = el.getBoundingClientRect();
                var diff = r.left + r.width / 2 - mid;
                if (Math.abs(diff) > 1.5) fail('collapsed-sidebar-centered', el, 'bergeser ' + diff.toFixed(1) + 'px');
            });
            if (!all('.fi-main-sidebar .sc-brand-icon').length && document.querySelector('.sc-brand')) {
                fail('collapsed-sidebar-brand-visible', sidebar, 'kotak ikon merek hilang');
            }
        }
    }

    // 8. Login split: form dan panel gambar satu kartu (atas & tinggi sama).
    var aside = document.querySelector('.sc-auth-aside');
    var mainCtn = document.querySelector('.fi-simple-main-ctn');
    if (aside && mainCtn && visible(aside)) {
        var a = aside.getBoundingClientRect();
        var m = mainCtn.getBoundingClientRect();
        if (Math.abs(a.top - m.top) > 1 || Math.abs(a.height - m.height) > 1) {
            fail('auth-split-one-card', aside, 'form ' + m.top.toFixed(0) + '/' + m.height.toFixed(0) + ' vs gambar ' + a.top.toFixed(0) + '/' + a.height.toFixed(0));
        }
    }

    return fails;
}

if (typeof module !== 'undefined') module.exports = { shadcnUiChecks: shadcnUiChecks };
