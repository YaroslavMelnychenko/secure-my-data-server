@extends('emails.layouts.default')

@section('title', 'Реєстрація в SecureMyData')

@section('preheader', 'Ви зареєстровані в SecureMyData')

@section('content')
    <p>Привіт!</p>
    <p>Ви зареєстровані в системі безпечного зберігання конфіденційних даних SecureMyData.</p>
    <p>Вам необхідно підтвердити вашу електронну пошту. Для цього введіть код в програмі.</p>
    <p>Увага! Не передавайте нікому ваш особистий закритий ключ та не зберігайте пароль у відкритому доступі.</p>
    <br>
    <strong>{{ $verificationCode }}<strong>
@endsection