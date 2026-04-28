const gridOptions = {
    rowData: [],
    columnDefs: [{
        field: "id",
        headerName: "ID",
        filter: true,
        sortable: true,
    },
    {
        field: "username",
        headerName: "Username",
        filter: true,
        sortable: true,
        hide: true,
    },
    {
        field: "email",
        headerName: "Email",
        filter: true,
        sortable: true,
        hide: true,
    },
    {
        field: "role_id",
        headerName: "Rol",
        filter: true,
        sortable: true,
    },
    {
        field: "estado",
        headerName: "Estado",
        filter: true
    },
    ],

    pagination: true,
    paginationPageSize: 10,
    paginationPageSizeSelector: [10, 20, 50, 100]
};