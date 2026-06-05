@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="text-secondary fw-bold">Kalite ve Laboratuvar Testleri</h4>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white fw-bold">
                    <h5 class="mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Yeni Test Girişi</h5>
                </div>
                <div class="card-body">
                    <form id="labTestForm">
                        <div class="mb-3">
                            <label class="form-label">Müşteri</label>
                            <select id="customer_id" name="customer_id" class="form-select" required>
                                <option value="">Yükleniyor...</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Test Tipi</label>
                                <input type="text" name="test_type" class="form-control" placeholder="Örn: Basınç Testi"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tarih</label>
                                <input type="datetime-local" name="test_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Durum</label>
                            <select name="status" class="form-select" required>
                                <option value="bekliyor">Bekliyor</option>
                                <option value="test_ediliyor">Test Ediliyor</option>
                                <option value="onaylandi">Onaylandı</option>
                                <option value="red">Reddedildi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Özet Sonuç</label>
                            <textarea name="result_summary" class="form-control" rows="2"></textarea>
                        </div>

                        <div id="dynamicFieldsContainer" class="p-3 mb-3 bg-light border rounded">
                            <small class="text-muted">Laboratuvar spesifik alanları yükleniyor...</small>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold">Testi Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Test Geçmişi</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tarih</th>
                                <th>Müşteri</th>
                                <th>Test Tipi</th>
                                <th>Durum</th>
                                <th>Makine/Ürün Bağlantısı</th>
                            </tr>
                        </thead>
                        <tbody id="labTestsTableBody">
                            <tr>
                                <td colspan="5" class="text-center py-4">Kayıtlar yükleniyor...</td>
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
            loadCustomers();
            loadDynamicForm();
            loadLabTests();
        });

        async function loadCustomers() {
            const response = await fetch('/api/customers', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const select = document.getElementById('customer_id');
            select.innerHTML = '<option value="">Müşteri Seçiniz...</option>';
            if (result.data) {
                result.data.forEach(c => select.innerHTML += `<option value="${c.id}">${c.company_name}</option>`);
            }
        }

        async function loadDynamicForm() {
            const response = await fetch(
            `/api/dynamic-fields?model_type=${encodeURIComponent('App\\Models\\LabTest')}`, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '<small class="text-muted d-block mb-2">Dinamik EAV Alanları</small>';

            result.data.forEach(f => {
                let input =
                    `<input type="${f.type}" name="dynamic_${f.name}" class="form-control" ${f.is_required ? 'required' : ''}>`;
                container.innerHTML +=
                    `<div class="mb-2"><label class="form-label text-danger fw-bold">${f.label}</label>${input}</div>`;
            });
        }

        async function loadLabTests() {
            const response = await fetch('/api/lab-tests', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('labTestsTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) return tbody.innerHTML =
                '<tr><td colspan="5" class="text-center py-4">Test kaydı bulunamadı.</td></tr>';

            result.data.forEach(t => {
                let badge = t.status === 'onaylandi' ? 'success' : (t.status === 'red' ? 'danger' : (t
                    .status === 'test_ediliyor' ? 'warning' : 'secondary'));
                tbody.innerHTML += `<tr>
                <td class="fw-bold">${t.test_date}</td>
                <td><i class="bi bi-building me-1"></i>${t.customer ? t.customer.company_name : '-'}</td>
                <td class="text-danger fw-bold">${t.test_type}</td>
                <td><span class="badge bg-${badge}">${t.status.toUpperCase()}</span></td>
                <td><small class="text-muted">${t.machine ? '<i class="bi bi-gear me-1"></i>' + t.machine.name : 'Bağımsız Test'}</small></td>
            </tr>`;
            });
        }

        document.getElementById('labTestForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                custom_data: {}
            };

            new FormData(e.target).forEach((val, key) => {
                if (key.startsWith('dynamic_')) {
                    payload.custom_data[key.replace('dynamic_', '')] = val;
                } else {
                    payload[key] = val;
                }
            });

            const response = await fetch('/api/lab-tests', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                alert('Laboratuvar testi başarıyla kaydedildi!');
                e.target.reset();
                loadLabTests();
            } else {
                const err = await response.json();
                console.error(err);
                alert('Kayıt başarısız. Lütfen zorunlu alanları kontrol edin.');
            }
        });
    </script>
@endsection
