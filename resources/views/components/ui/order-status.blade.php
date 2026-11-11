@props(['order'])
<span class="commerce-badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
