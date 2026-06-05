@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="text-secondary fw-bold">Makine Parkuru Yönetimi</h4>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white fw-bold">
                    <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i>Yeni Makine Kaydı</h5>
                </div>
                <div class="card-body">
                    <form id="machineForm">

                        <div class="mb-3">
                            <label class="form-label">Müşteri (Lokasyon)</label>
                            <select id="customer_id" name="customer_id" class="form-select" required>
                                <option value="">Yükleniyor...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mülkiyet Durumu</label>
                            <select name="ownership" class="form-select" required>
                                <option value="Müşterinin Kendi Makinesi">Müşterinin Kendi Makinesi</option>
                                <option value="KÖKSAN Makinesi">KÖKSAN Makinesi (Emanet/Kira)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marka</label>
                                <input type="text" name="brand" class="form-control" placeholder="Örn: Husky" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Model</label>
                                <input type="text" name="model_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Seri No</label>
                                <input type="text" name="serial_number" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kurulum Tarihi</label>
                                <input type="date" name="installed_at" class="form-control">
                            </div>
                        </div>

                        <div id="dynamicFieldsContainer" class="p-3 mb-3 bg-light border rounded">
                            <small class="text-muted">Dinamik alanlar yükleniyor...</small>
                        </div>

                        <button type="submit" class="btn btn-info text-white w-100 fw-bold">Makineyi Sisteme Ekle</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Makine Listesi</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Müşteri</th>
                                <th>Marka / Model</th>
                                <th>Mülkiyet</th>
                                <th>Seri No</th>
                                <th>Kurulum</th>
                            </tr>
                        </thead>
                        <tbody id="machinesTableBody">
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
            loadMachines();
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
            `/api/dynamic-fields?model_type=${encodeURIComponent('App\\Models\\Machine')}`, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '<small class="text-muted d-block mb-2">Makine Spesifik Alanları (EAV)</small>';

            result.data.forEach(f => {
                let input =
                    `<input type="${f.type}" name="dynamic_${f.name}" class="form-control" ${f.is_required ? 'required' : ''}>`;
                container.innerHTML +=
                    `<div class="mb-2"><label class="form-label text-info fw-bold">${f.label}</label>${input}</div>`;
            });
        }

        async function loadMachines() {
            const response = await fetch('/api/machines', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('machinesTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) return tbody.innerHTML =
                '<tr><td colspan="5" class="text-center py-4">Sistemde makine kaydı bulunamadı.</td></tr>';

            result.data.forEach(m => {
                let ownershipBadge = m.ownership.includes('KÖKSAN') ? 'warning text-dark' : 'secondary';
                tbody.innerHTML += `<tr>
                <td class="fw-bold"><i class="bi bi-building me-1"></i>${m.customer ? m.customer.company_name : '-'}</td>
                <td><strong class="text-primary">${m.brand}</strong> <br> <small>${m.model_name}</small></td>
                <td><span class="badge bg-${ownershipBadge}">${m.ownership}</span></td>
                <td>${m.serial_number || '-'}</td>
                <td>${m.installed_at || '-'}</td>
            </tr>`;
            });
        }

        document.getElementById('machineForm').addEventListener('submit', async (e) => {
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

            const response = await fetch('/api/machines', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                alert('Makine başarıyla eklendi!');
                e.target.reset();
                loadMachines();
            } else {
                const err = await response.json();
                console.error(err);
                alert('Kayıt başarısız. Serio No çakışması veya validasyon hatası olabilir.');
            }
        });
    </script>
@endsection
