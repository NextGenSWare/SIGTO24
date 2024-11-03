// Obtener información del carrito y calcular el total
document.addEventListener('DOMContentLoaded', () => {
    const priceElement = document.getElementById('productPrice');
    const quantityElement = document.getElementById('productQuantity');
    const totalElement = document.getElementById('precio-total');

    if (priceElement && quantityElement && totalElement) {
        const price = parseFloat(priceElement.textContent);
        const quantity = parseInt(quantityElement.textContent, 10);
        
        if (!isNaN(price) && !isNaN(quantity)) {
            const total = price * quantity;
            totalElement.textContent = `Total a pagar: U$S ${total.toFixed(2)}`;
        } else {
            console.error('Error al calcular el total. Verifica los datos del producto.');
        }
    } else {
        console.error('No se encontraron los elementos necesarios para mostrar el total.');
    }
});
