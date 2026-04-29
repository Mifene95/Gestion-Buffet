$(document).ready(function() {
    let labels = [];
    let dataOcupadas = [];
    let dataDisponibles = [];
    
    mesasData.forEach((mesa) => {
        labels.push(mesa.nombre);
        dataOcupadas.push(mesa.posiciones_rellenas);
        dataDisponibles.push(mesa.posiciones_totales - mesa.posiciones_rellenas);
    });
    
    // Crear gráfico de barras apiladas
    const ctx = document.getElementById('chartGlobal');
    const chartGlobal = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Posiciones Ocupadas',
                    data: dataOcupadas,
                    backgroundColor: '#36A2EB',
                    borderColor: '#fff',
                    borderWidth: 1
                },
                {
                    label: 'Espacio Disponible',
                    data: dataDisponibles,
                    backgroundColor: '#D3D3D3',
                    borderColor: '#fff',
                    borderWidth: 1
                }
            ]
        },
        options: {
            indexAxis: 'y', // barras horizontales
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    // Gráfico de cambios por día
    let diasLabels = [];
    let cambiosCantidad = [];
    
    cambiosData.forEach((cambio) => {
        diasLabels.push(cambio.dia);
        cambiosCantidad.push(cambio.total_cambios);
    });
    
    // Si no hay datos, crear array vacío con últimos 7 días
    if (diasLabels.length === 0) {
        for (let i = 6; i >= 0; i--) {
            let fecha = new Date();
            fecha.setDate(fecha.getDate() - i);
            diasLabels.push(fecha.toISOString().split('T')[0]);
            cambiosCantidad.push(0);
        }
    }
    
    // Crear gráfico de líneas
    const ctxCambios = document.getElementById('chartCambios');
    const chartCambios = new Chart(ctxCambios, {
        type: 'line',
        data: {
            labels: diasLabels,
            datasets: [{
                label: 'Cambios Realizados',
                data: cambiosCantidad,
                borderColor: '#FF6384',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#FF6384'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico alérgenos más comunes

let alergenos_labels = [];
let alergenos_valores = [];

alergenos_data.forEach(alergeno => {
    alergenos_labels.push(alergeno.nombre);
    alergenos_valores.push(alergeno.total);
});

const ctx_alergenos = document.getElementById('chartAlergenos');
const chart_alergenos = new Chart(ctx_alergenos, {
    type: 'bar',
    data: {
        labels: alergenos_labels,
        datasets: [{
            label: 'Cantidad de Platos',
            data: alergenos_valores,
            backgroundColor: '#FF6384',
            borderColor: '#fff',
            borderWidth: 1
        }]
    },
    
    options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        x: {
            beginAtZero: true,
            ticks: {
                stepSize: 1 
            }
        }
    },
    plugins: {
        legend: {
            position: 'bottom'
        }
    }
}
});
});