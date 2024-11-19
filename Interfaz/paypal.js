document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('pagarBtn').addEventListener('click', function() {
        const total = parseFloat(document.getElementById('productPrice').textContent);
        if (isNaN(total) || total <= 0) {
            alert('El total no es válido para realizar un pago.');
            return;
        }

        fetch('./paypal_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                amount: total,
                currency: 'USD',
                description: 'Compra de productos en Flipcoin'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.approvalUrl) {
                window.location.href = data.approvalUrl;
            } else {
                console.error('Error al generar el pago:', data.error);
                alert(data.error || 'Error desconocido al procesar el pago.');
            }
        })
        .catch(error => console.error('Error en la solicitud:', error));
    });
});
