<?php

return [

    'name' => env('SCHOOL_NAME', 'InvestaSchool'),

    'tagline' => env('SCHOOL_TAGLINE', 'UNGGUL • TERAMPIL • BERKARAKTER'),

    'address' => env('SCHOOL_ADDRESS', 'Cibubur Country, Bogor'),

    'phone' => env('SCHOOL_PHONE', '(021) 555-0198'),

    'email' => env('SCHOOL_EMAIL', 'info@investaschool.sch.id'),

    'website' => env('SCHOOL_WEBSITE', 'https://investaschool.sch.id'),

    'npsn' => env('SCHOOL_NPSN', '20234567'),

    'logo' => env('SCHOOL_LOGO', 'img/logo.svg'),

    // Wali kelas / identitas
    'city' => env('SCHOOL_CITY', 'Bogor'),

    // Kepala sekolah (kosongkan untuk memakai akun user berperan principal)
    'principal_name' => env('SCHOOL_PRINCIPAL_NAME', ''),

    'principal_nip' => env('SCHOOL_PRINCIPAL_NIP', ''),

    'principal_credentials' => env('SCHOOL_PRINCIPAL_CREDENTIALS', 'S.Pd., M.Pd.'),
];
