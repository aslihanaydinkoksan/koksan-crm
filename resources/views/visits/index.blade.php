@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-12 mb-3">
            <h4 class="text-secondary fw-bold">Saha Ziyaretleri</h4>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white fw-bold">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Yeni Ziyaret Kaydı</h5>
                </div>
                <div class="card-body">
                    <form id="visitForm">
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                        <div class="mb-3">
                            <label class="form-label">Müşteri</label>
                            <select id="customer_id" name="customer_id" class="form-select" required>
                                <option value="">Yükleniyor...</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tarih</label>
                                <input type="datetime-local" name="visit_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sebep</label>
                                <select name="reason" class="form-select" required>
                                    <option value="ziyaret">Rutin Ziyaret</option>
                                    <option value="sikayet">Şikayet</option>
                                    <option value="urun_denemesi">Ürün Denemesi</option>
                                    <option value="diger">Diğer</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Görüşülen Kişiler <small class="text-muted">(Virgülle
                                    ayırın)</small></label>
                            <input type="text" name="contact_persons" class="form-control"
                                placeholder="Örn: Ahmet Yılmaz, Ayşe Demir">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tespitler / Gözlemler</label>
                            <textarea name="observations" class="form-control" rows="2" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sonuç / Karar</label>
                            <textarea name="result" class="form-control" rows="2"></textarea>
                        </div>

                        <div id="dynamicFieldsContainer" class="p-3 mb-3 bg-light border rounded">
                            <small class="text-muted">Dinamik alanlar yükleniyor...</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Ziyareti Kaydet</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Geçmiş Ziyaretler</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tarih</th>
                                <th>Müşteri</th>
                                <th>Sebep</th>
                                <th>Personel</th>
                                <th>Barkod (EAV)</th>
                            </tr>
                        </thead>
                        <tbody id="visitsTableBody">
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
            loadVisits();
        });

        // Müşterileri Select'e Çek
        async function loadCustomers() {
            const response = await fetch('/api/customers', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const select = document.getElementById('customer_id');
            select.innerHTML = '<option value="">Seçiniz...</option>';
            if (result.data) {
                result.data.forEach(c => select.innerHTML += `<option value="${c.id}">${c.company_name}</option>`);
            }
        }

        // EAV Formunu Çiz
        async function loadDynamicForm() {
            const response = await fetch(`/api/dynamic-fields?model_type=${encodeURIComponent('App\\Models\\Visit')}`, {
                headers: API_HEADERS
            });
            const result = await response.json();
            const container = document.getElementById('dynamicFieldsContainer');
            container.innerHTML = '<small class="text-muted d-block mb-2">Sahaya Özel Alanlar</small>';

            result.data.forEach(f => {
                let input =
                    `<input type="${f.type}" name="dynamic_${f.name}" class="form-control" ${f.is_required ? 'required' : ''}>`;
                container.innerHTML +=
                    `<div class="mb-2"><label class="form-label text-primary fw-bold">${f.label}</label>${input}</div>`;
            });
        }

        // Ziyaretleri Listele
        async function loadVisits() {
            const response = await fetch('/api/visits', {
                headers: API_HEADERS
            });
            const result = await response.json();
            const tbody = document.getElementById('visitsTableBody');
            tbody.innerHTML = '';

            if (result.data.length === 0) return tbody.innerHTML =
                '<tr><td colspan="5" class="text-center py-4">Kayıtlı ziyaret bulunamadı.</td></tr>';

            result.data.forEach(v => {
                let badge = v.reason === 'sikayet' ? 'danger' : (v.reason === 'urun_denemesi' ? 'warning' :
                    'success');
                let barcode = v.custom_data?.barcode_no || '-';

                tbody.innerHTML += `<tr>
                <td class="fw-bold">${v.visit_date}</td>
                <td><i class="bi bi-building me-1"></i>${v.customer ? v.customer.company_name : '-'}</td>
                <td><span class="badge bg-${badge}">${v.reason.toUpperCase()}</span></td>
                <td><small class="text-muted"><i class="bi bi-person me-1"></i>${v.user ? v.user.name : '-'}</small></td>
                <td class="text-primary fw-bold">${barcode}</td>
            </tr>`;
            });
        }

        // Formu Gönder
        document.getElementById('visitForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                custom_data: {}
            };

            new FormData(e.target).forEach((val, key) => {
                if (key.startsWith('dynamic_')) {
                    payload.custom_data[key.replace('dynamic_', '')] = val;
                } else if (key === 'contact_persons') {
                    // Virgülle ayrılmış metni array'e çevir (Backend'in beklediği format)
                    payload[key] = val ? val.split(',').map(s => s.trim()).filter(Boolean) : [];
                } else {
                    payload[key] = val;
                }
            });

            const response = await fetch('/api/visits', {
                method: 'POST',
                headers: API_HEADERS,
                body: JSON.stringify(payload)
            });
            if (response.ok) {
                alert('Ziyaret başarıyla kaydedildi!');
                e.target.reset();
                loadVisits();
            } else {
                const errorData = await response.json();
                console.error(errorData);
                alert('Kayıt başarısız, konsolu kontrol edin.');
            }
        });
    </script>
@endsection
