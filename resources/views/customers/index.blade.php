<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KÖKSAN CRM - Müşteri Modülü Testi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Yeni Müşteri Ekle</h5>
                    </div>
                    <div class="card-body">
                        <form id="customerForm">
                            <div class="mb-3">
                                <label class="form-label">Firma Adı</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Statü</label>
                                <select name="status" class="form-select" required>
                                    <option value="aday">Aday</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="pasif">Pasif</option>
                                </select>
                            </div>

                            <div id="dynamicFieldsContainer" class="p-3 mb-3 bg-light border rounded">
                                <small class="text-muted d-block mb-2">Dinamik Alanlar (API'den geliyor)</small>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Müşteri Listesi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Firma Adı</th>
                                    <th>Statü</th>
                                    <th>Ambalaj Tipi (JSON)</th>
                                </tr>
                            </thead>
                            <tbody id="customersTableBody">
                                <tr>
                                    <td colspan="3" class="text-center">Yükleniyor...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_HEADERS = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        // 1. Sayfa yüklendiğinde çalışacak ana akış
        document.addEventListener('DOMContentLoaded', () => {
            loadDynamicForm();
            loadCustomers();
        });

        // 2. Dinamik Form Şemasını (Schema) çek ve arayüze çiz
        async function loadDynamicForm() {
            const modelType = 'App%5CModels%5CCustomer';
            const response = await fetch(`/api/dynamic-fields?model_type=${modelType}`, {
                headers: API_HEADERS
            });
            const result = await response.json();

            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '<small class="text-muted d-block mb-2">Sistemden Gelen Alanlar</small>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(field => {
                    let inputHtml = '';

                    // Select Tipi (Örn: Ambalaj Tipi)
                    if (field.type === 'select') {
                        let options = field.options.map(opt => `<option value="${opt}">${opt}</option>`).join(
                            '');
                        inputHtml = `<select name="dynamic_${field.name}" class="form-select" ${field.is_required ? 'required' : ''}>
                                    <option value="">Seçiniz...</option>
                                    ${options}
                                 </select>`;
                    }
                    // Diğer Tipler (text, number vs.) eklenebilir
                    else {
                        inputHtml =
                            `<input type="${field.type}" name="dynamic_${field.name}" class="form-control" ${field.is_required ? 'required' : ''}>`;
                    }

                    // Elementi DOM'a ekle
                    const wrapper = document.createElement('div');
                    wrapper.className = 'mb-2';
                    wrapper.innerHTML = `<label class="form-label">${field.label}</label> ${inputHtml}`;
                    container.appendChild(wrapper);
                });
            } else {
                container.innerHTML += '<span class="text-danger">Dinamik alan bulunamadı.</span>';
            }
        }

        // 3. Müşterileri çek ve tabloya bas
        async function loadCustomers() {
            const response = await fetch('/api/customers', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('customersTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center">Kayıt bulunamadı.</td></tr>';
                return;
            }

            result.data.forEach(customer => {
                // JSON içerisindeki dinamik veriyi okuma
                const packagingType = customer.custom_data?.packaging_type || '-';

                tbody.innerHTML += `
                <tr>
                    <td>${customer.company_name}</td>
                    <td><span class="badge bg-secondary">${customer.status}</span></td>
                    <td><strong class="text-primary">${packagingType}</strong></td>
                </tr>
            `;
            });
        }

        // 4. Formu Gönder (Statik ve Dinamik alanları ayırıp JSON payload yapıyoruz)
        document.getElementById('customerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const payload = {
                custom_data: {}
            }; // JSON kolonumuz

            // Formdaki verileri dolaş
            formData.forEach((value, key) => {
                if (key.startsWith('dynamic_')) {
                    // Dinamik alanları custom_data objesi içine at
                    let actualKey = key.replace('dynamic_', '');
                    payload.custom_data[actualKey] = value;
                } else {
                    // Statik alanları doğrudan ana objeye at
                    payload[key] = value;
                }
            });

            // Backend'e POST at
            const response = await fetch('/api/customers', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                e.target.reset(); // Formu temizle
                loadCustomers(); // Tabloyu yenile
            } else {
                // Laravel Validation hatalarını göster
                console.error('Hata:', result);
                alert('Kayıt başarısız! Konsolu kontrol edin.');
            }
        });
    </script>

</body>

</html>
