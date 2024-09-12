@php
    use Illuminate\Support\Str;
@endphp
<div class="sidebar_menu col-12  p-0 flex-shrink-1">
    <ul class="list-unstyle p-0 m-0">
        <li><a href="/accdashboard"
                class="nav_link {{ Str::contains(request()->url(), URL('accdashboard')) ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/home.svg') }}" alt=""></a></li>
        @if ($manage_campaigns == true || $manage_campaigns == 'view_only')
            <li><a href="{{ route('campaigns') }}"
                    class="nav_link {{ Str::contains(request()->url(), URL('campaign')) ? 'active' : '' }}"><img
                        src="{{ asset('assets/img/speaker.svg') }}" alt=""></a></li>
        @endif
        <li><a href="{{ route('dash-leads') }}"
                class="nav_link {{ Str::contains(request()->url(), URL('leads')) ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/leads.svg') }}" alt=""></a></li>
        @if ($manage_campaign_details_and_reports == true || $manage_campaign_details_and_reports == 'view_only')
            <li><a href="{{ route('dash-reports') }}"
                    class="nav_link {{ Str::contains(request()->url(), URL('report')) ? 'active' : '' }}"><img
                        src="{{ asset('assets/img/stat.svg') }}" alt=""></a></li>
        @endif
        @if ($manage_chat == true || $manage_chat == 'view_only')
            <li><a href="{{ route('dash-messages') }}"
                    class="nav_link {{ Str::contains(request()->url(), URL('message')) ? 'active' : '' }}"><img
                        src="{{ asset('assets/img/message.svg') }}" alt=""></a></li>
        @endif
        {{-- <li><a href="#" class="nav_link"><img src="{{ asset('assets/img/phonecall.svg') }}" alt=""></a></li> --}}
        @if ($manage_webhooks == true || $manage_webhooks == 'view_only')
            <li><a href="{{ route('dash-integrations') }}"
                    class="nav_link {{ Str::contains(request()->url(), URL('integration')) ? 'active' : '' }}"><img
                        src="{{ asset('assets/img/clip.svg') }}" alt=""></a></li>
        @endif
        <li><a href="{{ route('dash-settings') }}"
                class="nav_link {{ Str::contains(request()->url(), URL('setting')) ? 'active' : '' }}"><img
                    src="{{ asset('assets/img/settings.svg') }}" alt=""></a></li>
        {{-- <li><a href="#" class="nav_link"><img src="{{ asset('assets/img/calendar.svg') }}" alt=""></a></li> --}}
    </ul>
    @php
        $user = auth()->user();
    @endphp
    <div class="logout">
        @if ($user)
            <a href="{{ route('dashobardz') }}">
                <img src="{{ asset('assets/img/logout.svg') }}" alt="">
            </a>
        @endif
    </div>
</div>
