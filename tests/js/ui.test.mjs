import test from 'node:test';
import assert from 'node:assert/strict';
import { normalizeToast, showToast } from '../../resources/js/ui.js';

test('named Livewire payloads are normalized', () => {
    assert.deepEqual(normalizeToast({ type: 'success', message: 'انجام شد' }), { type: 'success', title: 'انجام شد', message: 'انجام شد' });
});
test('positional Livewire payloads from existing financial components are supported', () => {
    assert.deepEqual(normalizeToast([{ type: 'error', title: 'خطا', message: 'ناموفق' }]), { type: 'error', title: 'خطا', message: 'ناموفق' });
});
test('unexpected and empty payloads have safe defaults', () => {
    assert.equal(normalizeToast(null).type, 'info');
    assert.equal(normalizeToast([]).message, '');
    assert.equal(normalizeToast({ type: 'unknown' }).type, 'info');
});
test('message markup remains plain text in the normalizer', () => {
    assert.equal(normalizeToast({ message: '<img onerror=alert(1)>' }).message, '<img onerror=alert(1)>');
});
test('no feedback region during navigation is handled without an error', () => {
    globalThis.document = { getElementById: () => null };
    assert.doesNotThrow(() => showToast('success', 'آزمایش', 'متن'));
    delete globalThis.document;
});
