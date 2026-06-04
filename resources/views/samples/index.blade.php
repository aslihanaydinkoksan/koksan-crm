@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="text-secondary fw-bold">Numune Yönetimi (Polymorphic)</h4>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark fw-bold">
                    <h5 class="mb-0">Yeni Numune Gönder</h5>
                </div>
                <div class="card-body">
                    <form id="sampleForm">
                        <div class="mb-3 p-2 border rounded bg-light border-info">
                            <label class="form-label text-info fw-bold">Alıcı Tipi</label>
                            <select id="receivable_type" name="receivable_type" class="form-select mb-2" required>
                                <option value="">Tip Seçin...</option>
                                <option value="App\Models\Customer">Kurumsal Müşteri</option>
                                <option value="App\Models\Person">Bireysel Şahıs</option>
                            </select>
                            <label class="form-label text-info fw-bold">Alıcı</label>
                            <select id="receivable_id" name="receivable_id" class="form-select" required disabled>
                                <option value="">Önce tip seçiniz...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konu</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>

                        <div id="dynamicFieldsContainer" class="p-3 mb-3 bg-light border rounded">
                            <small class="text-muted">Alanlar yükleniyor...</small>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold">Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Gönderim Geçmişi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Konu</th>
                                <th>Alıcı Tipi & Adı</th>
                                <th>Kargo Firması</th>
                            </tr>
                        </thead>
                        <tbody id="samplesTableBody">
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
            loadSamples();
        });

        document.getElementById('receivable_type').addEventListener('change', async (e) => {
            const type = e.target.value;
            const idSelect = document.getElementById('receivable_id');
            if (!type) return idSelect.innerHTML = '<option value="">Önce tip seçiniz...</option>', idSelect
                .disabled = true;

            const response = await fetch(type === 'App\\Models\\Customer' ? '/api/customers' : '/api/people', {
                headers: API_HEADERS
            });
            const result = await response.json();

            idSelect.innerHTML = '<option value="">Seçiniz...</option>';
            result.data.forEach(item => idSelect.innerHTML +=
                `<option value="${item.id}">${item.company_name || item.first_name + ' ' + item.last_name}</option>`
                );
            idSelect.disabled = false;
        });

        async function loadDynamicForm() {
            const response = await fetch(
            `/api/dynamic-fields?model_type=${encodeURIComponent('App\\Models\\Sample')}`, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '';
            result.data.forEach(f => {
                let options = f.type === 'select' ? f.options.map(o => `<option value="${o}">${o}</option>`)
                    .join('') : '';
                let input = f.type === 'select' ?
                    `<select name="dynamic_${f.name}" class="form-select">${options}</select>` :
                    `<input type="${f.type}" name="dynamic_${f.name}" class="form-control">`;
                container.innerHTML +=
                    `<div class="mb-2"><label class="form-label">${f.label}</label>${input}</div>`;
            });
        }

        async function loadSamples() {
            const response = await fetch('/api/samples', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('samplesTableBody');
            tbody.innerHTML = '';
            if (result.data.length === 0) return tbody.innerHTML =
                '<tr><td colspan="3" class="text-center">Kayıt yok.</td></tr>';

            result.data.forEach(s => {
                let badge = s.receiver.type === 'App\\Models\\Customer' ? 'primary' : 'success';
                tbody.innerHTML += `<tr>
                <td class="fw-bold">${s.subject}</td>
                <td><span class="badge bg-${badge}">${s.receiver.type.includes('Customer')?'Müşteri':'Şahıs'}</span> <br> <small>${s.receiver.display_name}</small></td>
                <td><span class="text-info fw-bold">${s.custom_data?.shipping_company || '-'}</span></td>
            </tr>`;
            });
        }

        document.getElementById('sampleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                custom_data: {}
            };
            new FormData(e.target).forEach((val, key) => key.startsWith('dynamic_') ? payload.custom_data[key
                .replace('dynamic_', '')] = val : payload[key] = val);

            const response = await fetch('/api/samples', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                alert('Kaydedildi!');
                e.target.reset();
                document.getElementById('receivable_id').disabled = true;
                loadSamples();
            }
        });
    </script>
@endsection
