<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Custom DataTables styling to match */
    .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .dataTables_wrapper .dataTables_paginate .pagination .page-item .page-link {
        color: var(--bs-gray-700);
        border-radius: 0.475rem;
        margin: 0 0.25rem;
    }

    .dataTables_wrapper .dataTables_info {
        color: var(--bs-gray-600);
        font-weight: 500;
        padding-top: 0.85em;
    }

    .table.dataTable>thead>tr>th {
        border-bottom-width: 1px;
        color: var(--bs-gray-700);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.05rem;
    }

    .table.dataTable>tbody>tr>td {
        vertical-align: middle;
        color: var(--bs-gray-800);
    }

    /* Hide default search input to use our custom one if needed */
    .dataTables_filter {
        display: none;
    }

    /* Adjust row padding */
    table.dataTable.table-striped>tbody>tr:nth-of-type(odd)>* {
        box-shadow: inset 0 0 0 9999px var(--bs-gray-100);
    }

    table.dataTable.table-striped>tbody>tr>td {
        border-bottom: 1px dashed var(--bs-gray-300);
    }
</style>