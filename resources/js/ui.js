// Registered once per document. Livewire navigation replaces the region, not the listener.
export function normalizeToast(detail = {}) {
    const payload = Array.isArray(detail) ? detail[0] : detail;
    const types = ['success', 'error', 'warning', 'info'];
    const type = types.includes(payload?.type) ? payload.type : 'info';
    return {
        type,
        title: String(payload?.title || ({ success: 'انجام شد', error: 'خطا', warning: 'توجه', info: 'اطلاع‌رسانی' }[type])),
        message: String(payload?.message || ''),
    };
}

export function showToast(type, title, message) {
    const region = document.getElementById('ui-toast-region');
    if (!region) return;
    const payload = normalizeToast({ type, title, message });
    const toast = document.createElement('div');
    toast.className = 'ui-toast';
    toast.dataset.type = payload.type;
    toast.setAttribute('role', payload.type === 'error' ? 'alert' : 'status');
    const content = document.createElement('div');
    const heading = document.createElement('strong');
    const body = document.createElement('p');
    heading.textContent = payload.title;
    body.textContent = payload.message;
    content.append(heading, body);
    const close = document.createElement('button');
    close.type = 'button';
    close.setAttribute('aria-label', 'بستن پیام');
    close.textContent = '×';
    toast.append(content, close);
    while (region.children.length >= 4) region.firstElementChild.remove();
    region.append(toast);
    let timer;
    const dismiss = () => { clearTimeout(timer); toast.remove(); };
    const schedule = () => { clearTimeout(timer); timer = setTimeout(dismiss, payload.type === 'error' ? 10000 : 6000); };
    close.addEventListener('click', dismiss);
    toast.addEventListener('mouseenter', () => clearTimeout(timer));
    toast.addEventListener('mouseleave', schedule);
    toast.addEventListener('focusin', () => clearTimeout(timer));
    toast.addEventListener('focusout', schedule);
    schedule();
}

export async function copyToClipboard(text, label = 'متن') {
    if (!text) return showToast('warning', 'اطلاعاتی موجود نیست', 'اطلاعاتی برای کپی وجود ندارد.');
    try {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(String(text));
        } else {
            const previousFocus = document.activeElement;
            const field = document.createElement('textarea');
            field.value = String(text);
            field.style.cssText = 'position:fixed;opacity:0;top:0;left:0';
            document.body.append(field);
            field.select();
            try {
                if (!document.execCommand('copy')) throw new Error('Copy failed');
            } finally {
                field.remove();
                previousFocus?.focus();
            }
        }
        showToast('success', 'کپی شد', `${label} با موفقیت کپی شد.`);
    } catch {
        showToast('error', 'کپی انجام نشد', 'لطفاً متن را انتخاب و به‌صورت دستی کپی کنید.');
    }
}

if (typeof window !== 'undefined') {
    window.showToast = showToast;
    window.copyToClipboard = copyToClipboard;
    window.addEventListener('toast', event => {
        const { type, title, message } = normalizeToast(event.detail);
        showToast(type, title, message);
    });
    // Charts in existing Alpine components use the same Persian family and palette.
    const initializeChartDefaults = () => {
        if (window.Chart) {
            window.Chart.defaults.font.family = 'IRANSans';
            window.Chart.defaults.color = '#a5b4c9';
        }
    };
    document.addEventListener('livewire:navigated', initializeChartDefaults);
    window.addEventListener('load', initializeChartDefaults);
}
