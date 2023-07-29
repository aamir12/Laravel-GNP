<!DOCTYPE html>
<html>
    <head>

    </head>
    <body>
        <div style="width:100%; background: {{ isset($branding->logo) ? 'white' : $branding->primary_color }};">
            <div style="padding: 12px 20px; display: flex;">
                @if (isset($branding->logo))
                    <img src="{{ $branding->logo }}" style="display: inline-block; height: 50px; margin-right: 15px;">
                @else
                    <span style="
                        display: inline-block;
                        font-size: 36px;
                        color: {{ \App\Classes\Helper::getContrastColor($branding->primary_color) }};
                        line-height: 50px;"
                    >
                        {{ $branding->company_name ?? 'Earnie'}}
                    </span>
                @endif
            </div>
        </div>

        <div style="margin: 30px 20px;">
        @yield('content')
        </div>

        <div class="footer" style="background: #e6e6e6;">
            <div style="padding: 15px 20px; color: #333">
                <p style="margin-bottom: 20px">If you have any issues please contact us by emailing support@myearnie.co.uk</p>
                <p style="margin-bottom: 20px">{{ $branding->company_address }}</p>
                <p>
                    <a style="color: #333; padding-right: 15px;" href="{{ $branding->terms_url }}">Terms and Conditions</a>
                    <a style="color: #333" href="{{ $branding->privacy_url }}"> Privacy Policy</a>
                </p>
            </div>
        </div>
    </body>
</html>
