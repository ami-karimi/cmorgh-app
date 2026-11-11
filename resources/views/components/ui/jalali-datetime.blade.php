@props(['model', 'label', 'hint' => 'اختیاری · به وقت تهران'])
<div class="commerce-field" x-data="jalaliPicker($wire.entangle('{{ $model }}'))" @keydown.escape.stop="open = false; $refs.trigger.focus()">
    <label for="date-{{ $model }}">{{ $label }} <span class="commerce-optional">شمسی</span></label>
    <div class="commerce-date-control">
        <input id="date-{{ $model }}" type="text" x-model="value" dir="ltr" autocomplete="off" placeholder="1405/08/21 14:30" aria-describedby="hint-{{ $model }}" class="commerce-input commerce-numeric" @blur="sync()">
        <button type="button" x-ref="trigger" @click="sync(); open = !open" :aria-expanded="open" aria-controls="calendar-{{ $model }}" aria-label="انتخاب {{ $label }} از تقویم شمسی" class="commerce-calendar-trigger"><x-ui.icon name="calendar" /></button>
    </div>
    <p id="hint-{{ $model }}" class="commerce-hint">{{ $hint }}</p>
    @error($model)<p role="alert" class="commerce-field-error">{{ $message }}</p>@enderror
    <div id="calendar-{{ $model }}" class="commerce-calendar" x-show="open" x-cloak @click.outside="open = false" role="group" aria-label="تقویم شمسی {{ $label }}">
        <div class="commerce-calendar-heading">
            <button type="button" @click="move(-1)" aria-label="ماه قبل" class="commerce-calendar-nav">›</button>
            <select x-model.number="month" aria-label="ماه شمسی"><template x-for="(name, index) in monthNames" :key="index"><option :value="index + 1" :selected="Number(month) === index + 1" x-text="name"></option></template></select>
            <select x-model.number="year" aria-label="سال شمسی"><template x-for="yearOption in years" :key="yearOption"><option :value="yearOption" :selected="Number(year) === yearOption" x-text="persianDigits(yearOption)"></option></template></select>
            <button type="button" @click="move(1)" aria-label="ماه بعد" class="commerce-calendar-nav">‹</button>
        </div>
        <div class="commerce-calendar-grid"><template x-for="label in ['ش','ی','د','س','چ','پ','ج']"><span class="commerce-calendar-weekday" x-text="label"></span></template></div>
        <div class="commerce-calendar-grid">
            <template x-for="cell in cells" :key="cell.key">
                <button type="button" :disabled="cell.day === null" @click="choose(cell.day)" :class="{'is-selected': selected && selected.year === Number(year) && selected.month === Number(month) && selected.day === cell.day}" :aria-label="cell.day ? persianDigits(cell.day) + ' ' + monthNames[month-1] + ' ' + persianDigits(year) : ''" :aria-pressed="!!(selected && selected.year === Number(year) && selected.month === Number(month) && selected.day === cell.day)" x-text="cell.day ? persianDigits(cell.day) : ''"></button>
            </template>
        </div>
        <div class="commerce-calendar-footer"><label>ساعت <input type="time" x-model="time" @change="updateTime()" aria-label="ساعت {{ $label }}" dir="ltr"></label><button type="button" @click="clear()">بدون تاریخ</button></div>
    </div>
</div>
