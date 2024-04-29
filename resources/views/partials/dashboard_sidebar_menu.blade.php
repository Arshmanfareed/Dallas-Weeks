@php
    use Illuminate\Support\Str;
@endphp
<div class="sidebar_menu col-12  p-0 flex-shrink-1">
    <ul class="list-unstyle p-0 m-0">
        <li><a href="/accdashboard"
                class="nav_link {{ Str::contains(request()->url(), 'accdashboard') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/home.svg') }}" alt=""></a></li>
        <li><a href="{{ route('campaigns') }}"
                class="nav_link {{ Str::contains(request()->url(), 'campaign') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/speaker.svg') }}" alt=""></a></li>
        <li><a href="{{ route('dash-leads') }}"
                class="nav_link {{ Str::contains(request()->url(), 'leads') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/leads.svg') }}" alt=""></a></li>
        <li><a href="{{ route('dash-reports') }}"
                class="nav_link {{ Str::contains(request()->url(), 'report') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/stat.svg') }}" alt=""></a></li>
        <li><a href="{{ route('dash-messages') }}"
                class="nav_link {{ Str::contains(request()->url(), 'message') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/message.svg') }}" alt=""></a></li>
        {{-- <li><a href="#" class="nav_link"><img src="{{ asset('assets/img/phonecall.svg') }}" alt=""></a></li> --}}
        <li><a href="{{ route('dash-integrations') }}"
                class="nav_link {{ Str::contains(request()->url(), 'integration') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/clip.svg') }}" alt=""></a></li>
        <li><a href="{{ route('dash-settings') }}"
                class="nav_link {{ Str::contains(request()->url(), 'setting') ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/settings.svg') }}" alt=""></a></li>
        {{-- <li><a href="#" class="nav_link"><img src="{{ asset('assets/img/calendar.svg') }}" alt=""></a></li> --}}
    </ul>
    <div class="logout">
        <a href="#"><img src="{{ asset('assets/img/logout.svg') }}" alt=""></a>
    </div>
</div>
