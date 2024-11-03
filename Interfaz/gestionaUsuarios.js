document.addEventListener('DOMContentLoaded', () => {
    fetch('obtener_usuarios.php') // Archivo PHP que consulta todos los tipos de usuarios
        .then(response => response.json())
        .then(data => {
            const tablaUsuarios = document.getElementById('tabla-usuarios');
            data.forEach(usuario => {
                const fila = `
                    <tr>
                        <td>${usuario.id}</td>
                        <td>${usuario.nombre}</td>
                        <td>${usuario.tipo}</td>
                        <td>${usuario.email}</td>
                        <td>${usuario.telefono}</td>
                        <td>
                            <button class="btn-editar">Editar</button>
                            <button class="btn-eliminar">Eliminar</button>
                        </td>
                    </tr>
                `;
                tablaUsuarios.innerHTML += fila;
            });
        })
        .catch(error => console.error('Error al cargar los usuarios:', error));
});

