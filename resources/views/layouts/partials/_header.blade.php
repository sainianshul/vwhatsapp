<!DOCTYPE html>
<html lang="en" data-bs-theme="light" style="background-color:#f5f8fa;">
<!--begin::Head-->

<head>
    <title>@yield('title', 'Social Manager Admin')</title>
    <meta charset="utf-8" />
    <meta name="description" content="A Nurse Scheduling Software" />
    <meta name="keywords" content="Nurse Scheduling Software" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Social Manager" />
    <meta property="og:site_name" content="Social Manager" />
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" />

    <!--begin::Theme mode setup — runs BEFORE any render-->
    <script>
        var mode = localStorage.getItem('data-bs-theme') || 'light';
        if (mode === 'system') mode = window.matchMedia('(prefers-color-scheme:dark)').matches ? 'dark' : 'light';
        document.documentElement.setAttribute('data-bs-theme', mode);
        document.documentElement.style.backgroundColor = mode === 'dark' ? '#1e1e2d' : '#f5f8fa';
    </script>
    <!--end::Theme mode setup-->



    <!--begin::Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap" />
    <!--end::Fonts-->

    <!--begin::Global Stylesheets Bundle (CSS only — keeps the premium css look)-->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    <!--begin::Custom admin styles-->
    <link href="{{ asset('css/admin-custom.css') }}?v=13" rel="stylesheet" type="text/css" />
    <!--end::Custom admin styles-->

    {{-- DataTables CSS — only on table pages --}}
    @stack('datatables_css')

    {{-- Page-specific styles --}}
    @stack('styles')

</head>
<!--end::Head-->
<!--begin::Body-->