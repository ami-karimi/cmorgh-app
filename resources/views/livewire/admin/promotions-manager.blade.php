<div class="commerce-page">
    @php
        $kindLabels = ['coupon'=>'کد تخفیف', 'plan_discount'=>'تخفیف پلن', 'topup_bonus'=>'بونوس شارژ'];
        $audienceLabels = ['customer'=>'مشتری‌ها', 'agent'=>'نماینده‌ها', 'all'=>'همه کاربران'];
        $running = $promotions->filter(fn($p) => $p->is_active && (!$p->starts_at || $p->starts_at->lte(now())) && (!$p->ends_at || $p->ends_at->gt(now())))->count();
        $scheduled = $promotions->filter(fn($p) => $p->is_active && $p->starts_at?->isFuture())->count();
    @endphp
    <header class="commerce-heading">
        <div class="commerce-heading-main"><span class="commerce-mark"><x-ui.icon name="ticket" /></span><div><h1>تخفیف‌ها و بونوس شارژ</h1><p>یک پیشنهاد خوب، برای کاربر درست، در زمان درست.</p></div></div>
        <button type="button" wire:click="resetForm" class="commerce-button commerce-button-secondary"><x-ui.icon name="plus" />کمپین جدید</button>
    </header>
    @if(session('success'))<div role="status" class="commerce-alert commerce-alert-success"><x-ui.icon name="check" /><div>{{ session('success') }}</div></div>@endif
    @if($errors->any())<div role="alert" class="commerce-alert commerce-alert-error"><x-ui.icon name="alert" /><div><strong>چند مورد نیاز به بررسی دارد</strong>@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div></div>@endif
    <div class="commerce-stat-grid">
        <div class="commerce-stat"><x-ui.icon name="ticket" /><div><span>همه کمپین‌ها</span><strong>{{ $promotions->count() }}</strong></div></div>
        <div class="commerce-stat"><x-ui.icon name="check" /><div><span>در حال اجرا</span><strong>{{ $running }}</strong></div></div>
        <div class="commerce-stat"><x-ui.icon name="calendar" /><div><span>زمان‌بندی‌شده</span><strong>{{ $scheduled }}</strong></div></div>
    </div>
    <div class="commerce-form-layout">
        <form wire:submit="save" class="commerce-panel">
            <div class="commerce-panel-head"><div><h2>{{ $editingId ? 'ویرایش کمپین' : 'ساخت یک پیشنهاد جدید' }}</h2><p>نوع پیشنهاد را انتخاب کنید؛ تنظیمات مرتبط نمایش داده می‌شوند.</p></div><span class="commerce-badge">{{ $editingId ? 'در حال ویرایش' : 'پیشنهاد جدید' }}</span></div>
            <section class="commerce-section">
                <div class="commerce-section-heading"><span>۱</span><h3>مشخصات کمپین</h3></div>
                <div class="commerce-type-grid" role="group" aria-label="نوع کمپین">
                    @foreach(['coupon'=>['ticket','کد تخفیف','ورود کد هنگام خرید'], 'plan_discount'=>['sparkles','تخفیف پلن','اعمال خودکار روی تعرفه'], 'topup_bonus'=>['gift','بونوس شارژ','اعتبار بیشتر در کیف پول']] as $type=>$details)
                        <button type="button" wire:click="$set('kind','{{ $type }}')" class="commerce-type" aria-pressed="{{ $kind === $type ? 'true' : 'false' }}"><x-ui.icon :name="$details[0]" /><strong>{{ $details[1] }}</strong><small>{{ $details[2] }}</small></button>
                    @endforeach
                </div>
                <div class="commerce-fields mt-5">
                    <div class="commerce-field"><label for="campaign-name">عنوان کمپین</label><input id="campaign-name" wire:model.live.debounce.350ms="name" class="commerce-input" placeholder="مثلاً پیشنهاد ویژه پاییز" maxlength="255">@error('name')<p class="commerce-field-error">{{ $message }}</p>@enderror</div>
                    <div class="commerce-field"><label for="campaign-audience">مخاطب پیشنهاد</label><select id="campaign-audience" wire:model.live="audience" class="commerce-input"><option value="customer">مشتری‌ها</option><option value="agent">نماینده‌ها و زیرنماینده‌ها</option><option value="all">همه کاربران</option></select></div>
                    @if($kind === 'coupon')<div class="commerce-field commerce-wide"><label for="campaign-code">کد تخفیف <span class="commerce-optional">حروف انگلیسی و عدد</span></label><input id="campaign-code" wire:model.live.debounce.350ms="code" dir="ltr" class="commerce-input" placeholder="WELCOME10" maxlength="64" autocomplete="off"><p class="commerce-hint">کاربر این کد را هنگام ثبت سفارش وارد می‌کند.</p>@error('code')<p class="commerce-field-error">{{ $message }}</p>@enderror</div>@endif
                </div>
            </section>
            <section class="commerce-section">
                <div class="commerce-section-heading"><span>۲</span><h3>{{ $kind === 'topup_bonus' ? 'مقدار اعتبار هدیه' : 'مقدار تخفیف' }}</h3></div>
                <div class="commerce-fields">
                    <div class="commerce-field"><label for="campaign-value-type">روش محاسبه</label><select id="campaign-value-type" wire:model.live="value_type" class="commerce-input"><option value="percent">درصدی</option><option value="fixed">مبلغ ثابت</option></select></div>
                    <div class="commerce-field"><label for="campaign-value">مقدار <span class="commerce-optional">{{ $value_type === 'percent' ? 'درصد' : 'تومان' }}</span></label><input id="campaign-value" wire:model.live.debounce.350ms="value" type="number" min="1" max="{{ $value_type === 'percent' ? 100 : 100000000 }}" class="commerce-input commerce-numeric" dir="ltr">@error('value')<p class="commerce-field-error">{{ $message }}</p>@enderror</div>
                    <div class="commerce-field"><label for="campaign-minimum">حداقل {{ $kind === 'topup_bonus' ? 'شارژ' : 'خرید' }} <span class="commerce-optional">تومان</span></label><input id="campaign-minimum" wire:model="min_amount" type="number" min="0" class="commerce-input commerce-numeric" dir="ltr"><p class="commerce-hint">صفر یعنی بدون محدودیت حداقل مبلغ.</p></div>
                    <div class="commerce-field"><label for="campaign-cap">سقف {{ $kind === 'topup_bonus' ? 'بونوس' : 'تخفیف' }} <span class="commerce-optional">اختیاری · تومان</span></label><input id="campaign-cap" wire:model="max_discount" type="number" min="1" class="commerce-input commerce-numeric" placeholder="بدون سقف" dir="ltr"><p class="commerce-hint">بیشترین منفعت هر خرید یا شارژ.</p></div>
                </div>
            </section>
            <section class="commerce-section">
                <div class="commerce-section-heading"><span>۳</span><h3>زمان‌بندی و محدودیت استفاده</h3></div>
                <div class="commerce-fields">
                    <x-ui.jalali-datetime model="starts_at" label="شروع کمپین" hint="خالی باشد، کمپین بلافاصله قابل اجراست." />
                    <x-ui.jalali-datetime model="ends_at" label="پایان کمپین" hint="خالی باشد، کمپین تاریخ پایان ندارد." />
                    @if($kind !== 'plan_discount')
                        <div class="commerce-field"><label for="campaign-limit">سقف مصرف کل <span class="commerce-optional">اختیاری</span></label><input id="campaign-limit" wire:model="usage_limit" type="number" min="1" class="commerce-input" placeholder="نامحدود" dir="ltr"></div>
                        <div class="commerce-field"><label for="campaign-user-limit">سقف مصرف هر کاربر <span class="commerce-optional">اختیاری</span></label><input id="campaign-user-limit" wire:model="per_user_limit" type="number" min="1" class="commerce-input" placeholder="نامحدود" dir="ltr"></div>
                    @else<p class="commerce-hint commerce-wide">تخفیف خودکار پلن با بازه زمانی محدود می‌شود؛ محدودیت دفعات برای کد تخفیف و بونوس است.</p>@endif
                </div>
            </section>
            <section class="commerce-section">
                <div class="commerce-section-heading"><span>۴</span><h3>دامنه اجرا</h3></div>
                <div class="commerce-fields">
                    @if($kind !== 'topup_bonus')<div class="commerce-field"><label>پلن‌های مشمول <span class="commerce-optional">خالی = همه پلن‌ها</span></label><div class="commerce-scope-list">@forelse($groups as $group)<label><input type="checkbox" wire:model="group_ids" value="{{ $group->id }}">{{ $group->name }}</label>@empty<span class="commerce-hint">پلنی ثبت نشده است.</span>@endforelse</div>@error('group_ids')<p class="commerce-field-error">{{ $message }}</p>@enderror</div>@endif
                    <div class="commerce-field {{ $kind === 'topup_bonus' ? 'commerce-wide' : '' }}"><label for="campaign-users">کاربران خاص <span class="commerce-optional">اختیاری</span></label><input id="campaign-users" wire:model="user_ids_text" dir="ltr" class="commerce-input" placeholder="12, 25, 38"><p class="commerce-hint">شناسه‌ها را با کاما جدا کنید؛ خالی یعنی همه مخاطبان انتخاب‌شده.</p>@error('user_ids_text')<p class="commerce-field-error">{{ $message }}</p>@enderror</div>
                </div>
            </section>
            <footer class="commerce-form-footer"><label class="commerce-check"><input wire:model.live="is_active" type="checkbox">کمپین فعال باشد</label><button type="submit" wire:loading.attr="disabled" class="commerce-button"><x-ui.icon name="check" /><span wire:loading.remove wire:target="save">{{ $editingId ? 'ذخیره تغییرات' : 'ساخت کمپین' }}</span><span wire:loading wire:target="save">در حال ذخیره…</span></button></footer>
        </form>
        <aside class="commerce-summary">
            <section class="commerce-panel commerce-preview"><span class="commerce-badge">پیش‌نمایش پیشنهاد</span><h2 class="text-sm font-bold mt-5">{{ $name ?: 'عنوان کمپین شما' }}</h2><div class="commerce-preview-value">{{ is_numeric($value) ? number_format($value) : '—' }}<small>{{ $value_type === 'percent' ? 'درصد' : 'تومان' }} {{ $kind === 'topup_bonus' ? 'هدیه' : 'تخفیف' }}</small></div>@if($kind === 'coupon')<code dir="ltr" class="commerce-badge">{{ strtoupper($code ?: 'YOURCODE') }}</code>@endif<hr class="commerce-divider"><dl class="commerce-summary-rows"><div><dt>نوع پیشنهاد</dt><dd>{{ $kindLabels[$kind] ?? '—' }}</dd></div><div><dt>مخاطب</dt><dd>{{ $audienceLabels[$audience] ?? '—' }}</dd></div><div><dt>وضعیت</dt><dd>{{ $is_active ? 'فعال پس از ذخیره' : 'غیرفعال' }}</dd></div></dl></section>
            <div class="commerce-alert"><x-ui.icon name="shield" /><div><strong>قیمت‌گذاری کنترل‌شده</strong><p>کد تخفیف جایگزین پیشنهاد خودکار است؛ تخفیف‌ها روی هم جمع نمی‌شوند.</p><p class="mt-2">در فروشگاه نماینده، قیمت نهایی کمتر از هزینه عمده نخواهد شد. بونوس فقط برای شارژ کیف پول است.</p></div></div>
        </aside>
    </div>
    <section class="commerce-panel">
        <div class="commerce-panel-head"><div><h2>کمپین‌های ثبت‌شده</h2><p>زمان‌ها شمسی و به وقت تهران نمایش داده می‌شوند.</p></div><span class="commerce-badge commerce-badge-muted">{{ $promotions->count() }} کمپین</span></div>
        @if($promotions->isEmpty())<div class="commerce-empty"><span class="commerce-mark"><x-ui.icon name="ticket" /></span><strong>اولین پیشنهاد شما از اینجا شروع می‌شود</strong><p>فرم بالا را تکمیل کنید تا کمپین در این فهرست نمایش داده شود.</p></div>@else
        <div class="commerce-table-wrap"><table class="commerce-table"><thead><tr><th>کمپین</th><th>مخاطب / پیشنهاد</th><th>مقدار</th><th>زمان‌بندی شمسی</th><th>وضعیت</th><th>عملیات</th></tr></thead><tbody>
            @foreach($promotions as $p)
                @php $state = !$p->is_active ? 'paused' : (($p->ends_at && $p->ends_at->lte(now())) ? 'ended' : (($p->starts_at && $p->starts_at->isFuture()) ? 'scheduled' : 'running')); @endphp
                <tr wire:key="campaign-{{ $p->id }}"><td><strong>{{ $p->name }}</strong>@if($p->code)<small dir="ltr">{{ $p->code }}</small>@endif</td><td>{{ $audienceLabels[$p->audience] ?? '—' }}<small>{{ $kindLabels[$p->kind] ?? '—' }}</small></td><td><strong>{{ number_format($p->value) }} {{ $p->value_type === 'percent' ? '٪' : 'تومان' }}</strong>@if($p->max_discount)<small>سقف {{ number_format($p->max_discount) }} تومان</small>@endif</td><td><div class="text-[10px] whitespace-nowrap">از {{ \App\Support\JalaliDateTime::display($p->starts_at) }}<br>تا {{ \App\Support\JalaliDateTime::display($p->ends_at) }}</div></td><td><span class="commerce-badge {{ $state === 'running' ? 'commerce-badge-success' : ($state === 'scheduled' ? 'commerce-badge-warning' : 'commerce-badge-muted') }}">{{ ['running'=>'در حال اجرا','scheduled'=>'زمان‌بندی‌شده','ended'=>'پایان‌یافته','paused'=>'غیرفعال'][$state] }}</span></td><td><div class="commerce-actions"><button type="button" wire:click="edit({{ $p->id }})">ویرایش</button><button type="button" wire:click="toggle({{ $p->id }})" wire:confirm="وضعیت کمپین تغییر کند؟">{{ $p->is_active ? 'غیرفعال‌کردن' : 'فعال‌کردن' }}</button></div></td></tr>
            @endforeach
        </tbody></table></div>@endif
    </section>
</div>
