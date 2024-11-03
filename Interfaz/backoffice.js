// Mostrar el formulario para agregar/editar productos
const mostrarFormulario = () => {
    document.getElementById('formulario-producto').style.display = 'block';
};

// Cerrar el formulario de productos
const cerrarFormulario = () => {
    document.getElementById('formulario-producto').style.display = 'none';
};

// Manejar el envío del formulario de productos
document.getElementById('form-producto').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const response = await fetch('añadir_producto.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    if (result.success) {
        // Agregar el nuevo producto a la tabla
        const tabla = document.getElementById('tabla-productos');
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${result.id}</td>
            <td>${result.nombreProd}</td>
            <td>${result.idCategoria}</td>
            <td>U$S ${result.precio}</td>
            <td>${result.stock}</td>
            <td><button onclick="eliminarProducto(${result.id})">Eliminar</button></td>
        `;
        tabla.appendChild(newRow);
        alert(`Producto ${result.nombreProd} agregado con éxito.`);
    } else {
        alert('Error: ' + (result.error || 'Error desconocido'));
    }

    cerrarFormulario();
});

// Mostrar el formulario para agregar/editar usuarios
const mostrarFormularioUsuario = () => {
    document.getElementById('formulario-usuario').style.display = 'block';
};

// Cerrar el formulario de usuarios
const cerrarFormularioUsuario = () => {
    document.getElementById('formulario-usuario').style.display = 'none';
};

// Manejar el envío del formulario de usuarios
document.getElementById('form-usuario').addEventListener('submit', (e) => {
    e.preventDefault();

    const nombreElement = document.getElementById('nombreUsuario');
    const tipoElement = document.getElementById('tipoUsuario');
    const emailElement = document.getElementById('emailUsuario');
    const telefonoElement = document.getElementById('telefonoUsuario');

    if (!nombreElement || !tipoElement || !emailElement || !telefonoElement) {
        console.error('Uno o más elementos del formulario no se encontraron.');
        return;
    }

    const nombre = nombreElement.value;
    const tipo = tipoElement.value;
    const email = emailElement.value;
    const telefono = telefonoElement.value;

    const formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('tipo', tipo);
    formData.append('email', email);
    formData.append('telefono', telefono);

    fetch('gestionar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`Usuario ${nombre} gestionado con éxito.`);
            cerrarFormularioUsuario();
        } else {
            alert('Error al gestionar el usuario.');
            console.error(data.error);
        }
    })
    .catch(error => console.error('Error en la solicitud:', error));
});
