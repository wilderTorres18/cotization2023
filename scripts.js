function limpiarCampos() {
    document.getElementById('formularioCotizacion').reset();
}

async function calcularAutomatico() {
    const data = obtenerDatosDelFormulario();
    
    try {
        const response = await axios.post('proformav1.php', data);
        const result = response.data;
        console.log(result);
        Swal.fire({
            title: 'Resultados Automáticos',
            html: `Lado Vertical: ${result.verticalSide}<br/>Lado Horizontal: ${result.horizontalSide}<br/>Waste: ${result.waste}<br/>Costo de Waste: ${result.wasteCost}<br/>Costo Total: ${result.totalCost}`,
            icon: 'success',
            confirmButtonText: 'Ok'
        });
    } catch (error) {
        mostrarError();
    }
}

async function calcularConEleccion() {
    const { value: eleccion } = await Swal.fire({
        title: 'Elección de Corte',
        input: 'select',
        inputOptions: {
            'vertical': 'Vertical',
            'horizontal': 'Horizontal'
        },
        inputPlaceholder: 'Seleccione una opción',
        showCancelButton: true
    });
    
    if (eleccion) {
        const data = obtenerDatosDelFormulario();
        data.isChoice = true;
        data.isHorizontalCut = eleccion === 'horizontal';
        console.log(data);

        try {
            const response = await axios.post('proformav1.php', data);
            const result = response.data;
            Swal.fire({
                title: 'Resultados por Elección',
                html: `Lado Vertical: ${result.verticalSide}<br/>Lado Horizontal: ${result.horizontalSide}<br/>Waste: ${result.waste}<br/>Costo de Waste: ${result.wasteCost}<br/>Costo Total: ${result.totalCost}`,
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        } catch (error) {
            mostrarError();
        }
    }
}

function mostrarError() {
    Swal.fire({
        title: 'Error',
        text: 'Hubo un error al realizar el cálculo.',
        icon: 'error',
        confirmButtonText: 'Ok'
    });
}

function obtenerDatosDelFormulario() {
    // Aquí deberías obtener los valores de los campos del formulario y retornarlos como un objeto.
    // Por ejemplo:
    return {
        verticalSide: document.getElementById('verticalSide').value,
        horizontalSide: document.getElementById('horizontalSide').value,
        price: document.getElementById('price').value,
        courtesy: document.getElementById('courtesy').value,
        materials: document.getElementById('materials').value,
        wastePrice: document.getElementById('wastePrice').value,
        // ... los demás campos
    };
}
