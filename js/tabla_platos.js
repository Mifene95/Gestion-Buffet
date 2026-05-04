const turnos = [
    { id: 1, nombre: 'Desayuno' },
    { id: 2, nombre: 'Comida' },
    { id: 3, nombre: 'Cena' }
];

const alergenos_iconos = {
    'Gluten': '🌾',
    'Lácteos': '🧀',
    'Huevos': '🥚',
    'Pescado': '🐟',
    'Crustáceos': '🦐',
    'Frutos Secos': '🌰'
};

function verAlergenos(alergenos) {
    const lista = alergenos.split(',').map(a => {
        const nombre = a.trim();
        const icono = alergenos_iconos[nombre] || '?';
        return `<li>${icono} ${nombre}</li>`;
    }).join('');
    
    Swal.fire({
        title: 'Alérgenos',
        html: `<ul style="text-align: left; list-style: none;">${lista}</ul>`,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
}

const gridOptions = {
    rowData: [],
    columnDefs: [{
        field: "nombre_es",
        headerName: "Nombre_es",
        filter: true,
        sortable: true,
    },
    {
        field: "nombre_en",
        headerName: "Nombre_en",
        filter: true,
        sortable: true,
    },
    {
        field: "nombre_fr",
        headerName: "Nombre_fr",
        filter: true,
        sortable: true,
    },
    {
        field: "alergenos",
        headerName: "Alérgenos",
        filter: true,
        sortable: true,
        flex: 1,
        cellRenderer: function(params) {
            if (!params.data.alergenos || params.data.alergenos.length === 0) {
                return '-';
            }
            
            const alergenos = params.data.alergenos.split(',').map(a => a.trim());
            const iconos = alergenos.map(alergeno => alergenos_iconos[alergeno] || '?').join(' ');
            
            return `<button class="btn btn-lg" onclick="verAlergenos('${params.data.alergenos.replace(/'/g, "\\'")}')">
                        ${iconos}
                    </button>`;
        }
    },
    {
        headerName: "Acciones",
        field: "id",
        width: 200,
        sortable: false,
        filter: false,
        cellRenderer: function(params) {
            if (ROL_USUARIO === 1) {
                return `
                    <div style="display: flex; gap: 5px;">
                        <a href="editar_plato.php?id=${params.data.id}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button class="btn btn-sm btn-danger borrar-plato" data-id-plato="${params.data.id}">
                            <i class="fas fa-trash"></i> Borrar
                        </button>
                    </div>
                `;
            }
        }
    }],
};

let gridApi;

function cargar_platos(){
    $.ajax({
        url: '../inc/get_platos.php',
        method: 'GET',
        success: function(respuesta){
            gridApi.setGridOption('rowData', respuesta);
            console.log(respuesta);
        }
        
    });
}

// MOSTRAR PLATOS EN LISTA
function mostrarPlatos(platos) {
    const lista = $('#listaPlatosEditar');
    lista.empty();
    
    if (platos.length === 0) {
        lista.append('<div class="alert alert-info">No se encontraron platos</div>');
        return;
    }
    
    platos.forEach(plato => {
        lista.append(`
            <div class="list-group-item list-group-item-action plato-editar"
                data-id="${plato.id}"
                style="cursor: pointer; padding: 10px; border-bottom: 1px solid #eee;">
                <strong>${plato.nombre_es}</strong>
            </div>
        `);
    });
}

$(window).on('load', function() {
    const gridDiv = document.querySelector('#myGrid');
    if (gridDiv) {
        gridApi = agGrid.createGrid(gridDiv, gridOptions);
    }
    cargar_platos();

    // ABRIR MODAL BUSCAR PLATO
    $(document).on('click', '#btnBuscarPlato', function() {
        $('#buscadorPlatosEditar').val('');
        
        $.ajax({
            url: '../inc/get_todos_platos.php',
            method: 'GET',
            success: function(respuesta) {
                respuesta.sort((a, b) => a.nombre_es.localeCompare(b.nombre_es));
                window.todosLosPlatosEditar = respuesta;
                mostrarPlatos(respuesta.slice(0, 10));
                $('#modalBuscarPlato').modal('show');
            }
        });
    });

    // BUSCADOR EN TIEMPO REAL
    $(document).on('keyup', '#buscadorPlatosEditar', function() {
        const termino = $(this).val().toLowerCase();
        
        if (termino === '') {
            mostrarPlatos(window.todosLosPlatosEditar.slice(0, 10));
        } else {
            const filtrados = window.todosLosPlatosEditar.filter(plato =>
                plato.nombre_es.toLowerCase().includes(termino)
            );
            mostrarPlatos(filtrados);
        }
    });

    // CLICK EN PLATO → IR A EDITAR_PLATO.PHP
    $(document).on('click', '.plato-editar', function() {
        const id = $(this).data('id');
        window.location.href = 'editar_plato.php?id=' + id;
    });

    // BORRAR PLATO
    $(document).on('click', '.borrar-plato', function(e){
        e.preventDefault();
        const plato_id = $(this).data('id-plato');

        Swal.fire({
            title: "¿Eliminar plato?",
            text: "Esta accion no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar"
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../inc/borrar_plato.php',
                    method: 'POST',
                    data: { plato_id: plato_id },
                    success: function(respuesta){
                        if(respuesta.trim() === "ok") {
                            Swal.fire("¡Eliminado!", "", "success");
                            cargar_platos();
                        }
                    }
                }) 
            }
        })   
    });

});