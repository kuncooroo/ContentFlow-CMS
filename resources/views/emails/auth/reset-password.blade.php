@component('mail::message')
# {{ __('Reset your password') }}

{{ __('You are receiving this email because we received a password reset request for your account on :site.', ['site' => $siteName]) }}

@component('mail::button', ['url' => $url, 'color' => 'primary'])
{{ __('Reset Password') }}
@endcomponent

{{ __('This password reset link will expire in :count minutes.', ['count' => $count]) }}

{{ __('If you did not request a password reset, no further action is required.') }}

{{ __('Thanks,') }}<br>
{{ $siteName }}
@endcomponent
