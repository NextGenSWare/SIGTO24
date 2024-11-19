document.addEventListener('DOMContentLoaded', () => {
    // Obtener el carrito del localStorage
    let carrito = JSON.parse(localStorage.getItem('carrito')) || [];
    let total = 20;

    // Calcular el total sumando los precios de los productos
    carrito.forEach(producto => {
        total += producto.precio * producto.cantidad; // Asume que el objeto producto tiene un campo cantidad
    });

    // Mostrar el total en la página
    document.getElementById('precio-total').textContent = `Total a pagar: U$S ${total.toFixed(2)}`;
});
