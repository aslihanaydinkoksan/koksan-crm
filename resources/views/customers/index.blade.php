@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="text-secondary fw-bold">Müşteri Yönetimi</h4>
        </div>
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
                            <label class="form-label">Vergi No</label>
                            <input type="text" name="tax_number" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-Posta</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
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
                            <small class="text-muted d-block mb-2">Dinamik Alanlar Yükleniyor...</small>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold">Kaydet</button>
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
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Firma Adı</th>
                                <th>Statü</th>
                                <th>Ambalaj Tipi</th>
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

    <script>
        const API_HEADERS = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        document.addEventListener('DOMContentLoaded', () => {
            loadDynamicForm();
            loadCustomers();
        });

        async function loadDynamicForm() {
            const modelType = encodeURIComponent('App\\Models\\Customer');
            const response = await fetch(`/api/dynamic-fields?model_type=${modelType}`, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '<small class="text-muted d-block mb-2">Sistemden Gelen Alanlar</small>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(field => {
                    let inputHtml = field.type === 'select' ?
                        `<select name="dynamic_${field.name}" class="form-select" ${field.is_required ? 'required' : ''}><option value="">Seçiniz...</option>${field.options.map(o => `<option value="${o}">${o}</option>`).join('')}</select>` :
                        `<input type="${field.type}" name="dynamic_${field.name}" class="form-control" ${field.is_required ? 'required' : ''}>`;
                    container.innerHTML +=
                        `<div class="mb-2"><label class="form-label">${field.label}</label>${inputHtml}</div>`;
                });
            }
        }

        async function loadCustomers() {
            const response = await fetch('/api/customers', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('customersTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) return tbody.innerHTML =
                '<tr><td colspan="3" class="text-center">Kayıt bulunamadı.</td></tr>';

            result.data.forEach(c => {
                tbody.innerHTML += `<tr>
                <td class="fw-bold">
                    <a href="/customers/${c.id}" class="text-decoration-none text-primary">
                        <i class="bi bi-box-arrow-in-up-right me-1"></i> ${c.company_name}
                    </a>
                </td>
                <td><span class="badge bg-secondary">${c.status}</span></td>
                <td><strong class="text-dark">${c.custom_data?.packaging_type || '-'}</strong></td>
            </tr>`;
            });
        }

        document.getElementById('customerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                custom_data: {}
            };
            new FormData(e.target).forEach((val, key) => key.startsWith('dynamic_') ? payload.custom_data[key
                .replace('dynamic_', '')] = val : payload[key] = val);

            const response = await fetch('/api/customers', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                alert('Başarılı!');
                e.target.reset();
                loadCustomers();
            } else alert('Hata oluştu!');
        });
    </script>
@endsection
