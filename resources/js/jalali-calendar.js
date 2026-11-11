const formatter = new Intl.DateTimeFormat('en-US-u-ca-persian', {
    year: 'numeric', month: 'numeric', day: 'numeric', timeZone: 'UTC', numberingSystem: 'latn',
});
const tehranFormatter = new Intl.DateTimeFormat('en-US-u-ca-persian', {
    year: 'numeric', month: 'numeric', day: 'numeric', timeZone: 'Asia/Tehran', numberingSystem: 'latn',
});
export const monthNames = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
export const normalizeDigits = value => String(value ?? '').replace(/[۰-۹٠-٩]/g, digit => {
    const c = digit.charCodeAt(0); return String(c >= 1776 ? c - 1776 : c - 1632);
});
export const persianDigits = value => String(value).replace(/[0-9]/g, n => '۰۱۲۳۴۵۶۷۸۹'[Number(n)]);
const pad = value => String(value).padStart(2, '0');
export function jalaliParts(date, tehran = false) {
    const parts = Object.fromEntries((tehran ? tehranFormatter : formatter).formatToParts(date).map(p => [p.type, p.value]));
    return { year: Number(parts.year), month: Number(parts.month), day: Number(parts.day) };
}
export function gregorianDate(year, month, day = 1) {
    let first;
    for (let d = 17; d <= 26; d++) {
        const candidate = new Date(Date.UTC(year + 621, 2, d, 12));
        const p = jalaliParts(candidate);
        if (p.year === year && p.month === 1 && p.day === 1) { first = candidate; break; }
    }
    if (!first || month < 1 || month > 12 || day < 1 || day > 31) return null;
    const offset = month <= 6 ? (month - 1) * 31 : 186 + (month - 7) * 30;
    const date = new Date(first.getTime() + (offset + day - 1) * 86400000);
    const parts = jalaliParts(date);
    return parts.year === year && parts.month === month && parts.day === day ? date : null;
}
export function calendarCells(year, month) {
    const first = gregorianDate(year, month);
    if (!first) return [];
    const cells = Array.from({ length: (first.getUTCDay() + 1) % 7 }, (_, index) => ({ key: `blank-${index}`, day: null }));
    for (let day = 1; day <= 31; day++) {
        if (!gregorianDate(year, month, day)) break;
        cells.push({ key: `day-${day}`, day });
    }
    return cells;
}
export function parseInput(value) {
    const match = normalizeDigits(value).match(/^(13\d{2}|14\d{2})\/(\d{2})\/(\d{2}) (\d{2}):(\d{2})$/);
    if (!match || Number(match[4]) > 23 || Number(match[5]) > 59 || !gregorianDate(Number(match[1]), Number(match[2]), Number(match[3]))) return null;
    return { year: Number(match[1]), month: Number(match[2]), day: Number(match[3]), time: `${match[4]}:${match[5]}` };
}
export function jalaliPicker(value) {
    const today = jalaliParts(new Date(), true);
    return {
        value, open: false, year: today.year, month: today.month, time: '00:00', monthNames,
        persianDigits,
        init() { this.sync(); this.$watch('value', () => this.sync()); },
        sync() { const p = parseInput(this.value); if (p) { this.year = p.year; this.month = p.month; this.time = p.time; } },
        get cells() { return calendarCells(Number(this.year), Number(this.month)); },
        get years() { return Array.from({length: 200}, (_, i) => 1300+i); },
        get selected() { return parseInput(this.value); },
        move(delta) { let m = Number(this.month)+delta; let y = Number(this.year); if (m < 1) {m=12; y--;} if (m > 12) {m=1; y++;} if (y >= 1300 && y <= 1499) {this.year=y; this.month=m;} },
        choose(day) { this.value = `${this.year}/${pad(this.month)}/${pad(day)} ${this.time || '00:00'}`; this.open=false; this.$refs.trigger.focus(); },
        updateTime() { const p=this.selected; if (p) this.value=`${p.year}/${pad(p.month)}/${pad(p.day)} ${this.time || '00:00'}`; },
        clear() { this.value=''; this.open=false; this.$refs.trigger.focus(); },
    };
}
if (typeof window !== 'undefined') {
    window.jalaliPicker = jalaliPicker;
    const register = () => window.Alpine?.data('jalaliPicker', jalaliPicker);
    document.addEventListener('alpine:init', register, { once: true });
    if (window.Alpine) register();
}
