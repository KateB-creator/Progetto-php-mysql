let chart; // riferimento globale al grafico

document.getElementById('loadBtn').addEventListener('click', () => {
  const start = document.getElementById('start_date').value;
  const end = document.getElementById('end_date').value;
  const productId = document.getElementById('product_id').value;

  let url = 'http://localhost/sunnee-app/index.php/api/recycled-plastic';
  const params = [];

  if (start) params.push(`start_date=${start}`);
  if (end) params.push(`end_date=${end}`);
  if (productId) params.push(`product_id=${productId}`);

  if (params.length > 0) {
    url += '?' + params.join('&');
  }

  fetch(url)
    .then(async response => {
      if (!response.ok) {
        const text = await response.text();
        throw new Error(`Errore HTTP ${response.status}: ${text}`);
      }
      return response.json();
    })
    .then(data => {
      const totalKg = parseFloat(data.total_kg_recycled ?? 0).toFixed(2);
      document.getElementById('kgResult').textContent = totalKg;
      updateChart(totalKg);
    })
    .catch(err => {
      console.error('Errore:', err);
      document.getElementById('kgResult').textContent = 'Errore';
    });
});


function updateChart(value) {
  const ctx = document.getElementById('plasticChart').getContext('2d');

  if (chart) {
    chart.data.datasets[0].data = [value];
    chart.update();
  } else {
    chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Totale Kg'],
        datasets: [{
          label: 'Plastica Riciclata (kg)',
          data: [value],
          backgroundColor: '#28a745',
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function (value) {
                return value + ' kg';
              }
            }
          }
        }
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadProducts();
  loadOrders();
});

function loadProducts() {
  fetch('http://localhost/sunnee-app/index.php/api/products')
    .then(res => res.json())
    .then(products => {
      const container = document.getElementById('productsContainer');
      container.innerHTML = '';

      products.forEach(product => {
        const card = document.createElement('div');
        card.className = 'col product-item';
        card.innerHTML = `
          <div class="card h-100">
            <img src="${product.image_url || 'img/default.jpg'}" class="card-img-top" alt="${product.name}">
            <div class="card-body">
              <h5 class="card-title product-name">${product.name}</h5>
              <p class="card-text">♻️ ${product.kg_recycled} kg plastica riciclata</p>
            </div>
          </div>
        `;
        container.appendChild(card);
      });
    })
    .catch(err => console.error('Errore caricamento prodotti:', err));
}

function loadOrders() {
  const ordersContainer = document.getElementById('ordersContainer');
  ordersContainer.innerHTML = '';

  fetch('http://localhost/sunnee-app/index.php/api/orders')
    .then(res => res.json())
    .then(orders => {
      orders.forEach(order => {
        const div = document.createElement('div');
        div.className = 'card mb-3';
        div.innerHTML = `
          <div class="card-body">
            <h5 class="card-title">Ordine #${order.id} - ${order.order_date}</h5>
            <ul class="list-group list-group-flush mb-3">
              ${order.items.map(item => `
                <li class="list-group-item">Prodotto ID: ${item.product_id} - Quantità: ${item.quantity}</li>
              `).join('')}
            </ul>
            <button class="btn btn-sm btn-warning me-2" onclick="editOrder(${order.id})">✏️ Modifica</button>
            <button class="btn btn-sm btn-danger" onclick="deleteOrder(${order.id})">🗑️ Elimina</button>
          </div>
        `;
        ordersContainer.appendChild(div);
      });
    });
}

document.getElementById('searchInput').addEventListener('keyup', function () {
  const search = this.value.toLowerCase();
  document.querySelectorAll('.product-item').forEach(item => {
    const name = item.querySelector('.product-name').textContent.toLowerCase();
    item.style.display = name.includes(search) ? '' : 'none';
  });
});

function deleteOrder(orderId) {
  if (!confirm(`Sei sicuro di voler eliminare l'ordine #${orderId}?`)) return;

  fetch(`http://localhost/sunnee-app/index.php/api/orders/${orderId}`, {
    method: 'DELETE'
  })
    .then(res => {
      if (!res.ok) throw new Error('Errore eliminazione');
      alert(`Ordine #${orderId} eliminato.`);
      loadOrders();
    })
    .catch(err => {
      console.error(err);
      alert('Impossibile eliminare l’ordine.');
    });
}

let currentOrderModal = new bootstrap.Modal(document.getElementById('editOrderModal'));

function editOrder(orderId) {
  fetch(`http://localhost/sunnee-app/index.php/api/orders/${orderId}`)
    .then(res => res.json())
    .then(order => {
      document.getElementById('editOrderId').value = order.id;
      document.getElementById('editOrderDate').value = order.order_date;
      currentOrderModal.show();
    });
}

document.getElementById('editOrderForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const orderId = document.getElementById('editOrderId').value;
  const newDate = document.getElementById('editOrderDate').value;

  fetch(`http://localhost/sunnee-app/index.php/api/orders/${orderId}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ order_date: newDate })
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message || 'Ordine aggiornato');
      currentOrderModal.hide();
      loadOrders();
    })
    .catch(() => alert('Errore aggiornamento ordine'));
});
